<?php

namespace Database\Factories;

use App\Models\Nilai;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Nilai>
 */
class NilaiFactory extends Factory
{
    protected $model = Nilai::class;

    public function definition(): array
    {
        return [
            'nilai_otomatis' => $this->faker->numberBetween(0, 100),
            'nilai_essay' => $this->faker->numberBetween(0, 100),
            'nilai_akhir' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['lulus', 'tidak_lulus', 'pending', 'pending_essay']),
            'tenant_id' => null,
        ];
    }
}
