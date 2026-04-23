<?php

namespace App\Http\Requests\ExaminationResult;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Carbon\Carbon;

class StoreExaminationResultRequest extends FormRequest
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
            'diagnosis' => 'required|string|max:2000',
            'examination_time' => 'nullable|date'
        ];
    }
    /**
     * Custom error messages for validation rules.
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'diagnosis.required' => 'Chẩn đoán không được bỏ trống',
            'diagnosis.string'   => 'Chẩn đoán phải là chuỗi ký tự',
            'diagnosis.max'      => 'Chẩn đoán không được vượt quá 2000 ký tự'
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

    protected function prepareForValidation()
{
    if (!$this->filled('examination_time')) {
        $this->merge([
            'examination_time' => Carbon::now(),
        ]);
    }
}
}
