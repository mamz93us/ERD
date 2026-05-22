<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::TEMPLATES as $row) {
            NotificationTemplate::query()->updateOrCreate(
                ['key' => $row['key'], 'channel' => $row['channel'], 'locale' => $row['locale']],
                [
                    'subject' => $row['subject'] ?? null,
                    'body' => $row['body'],
                    'is_active' => true,
                ],
            );
        }
    }

    private const TEMPLATES = [
        // booking_confirmed — to customer
        ['key' => 'booking_confirmed', 'channel' => 'whatsapp', 'locale' => 'ar',
            'body' => "مرحباً {{customer_name}}،\nتم تأكيد حجزك رقم {{trip_number}}.\nمن: {{pickup_location}}\nإلى: {{dropoff_location}}\nالموعد: {{scheduled_start}}\nشكراً لاختيارك مجموعة عدلي."],
        ['key' => 'booking_confirmed', 'channel' => 'whatsapp', 'locale' => 'en',
            'body' => "Hello {{customer_name}},\nYour booking {{trip_number}} is confirmed.\nFrom: {{pickup_location}}\nTo: {{dropoff_location}}\nWhen: {{scheduled_start}}\nThank you for choosing Adly Group Agency."],
        ['key' => 'booking_confirmed', 'channel' => 'mail', 'locale' => 'ar',
            'subject' => 'تأكيد حجز {{trip_number}}',
            'body' => 'مرحباً {{customer_name}}، تم تأكيد حجزك رقم {{trip_number}} من {{pickup_location}} إلى {{dropoff_location}} في موعد {{scheduled_start}}.'],
        ['key' => 'booking_confirmed', 'channel' => 'mail', 'locale' => 'en',
            'subject' => 'Booking {{trip_number}} confirmed',
            'body' => 'Hello {{customer_name}}, your booking {{trip_number}} from {{pickup_location}} to {{dropoff_location}} on {{scheduled_start}} is confirmed.'],

        // trip_reminder_24h — to customer + driver
        ['key' => 'trip_reminder_24h', 'channel' => 'whatsapp', 'locale' => 'ar',
            'body' => 'تذكير: رحلتك {{trip_number}} غداً الساعة {{scheduled_start}} من {{pickup_location}}.'],
        ['key' => 'trip_reminder_24h', 'channel' => 'whatsapp', 'locale' => 'en',
            'body' => 'Reminder: trip {{trip_number}} is tomorrow at {{scheduled_start}}, pickup at {{pickup_location}}.'],
        ['key' => 'trip_reminder_24h', 'channel' => 'mail', 'locale' => 'ar',
            'subject' => 'تذكير برحلة {{trip_number}} غداً',
            'body' => 'تذكير برحلتك {{trip_number}} غداً الساعة {{scheduled_start}} من {{pickup_location}}.'],
        ['key' => 'trip_reminder_24h', 'channel' => 'mail', 'locale' => 'en',
            'subject' => 'Reminder: trip {{trip_number}} tomorrow',
            'body' => 'Reminder for trip {{trip_number}} tomorrow at {{scheduled_start}}, pickup at {{pickup_location}}.'],

        // trip_assigned — to driver
        ['key' => 'trip_assigned', 'channel' => 'whatsapp', 'locale' => 'ar',
            'body' => "تم إسناد رحلة جديدة لك:\nرقم: {{trip_number}}\nالعميل: {{customer_name}}\nمن: {{pickup_location}}\nإلى: {{dropoff_location}}\nالموعد: {{scheduled_start}}"],
        ['key' => 'trip_assigned', 'channel' => 'whatsapp', 'locale' => 'en',
            'body' => "A new trip has been assigned to you:\nNumber: {{trip_number}}\nCustomer: {{customer_name}}\nFrom: {{pickup_location}}\nTo: {{dropoff_location}}\nWhen: {{scheduled_start}}"],
        ['key' => 'trip_assigned', 'channel' => 'mail', 'locale' => 'ar',
            'subject' => 'رحلة جديدة {{trip_number}}',
            'body' => 'تم إسناد رحلة {{trip_number}} لك مع العميل {{customer_name}} في {{scheduled_start}}.'],
        ['key' => 'trip_assigned', 'channel' => 'mail', 'locale' => 'en',
            'subject' => 'New trip {{trip_number}}',
            'body' => 'Trip {{trip_number}} with {{customer_name}} on {{scheduled_start}} has been assigned to you.'],

        // invoice_issued — to customer (with PDF attachment)
        ['key' => 'invoice_issued', 'channel' => 'whatsapp', 'locale' => 'ar',
            'body' => "مرحباً {{customer_name}}،\nتم إصدار الفاتورة {{invoice_number}} بقيمة {{total}}.\nتاريخ الاستحقاق: {{due_date}}.\nشكراً."],
        ['key' => 'invoice_issued', 'channel' => 'whatsapp', 'locale' => 'en',
            'body' => "Hello {{customer_name}},\nInvoice {{invoice_number}} has been issued for {{total}}.\nDue: {{due_date}}.\nThank you."],
        ['key' => 'invoice_issued', 'channel' => 'mail', 'locale' => 'ar',
            'subject' => 'فاتورة {{invoice_number}}',
            'body' => 'مرحباً {{customer_name}}، نرفق فاتورة {{invoice_number}} بقيمة {{total}}، تاريخ الاستحقاق {{due_date}}.'],
        ['key' => 'invoice_issued', 'channel' => 'mail', 'locale' => 'en',
            'subject' => 'Invoice {{invoice_number}}',
            'body' => 'Hello {{customer_name}}, please find attached invoice {{invoice_number}} for {{total}}, due {{due_date}}.'],

        // payment_received — to customer
        ['key' => 'payment_received', 'channel' => 'whatsapp', 'locale' => 'ar',
            'body' => "مرحباً {{customer_name}}،\nاستلمنا دفعتك رقم {{payment_number}} بمبلغ {{amount}} بتاريخ {{payment_date}}.\nطريقة الدفع: {{method}}.\nشكراً."],
        ['key' => 'payment_received', 'channel' => 'whatsapp', 'locale' => 'en',
            'body' => "Hello {{customer_name}},\nWe received your payment {{payment_number}} of {{amount}} on {{payment_date}}.\nMethod: {{method}}.\nThank you."],
        ['key' => 'payment_received', 'channel' => 'mail', 'locale' => 'ar',
            'subject' => 'إيصال استلام دفعة {{payment_number}}',
            'body' => 'مرحباً {{customer_name}}، نؤكد استلام دفعة {{payment_number}} بمبلغ {{amount}} عبر {{method}} بتاريخ {{payment_date}}.'],
        ['key' => 'payment_received', 'channel' => 'mail', 'locale' => 'en',
            'subject' => 'Payment receipt {{payment_number}}',
            'body' => 'Hello {{customer_name}}, we confirm receipt of payment {{payment_number}} of {{amount}} via {{method}} on {{payment_date}}.'],

        // document_expiring_soon — to fleet_manager + branch_manager
        ['key' => 'document_expiring_soon', 'channel' => 'whatsapp', 'locale' => 'ar',
            'body' => "تنبيه انتهاء وثيقة:\nالموضوع: {{subject}}\nنوع الوثيقة: {{doc_type}}\nالأيام المتبقية: {{days}}\nتاريخ الانتهاء: {{expiry_date}}"],
        ['key' => 'document_expiring_soon', 'channel' => 'whatsapp', 'locale' => 'en',
            'body' => "Document expiry alert:\nSubject: {{subject}}\nDoc type: {{doc_type}}\nDays remaining: {{days}}\nExpiry: {{expiry_date}}"],
        ['key' => 'document_expiring_soon', 'channel' => 'mail', 'locale' => 'ar',
            'subject' => 'وثيقة على وشك الانتهاء — {{subject}}',
            'body' => 'تنبيه: وثيقة {{doc_type}} لـ {{subject}} ستنتهي خلال {{days}} يوم (تاريخ الانتهاء: {{expiry_date}}).'],
        ['key' => 'document_expiring_soon', 'channel' => 'mail', 'locale' => 'en',
            'subject' => 'Document expiring soon — {{subject}}',
            'body' => 'Heads up: {{doc_type}} for {{subject}} expires in {{days}} days (expiry date: {{expiry_date}}).'],

        // credit_note_approval_needed — to branch_manager+
        ['key' => 'credit_note_approval_needed', 'channel' => 'whatsapp', 'locale' => 'ar',
            'body' => "إشعار ائتمان بحاجة لاعتمادك:\nرقم: {{note_number}}\nالفاتورة: {{invoice_number}}\nالقيمة: {{amount}}\nالسبب: {{reason}}\nأنشأه: {{created_by}}"],
        ['key' => 'credit_note_approval_needed', 'channel' => 'whatsapp', 'locale' => 'en',
            'body' => "Credit note awaiting your approval:\nNumber: {{note_number}}\nInvoice: {{invoice_number}}\nAmount: {{amount}}\nReason: {{reason}}\nCreated by: {{created_by}}"],
        ['key' => 'credit_note_approval_needed', 'channel' => 'mail', 'locale' => 'ar',
            'subject' => 'إشعار ائتمان بحاجة للاعتماد — {{note_number}}',
            'body' => 'إشعار ائتمان {{note_number}} على الفاتورة {{invoice_number}} بقيمة {{amount}} ينتظر اعتمادك. السبب: {{reason}} (أنشأه {{created_by}}).'],
        ['key' => 'credit_note_approval_needed', 'channel' => 'mail', 'locale' => 'en',
            'subject' => 'Credit note needs approval — {{note_number}}',
            'body' => 'Credit note {{note_number}} on invoice {{invoice_number}} for {{amount}} is awaiting your approval. Reason: {{reason}} (created by {{created_by}}).'],
    ];
}
