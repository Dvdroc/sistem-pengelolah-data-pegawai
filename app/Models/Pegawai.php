<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'nip',
        'nama',
        'alamat',
        'tgl_lahir',
        'gaji',
        'status',
        'jabatan_id',
        'mulai_kontrak',
        'akhir_kontrak',
    ];

    public function sisaHariKontrak()
    {
        if ($this->status !== 'kontrak' || !$this->akhir_kontrak) {
            return null;
        }
        $akhir = \Carbon\Carbon::parse($this->akhir_kontrak)->startOfDay();
        return (int) now()->startOfDay()->diffInDays($akhir, false);
    }

    public function labelKontrak(): string
    {
        // Menghitung sisa hari masa kontrak pegawai
        $sisa = $this->sisaHariKontrak();

        // Jika tidak ada tanggal kontrak, tampilkan tanda strip
        if ($sisa === null) {
            return '-';
        } elseif ($sisa < 0) {
            // Menampilkan berapa hari kontrak sudah berakhir
            return 'Berakhir ' . abs($sisa) . ' hari lalu';
        } elseif ($sisa <= 30) {
            // Memberi tanda jika kontrak akan segera berakhir
            return 'Segera berakhir (' . $sisa . ' hari lagi)';
        }
        // Menampilkan sisa hari kontrak jika masih lebih dari 30 hari
        return $sisa . ' hari lagi';
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}
