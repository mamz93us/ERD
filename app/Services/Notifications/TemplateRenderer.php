<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Models\NotificationTemplate;

/**
 * Spec §6 Phase 9 + risk #10: safe placeholder rendering.
 *
 *   "{{customer_name}}, your trip {{trip_number}} is confirmed."
 *
 * Substitutes a small, caller-allowed set of `{{var}}` placeholders.
 * Unknown placeholders are LEFT LITERAL (not blanked) so a typo in the
 * template doesn't silently produce a broken message — the operator
 * notices and fixes the template instead.
 *
 * Values are HTML-escaped for the `mail` channel and left raw for
 * `whatsapp` (Green API treats payload as plain text). Per CLAUDE.md
 * risk #10: NEVER pass the raw template through Blade or eval() —
 * editable templates touching eval is the textbook XSS / RCE pivot.
 */
class TemplateRenderer
{
    /**
     * @param  array<string, scalar|null>  $vars
     */
    public function render(string $key, string $channel, string $locale, array $vars): RenderedTemplate
    {
        $template = NotificationTemplate::lookup($key, $channel, $locale);

        if ($template === null) {
            return new RenderedTemplate(
                subject: null,
                body: "[missing template: {$key}/{$channel}/{$locale}]",
                templateFound: false,
            );
        }

        $escape = $channel === 'mail';

        return new RenderedTemplate(
            subject: $this->substitute($template->subject ?? '', $vars, $escape) ?: null,
            body: $this->substitute($template->body, $vars, $escape),
            templateFound: true,
        );
    }

    /**
     * @param  array<string, scalar|null>  $vars
     */
    private function substitute(string $template, array $vars, bool $escape): string
    {
        return preg_replace_callback(
            '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/',
            function (array $match) use ($vars, $escape): string {
                $name = $match[1];
                if (! array_key_exists($name, $vars)) {
                    return $match[0]; // leave literal
                }
                $value = (string) ($vars[$name] ?? '');

                return $escape ? e($value) : $value;
            },
            $template,
        ) ?? $template;
    }
}
