<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePatientRequest extends FormRequest
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
     * Validation rules for patient actions (store, login, update).
     * @return array<string, string>
     */
    public function rules(): array
    {
        $action = $this->route()->getActionMethod();

        if ($action == 'store') {
            return [
                'name'        => 'required|string|max:255',
                'gender'      => 'required|string',
                'date'        => 'required|date|date_format:Y-m-d',
                'address'     => 'required|string',
                'gmail'       => 'required|email|unique:patients,gmail',
                'cccd'        => 'required|string|unique:patients,cccd',
                'bhyt'        => 'required|string|max:50',
                'password'    => 'required|string|min:6',
                'prehistoric' => 'nullable|string|max:500',
            ];
        } else if ($action == 'login') {
            return [
                'gmail'    => 'required|email|exists:patients,gmail',
                'password' => 'required|string|min:6',
            ];
        } else {
            return [
                'name'        => 'required|string|max:255',
                'gender'      => 'required|string',
                'date'        => 'required|date|date_format:Y-m-d',
                'address'     => 'required|string',
                'gmail'       => 'required|email',
                'cccd'        => 'required|string',
                'bhyt'        => 'required|string|max:50',
                'password'    => 'nullable|string|min:6',
                'prehistoric' => 'nullable|string|max:500',
            ];
        }
    }

    /**
     * Custom validation messages.
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'        => 'Tên bệnh nhân bắt buộc',
            'gender.required'      => 'Giới tính bắt buộc',
            'date.required'        => 'Ngày sinh bắt buộc',
            'date.date'            => 'Ngày sinh không hợp lệ',
            'date.date_format'     => 'Ngày sinh phải có định dạng Y-m-d (VD: 2000-01-01)',
            'address.required'     => 'Địa chỉ bắt buộc',
            'gmail.required'       => 'Email bắt buộc',
            'gmail.email'          => 'Email không hợp lệ',
            'gmail.unique'         => 'Email đã tồn tại',
            'gmail.exists'         => 'Email không tồn tại',
            'password.required'    => 'Mật khẩu bắt buộc',
            'password.min'         => 'Mật khẩu tối thiểu 6 ký tự',
            'cccd.required'        => 'CCCD bắt buộc',
            'cccd.unique'          => 'CCCD đã tồn tại',
            'bhyt.required'        => 'Mã BHYT bắt buộc',
            'bhyt.max'             => 'Mã BHYT tối đa 50 ký tự',
            'prehistoric.max'      => 'Tiền sử bệnh tối đa 500 ký tự',
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
