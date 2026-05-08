<?php

namespace Database\Factories;

use App\Models\JawabanSiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JawabanSiswa>
 */
class JawabanSiswaFactory extends Factory
{
    protected $model = JawabanSiswa::class;

    public function definition(): array
    {
        return [
            'jawaban' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            'is_submitted' => false,
            'waktu_submit' => null,
            'tenant_id' => null,
        ];
    }
}
