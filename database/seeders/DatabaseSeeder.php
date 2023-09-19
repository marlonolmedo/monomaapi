<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $roles = Role::all()->map(function($item){
            return ['role_id' => $item->id];
        })->toArray();
        \App\Models\User::factory(10)
        ->sequence(
            ...$roles
        )->create();
        \App\Models\User::factory(5)
        ->sequence(
            ...$roles
        )->sequence(
            ['is_active' => 1],
            ['is_active' => 0],
        )->create();
        $this->call(CandidatoSeeder::class);
    }
}
