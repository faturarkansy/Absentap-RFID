<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Investor;
use App\Models\Opd;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(1)->create();

        // $user = User::create([
        //     'name' => fake()->name(),
        //     'position' => 'Direktur',
        //     'username' => 'arsieaziz',
        //     'email' => fake()->unique()->safeEmail(),
        //     'email_verified_at' => now(),
        //     'active' => 1,
        //     'password' => '$2y$10$6aDssOgnr/KglHpbazH/8.XmxPI/hvTUYpd53SzIWaEwAnBkSZdBq', // admin
        //     'remember_token' => Str::random(10),
        // ]);

        // Setting::create([
        //     'name' => 'web_title',
        //     'description' => 'Web Title',
        //     'type' => 'text',
        //     'value' => 'Absen Tap',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'web_description',
        //     'description' => 'Web Description',
        //     'type' => 'textarea',
        //     'value' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Saepe iste laboriosam asperiores ipsam recusandae nulla beatae laborum voluptatem eum hic ipsum animi quae veritatis tempora aut, ad, quia harum qui!',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'web_keyword',
        //     'description' => 'Web Keyword',
        //     'type' => 'text',
        //     'value' => 'Website, Apps, Absen Tap',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'address',
        //     'description' => 'Address',
        //     'type' => 'textarea',
        //     'value' => 'Lampung',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'telp',
        //     'description' => 'Telepon',
        //     'type' => 'text',
        //     'value' => '(0721) 781740',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'email',
        //     'description' => 'Email',
        //     'type' => 'text',
        //     'value' => 'absentap@gmail.com',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'latlong',
        //     'description' => 'Latitude & Longitude',
        //     'type' => 'text',
        //     'value' => '-5.4417824,105.2566965',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'logo_one',
        //     'description' => 'First Logo Default',
        //     'type' => 'text',
        //     'value' => 'images/logomsonly.png',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'logo_two',
        //     'description' => 'Second Logo Default',
        //     'type' => 'text',
        //     'value' => 'images/logomsonly.png',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'title_one',
        //     'description' => 'First Title Default',
        //     'type' => 'text',
        //     'value' => 'Mudah Saja',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'title_two',
        //     'description' => 'Second Title Default',
        //     'type' => 'text',
        //     'value' => 'University',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // Setting::create([
        //     'name' => 'school_erp_url',
        //     'description' => 'School ERP URL',
        //     'type' => 'text',
        //     'value' => 'http://sekolaherp.test/',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        // $permissions = [
        //     'settings',
        //     'user-list',
        //     'user-create',
        //     'user-edit',
        //     'user-reset',
        //     'user-delete',
        //     'role-list',
        //     'role-create',
        //     'role-edit',
        //     'role-delete',
        //     'student',
        //     'SchoolTime',
        //     'HolidayDate'
        // ];

        // foreach ($permissions as $permission) {
        //     Permission::create(['name' => $permission]);
        // }

        // $role = Role::create(['name' => 'Admin']);

        // $permissions = Permission::pluck('id', 'id')->all();

        // $role->syncPermissions($permissions);

        // $roleSA = Role::create(['name' => 'Super Admin']);

        // $user->assignRole($roleSA->id);

        Permission::create(['name' => 'HolidayDate']);
        Permission::create(['name' => 'Parentreport']);
    }
}