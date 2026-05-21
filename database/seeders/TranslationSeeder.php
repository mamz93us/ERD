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
        ];

        foreach ($rows as $row) {
            Translation::query()->updateOrCreate(
                ['group' => $row['group'], 'key' => $row['key']],
                array_merge($row, ['is_system' => true])
            );
        }
    }
}
