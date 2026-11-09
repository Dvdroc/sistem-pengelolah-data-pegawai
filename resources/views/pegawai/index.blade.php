@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('content')

    {{-- Judul halaman + tombol tambah --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="h4 mb-0">Daftar pegawai</h1>
        <a class="btn btn-primary" href="{{ route('pegawai.create') }}">
            <i class="bi bi-plus-lg me-1"></i>Tambah pegawai
        </a>
    </div>

    {{-- Ringkasan: memakai array $ringkasan dan $perStatus dari controller --}}
    <div class="card mb-3 stat-strip">
        <div class="d-flex flex-wrap">
            <div class="stat">
                <div class="stat-label">Jumlah pegawai</div>
                <div class="stat-value">{{ $ringkasan['jumlah'] }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Total gaji</div>
                <div class="stat-value">Rp {{ number_format($ringkasan['total'], 0, ',', '.') }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Rata-rata gaji</div>
                <div class="stat-value">Rp {{ number_format($ringkasan['rata_rata'], 0, ',', '.') }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Tertinggi</div>
                <div class="stat-value">Rp {{ number_format($ringkasan['tertinggi'], 0, ',', '.') }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Terendah</div>
                <div class="stat-value">Rp {{ number_format($ringkasan['terendah'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="border-top px-3 py-2 small text-muted">
            @foreach($perStatus as $kode => $jumlah)
                <span class="me-3">{{ $statusList[$kode] ?? $kode }}: <b class="text-dark">{{ $jumlah }}</b></span>
            @endforeach
        </div>
    </div>

    {{-- Peringatan kontrak yang segera berakhir (array $kontrakHampirHabis) --}}
    @if(count($kontrakHampirHabis) > 0)
        <div class="alert alert-warning d-flex align-items-start" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>Kontrak berakhir dalam 30 hari: <b>{{ implode(', ', $kontrakHampirHabis) }}</b></div>
        </div>
    @endif

    {{-- Pencarian --}}
    <form method="get" class="row g-2 mb-3">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="cari" value="{{ $cari }}" class="form-control" placeholder="Cari nama pegawai">
            </div>
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary">Cari</button>
            @if($cari)
                <a href="{{ route('pegawai.index') }}" class="btn btn-link">Reset</a>
            @endif
        </div>
    </form>

    {{-- Tabel pegawai --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Masa kontrak</th>
                        <th class="text-end">Gaji</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $p)
                        @php
                            // Warna badge status (array asosiatif: status => class Bootstrap)
                            $warna = ['tetap' => 'text-bg-success', 'kontrak' => 'text-bg-warning', 'magang' => 'text-bg-secondary'];
                          @endphp
                        <tr class="baris" style="--i: {{ min($loop->index, 10) }}">
                            <td>{{ $pegawai->firstItem() + $loop->index }}</td>
                            <td>{{ $p->nip }}</td>
                            <td>
                                <div class="fw-semibold">{{ $p->nama }}</div>
                                <div class="small text-muted">{{ $p->alamat }}</div>
                            </td>
                            <td>{{ $p->jabatan->nama_jabatan }}</td>
                            <td>
                                <span class="badge {{ $warna[$p->status] ?? 'text-bg-light' }}">
                                    {{ $statusList[$p->status] ?? $p->status }}
                                </span>
                            </td>
                            <td>
                                @if($p->status === 'kontrak')
                                    {{ \Carbon\Carbon::parse($p->mulai_kontrak)->format('d/m/Y') }} s/d
                                    {{ \Carbon\Carbon::parse($p->akhir_kontrak)->format('d/m/Y') }}
                                    <div class="small text-muted">{{ $p->labelKontrak() }}</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">Rp {{ number_format($p->gaji, 0, ',', '.') }}</td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('pegawai.edit', $p) }}">
                                    <i class="bi bi-pencil-square"></i> Ubah
                                </a>
                                {{-- Tombol ini hanya membuka modal; data baris dikirim lewat atribut data-* --}}
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#modalHapus" data-nama="{{ $p->nama }}" data-nip="{{ $p->nip }}"
                                    data-url="{{ route('pegawai.destroy', $p) }}">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr class="baris" style="--i: {{ min($loop->index, 10) }}">
                            <td colspan="8" class="text-center text-muted py-5">
                                Belum ada data pegawai. <a href="{{ route('pegawai.create') }}">Tambah pegawai pertama</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- Paginasi: info jumlah data + tombol halaman --}}
            @if($pegawai->total() > 0)
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                    <div class="small text-muted">
                        Menampilkan {{ $pegawai->firstItem() }}–{{ $pegawai->lastItem() }} dari {{ $pegawai->total() }} pegawai
                    </div>
                    {{ $pegawai->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
    {{-- Modal konfirmasi hapus (satu modal dipakai semua baris) --}}
    <div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="judulHapus" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" id="formHapus">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body text-center p-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger mb-3"
                            style="width:56px;height:56px;font-size:1.5rem">
                            <i class="bi bi-trash3"></i>
                        </div>
                        <h2 class="h5" id="judulHapus">Hapus data pegawai?</h2>
                        <p class="text-muted mb-4">
                            Data <b id="namaHapus"></b> (NIP <span id="nipHapus"></span>)
                            akan dihapus permanen dan tidak bisa dikembalikan.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger" id="tombolHapus">Ya, hapus</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        const modalHapus = document.getElementById('modalHapus');
        const formHapus = document.getElementById('formHapus');
        const tombolHapus = document.getElementById('tombolHapus');

        // Saat modal dibuka, isi nama, NIP, dan alamat tujuan form dari tombol yang diklik
        modalHapus.addEventListener('show.bs.modal', function (event) {
            const tombol = event.relatedTarget;
            document.getElementById('namaHapus').textContent = tombol.dataset.nama;
            document.getElementById('nipHapus').textContent = tombol.dataset.nip;
            formHapus.action = tombol.dataset.url;

            // Kembalikan tombol ke keadaan awal
            tombolHapus.disabled = false;
            tombolHapus.textContent = 'Ya, hapus';
        });

        // Saat dikirim, tombol jadi "Menghapus..." supaya tidak diklik dua kali
        formHapus.addEventListener('submit', function () {
            tombolHapus.disabled = true;
            tombolHapus.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menghapus...';
        });
    </script>
@endpush