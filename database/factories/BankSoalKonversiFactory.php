<?php

namespace Database\Factories;

use App\Models\BankSoalKonversi;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankSoalKonversiFactory extends Factory
{
    protected $model = BankSoalKonversi::class;

    public function definition(): array
    {
        return [
            'jawaban'    => fake()->paragraph(),
            'output'     => fake()->sentence(),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
        ];
    }
}
