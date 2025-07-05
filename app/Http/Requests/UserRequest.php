<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['nullable', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'  => ['required', 'string', 'min:8', 'confirmed', 'regex:/^[a-zA-Z0-9]+$/'],
        ];
    }
    public function messages(): array
    {
        return [
            'email.required'        => 'Vui lòng nhập email.',
            'email.unique'          => 'Email đã tồn tại.',
            'password.required'     => 'Vui lòng nhập mậy khẩu.',
            'password.min'          => 'Mật khẩu phải có í nhất 8kis tự.',
            'password.regex'        => 'Mật khẩu không đước có các kí tự đặc biệt.',
            'password.confirmed'    => 'Mật khẩu không khớp nhau.',
        ];
    }
}
