<?php

namespace App\Http\Requests\Subscription;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
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
            'id' => ['required'],
            'value' => ['required', 'numeric'],
            'paid_at' => ['required', 'before_or_equal:today'],
            'payment_method' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'paid_at.before_or_equal' => 'A data do pagamento não pode ser maior que a data atual.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('paid_at')) {
            try {
                $this->merge([
                    'paid_at' => Carbon::createFromFormat('d/m/Y', $this->paid_at)->format('Y-m-d'),
                ]);
            } catch (\Exception $e) {}
        }
    }
}
