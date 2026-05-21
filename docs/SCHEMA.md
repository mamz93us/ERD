# Database Schema — Adly Group Agency CRMS

> Authoritative copy of CLAUDE.md §5, kept in sync. UUID primary keys throughout. All money is `decimal(15,2)`. All FKs default `ON DELETE RESTRICT` unless noted.
>
> When changing the schema: update CLAUDE.md §5 AND this file in the same commit, add a DECISIONS.md entry if the change is non-trivial.

---

## 5.1 Organization

**branches**: `id (uuid pk)`, `code (unique, e.g. ABH/CAI)`, `name`, `name_ar`, `city`, `address`, `phone`, `manager_user_id (fk users)`, timestamps, soft deletes.

**roles** (from spatie/laravel-permission, standard schema).

**users**: `id (uuid pk)`, `branch_id (fk)`, `email (unique)`, `password`, `full_name`, `full_name_ar`, `phone`, `is_active`, `preferred_locale (ar|en)`, timestamps, soft deletes.

**translations** *(added 2026-05-20)*: `id (uuid pk)`, `group`, `key`, `text_ar (text)`, `text_en (text)`, `is_system (boolean)`, `updated_by_user_id (fk users nullable)`, timestamps. **Unique** on `(group, key)`. **Index** on `key`. Loaded via `spatie/laravel-translation-loader`; `lang/ar/`+`lang/en/` are fallback only. Audit-logged.

## 5.2 Fleet

**car_categories**: `id (uuid pk)`, `name`, `name_ar`, `class_code (economy|midsize|suv|luxury|van|minibus)`, `default_seats`, `sort_order`.

**partner_agencies**: `id (uuid pk)`, `name`, `name_ar`, `contact_person`, `phone`, `email`, `tax_id`, `address`, `credit_limit`, `payment_terms_days`, `is_active`, timestamps, soft deletes.

**cars**: `id (uuid pk)`, `branch_id (fk)`, `category_id (fk car_categories)`, `plate (unique)`, `vin (unique nullable)`, `make`, `model`, `year`, `color`, `transmission (manual|auto)`, `fuel_type (petrol|diesel|hybrid|electric)`, `seats`, `ownership_type (owned|sub_rented|replacement)`, `status (available|on_trip|in_maintenance|at_partner|out_of_service)`, `current_odometer`, `acquisition_date`, `acquisition_cost (nullable)`, `notes`, timestamps, soft deletes. **Index** on `(status, branch_id)` and `(ownership_type)`.

**sub_rental_contracts**: `id (uuid pk)`, `partner_agency_id (fk)`, `car_id (fk cars)`, `start_date`, `end_date`, `daily_cost`, `included_km_per_day (nullable)`, `extra_km_cost (nullable)`, `terms (text)`, `status (active|expired|cancelled)`, `contract_file_path`, timestamps. **Constraint**: when `cars.ownership_type = 'sub_rented'`, must have one active contract.

**car_documents**: `id (uuid pk)`, `car_id (fk)`, `doc_type (registration_license|compulsory_insurance|comprehensive_insurance|technical_inspection|inspection_sticker)`, `document_number`, `issue_date`, `expiry_date`, `issuer`, `cost (nullable)`, `file_path`, `is_active (boolean)`, timestamps. Old docs kept with `is_active=false`. Unique on `(car_id, doc_type, is_active=true)` enforced in observer.

## 5.3 Drivers

**drivers**: `id (uuid pk)`, `branch_id (fk)`, `national_id (unique)`, `full_name`, `full_name_ar`, `phone`, `whatsapp_phone`, `address`, `date_of_birth`, `hire_date`, `employment_type (salaried|freelance|on_demand)`, `base_salary`, `trip_commission_percentage`, `status (active|on_leave|suspended|terminated)`, `rating (decimal 3,2)`, `notes`, timestamps, soft deletes.

**driver_documents**: `id (uuid pk)`, `driver_id (fk)`, `doc_type (driving_license|national_id|criminal_record|medical_certificate|professional_license)`, `document_number`, `issue_date`, `expiry_date`, `issuer`, `file_path`, `is_active`, timestamps.

**driver_earnings** *(Phase 10)*: `id (uuid pk)`, `driver_id (fk)`, `trip_id (fk)`, `gross_commission`, `deductions (json — fines, advances)`, `net_payable`, `pay_period_start`, `pay_period_end`, `paid_at (nullable)`, `payment_reference (nullable)`, timestamps.

## 5.4 Customers & CRM

**corporate_accounts**: `id (uuid pk)`, `company_name`, `company_name_ar`, `tax_id`, `commercial_register`, `industry`, `address`, `billing_email`, `billing_phone`, `credit_limit`, `payment_terms_days`, `discount_percentage`, `is_active`, `notes`, timestamps, soft deletes.

**customers**: `id (uuid pk)`, `corporate_account_id (fk nullable)`, `type (individual|corporate_contact|vip)`, `full_name`, `full_name_ar`, `phone`, `whatsapp_phone`, `email`, `national_id (nullable)`, `address`, `preferred_language (ar|en)`, `loyalty_points (default 0)`, `is_blacklisted (default false)`, `blacklist_reason (nullable)`, `notes`, timestamps, soft deletes.

