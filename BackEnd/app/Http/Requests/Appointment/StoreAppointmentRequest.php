<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for appointment creation.
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_patient' => ['required', 'exists:patients,id'],
            'id_doctor'  => ['nullable', 'exists:users,id'],
            'id_service' => ['required', 'integer'],
            'date'       => ['required', 'date', 'after_or_equal:today'],
            'symptom'    => ['required', 'string', 'max:255'],
            'status'     => ['nullable', 'integer', 'in:0,1,2'],
        ];
    }

    /**
     * Custom error messages for validation rules.
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_patient.required' => 'Bệnh nhân không được bỏ trống',
            'id_patient.exists'   => 'Bệnh nhân không tồn tại',
            'id_doctor.exists'    => 'Bác sĩ không tồn tại',
            'id_service.required' => 'Dịch vụ không được bỏ trống',
            'date.required'       => 'Ngày khám không được bỏ trống',
            'date.after_or_equal' => 'Ngày khám phải từ hôm nay trở đi',
            'symptom.required'    => 'Triệu chứng không được bỏ trống',
        ];
    }

    /**
     * Handle failed validation response.
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        $firstError = $errors[0] ?? 'Dữ liệu nhập vào không hợp lệ';

        throw new HttpResponseException(response()->json([
            'message' => $firstError,
        ], 422));
    }
}
