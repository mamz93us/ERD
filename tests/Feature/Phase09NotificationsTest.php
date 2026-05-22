<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\NotificationTemplate;
use App\Models\Trip;
use App\Notifications\BookingConfirmed;
use App\Notifications\InvoiceIssued;
use App\Services\Notifications\TemplateRenderer;
use App\Services\Notifications\WhatsappService;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\NotificationTemplateSeeder;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification as NotificationFacade;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
    $this->seed(CarCategorySeeder::class);
    $this->seed(RateCardSeeder::class);
    $this->seed(NotificationTemplateSeeder::class);
});

/* ============================================================================
 * TemplateRenderer
 * ========================================================================== */

it('TemplateRenderer substitutes {{var}} placeholders for known keys', function (): void {
    NotificationTemplate::factory()->create([
        'key' => 'test_hello',
        'channel' => 'whatsapp',
        'locale' => 'ar',
        'body' => 'مرحباً {{name}}، رقم {{ref}}',
        'is_active' => true,
    ]);

    $r = app(TemplateRenderer::class)->render('test_hello', 'whatsapp', 'ar', [
        'name' => 'محمد',
        'ref' => 'T-001',
    ]);

    expect($r->body)->toBe('مرحباً محمد، رقم T-001')
        ->and($r->templateFound)->toBeTrue();
});

it('TemplateRenderer leaves unknown placeholders literal so missing vars surface visibly', function (): void {
    NotificationTemplate::factory()->create([
        'key' => 'test_unk',
        'channel' => 'whatsapp',
        'locale' => 'ar',
        'body' => 'Hi {{name}}, your {{missing_var}} is ready',
    ]);

    $r = app(TemplateRenderer::class)->render('test_unk', 'whatsapp', 'ar', ['name' => 'X']);

    expect($r->body)->toBe('Hi X, your {{missing_var}} is ready');
});

it('TemplateRenderer escapes HTML in mail channel (XSS defense)', function (): void {
    NotificationTemplate::factory()->create([
        'key' => 'test_xss',
        'channel' => 'mail',
        'locale' => 'en',
        'body' => 'Hello {{name}}',
    ]);

    $r = app(TemplateRenderer::class)->render('test_xss', 'mail', 'en', [
        'name' => '<script>alert(1)</script>',
    ]);

    expect($r->body)->not->toContain('<script>')
        ->and($r->body)->toContain('&lt;script&gt;');
});

it('TemplateRenderer does NOT escape HTML in WhatsApp channel (Green API takes plain text)', function (): void {
    NotificationTemplate::factory()->create([
        'key' => 'test_wa',
        'channel' => 'whatsapp',
        'locale' => 'en',
        'body' => 'Hello {{name}}',
    ]);

    $r = app(TemplateRenderer::class)->render('test_wa', 'whatsapp', 'en', [
        'name' => "O'Brien & Co",
    ]);

    expect($r->body)->toBe("Hello O'Brien & Co");
});

it('TemplateRenderer falls back to en when requested locale missing', function (): void {
    NotificationTemplate::factory()->create([
        'key' => 'fb_test',
        'channel' => 'whatsapp',
        'locale' => 'en',
        'body' => 'English body',
    ]);

    $r = app(TemplateRenderer::class)->render('fb_test', 'whatsapp', 'ar', []);

    expect($r->body)->toBe('English body')->and($r->templateFound)->toBeTrue();
});

it('TemplateRenderer returns sentinel when no template found at all', function (): void {
    $r = app(TemplateRenderer::class)->render('no_such_key', 'whatsapp', 'ar', []);

    expect($r->templateFound)->toBeFalse()
        ->and($r->body)->toContain('missing template');
});

/* ============================================================================
 * WhatsappService
 * ========================================================================== */

it('WhatsappService hits the configured Green API endpoint and parses idMessage', function (): void {
    Http::fake([
        'api.green-api.com/*' => Http::response(['idMessage' => 'wa-abc-123'], 200),
    ]);

    $svc = new WhatsappService('https://api.green-api.com', '1101000001', 'token-xyz');
    $id = $svc->send('+20 100 123 4567', 'hi');

    expect($id)->toBe('wa-abc-123');

    Http::assertSent(function ($req) {
        return str_contains($req->url(), 'waInstance1101000001')
            && str_contains($req->url(), '/sendMessage/token-xyz')
            && $req['chatId'] === '201001234567@c.us'
            && $req['message'] === 'hi';
    });
});

it('WhatsappService returns null and logs on non-2xx Green API response', function (): void {
    Http::fake([
        'api.green-api.com/*' => Http::response(['error' => 'bad token'], 401),
    ]);

    $svc = new WhatsappService('https://api.green-api.com', '1101000001', 'token-xyz');
    expect($svc->send('+201001234567', 'hi'))->toBeNull();
});

it('WhatsappService short-circuits in dev when instance_id/token are empty', function (): void {
    Http::fake();

    $svc = new WhatsappService('https://api.green-api.com', null, null);
    $id = $svc->send('+201001234567', 'hi');

    expect($id)->toStartWith('fake-id-');
    Http::assertNothingSent();
});

/* ============================================================================
 * Notification dispatch end-to-end
 * ========================================================================== */

it('Customer with phone + email receives BookingConfirmed on database + whatsapp + mail', function (): void {
    NotificationFacade::fake();

    $customer = Customer::factory()->create([
        'whatsapp_phone' => '+201001234567',
        'email' => 'customer@example.com',
    ]);
    $trip = Trip::factory()->create(['customer_id' => $customer->id]);

    $customer->notify(new BookingConfirmed($trip));

    NotificationFacade::assertSentTo($customer, BookingConfirmed::class, function (BookingConfirmed $n, array $channels) {
        return in_array('database', $channels, true)
            && in_array('whatsapp', $channels, true)
            && in_array('mail', $channels, true);
    });
});

it('Customer with no email skips the mail channel and only sends to database + whatsapp', function (): void {
    NotificationFacade::fake();

    $customer = Customer::factory()->create([
        'whatsapp_phone' => '+201001234567',
        'email' => null,
    ]);
    $trip = Trip::factory()->create(['customer_id' => $customer->id]);

    $customer->notify(new BookingConfirmed($trip));

    NotificationFacade::assertSentTo($customer, BookingConfirmed::class, function (BookingConfirmed $n, array $channels) {
        return in_array('database', $channels, true)
            && in_array('whatsapp', $channels, true)
            && ! in_array('mail', $channels, true);
    });
});

it('InvoiceIssued.toMail attaches the bilingual invoice PDF', function (): void {
    $customer = Customer::factory()->create(['email' => 'c@example.com']);
    $invoice = Invoice::factory()->create(['customer_id' => $customer->id]);

    $msg = (new InvoiceIssued($invoice))->toMail($customer);

    expect($msg->rawAttachments)->not->toBeEmpty()
        ->and($msg->rawAttachments[0]['name'])->toBe("{$invoice->invoice_number}.pdf");
});

it('NotificationTemplateSeeder seeds 28 default rows (7 notifications × 2 channels × 2 locales)', function (): void {
    // NotificationTemplateSeeder already ran in beforeEach
    expect(NotificationTemplate::count())->toBeGreaterThanOrEqual(28);

    foreach (['booking_confirmed', 'trip_reminder_24h', 'trip_assigned', 'invoice_issued', 'payment_received', 'document_expiring_soon', 'credit_note_approval_needed'] as $key) {
        expect(NotificationTemplate::where('key', $key)->count())->toBeGreaterThanOrEqual(4);
    }
});
