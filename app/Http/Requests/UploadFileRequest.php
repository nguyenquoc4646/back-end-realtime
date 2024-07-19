<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
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
            'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // đuôi file hợp lệ, và giới hạn dung lượng file
        ];
    }
    public function messages(): array
    {
        return [
            'avatar.image' => 'Tệp tải lên phải là một hình ảnh.',
            'avatar.mimes' => 'Chỉ chấp nhận hình ảnh định dạng JPEG, PNG, JPG, hoặc GIF.',
            'avatar.max' => 'Kích thước tệp ảnh tối đa là 2MB.',
        ];
    }
}