**customer_communications**: `id (uuid pk)`, `customer_id (fk)`, `user_id (fk nullable, who logged it)`, `channel (whatsapp|email|phone|in_person|sms)`, `direction (inbound|outbound)`, `subject (nullable)`, `body (text)`, `attachments (json nullable)`, `external_message_id (nullable)`, `sent_at`, timestamps.

**leads**: `id (uuid pk)`, `customer_id (fk nullable)`, `assigned_user_id (fk users)`, `source (whatsapp|website|referral|walk_in|phone|corporate)`, `status (new|contacted|quoted|won|lost)`, `requirements (text)`, `estimated_value`, `lost_reason (nullable)`, `due_at (nullable)`, `closed_at (nullable)`, timestamps.

## 5.5 Pricing & Operations

**rate_cards**: `id (uuid pk)`, `category_id (fk car_categories)`, `corporate_account_id (fk nullable, null = default)`, `name`, `hourly_rate`, `daily_rate`, `weekly_rate`, `monthly_rate`, `included_km_per_day`, `extra_km_rate`, `extra_hour_rate`, `driver_daily_allowance`, `cross_city_surcharge`, `effective_from`, `effective_to (nullable)`, `is_active`, timestamps. **Index** on `(category_id, corporate_account_id, is_active)`.

**quotations**: `id (uuid pk)`, `quotation_number (unique, Q-2026-0001)`, `customer_id (fk)`, `corporate_account_id (fk nullable)`, `created_by_user_id (fk users)`, `pickup_at`, `dropoff_at`, `pickup_location`, `dropoff_location`, `estimated_distance_km`, `category_id (fk car_categories)`, `rate_card_id (fk)`, `subtotal`, `vat_amount`, `total_amount`, `valid_until`, `status (draft|sent|accepted|rejected|expired)`, `notes`, `terms_and_conditions (text)`, timestamps, soft deletes.

**trips**: `id (uuid pk)`, `trip_number (unique, T-2026-0001)`, `branch_id (fk)`, `customer_id (fk)`, `corporate_account_id (fk nullable)`, `car_id (fk)`, `driver_id (fk, NOT NULL — chauffeur model)`, `quotation_id (fk nullable)`, `rate_card_id (fk)`, `scheduled_start`, `scheduled_end`, `actual_start (nullable)`, `actual_end (nullable)`, `pickup_location`, `dropoff_location`, `start_odometer (nullable)`, `end_odometer (nullable)`, `status (draft|confirmed|assigned|en_route|in_progress|completed|invoiced|closed|cancelled|no_show)`, `cancellation_reason (nullable)`, `subtotal`, `vat_amount`, `total_amount`, `notes`, timestamps, soft deletes. **Indexes**: `(car_id, scheduled_start, scheduled_end)`, `(driver_id, scheduled_start, scheduled_end)`, `(status)`, `(customer_id)`.

**trip_inspections**: `id (uuid pk)`, `trip_id (fk)`, `stage (pickup|return)`, `inspector_user_id (fk users)`, `odometer`, `fuel_level (empty|quarter|half|three_quarter|full)`, `damage_marks (json)`, `accessories_checklist (json)`, `customer_signature_path (nullable)`, `driver_signature_path`, `photos (json)`, `notes`, `performed_at`, timestamps.

**trip_expenses**: `id (uuid pk)`, `trip_id (fk)`, `type (fuel|toll|parking|food|accommodation|misc)`, `amount`, `receipt_path (nullable)`, `reimbursed (boolean)`, `notes`, `incurred_at`, timestamps.

**trip_damage_reports**: `id (uuid pk)`, `trip_id (fk)`, `description`, `damage_area (json)`, `photos (json)`, `repair_cost_estimate`, `actual_repair_cost (nullable)`, `charged_to_customer (boolean)`, `customer_charge_amount (nullable)`, `status (reported|approved|repaired|disputed)`, timestamps.

## 5.6 Maintenance

**garages**: `id (uuid pk)`, `name`, `phone`, `address`, `is_internal (boolean)`, `specialties (json nullable)`, `is_active`, timestamps.

**maintenance_schedules**: `id (uuid pk)`, `car_id (fk)`, `service_type (oil_change|tire_rotation|brake_inspection|major_service|ac_service|battery_check|other)`, `interval_km`, `interval_days`, `last_done_km (nullable)`, `last_done_date (nullable)`, `next_due_km`, `next_due_date`, `is_active`, timestamps.

**maintenance_orders**: `id (uuid pk)`, `order_number (unique, M-2026-0001)`, `car_id (fk)`, `garage_id (fk)`, `order_type (preventive|corrective|accident_repair)`, `description`, `scheduled_start`, `scheduled_end`, `actual_start (nullable)`, `actual_end (nullable)`, `odometer_at_service`, `subtotal`, `vat_amount`, `total_cost`, `status (scheduled|in_service|completed|cancelled)`, `invoice_file_path (nullable)`, `notes`, timestamps, soft deletes. **Index** on `(car_id, scheduled_start, scheduled_end)`.

