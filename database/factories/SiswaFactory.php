<?php

namespace Database\Factories;

use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siswa>
 */
class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        return [
            'nama_siswa' => $this->faker->name(),
            'nis' => $this->faker->unique()->numerify('###########'),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-20 years', '-15 years'),
            'alamat' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
            'no_hp' => $this->faker->phoneNumber(),
            'nama_orang_tua' => $this->faker->name(),
            'no_telepon_orang_tua' => $this->faker->phoneNumber(),
            'is_active' => true,
            'tenant_id' => null,
        ];
    }
}
