<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
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
            'name'      => 'required|string|max:255',
            'date'      => 'required|date|before:today',
            'gender'    => 'required|in:Nam,Nữ,Khác',
            'gmail'     => 'required|email|unique:users,gmail',
            'password'  => 'required|string|min:6',
            'id_role'   => 'required|exists:roles,id',
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
            'name.required'     => 'Tên không được để trống',
            'date.required'     => 'Ngày sinh bắt buộc',
            'date.before'       => 'Ngày sinh phải nhỏ hơn ngày hiện tại',
            'gender.required'   => 'Giới tính bắt buộc',
            'gender.in'         => 'Giới tính không hợp lệ',
            'gmail.required'    => 'Email không được để trống',
            'gmail.email'       => 'Email không đúng định dạng',
            'gmail.unique'      => 'Email đã tồn tại',
            'password.required' => 'Mật khẩu bắt buộc',
            'password.min'      => 'Mật khẩu ít nhất 6 ký tự',
            'id_role.required'  => 'Vai trò bắt buộc',
            'id_role.exists'    => 'Vai trò không tồn tại',
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
