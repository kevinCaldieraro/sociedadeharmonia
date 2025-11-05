<?php

namespace App\Http\Requests\Member;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'cpf' => ['required'],
            'birth_date' => ['required'],
            'type_member' => ['required', Rule::in(['patrimonial', 'patrimonial_spouse', 'affiliated'])],
            'join_date' => ['required', 'before_or_equal:today'],
            'patrimonial_purchase_date' => [
                'nullable',
                Rule::requiredIf(fn () => $this->type_member === 'patrimonial'),
            ],
            'relationship' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($this->type_member, ['patrimonial_spouse', 'affiliated']))
            ],
            'patrimonial_member' => [
                'nullable',
                Rule::requiredIf(fn () => in_array($this->type_member, ['patrimonial_spouse', 'affiliated'])),
            ],
            'street' => ['required'],
            'number' => ['required'],
            'neighborhood' => ['required'],
            'city' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'join_date.before_or_equal' => 'A data de adesão não pode ser maior que a data atual.',
            'patrimonial_purchase_date.required' => 'A data de compra do título é obrigatória para membros patrimoniais.',
            'relationship.required' => 'O campo Parentesco é obrigatório para cônjuges e agregados.',
            'patrimonial_member.required' => 'O membro patrimonial vinculado é obrigatório para cônjuges e agregados.',
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['birth_date', 'join_date', 'patrimonial_purchase_date'] as $field) {
            if ($this->filled($field)) {
                try {
                    $this->merge([
                        $field => Carbon::createFromFormat('d/m/Y', $this->$field)->format('Y-m-d'),
                    ]);
                } catch (\Exception $e) {}
            }
        }
    }
}
