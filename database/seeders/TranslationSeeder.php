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

            // Phase 4 navigation
            ['group' => 'navigation', 'key' => 'pricing', 'text_ar' => 'التسعير', 'text_en' => 'Pricing'],
            ['group' => 'navigation', 'key' => 'rate_cards', 'text_ar' => 'بطاقات الأسعار', 'text_en' => 'Rate Cards'],
            ['group' => 'navigation', 'key' => 'quotations', 'text_ar' => 'عروض الأسعار', 'text_en' => 'Quotations'],

            // QuotationStatus
            ['group' => 'enums', 'key' => 'quotation_status.draft', 'text_ar' => 'مسودة', 'text_en' => 'Draft'],
            ['group' => 'enums', 'key' => 'quotation_status.sent', 'text_ar' => 'مُرسل', 'text_en' => 'Sent'],
            ['group' => 'enums', 'key' => 'quotation_status.accepted', 'text_ar' => 'مقبول', 'text_en' => 'Accepted'],
            ['group' => 'enums', 'key' => 'quotation_status.rejected', 'text_ar' => 'مرفوض', 'text_en' => 'Rejected'],
            ['group' => 'enums', 'key' => 'quotation_status.expired', 'text_ar' => 'منتهي', 'text_en' => 'Expired'],

            // RateCards field labels
            ['group' => 'rate_cards', 'key' => 'category', 'text_ar' => 'الفئة', 'text_en' => 'Category'],
            ['group' => 'rate_cards', 'key' => 'corporate_account', 'text_ar' => 'الحساب المؤسسي', 'text_en' => 'Corporate account'],
            ['group' => 'rate_cards', 'key' => 'corporate_account_help', 'text_ar' => 'اتركه فارغاً لتعريف بطاقة الأسعار الافتراضية لهذه الفئة.', 'text_en' => 'Leave empty to define the default rate card for this category.'],
            ['group' => 'rate_cards', 'key' => 'default', 'text_ar' => 'افتراضي', 'text_en' => 'Default'],
            ['group' => 'rate_cards', 'key' => 'name', 'text_ar' => 'الاسم', 'text_en' => 'Name'],
            ['group' => 'rate_cards', 'key' => 'hourly_rate', 'text_ar' => 'سعر الساعة', 'text_en' => 'Hourly rate'],
            ['group' => 'rate_cards', 'key' => 'daily_rate', 'text_ar' => 'سعر اليوم', 'text_en' => 'Daily rate'],
            ['group' => 'rate_cards', 'key' => 'weekly_rate', 'text_ar' => 'سعر الأسبوع', 'text_en' => 'Weekly rate'],
            ['group' => 'rate_cards', 'key' => 'monthly_rate', 'text_ar' => 'سعر الشهر', 'text_en' => 'Monthly rate'],
            ['group' => 'rate_cards', 'key' => 'included_km_per_day', 'text_ar' => 'الكيلومترات المشمولة يومياً', 'text_en' => 'Included km/day'],
            ['group' => 'rate_cards', 'key' => 'extra_km_rate', 'text_ar' => 'سعر الكيلومتر الإضافي', 'text_en' => 'Extra km rate'],
            ['group' => 'rate_cards', 'key' => 'extra_hour_rate', 'text_ar' => 'سعر الساعة الإضافية', 'text_en' => 'Extra hour rate'],
            ['group' => 'rate_cards', 'key' => 'driver_daily_allowance', 'text_ar' => 'بدل السائق اليومي', 'text_en' => 'Driver daily allowance'],
            ['group' => 'rate_cards', 'key' => 'cross_city_surcharge', 'text_ar' => 'رسوم التنقل بين المدن', 'text_en' => 'Cross-city surcharge'],
            ['group' => 'rate_cards', 'key' => 'effective_from', 'text_ar' => 'ساري من', 'text_en' => 'Effective from'],
            ['group' => 'rate_cards', 'key' => 'effective_to', 'text_ar' => 'ساري حتى', 'text_en' => 'Effective to'],
            ['group' => 'rate_cards', 'key' => 'is_active', 'text_ar' => 'نشطة', 'text_en' => 'Active'],

            // Quotations field labels
            ['group' => 'quotations', 'key' => 'quotation_number', 'text_ar' => 'رقم العرض', 'text_en' => 'Quotation #'],
            ['group' => 'quotations', 'key' => 'customer', 'text_ar' => 'العميل', 'text_en' => 'Customer'],
            ['group' => 'quotations', 'key' => 'corporate_account', 'text_ar' => 'الحساب المؤسسي', 'text_en' => 'Corporate account'],
            ['group' => 'quotations', 'key' => 'category', 'text_ar' => 'فئة السيارة', 'text_en' => 'Car category'],
            ['group' => 'quotations', 'key' => 'pickup_at', 'text_ar' => 'تاريخ الاستلام', 'text_en' => 'Pickup at'],
            ['group' => 'quotations', 'key' => 'dropoff_at', 'text_ar' => 'تاريخ التسليم', 'text_en' => 'Drop-off at'],
            ['group' => 'quotations', 'key' => 'pickup_location', 'text_ar' => 'مكان الاستلام', 'text_en' => 'Pickup location'],
            ['group' => 'quotations', 'key' => 'dropoff_location', 'text_ar' => 'مكان التسليم', 'text_en' => 'Drop-off location'],
            ['group' => 'quotations', 'key' => 'estimated_distance_km', 'text_ar' => 'المسافة التقديرية', 'text_en' => 'Estimated distance'],
            ['group' => 'quotations', 'key' => 'rate_card', 'text_ar' => 'بطاقة الأسعار', 'text_en' => 'Rate card'],
            ['group' => 'quotations', 'key' => 'rate_card_help', 'text_ar' => 'اتركه فارغاً لاختيار البطاقة المناسبة تلقائياً بناءً على الفئة والحساب المؤسسي.', 'text_en' => 'Leave blank to auto-pick based on category and corporate account.'],
            ['group' => 'quotations', 'key' => 'subtotal', 'text_ar' => 'المجموع الفرعي', 'text_en' => 'Subtotal'],
            ['group' => 'quotations', 'key' => 'vat_amount', 'text_ar' => 'ضريبة القيمة المضافة', 'text_en' => 'VAT'],
            ['group' => 'quotations', 'key' => 'total_amount', 'text_ar' => 'الإجمالي', 'text_en' => 'Total'],
            ['group' => 'quotations', 'key' => 'valid_until', 'text_ar' => 'صالح حتى', 'text_en' => 'Valid until'],
            ['group' => 'quotations', 'key' => 'status', 'text_ar' => 'الحالة', 'text_en' => 'Status'],
            ['group' => 'quotations', 'key' => 'notes', 'text_ar' => 'ملاحظات', 'text_en' => 'Notes'],
            ['group' => 'quotations', 'key' => 'terms_and_conditions', 'text_ar' => 'الشروط والأحكام', 'text_en' => 'Terms and conditions'],

            // Quotations form tabs + actions
            ['group' => 'quotations', 'key' => 'tabs.customer_trip', 'text_ar' => 'العميل والرحلة', 'text_en' => 'Customer & Trip'],
            ['group' => 'quotations', 'key' => 'tabs.pricing', 'text_ar' => 'التسعير', 'text_en' => 'Pricing'],
            ['group' => 'quotations', 'key' => 'tabs.notes', 'text_ar' => 'الملاحظات والشروط', 'text_en' => 'Notes & Terms'],
            ['group' => 'quotations', 'key' => 'pricing_help', 'text_ar' => 'يتم احتساب السعر تلقائياً عند الحفظ من خلال PricingService بناءً على الفئة وفترة الإيجار والمسافة.', 'text_en' => 'Pricing is auto-computed on save by PricingService from category, duration, and distance.'],
            ['group' => 'quotations', 'key' => 'download_pdf', 'text_ar' => 'تحميل PDF', 'text_en' => 'Download PDF'],
            ['group' => 'quotations', 'key' => 'send', 'text_ar' => 'إرسال للعميل', 'text_en' => 'Send to customer'],
            ['group' => 'quotations', 'key' => 'send_stub_notice', 'text_ar' => 'في المرحلة 4 يتم تغيير الحالة إلى مُرسل وعرض رابط الواتساب. سيتم ربط الواتساب والبريد بالكامل في المرحلة 9.', 'text_en' => 'For Phase 4 this marks the quote as sent and shows the WhatsApp deep-link. Phase 9 wires real WhatsApp + email delivery.'],
            ['group' => 'quotations', 'key' => 'send_stub_title', 'text_ar' => 'تم وضع علامة "مُرسل"', 'text_en' => 'Marked as sent'],
            ['group' => 'quotations', 'key' => 'send_no_channel', 'text_ar' => 'لا توجد قناة اتصال متاحة للعميل.', 'text_en' => 'No contact channel available for this customer.'],

            // Phase 5 navigation
            ['group' => 'navigation', 'key' => 'operations', 'text_ar' => 'العمليات', 'text_en' => 'Operations'],
            ['group' => 'navigation', 'key' => 'trips', 'text_ar' => 'الرحلات', 'text_en' => 'Trips'],

            // TripStatus
            ['group' => 'enums', 'key' => 'trip_status.draft', 'text_ar' => 'مسودة', 'text_en' => 'Draft'],
            ['group' => 'enums', 'key' => 'trip_status.confirmed', 'text_ar' => 'مؤكدة', 'text_en' => 'Confirmed'],
            ['group' => 'enums', 'key' => 'trip_status.assigned', 'text_ar' => 'مُسندة', 'text_en' => 'Assigned'],
            ['group' => 'enums', 'key' => 'trip_status.en_route', 'text_ar' => 'في الطريق', 'text_en' => 'En route'],
            ['group' => 'enums', 'key' => 'trip_status.in_progress', 'text_ar' => 'جارية', 'text_en' => 'In progress'],
            ['group' => 'enums', 'key' => 'trip_status.completed', 'text_ar' => 'مكتملة', 'text_en' => 'Completed'],
            ['group' => 'enums', 'key' => 'trip_status.invoiced', 'text_ar' => 'صدرت فاتورتها', 'text_en' => 'Invoiced'],
            ['group' => 'enums', 'key' => 'trip_status.closed', 'text_ar' => 'مُغلقة', 'text_en' => 'Closed'],
            ['group' => 'enums', 'key' => 'trip_status.cancelled', 'text_ar' => 'ملغاة', 'text_en' => 'Cancelled'],
            ['group' => 'enums', 'key' => 'trip_status.no_show', 'text_ar' => 'لم يحضر', 'text_en' => 'No show'],

            // TripExpenseType
            ['group' => 'enums', 'key' => 'trip_expense_type.fuel', 'text_ar' => 'وقود', 'text_en' => 'Fuel'],
            ['group' => 'enums', 'key' => 'trip_expense_type.toll', 'text_ar' => 'رسوم طريق', 'text_en' => 'Toll'],
            ['group' => 'enums', 'key' => 'trip_expense_type.parking', 'text_ar' => 'موقف', 'text_en' => 'Parking'],
            ['group' => 'enums', 'key' => 'trip_expense_type.food', 'text_ar' => 'طعام', 'text_en' => 'Food'],
            ['group' => 'enums', 'key' => 'trip_expense_type.accommodation', 'text_ar' => 'إقامة', 'text_en' => 'Accommodation'],
            ['group' => 'enums', 'key' => 'trip_expense_type.misc', 'text_ar' => 'متفرقة', 'text_en' => 'Misc'],

            // TripInspectionStage
            ['group' => 'enums', 'key' => 'trip_inspection_stage.pickup', 'text_ar' => 'استلام', 'text_en' => 'Pickup'],
            ['group' => 'enums', 'key' => 'trip_inspection_stage.return', 'text_ar' => 'إعادة', 'text_en' => 'Return'],

            // FuelLevel
            ['group' => 'enums', 'key' => 'fuel_level.empty', 'text_ar' => 'فارغ', 'text_en' => 'Empty'],
            ['group' => 'enums', 'key' => 'fuel_level.quarter', 'text_ar' => 'ربع', 'text_en' => 'Quarter'],
            ['group' => 'enums', 'key' => 'fuel_level.half', 'text_ar' => 'نصف', 'text_en' => 'Half'],
            ['group' => 'enums', 'key' => 'fuel_level.three_quarter', 'text_ar' => 'ثلاثة أرباع', 'text_en' => 'Three quarter'],
            ['group' => 'enums', 'key' => 'fuel_level.full', 'text_ar' => 'كامل', 'text_en' => 'Full'],

            // TripDamageReportStatus
            ['group' => 'enums', 'key' => 'trip_damage_report_status.reported', 'text_ar' => 'مُبلغ', 'text_en' => 'Reported'],
            ['group' => 'enums', 'key' => 'trip_damage_report_status.approved', 'text_ar' => 'مُعتمد', 'text_en' => 'Approved'],
            ['group' => 'enums', 'key' => 'trip_damage_report_status.repaired', 'text_ar' => 'مُصلح', 'text_en' => 'Repaired'],
            ['group' => 'enums', 'key' => 'trip_damage_report_status.disputed', 'text_ar' => 'مُتنازع عليه', 'text_en' => 'Disputed'],

            // DamageSeverity
            ['group' => 'enums', 'key' => 'damage_severity.scratch', 'text_ar' => 'خدش', 'text_en' => 'Scratch'],
            ['group' => 'enums', 'key' => 'damage_severity.dent', 'text_ar' => 'انبعاج', 'text_en' => 'Dent'],
            ['group' => 'enums', 'key' => 'damage_severity.crack', 'text_ar' => 'شرخ', 'text_en' => 'Crack'],
            ['group' => 'enums', 'key' => 'damage_severity.missing', 'text_ar' => 'مفقود', 'text_en' => 'Missing'],
            ['group' => 'enums', 'key' => 'damage_severity.other', 'text_ar' => 'أخرى', 'text_en' => 'Other'],

            // DamageSide
            ['group' => 'enums', 'key' => 'damage_side.front_left', 'text_ar' => 'أمامي يسار', 'text_en' => 'Front left'],
            ['group' => 'enums', 'key' => 'damage_side.front_right', 'text_ar' => 'أمامي يمين', 'text_en' => 'Front right'],
            ['group' => 'enums', 'key' => 'damage_side.rear_left', 'text_ar' => 'خلفي يسار', 'text_en' => 'Rear left'],
            ['group' => 'enums', 'key' => 'damage_side.rear_right', 'text_ar' => 'خلفي يمين', 'text_en' => 'Rear right'],
            ['group' => 'enums', 'key' => 'damage_side.top', 'text_ar' => 'سقف', 'text_en' => 'Top'],
            ['group' => 'enums', 'key' => 'damage_side.bottom', 'text_ar' => 'أسفل', 'text_en' => 'Bottom'],

            // Trips field labels
            ['group' => 'trips', 'key' => 'trip_number', 'text_ar' => 'رقم الرحلة', 'text_en' => 'Trip #'],
            ['group' => 'trips', 'key' => 'branch', 'text_ar' => 'الفرع', 'text_en' => 'Branch'],
            ['group' => 'trips', 'key' => 'customer', 'text_ar' => 'العميل', 'text_en' => 'Customer'],
            ['group' => 'trips', 'key' => 'corporate_account', 'text_ar' => 'الحساب المؤسسي', 'text_en' => 'Corporate account'],
            ['group' => 'trips', 'key' => 'car', 'text_ar' => 'السيارة', 'text_en' => 'Car'],
            ['group' => 'trips', 'key' => 'driver', 'text_ar' => 'السائق', 'text_en' => 'Driver'],
            ['group' => 'trips', 'key' => 'driver_help', 'text_ar' => 'النموذج بسائق دائماً (chauffeur-driven). كل رحلة لها سائق.', 'text_en' => 'Chauffeur model — every trip has a driver.'],
            ['group' => 'trips', 'key' => 'rate_card', 'text_ar' => 'بطاقة الأسعار', 'text_en' => 'Rate card'],
            ['group' => 'trips', 'key' => 'scheduled_start', 'text_ar' => 'وقت البداية المجدول', 'text_en' => 'Scheduled start'],
            ['group' => 'trips', 'key' => 'scheduled_end', 'text_ar' => 'وقت النهاية المجدول', 'text_en' => 'Scheduled end'],
            ['group' => 'trips', 'key' => 'pickup_location', 'text_ar' => 'مكان الاستلام', 'text_en' => 'Pickup location'],
            ['group' => 'trips', 'key' => 'dropoff_location', 'text_ar' => 'مكان التسليم', 'text_en' => 'Drop-off location'],
            ['group' => 'trips', 'key' => 'status', 'text_ar' => 'الحالة', 'text_en' => 'Status'],
            ['group' => 'trips', 'key' => 'status_help', 'text_ar' => 'استخدم الإجراءات في الأعلى لتغيير الحالة وفق قواعد الانتقال.', 'text_en' => 'Use header actions to transition status according to the lifecycle rules.'],
            ['group' => 'trips', 'key' => 'cancellation_reason', 'text_ar' => 'سبب الإلغاء', 'text_en' => 'Cancellation reason'],
            ['group' => 'trips', 'key' => 'subtotal', 'text_ar' => 'المجموع الفرعي', 'text_en' => 'Subtotal'],
            ['group' => 'trips', 'key' => 'vat_amount', 'text_ar' => 'ضريبة القيمة المضافة', 'text_en' => 'VAT'],
            ['group' => 'trips', 'key' => 'total_amount', 'text_ar' => 'الإجمالي', 'text_en' => 'Total'],
            ['group' => 'trips', 'key' => 'notes', 'text_ar' => 'ملاحظات', 'text_en' => 'Notes'],

            // Trips tabs + actions + booking-check messages
            ['group' => 'trips', 'key' => 'tabs.booking', 'text_ar' => 'الحجز', 'text_en' => 'Booking'],
            ['group' => 'trips', 'key' => 'tabs.pricing', 'text_ar' => 'التسعير', 'text_en' => 'Pricing'],
            ['group' => 'trips', 'key' => 'tabs.notes', 'text_ar' => 'الملاحظات', 'text_en' => 'Notes'],
            ['group' => 'trips', 'key' => 'move_to', 'text_ar' => 'انتقال إلى :status', 'text_en' => 'Move to :status'],
            ['group' => 'trips', 'key' => 'status_changed', 'text_ar' => 'تم تغيير الحالة', 'text_en' => 'Status changed'],
            ['group' => 'trips', 'key' => 'status_change_failed', 'text_ar' => 'فشل تغيير الحالة', 'text_en' => 'Status change failed'],
            ['group' => 'trips', 'key' => 'booking_warning', 'text_ar' => 'تحذير الحجز', 'text_en' => 'Booking warning'],
            ['group' => 'trips', 'key' => 'booking_blocked', 'text_ar' => 'تعذّر حفظ الحجز بسبب التعارضات التالية', 'text_en' => 'Booking blocked due to the following conflicts'],

            // TripInspections field labels
            ['group' => 'trip_inspections', 'key' => 'stage', 'text_ar' => 'المرحلة', 'text_en' => 'Stage'],
            ['group' => 'trip_inspections', 'key' => 'odometer', 'text_ar' => 'العداد', 'text_en' => 'Odometer'],
            ['group' => 'trip_inspections', 'key' => 'fuel_level', 'text_ar' => 'مستوى الوقود', 'text_en' => 'Fuel level'],
            ['group' => 'trip_inspections', 'key' => 'performed_at', 'text_ar' => 'تاريخ الفحص', 'text_en' => 'Performed at'],
            ['group' => 'trip_inspections', 'key' => 'inspector', 'text_ar' => 'المُفتش', 'text_en' => 'Inspector'],
            ['group' => 'trip_inspections', 'key' => 'accessories_checklist', 'text_ar' => 'قائمة الإكسسوارات', 'text_en' => 'Accessories checklist'],
            ['group' => 'trip_inspections', 'key' => 'accessory', 'text_ar' => 'الإكسسوار', 'text_en' => 'Accessory'],
            ['group' => 'trip_inspections', 'key' => 'present', 'text_ar' => 'موجود', 'text_en' => 'Present'],
            ['group' => 'trip_inspections', 'key' => 'driver_signature', 'text_ar' => 'توقيع السائق', 'text_en' => 'Driver signature'],
            ['group' => 'trip_inspections', 'key' => 'customer_signature', 'text_ar' => 'توقيع العميل', 'text_en' => 'Customer signature'],
            ['group' => 'trip_inspections', 'key' => 'photos', 'text_ar' => 'الصور', 'text_en' => 'Photos'],
            ['group' => 'trip_inspections', 'key' => 'notes', 'text_ar' => 'ملاحظات', 'text_en' => 'Notes'],

            // TripExpenses field labels
            ['group' => 'trip_expenses', 'key' => 'type', 'text_ar' => 'النوع', 'text_en' => 'Type'],
            ['group' => 'trip_expenses', 'key' => 'amount', 'text_ar' => 'المبلغ', 'text_en' => 'Amount'],
            ['group' => 'trip_expenses', 'key' => 'incurred_at', 'text_ar' => 'تاريخ الصرف', 'text_en' => 'Incurred at'],
            ['group' => 'trip_expenses', 'key' => 'receipt', 'text_ar' => 'الإيصال', 'text_en' => 'Receipt'],
            ['group' => 'trip_expenses', 'key' => 'reimbursed', 'text_ar' => 'تم تعويضه', 'text_en' => 'Reimbursed'],
            ['group' => 'trip_expenses', 'key' => 'notes', 'text_ar' => 'ملاحظات', 'text_en' => 'Notes'],

            // TripDamageReports field labels
            ['group' => 'trip_damage_reports', 'key' => 'description', 'text_ar' => 'الوصف', 'text_en' => 'Description'],
            ['group' => 'trip_damage_reports', 'key' => 'photos', 'text_ar' => 'الصور', 'text_en' => 'Photos'],
            ['group' => 'trip_damage_reports', 'key' => 'repair_cost_estimate', 'text_ar' => 'تكلفة الإصلاح المتوقعة', 'text_en' => 'Repair cost estimate'],
            ['group' => 'trip_damage_reports', 'key' => 'actual_repair_cost', 'text_ar' => 'تكلفة الإصلاح الفعلية', 'text_en' => 'Actual repair cost'],
            ['group' => 'trip_damage_reports', 'key' => 'charged_to_customer', 'text_ar' => 'يُحمَّل على العميل', 'text_en' => 'Charged to customer'],
            ['group' => 'trip_damage_reports', 'key' => 'customer_charge_amount', 'text_ar' => 'مبلغ تحميل العميل', 'text_en' => 'Customer charge amount'],
            ['group' => 'trip_damage_reports', 'key' => 'status', 'text_ar' => 'الحالة', 'text_en' => 'Status'],

            // Quotation + Trip governorate-related help text
            ['group' => 'quotations', 'key' => 'governorate_help', 'text_ar' => 'اختر المحافظة. إذا اختلفت محافظة الاستلام عن التسليم تُضاف رسوم التنقل بين المحافظات تلقائياً.', 'text_en' => 'Select the governorate. A cross-city surcharge is applied automatically when pickup ≠ dropoff governorate.'],
            ['group' => 'trips', 'key' => 'governorate_help', 'text_ar' => 'اختر المحافظة. إذا اختلفت محافظة الاستلام عن التسليم تُضاف رسوم التنقل بين المحافظات تلقائياً.', 'text_en' => 'Select the governorate. A cross-city surcharge is applied automatically when pickup ≠ dropoff governorate.'],

            // 27 Egyptian governorates
            ['group' => 'enums', 'key' => 'egyptian_governorate.alexandria', 'text_ar' => 'الإسكندرية', 'text_en' => 'Alexandria'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.aswan', 'text_ar' => 'أسوان', 'text_en' => 'Aswan'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.asyut', 'text_ar' => 'أسيوط', 'text_en' => 'Asyut'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.beheira', 'text_ar' => 'البحيرة', 'text_en' => 'Beheira'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.beni_suef', 'text_ar' => 'بني سويف', 'text_en' => 'Beni Suef'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.cairo', 'text_ar' => 'القاهرة', 'text_en' => 'Cairo'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.dakahlia', 'text_ar' => 'الدقهلية', 'text_en' => 'Dakahlia'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.damietta', 'text_ar' => 'دمياط', 'text_en' => 'Damietta'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.faiyum', 'text_ar' => 'الفيوم', 'text_en' => 'Faiyum'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.gharbia', 'text_ar' => 'الغربية', 'text_en' => 'Gharbia'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.giza', 'text_ar' => 'الجيزة', 'text_en' => 'Giza'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.ismailia', 'text_ar' => 'الإسماعيلية', 'text_en' => 'Ismailia'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.kafr_el_sheikh', 'text_ar' => 'كفر الشيخ', 'text_en' => 'Kafr el-Sheikh'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.luxor', 'text_ar' => 'الأقصر', 'text_en' => 'Luxor'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.matrouh', 'text_ar' => 'مطروح', 'text_en' => 'Matrouh'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.minya', 'text_ar' => 'المنيا', 'text_en' => 'Minya'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.monufia', 'text_ar' => 'المنوفية', 'text_en' => 'Monufia'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.new_valley', 'text_ar' => 'الوادي الجديد', 'text_en' => 'New Valley'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.north_sinai', 'text_ar' => 'شمال سيناء', 'text_en' => 'North Sinai'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.port_said', 'text_ar' => 'بورسعيد', 'text_en' => 'Port Said'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.qalyubia', 'text_ar' => 'القليوبية', 'text_en' => 'Qalyubia'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.qena', 'text_ar' => 'قنا', 'text_en' => 'Qena'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.red_sea', 'text_ar' => 'البحر الأحمر', 'text_en' => 'Red Sea'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.sharqia', 'text_ar' => 'الشرقية', 'text_en' => 'Sharqia'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.sohag', 'text_ar' => 'سوهاج', 'text_en' => 'Sohag'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.south_sinai', 'text_ar' => 'جنوب سيناء', 'text_en' => 'South Sinai'],
            ['group' => 'enums', 'key' => 'egyptian_governorate.suez', 'text_ar' => 'السويس', 'text_en' => 'Suez'],
        ];

        foreach ($rows as $row) {
            Translation::query()->updateOrCreate(
                ['group' => $row['group'], 'key' => $row['key']],
                array_merge($row, ['is_system' => true])
            );
        }
    }
}
