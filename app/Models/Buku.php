<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class Buku extends Model
{
    /** @use HasFactory<\Database\Factories\BukuFactory> */
    use HasFactory;

    protected $table = 'buku';

    protected $fillable = ['judul', 'penulis', 'stok'];

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function pinjam(string $namaPeminjam): Peminjaman
    {
        return DB::transaction(function () use ($namaPeminjam) {
            $buku = static::lockForUpdate()->findOrFail($this->id);
            if ($buku->stok < 1) {
                throw new RuntimeException("Stok buku \"{$buku->judul}\" habis.");
            }
            $buku->decrement('stok');
            $this->stok = $buku->stok;

            return $buku->peminjaman()->create([
                'nama_peminjam' => $namaPeminjam,
                'tanggal_pinjam' => now()->toDateString(),
            ]);
        });
    }
}
