<?php

namespace App\Repositories\Member;

use App\Models\Member\Member;
use App\Models\Member\MemberRole;

class MemberRepository
{
    public function find($id): Member
    {
        return Member::find($id);
    }

    public function role($id): MemberRole
    {
        return MemberRole::where('member_id', $id)->first();
    }

    public function save()
    {

    }
}