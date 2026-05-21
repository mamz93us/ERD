# Adly Group Agency Car Rental Management System (CRMS)

> **Project prompt for Claude Code.** Read this entire document before writing any code. This is the source of truth for scope, conventions, and constraints. Update it as decisions evolve.
>
> **Rebrand & restack note (2026-05-20).** This spec was originally written for "SamirGroup–SSS" on Laravel 11 + Filament v3 with file-based translations. Before any code was written, the owner rebranded the system to **Adly Group Agency** and upgraded the stack to **Laravel 12 + Filament v5 + DB-backed editable translations**. (Filament v4 was the owner's stated preference but v4.0.0 had a security advisory with no patched 4.x release; composer rolled forward to v5.6 — the current supported line — and we accepted that.) All other choices stand. See `docs/DECISIONS.md` entries `001`–`003` for the rationale.

---

## 1. Mission

Build a production-ready Laravel 12 car rental management system for Adly Group Agency in Egypt. Chauffeur-driven model (every trip has a driver). Multi-branch (ABH, CAI, extendable). 20–100 vehicles. Full lifecycle: CRM → quote → trip scheduling → execution → invoicing → accounting → maintenance → compliance, with sub-rental from partner agencies. Bilingual (Arabic primary, English secondary), RTL-first, **with admin-editable translations** so the owner can revise copy without redeploy.

Deployment target: WHM/cPanel AlmaLinux server, MySQL 8 or MariaDB 10.4+.

---

## 2. Business Context

- **Industry**: Egyptian car rental, chauffeur-driven only
- **Branches**: ABH (Abu Hammad) and CAI (Cairo) at launch, system must support N branches
- **Fleet**: mix of owned vehicles and sub-rented from partner agencies to cover demand spikes
- **Customers**: individuals, corporate accounts (B2B), travel agencies, hotels
- **Languages**: Arabic (default, RTL) + English (LTR) — switchable per user, **editable in admin**
- **Currency**: EGP (Egyptian Pound), `decimal(15,2)` everywhere
- **Tax**: VAT 14% on services, with future hook for Egyptian e-invoice portal (`مصلحة الضرائب`)
- **Timezone**: store in UTC, display in `Africa/Cairo`
- **Notifications**: WhatsApp (Green API — owner has account) + Email (Amazon SES smart host — already configured on WHM)

---

## 3. Tech Stack (mandatory — do not substitute)

| Concern | Choice | Notes |
|---|---|---|
| Language | PHP 8.2+ | cPanel selectable PHP version |
| Framework | **Laravel 12.x** | Owner-confirmed upgrade from spec's original L11 |
| Database | MySQL 8.0 (InnoDB) **or MariaDB 10.4+** | cPanel supports both; local dev uses MariaDB via XAMPP |
| Admin UI | **Filament v5** | Owner asked for v4 but v4.0.0 had a security advisory with no patched 4.x release — composer rolled to the current supported line, v5.6. See DECISIONS.md #002. |
| Customer portal | Inertia.js + Vue 3 + Tailwind | Single SPA |
| Auth (admin) | Filament built-in | |
| Auth (customer) | Laravel Breeze (Inertia/Vue stack) | |
| Permissions | `spatie/laravel-permission` | Roles + permissions matrix |
| Audit log | `owen-it/laravel-auditing` | Mandatory on financial models AND `translations` rows |
| Media | `spatie/laravel-medialibrary` | Photos, documents, signatures |
| PDF | `barryvdh/laravel-dompdf` | Arabic font support required |
| Excel | `maatwebsite/excel` | Reports export + translation import/export |
| Localization (URLs) | `mcamara/laravel-localization` | URL prefix `/ar`, `/en` |
| **Localization (strings)** | **`spatie/laravel-translation-loader` + `translations` DB table** | DB is primary source; `lang/ar/`+`lang/en/` are fallback only |
| Queues | `database` driver | Cron-based, NOT long-running worker (cPanel limitation) |
| Cache/sessions | `database` driver | Redis not guaranteed on shared cPanel |
| Mail | SMTP via Amazon SES | Use env-driven config |
| WhatsApp | Green API REST | Custom `WhatsappService` class |
| Testing | PestPHP | Faster syntax than PHPUnit |

---

## 4. Critical cPanel/WHM Deployment Constraints

Owner has previously deployed a Laravel app on WHM/cPanel AlmaLinux at `t.samirgroup.net` and hit these limits. Build with them in mind from Day 1.

1. **No long-running queue workers**. Use `database` queue driver. Process via cron: `* * * * * cd /home/USER/app && php artisan queue:work --stop-when-empty --max-time=55`. Plus standard `* * * * * php artisan schedule:run`.
2. **Composer limitations server-side**. Plan for hybrid local-build deploys: run `composer install --optimize-autoloader --no-dev` and `npm run build` locally, upload `vendor/` and `public/build/` via cPanel File Manager or FTP. Document this in `DEPLOY.md`.
3. **Public folder pattern**. Application lives outside `public_html/`. Contents of `public/` are copied/symlinked into the domain doc root, with `index.php` paths adjusted. Document this exact procedure.
4. **No root access**. Don't assume `apt`, `systemctl`, custom PHP extensions beyond what cPanel offers (selectable: bcmath, intl, mbstring, gd, exif, fileinfo, mysqli, pdo_mysql, zip, curl, openssl, sodium — all need to be enabled).
5. **Storage symlink**. `php artisan storage:link` must be runnable once via cPanel terminal or scheduled task.
6. **File permissions**. `storage/` and `bootstrap/cache/` → `775`. Owner = cPanel user.
7. **No Docker, no Sail**. Local dev uses XAMPP on Windows (MariaDB 10.4); production is bare PHP-FPM.
8. **MySQL or MariaDB, not Postgres**. We had planned PostgreSQL `EXCLUDE USING GIST` constraints for booking overlap prevention. Replace with **triggers + application-layer pessimistic locking** (spec below). The trigger syntax in §5 Phase 5 works on both MySQL 8 and MariaDB 10.4+.
9. **HTTPS**. AutoSSL via cPanel — assume cert is in place, force HTTPS in `.htaccess` and `AppServiceProvider` (`URL::forceScheme('https')` in production).
10. **Cron jobs** are configured via cPanel UI. Required crons documented in `DEPLOY.md`.

---

## 5. Database Schema (ERD)

The complete data model. UUID primary keys throughout. All money is `decimal(15,2)`. All FKs default `ON DELETE RESTRICT` unless noted.

### 5.1 Organization

**branches**: `id (uuid pk)`, `code (unique, e.g. ABH/CAI)`, `name`, `name_ar`, `city`, `address`, `phone`, `manager_user_id (fk users)`, timestamps, soft deletes.

**roles** (from spatie/laravel-permission, standard schema).

**users**: `id (uuid pk)`, `branch_id (fk)`, `email (unique)`, `password`, `full_name`, `full_name_ar`, `phone`, `is_active`, `preferred_locale (ar|en)`, timestamps, soft deletes.

**translations** *(added 2026-05-20 for editable bilingual UI — see DECISIONS.md #003)*: `id (uuid pk)`, `group (e.g. trips, auth, validation)`, `key (e.g. trips.create)`, `text_ar (text)`, `text_en (text)`, `is_system (boolean — true for shipped strings, false for owner-added)`, `updated_by_user_id (fk users nullable)`, timestamps. **Unique** on `(group, key)`. **Index** on `key`. Loaded via `spatie/laravel-translation-loader`; file-based `lang/ar/`+`lang/en/` remain as fallback only.

### 5.2 Fleet

**car_categories**: `id (uuid pk)`, `name`, `name_ar`, `class_code (economy|midsize|suv|luxury|van|minibus)`, `default_seats`, `sort_order`.

**partner_agencies**: `id (uuid pk)`, `name`, `name_ar`, `contact_person`, `phone`, `email`, `tax_id`, `address`, `credit_limit`, `payment_terms_days`, `is_active`, timestamps, soft deletes.

**cars**: `id (uuid pk)`, `branch_id (fk)`, `category_id (fk car_categories)`, `plate (unique)`, `vin (unique nullable)`, `make`, `model`, `year`, `color`, `transmission (manual|auto)`, `fuel_type (petrol|diesel|hybrid|electric)`, `seats`, `ownership_type (owned|sub_rented|replacement)`, `status (available|on_trip|in_maintenance|at_partner|out_of_service)`, `current_odometer`, `acquisition_date`, `acquisition_cost (nullable)`, `notes`, timestamps, soft deletes. **Index** on `(status, branch_id)` and `(ownership_type)`.

**sub_rental_contracts**: `id (uuid pk)`, `partner_agency_id (fk)`, `car_id (fk cars)`, `start_date`, `end_date`, `daily_cost`, `included_km_per_day (nullable)`, `extra_km_cost (nullable)`, `terms (text)`, `status (active|expired|cancelled)`, `contract_file_path`, timestamps. **Constraint**: when `cars.ownership_type = 'sub_rented'`, must have one active contract.

**car_documents**: `id (uuid pk)`, `car_id (fk)`, `doc_type (registration_license|compulsory_insurance|comprehensive_insurance|technical_inspection|inspection_sticker)`, `document_number`, `issue_date`, `expiry_date`, `issuer`, `cost (nullable)`, `file_path`, `is_active (boolean)`, timestamps. Old docs kept with `is_active=false`. Unique on `(car_id, doc_type, is_active=true)` via partial index logic (enforce in observer).

### 5.3 Drivers

**drivers**: `id (uuid pk)`, `branch_id (fk)`, `national_id (unique)`, `full_name`, `full_name_ar`, `phone`, `whatsapp_phone`, `address`, `date_of_birth`, `hire_date`, `employment_type (salaried|freelance|on_demand)`, `base_salary`, `trip_commission_percentage`, `status (active|on_leave|suspended|terminated)`, `rating (decimal 3,2)`, `notes`, timestamps, soft deletes.

**driver_documents**: `id (uuid pk)`, `driver_id (fk)`, `doc_type (driving_license|national_id|criminal_record|medical_certificate|professional_license)`, `document_number`, `issue_date`, `expiry_date`, `issuer`, `file_path`, `is_active`, timestamps.

**driver_earnings** *(scheduled for Phase 10, called out in §9.2)*: `id (uuid pk)`, `driver_id (fk)`, `trip_id (fk)`, `gross_commission`, `deductions (json — fines, advances)`, `net_payable`, `pay_period_start`, `pay_period_end`, `paid_at (nullable)`, `payment_reference (nullable)`, timestamps.

### 5.4 Customers & CRM

**corporate_accounts**: `id (uuid pk)`, `company_name`, `company_name_ar`, `tax_id`, `commercial_register`, `industry`, `address`, `billing_email`, `billing_phone`, `credit_limit`, `payment_terms_days`, `discount_percentage`, `is_active`, `notes`, timestamps, soft deletes.

**customers**: `id (uuid pk)`, `corporate_account_id (fk nullable)`, `type (individual|corporate_contact|vip)`, `full_name`, `full_name_ar`, `phone`, `whatsapp_phone`, `email`, `national_id (nullable)`, `address`, `preferred_language (ar|en)`, `loyalty_points (default 0)`, `is_blacklisted (default false)`, `blacklist_reason (nullable)`, `notes`, timestamps, soft deletes.

**customer_communications**: `id (uuid pk)`, `customer_id (fk)`, `user_id (fk nullable, who logged it)`, `channel (whatsapp|email|phone|in_person|sms)`, `direction (inbound|outbound)`, `subject (nullable)`, `body (text)`, `attachments (json nullable)`, `external_message_id (nullable)`, `sent_at`, timestamps.

**leads**: `id (uuid pk)`, `customer_id (fk nullable)`, `assigned_user_id (fk users)`, `source (whatsapp|website|referral|walk_in|phone|corporate)`, `status (new|contacted|quoted|won|lost)`, `requirements (text)`, `estimated_value`, `lost_reason (nullable)`, `due_at (nullable)`, `closed_at (nullable)`, timestamps.

### 5.5 Pricing & Operations

**rate_cards**: `id (uuid pk)`, `category_id (fk car_categories)`, `corporate_account_id (fk nullable, null = default)`, `name`, `hourly_rate`, `daily_rate`, `weekly_rate`, `monthly_rate`, `included_km_per_day`, `extra_km_rate`, `extra_hour_rate`, `driver_daily_allowance`, `cross_city_surcharge`, `effective_from`, `effective_to (nullable)`, `is_active`, timestamps. **Index** on `(category_id, corporate_account_id, is_active)`.

**quotations**: `id (uuid pk)`, `quotation_number (unique, e.g. Q-2026-0001)`, `customer_id (fk)`, `corporate_account_id (fk nullable)`, `created_by_user_id (fk users)`, `pickup_at`, `dropoff_at`, `pickup_location`, `dropoff_location`, `estimated_distance_km`, `category_id (fk car_categories)`, `rate_card_id (fk)`, `subtotal`, `vat_amount`, `total_amount`, `valid_until`, `status (draft|sent|accepted|rejected|expired)`, `notes`, `terms_and_conditions (text)`, timestamps, soft deletes.

**trips**: `id (uuid pk)`, `trip_number (unique, T-2026-0001)`, `branch_id (fk)`, `customer_id (fk)`, `corporate_account_id (fk nullable)`, `car_id (fk)`, `driver_id (fk, NOT NULL — chauffeur model)`, `quotation_id (fk nullable)`, `rate_card_id (fk)`, `scheduled_start`, `scheduled_end`, `actual_start (nullable)`, `actual_end (nullable)`, `pickup_location`, `dropoff_location`, `start_odometer (nullable)`, `end_odometer (nullable)`, `status (draft|confirmed|assigned|en_route|in_progress|completed|invoiced|closed|cancelled|no_show)`, `cancellation_reason (nullable)`, `subtotal`, `vat_amount`, `total_amount`, `notes`, timestamps, soft deletes. **Indexes**: `(car_id, scheduled_start, scheduled_end)`, `(driver_id, scheduled_start, scheduled_end)`, `(status)`, `(customer_id)`.

**trip_inspections**: `id (uuid pk)`, `trip_id (fk)`, `stage (pickup|return)`, `inspector_user_id (fk users)`, `odometer`, `fuel_level (empty|quarter|half|three_quarter|full)`, `damage_marks (json)`, `accessories_checklist (json)`, `customer_signature_path (nullable)`, `driver_signature_path`, `photos (json)`, `notes`, `performed_at`, timestamps.

**trip_expenses**: `id (uuid pk)`, `trip_id (fk)`, `type (fuel|toll|parking|food|accommodation|misc)`, `amount`, `receipt_path (nullable)`, `reimbursed (boolean)`, `notes`, `incurred_at`, timestamps.

**trip_damage_reports**: `id (uuid pk)`, `trip_id (fk)`, `description`, `damage_area (json)`, `photos (json)`, `repair_cost_estimate`, `actual_repair_cost (nullable)`, `charged_to_customer (boolean)`, `customer_charge_amount (nullable)`, `status (reported|approved|repaired|disputed)`, timestamps.

### 5.6 Maintenance

**garages**: `id (uuid pk)`, `name`, `phone`, `address`, `is_internal (boolean)`, `specialties (json nullable)`, `is_active`, timestamps.

**maintenance_schedules**: `id (uuid pk)`, `car_id (fk)`, `service_type (oil_change|tire_rotation|brake_inspection|major_service|ac_service|battery_check|other)`, `interval_km`, `interval_days`, `last_done_km (nullable)`, `last_done_date (nullable)`, `next_due_km`, `next_due_date`, `is_active`, timestamps.

**maintenance_orders**: `id (uuid pk)`, `order_number (unique, M-2026-0001)`, `car_id (fk)`, `garage_id (fk)`, `order_type (preventive|corrective|accident_repair)`, `description`, `scheduled_start`, `scheduled_end`, `actual_start (nullable)`, `actual_end (nullable)`, `odometer_at_service`, `subtotal`, `vat_amount`, `total_cost`, `status (scheduled|in_service|completed|cancelled)`, `invoice_file_path (nullable)`, `notes`, timestamps, soft deletes. **Index** on `(car_id, scheduled_start, scheduled_end)`.

**maintenance_items**: `id (uuid pk)`, `maintenance_order_id (fk)`, `item_type (part|labor|consumable)`, `description`, `quantity`, `unit_cost`, `total_cost`, timestamps.

### 5.7 Compliance

**traffic_fines**: `id (uuid pk)`, `car_id (fk)`, `driver_id (fk nullable)`, `trip_id (fk nullable — auto-attributed)`, `violation_number`, `violation_date`, `violation_type`, `location`, `amount`, `payment_status (unpaid|paid|disputed|waived)`, `paid_date (nullable)`, `paid_amount (nullable)`, `deducted_from_driver (boolean)`, `notes`, `attachment_path`, timestamps.

**insurance_claims**: `id (uuid pk)`, `car_id (fk)`, `trip_id (fk nullable)`, `claim_number`, `incident_date`, `incident_location`, `description (text)`, `police_report_number (nullable)`, `claim_amount`, `payout_amount (nullable)`, `status (reported|submitted|under_review|approved|rejected|paid)`, `documents (json)`, `notes`, timestamps.

### 5.8 Finance

**invoices**: `id (uuid pk)`, `invoice_number (unique, INV-2026-0001)`, `customer_id (fk)`, `corporate_account_id (fk nullable)`, `trip_id (fk nullable)`, `issue_date`, `due_date`, `subtotal`, `vat_amount`, `discount_amount`, `total`, `paid_amount (default 0)`, `balance_due`, `currency (default EGP)`, `status (draft|sent|partially_paid|paid|overdue|cancelled)`, `notes`, `terms (text)`, `pdf_path`, `e_invoice_reference (nullable)`, timestamps, soft deletes. **Index** on `(customer_id, status)`, `(due_date, status)`.

**invoice_lines**: `id (uuid pk)`, `invoice_id (fk)`, `description`, `quantity`, `unit_price`, `discount_amount`, `vat_rate`, `vat_amount`, `line_total`, `trip_id (fk nullable — for consolidated invoices linking back to trips)`, `sort_order`, timestamps.

**credit_notes**: `id (uuid pk)`, `note_number (unique, CN-2026-0001)`, `invoice_id (fk)`, `created_by_user_id (fk)`, `approved_by_user_id (fk nullable)`, `issue_date`, `reason (cancellation|service_complaint|goodwill|deposit_return|billing_error|other)`, `reason_details`, `amount`, `status (draft|pending_approval|approved|applied|rejected)`, `approved_at (nullable)`, `applied_at (nullable)`, `pdf_path`, `e_invoice_reference (nullable)`, timestamps, soft deletes.

**payments**: `id (uuid pk)`, `payment_number (unique, P-2026-0001)`, `customer_id (fk)`, `corporate_account_id (fk nullable)`, `method (cash|bank_transfer|visa|mastercard|fawry|instapay|cheque)`, `amount`, `payment_date`, `reference_number (nullable, e.g. transaction ID)`, `received_by_user_id (fk)`, `branch_id (fk)`, `notes`, `is_reconciled (boolean default false)`, timestamps, soft deletes.

**payment_allocations**: `id (uuid pk)`, `payment_id (fk)`, `invoice_id (fk)`, `allocated_amount`, `allocated_at`, timestamps. Many-to-many bridge — one payment can apply to multiple invoices, one invoice can have multiple payments.

**vendor_bills**: `id (uuid pk)`, `bill_number`, `vendor_type (partner_agency|garage|fuel|insurance|other)`, `partner_agency_id (fk nullable)`, `garage_id (fk nullable)`, `bill_date`, `due_date`, `subtotal`, `vat_amount`, `total`, `paid_amount`, `balance_due`, `status (draft|received|partially_paid|paid|disputed)`, `related_car_id (fk nullable)`, `related_sub_rental_contract_id (fk nullable)`, `description`, `attachment_path`, timestamps, soft deletes.

**expenses**: `id (uuid pk)`, `branch_id (fk)`, `car_id (fk nullable)`, `driver_id (fk nullable)`, `category (fuel|maintenance|salaries|insurance|fines|office|utilities|marketing|depreciation|other)`, `amount`, `expense_date`, `paid_by (cash|bank|petty_cash)`, `paid_by_user_id (fk)`, `description`, `attachment_path`, timestamps, soft deletes.

### 5.9 System

**audit_logs** (managed by owen-it/laravel-auditing — standard schema).

**notifications** (Laravel's standard notifications table).

**jobs**, **failed_jobs** (Laravel queue standard).

---

## 6. Phased Build Plan

Build phase-by-phase. After each phase: commit to git, run tests, ask the owner for sign-off before moving on. Do not run multiple phases in parallel.

### Phase 0: Project Bootstrap
- `composer create-project laravel/laravel . "^12.0"`
- Install all packages from section 3 (including `spatie/laravel-translation-loader` and Filament v5)
- Initialize git, create `.gitignore` additions (e.g. `/public/build`, `/public/storage`)
- Set up `.env.example` with all required keys (DB, MAIL, GREEN_API_*, AWS_SES_*, APP_NAME="Adly Group Agency", APP_TIMEZONE=Africa/Cairo, APP_LOCALE=ar, APP_FALLBACK_LOCALE=en)
- Set up Pest, write a smoke test
- Create `docs/` folder with: `DEPLOY.md`, `SCHEMA.md` (copy section 5), `DECISIONS.md` (ADR log; starter entries 001 Laravel-12-over-11, 002 Filament-v5-over-v3, 003 DB-translations-over-files)
- README with quickstart

### Phase 1: Foundation Layer
- Configure spatie/permission with roles: `super_admin`, `branch_manager`, `dispatcher`, `accountant`, `reservations_agent`, `driver_supervisor`, `fleet_manager`
- Configure mcamara/localization with `ar` (default, RTL) and `en`, URL prefix routing
- **Create `translations` table + Filament v5 resource + plug in `spatie/laravel-translation-loader` so `__()` resolves from DB first, files as fallback**
- Configure Filament v5 admin panel at `/admin`
- Base layout: header with language switcher, branch selector (for multi-branch users), user menu
- Create `branches` migration + model + factory + seeder (ABH, CAI) + Filament resource
- Apply branch scoping middleware (non-super-admin users see only their branch's data)
- Set up audit logging baseline (including on `translations.text_ar`/`text_en`)
- Configure timezone (`Africa/Cairo` display, UTC storage)
- Tailwind RTL config

### Phase 2: Master Data
Migrations + Models + Filament v5 resources for:
- `car_categories` (seed with: Economy, Midsize, SUV, Luxury, Van, Minibus)
- `partner_agencies`
- `garages` (seed with one internal garage placeholder)
- `corporate_accounts`
- Driver: `drivers` + `driver_documents`
- Customer: `customers` + `customer_communications` + `leads`

Filament features per resource: searchable + filterable tables, multi-step forms where natural, bulk actions, exports to Excel. All labels resolved through DB translations.

### Phase 3: Fleet Module
- Migrations: `cars`, `car_documents`, `sub_rental_contracts`
- Models with relationships, scopes (`available()`, `inMaintenance()`, `documentExpiringWithin($days)`), accessors
- Filament `CarResource` with tabs: Basic info / Documents / Sub-rental contract (if applicable) / Damage map / Photos / Service history
- Damage map: SVG of car body with click-to-mark coordinates stored as `{side, x, y, severity, photo_id}`
- Background scheduled job: `CheckCarDocumentExpiry` runs daily at 06:00, alerts at 60/30/7/1 days before expiry via WhatsApp + email to assigned fleet_manager
- Dashboard widget: "Documents expiring soon" with traffic-light coloring

### Phase 4: Pricing & Quotations
- Migrations: `rate_cards`, `quotations`
- `PricingService` (pure class, unit-tested):
  ```
  calculate(categoryId, corporateAccountId|null, startDateTime, endDateTime, estimatedKm, addons[]): PricingResult
  ```
  PricingResult contains: rate card used, base amount, included km, extra km, extra hours, surcharges, subtotal, VAT, total
- `QuotationResource` in Filament v5 with live pricing preview as fields change
- Quotation PDF (Arabic-aware, with **Adly Group Agency** letterhead, RTL layout)
- "Send Quotation" action: WhatsApp link + Email with PDF attached
- Quotation → Trip conversion action (creates trip in `draft` status)

### Phase 5: Trip Scheduling — THE CRITICAL MODULE
- Migrations: `trips`, `trip_inspections`, `trip_expenses`, `trip_damage_reports`
- Models with state machine (use `spatie/laravel-model-states`)

**`BookingAvailabilityService`** (must exist and be the gatekeeper for all booking creation):
```
checkAvailability(carId, driverId, scheduledStart, scheduledEnd): AvailabilityResult
```
AvailabilityResult is an object reporting:
- `carConflict`: any other non-cancelled trip on this car overlapping the window ± 2hr buffer
- `driverConflict`: same check for driver ± 2hr buffer
- `maintenanceConflict`: any scheduled or in-service maintenance order on this car in the window
- `carDocumentExpiry`: any active car_document expiring inside the window
- `driverDocumentExpiry`: same for driver docs
- `subRentalCoverage`: if car is sub_rented, check active contract covers the window
- Returns `isAvailable: bool` + list of issues

**MySQL/MariaDB trigger for hard overlap prevention** (defense in depth):
```sql
DELIMITER $$
CREATE TRIGGER trips_no_car_overlap_insert
BEFORE INSERT ON trips
FOR EACH ROW
BEGIN
  DECLARE conflict_count INT;
  IF NEW.status NOT IN ('cancelled', 'no_show') THEN
    SELECT COUNT(*) INTO conflict_count
    FROM trips
    WHERE car_id = NEW.car_id
      AND status NOT IN ('cancelled', 'no_show', 'completed', 'closed')
      AND deleted_at IS NULL
      AND id != NEW.id
      AND (
        (NEW.scheduled_start BETWEEN scheduled_start AND scheduled_end)
        OR (NEW.scheduled_end BETWEEN scheduled_start AND scheduled_end)
        OR (scheduled_start BETWEEN NEW.scheduled_start AND NEW.scheduled_end)
      );
    IF conflict_count > 0 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Car booking overlap detected';
    END IF;
  END IF;
END$$
DELIMITER ;
```
Plus mirror trigger on UPDATE, plus equivalent pair for `driver_id`. Wrap booking creation in DB transaction with `SELECT ... FOR UPDATE` on the car and driver rows to prevent race conditions.

**Filament v5 `TripResource`**:
- Booking form with live availability check via Livewire (debounced)
- Conflict warnings shown inline, soft warnings allow override with reason
- Trip detail page with timeline of state transitions
- Pickup inspection form + Return inspection form (with damage diff)
- Trip expenses tab

**Calendar/Gantt view** as Filament v5 custom page:
- Cars on Y-axis, time on X-axis, trips as colored blocks
- Filterable by branch, category, status
- Drag-and-drop reschedule (with conflict re-check)
- Driver schedule as overlay swimlane

### Phase 6: Maintenance Module
- Migrations: `maintenance_schedules`, `maintenance_orders`, `maintenance_items`
- `MaintenanceScheduleService` with `recomputeNextDue()` after each completed order
- Scheduled job daily: check schedules vs `cars.current_odometer` and today's date, create draft `maintenance_orders` where due
- Filament v5 resources, with auto status flip on cars during `in_service`

### Phase 7: Compliance Module
- Migrations: `traffic_fines`, `insurance_claims`
- On `traffic_fines` create: `TrafficFineAttributionService` finds the trip active for that car at the violation timestamp and sets `trip_id` + `driver_id`
- Filament v5 resources with attachment upload
- Action: "Deduct from driver payroll" sets `deducted_from_driver = true`

### Phase 8: Accounting Module
- Migrations: `invoices`, `invoice_lines`, `credit_notes`, `payments`, `payment_allocations`, `vendor_bills`, `expenses`
- `InvoiceService`:
  - `generateFromTrip(Trip): Invoice` (single trip)
  - `generateConsolidatedForCorporate(CorporateAccount, monthStart, monthEnd): Invoice` (monthly batch)
- `CreditNoteService` with approval workflow (threshold > 5000 EGP needs `branch_manager` or above)
- `PaymentService.allocate(Payment, [invoiceId => amount]): void`
- Invoice PDF template (bilingual, VAT breakdown, payment terms, Adly Group letterhead)
- Reports:
  - Customer aging (0–30 / 31–60 / 61–90 / 90+ days)
  - Vendor aging
  - Revenue per car per month
  - Per-car P&L (revenue − direct costs − allocated overhead)
  - VAT report (sales/purchases for tax filing)
  - Cash flow (in/out by date)
- Hook stub for Egyptian e-invoice portal submission (placeholder, real integration later)

### Phase 9: Notifications
- `WhatsappService` using Green API REST (re-use owner's existing pattern)
- `NotificationChannels`: WhatsApp + Email + Database
- Notification classes for:
  - `BookingConfirmed` (to customer)
  - `TripReminder24h` (to customer + driver)
  - `TripAssigned` (to driver)
  - `InvoiceIssued` (to customer with PDF)
  - `PaymentReceived` (to customer)
  - `DocumentExpiringSoon` (to fleet_manager)
  - `CreditNoteApprovalNeeded` (to branch_manager)
- Templates with Arabic + English variants, **editable in admin (via a `templates` table, scoped to this phase)**
- Bulk notification action in Filament for marketing (e.g., promo to all corporate accounts)

### Phase 10: Driver Mobile-Friendly View
- Mobile-first responsive web view at `/driver` (no native app yet — phase later)
- Driver logs in with phone + OTP (or simple password)
- Sees: today's trips, next trip, action buttons
- Trip start: capture odometer photo, fuel level, customer signature
- Trip end: same plus damage photos
- Expense submission with receipt photo
- Read-only payroll view backed by `driver_earnings` table

### Phase 11: Customer Portal
- Laravel Breeze Inertia/Vue install at `/portal`
- Pages: dashboard, request booking, view quotations (accept/reject), active trips, trip history, invoices (download PDF), profile, documents
- WhatsApp link for support
- All UI text resolved from same DB `translations` table

### Phase 12: Dashboards & KPIs
- Admin dashboard widgets:
  - Active trips count
  - Fleet utilization % (trips hours / available hours, last 30 days)
  - RevPACD (revenue per available car day)
  - Cars in maintenance count
  - Documents expiring within 30 days
  - Outstanding receivables (top 10 customers by aging)
  - This month's revenue vs last month
  - Driver leaderboard (trips completed, rating)
- All widgets respect branch scoping

### Phase 13: Hardening
- Permission tests (every role tries every action, expected allow/deny)
- Feature tests for full booking → trip → invoice → payment lifecycle
- Concurrency test: 10 parallel attempts to book the same car
- Translation-loader integrity test: assert DB-stored translation overrides file fallback
- Backup script: daily mysqldump + storage tarball to off-site
- Rate limiting on auth endpoints
- CSP headers, security headers in middleware

### Phase 14: Deployment
- Build `DEPLOY.md` with exact cPanel steps
- Local build artifacts: `composer install --no-dev --optimize-autoloader`, `npm run build`
- Sanitize `.env.production.example` (no secrets)
- Public folder migration steps documented
- Cron entries documented
- Storage symlink command documented
- Migration plan for production data (if migrating from manual records)
- Post-deploy: translator seeds final ar/en copy edits into `translations` table via admin

---

## 7. Coding Conventions

- **PSR-12 strict**. Run Pint before commits.
- **UUIDs everywhere** as primary keys. Use the `HasUuids` trait on every model.
- **Migrations one table per file**, named with timestamp + descriptive name. Order them so FK targets exist first.
- **Models** in `app/Models/`, with: traits → constants → casts → relationships → scopes → accessors → mutators → methods.
- **Service classes** in `app/Services/{Module}/`, single-purpose, dependency-injected. Controllers stay thin (call service, return response).
- **Form Requests** in `app/Http/Requests/{Module}/`. Never validate in controllers.
- **Policies** in `app/Policies/`, registered in `AuthServiceProvider`. Use Filament's policy auto-detection.
- **Events & Listeners** for side-effects (e.g. `TripCompleted` → `GenerateInvoice`, `IncrementLoyaltyPoints`).
- **Observers** for cross-cutting model concerns (e.g. setting branch_id from auth user).
- **NO Repository pattern** — Eloquent is sufficient.
- **Translation keys** from day one. Never hardcode user-facing strings. Strings resolve from `translations` table (Phase 1 loader); `lang/ar/`+`lang/en/` files are fallback only.
- **Money** is always `decimal(15,2)`, never float. Use `Brick\Money\Money` or simple decimal handling consistently.
- **Dates** stored in UTC, displayed in `Africa/Cairo`. Use `CarbonImmutable` everywhere.
- **Tests** with Pest. Aim 70%+ on services, full feature tests for booking + invoicing flows.

---

## 8. Folder Structure

```
app/
  Filament/
    Resources/
      {ModuleName}/
        Pages/
        RelationManagers/
    Pages/         # custom pages (calendar, dashboards)
    Widgets/
  Models/
  Services/
    Booking/
    Pricing/
    Invoicing/
    Notifications/
    Maintenance/
  Http/
    Controllers/
      Portal/      # customer portal
      Driver/      # driver web app
    Requests/
    Middleware/
  Policies/
  Events/
  Listeners/
  Notifications/
  Observers/
  Enums/           # PHP 8.1+ enums for all status fields
  Support/         # helpers, value objects
docs/
  DEPLOY.md
  SCHEMA.md
  DECISIONS.md
  ADRs/            # one file per architectural decision
database/
  migrations/
  seeders/
  factories/
lang/
  ar/              # fallback only — DB translations are primary
  en/              # fallback only
resources/
  views/           # blade for emails, PDFs
  js/
    Pages/         # Inertia/Vue pages for portal
  css/
tests/
  Feature/
  Unit/
```

---

## 9. Specific Implementation Notes

### 9.1 Damage Map JSON

```json
{
  "version": 1,
  "marks": [
    {
      "id": "uuid",
      "side": "front_left|front_right|rear_left|rear_right|top|bottom",
      "x": 0.42,
      "y": 0.18,
      "severity": "scratch|dent|crack|missing|other",
      "size_cm": 5,
      "photo_path": "trip-damage/abc.jpg",
      "noted_at": "2026-05-20T10:30:00Z",
      "noted_by_user_id": "uuid"
    }
  ]
}
```

### 9.2 Driver Commission Calculation

On trip completion:
```
commission_amount = trip.subtotal * driver.trip_commission_percentage / 100
```
Stored as an entry in the `driver_earnings` table (created in Phase 10 — see §5.3). Deduct from this any traffic_fines flagged `deducted_from_driver`. Net payable each pay period.

### 9.3 Document Expiry Alert Logic

Daily scheduled job at 06:00 Cairo time:
```
For each active car_document:
  days_until = expiry_date - today
  If days_until in [60, 30, 7, 1, 0]:
    Dispatch DocumentExpiringSoon notification (WhatsApp + email) to fleet_manager + branch_manager
  If days_until < 0 and not yet flagged expired:
    Mark car status = out_of_service, dispatch DocumentExpired alert
Repeat for driver_documents → notify driver + branch_manager
```

### 9.4 Multi-Language URLs

```
GET /ar/admin/trips
GET /en/admin/trips
GET /ar/portal/dashboard
GET /en/portal/dashboard
```
Default locale `ar`. Switcher in header updates session locale + redirects.

### 9.5 Sub-Rental Cost on Trip P&L

When a trip uses a `sub_rented` car:
```
trip_direct_cost = (sub_rental_contract.daily_cost * trip_duration_days)
                 + driver_commission
                 + sum(trip_expenses)
                 + allocated fuel cost
trip_margin = trip.total_amount - trip_direct_cost
```
Show this on the trip detail page for fleet_manager+ roles.

### 9.6 Bilingual PDF Templates

Use DomPDF with the Cairo or Amiri font (download to `storage/fonts/`). Templates in `resources/views/pdfs/`. Two versions per template: `invoice-ar.blade.php`, `invoice-en.blade.php`. Selected based on customer's `preferred_language`. **Static copy inside templates (labels, headers) resolved through the same `__()` helper backed by the `translations` table — so owner can edit PDF wording without redeploy.**

### 9.7 Translation Loader & Editing Workflow *(added 2026-05-20)*

- Runtime: `spatie/laravel-translation-loader` registered with priority over the file loader. `__()` and `trans()` query the `translations` DB table first; on miss, fall back to `lang/{locale}/*.php`.
- Authoring: shipped strings live in `lang/ar/*.php` + `lang/en/*.php` (committed to git, the safety net). On first boot a one-time seeder imports them into `translations` rows with `is_system=true`.
- Editing: any user with the `manage_translations` permission can edit `text_ar` / `text_en` in the Filament admin. Edits are audit-logged. System rows show a "reset to default" action that reloads from the file fallback.
- Performance: translation queries cached in the request lifecycle; cache busts on write via observer. No N+1 risk for typical page loads.

---

## 10. What NOT to Do

- **Do NOT use PostgreSQL features** (exclusion constraints, ranges, JSONB ops). MySQL/MariaDB only.
- **Do NOT assume Redis is available**. Database driver for cache/queue/sessions. Redis can be added later via config.
- **Do NOT use long-running queue workers**. Cron-based only.
- **Do NOT hardcode language, currency, timezone, or VAT rate**. Config.
- **Do NOT commit `.env`, `vendor/`, `node_modules/`, `public/build/`, `storage/app/public/*`**.
- **Do NOT create tables not in the ERD without asking**. If a new table is genuinely needed, document the decision in `docs/DECISIONS.md` and ask.
- **Do NOT use raw SQL where Eloquent works**. Exception: the booking-overlap triggers in Phase 5.
- **Do NOT write English-only labels**. Use `__('trips.create')` from day one — value resolves through DB translations.
- **Do NOT bypass the DB translation loader**. No direct `lang/*/file.php` lookups. The file layer is fallback only.
- **Do NOT skip the audit log** on `invoices`, `credit_notes`, `payments`, `rate_cards`, `customers.is_blacklisted`, `trips.cancellation_reason`, `translations.text_ar/text_en`.
- **Do NOT allow hard delete** on `payments`, `invoices`, `credit_notes`, `trip_inspections`. Status flags only (`cancelled`, `void`).
- **Do NOT use Filament's `softDeletes` default toggle** on financial models — confused with audit needs.
- **Do NOT build the driver native mobile app in v1**. Mobile-friendly web at `/driver` covers it.
- **Do NOT integrate with Egyptian e-invoice portal in v1**. Stub the hooks, real integration is a separate project.
- **Do NOT introduce new packages** without justifying in `docs/DECISIONS.md`.
- **Do NOT say "SamirGroup" anywhere user-visible**. The system is **Adly Group Agency**; SamirGroup references only survive as historical notes (e.g. §4's mention of the prior `t.samirgroup.net` deployment).

---

## 11. Starting Instructions for Each Claude Code Session

1. Read this entire file first.
2. Read `docs/DECISIONS.md` for any updates since last session.
3. Check current git status — confirm which phase is in progress.
4. State which phase you'll work on this session and the specific deliverable.
5. Work in small commits (one logical change per commit) on a feature branch named `phase-NN-{slug}`.
6. After each commit, run: `vendor/bin/pint && vendor/bin/pest --parallel`.
7. At the end of the session, push the branch and summarize what's done + what's next.

For the very first session: start at **Phase 0**.

---

## 12. Owner Decisions Log (DECISIONS.md template)

Maintain in `docs/DECISIONS.md`:

```
## YYYY-MM-DD — Decision title
**Context**: why this came up
**Decision**: what was chosen
**Alternatives considered**: what else was on the table
**Consequences**: what this means going forward
```

Every non-trivial architectural choice gets one. Examples already populated by Phase 0:
- 001 Laravel 12 over Laravel 11 (owner directive at project kickoff)
- 002 Filament v5 over Filament v3 (owner asked for v4; v4.0.0 had security advisory with no patched 4.x; composer picked v5.6 — the current supported line)
- 003 DB-backed editable translations over file-only (owner directive; `translations` table + `spatie/laravel-translation-loader`)

Future examples to expect:
- Choice of trigger vs application-only locking for booking overlap (Phase 5)
- Choice of DomPDF vs alternatives for Arabic (Phase 4)
- How sub-rented car costs allocate to trip margin (Phase 8)
- Workflow for refund processing (Phase 8)
- Whether to expose REST API for future mobile app (Phase 10 or later)

---

**End of CLAUDE.md.** This document is the contract. When in doubt, ask the owner rather than guessing.
