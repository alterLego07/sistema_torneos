<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // Limpia cache de permisos (CLAVE, si no, a veces "no aparecen")
            app(PermissionRegistrar::class)->forgetCachedPermissions();

            // Roles
            $roles = [
                'admin',
                'organizer',  // organiza torneos
                'referee',    // carga resultados
                'viewer',     // solo lectura
            ];

            foreach ($roles as $r) {
                Role::firstOrCreate(['name' => $r]);
            }

            // Permisos (arrancamos con un set base y escalable)
            $permissions = [
                // Tournaments
                'tournaments.view',
                'tournaments.create',
                'tournaments.update',
                'tournaments.delete',

                // Disciplines / Categories
                'disciplines.manage',
                'categories.manage',

                // Registration
                'players.manage',
                'participants.manage',
                'registrations.manage',

                // Matches
                'matches.view',
                'matches.schedule',
                'matches.update',
                'results.enter',

                // Standings / Brackets
                'standings.view',
                'brackets.view',

                // Users/Roles (admin)
                'users.manage',
                'roles.manage',
            ];

            foreach ($permissions as $p) {
                Permission::firstOrCreate(['name' => $p]);
            }

            // Asignar permisos a roles
            $admin = Role::where('name', 'admin')->firstOrFail();
            $organizer = Role::where('name', 'organizer')->firstOrFail();
            $referee = Role::where('name', 'referee')->firstOrFail();
            $viewer = Role::where('name', 'viewer')->firstOrFail();

            // admin: todo
            $admin->syncPermissions(Permission::all());

            // organizer: gestiona el torneo completo (menos users/roles)
            $organizer->syncPermissions([
                'tournaments.view',
                'tournaments.create',
                'tournaments.update',
                'tournaments.delete',
                'disciplines.manage',
                'categories.manage',
                'players.manage',
                'participants.manage',
                'registrations.manage',
                'matches.view',
                'matches.schedule',
                'matches.update',
                'standings.view',
                'brackets.view',
            ]);

            // referee: ve partidos y carga resultados
            $referee->syncPermissions([
                'matches.view',
                'results.enter',
                'standings.view',
                'brackets.view',
            ]);

            // viewer: solo lectura
            $viewer->syncPermissions([
                'tournaments.view',
                'matches.view',
                'standings.view',
                'brackets.view',
            ]);

            // Crear/actualizar usuario admin
            $user = User::updateOrCreate(
                ['email' => 'admin@chacomer.test'],
                [
                    'name' => 'Admin Chacomer',
                    'password' => Hash::make('Admin12345!'),
                ]
            );

            // Importante: si el user ya tenía roles, lo reseteamos y asignamos
            $user->syncRoles(['admin']);
        });
    }
}
