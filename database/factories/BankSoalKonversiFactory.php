<?php

namespace Database\Factories;

use App\Models\BankSoalKonversi;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankSoalKonversiFactory extends Factory
{
    protected $model = BankSoalKonversi::class;

    public function definition(): array
    {
        return [
            'id_level'   => Level::query()->inRandomOrder()->value('id') ?? Level::query()->create([
                'name' => 'Default',
                'jumlah_soal' => 10,
                'limit_soal' => 10,
            ])->id,
            'jawaban'    => fake()->paragraph(),
            'output'     => fake()->sentence(),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
        ];
    }
}
