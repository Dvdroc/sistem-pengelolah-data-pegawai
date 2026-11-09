<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Data Pegawai')</title>

    {{-- Library pre-existing: Bootstrap 5 + Bootstrap Icons lewat CDN (tanpa npm) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f3f5f7;
        }

        .topbar {
            background: #1f3a4d;
        }

        .topbar .navbar-brand {
            font-weight: 600;
            letter-spacing: .2px;
        }

        .stat-strip .stat {
            padding: .75rem 1.25rem;
        }

        .stat-strip .stat+.stat {
            border-left: 1px solid #dee2e6;
        }

        .stat-label {
            font-size: .8rem;
            color: #6c757d;
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 600;
        }

        table th {
            white-space: nowrap;
        }

        /* ===== Micro animation ===== */
        @keyframes naik {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes getar {
            25% {
                transform: translateX(-4px);
            }

            75% {
                transform: translateX(4px);
            }
        }

        /* Hanya jalan jika pengguna tidak mematikan animasi di perangkatnya */
        @media (prefers-reduced-motion: no-preference) {

            /* Halaman muncul perlahan saat dibuka */
            main.container {
                animation: naik .35s ease-out;
            }

            /* Baris tabel muncul bergantian (urutan diatur lewat variabel --i) */
            tbody tr.baris {
                animation: naik .35s ease-out both;
                animation-delay: calc(.1s + var(--i) * 40ms);
            }

            /* Notifikasi masuk dari atas */
            .alert {
                animation: naik .3s ease-out;
            }

            /* Tombol sedikit terangkat saat disorot, dan menekan saat diklik */
            .btn {
                transition: transform .12s ease, background-color .15s, color .15s, border-color .15s;
            }

            .btn:hover {
                transform: translateY(-1px);
            }

            .btn:active {
                transform: scale(.97);
            }

            /* Baris tabel dan badge bereaksi saat disorot */
            .table-hover tbody tr {
                transition: background-color .15s;
            }

            .badge {
                transition: transform .15s;
            }

            tbody tr:hover .badge {
                transform: scale(1.08);
            }

            /* Kolom yang salah bergetar sekali */
            .is-invalid {
                animation: getar .3s;
            }
        }
    </style>
</head>

<body>

    {{-- Navbar: ditulis sekali di layout, dipakai semua halaman (prinsip DRY) --}}
    <nav class="navbar topbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('pegawai.index') }}">
                <i class="bi bi-people-fill me-2"></i>Pengelolaan Data Pegawai
            </a>
        </div>
    </nav>

    <main class="container my-4">

        {{-- Pesan sukses setelah tambah/ubah/hapus --}}
        @if(session('ok'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>{{ session('ok') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        {{-- Isi halaman masuk di sini --}}
        @yield('content')

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>