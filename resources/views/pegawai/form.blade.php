@extends('layouts.app')

@section('title', $pegawai->exists ? 'Ubah pegawai' : 'Tambah pegawai')

@section('content')

  {{-- $pegawai->exists bernilai true kalau sedang mengubah data lama --}}
  <h1 class="h4 mb-3">{{ $pegawai->exists ? 'Ubah data pegawai' : 'Tambah pegawai' }}</h1>

  <div class="card">
    <div class="card-body">

      {{-- Ringkasan error validasi --}}
      @if($errors->any())
        <div class="alert alert-danger">
          <b>Data belum bisa disimpan.</b> Periksa kolom yang ditandai merah.
        </div>
      @endif

      <form method="post" action="{{ $pegawai->exists ? route('pegawai.update', $pegawai) : route('pegawai.store') }}"
        class="row g-3">
        @csrf
        @if($pegawai->exists) @method('PUT') @endif

        {{-- old(...) mempertahankan isian jika validasi gagal --}}
        <div class="col-md-4">
          <label class="form-label" for="nip">NIP</label>
          <input id="nip" name="nip" value="{{ old('nip', $pegawai->nip) }}"
            class="form-control @error('nip') is-invalid @enderror">
          @error('nip')
          <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
          <label class="form-label" for="nama">Nama lengkap</label>
          <input id="nama" name="nama" value="{{ old('nama', $pegawai->nama) }}"
            class="form-control @error('nama') is-invalid @enderror">
          @error('nama')
          <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label class="form-label" for="alamat">Alamat</label>
          <textarea id="alamat" name="alamat" rows="2"
            class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $pegawai->alamat) }}</textarea>
          @error('alamat')
          <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label" for="tgl_lahir">Tanggal lahir</label>
          <input id="tgl_lahir" type="date" name="tgl_lahir" value="{{ old('tgl_lahir', $pegawai->tgl_lahir) }}"
            class="form-control @error('tgl_lahir') is-invalid @enderror">
          @error('tgl_lahir')
          <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label" for="gaji">Gaji (Rp)</label>
          <input id="gaji" type="number" name="gaji" value="{{ old('gaji', $pegawai->gaji) }}"
            class="form-control @error('gaji') is-invalid @enderror">
          @error('gaji')
          <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Dropdown jabatan: dibuat dari tabel jabatans (relasi) --}}
        <div class="col-md-4">
          <label class="form-label" for="jabatan_id">Jabatan</label>
          <select id="jabatan_id" name="jabatan_id" class="form-select @error('jabatan_id') is-invalid @enderror">
            <option value="">Pilih jabatan</option>
            @foreach($jabatan as $j)
              <option value="{{ $j->id }}" @selected(old('jabatan_id', $pegawai->jabatan_id) == $j->id)>
                {{ $j->nama_jabatan }}
              </option>
            @endforeach
          </select>
          @error('jabatan_id')
          <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Dropdown status: dibuat dari array $statusList --}}
        <div class="col-md-4">
          <label class="form-label" for="status">Status kepegawaian</label>
          <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
            <option value="">Pilih status</option>
            @foreach($statusList as $kode => $label)
              <option value="{{ $kode }}" @selected(old('status', $pegawai->status) == $kode)>
                {{ $label }}
              </option>
            @endforeach
          </select>
          @error('status')
          <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Bagian kontrak: hanya tampil jika status = kontrak (diatur JavaScript di bawah) --}}
        <div class="col-md-4 kolom-kontrak">
          <label class="form-label" for="mulai_kontrak">Mulai kontrak</label>
          <input id="mulai_kontrak" type="date" name="mulai_kontrak"
            value="{{ old('mulai_kontrak', $pegawai->mulai_kontrak) }}"
            class="form-control @error('mulai_kontrak') is-invalid @enderror">
          @error('mulai_kontrak')
          <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4 kolom-kontrak">
          <label class="form-label" for="akhir_kontrak">Akhir kontrak</label>
          <input id="akhir_kontrak" type="date" name="akhir_kontrak"
            value="{{ old('akhir_kontrak', $pegawai->akhir_kontrak) }}"
            class="form-control @error('akhir_kontrak') is-invalid @enderror">
          @error('akhir_kontrak')
          <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12 d-flex gap-2 pt-2">
          <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan pegawai</button>
          <a href="{{ route('pegawai.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    // Tampilkan kolom kontrak hanya bila status "kontrak" dipilih
    const status = document.getElementById('status');
    const kolomKontrak = document.querySelectorAll('.kolom-kontrak');

    function aturKontrak() {
      kolomKontrak.forEach(function (k) {
        k.style.display = (status.value === 'kontrak') ? 'block' : 'none';
      });
    }
    status.addEventListener('change', aturKontrak);
    aturKontrak();   // jalankan sekali saat halaman dibuka
    // Tombol simpan berubah jadi "Menyimpan..." supaya tidak diklik dua kali
    document.querySelector('form.row').addEventListener('submit', function () {
      const tombol = this.querySelector('button.btn-primary');
      tombol.disabled = true;
      tombol.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });
  </script>
@endpush