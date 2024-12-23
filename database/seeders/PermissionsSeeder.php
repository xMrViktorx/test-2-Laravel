<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = ['user-management'];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $user = User::find(1); // Admin user
        $user->syncPermissions($permissions);
    }
}
