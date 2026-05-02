<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;  // <-- IMPORTANTE

class Media extends Model
{
    protected $fillable = [
        'model_id',
        'model_type',
        'collection',
        'disk',
        'path',
        'filename',
        'mime_type',
        'size',
        'width',
        'height',
        'order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'order' => 'integer',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return $this->getUrl();
    }

    public function getUrl(?string $conversion = null): string
    {
        $path = $this->path;

        if ($conversion === 'thumb') {
            $thumbPath = pathinfo($path, PATHINFO_DIRNAME) . '/thumb_' . pathinfo($path, PATHINFO_BASENAME);
            if (Storage::disk($this->disk)->exists($thumbPath)) {
                $path = $thumbPath;
            }
        }

        return $this->storageUrl($path);
    }

    protected function storageUrl(string $path): string
    {
        if (function_exists('request') && request()->instance()) {
            $basePath = rtrim(request()->getBasePath(), '/');
            return ($basePath ? $basePath : '') . '/storage/' . ltrim($path, '/');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk($this->disk);

        return $storage->url($path);
    }

    public function getFullPathAttribute(): string
    {
        return Storage::disk($this->disk)->path($this->path);
    }
}