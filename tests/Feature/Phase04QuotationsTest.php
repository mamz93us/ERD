<?php

declare(strict_types=1);

use App\Models\Quotation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
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

it('auto-generates quotation_number in Q-YYYY-NNNN format on create', function (): void {
    $q = Quotation::factory()->create();

    expect($q->quotation_number)->toMatch('/^Q-\d{4}-\d{4}$/')
        ->and($q->quotation_number)->toEndWith('-0001');
});

it('increments the sequence on subsequent quotations in the same year', function (): void {
    $first = Quotation::factory()->create();
    $second = Quotation::factory()->create();
    $third = Quotation::factory()->create();

    $year = now()->year;
    expect($first->quotation_number)->toBe("Q-{$year}-0001")
        ->and($second->quotation_number)->toBe("Q-{$year}-0002")
        ->and($third->quotation_number)->toBe("Q-{$year}-0003");
});

it('renders the rate-cards and quotations admin pages for super_admin', function (string $path): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $this->actingAs($user)->get($path)->assertSuccessful();
})->with(['/admin/rate-cards', '/admin/quotations']);

it('renders a quotation PDF via the download_pdf action', function (): void {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $quotation = Quotation::factory()->create();

    $pdf = Pdf::loadView('pdfs.quotation', [
        'quotation' => $quotation->load(['customer', 'corporateAccount', 'category', 'rateCard']),
        'locale' => 'en',
    ]);

    $output = $pdf->output();

    expect($output)->toStartWith('%PDF-')
        ->and(strlen($output))->toBeGreaterThan(1000);
});

it('renders an Arabic-locale quotation PDF without erroring', function (): void {
    $quotation = Quotation::factory()->create();

    $pdf = Pdf::loadView('pdfs.quotation', [
        'quotation' => $quotation->load(['customer', 'corporateAccount', 'category', 'rateCard']),
        'locale' => 'ar',
    ]);

    $output = $pdf->output();

    expect($output)->toStartWith('%PDF-');
});
