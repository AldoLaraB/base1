<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasMedia, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Gestione automatica alla cancellazione
    protected static function booted()
    {
        static::deleting(function ($user) {
            // Cancella avatar (se esiste)
            $avatar = $user->primaryMedia('avatar');
            if ($avatar) {
                $user->deleteMedia($avatar);
            }

            // Cancella tutte le immagini della gallery personale
            $galleryImages = $user->getMediaByCollection('gallery');
            foreach ($galleryImages as $image) {
                $user->deleteMedia($image);
            }

            // NOTA: prodotti, post e altri contenuti NON vengono toccati
        });
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}