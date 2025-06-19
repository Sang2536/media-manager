<?php

namespace App\Helpers;

use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\MediaTag;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaCrawlHelper
{
    public static function fetchDom(string $url): ?\DOMXPath
    {
        $html = @file_get_contents($url);
        if (!$html) return null;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        libxml_clear_errors();

        return new \DOMXPath($dom);
    }

    public static function crawlBreadcrumbToFolder(\DOMXPath $xpath, int $userId): MediaFolder
    {
        $nodes = $xpath->query('//div[contains(@class, "motgame-breadcrumb")]//a');

        switch (true) {
            case $nodes->length > 1:
                $folderName = trim($nodes[$nodes->length - 1]->nodeValue);
                break;

            case $nodes->length === 1:
                $folderName = trim($nodes[0]->nodeValue);
                break;

            default:
                $folderName = 'Không rõ';
                break;
        }

        $slug = Str::slug($folderName);

        $parentFolder = MediaFolder::where('user_id', $userId)
            ->where('parent_id', null)->first();

        $countFolder = MediaFolder::where('parent_id', $parentFolder->id)->count();

        // DB
        $folder = MediaFolder::firstOrCreate([
            'user_id' => $userId,
            'name' => $folderName . '-' . ($countFolder + 1),
            'path' => '/' . Str::slug($parentFolder->name),
            'parent_id' => $parentFolder ? $parentFolder->id : null,
        ]);

        // Storage
        Storage::disk('public')->makeDirectory("media/Crawl/{$slug}");

        return $folder;
    }

    public static function crawlTags(\DOMXPath $xpath): array
    {
        $tags = [];
        $nodes = $xpath->query('//div[contains(@class, "motgame-tag-content")]//a');
        foreach ($nodes as $node) {
            $name = trim($node->nodeValue);
            if (!$name) continue;
            $tag = MediaTag::firstOrCreate(['name' => $name]);
            $tags[] = $tag->id;
        }
        return $tags;
    }

    public static function crawlImagesAndSave(\DOMXPath $xpath, string $url, int $userId, MediaFolder $folder, array $tagIds): void
    {
        $nodes = $xpath->query('//div[contains(@class, "motgame-detail-grid")]//img');

        foreach ($nodes as $img) {
            $src = $img->getAttribute('src') ?: $img->getAttribute('data-src');
            if (!$src) continue;

            // Normalize URL
            if (str_starts_with($src, '//')) $src = 'https:' . $src;
            elseif (str_starts_with($src, '/')) {
                $parsed = parse_url($url);
                $src = "{$parsed['scheme']}://{$parsed['host']}{$src}";
            }

            try {
                $saved = self::downloadImage($src, Str::slug($folder->name));

                $mediaFile = MediaFile::create([
                    'user_id' => $userId,
                    'filename' => $saved['filename'],
                    'original_name' => $saved['original_name'],
                    'mime_type' => $saved['mime_type'],
                    'size' => $saved['size'],
                    'path' => $saved['path'],
                    'thumbnail_path' => null,
                    'source_url' => $url,
                    'media_folder_id' => $folder->id,
                    'is_public' => true,
                ]);

                echo "✅ Đã lưu: {$saved['path']}\n";

                $mediaFile->tags()->sync($tagIds);

            } catch (\Throwable $e) {
                logger()->error("❌ Lỗi lưu ảnh {$src}: {$e->getMessage()}");
            }
        }
    }

    protected static function downloadImage(string $url, string $folderSlug): array
    {
        $path = parse_url($url, PHP_URL_PATH); // bỏ query string
        $name = pathinfo($path, PATHINFO_FILENAME);
        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg'; // fallback nếu thiếu

        $slugName = Str::slug($name);
        $filename = "{$slugName}.{$ext}";

        $relativePath = "media/Crawl/{$folderSlug}/{$filename}";

        $content = file_get_contents($url);
        Storage::disk('public')->put($relativePath, $content);

        return [
            'filename' => $filename,
            'original_name' => basename($url),
            'mime_type' => self::guessMime($ext),
            'size' => strlen($content),
            'path' => $relativePath,
        ];
    }

    protected static function guessMime(string $ext): string
    {
        return match (strtolower($ext)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => 'application/octet-stream',
        };
    }
}