**maintenance_items**: `id (uuid pk)`, `maintenance_order_id (fk)`, `item_type (part|labor|consumable)`, `description`, `quantity`, `unit_cost`, `total_cost`, timestamps.

## 5.7 Compliance

**traffic_fines**: `id (uuid pk)`, `car_id (fk)`, `driver_id (fk nullable)`, `trip_id (fk nullable — auto-attributed)`, `violation_number`, `violation_date`, `violation_type`, `location`, `amount`, `payment_status (unpaid|paid|disputed|waived)`, `paid_date (nullable)`, `paid_amount (nullable)`, `deducted_from_driver (boolean)`, `notes`, `attachment_path`, timestamps.

**insurance_claims**: `id (uuid pk)`, `car_id (fk)`, `trip_id (fk nullable)`, `claim_number`, `incident_date`, `incident_location`, `description (text)`, `police_report_number (nullable)`, `claim_amount`, `payout_amount (nullable)`, `status (reported|submitted|under_review|approved|rejected|paid)`, `documents (json)`, `notes`, timestamps.

## 5.8 Finance

**invoices**: `id (uuid pk)`, `invoice_number (unique, INV-2026-0001)`, `customer_id (fk)`, `corporate_account_id (fk nullable)`, `trip_id (fk nullable)`, `issue_date`, `due_date`, `subtotal`, `vat_amount`, `discount_amount`, `total`, `paid_amount (default 0)`, `balance_due`, `currency (default EGP)`, `status (draft|sent|partially_paid|paid|overdue|cancelled)`, `notes`, `terms (text)`, `pdf_path`, `e_invoice_reference (nullable)`, timestamps, soft deletes. **Index** on `(customer_id, status)`, `(due_date, status)`.

**invoice_lines**: `id (uuid pk)`, `invoice_id (fk)`, `description`, `quantity`, `unit_price`, `discount_amount`, `vat_rate`, `vat_amount`, `line_total`, `trip_id (fk nullable)`, `sort_order`, timestamps.

**credit_notes**: `id (uuid pk)`, `note_number (unique, CN-2026-0001)`, `invoice_id (fk)`, `created_by_user_id (fk)`, `approved_by_user_id (fk nullable)`, `issue_date`, `reason (cancellation|service_complaint|goodwill|deposit_return|billing_error|other)`, `reason_details`, `amount`, `status (draft|pending_approval|approved|applied|rejected)`, `approved_at (nullable)`, `applied_at (nullable)`, `pdf_path`, `e_invoice_reference (nullable)`, timestamps, soft deletes.

**payments**: `id (uuid pk)`, `payment_number (unique, P-2026-0001)`, `customer_id (fk)`, `corporate_account_id (fk nullable)`, `method (cash|bank_transfer|visa|mastercard|fawry|instapay|cheque)`, `amount`, `payment_date`, `reference_number (nullable)`, `received_by_user_id (fk)`, `branch_id (fk)`, `notes`, `is_reconciled (boolean default false)`, timestamps, soft deletes.

**payment_allocations**: `id (uuid pk)`, `payment_id (fk)`, `invoice_id (fk)`, `allocated_amount`, `allocated_at`, timestamps. Many-to-many bridge.

**vendor_bills**: `id (uuid pk)`, `bill_number`, `vendor_type (partner_agency|garage|fuel|insurance|other)`, `partner_agency_id (fk nullable)`, `garage_id (fk nullable)`, `bill_date`, `due_date`, `subtotal`, `vat_amount`, `total`, `paid_amount`, `balance_due`, `status (draft|received|partially_paid|paid|disputed)`, `related_car_id (fk nullable)`, `related_sub_rental_contract_id (fk nullable)`, `description`, `attachment_path`, timestamps, soft deletes.

**expenses**: `id (uuid pk)`, `branch_id (fk)`, `car_id (fk nullable)`, `driver_id (fk nullable)`, `category (fuel|maintenance|salaries|insurance|fines|office|utilities|marketing|depreciation|other)`, `amount`, `expense_date`, `paid_by (cash|bank|petty_cash)`, `paid_by_user_id (fk)`, `description`, `attachment_path`, timestamps, soft deletes.

## 5.9 System

**audit_logs** (managed by owen-it/laravel-auditing — standard schema).

**notifications** (Laravel's standard notifications table).

**jobs**, **failed_jobs** (Laravel queue standard).

---

## Booking-overlap triggers (Phase 5)

Four triggers on `trips` (BEFORE INSERT / BEFORE UPDATE, for car_id and driver_id). See CLAUDE.md §6 Phase 5 for the trigger body. Required for defense-in-depth alongside `BookingAvailabilityService` + `SELECT ... FOR UPDATE`.

## Audit logging required on

`invoices`, `credit_notes`, `payments`, `rate_cards`, `customers.is_blacklisted`, `trips.cancellation_reason`, `translations.text_ar`/`text_en`.
