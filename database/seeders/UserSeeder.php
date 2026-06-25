<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed default portal users.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@datadesa.test',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Operator',
                'email' => 'operator@datadesa.test',
                'role' => 'operator',
            ],
            [
                'name' => 'Viewer',
                'email' => 'viewer@datadesa.test',
                'role' => 'viewer',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate([
                'email' => $user['email'],
            ], [
                'name' => $user['name'],
                'password' => env('DEFAULT_ADMIN_PASSWORD', 'GantiSaya!2026'),
                'role' => $user['role'],
            ]);
        }
    }
}
