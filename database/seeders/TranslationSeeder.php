<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

/**
 * Seeds bilingual UI strings used to verify the DB loader.
 *
 * Phase 2+ resources add their own translation rows through their seeders
 * with `is_system => true`. Owner-authored strings are added through admin UI
 * with `is_system => false`.
 */
class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // App + common
            ['group' => 'app', 'key' => 'name', 'text_ar' => 'مجموعة عدلي', 'text_en' => 'Adly Group Agency'],
            ['group' => 'common', 'key' => 'yes', 'text_ar' => 'نعم', 'text_en' => 'Yes'],
            ['group' => 'common', 'key' => 'no', 'text_ar' => 'لا', 'text_en' => 'No'],
            ['group' => 'common', 'key' => 'active', 'text_ar' => 'نشط', 'text_en' => 'Active'],
            ['group' => 'common', 'key' => 'inactive', 'text_ar' => 'غير نشط', 'text_en' => 'Inactive'],
            ['group' => 'common', 'key' => 'created_at', 'text_ar' => 'تاريخ الإنشاء', 'text_en' => 'Created at'],
            ['group' => 'common', 'key' => 'updated_at', 'text_ar' => 'تاريخ التحديث', 'text_en' => 'Updated at'],

            // Navigation
            ['group' => 'navigation', 'key' => 'branches', 'text_ar' => 'الفروع', 'text_en' => 'Branches'],
            ['group' => 'navigation', 'key' => 'translations', 'text_ar' => 'الترجمات', 'text_en' => 'Translations'],
            ['group' => 'navigation', 'key' => 'users', 'text_ar' => 'المستخدمون', 'text_en' => 'Users'],
            ['group' => 'navigation', 'key' => 'settings', 'text_ar' => 'الإعدادات', 'text_en' => 'Settings'],
            ['group' => 'navigation', 'key' => 'car_categories', 'text_ar' => 'فئات السيارات', 'text_en' => 'Car Categories'],
            ['group' => 'navigation', 'key' => 'partner_agencies', 'text_ar' => 'الوكالات الشريكة', 'text_en' => 'Partner Agencies'],
            ['group' => 'navigation', 'key' => 'garages', 'text_ar' => 'الورش', 'text_en' => 'Garages'],
            ['group' => 'navigation', 'key' => 'corporate_accounts', 'text_ar' => 'الحسابات المؤسسية', 'text_en' => 'Corporate Accounts'],
            ['group' => 'navigation', 'key' => 'customers', 'text_ar' => 'العملاء', 'text_en' => 'Customers'],
            ['group' => 'navigation', 'key' => 'leads', 'text_ar' => 'الفرص', 'text_en' => 'Leads'],
            ['group' => 'navigation', 'key' => 'drivers', 'text_ar' => 'السائقون', 'text_en' => 'Drivers'],
            ['group' => 'navigation', 'key' => 'fleet', 'text_ar' => 'الأسطول', 'text_en' => 'Fleet'],
            ['group' => 'navigation', 'key' => 'crm', 'text_ar' => 'العلاقات', 'text_en' => 'CRM'],

            // CarCategoryClass
            ['group' => 'enums', 'key' => 'car_category_class.economy', 'text_ar' => 'اقتصادية', 'text_en' => 'Economy'],
            ['group' => 'enums', 'key' => 'car_category_class.midsize', 'text_ar' => 'متوسطة', 'text_en' => 'Midsize'],
            ['group' => 'enums', 'key' => 'car_category_class.suv', 'text_ar' => 'دفع رباعي', 'text_en' => 'SUV'],
            ['group' => 'enums', 'key' => 'car_category_class.luxury', 'text_ar' => 'فاخرة', 'text_en' => 'Luxury'],
            ['group' => 'enums', 'key' => 'car_category_class.van', 'text_ar' => 'فان', 'text_en' => 'Van'],
            ['group' => 'enums', 'key' => 'car_category_class.minibus', 'text_ar' => 'ميني باص', 'text_en' => 'Minibus'],

            // EmploymentType
            ['group' => 'enums', 'key' => 'employment_type.salaried', 'text_ar' => 'بمرتب', 'text_en' => 'Salaried'],
            ['group' => 'enums', 'key' => 'employment_type.freelance', 'text_ar' => 'حر', 'text_en' => 'Freelance'],
            ['group' => 'enums', 'key' => 'employment_type.on_demand', 'text_ar' => 'عند الطلب', 'text_en' => 'On demand'],

            // DriverStatus
            ['group' => 'enums', 'key' => 'driver_status.active', 'text_ar' => 'نشط', 'text_en' => 'Active'],
            ['group' => 'enums', 'key' => 'driver_status.on_leave', 'text_ar' => 'في إجازة', 'text_en' => 'On leave'],
            ['group' => 'enums', 'key' => 'driver_status.suspended', 'text_ar' => 'موقوف', 'text_en' => 'Suspended'],
            ['group' => 'enums', 'key' => 'driver_status.terminated', 'text_ar' => 'منتهي الخدمة', 'text_en' => 'Terminated'],

            // DriverDocumentType
            ['group' => 'enums', 'key' => 'driver_document_type.driving_license', 'text_ar' => 'رخصة قيادة', 'text_en' => 'Driving license'],
            ['group' => 'enums', 'key' => 'driver_document_type.national_id', 'text_ar' => 'بطاقة قومية', 'text_en' => 'National ID'],
            ['group' => 'enums', 'key' => 'driver_document_type.criminal_record', 'text_ar' => 'فيش جنائي', 'text_en' => 'Criminal record'],
            ['group' => 'enums', 'key' => 'driver_document_type.medical_certificate', 'text_ar' => 'شهادة صحية', 'text_en' => 'Medical certificate'],
            ['group' => 'enums', 'key' => 'driver_document_type.professional_license', 'text_ar' => 'رخصة مهنية', 'text_en' => 'Professional license'],

            // CustomerType
            ['group' => 'enums', 'key' => 'customer_type.individual', 'text_ar' => 'فرد', 'text_en' => 'Individual'],
            ['group' => 'enums', 'key' => 'customer_type.corporate_contact', 'text_ar' => 'جهة اتصال مؤسسية', 'text_en' => 'Corporate contact'],
            ['group' => 'enums', 'key' => 'customer_type.vip', 'text_ar' => 'كبار العملاء', 'text_en' => 'VIP'],

            // CommunicationChannel
            ['group' => 'enums', 'key' => 'communication_channel.whatsapp', 'text_ar' => 'واتساب', 'text_en' => 'WhatsApp'],
            ['group' => 'enums', 'key' => 'communication_channel.email', 'text_ar' => 'بريد إلكتروني', 'text_en' => 'Email'],
            ['group' => 'enums', 'key' => 'communication_channel.phone', 'text_ar' => 'هاتف', 'text_en' => 'Phone'],
            ['group' => 'enums', 'key' => 'communication_channel.in_person', 'text_ar' => 'شخصياً', 'text_en' => 'In person'],
            ['group' => 'enums', 'key' => 'communication_channel.sms', 'text_ar' => 'رسالة نصية', 'text_en' => 'SMS'],

            // CommunicationDirection
            ['group' => 'enums', 'key' => 'communication_direction.inbound', 'text_ar' => 'وارد', 'text_en' => 'Inbound'],
            ['group' => 'enums', 'key' => 'communication_direction.outbound', 'text_ar' => 'صادر', 'text_en' => 'Outbound'],

            // LeadSource
            ['group' => 'enums', 'key' => 'lead_source.whatsapp', 'text_ar' => 'واتساب', 'text_en' => 'WhatsApp'],
            ['group' => 'enums', 'key' => 'lead_source.website', 'text_ar' => 'الموقع', 'text_en' => 'Website'],
            ['group' => 'enums', 'key' => 'lead_source.referral', 'text_ar' => 'إحالة', 'text_en' => 'Referral'],
            ['group' => 'enums', 'key' => 'lead_source.walk_in', 'text_ar' => 'حضور شخصي', 'text_en' => 'Walk-in'],
            ['group' => 'enums', 'key' => 'lead_source.phone', 'text_ar' => 'هاتف', 'text_en' => 'Phone'],
            ['group' => 'enums', 'key' => 'lead_source.corporate', 'text_ar' => 'مؤسسي', 'text_en' => 'Corporate'],

            // LeadStatus
            ['group' => 'enums', 'key' => 'lead_status.new', 'text_ar' => 'جديد', 'text_en' => 'New'],
            ['group' => 'enums', 'key' => 'lead_status.contacted', 'text_ar' => 'تم التواصل', 'text_en' => 'Contacted'],
            ['group' => 'enums', 'key' => 'lead_status.quoted', 'text_ar' => 'تم عرض السعر', 'text_en' => 'Quoted'],
            ['group' => 'enums', 'key' => 'lead_status.won', 'text_ar' => 'مكسوب', 'text_en' => 'Won'],
            ['group' => 'enums', 'key' => 'lead_status.lost', 'text_ar' => 'مفقود', 'text_en' => 'Lost'],

            // Phase 3 navigation
            ['group' => 'navigation', 'key' => 'cars', 'text_ar' => 'السيارات', 'text_en' => 'Cars'],
            ['group' => 'navigation', 'key' => 'sub_rental_contracts', 'text_ar' => 'عقود الإيجار الفرعي', 'text_en' => 'Sub-rental Contracts'],

            // CarTransmission
            ['group' => 'enums', 'key' => 'car_transmission.manual', 'text_ar' => 'يدوي', 'text_en' => 'Manual'],
            ['group' => 'enums', 'key' => 'car_transmission.auto', 'text_ar' => 'أوتوماتيك', 'text_en' => 'Automatic'],

            // CarFuelType
            ['group' => 'enums', 'key' => 'car_fuel_type.petrol', 'text_ar' => 'بنزين', 'text_en' => 'Petrol'],
            ['group' => 'enums', 'key' => 'car_fuel_type.diesel', 'text_ar' => 'ديزل', 'text_en' => 'Diesel'],
            ['group' => 'enums', 'key' => 'car_fuel_type.hybrid', 'text_ar' => 'هجين', 'text_en' => 'Hybrid'],
            ['group' => 'enums', 'key' => 'car_fuel_type.electric', 'text_ar' => 'كهرباء', 'text_en' => 'Electric'],

            // CarOwnershipType
            ['group' => 'enums', 'key' => 'car_ownership_type.owned', 'text_ar' => 'مملوكة', 'text_en' => 'Owned'],
            ['group' => 'enums', 'key' => 'car_ownership_type.sub_rented', 'text_ar' => 'مؤجرة من الباطن', 'text_en' => 'Sub-rented'],
            ['group' => 'enums', 'key' => 'car_ownership_type.replacement', 'text_ar' => 'بديلة', 'text_en' => 'Replacement'],

            // CarStatus
            ['group' => 'enums', 'key' => 'car_status.available', 'text_ar' => 'متاحة', 'text_en' => 'Available'],
            ['group' => 'enums', 'key' => 'car_status.on_trip', 'text_ar' => 'في رحلة', 'text_en' => 'On trip'],
            ['group' => 'enums', 'key' => 'car_status.in_maintenance', 'text_ar' => 'في الصيانة', 'text_en' => 'In maintenance'],
            ['group' => 'enums', 'key' => 'car_status.at_partner', 'text_ar' => 'لدى الشريك', 'text_en' => 'At partner'],
            ['group' => 'enums', 'key' => 'car_status.out_of_service', 'text_ar' => 'خارج الخدمة', 'text_en' => 'Out of service'],

            // CarDocumentType
            ['group' => 'enums', 'key' => 'car_document_type.registration_license', 'text_ar' => 'رخصة تسيير', 'text_en' => 'Registration license'],
            ['group' => 'enums', 'key' => 'car_document_type.compulsory_insurance', 'text_ar' => 'تأمين إجباري', 'text_en' => 'Compulsory insurance'],
            ['group' => 'enums', 'key' => 'car_document_type.comprehensive_insurance', 'text_ar' => 'تأمين شامل', 'text_en' => 'Comprehensive insurance'],
            ['group' => 'enums', 'key' => 'car_document_type.technical_inspection', 'text_ar' => 'فحص فني', 'text_en' => 'Technical inspection'],
            ['group' => 'enums', 'key' => 'car_document_type.inspection_sticker', 'text_ar' => 'ملصق الفحص', 'text_en' => 'Inspection sticker'],

            // SubRentalContractStatus
            ['group' => 'enums', 'key' => 'sub_rental_contract_status.active', 'text_ar' => 'نشط', 'text_en' => 'Active'],
            ['group' => 'enums', 'key' => 'sub_rental_contract_status.expired', 'text_ar' => 'منتهي', 'text_en' => 'Expired'],
            ['group' => 'enums', 'key' => 'sub_rental_contract_status.cancelled', 'text_ar' => 'ملغي', 'text_en' => 'Cancelled'],

            // Cars tabs
            ['group' => 'cars', 'key' => 'tabs.basic_info', 'text_ar' => 'البيانات الأساسية', 'text_en' => 'Basic info'],
            ['group' => 'cars', 'key' => 'tabs.damage_map', 'text_ar' => 'خريطة الأضرار', 'text_en' => 'Damage map'],
            ['group' => 'cars', 'key' => 'tabs.damage_map_placeholder', 'text_ar' => 'سيتم تعبئة هذه الخريطة من فحوصات الرحلات في المرحلة 5.', 'text_en' => 'This map will be populated from trip inspections in Phase 5.'],
            ['group' => 'cars', 'key' => 'tabs.service_history', 'text_ar' => 'سجل الصيانة', 'text_en' => 'Service history'],
            ['group' => 'cars', 'key' => 'tabs.service_history_placeholder', 'text_ar' => 'سيتم تعبئة هذا السجل من أوامر الصيانة في المرحلة 6.', 'text_en' => 'This log will be populated from maintenance orders in Phase 6.'],

            // ExpiringDocumentsWidget
            ['group' => 'widgets', 'key' => 'expiring_documents.expired', 'text_ar' => 'وثائق منتهية', 'text_en' => 'Expired documents'],
            ['group' => 'widgets', 'key' => 'expiring_documents.expired_description', 'text_ar' => 'تحتاج إجراءً فورياً', 'text_en' => 'Needs immediate action'],
            ['group' => 'widgets', 'key' => 'expiring_documents.within_30_days', 'text_ar' => 'تنتهي خلال 30 يوماً', 'text_en' => 'Expiring in 30 days'],
            ['group' => 'widgets', 'key' => 'expiring_documents.within_30_description', 'text_ar' => 'جدولة التجديد قريباً', 'text_en' => 'Schedule renewal soon'],
            ['group' => 'widgets', 'key' => 'expiring_documents.within_60_days', 'text_ar' => 'تنتهي خلال 60 يوماً', 'text_en' => 'Expiring in 60 days'],
            ['group' => 'widgets', 'key' => 'expiring_documents.within_60_description', 'text_ar' => 'للمراقبة', 'text_en' => 'For monitoring'],

            // Branches field labels
            ['group' => 'branches', 'key' => 'singular', 'text_ar' => 'فرع', 'text_en' => 'Branch'],
            ['group' => 'branches', 'key' => 'code', 'text_ar' => 'الكود', 'text_en' => 'Code'],
            ['group' => 'branches', 'key' => 'name', 'text_ar' => 'الاسم', 'text_en' => 'Name'],
            ['group' => 'branches', 'key' => 'name_ar', 'text_ar' => 'الاسم بالعربية', 'text_en' => 'Name (Arabic)'],
            ['group' => 'branches', 'key' => 'city', 'text_ar' => 'المدينة', 'text_en' => 'City'],
            ['group' => 'branches', 'key' => 'address', 'text_ar' => 'العنوان', 'text_en' => 'Address'],
            ['group' => 'branches', 'key' => 'phone', 'text_ar' => 'الهاتف', 'text_en' => 'Phone'],
            ['group' => 'branches', 'key' => 'manager', 'text_ar' => 'المدير', 'text_en' => 'Manager'],

            // Users field labels
            ['group' => 'users', 'key' => 'singular', 'text_ar' => 'مستخدم', 'text_en' => 'User'],
            ['group' => 'users', 'key' => 'full_name', 'text_ar' => 'الاسم الكامل', 'text_en' => 'Full name'],
            ['group' => 'users', 'key' => 'full_name_ar', 'text_ar' => 'الاسم بالعربية', 'text_en' => 'Full name (Arabic)'],
            ['group' => 'users', 'key' => 'email', 'text_ar' => 'البريد الإلكتروني', 'text_en' => 'Email'],
            ['group' => 'users', 'key' => 'phone', 'text_ar' => 'الهاتف', 'text_en' => 'Phone'],
            ['group' => 'users', 'key' => 'password', 'text_ar' => 'كلمة المرور', 'text_en' => 'Password'],
            ['group' => 'users', 'key' => 'branch', 'text_ar' => 'الفرع', 'text_en' => 'Branch'],
            ['group' => 'users', 'key' => 'branch_help', 'text_ar' => 'اتركه فارغاً للمستخدمين العامين (مثل المسؤول)', 'text_en' => 'Leave empty for global users (e.g. super admin)'],
            ['group' => 'users', 'key' => 'preferred_locale', 'text_ar' => 'اللغة المفضلة', 'text_en' => 'Preferred language'],
            ['group' => 'users', 'key' => 'is_active', 'text_ar' => 'نشط', 'text_en' => 'Active'],
            ['group' => 'users', 'key' => 'roles', 'text_ar' => 'الأدوار', 'text_en' => 'Roles'],

            // Translations field labels
            ['group' => 'translations', 'key' => 'singular', 'text_ar' => 'ترجمة', 'text_en' => 'Translation'],
            ['group' => 'translations', 'key' => 'group', 'text_ar' => 'المجموعة', 'text_en' => 'Group'],
            ['group' => 'translations', 'key' => 'key', 'text_ar' => 'المفتاح', 'text_en' => 'Key'],
            ['group' => 'translations', 'key' => 'text_ar', 'text_ar' => 'النص بالعربية', 'text_en' => 'Arabic text'],
            ['group' => 'translations', 'key' => 'text_en', 'text_ar' => 'النص بالإنجليزية', 'text_en' => 'English text'],
            ['group' => 'translations', 'key' => 'is_system', 'text_ar' => 'نظامي', 'text_en' => 'System'],

            // CarCategories field labels
            ['group' => 'car_categories', 'key' => 'name', 'text_ar' => 'الاسم', 'text_en' => 'Name'],
            ['group' => 'car_categories', 'key' => 'name_ar', 'text_ar' => 'الاسم بالعربية', 'text_en' => 'Name (Arabic)'],
            ['group' => 'car_categories', 'key' => 'class_code', 'text_ar' => 'الفئة', 'text_en' => 'Class'],
            ['group' => 'car_categories', 'key' => 'default_seats', 'text_ar' => 'عدد المقاعد', 'text_en' => 'Seats'],
            ['group' => 'car_categories', 'key' => 'sort_order', 'text_ar' => 'الترتيب', 'text_en' => 'Order'],

            // PartnerAgencies field labels
            ['group' => 'partner_agencies', 'key' => 'name', 'text_ar' => 'اسم الوكالة', 'text_en' => 'Agency name'],
            ['group' => 'partner_agencies', 'key' => 'name_ar', 'text_ar' => 'الاسم بالعربية', 'text_en' => 'Name (Arabic)'],
            ['group' => 'partner_agencies', 'key' => 'contact_person', 'text_ar' => 'الشخص المسؤول', 'text_en' => 'Contact person'],
            ['group' => 'partner_agencies', 'key' => 'phone', 'text_ar' => 'الهاتف', 'text_en' => 'Phone'],
            ['group' => 'partner_agencies', 'key' => 'email', 'text_ar' => 'البريد الإلكتروني', 'text_en' => 'Email'],
            ['group' => 'partner_agencies', 'key' => 'tax_id', 'text_ar' => 'الرقم الضريبي', 'text_en' => 'Tax ID'],
            ['group' => 'partner_agencies', 'key' => 'address', 'text_ar' => 'العنوان', 'text_en' => 'Address'],
            ['group' => 'partner_agencies', 'key' => 'credit_limit', 'text_ar' => 'حد الائتمان', 'text_en' => 'Credit limit'],
            ['group' => 'partner_agencies', 'key' => 'payment_terms_days', 'text_ar' => 'أيام السداد', 'text_en' => 'Payment terms (days)'],
            ['group' => 'partner_agencies', 'key' => 'is_active', 'text_ar' => 'نشطة', 'text_en' => 'Active'],

            // Garages field labels
            ['group' => 'garages', 'key' => 'name', 'text_ar' => 'اسم الورشة', 'text_en' => 'Garage name'],
            ['group' => 'garages', 'key' => 'phone', 'text_ar' => 'الهاتف', 'text_en' => 'Phone'],
            ['group' => 'garages', 'key' => 'address', 'text_ar' => 'العنوان', 'text_en' => 'Address'],
            ['group' => 'garages', 'key' => 'is_internal', 'text_ar' => 'داخلية', 'text_en' => 'Internal'],
            ['group' => 'garages', 'key' => 'specialties', 'text_ar' => 'التخصصات', 'text_en' => 'Specialties'],
            ['group' => 'garages', 'key' => 'specialties_placeholder', 'text_ar' => 'مثال: زيوت، فرامل، محرك', 'text_en' => 'e.g. oil, brakes, engine'],
            ['group' => 'garages', 'key' => 'is_active', 'text_ar' => 'نشطة', 'text_en' => 'Active'],

            // CorporateAccounts field labels
            ['group' => 'corporate_accounts', 'key' => 'company_name', 'text_ar' => 'اسم الشركة', 'text_en' => 'Company name'],
            ['group' => 'corporate_accounts', 'key' => 'company_name_ar', 'text_ar' => 'الاسم بالعربية', 'text_en' => 'Company name (Arabic)'],
            ['group' => 'corporate_accounts', 'key' => 'tax_id', 'text_ar' => 'الرقم الضريبي', 'text_en' => 'Tax ID'],
            ['group' => 'corporate_accounts', 'key' => 'commercial_register', 'text_ar' => 'السجل التجاري', 'text_en' => 'Commercial register'],
            ['group' => 'corporate_accounts', 'key' => 'industry', 'text_ar' => 'النشاط', 'text_en' => 'Industry'],
            ['group' => 'corporate_accounts', 'key' => 'address', 'text_ar' => 'العنوان', 'text_en' => 'Address'],
            ['group' => 'corporate_accounts', 'key' => 'billing_email', 'text_ar' => 'بريد الفواتير', 'text_en' => 'Billing email'],
            ['group' => 'corporate_accounts', 'key' => 'billing_phone', 'text_ar' => 'هاتف الفواتير', 'text_en' => 'Billing phone'],
            ['group' => 'corporate_accounts', 'key' => 'credit_limit', 'text_ar' => 'حد الائتمان', 'text_en' => 'Credit limit'],
            ['group' => 'corporate_accounts', 'key' => 'payment_terms_days', 'text_ar' => 'أيام السداد', 'text_en' => 'Payment terms (days)'],
            ['group' => 'corporate_accounts', 'key' => 'discount_percentage', 'text_ar' => 'نسبة الخصم', 'text_en' => 'Discount %'],
            ['group' => 'corporate_accounts', 'key' => 'is_active', 'text_ar' => 'نشط', 'text_en' => 'Active'],
            ['group' => 'corporate_accounts', 'key' => 'notes', 'text_ar' => 'ملاحظات', 'text_en' => 'Notes'],

            // Customers field labels
            ['group' => 'customers', 'key' => 'singular', 'text_ar' => 'عميل', 'text_en' => 'Customer'],
            ['group' => 'customers', 'key' => 'corporate_account', 'text_ar' => 'الحساب المؤسسي', 'text_en' => 'Corporate account'],
            ['group' => 'customers', 'key' => 'type', 'text_ar' => 'النوع', 'text_en' => 'Type'],
            ['group' => 'customers', 'key' => 'full_name', 'text_ar' => 'الاسم الكامل', 'text_en' => 'Full name'],
            ['group' => 'customers', 'key' => 'full_name_ar', 'text_ar' => 'الاسم بالعربية', 'text_en' => 'Full name (Arabic)'],
            ['group' => 'customers', 'key' => 'phone', 'text_ar' => 'الهاتف', 'text_en' => 'Phone'],
            ['group' => 'customers', 'key' => 'whatsapp_phone', 'text_ar' => 'رقم الواتساب', 'text_en' => 'WhatsApp phone'],
            ['group' => 'customers', 'key' => 'email', 'text_ar' => 'البريد الإلكتروني', 'text_en' => 'Email'],
            ['group' => 'customers', 'key' => 'national_id', 'text_ar' => 'الرقم القومي', 'text_en' => 'National ID'],
            ['group' => 'customers', 'key' => 'address', 'text_ar' => 'العنوان', 'text_en' => 'Address'],
            ['group' => 'customers', 'key' => 'preferred_language', 'text_ar' => 'اللغة المفضلة', 'text_en' => 'Preferred language'],
            ['group' => 'customers', 'key' => 'loyalty_points', 'text_ar' => 'نقاط الولاء', 'text_en' => 'Loyalty points'],
            ['group' => 'customers', 'key' => 'is_blacklisted', 'text_ar' => 'محظور', 'text_en' => 'Blacklisted'],
            ['group' => 'customers', 'key' => 'blacklist_reason', 'text_ar' => 'سبب الحظر', 'text_en' => 'Blacklist reason'],
            ['group' => 'customers', 'key' => 'notes', 'text_ar' => 'ملاحظات', 'text_en' => 'Notes'],

            // CustomerCommunications field labels
            ['group' => 'customer_communications', 'key' => 'channel', 'text_ar' => 'القناة', 'text_en' => 'Channel'],
            ['group' => 'customer_communications', 'key' => 'direction', 'text_ar' => 'الاتجاه', 'text_en' => 'Direction'],
            ['group' => 'customer_communications', 'key' => 'subject', 'text_ar' => 'الموضوع', 'text_en' => 'Subject'],
            ['group' => 'customer_communications', 'key' => 'body', 'text_ar' => 'المحتوى', 'text_en' => 'Body'],
            ['group' => 'customer_communications', 'key' => 'sent_at', 'text_ar' => 'تاريخ الإرسال', 'text_en' => 'Sent at'],
            ['group' => 'customer_communications', 'key' => 'logged_by', 'text_ar' => 'سُجّل بواسطة', 'text_en' => 'Logged by'],

            // Leads field labels
            ['group' => 'leads', 'key' => 'customer', 'text_ar' => 'العميل', 'text_en' => 'Customer'],
            ['group' => 'leads', 'key' => 'assigned_user', 'text_ar' => 'المسؤول', 'text_en' => 'Assigned to'],
            ['group' => 'leads', 'key' => 'source', 'text_ar' => 'المصدر', 'text_en' => 'Source'],
            ['group' => 'leads', 'key' => 'status', 'text_ar' => 'الحالة', 'text_en' => 'Status'],
            ['group' => 'leads', 'key' => 'requirements', 'text_ar' => 'المتطلبات', 'text_en' => 'Requirements'],
            ['group' => 'leads', 'key' => 'estimated_value', 'text_ar' => 'القيمة المتوقعة', 'text_en' => 'Estimated value'],
            ['group' => 'leads', 'key' => 'lost_reason', 'text_ar' => 'سبب الخسارة', 'text_en' => 'Lost reason'],
            ['group' => 'leads', 'key' => 'due_at', 'text_ar' => 'تاريخ الاستحقاق', 'text_en' => 'Due at'],
            ['group' => 'leads', 'key' => 'closed_at', 'text_ar' => 'تاريخ الإغلاق', 'text_en' => 'Closed at'],

            // Drivers field labels
            ['group' => 'drivers', 'key' => 'branch', 'text_ar' => 'الفرع', 'text_en' => 'Branch'],
            ['group' => 'drivers', 'key' => 'national_id', 'text_ar' => 'الرقم القومي', 'text_en' => 'National ID'],
            ['group' => 'drivers', 'key' => 'full_name', 'text_ar' => 'الاسم الكامل', 'text_en' => 'Full name'],
            ['group' => 'drivers', 'key' => 'full_name_ar', 'text_ar' => 'الاسم بالعربية', 'text_en' => 'Full name (Arabic)'],
            ['group' => 'drivers', 'key' => 'phone', 'text_ar' => 'الهاتف', 'text_en' => 'Phone'],
            ['group' => 'drivers', 'key' => 'whatsapp_phone', 'text_ar' => 'رقم الواتساب', 'text_en' => 'WhatsApp phone'],
            ['group' => 'drivers', 'key' => 'address', 'text_ar' => 'العنوان', 'text_en' => 'Address'],
            ['group' => 'drivers', 'key' => 'date_of_birth', 'text_ar' => 'تاريخ الميلاد', 'text_en' => 'Date of birth'],
            ['group' => 'drivers', 'key' => 'hire_date', 'text_ar' => 'تاريخ التعيين', 'text_en' => 'Hire date'],
            ['group' => 'drivers', 'key' => 'employment_type', 'text_ar' => 'نوع التوظيف', 'text_en' => 'Employment type'],
            ['group' => 'drivers', 'key' => 'base_salary', 'text_ar' => 'الراتب الأساسي', 'text_en' => 'Base salary'],
            ['group' => 'drivers', 'key' => 'trip_commission_percentage', 'text_ar' => 'نسبة العمولة', 'text_en' => 'Trip commission %'],
            ['group' => 'drivers', 'key' => 'status', 'text_ar' => 'الحالة', 'text_en' => 'Status'],
            ['group' => 'drivers', 'key' => 'rating', 'text_ar' => 'التقييم', 'text_en' => 'Rating'],
            ['group' => 'drivers', 'key' => 'notes', 'text_ar' => 'ملاحظات', 'text_en' => 'Notes'],

            // DriverDocuments field labels
            ['group' => 'driver_documents', 'key' => 'doc_type', 'text_ar' => 'نوع الوثيقة', 'text_en' => 'Document type'],
            ['group' => 'driver_documents', 'key' => 'document_number', 'text_ar' => 'رقم الوثيقة', 'text_en' => 'Document number'],
            ['group' => 'driver_documents', 'key' => 'issue_date', 'text_ar' => 'تاريخ الإصدار', 'text_en' => 'Issue date'],
            ['group' => 'driver_documents', 'key' => 'expiry_date', 'text_ar' => 'تاريخ الانتهاء', 'text_en' => 'Expiry date'],
            ['group' => 'driver_documents', 'key' => 'issuer', 'text_ar' => 'الجهة المُصدرة', 'text_en' => 'Issuer'],
            ['group' => 'driver_documents', 'key' => 'file', 'text_ar' => 'الملف', 'text_en' => 'File'],
            ['group' => 'driver_documents', 'key' => 'is_active', 'text_ar' => 'نشطة', 'text_en' => 'Active'],

            // Cars field labels
            ['group' => 'cars', 'key' => 'branch', 'text_ar' => 'الفرع', 'text_en' => 'Branch'],
            ['group' => 'cars', 'key' => 'category', 'text_ar' => 'الفئة', 'text_en' => 'Category'],
            ['group' => 'cars', 'key' => 'plate', 'text_ar' => 'رقم اللوحة', 'text_en' => 'Plate'],
            ['group' => 'cars', 'key' => 'vin', 'text_ar' => 'رقم الهيكل', 'text_en' => 'VIN'],
            ['group' => 'cars', 'key' => 'make', 'text_ar' => 'الماركة', 'text_en' => 'Make'],
            ['group' => 'cars', 'key' => 'model', 'text_ar' => 'الموديل', 'text_en' => 'Model'],
            ['group' => 'cars', 'key' => 'year', 'text_ar' => 'سنة الصنع', 'text_en' => 'Year'],
            ['group' => 'cars', 'key' => 'color', 'text_ar' => 'اللون', 'text_en' => 'Color'],
            ['group' => 'cars', 'key' => 'transmission', 'text_ar' => 'ناقل الحركة', 'text_en' => 'Transmission'],
            ['group' => 'cars', 'key' => 'fuel_type', 'text_ar' => 'نوع الوقود', 'text_en' => 'Fuel type'],
            ['group' => 'cars', 'key' => 'seats', 'text_ar' => 'عدد المقاعد', 'text_en' => 'Seats'],
            ['group' => 'cars', 'key' => 'ownership_type', 'text_ar' => 'نوع الملكية', 'text_en' => 'Ownership'],
            ['group' => 'cars', 'key' => 'status', 'text_ar' => 'الحالة', 'text_en' => 'Status'],
            ['group' => 'cars', 'key' => 'current_odometer', 'text_ar' => 'العداد الحالي', 'text_en' => 'Current odometer'],
            ['group' => 'cars', 'key' => 'acquisition_date', 'text_ar' => 'تاريخ الاقتناء', 'text_en' => 'Acquisition date'],
            ['group' => 'cars', 'key' => 'acquisition_cost', 'text_ar' => 'تكلفة الاقتناء', 'text_en' => 'Acquisition cost'],
            ['group' => 'cars', 'key' => 'notes', 'text_ar' => 'ملاحظات', 'text_en' => 'Notes'],

            // CarDocuments field labels
            ['group' => 'car_documents', 'key' => 'doc_type', 'text_ar' => 'نوع الوثيقة', 'text_en' => 'Document type'],
            ['group' => 'car_documents', 'key' => 'document_number', 'text_ar' => 'رقم الوثيقة', 'text_en' => 'Document number'],
            ['group' => 'car_documents', 'key' => 'issue_date', 'text_ar' => 'تاريخ الإصدار', 'text_en' => 'Issue date'],
            ['group' => 'car_documents', 'key' => 'expiry_date', 'text_ar' => 'تاريخ الانتهاء', 'text_en' => 'Expiry date'],
            ['group' => 'car_documents', 'key' => 'issuer', 'text_ar' => 'الجهة المُصدرة', 'text_en' => 'Issuer'],
            ['group' => 'car_documents', 'key' => 'cost', 'text_ar' => 'التكلفة', 'text_en' => 'Cost'],
            ['group' => 'car_documents', 'key' => 'file', 'text_ar' => 'الملف', 'text_en' => 'File'],
            ['group' => 'car_documents', 'key' => 'is_active', 'text_ar' => 'نشطة', 'text_en' => 'Active'],
            ['group' => 'car_documents', 'key' => 'is_active_help', 'text_ar' => 'حفظ هذه نشطة سيُعطّل الوثيقة السابقة من نفس النوع.', 'text_en' => 'Saving this active will deactivate the previous doc of the same type.'],

            // SubRentalContracts field labels
            ['group' => 'sub_rental_contracts', 'key' => 'partner_agency', 'text_ar' => 'الوكالة الشريكة', 'text_en' => 'Partner agency'],
            ['group' => 'sub_rental_contracts', 'key' => 'car', 'text_ar' => 'السيارة', 'text_en' => 'Car'],
            ['group' => 'sub_rental_contracts', 'key' => 'start_date', 'text_ar' => 'تاريخ البداية', 'text_en' => 'Start date'],
            ['group' => 'sub_rental_contracts', 'key' => 'end_date', 'text_ar' => 'تاريخ النهاية', 'text_en' => 'End date'],
            ['group' => 'sub_rental_contracts', 'key' => 'daily_cost', 'text_ar' => 'التكلفة اليومية', 'text_en' => 'Daily cost'],
            ['group' => 'sub_rental_contracts', 'key' => 'included_km_per_day', 'text_ar' => 'الكيلومترات المشمولة يومياً', 'text_en' => 'Included km/day'],
            ['group' => 'sub_rental_contracts', 'key' => 'extra_km_cost', 'text_ar' => 'تكلفة الكيلومتر الإضافي', 'text_en' => 'Extra km cost'],
            ['group' => 'sub_rental_contracts', 'key' => 'terms', 'text_ar' => 'الشروط', 'text_en' => 'Terms'],
            ['group' => 'sub_rental_contracts', 'key' => 'status', 'text_ar' => 'الحالة', 'text_en' => 'Status'],
            ['group' => 'sub_rental_contracts', 'key' => 'contract_file', 'text_ar' => 'ملف العقد', 'text_en' => 'Contract file'],
        ];

        foreach ($rows as $row) {
            Translation::query()->updateOrCreate(
                ['group' => $row['group'], 'key' => $row['key']],
                array_merge($row, ['is_system' => true])
            );
        }
    }
}
