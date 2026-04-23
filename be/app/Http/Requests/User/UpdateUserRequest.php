<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Check if the request is authorized.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Allow all requests (can add role/permission checks later)
    }

    /**
     * Validation rules for updating a user.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        // Get user ID from route for unique email check
        $userId = $this->route('user');

        return [
            'name'     => 'sometimes|string|max:255',
            'gender'   => 'sometimes|in:Nam,Nữ,Khác',
            'date'     => 'sometimes|date|before:today',
            'gmail'    => "sometimes|email|unique:users,gmail,$userId",
            'password' => 'nullable|string|min:6',
            'id_role'  => 'sometimes|exists:roles,id',
            'status'   => 'sometimes|integer|in:0,1',
        ];
    }
}
