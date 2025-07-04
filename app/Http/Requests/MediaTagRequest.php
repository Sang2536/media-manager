<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagId = $this->route('tag')?->id;

        $rules = [];

        if ($this->isMethod('post')) {
            $rules['names'] = ['required', 'string'];
        } else {
            $rules['name'] = [
                'required',
                'max:255',
                Rule::unique('media_tags', 'name')->ignore($tagId),
            ];
        }

        $rules['color'] = ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'];

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'names' => 'Tên các thẻ',
            'name'  => 'Tên thẻ',
            'color' => 'Màu sắc',
        ];
    }

    public function messages(): array
    {
        return [
            'names.required' => 'Vui lòng nhập ít nhất một tên tag.',
            'name.required'  => 'Vui lòng nhập tên thẻ.',
            'name.unique'    => 'Tên thẻ đã tồn tại.',
            'color.regex'    => 'Màu sắc phải đúng định dạng mã HEX (vd: #ff0000).',
        ];
    }
}
