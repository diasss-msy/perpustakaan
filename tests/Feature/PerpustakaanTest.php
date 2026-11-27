<?php

namespace Tests\Feature;

use App\Models\Buku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class PerpustakaanTest extends TestCase
{
    use RefreshDatabase;

    public function test_meminjam_buku_mengurangi_stok_dan_mencatat_peminjaman(): void
    {
        $buku = Buku::factory()->create(['stok' => 2]);

        $pinjam = $buku->pinjam('Syahrial');

        $this->assertSame(1, $buku->fresh()->stok);
        $this->assertSame('Syahrial', $pinjam->nama_peminjam);
        $this->assertNull($pinjam->tanggal_kembali);
        $this->assertCount(1, $buku->peminjaman);
    }

    public function test_tidak_bisa_meminjam_kalau_stok_habis(): void
    {
        $buku = Buku::factory()->create(['stok' => 0]);

        $this->expectException(RuntimeException::class);
        try {
            $buku->pinjam('Syahrial');
        } finally {
            $this->assertSame(0, $buku->fresh()->stok);
            $this->assertSame(0, $buku->peminjaman()->count());
        }
    }

    public function test_mengembalikan_buku_menambah_stok(): void
    {
        $buku = Buku::factory()->create(['stok' => 1]);
        $pinjam = $buku->pinjam('Syahrial');

        $pinjam->kembalikan();

        $this->assertSame(1, $buku->fresh()->stok);
        $this->assertNotNull($pinjam->fresh()->tanggal_kembali);
    }

    public function test_tidak_bisa_mengembalikan_dua_kali(): void
    {
        $buku = Buku::factory()->create(['stok' => 1]);
        $pinjam = $buku->pinjam('Syahrial');
        $pinjam->kembalikan();

        $this->expectException(RuntimeException::class);
        try {
            $pinjam->kembalikan();
        } finally {
            $this->assertSame(1, $buku->fresh()->stok);
        }
    }
}
