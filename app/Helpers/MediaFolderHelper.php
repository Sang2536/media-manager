<?php

namespace App\Helpers;

use App\Models\MediaFolder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

class MediaFolderHelper
{
    //  kiểm tra quyền user
    public static function isOwnedByUser(MediaFolder $folder, int $userId): bool
    {
        return $folder->user_id === $userId;
    }

    //  bộ lọc cho folder
    public static function filterQuery(?int $parentId = null, array $filters = []): Builder
    {
        $query = MediaFolder::query();

        // Lọc theo user (nếu đăng nhập)
        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        }

        // Lọc theo thư mục cha
        if (!is_null($parentId)) {
            $query->where('parent_id', $parentId);
        } else {
            $query->whereNull('parent_id');
        }

        // Lọc theo tên
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . trim($filters['name']) . '%');
        }

        // Lọc theo storage
        if (!empty($filters['storage'])) {
            $query->where('storage', $filters['storage']);
        }

        // Sắp xếp
        $sort = $filters['sort'] ?? 'created_at';
        $order = $filters['order'] ?? 'desc';

        return $query->orderBy($sort, $order);
    }

    //  lấy folder
    public static function getFoldersByParent(?int $parentId = null, array $filters = []): LengthAwarePaginator
    {
        return self::filterQuery($parentId, $filters)
            ->paginate(12)
            ->appends($filters); // Giữ lại query string khi phân trang
    }

    //  lấy root folder
    public static function getRootFolder(int $userId): ?MediaFolder
    {
        return MediaFolder::where('user_id', $userId)
            ->whereNull('parent_id')
            ->first();
    }

    //  kiểm tra folder có phải là hậu duệ của root folder
    public static function isDescendantOf(int $folderId, int $rootId): bool
    {
        while ($folderId) {
            if ($folderId === $rootId) return true;

            $folder = MediaFolder::find($folderId);
            $folderId = $folder?->parent_id;
        }

        return false;
    }

    //  kiểm tra folder đã tồn tại trong cùng cấp (trừ chính nó)
    public static function folderExists(string $name, int $userId, ?int $parentId = null): bool
    {
        return MediaFolder::where('user_id', $userId)
            ->where('name', $name)
            ->where('parent_id', $parentId)
            ->exists();
    }

    //  xây dựng path cho folder
    protected static function buildPath(string $name, ?int $parentId = null): string
    {
        $name = str()->slug($name);

        if (! $parentId) {
            return $name;
        }

        $parent = MediaFolder::find($parentId);

        return $parent ? $parent->path . '/' . $name : $name;
    }

    //  lưu 1 folder mới
    public static function saveSingle(string $name, int $userId, ?int $parentId = null, MediaFolder $existingFolder = null): MediaFolder
    {
        // Kiểm tra trùng tên trong cùng cấp (trừ chính nó)
        $query = MediaFolder::where('user_id', $userId)
            ->where('name', $name)
            ->where('parent_id', $parentId);

        if ($existingFolder) {
            $query->where('id', '!=', $existingFolder->id);
        }

        if ($query->exists()) {
            throw new \Exception("Thư mục '$name' đã tồn tại trong cùng cấp.");
        }

        // Build path mới
        $newPath = self::buildPath($name, $parentId);

        if ($existingFolder) {
            $existingFolder->update([
                'name'      => $name,
                'parent_id' => $parentId,
                'path'      => $newPath,
            ]);
            return $existingFolder;
        }

        return MediaFolder::create([
            'user_id'   => $userId,
            'name'      => $name,
            'parent_id' => $parentId,
            'path'      => $newPath,
            'storage'   => 'local',
        ]);
    }

    //  lưu folder theo chuỗi breadcrumb
    public static function saveFromBreadcrumb(string $breadcrumb, int $userId, ?int $baseParentId = null, ?MediaFolder $existingFolder = null): MediaFolder
    {
        $folders = array_filter(array_map('trim', explode('/', $breadcrumb)));
        $parentId = $baseParentId;
        $finalFolder = null;
        $allExisted = true;

        foreach ($folders as $index => $folderName) {
            $isLast = $index === array_key_last($folders);

            if ($isLast && $existingFolder) {
                // ✅ Trường hợp cập nhật folder cuối cùng (rename/move)
                $finalFolder = self::updateFolderInfo($existingFolder, $folderName, $parentId);
                $allExisted = false; // đang update, nên không gọi là "existed toàn bộ"
            } elseif (self::folderExists($folderName, $userId, $parentId)) {
                // Lấy lại folder đã có
                $finalFolder = MediaFolder::where([
                    'user_id'   => $userId,
                    'name'      => $folderName,
                    'parent_id' => $parentId,
                ])->first();
            } else {
                // ✅ Tạo mới folder (chỉ khi không tồn tại và không phải đang update)
                $finalFolder = self::saveSingle($folderName, $userId, $parentId);
                $allExisted = false;
            }

            // Chuẩn bị parent cho cấp tiếp theo
            $parentId = $finalFolder->id;
        }

        // Nếu toàn bộ folder đã tồn tại và không phải đang update → lỗi trùng
        if ($allExisted && !$existingFolder) {
            throw new \Exception("Cây thư mục '$breadcrumb' đã tồn tại.");
        }

        return $finalFolder;
    }

    //  di chuyển folder đã có và cập nhật lại các folder con
    public static function moveFolderAndUpdatePaths(int $folderId, ?int $newParentId = null, ?string $newName = null): MediaFolder
    {
        $folder = MediaFolder::findOrFail($folderId);

        return self::updateFolderInfo(
            $folder,
            $newName ?? $folder->name,
            $newParentId ?? $folder->parent_id
        );
    }

    //  cập nhật path của toàn bộ folder con theo fplder cha mới
    public static function rebuildPathRecursive(MediaFolder $folder): void
    {
        // Cập nhật path mới của folder hiện tại
        $folder->path = self::buildPath($folder->name, $folder->parent_id);
        $folder->save();

        // Cập nhật path cho các folder con
        $children = MediaFolder::where('parent_id', $folder->id)->get();

        foreach ($children as $child) {
            self::rebuildPathRecursive($child);
        }
    }

    //  kiểm tra và cập nhật thông tin folder đã có
    public static function updateFolderInfo(MediaFolder $folder, string $newName, ?int $newParentId = null): MediaFolder
    {
        $userId = $folder->user_id;
        $oldParentId = $folder->parent_id;
        $oldName = $folder->name;

        $parentId = $newParentId ?? $oldParentId;

        // Không cho move vào thư mục con của chính nó
        if (self::isDescendantOf($parentId, $folder->id)) {
            throw new \Exception("Không thể di chuyển thư mục vào chính nó hoặc thư mục con của nó.");
        }

        // Kiểm tra trùng tên trong cùng cấp (loại trừ chính nó)
        $exists = MediaFolder::where('user_id', $userId)
            ->where('name', $newName)
            ->where('parent_id', $parentId)
            ->where('id', '!=', $folder->id)
            ->exists();

        if ($exists) {
            throw new \Exception("Thư mục '$newName' đã tồn tại trong cùng cấp.");
        }

        // Cập nhật tên, parent_id, path
        $folder->update([
            'name'      => $newName,
            'parent_id' => $parentId,
            'path'      => self::buildPath($newName, $parentId),
        ]);

        // Cập nhật path của tất cả thư mục con
        self::rebuildPathRecursive($folder);

        return $folder;
    }

    //  xây dựng breadcrumb hiển thị ra view
    public static function buildBreadcrumb(int|MediaFolder|null $folder, bool $asString = false): string|array
    {
        if (is_null($folder)) {
            return $asString ? '' : [];
        }

        // Nếu truyền vào là ID, tìm folder
        if (is_int($folder)) {
            $folder = MediaFolder::find($folder);
        }

        $items = [];

        while ($folder) {
            $items[] = $folder;
            $folder = $folder->parent;
        }

        $items = array_reverse($items);

        if ($asString) {
            return implode('/', array_map(fn($f) => $f->name, $items));
        }

        return $items; // array<MediaFolder>
    }

    //  render html => form.select
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

    //  render html => thành phần option thêm vào form.select
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
