<?php

namespace App\Models\Member;

use App\Models\Subscription\SubscriptionMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birth_date',
        'cpf',
        'city',
        'neighborhood',
        'street',
        'number'
    ];

    public function role(): HasOne
    {
        return $this->hasOne(MemberRole::class, 'member_id', 'id');
    }

    public function affiliates(): HasMany
    {
        return $this->hasMany(MemberRole::class, 'patrimonial_member_id', 'id');
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(SubscriptionMember::class, 'member_id', 'id');
    }

    protected static function booted()
    {
        static::deleting(function (Member $member) {
            $member->affiliates()->each(function (MemberRole $role) {
                $role->member()->delete();
            });
        });
    }
}
