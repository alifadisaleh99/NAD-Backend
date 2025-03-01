<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions  = Permission::all()->pluck('name')->toArray();

        if(!in_array('users.read', $permissions))
            Permission::create(['name' => 'users.read']);
        if(!in_array('users.write', $permissions))
            Permission::create(['name' => 'users.write']);
        if(!in_array('users.delete', $permissions))
            Permission::create(['name' => 'users.delete']);

        if(!in_array('roles.read', $permissions))
            Permission::create(['name' => 'roles.read']);
        if(!in_array('roles.write', $permissions))
            Permission::create(['name' => 'roles.write']);
        if(!in_array('roles.delete', $permissions))
            Permission::create(['name' => 'roles.delete']);

        if(!in_array('animalTypes.read', $permissions))
            Permission::create(['name' => 'animalTypes.read']);
        if(!in_array('animalTypes.write', $permissions))
            Permission::create(['name' => 'animalTypes.write']);
        if(!in_array('animalTypes.delete', $permissions))
            Permission::create(['name' => 'animalTypes.delete']);

        if(!in_array('entities.read', $permissions))
            Permission::create(['name' => 'entities.read']);
        if(!in_array('entities.write', $permissions))
            Permission::create(['name' => 'entities.write']);
        if(!in_array('entities.delete', $permissions))
            Permission::create(['name' => 'entities.delete']);
        
        if (!in_array('categories.read', $permissions))
            Permission::create(['name' => 'categories.read']);
        if (!in_array('categories.write', $permissions))
            Permission::create(['name' => 'categories.write']);
        if (!in_array('categories.delete', $permissions))
            Permission::create(['name' => 'categories.delete']);

        if (!in_array('animalSpecies.read', $permissions))
            Permission::create(['name' => 'animalSpecies.read']);
        if (!in_array('animalSpecies.write', $permissions))
            Permission::create(['name' => 'animalSpecies.write']);
        if (!in_array('animalSpecies.delete', $permissions))
            Permission::create(['name' => 'animalSpecies.delete']);

        if (!in_array('animalBreeds.read', $permissions))
            Permission::create(['name' => 'animalBreeds.read']);
        if (!in_array('animalBreeds.write', $permissions))
            Permission::create(['name' => 'animalBreeds.write']);
        if (!in_array('animalBreeds.delete', $permissions))
            Permission::create(['name' => 'animalBreeds.delete']);

        if (!in_array('colors.read', $permissions))
            Permission::create(['name' => 'colors.read']);
        if (!in_array('colors.write', $permissions))
            Permission::create(['name' => 'colors.write']);
        if (!in_array('colors.delete', $permissions))
            Permission::create(['name' => 'colors.delete']);

        if (!in_array('animals.read', $permissions))
            Permission::create(['name' => 'animals.read']);
        if (!in_array('animals.write', $permissions))
            Permission::create(['name' => 'animals.write']);
        if (!in_array('animals.delete', $permissions))
            Permission::create(['name' => 'animals.delete']);

        if (!in_array('animalStatuses.read', $permissions))
            Permission::create(['name' => 'animalStatuses.read']);
        if (!in_array('animalStatuses.write', $permissions))
            Permission::create(['name' => 'animalStatuses.write']);
        if (!in_array('animalStatuses.delete', $permissions))
            Permission::create(['name' => 'animalStatuses.delete']);

        if (!in_array('plans.read', $permissions))
            Permission::create(['name' => 'plans.read']);
        if (!in_array('plans.write', $permissions))
            Permission::create(['name' => 'plans.write']);
        if (!in_array('plans.delete', $permissions))
            Permission::create(['name' => 'plans.delete']);

        if (!in_array('statistics.read', $permissions))
            Permission::create(['name' => 'statistics.read']);


        if(!Role::where('name', 'مشرف')->exists())
            Role::create([
                'id'         => 1,
                'name'       => 'مشرف',
                'name_en'    => 'admin',
                'name_ar'    =>  'مشرف',
                'guard_name' => 'web',
            ]);
        if(!Role::where('name', 'مستخدم')->exists())
             Role::create([
                'id'         => 2,
                'name'       => 'مستخدم',
                'name_en'    => 'user',
                'name_ar'    => 'مستخدم',
                'guard_name' => 'web',
             ]);

        $admin_role = Role::where('name_en', 'admin')->first();
        $admin_permissions = Permission::pluck('id')->toArray();
        $admin_role->syncPermissions($admin_permissions);
    }
}
