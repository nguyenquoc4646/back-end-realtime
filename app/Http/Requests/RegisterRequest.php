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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên là trường bắt buộc.',
            'name.string' => 'Tên phải là chuỗi.',
            'name.max' => 'Tên không được vượt quá :max ký tự.',
            'email.required' => 'Email là trường bắt buộc.',
            'email.string' => 'Email phải là chuỗi.',
            'email.email' => 'Email không hợp lệ.',
            'email.max' => 'Email không được vượt quá :max ký tự.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.required' => 'Mật khẩu là trường bắt buộc.',
            'confirm_password.required' => 'Xác nhận mật khẩu là trường bắt buộc.',
            'confirm_password.same' => 'Xác nhận mật khẩu phải giống với mật khẩu đã nhập.',
        ];
    }
}
