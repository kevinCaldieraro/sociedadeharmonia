<?php

namespace App\Services\Member;

use App\Factory\MemberFactory;
use App\Models\Member\Member;
use App\Repositories\Member\MemberRepository;
use App\Traits\ConvertsDateAttributes;
use DB;

class MemberService
{
    use ConvertsDateAttributes;

    protected MemberRepository $repository;

    public function __construct()
    {
        $this->repository = new MemberRepository();
    }

    public function register(array $data): Member
    {
        $memberEntity = MemberFactory::create($data);

        $newMember = DB::transaction(function () use($memberEntity) {
            $memberRegistered = $memberEntity->save();

            return $memberRegistered->fresh([
                'role:id,member_id,patrimonial_member_id,type,is_exempt,relationship,status,patrimonial_purchase_date,patrimonial_value,join_date',
                'role.patrimonialMember:id,name',
                'role.patrimonialMember.subscription:id,member_id,status,join_date',
                'subscription:id,member_id,status,join_date',
            ]);
        });

        return $newMember;
    }

    public function update(Member $member, array $data): Member
    {
        $member->update($data);

        return $member->fresh([
            'role:id,member_id,patrimonial_member_id,type,is_exempt,relationship,status,patrimonial_purchase_date,patrimonial_value,join_date',
            'role.patrimonialMember:id,name',
            'role.patrimonialMember.subscription:id,member_id,status,join_date',
            'subscription:id,member_id,status,join_date',
        ]);;
    }

    /**
     * @return Member[]
     */
    public function disable($id): array
    {
        $memberEntity = MemberFactory::resolve($id);
        return $memberEntity->disable();
    }

    /**
     * @return Member[]
     */
    public function enable($id): array
    {
        $memberEntity = MemberFactory::resolve($id);
        return $memberEntity->enable();
    }
}