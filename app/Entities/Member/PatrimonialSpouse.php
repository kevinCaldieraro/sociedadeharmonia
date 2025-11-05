<?php

namespace App\Entities\Member;

use App\Contracts\MemberInterface;
use App\Models\Member\Member;
use App\Models\Member\MemberRole;
use Carbon\Carbon;
use DomainException;

class PatrimonialSpouse implements MemberInterface
{
    protected ?Member $memberModel;
    protected array $data;

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
        $idPatrimonial = $this->data['patrimonial_member']['id'];
        $patrimonialMember = MemberRole::where('member_id', $idPatrimonial)->first();
        $status = 'ativo';

        $joinDate = Carbon::createFromFormat('Y-m-d', $this->data['join_date']);
        $purchaseDatePatrimonial = Carbon::createFromFormat('Y-m-d', $patrimonialMember->patrimonial_purchase_date);

        if ($joinDate->lt($purchaseDatePatrimonial)) {
            throw new DomainException('A data de adesão do membro deve ser igual ou após a data de compra do título do membro patrimonial.');
        }

        if ($patrimonialMember->status === 'desativado') {
            $status = 'desativado';
        }

        MemberRole::create([
            'member_id' => $member->id,
            'patrimonial_member_id' => $idPatrimonial,
            'type' => $this->data['type_member'],
            'is_exempt' => true,
            'relationship' => $this->data['relationship'],
            'status' => $status,
            'join_date' => $joinDate->format('Y-m-d')
        ]);

        return $member;
    }

    /**
     * @return Member[]
     */
    public function disable(): array
    {
        return $this->handleStatusMember('desativado');
    }

    /**
     * @return Member[]
     */
    public function enable(): array
    {
        if ($this->memberModel->role->patrimonialMember->role->status === 'desativado') {
            throw new DomainException(
                'Não é possível ativar o membro porque seu membro patrimonial vinculado está desativado.'
            );
        }
        
        return $this->handleStatusMember('ativo');
    }

    /**
     * @return Member[]
     */
    private function handleStatusMember(string $status): array
    {
        $role = $this->memberModel->role;
        $role->status = $status;
        $role->save();

        $this->memberModel = $this->memberModel->fresh([
            'role:id,member_id,patrimonial_member_id,type,is_exempt,relationship,status',
            'role.patrimonialMember:id,name',
            'role.patrimonialMember.subscription:id,member_id,status',
            'subscription:id,member_id,status'
        ]);

        return [$this->memberModel];
    }
}