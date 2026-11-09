<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Menambahkan data jabatan ke tabel jabatans
        $daftarJabatan = ['Manager', 'Supervisor', 'Staff', 'Admin'];

        foreach ($daftarJabatan as $nama) {
            Jabatan::create(['nama_jabatan' => $nama]);
        }

        // Mengambil ID jabatan berdasarkan nama jabatan
        // agar bisa digunakan saat menyimpan data pegawai
        $idJabatan = Jabatan::pluck('id', 'nama_jabatan')->toArray();

        // Menyiapkan data pegawai yang akan dimasukkan ke database
        $daftarPegawai = [
            ['nip' => '19850101001', 'nama' => 'Budi Santoso', 'alamat' => 'Jl. Merdeka No. 10, Sidoarjo', 'tgl_lahir' => '1985-01-01', 'gaji' => 12000000, 'status' => 'tetap', 'jabatan' => 'Manager'],
            ['nip' => '19880315002', 'nama' => 'Siti Aminah', 'alamat' => 'Jl. Diponegoro No. 5, Surabaya', 'tgl_lahir' => '1988-03-15', 'gaji' => 9000000, 'status' => 'tetap', 'jabatan' => 'Supervisor'],
            ['nip' => '19920720003', 'nama' => 'Agus Prasetyo', 'alamat' => 'Jl. Pahlawan No. 21, Gresik', 'tgl_lahir' => '1992-07-20', 'gaji' => 6500000, 'status' => 'tetap', 'jabatan' => 'Staff'],
            ['nip' => '19950910004', 'nama' => 'Dewi Lestari', 'alamat' => 'Jl. Sudirman No. 8, Sidoarjo', 'tgl_lahir' => '1995-09-10', 'gaji' => 6000000, 'status' => 'kontrak', 'mulai_kontrak' => '2026-01-01', 'akhir_kontrak' => '2027-01-01', 'jabatan' => 'Admin'],
            ['nip' => '19970505005', 'nama' => 'Rizky Hidayat', 'alamat' => 'Jl. Gajah Mada No. 17, Mojokerto', 'tgl_lahir' => '1997-05-05', 'gaji' => 5500000, 'status' => 'kontrak', 'mulai_kontrak' => '2026-05-01', 'akhir_kontrak' => '2026-11-20', 'jabatan' => 'Staff'],
            ['nip' => '19990212006', 'nama' => 'Putri Wulandari', 'alamat' => 'Jl. Ahmad Yani No. 3, Surabaya', 'tgl_lahir' => '1999-02-12', 'gaji' => 3000000, 'status' => 'magang', 'jabatan' => 'Admin'],
            ['nip' => '20010830007', 'nama' => 'Fajar Nugroho', 'alamat' => 'Jl. Veteran No. 12, Sidoarjo', 'tgl_lahir' => '2001-08-30', 'gaji' => 2800000, 'status' => 'magang', 'jabatan' => 'Staff'],
            ['nip' => '19830612008', 'nama' => 'Budi Hartono', 'alamat' => 'Jl. Raya Waru No. 45, Sidoarjo', 'tgl_lahir' => '1983-06-12', 'gaji' => 11000000, 'status' => 'tetap', 'jabatan' => 'Manager'],
            ['nip' => '19900425009', 'nama' => 'Ratna Sari', 'alamat' => 'Jl. Kenjeran No. 30, Surabaya', 'tgl_lahir' => '1990-04-25', 'gaji' => 8500000, 'status' => 'tetap', 'jabatan' => 'Supervisor'],
            ['nip' => '19940118010', 'nama' => 'Dimas Aditya', 'alamat' => 'Jl. Pemuda No. 7, Mojokerto', 'tgl_lahir' => '1994-01-18', 'gaji' => 5800000, 'status' => 'kontrak', 'mulai_kontrak' => '2025-11-28', 'akhir_kontrak' => '2026-11-28', 'jabatan' => 'Staff'],
            ['nip' => '19960903011', 'nama' => 'Nadia Putri', 'alamat' => 'Jl. Teuku Umar No. 14, Gresik', 'tgl_lahir' => '1996-09-03', 'gaji' => 5600000, 'status' => 'kontrak', 'mulai_kontrak' => '2025-10-15', 'akhir_kontrak' => '2026-10-15', 'jabatan' => 'Admin'],
            ['nip' => '19980321012', 'nama' => 'Eko Wahyudi', 'alamat' => 'Jl. Pandaan No. 2, Pasuruan', 'tgl_lahir' => '1998-03-21', 'gaji' => 6200000, 'status' => 'kontrak', 'mulai_kontrak' => '2026-07-01', 'akhir_kontrak' => '2027-06-30', 'jabatan' => 'Staff'],
            ['nip' => '20020714013', 'nama' => 'Lina Marlina', 'alamat' => 'Jl. Ketintang No. 19, Surabaya', 'tgl_lahir' => '2002-07-14', 'gaji' => 2900000, 'status' => 'magang', 'jabatan' => 'Admin'],
        ];

        // Menyimpan data pegawai ke tabel pegawais
        // Nama jabatan diubah menjadi jabatan_id sesuai ID yang sudah diambil
        foreach ($daftarPegawai as $p) {
            Pegawai::create([
                'nip' => $p['nip'],
                'nama' => $p['nama'],
                'alamat' => $p['alamat'],
                'tgl_lahir' => $p['tgl_lahir'],
                'gaji' => $p['gaji'],
                'status' => $p['status'],
                'jabatan_id' => $idJabatan[$p['jabatan']],
                'mulai_kontrak' => $p['mulai_kontrak'] ?? null,
                'akhir_kontrak' => $p['akhir_kontrak'] ?? null,
            ]);
        }
    }
}