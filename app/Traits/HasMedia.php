<?php

namespace App\Traits;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;

trait HasMedia
{
    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function primaryMedia($collection = null)
    {
        $query = $this->media()->where('is_primary', true);
        
        if ($collection) {
            $query->where('collection', $collection);
        }
        
        return $query->first();
    }

    public function getMediaByCollection($collection)
    {
        return $this->media()->where('collection', $collection)->get();
    }

    public function addMedia(UploadedFile $file, $collection = 'default', $isPrimary = false)
    {
        // Validazione tipi permessi
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        $mime = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();
        
        if (!in_array($mime, $allowedMimes) || !in_array(strtolower($extension), $allowedExtensions)) {
            throw new \InvalidArgumentException(
                'File non valido. Sono accettate solo immagini (JPG, PNG, GIF, WEBP).'
            );
        }

        $modelType = class_basename($this);
        $modelId = $this->id;
        
        // Genera nome file sicuro
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = time() . '_' . Str::slug($originalName) . '.' . $extension;
        
        // Costruisci percorso
        $folderPath = strtolower($modelType) . '/' . $modelId . '/' . $collection;
        $fullFolderPath = storage_path('app/public/' . $folderPath);
        
        // Crea cartella se non esiste
        if (!file_exists($fullFolderPath)) {
            mkdir($fullFolderPath, 0755, true);
        }
        
        $fullPath = $fullFolderPath . '/' . $safeName;
        
        // Ottimizzazione intelligente con Intervention
        try {
            $image = Image::read($file->getRealPath());
            
            switch ($collection) {
                case 'avatar':
                    $image->cover(300, 300);
                    $quality = 70;
                    break;
                    
                case 'post':
                case 'content':
                    $image->scaleDown(width: 1200);
                    $quality = 85;
                    break;
                    
                case 'gallery':
                    $image->scaleDown(width: 800);
                    $quality = 80;
                    break;
                    
                default:
                    $image->scaleDown(width: 1000);
                    $quality = 80;
                    break;
            }
            
            if (method_exists($image, 'height') && $image->height() > 1200) {
                $image->scaleDown(height: 1200);
            }
            
            $image->save($fullPath, quality: $quality);
            $fileSize = filesize($fullPath);
            
        } catch (\Exception $e) {
            // Se non è un'immagine o errore, salva originale
            $file->move($fullFolderPath, $safeName);
            $fileSize = $file->getSize();
            Log::warning('Intervention failed: ' . $e->getMessage());
        }
        
        // Percorso relativo per il database
        $relativePath = $folderPath . '/' . $safeName;
        
        // Se è primary, rimuovi primary dagli altri della stessa collection
        if ($isPrimary) {
            $this->media()
                 ->where('collection', $collection)
                 ->update(['is_primary' => false]);
        }
        
        // Salva nel database
        return $this->media()->create([
            'collection' => $collection,
            'disk' => 'public',
            'path' => $relativePath,
            'filename' => $safeName,
            'mime_type' => $file->getMimeType(),
            'size' => $fileSize,
            'is_primary' => $isPrimary,
        ]);
    }

    public function deleteMedia(Media $media)
    {
        // 1. Cancella il file fisico
        Storage::disk($media->disk)->delete($media->path);
        
        // 2. Ottieni il percorso della cartella
        $folderPath = dirname($media->path);
        
        // 3. Cancella il record dal database
        $result = $media->delete();
        
        // 4. Verifica se la cartella è vuota e cancellala
        if ($result) {
            $this->cleanupEmptyDirectory($folderPath, $media->disk);
        }
        
        return $result;
    }

    protected function cleanupEmptyDirectory($path, $disk = 'public')
    {
        $storage = Storage::disk($disk);
        
        if (!$storage->exists($path)) {
            return;
        }
        
        $files = $storage->allFiles($path);
        
        if (count($files) === 0) {
            $storage->deleteDirectory($path);
            
            $parentPath = dirname($path);
            if ($parentPath !== '.' && $parentPath !== '/' && $parentPath !== '\\') {
                $this->cleanupEmptyDirectory($parentPath, $disk);
            }
        }
    }
}