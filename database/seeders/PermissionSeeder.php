<?php

namespace Database\Seeders;

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

        $admin_permissions = [];

        $user_permissions = [];

        if (!in_array('users.read', $permissions)) {
            $permission = Permission::create(['name' => 'users.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('users.write', $permissions)) {
            $permission = Permission::create(['name' => 'users.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('users.delete', $permissions)) {
            $permission = Permission::create(['name' => 'users.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('roles.read', $permissions)) {
            $permission = Permission::create(['name' => 'roles.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('roles.write', $permissions)) {
            $permission = Permission::create(['name' => 'roles.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('roles.delete', $permissions)) {
            $permission = Permission::create(['name' => 'roles.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('animalTypes.read', $permissions)) {
            $permission = Permission::create(['name' => 'animalTypes.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animalTypes.write', $permissions)) {
            $permission = Permission::create(['name' => 'animalTypes.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animalTypes.delete', $permissions)) {
            $permission = Permission::create(['name' => 'animalTypes.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('entities.read', $permissions)) {
            $permission = Permission::create(['name' => 'entities.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('entities.write', $permissions)) {
            $permission = Permission::create(['name' => 'entities.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('entities.delete', $permissions)) {
            $permission = Permission::create(['name' => 'entities.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('categories.read', $permissions)) {
            $permission = Permission::create(['name' => 'categories.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('categories.write', $permissions)) {
            $permission = Permission::create(['name' => 'categories.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('categories.delete', $permissions)) {
            $permission = Permission::create(['name' => 'categories.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('animalSpecies.read', $permissions)) {
            $permission = Permission::create(['name' => 'animalSpecies.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animalSpecies.write', $permissions)) {
            $permission = Permission::create(['name' => 'animalSpecies.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animalSpecies.delete', $permissions)) {
            $permission = Permission::create(['name' => 'animalSpecies.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('animalBreeds.read', $permissions)) {
            $permission = Permission::create(['name' => 'animalBreeds.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animalBreeds.write', $permissions)) {
            $permission = Permission::create(['name' => 'animalBreeds.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animalBreeds.delete', $permissions)) {
            $permission = Permission::create(['name' => 'animalBreeds.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('colors.read', $permissions)) {
            $permission = Permission::create(['name' => 'colors.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('colors.write', $permissions)) {
            $permission = Permission::create(['name' => 'colors.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('colors.delete', $permissions)) {
            $permission = Permission::create(['name' => 'colors.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('animals.read', $permissions)) {
            $permission = Permission::create(['name' => 'animals.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animals.write', $permissions)) {
            $permission = Permission::create(['name' => 'animals.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animals.delete', $permissions)) {
            $permission = Permission::create(['name' => 'animals.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('animalStatuses.read', $permissions)) {
            $permission = Permission::create(['name' => 'animalStatuses.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animalStatuses.write', $permissions)) {
            $permission = Permission::create(['name' => 'animalStatuses.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('animalStatuses.delete', $permissions)) {
            $permission = Permission::create(['name' => 'animalStatuses.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('plans.read', $permissions)) {
            $permission = Permission::create(['name' => 'plans.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('plans.write', $permissions)) {
            $permission = Permission::create(['name' => 'plans.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('plans.delete', $permissions)) {
            $permission = Permission::create(['name' => 'plans.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('statistics.read', $permissions)) {
            $permission = Permission::create(['name' => 'statistics.read']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('petMarks.read', $permissions)) {
            $permission =  Permission::create(['name' => 'petMarks.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('petMarks.write', $permissions)) {
            $permission =  Permission::create(['name' => 'petMarks.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('petMarks.delete', $permissions)) {
            $permission = Permission::create(['name' => 'petMarks.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('tagTypes.read', $permissions)) {
            $permission = Permission::create(['name' => 'tagTypes.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('tagTypes.write', $permissions)) {
            $permission = Permission::create(['name' => 'tagTypes.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('tagTypes.delete', $permissions)) {
            $permission = Permission::create(['name' => 'tagTypes.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('tags.read', $permissions)) {
            $permission =  Permission::create(['name' => 'tags.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('tags.write', $permissions)) {
            $permission = Permission::create(['name' => 'tags.write']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('tags.delete', $permissions)) {
            $permission = Permission::create(['name' => 'tags.delete']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!in_array('animals.transfer', $permissions)) {
            Permission::create(['name' => 'animals.transfer']);
        }
        //
        if (!in_array('ownershipRecords.read', $permissions)) {
            $permission = Permission::create(['name' => 'ownershipRecords.read']);
            $admin_permissions[] = $permission->id;
        }
       //
       if (!in_array('logos.read', $permissions)) {
            $permission = Permission::create(['name' => 'logos.read']);
            $admin_permissions[] = $permission->id;
        }
        if (!in_array('logos.write', $permissions)) {
            $permission = Permission::create(['name' => 'logos.write']);
            $admin_permissions[] = $permission->id;
        }
        //
        if (!Role::where('name', 'مشرف')->exists())
            Role::create([
                'id'         => 1,
                'name'       => 'مشرف',
                'name_en'    => 'admin',
                'name_ar'    =>  'مشرف',
                'guard_name' => 'web',
            ]);
        if (!Role::where('name', 'مستخدم')->exists())
            Role::create([
                'id'         => 2,
                'name'       => 'مستخدم',
                'name_en'    => 'user',
                'name_ar'    => 'مستخدم',
                'guard_name' => 'web',
            ]);

        $admin_role = Role::where('name_en', 'admin')->first();
        $admin_role->syncPermissions($admin_permissions);
}
}
