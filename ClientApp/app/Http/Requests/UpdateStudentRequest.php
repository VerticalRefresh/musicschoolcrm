<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UpdateStudentRequest extends FormRequest
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
            'first_name'   => ['sometimes','string','max:255'],
            'last_name'    => ['sometimes','string','max:255'],
            'email'        => ['sometimes','string','email:filter','max:255'],
            'phone'        => ['sometimes','string','max:32'],
            'birthday'     => ['sometimes','date'],

            'subscription' => ['sometimes','numeric','min:0'],
            'balance'      => ['sometimes','numeric','min:0'],

            'tutor_id'     => ['sometimes','nullable','integer', Rule::exists('tutors','id')],
            'guardian_id'  => ['sometimes','nullable','integer', Rule::exists('guardians','id')],
            'franchise_id' => ['sometimes','nullable','integer', Rule::exists('franchises','id')],
        ];
    }

    public function withValidator ($validator): void
    {
            $validator->after(function ($v) {
            $student = $this->route('student'); // route-model bound instance
            $birthday = $this->filled('birthday') ? $this->date('birthday') : optional($student)->birthday;
            if ($birthday) {
                $age = now()->diffInYears(\Carbon\Carbon::parse($birthday));
                $guardianId = $this->has('guardian_id') ? $this->input('guardian_id') : optional($student)->guardian_id;
                if ($age < 18 && empty($guardianId)) {
                    $v->errors()->add('guardian_id', 'Guardian is required for minors.');
                }
            }
        });
    }
}
