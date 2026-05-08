<?php

namespace Database\Factories;

use App\Models\Ujian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ujian>
 */
class UjianFactory extends Factory
{
    protected $model = Ujian::class;

    public function definition(): array
    {
        return [
            'nama_ujian' => $this->faker->sentence(4),
            'deskripsi' => $this->faker->paragraph(),
            'tipe_ujian' => $this->faker->randomElement(['ujian_harian', 'ujian_tengah', 'ujian_akhir']),
            'durasi_menit' => $this->faker->numberBetween(30, 180),
            'tanggal_mulai' => $this->faker->dateTimeBetween('now', '+1 month'),
            'tanggal_selesai' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'is_published' => true,
            'is_shuffle_questions' => false,
            'is_show_score_immediately' => true,
            'tenant_id' => null,
        ];
    }
}
