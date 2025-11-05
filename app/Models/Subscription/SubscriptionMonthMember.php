<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionMonthMember extends Model
{
    use HasFactory;

    protected $table = 'subscription_month_member';
    protected $fillable = [
        'subscription_member_id',
        'status',
        'expiration_date',
        'value',
        'month',
        'year'
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(SubscriptionMember::class, 'subscription_member_id', 'id');
    }
}
