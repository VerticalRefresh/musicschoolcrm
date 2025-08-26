<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name'        => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email:filter', 'max:255'],
            'phone'             => ['required', 'string', 'max:32'],
            'birthday'          => ['required', 'date'],

            'subscription'      => ['required', 'numeric', 'min:0'],
            'balance'           => ['required', 'numeric', 'min:0'],

            'tutor_id'          => ['nullable', 'integer', Rule::exists('tutors', 'id')],
            'guardian_id'       => ['nullable', 'integer', Rule::exists('guardians', 'id')],
            'franchise_id'      => ['nullable', 'integer', Rule::exists('franchises', 'id')],

        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $bday = $this->date('birthday');
            if ($bday instanceof \DateTimeInterface) {
                $age = Carbon::now()->diffInYears($bday);
                if ($age < 18 && !$this->filled('guardian_id')) {
                    $v->errors()->add('guardian_id', 'Guardian is required for minors.');
                }
            }
        });
    }
}
