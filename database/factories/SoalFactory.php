<?php

namespace Database\Factories;

use App\Models\Soal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Soal>
 */
class SoalFactory extends Factory
{
    protected $model = Soal::class;

    public function definition(): array
    {
        return [
            'pertanyaan' => $this->faker->sentence(10) . '?',
            'tipe_soal' => $this->faker->randomElement(['pg', 'essay']),
            'bobot' => $this->faker->numberBetween(5, 20),
            'opciona' => $this->faker->sentence(),
            'opcionb' => $this->faker->sentence(),
            'opcionc' => $this->faker->sentence(),
            'opciond' => $this->faker->sentence(),
            'opcionE' => $this->faker->sentence(),
            'kunci_jawaban' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            'pembahasan' => $this->faker->paragraph(),
            'tenant_id' => null,
        ];
    }
}
