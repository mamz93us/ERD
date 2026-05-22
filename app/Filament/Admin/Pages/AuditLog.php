<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use BackedEnum;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Models\Audit;

/**
 * Phase 15: read-only viewer over the owen-it/laravel-auditing `audits`
 * table. Every audited model (invoices, payments, credit notes, traffic
 * fines deduction, blacklist toggle, translations etc.) writes a row
 * here per CLAUDE.md §10. This page lets the operator search/filter
 * those rows.
 *
 * No edit/delete — audit rows are immutable evidence.
 */
class AuditLog extends Page implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected string $view = 'filament.admin.pages.audit-log';

    public static function getNavigationLabel(): string
    {
        return __('navigation.audit_log');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.settings');
    }

    public function getTitle(): string
    {
        return __('navigation.audit_log');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Audit::query()->latest('created_at'))
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('audit.timestamp'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('event')
                    ->label(__('audit.event'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'restored' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('auditable_type')
                    ->label(__('audit.model'))
                    ->formatStateUsing(fn (string $state) => class_basename($state))
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('auditable_id')
                    ->label(__('audit.record_id'))
                    ->limit(13)
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('user.full_name')
                    ->label(__('audit.user'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('ip_address')
                    ->label(__('audit.ip'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('url')
                    ->label(__('audit.url'))
                    ->limit(48)
                    ->toggleable(),
                TextColumn::make('changes_summary')
                    ->label(__('audit.changes'))
                    ->state(function (Audit $rec): string {
                        $changes = collect($rec->new_values ?? [])
                            ->map(function ($new, $field) use ($rec) {
                                $old = $rec->old_values[$field] ?? null;
                                $oldText = is_scalar($old) || $old === null ? (string) ($old ?? '∅') : '[...]';
                                $newText = is_scalar($new) || $new === null ? (string) ($new ?? '∅') : '[...]';

                                return "{$field}: {$oldText} → {$newText}";
                            })
                            ->take(3)
                            ->implode("\n");

                        return $changes ?: '—';
                    })
                    ->wrap()
                    ->extraAttributes(['style' => 'white-space: pre-wrap; font-size: 11px;']),
            ])
            ->filters([
                SelectFilter::make('event')->options([
                    'created' => 'created',
                    'updated' => 'updated',
                    'deleted' => 'deleted',
                    'restored' => 'restored',
                ]),
                SelectFilter::make('auditable_type')
                    ->label(__('audit.model'))
                    ->options(function () {
                        return \DB::table('audits')
                            ->distinct()
                            ->pluck('auditable_type', 'auditable_type')
                            ->map(fn ($t) => class_basename($t))
                            ->toArray();
                    }),
                TernaryFilter::make('with_user')
                    ->label(__('audit.has_user'))
                    ->placeholder(__('audit.any'))
                    ->queries(
                        true: fn (Builder $q) => $q->whereNotNull('user_id'),
                        false: fn (Builder $q) => $q->whereNull('user_id'),
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }
}
