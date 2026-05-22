<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Enums\LeadStatus;
use App\Enums\QuotationStatus;
use App\Enums\TripStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Quotation;
use App\Models\Trip;
use Carbon\CarbonImmutable;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CarCategorySeeder;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
    $this->seed(BranchSeeder::class);
    $this->seed(CarCategorySeeder::class);
    $this->seed(RateCardSeeder::class);
});

it('renders the portal login form', function (): void {
    $this->get('/portal/login')
        ->assertOk()
        ->assertSee('name="identifier"', false)
        ->assertSee('name="password"', false);
});

it('logs a customer in with email + password', function (): void {
    $customer = Customer::factory()->create([
        'email' => 'cx@example.com',
        'password' => 'cust-pw',
    ]);

    $this->post('/portal/login', ['identifier' => 'cx@example.com', 'password' => 'cust-pw'])
        ->assertRedirect('/portal');

    $this->assertAuthenticatedAs($customer, 'customer');
});

it('logs a customer in with phone + password', function (): void {
    $customer = Customer::factory()->create([
        'phone' => '+201112223333',
        'password' => 'cust-pw',
    ]);

    $this->post('/portal/login', ['identifier' => '+201112223333', 'password' => 'cust-pw'])
        ->assertRedirect('/portal');

    $this->assertAuthenticatedAs($customer, 'customer');
});

it('blocks blacklisted customers from logging in', function (): void {
    Customer::factory()->create([
        'email' => 'bad@example.com',
        'password' => 'cust-pw',
        'is_blacklisted' => true,
    ]);

    $this->post('/portal/login', ['identifier' => 'bad@example.com', 'password' => 'cust-pw'])
        ->assertSessionHasErrors('identifier');

    $this->assertGuest('customer');
});

it('redirects guests from /portal to /portal/login', function (): void {
    $this->get('/portal')->assertRedirect('/portal/login');
});

it("scopes the customer's dashboard to their own trips and invoices", function (): void {
    $me = Customer::factory()->create();
    $other = Customer::factory()->create();

    $myTrip = Trip::factory()->create([
        'customer_id' => $me->id,
        'status' => TripStatus::Confirmed,
        'scheduled_start' => CarbonImmutable::tomorrow(),
        'scheduled_end' => CarbonImmutable::tomorrow()->addHours(8),
    ]);
    $stalkerTrip = Trip::factory()->create([
        'customer_id' => $other->id,
        'status' => TripStatus::Confirmed,
        'scheduled_start' => CarbonImmutable::tomorrow(),
        'scheduled_end' => CarbonImmutable::tomorrow()->addHours(8),
    ]);

    $this->actingAs($me, 'customer')
        ->get('/portal')
        ->assertOk()
        ->assertSee($myTrip->trip_number)
        ->assertDontSee($stalkerTrip->trip_number);
});

it("won't let one customer see another customer's trip detail (404)", function (): void {
    $me = Customer::factory()->create();
    $stranger = Customer::factory()->create();
    $strangerTrip = Trip::factory()->create(['customer_id' => $stranger->id]);

    $this->actingAs($me, 'customer')
        ->get("/portal/trips/{$strangerTrip->id}")
        ->assertNotFound();
});

it('lets the customer accept a sent quotation', function (): void {
    $customer = Customer::factory()->create();
    $quote = Quotation::factory()->create([
        'customer_id' => $customer->id,
        'status' => QuotationStatus::Sent,
    ]);

    $this->actingAs($customer, 'customer')
        ->post("/portal/quotations/{$quote->id}/decide", ['action' => 'accept'])
        ->assertRedirect();

    expect($quote->fresh()->status)->toBe(QuotationStatus::Accepted);
});

it('refuses to decide a quotation that is already accepted', function (): void {
    $customer = Customer::factory()->create();
    $quote = Quotation::factory()->create([
        'customer_id' => $customer->id,
        'status' => QuotationStatus::Accepted,
    ]);

    $this->actingAs($customer, 'customer')
        ->post("/portal/quotations/{$quote->id}/decide", ['action' => 'reject'])
        ->assertSessionHasErrors('action');

    expect($quote->fresh()->status)->toBe(QuotationStatus::Accepted);
});

it('creates a lead when the customer submits a booking request', function (): void {
    $customer = Customer::factory()->create();

    $resp = $this->actingAs($customer, 'customer')->post('/portal/booking', [
        'pickup_at' => CarbonImmutable::tomorrow()->setHour(9)->toIso8601String(),
        'dropoff_at' => CarbonImmutable::tomorrow()->setHour(17)->toIso8601String(),
        'pickup_location' => 'Cairo Hotel',
        'dropoff_location' => 'Alexandria Port',
        'estimated_distance_km' => 220,
        'requirements' => 'Need an SUV with a child seat. 4 passengers total.',
    ]);

    $resp->assertRedirect('/portal');

    $lead = Lead::query()->where('customer_id', $customer->id)->first();
    expect($lead)->not->toBeNull()
        ->and($lead->status)->toBe(LeadStatus::New_);
});

it('streams an invoice PDF when the customer requests their own invoice', function (): void {
    $customer = Customer::factory()->create();
    $invoice = Invoice::factory()->create(['customer_id' => $customer->id, 'status' => InvoiceStatus::Sent]);

    $resp = $this->actingAs($customer, 'customer')->get("/portal/invoices/{$invoice->id}/pdf");

    $resp->assertOk();
    expect($resp->headers->get('Content-Type'))->toContain('application/pdf');
});

it("won't stream another customer's invoice", function (): void {
    $me = Customer::factory()->create();
    $stranger = Customer::factory()->create();
    $strangerInv = Invoice::factory()->create(['customer_id' => $stranger->id]);

    $this->actingAs($me, 'customer')
        ->get("/portal/invoices/{$strangerInv->id}/pdf")
        ->assertNotFound();
});
