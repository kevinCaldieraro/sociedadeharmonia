<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member\StoreMemberRequest;
use App\Imports\MembersImport;
use App\Models\Member\Member;
use App\Services\Member\MemberService;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    protected MemberService $service;

    public function __construct(MemberService $service)
    {
        $this->service = $service;
    }

    public function index(): Response
    {
        $members = Member::query()
            ->leftJoin('member_role', 'members.id', '=', 'member_role.member_id')
            ->with([
                'role:id,member_id,patrimonial_member_id,type,is_exempt,relationship,status,patrimonial_purchase_date,patrimonial_value,join_date',
                'role.patrimonialMember:id,name',
                'role.patrimonialMember.subscription:id,member_id,status',
                'subscription:id,member_id,status,join_date'
                ])
            ->select(['members.*'])
            ->orderByRaw("
                CASE 
                    WHEN member_role.type = 'patrimonial' THEN members.id 
                    ELSE member_role.patrimonial_member_id 
                END ASC
            ")
            ->orderByRaw("
                CASE member_role.type
                    WHEN 'patrimonial' THEN 1
                    WHEN 'patrimonial_spouse' THEN 2
                    WHEN 'affiliated' THEN 3
                END
            ")
            ->get();

        return Inertia::render('Members', [
            'members' => $members
        ]);
    }

    public function store(StoreMemberRequest $request): JsonResponse
    {
        $newMember = $this->service->register($request->all());

        return response()->json([
            'message' => 'Membro cadastrado com sucesso.',
            'member' => $newMember
        ], 201);
    }

    public function update(Member $member, Request $request): JsonResponse
    {
        $data = $request->only([
            'name', 'cpf', 'email', 'phone', 'street', 'number', 'neighborhood', 'city'
        ]);

        $memberUpdated = $this->service->update($member, $data);

        return response()->json([
            'message' => 'Membro atualizado com sucesso.',
            'memberUpdated' => $memberUpdated
        ], 200);
    }

    public function import(Request $request)
    {
        $request->validate([
            'member-sheet' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new MembersImport($this->service);
            Excel::import($import, $request->file('member-sheet'));
            DB::transaction(function () use ($import) {
                $import->registerMembers();
            });

            $members = Member::query()
            ->leftJoin('member_role', 'members.id', '=', 'member_role.member_id')
            ->with([
                'role:id,member_id,patrimonial_member_id,type,is_exempt,relationship,status,patrimonial_purchase_date,patrimonial_value,join_date',
                'role.patrimonialMember:id,name',
                'role.patrimonialMember.subscription:id,member_id,status',
                'subscription:id,member_id,status,join_date'
                ])
            ->select(['members.*'])
            ->orderByRaw("
                CASE 
                    WHEN member_role.type = 'patrimonial' THEN members.id 
                    ELSE member_role.patrimonial_member_id 
                END ASC
            ")
            ->orderByRaw("
                CASE member_role.type
                    WHEN 'patrimonial' THEN 1
                    WHEN 'patrimonial_spouse' THEN 2
                    WHEN 'affiliated' THEN 3
                END
            ")
            ->get();

            return response()->json([
                'message' => 'Arquivo importado e processado com sucesso!',
                'membersData' => $members
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar o arquivo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function disable($id): JsonResponse
    {
        $updatedMembers = $this->service->disable($id);

        return response()->json([
            'updatedMembers' => $updatedMembers,
            'message' => 'Membro desativado com sucesso.'
        ], 200);
    }

    public function enable($id): JsonResponse
    {
        $updatedMembers = $this->service->enable($id);

        return response()->json([
            'updatedMembers' => $updatedMembers,
            'message' => 'Membro ativado com sucesso.'
        ], 200);
    }

    public function getAllPatrimonialMembers(): JsonResponse
    {
        $patrimonialMembers = Member::with('role')
            ->whereHas('role', function ($query) {
                $query->where('type', 'patrimonial');
            })
            ->select('id', 'name')
            ->get();

        return response()->json([
            'message' => 'membros patrimoniais',
            'patrimonialMembers' => $patrimonialMembers
        ], 200);
    }
}
