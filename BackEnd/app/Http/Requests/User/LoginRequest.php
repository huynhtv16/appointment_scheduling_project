<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Check if the request is authorized.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Allow all requests
    }

    /**
     * Validation rules for creating a user.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'gmail'     => 'required|email',
            'password'  => 'required|string|min:6'
        ];
    }

    /**
     * Custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gmail.required'    => 'Email không được để trống',
            'gmail.email'       => 'Email không đúng định dạng',
            'password.required' => 'Mật khẩu bắt buộc',
            'password.min'      => 'Mật khẩu ít nhất 6 ký tự'
        ];
    }

    /**
     * Handle failed validation and return JSON response.
     *
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
