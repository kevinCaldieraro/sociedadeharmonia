<?php

namespace App\Contracts;

use App\Models\Member\Member;

interface MemberInterface
{
    public function save(): Member;
    /**
     * @return Member[]
     */
    public function disable(): array;
    /**
     * @return Member[]
     */
    public function enable(): array;
    public static function fromModel(Member $member): self;
    public static function fromData(array $data): self;
}