<?php

namespace Database\Seeders;

use App\Models\Candidato;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CandidatoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $key => $user) {
            $randomUSer = $users->random();

            Candidato::factory()->create([
                "owner" => $randomUSer->id,
                "created_by" => $user->id
            ]);
        }
    }
}
