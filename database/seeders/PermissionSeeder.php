<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'evaluator', 'user'];
        $modules = ['dashboard', 'evaluation_list', 'evaluation_create', 'audit_trail', 'system_settings', 'evaluation_request'];

        // Default Permissions
        $defaults = [
            'admin' => ['dashboard', 'evaluation_list', 'evaluation_create', 'evaluation_request'],
            'evaluator' => ['evaluation_list', 'evaluation_request'],
            'user' => ['evaluation_request'],
        ];

        foreach ($roles as $role) {
            foreach ($modules as $module) {
                \App\Models\Permission::updateOrCreate(
                    ['role' => $role, 'module' => $module],
                    ['is_allowed' => in_array($module, $defaults[$role])]
                );
            }
        }
    }
}
