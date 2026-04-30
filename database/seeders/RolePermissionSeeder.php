<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Pulisci cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crea permessi base
        $permissions = [
            // User management
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Profile
            'profile.view',
            'profile.edit',

            // Media
            'media.view',
            'media.upload',
            'media.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Crea ruoli
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Assegna tutti i permessi all'admin
        $adminRole->givePermissionTo(Permission::all());

        // Assegna permessi base all'editor
        $editorRole->givePermissionTo([
            'profile.view',
            'profile.edit',
            'media.view',
            'media.upload',
        ]);

        // Assegna permessi base all'user
        $userRole->givePermissionTo([
            'profile.view',
            'profile.edit',
        ]);

        // Migra utente admin esistente
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            // Rimuovi vecchio ruolo se presente
            $admin->removeRole($admin->role ?? null);
            // Assegna ruolo admin
            $admin->assignRole('admin');
            $this->command->info('Utente admin migrato al nuovo sistema ruoli');
        }

        $this->command->info('Ruoli e permessi creati con successo');
    }
}