<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // $this->call(MemberRoleSeeder::class);

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'bfexzeus@gmail.com',
            'password' => Hash::make('123'),
            'is_admin' => true,
        ]);
    }
}
