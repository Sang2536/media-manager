<?php

namespace App\Helpers;

use App\DataTransferObjects\MediaFolderData;
use App\Models\MediaFolder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MediaFolderHelper
{
    /**
     * ===== Basic Utilities =====
     */

    public static function isOwnedByUser(MediaFolder $folder, int $userId): bool
    {
        return $folder->user_id === $userId;
    }

    public static function folderExists(string $name, int $userId, ?int $parentId = null): bool
    {
        return MediaFolder::where('user_id', $userId)
            ->where('name', $name)
            ->where('parent_id', $parentId)
            ->exists();
    }

    public static function isDescendantOf(?int $possibleDescendantId, int $folderId): bool
    {
        if (!$possibleDescendantId) return false;

        $visited = [];

        while ($possibleDescendantId !== null) {
            if (in_array($possibleDescendantId, $visited)) break;
            if ($possibleDescendantId === $folderId) return true;

            $visited[] = $possibleDescendantId;

            $folder = MediaFolder::find($possibleDescendantId);
            if (!$folder) break;

            $possibleDescendantId = $folder->parent_id;
        }

        return false;
    }

    public static function getRootFolder(int $userId): ?MediaFolder
    {
        return MediaFolder::where('user_id', $userId)
            ->whereNull('parent_id')
            ->first();
    }

    /**
     * ===== Data conversion =====
     */

    public static function convertName(string $foldername): string {
        return preg_replace('/[^a-zA-Z0-9\-_ ]+/', '', $foldername);
    }

    public static function convertPath(string $foldername): string {
        return str()->slug($foldername);
    }

    public static function formatBytes(int|float $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        if ($bytes <= 0) return '0 B';

        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);

        $formatted = $bytes / (1024 ** $pow);

        return round($formatted, $precision) . ' ' . $units[$pow];
    }


    /**
     * ===== Folder Fetching / Filtering =====
     */

    protected static function filterQuery(?int $parentId = null, array $filters = []): Builder
    {
        $query = MediaFolder::query();

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        }

        if (!is_null($parentId)) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . trim($filters['name']) . '%');
        }

        if (!empty($filters['storage'])) {
            $query->where('storage', $filters['storage']);
        }

        $sort = $filters['sort'] ?? 'created_at';
        $order = $filters['order'] ?? 'desc';

        return $query->orderBy($sort, $order);
    }

    public static function getFoldersByParent(?int $parentId = null, array $filters = []): LengthAwarePaginator
    {
        return self::filterQuery($parentId, $filters)
            ->paginate(12)
            ->appends($filters);
    }

    /**
     * ===== Folder Manipulation =====
     */

    public static function saveSingle(MediaFolderData $dto, ?MediaFolder $existingFolder = null): MediaFolder
    {
        if ($existingFolder) {
            return self::updateFolderInfo($existingFolder, $dto);
        }

        $exists = self::folderExists($dto->name, $dto->userId, $dto->parentId);
        if ($exists) {
            throw new \Exception("Thư mục '{$dto->name}' đã tồn tại trong cùng cấp.");
        }

        $data = $dto->toArray();
        $data['path'] = self::buildPath($dto->name, $dto->parentId);
        $data['depth'] = substr_count($data['path'], '/');

        return MediaFolder::create($data);
    }

    public static function renameFolder(MediaFolder $folder, string $newName): MediaFolder
    {
        $dto = MediaFolderData::fromExisting($folder, $newName, $folder->parent_id);
        return self::saveSingle($dto, $folder);
    }

    public static function moveFolder(MediaFolder $folder, int $newParentId): MediaFolder
    {
        if (self::isDescendantOf($newParentId, $folder->id) || $folder->id === $newParentId) {
            throw new \Exception("Không thể di chuyển thư mục vào chính nó hoặc thư mục con của nó.");
        }

        $dto = MediaFolderData::fromExisting($folder, $folder->name, $newParentId);

        $isDuplicate = MediaFolder::where('user_id', $dto->userId)
            ->where('parent_id', $dto->parentId)
            ->where('name', $dto->name)
            ->where('id', '!=', $folder->id)
            ->exists();

        if ($isDuplicate) {
            throw new \Exception("Thư mục '{$dto->name}' đã tồn tại trong thư mục đích.");
        }

        return self::saveSingle($dto, $folder);
    }

    public static function renameAndMoveFolder(MediaFolder $folder, string $newName, int $newParentId): MediaFolder
    {
        if (self::convertName($folder->name) === self::convertName($newName) && $folder->parent_id === $newParentId) {
            throw new \Exception("Không có thay đổi nào để thực hiện. Vui lòng thay đổi tên hoặc thư mục cha.");
        }

        if (self::isDescendantOf($newParentId, $folder->id) || $folder->id === $newParentId) {
            throw new \Exception("Không thể di chuyển thư mục vào chính nó hoặc thư mục con của nó.");
        }

        $dto = MediaFolderData::fromExisting($folder, $newName, $newParentId);

        $isDuplicate = MediaFolder::where('user_id', $dto->userId)
            ->where('parent_id', $dto->parentId)
            ->where('name', $dto->name)
            ->where('id', '!=', $folder->id)
            ->exists();

        if ($isDuplicate) {
            throw new \Exception("Thư mục '{$dto->name}' đã tồn tại trong thư mục đích.");
        }

        return self::saveSingle($dto, $folder);
    }

    protected static function updateFolderInfo(MediaFolder $folder, MediaFolderData $dto): MediaFolder
    {
        $userId = $dto->userId;
        $parentId = $dto->parentId;

        if (self::isDescendantOf($parentId, $folder->id)) {
            throw new \Exception("Không thể di chuyển thư mục vào chính nó hoặc thư mục con của nó.");
        }

        $exists = MediaFolder::where('user_id', $userId)
            ->where('name', $dto->name)
            ->where('parent_id', $parentId)
            ->where('id', '!=', $folder->id)
            ->exists();

        if ($exists) {
            throw new \Exception("Thư mục '{$dto->name}' đã tồn tại trong cùng cấp.");
        }

        $folder->fill($dto->toArray());
        $folder->path = self::buildPath($dto->name, $dto->parentId);
        $folder->save();

        self::rebuildPathRecursive($folder);

        return $folder;
    }

    /**
     * ===== Bulk Creation from Breadcrumb =====
     */
    public static function saveFromBreadcrumb(string $breadcrumb, int $userId, ?int $baseParentId = null, ?MediaFolder $existingFolder = null, string $action = 'add'): MediaFolder
    {
        $folders = array_filter(array_map('trim', explode('/', $breadcrumb)));
        $rootId = self::getRootFolder($userId)?->id;
        $parentId = $baseParentId ?? $rootId;

        // ===== Trường hợp đặc biệt: create breadcrumb / add breadcrumb
        if ($action === 'add') {
            $parentIdAdd = $existingFolder->id ?? $parentId;
            return self::createFromBreadcrumb($breadcrumb, $userId, $parentIdAdd);
        }

        // ===== Trường hợp đặc biệt: move mà breadcrumb rỗng hoặc chỉ là '/'
        if ($action === 'move' && (empty($folders) || (count($folders) === 1 && $folders[0] === ''))) {
            if (is_null($rootId)) {
                throw new \Exception("Không tìm thấy thư mục gốc.");
            }
            return self::moveFolder($existingFolder, $rootId);
        }

        // ===== Trường hợp đặc biệt: move mà breadcrumb không chứa '/'
        if (count($folders) === 1 && $action === 'move' && $existingFolder) {
            $targetName = self::convertName($folders[0]);

            $targetParent = MediaFolder::where('user_id', $userId)
                ->where('name', $targetName)
                ->where('depth', 1)
                ->first();

            if (! $targetParent) {
                throw new \Exception("Không tìm thấy thư mục đích để di chuyển.");
            }

            if ($targetParent->id === $existingFolder->parent->id) {
                throw new \Exception("Thư mục đang hiện tại nằm trong thư mục cần chuyển đến.");
            }

            return self::moveFolder($existingFolder, $targetParent->id);
        }

        // ===== Trường hợp đặc biệt: rename_move yêu cầu breadcrumb có ít nhất 2 phần tử
        if ($action === 'rename_move') {
            if (count($folders) < 2) {
                throw new \Exception("Breadcrumb phải có ít nhất 2 phần để thực hiện đổi tên và di chuyển.");
            }

            $newName = array_pop($folders); // Tên mới
            $parentId = $rootId;

            foreach ($folders as $folderName) {
                $parent = MediaFolder::where([
                    'user_id'   => $userId,
                    'name'      => $folderName,
                    'parent_id' => $parentId,
                ])->first();

                if (! $parent) {
                    throw new \Exception("Không tìm thấy thư mục '{$folderName}' trong chuỗi breadcrumb.");
                }

                $parentId = $parent->id;
            }

            return self::renameAndMoveFolder($existingFolder, $newName, $parentId);
        }

        // ===== Xử lý phần cha trong breadcrumb cho các action còn lại
        foreach (array_slice($folders, 0, -1) as $folderName) {
            $parent = MediaFolder::where([
                'user_id'   => $userId,
                'name'      => $folderName,
                'parent_id' => $parentId,
            ])->first();

            if (! $parent) {
                $dto = MediaFolderData::fromBasic($folderName, $userId, $parentId);
                $parent = self::saveSingle($dto);
            }

            $parentId = $parent->id;
        }

        $newName = end($folders);

        return match ($action) {
            'rename' => self::renameFolder($existingFolder, $newName),
            'move' => self::moveFolder($existingFolder, $parentId),
            default => throw new \InvalidArgumentException("Unknown action: $action"),
        };
    }
    public static function createFromBreadcrumb(string $breadcrumb, int $userId, ?int $baseParentId = null): MediaFolder
    {
        $folders = array_filter(array_map('trim', explode('/', $breadcrumb)));
        $parentId = $baseParentId ?? self::getRootFolder($userId)?->id;
        $finalFolder = null;
        $allExisted = true;

        foreach ($folders as $folderName) {
            $existing = MediaFolder::where([
                'user_id'   => $userId,
                'name'      => $folderName,
                'parent_id' => $parentId,
            ])->first();

            if ($existing) {
                $finalFolder = $existing;
            } else {
                $allExisted = false;
                $dto = MediaFolderData::fromBasic($folderName, $userId, $parentId);
                $finalFolder = self::saveSingle($dto);
            }

            $parentId = $finalFolder->id;
        }

        // Nếu toàn bộ đều đã tồn tại → lỗi trùng
        if ($allExisted) {
            throw new \Exception("Cây thư mục '$breadcrumb' đã tồn tại.");
        }

        return $finalFolder;
    }

    /**
     * ===== Internal Utilities =====
     */

    protected static function buildPath(string $name, ?int $parentId = null): string
    {
        $name = str()->slug($name);
        if (!$parentId) return $name;
        $parent = MediaFolder::find($parentId);
        return $parent ? $parent->path . '/' . $name : $name;
    }

    protected static function rebuildPathRecursive(MediaFolder $folder): void
    {
        $dto = MediaFolderData::fromExisting($folder, $folder->name, $folder->parent_id);
        $folder->fill($dto->toArray());
        $folder->path = self::buildPath($dto->name, $dto->parentId);
        $folder->save();

        foreach ($folder->children as $child) {
            self::rebuildPathRecursive($child);
        }
    }

    /**
     * ===== Folder Deletion =====
     */

    public static function deleteFolder($folder, $userId): JsonResponse|RedirectResponse
    {
        if (!self::isOwnedByUser($folder, $userId)) {
            return ResponseHelper::result(false, 'Bạn không có quyền xoá thư mục này.', 403);
        }

        if ($folder->is_locked) {
            return ResponseHelper::result(false, 'Thư mục này đang bị khóa và không thể xoá.', 400);
        }

        if ($folder->files()->exists()) {
            return ResponseHelper::result(false, 'Không thể xoá thư mục đang chứa file.', 400);
        }

        if ($folder->children()->exists()) {
            $hasLockedChild = $folder->children()->where('is_locked', true)->exists();
            if ($hasLockedChild) {
                return ResponseHelper::result(false, 'Không thể xoá thư mục vì có thư mục con bị khóa.', 400);
            }
            return ResponseHelper::result(false, 'Không thể xoá thư mục đang chứa thư mục con.', 400);
        }

        $folder->delete();
        return ResponseHelper::result(true, 'Đã xoá thư mục', 200, route('media-folders.index'));
    }

    /**
     * ===== View Utilities =====
     */

    public static function countAllDescendants(MediaFolder $folder): array
    {
        $totalFolders = 0;
        $totalFiles = 0;
        $totalSize = 0;

        $stack = [$folder];

        while (!empty($stack)) {
            /** @var MediaFolder $current */
            $current = array_pop($stack);

            // Đếm files trong folder hiện tại
            $files = $current->files()->select('size')->get();
            $totalFiles += $files->count();
            $totalSize += $files->sum('size');

            // Lấy và đếm thư mục con
            $children = $current->children()->get();
            $totalFolders += $children->count();

            foreach ($children as $child) {
                $stack[] = $child;
            }
        }

        return [
            'folders' => $totalFolders,
            'files'   => $totalFiles,
            'size'    => self::formatBytes($totalSize), // đơn vị byte
        ];
    }

    public static function buildBreadcrumb(int|MediaFolder|null $folder, bool $asString = false, string $separate = '/'): string|array
    {
        if (is_null($folder)) return $asString ? '' : [];
        if (is_int($folder)) $folder = MediaFolder::find($folder);

        $items = [];
        while ($folder) {
            $items[] = $folder;
            $folder = $folder->parent;
        }

        $items = array_reverse($items);
        return $asString ? implode($separate, array_map(fn($f) => $f->name, $items)) : $items;
    }

    public static function renderFolderOptions(?int $userId = null, ?int $selectedFolderId = null, string $mode = 'media_file'): string
    {
        $query = MediaFolder::query()->with('children');
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $rootFolders = $query->whereNull('parent_id')->get();

        $html = '<label class="block font-semibold mb-1">📂 Thư mục cha</label>';
        $html .= '<select name="folder_id" class="w-full border rounded px-3 py-2">';
        $html .= '<option value="">-- Không có --</option>';

        foreach ($rootFolders as $folder) {
            $html .= self::renderFolderOptionItem($folder, $selectedFolderId, $mode);
        }

        $html .= '</select>';
        return $html;
    }

    protected static function renderFolderOptionItem($folder, ?int $selectedId = null, string $mode = 'media_file', string $prefix = ''): string
    {
        $selected = $selectedId === $folder->id ? 'selected' : '';
        $html = '<option value="' . e($folder->id) . '" ' . $selected . '>' . $prefix . '📁 ' . e($folder->name) . '</option>';

        foreach ($folder->children as $child) {
            $html .= self::renderFolderOptionItem($child, $selectedId, $mode, $prefix . '— ');
        }

        return $html;
    }
}
