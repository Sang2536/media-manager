<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaMetadataRequest extends FormRequest
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
            'media_file_id' => ['required', 'exists:media_files,id'],
            'metadata.*.key'           => ['required', 'string', 'max:255'],
            'metadata.*.value'         => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'media_file_id.required'    => 'Bạn chưa chọn File.',
            'media_file_id.exists'      => 'File không còn tồn tại.',
            'metadata.*.key'            => 'Vui lòng nhập key',
            'metadata.*.value'             => 'Vui lòng nhập value',
        ];
    }
}
