<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhanHoiRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id_yeu_cau' => 'required|exists:yeu_cau_bao_tri,id_yeu_cau',
            'id_cu_dan' => 'required|exists:nguoi_dung,id_nguoi_dung',
            'danh_gia' => 'required|integer|min:1|max:5',
            'binh_luan' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'id_yeu_cau.required' => 'Request ID is required',
            'id_cu_dan.required' => 'Resident ID is required',
            'danh_gia.required' => 'Rating is required',
            'danh_gia.min' => 'Rating must be at least 1',
            'danh_gia.max' => 'Rating must not exceed 5',
            'binh_luan.max' => 'Comment must not exceed 1000 characters',
        ];
    }
}
