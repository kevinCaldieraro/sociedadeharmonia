<?php

namespace App\Entities\Member;

use App\Contracts\MemberInterface;
use App\Entities\Subscription\Subscription;
use App\Events\SubscriptionMonthMemberUpdated;
use App\Models\Member\Member;
use App\Models\Member\MemberRole;
use App\Models\Subscription\SubscriptionMonthMember;
use App\Traits\ConvertsDateAttributes;
use Carbon\Carbon;
use DomainException;

class Patrimonial implements MemberInterface
{
    use ConvertsDateAttributes;
    protected array $data;
    protected ?Member $memberModel;

    public function __construct(Member $member = null, array $data = [])
    {
        $this->memberModel = $member;
        $this->data = $data;
    }

    public static function fromData(array $data): self
    {
        return new self(null, $data);
    }

    public static function fromModel(Member $member): self
    {
        return new self($member);
    }

    public function save(): Member
    {
        $member = Member::create($this->data);
        $purchaseDate = Carbon::createFromFormat('Y-m-d', $this->data['patrimonial_purchase_date']);
        $joinDate = Carbon::createFromFormat('Y-m-d', $this->data['join_date']);

        if (!$purchaseDate->eq($joinDate)) {
            throw new DomainException('A data de adesão do membro deve ser igual a data de compra do título.');
        }

        MemberRole::create([
            'member_id' => $member->id,
            'type' => $this->data['type_member'],
            'is_exempt' => false,
            'patrimonial_purchase_date' => $this->convertToDatabaseDate($this->data['patrimonial_purchase_date']),
            'patrimonial_value' => $this->data['patrimonial_value'],
            'join_date' => $joinDate->format('Y-m-d')
        ]);

        $subscription = new Subscription([
            'member_id' => $member->id,
            'join_date' => $joinDate->format('Y-m-d'),
            'status' => 'regular',
            'price' => 79.9
        ]);
        $subscription->save();

        return $member;
    }

    /**
     * @return Member[]
     */
    public function disable(): array
    {
        $updatedMembers = [];

        $this->memberModel->affiliates->each(function (MemberRole $affiliatedRole) use (&$updatedMembers) {
            if ($affiliatedRole->is_exempt) {
                $affiliatedRole->status = 'desativado';
                $affiliatedRole->save();
    
                $updatedMembers[] = $affiliatedRole->member->fresh([
                    'role:id,member_id,patrimonial_member_id,type,is_exempt,relationship,status',
                    'role.patrimonialMember:id,name',
                    'role.patrimonialMember.subscription:id,member_id,status',
                    'subscription:id,member_id,status'
                ]);
            }
        });

        $role = $this->memberModel->role;
        $role->status = 'desativado';
        $role->save();

        $this->memberModel = $this->memberModel->fresh([
            'role:id,member_id,patrimonial_member_id,type,is_exempt,relationship,status',
            'role.patrimonialMember:id,name',
            'role.patrimonialMember.subscription:id,member_id,status',
            'subscription:id,member_id,status'
        ]);

        return [$this->memberModel, ...$updatedMembers];
    }

    /**
     * @return Member[]
     */
    public function enable(): array
    {
        $role = $this->memberModel->role;
        $role->status = 'ativo';
        $role->save();

        $currDate = Carbon::now();
        $subscription = $this->memberModel->subscription;
        $subscriptionMonthExists = SubscriptionMonthMember::where('subscription_member_id', $subscription->id)
            ->where('month', $currDate->month)
            ->where('year', $currDate->year)
            ->exists();

        if (!$subscriptionMonthExists) {
            $status = 'pendente';

            if ($currDate->day > 15) {
                $status = 'vencida';
            }

            $expiration_date = Carbon::create($currDate->year, $currDate->month, 15);

            SubscriptionMonthMember::create([
                'subscription_member_id' => $subscription->id,
                'expiration_date' => $expiration_date,
                'month' => $currDate->month,
                'year' => $currDate->year,
                'status' => $status
            ]);

            event(new SubscriptionMonthMemberUpdated(
                $subscription->months()->latest('id')->first()
            ));
        }

        $this->memberModel = $this->memberModel->fresh([
            'role:id,member_id,patrimonial_member_id,type,is_exempt,relationship,status',
            'role.patrimonialMember:id,name',
            'role.patrimonialMember.subscription:id,member_id,status',
            'subscription:id,member_id,status'
        ]);

        return [$this->memberModel];
    }
}