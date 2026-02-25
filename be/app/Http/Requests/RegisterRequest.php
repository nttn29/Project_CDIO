<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|unique:nguoi_dung,email',
            'ten' => 'required|string|max:255',
            'mat_khau' => 'required|string|min:6|confirmed',
            'dien_thoai' => 'nullable|string|max:20',
            'id_can_ho' => 'required|exists:can_ho,id_can_ho',
            'vai_tro' => 'nullable|in:cu_dan,nhan_vien,quan_ly,admin',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required',
            'email.unique' => 'Email already registered',
            'ten.required' => 'Name is required',
            'mat_khau.required' => 'Password is required',
            'mat_khau.min' => 'Password must be at least 6 characters',
            'mat_khau.confirmed' => 'Password confirmation does not match',
            'id_can_ho.required' => 'Apartment is required',
            'id_can_ho.exists' => 'Apartment does not exist',
        ];
    }
}
