<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidato>
 */
class CandidatoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name" => fake()->name(),
            "source" => fake()->sentence(1),
            "created_at" => fake()->dateTimeBetween("now", "+1 month"),
            "updated_at" => fake()->dateTimeBetween("-1 month", "+2 months")
        ];
    }
}
