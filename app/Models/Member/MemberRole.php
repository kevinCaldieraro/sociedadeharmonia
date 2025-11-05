<?php

namespace App\Models\Member;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberRole extends Model
{
    use HasFactory;

    protected $table = 'member_role';
    protected $fillable = [
        'member_id',
        'patrimonial_member_id',
        'type',
        'relationship',
        'is_exempt',
        'patrimonial_purchase_date',
        'patrimonial_value',
        'status',
        'join_date'
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function patrimonialMember()
    {
        return $this->belongsTo(Member::class, 'patrimonial_member_id', 'id');
    }
}
