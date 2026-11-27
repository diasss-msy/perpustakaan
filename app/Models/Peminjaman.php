<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class Peminjaman extends Model
{
    /** @use HasFactory<\Database\Factories\PeminjamanFactory> */
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = ['buku_id', 'nama_peminjam', 'tanggal_pinjam', 'tanggal_kembali'];

    protected $casts = ['tanggal_pinjam' => 'date', 'tanggal_kembali' => 'date'];

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    public function kembalikan(): void
    {
        if ($this->tanggal_kembali) {
            throw new RuntimeException('Buku ini sudah dikembalikan.');
        }

        DB::transaction(function () {
            $this->update(['tanggal_kembali' => now()->toDateString()]);
            $this->buku()->increment('stok');
        });
    }
}
