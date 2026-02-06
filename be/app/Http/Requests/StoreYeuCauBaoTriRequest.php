<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreYeuCauBaoTriRequest extends FormRequest
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
            'id_cu_dan' => 'required|exists:nguoi_dung,id_nguoi_dung',
            'id_can_ho' => 'required|exists:can_ho,id_can_ho',
            'id_loai_su_co' => 'required|exists:loai_su_co,id_loai_su_co',
            'mo_ta' => 'required|string|max:1000',
            'thoi_gian_uu_tien' => 'nullable|in:gan,binh_thuong,kho',
        ];
    }

    public function messages(): array
    {
        return [
            'id_cu_dan.required' => 'Resident ID is required',
            'id_can_ho.required' => 'Apartment ID is required',
            'id_loai_su_co.required' => 'Issue type is required',
            'mo_ta.required' => 'Description is required',
            'mo_ta.max' => 'Description must not exceed 1000 characters',
        ];
    }
}
