<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
        $rules = [
            'name_group' => 'required',
            'leader_id' => 'required',
            'members' => 'required|array',
            'members.*' => 'required|distinct', // Đảm bảo các thành viên là một mảng và không có giá trị trùng lặp
        ];
        return $rules;


       
    }
    public function messages()
    {
        return [
            'name_group.required' => 'Tên nhóm là bắt buộc.',
            'leader_id.required' => 'Nhóm trưởng là bắt buộc.',
            'members.required' => 'Danh sách thành viên là bắt buộc.',
            'members.array' => 'Danh sách thành viên phải là một mảng.',
            'members.*.required' => 'Mỗi thành viên trong danh sách là bắt buộc.',
            'members.*.distinct' => 'Danh sách thành viên không được có giá trị trùng lặp.',
        ];
    }
}
