<?php

namespace App\Http\Requests;

use App\DataTransferObjects\MediaFolderData;
use App\Helpers\MediaFolderHelper;
use App\Models\MediaFolder;
use App\Traits\HasDto;
use Illuminate\Foundation\Http\FormRequest;

class MediaFolderRequest extends FormRequest
{
    use HasDto;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'folder_id' => ['nullable', 'exists:media_folders,id'],
            'breadcrumb_path' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\pN\s_\-\/]+$/u'],
            'folder_name' => ['nullable', 'required_without:breadcrumb_path', 'string', 'max:255', 'regex:/^[\pL\pN\s_\-]+$/u'],
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

    public function toUpdateDto(MediaFolder $folder): MediaFolderData
    {
        return MediaFolderData::fromExisting(
            folder: $folder,
            newName: $this->folderName(),
            newParentId: $this->validatedParentId($this->user()->id),
        );
    }
}
