<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CommunicationChannel;
use App\Enums\CommunicationDirection;
use Database\Factories\CustomerCommunicationFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerCommunication extends Model
{
    /** @use HasFactory<CustomerCommunicationFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'user_id',
        'channel',
        'direction',
        'subject',
        'body',
        'attachments',
        'external_message_id',
        'sent_at',
    ];

    protected $casts = [
        'channel' => CommunicationChannel::class,
        'direction' => CommunicationDirection::class,
        'attachments' => 'array',
        'sent_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
