<?php

namespace App\Factory;

use App\Contracts\MemberInterface;
use App\Entities\Member\Affiliated;
use App\Entities\Member\Patrimonial;
use App\Entities\Member\PatrimonialSpouse;
use App\Models\Member\Member;
use App\Models\Member\MemberRole;
use InvalidArgumentException;

class MemberFactory
{
    public static function create(array $data): ?MemberInterface
    {
        return match ($data['type_member']) {
            'patrimonial' => Patrimonial::fromData($data),
            'patrimonial_spouse' => PatrimonialSpouse::fromData($data),
            'affiliated' => Affiliated::fromData($data),
            default => throw new InvalidArgumentException('Tipo de membro inválido.')
        };
    }

    public static function resolve(int $memberId): ?MemberInterface
    {
        $member = Member::find($memberId);

        if (!$member) {
            return null;
        }

        return match ($member->role->type) {
            'patrimonial' => Patrimonial::fromModel($member),
            'patrimonial_spouse' => PatrimonialSpouse::fromModel($member),
            'affiliated' => Affiliated::fromModel($member),
            default => throw new InvalidArgumentException('Tipo de membro inválido.'),
        };
    }
}