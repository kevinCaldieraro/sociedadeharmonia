<?php

namespace App\Models\Subscription;

use App\Models\Member\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionMember extends Model
{
    use HasFactory;

    protected $table = 'subscription_member';
    protected $fillable = [
        'member_id',
        'join_date',
        'status',
        'irregular_since',
        'price'
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function months(): HasMany
    {
        return $this->hasMany(SubscriptionMonthMember::class, 'subscription_member_id', 'id');
    }
}
