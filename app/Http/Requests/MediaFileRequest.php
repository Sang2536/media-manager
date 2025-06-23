<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép mọi người gửi request, sửa tùy quyền
    }

    public function rules(): array
    {
        return [
            'file'      => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,gif,mp4',
            'file_name'      => 'nullable|string|max:255',
            'folder_id' => 'nullable|exists:media_folders,id',

            'tags'      => 'nullable|array',
            'tags.*'    => 'integer|exists:media_tags,id',

            'metadata'  => 'nullable|array',
            'metadata.*' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'file.max' => 'Tệp không được vượt quá 10MB.',
            'file.mimes' => 'Định dạng tệp không hợp lệ.',
            'name.required' => 'Vui lòng nhập tên media.',
        ];
    }
}
