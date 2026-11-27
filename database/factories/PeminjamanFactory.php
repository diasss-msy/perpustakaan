<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'buku_id' => Buku::factory(),
            'nama_peminjam' => fake()->name(),
            'tanggal_pinjam' => now()->toDateString(),
        ];
    }
}
