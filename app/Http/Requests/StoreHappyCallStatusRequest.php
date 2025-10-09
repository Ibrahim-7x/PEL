<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StoreHappyCallStatusRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'resolved_date' => [
                'required',
                'date',
                'before_or_equal:happy_call_date',
            ],
            'happy_call_date' => [
                'required',
                'date',
                'after_or_equal:resolved_date',
            ],
            'customer_satisfied' => [
                'required',
                Rule::in(['Yes', 'No']),
            ],
            'delay_reason' => [
                'required',
                'string',
                'max:1000',
                'regex:/^[^<>{}]*$/', // Prevent XSS by blocking HTML tags
            ],
            'voice_of_customer' => [
                'nullable',
                'string',
                'max:2000',
                'regex:/^[^<>{}]*$/', // Prevent XSS by blocking HTML tags
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'resolved_date.required' => 'The resolution date is required.',
            'resolved_date.date' => 'The resolution date must be a valid date.',
            'resolved_date.before_or_equal' => 'The resolution date cannot be in the future.',
            'resolved_date.before_or_equal_happy_call_date' => 'The resolution date must be before or equal to the happy call date.',

            'happy_call_date.required' => 'The happy call date is required.',
            'happy_call_date.date' => 'The happy call date must be a valid date.',
            'happy_call_date.before_or_equal' => 'The happy call date cannot be in the future.',
            'happy_call_date.after_or_equal' => 'The happy call date must be after or equal to the resolution date.',

            'customer_satisfied.required' => 'Please specify if the customer is satisfied.',
            'customer_satisfied.in' => 'Customer satisfaction must be either Yes or No.',

            'delay_reason.required' => 'Please select a reason for delay.',
            'delay_reason.string' => 'The delay reason must be a valid text.',
            'delay_reason.max' => 'The delay reason cannot exceed 1000 characters.',
            'delay_reason.regex' => 'The delay reason contains invalid characters.',

            'voice_of_customer.string' => 'The voice of customer must be a valid text.',
            'voice_of_customer.max' => 'The voice of customer cannot exceed 2000 characters.',
            'voice_of_customer.regex' => 'The voice of customer contains invalid characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'resolved_date' => 'resolution date',
            'happy_call_date' => 'happy call date',
            'customer_satisfied' => 'customer satisfaction',
            'delay_reason' => 'delay reason',
            'voice_of_customer' => 'voice of customer',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional custom validation logic can be added here
            $this->validateDateLogic($validator);
        });
    }

    /**
     * Custom validation for date logic.
     */
    private function validateDateLogic($validator): void
    {
        $resolvedDate = $this->input('resolved_date');
        $happyCallDate = $this->input('happy_call_date');

        if ($resolvedDate && $happyCallDate) {
            $resolved = Carbon::parse($resolvedDate);
            $happyCall = Carbon::parse($happyCallDate);
            $today = Carbon::today();

            // Business rule: Happy call should not be more than 30 days after resolution
            if ($happyCall->diffInDays($resolved) > 30) {
                $validator->errors()->add('happy_call_date', 'Happy call date should not be more than 30 days after resolution date.');
            }

            // Business rule: Resolution should not be more than 90 days ago
            if ($resolved->diffInDays($today) > 90) {
                $validator->errors()->add('resolved_date', 'Resolution date should not be more than 90 days ago.');
            }
        }
    }

    /**
     * Sanitize input data before validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace and sanitize string inputs
        $this->merge([
            'delay_reason' => $this->sanitizeString($this->input('delay_reason')),
            'voice_of_customer' => $this->sanitizeString($this->input('voice_of_customer')),
        ]);
    }

    /**
     * Sanitize string input by trimming and removing potential XSS vectors.
     */
    private function sanitizeString(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        // Trim whitespace
        $value = trim($value);

        // Remove null bytes and other potentially harmful characters
        $value = str_replace(["\0", "\r", "\n"], '', $value);

        return $value;
    }
}