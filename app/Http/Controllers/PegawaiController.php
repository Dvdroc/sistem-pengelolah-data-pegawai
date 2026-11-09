<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    /**
     * STRUKTUR DATA 1: array asosiatif (key => nilai).
     * Key disimpan di database, nilai ditampilkan ke pengguna.
     */
    private array $statusList = [
        'tetap' => 'Pegawai Tetap',
        'kontrak' => 'Kontrak',
        'magang' => 'Magang',
    ];

    /**
     * Menampilkan daftar pegawai (READ), mendukung pencarian nama.
     */
    public function index(Request $r)
    {
        $cari = $r->cari;

        // Mengambil data pegawai beserta jabatannya berdasarkan pencarian nama
        $query = Pegawai::with('jabatan')
            ->when($cari, fn($q) => $q->where('nama', 'like', "%$cari%"));

        // Menampilkan data pegawai dengan 10 data di setiap halaman
        // Kata pencarian tetap dibawa saat berpindah halaman
        $pegawai = (clone $query)->orderBy('id')->paginate(10)->withQueryString();

        // Mengambil semua data hasil pencarian untuk menghitung statistik
        $semua = (clone $query)->get();

        // Mengumpulkan seluruh nilai gaji ke dalam array
        $gajiList = $semua->pluck('gaji')->toArray();

        // Menghitung ringkasan gaji pegawai
        $ringkasan = [
            'jumlah' => count($gajiList),
            'total' => array_sum($gajiList),
            'tertinggi' => $gajiList ? max($gajiList) : 0,
            'terendah' => $gajiList ? min($gajiList) : 0,
            'rata_rata' => $gajiList ? round(array_sum($gajiList) / count($gajiList)) : 0,
        ];

        // Menghitung jumlah pegawai berdasarkan statusnya
        $perStatus = array_count_values($semua->pluck('status')->toArray());

        // Mencari pegawai yang masa kontraknya akan habis dalam 30 hari
        $kontrakHampirHabis = [];
        /** @var Pegawai $p */
        foreach ($semua as $p) {
            $sisa = $p->sisaHariKontrak();
            if ($sisa !== null && $sisa >= 0 && $sisa <= 30) {
                $kontrakHampirHabis[] = $p->nama;
            }
        }
        
        // Mengirim data pegawai dan hasil perhitungan ke halaman index
        return view('pegawai.index', [
            'pegawai' => $pegawai,
            'cari' => $cari,
            'ringkasan' => $ringkasan,
            'perStatus' => $perStatus,
            'statusList' => $this->statusList,
            'kontrakHampirHabis' => $kontrakHampirHabis,
        ]);
    }

    /** Menampilkan form tambah pegawai. */
    public function create()
    {
        return view('pegawai.form', [
            'pegawai' => new Pegawai,
            'jabatan' => Jabatan::all(),
            'statusList' => $this->statusList,
        ]);
    }

    /** Menyimpan pegawai baru (CREATE) setelah divalidasi. */
    public function store(Request $r)
    {
        Pegawai::create($this->bersihkanKontrak($r->validate($this->aturan())));
        return redirect('/pegawai')->with('ok', 'Data berhasil ditambahkan');
    }

    /** Menampilkan form ubah pegawai. */
    public function edit(Pegawai $pegawai)
    {
        return view('pegawai.form', [
            'pegawai' => $pegawai,
            'jabatan' => Jabatan::all(),
            'statusList' => $this->statusList,
        ]);
    }

    /** Menyimpan perubahan data pegawai (UPDATE). */
    public function update(Request $r, Pegawai $pegawai)
    {
        $pegawai->update($this->bersihkanKontrak($r->validate($this->aturan($pegawai->id))));
        return redirect('/pegawai')->with('ok', 'Data berhasil diubah');
    }

    /** Menghapus pegawai (DELETE). */
    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        return redirect('/pegawai')->with('ok', 'Data berhasil dihapus');
    }

    /**
     * Aturan validasi (array asosiatif).
     * $id diisi saat update supaya NIP milik pegawai itu sendiri tidak dianggap kembar.
     * Status hanya boleh salah satu key dari $statusList.
     */
    private function aturan($id = null)
    {
        return [
            'nip' => ['required', 'string', 'max:20', Rule::unique('pegawai', 'nip')->ignore($id)],
            'nama' => 'required',
            'alamat' => 'required',
            'tgl_lahir' => 'required|date',
            'gaji' => 'required|integer|min:0',
            'status' => ['required', Rule::in(array_keys($this->statusList))],
            'jabatan_id' => 'required|exists:jabatans,id',
            'mulai_kontrak' => 'required_if:status,kontrak|nullable|date',
            'akhir_kontrak' => 'required_if:status,kontrak|nullable|date|after:mulai_kontrak',
        ];
    }

    /**
     * Jika status bukan kontrak, tanggal kontrak dikosongkan
     * supaya data tidak tersisa dari status sebelumnya.
     */
    private function bersihkanKontrak(array $data): array
    {
        if ($data['status'] !== 'kontrak') {
            $data['mulai_kontrak'] = null;
            $data['akhir_kontrak'] = null;
        }
        return $data;
    }
}
