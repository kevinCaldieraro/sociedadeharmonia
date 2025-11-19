<?php

namespace App\Imports;

use App\Models\Member\Member;
use App\Services\Member\MemberService;
use Log;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MembersImport implements OnEachRow
{
    private array $headers = [];
    private array $columnMap = [
        'nome' => 'name',
        'email' => 'email',
        'telefone' => 'phone',
        'nascimento' => 'birth_date',
        'cpf' => 'cpf',
        'cidade' => 'city',
        'bairro' => 'neighborhood',
        'rua' => 'street',
        'número' => 'number',
        'tipo' => 'type_member',
        'data de adesão' => 'join_date',
        'data de compra do título' => 'patrimonial_purchase_date',
        'valor do título' => 'patrimonial_value',
        'parentesco' => 'relationship',
        'membro patrimonial' => 'patrimonial_member',
    ];
    private array $typeMap = [
        'patrimonial' => 'patrimonial',
        'agregado' => 'affiliated',
        'cônjuge' => 'patrimonial_spouse',
    ];
    private array $patrimonials;
    private array $affiliates;
    private array $spouses;
    private MemberService $service;

    public function __construct(MemberService $service)
    {
        $this->service = $service;
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $rowArray = $row->toArray();

        if ($rowIndex === 1) {
            $this->headers = $rowArray;
            return;
        }

        if (collect($rowArray)->filter(fn($cell) => !empty($cell))->isEmpty()) {
            return;
        }

        $formattedRow = $this->formatRow($rowArray);

        if ($formattedRow['type_member'] === 'patrimonial') {
            $this->patrimonials[] = $formattedRow;
        }
        
        if ($formattedRow['type_member'] === 'affiliated') {
            $this->affiliates[] = $formattedRow;
        }

        if ($formattedRow['type_member'] === 'patrimonial_spouse') {
            $this->spouses[] = $formattedRow;
        }
    }

    private function formatRow(array $rowArray): array
    {
        $formattedRow = collect($this->headers)
            ->combine($rowArray)
            ->mapWithKeys(function ($value, $key) {
                $mappedKey = $this->columnMap[$key] ?? $key;

                if ($mappedKey === 'type_member') {
                    $value = $this->typeMap[$value] ?? $value;
                }

                if (in_array($mappedKey, ['birth_date', 'patrimonial_purchase_date', 'join_date']) && !empty($value)) {
                    if (is_numeric($value)) {
                        $value = Date::excelToDateTimeObject($value)->format('Y-m-d');
                    } else {
                        $clean = str_replace('/', '-', trim($value));
                        $timestamp = strtotime($clean);

                        $value = date('Y-m-d', $timestamp);
                    }
                }

                if (in_array($mappedKey, ['patrimonial_value']) && !is_numeric($value)) {
                    $value = $this->normalizeCurrency($value);
                }

                return [$mappedKey => $value];
            })
            ->toArray();

        return $formattedRow;
    }

    public function registerMembers()
    {
        foreach ($this->patrimonials as $member) {
            $this->service->register($member);
        }

        foreach ($this->affiliates as $member) {
            $patrimonial = Member::where('name', $member['patrimonial_member'])->first();
            if ($patrimonial) {
                $member['patrimonial_member'] = [];
                $member['patrimonial_member']['id'] = $patrimonial->id;
                $this->service->register($member);
            }
        }

        foreach ($this->spouses as $member) {
            $patrimonial = Member::where('name', $member['patrimonial_member'])->first();
            if ($patrimonial) {
                $member['patrimonial_member'] = [];
                $member['patrimonial_member']['id'] = $patrimonial->id;
                $this->service->register($member);
            }
        }
    }

    private function normalizeCurrency($value)
    {
        $value = str_replace(['R$', ' ', '.'], '', $value);
        $value = str_replace(',', '.', $value);

        if (is_numeric($value)) {
            return floatval($value);
        }

        return null;
    }
}
