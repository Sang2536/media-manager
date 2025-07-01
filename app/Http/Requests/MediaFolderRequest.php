<?php

namespace App\Http\Requests;

use App\DataTransferObjects\MediaFolderData;
use App\Helpers\MediaFolderHelper;
use App\Models\MediaFolder;
use App\Traits\HasDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaFolderRequest extends FormRequest
{
    use HasDto;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_shared' => $this->boolean('is_shared'),
            'is_locked' => $this->boolean('is_locked'),
            'is_favorite' => $this->boolean('is_favorite'),
        ]);
    }

    public function rules(): array
    {
        return [
            'active_tab' => ['string', 'in:select,breadcrumb,area'],
            'folder_id' => ['nullable', 'exists:media_folders,id'],
            'breadcrumb_path' => [
                Rule::requiredIf(fn() => $this->input('active_tab') === 'breadcrumb'),
                'nullable', 'string', 'max:255', 'regex:/^[\pL\pN\s_\-\/]+$/u'
            ],
            'folder_name' => [
                Rule::requiredIf(fn() => $this->input('active_tab') === 'select'),
                'nullable', 'string', 'max:255', 'regex:/^[\pL\pN\s_\-]+$/u'
            ],
            'slug' => ['nullable', 'string'],
            'path' => ['nullable', 'string'],
            'depth' => ['nullable', 'integer'],
            'storage' => ['nullable', 'string'],
            'kind' => ['nullable', 'string'],
            'folder_type' => ['nullable', 'string'],
            'is_locked' => ['sometimes', 'boolean'],
            'is_shared' => ['sometimes', 'boolean'],
            'is_favorite' => ['sometimes', 'boolean'],
            'thumbnail' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'last_opened_at' => ['nullable', 'date'],
        ];
    }

    public function folderName(): string
    {
        $data = $this->validated();
        return $data['breadcrumb_path'] ?? $data['folder_name'];
    }

    public function validatedParentId(int $userId): int
    {
        $rootFolder = MediaFolderHelper::getRootFolder($userId);

        if (! $rootFolder) {
            throw new \Exception('Không tìm thấy thư mục gốc của người dùng.');
        }

        $rootId = $rootFolder->id;
        $inputParentId = (int) $this->input('folder_id');

        return MediaFolderHelper::isDescendantOf($inputParentId, $rootId)
            ? $inputParentId
            : $rootId;
    }

    public function toDto(int $userId): MediaFolderData
    {
        return MediaFolderData::fromBasic(
            name: $this->folderName(),
            userId: $userId,
            parentId: $this->validatedParentId($userId)
        );
    }

    public function toUpdateDto(MediaFolder $folder, int $userId): MediaFolderData
    {
        return MediaFolderData::fromExisting(
            folder: $folder,
            newName: $this->folderName(),
            newParentId: $this->input('folder_id') ? $this->validatedParentId($userId) : $folder->parent_id,
            extra: [
                'is_locked' => $this->boolean('is_locked'),
                'is_favorite' => $this->boolean('is_favorite'),
                'is_shared' => $this->boolean('is_shared'),
                'comments' => $this->input('comments'),
                'thumbnail' => $this->input('thumbnail'),
                'permissions' => $this->input('permissions'),
                'last_opened_at' => $this->input('last_opened_at'),
            ]
        );
    }

}
