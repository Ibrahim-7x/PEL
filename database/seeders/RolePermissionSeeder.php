<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            // other domain permissions:
            'ticket.view',
            'ticket.respond',
            'report.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $management = Role::firstOrCreate(['name' => 'Management']);
        $agent = Role::firstOrCreate(['name' => 'Agent']);

        $management->syncPermissions(Permission::all()); // admin gets everything

        $manager->syncPermissions([
            'user.view', 'user.edit', 'ticket.view', 'report.view'
        ]);

        $agent->syncPermissions([
            'ticket.view', 'ticket.respond'
        ]);
    }
}
