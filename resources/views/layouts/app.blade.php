<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'DEL-LEATS') - Sistem Manajemen Aset & Inventaris Lab Del</title>
    <link rel="stylesheet" href="{{ asset('css/del-leats.css') }}">
    @stack('styles')
</head>
<body>

    <header class="app-header">
        <div class="header-inner">
            <a href="{{ route('equipments.index') }}" class="brand-wrapper">
                <div class="brand-logo-badge">DEL</div>
                <div class="brand-info">
                    <h1>DEL-LEATS</h1>
                    <p>Lab Equipment & Asset Tracking System &bull; IT Del</p>
                </div>
            </a>


            <nav class="main-nav" aria-label="Navigasi Utama">
                <a href="{{ route('equipments.index') }}" class="{{ request()->routeIs('equipments.index') ? 'active' : '' }}">
                    <span>&#128187;</span> Katalog Aset
                </a>
                <a href="{{ route('equipments.create') }}" class="{{ request()->routeIs('equipments.create') ? 'active' : '' }}">
                    <span>&#10133;</span> Tambah Aset
                </a>
                <a href="{{ route('loans.index') }}" class="{{ request()->routeIs('loans.index') ? 'active' : '' }}">
                    <span>&#128220;</span> Riwayat Peminjaman
                </a>
            </nav>
        </div>
    </header>


    <main class="app-main">

        <div class="alert-container">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <span style="font-size: 1.25rem;">&#9989;</span>
                    <div>
                        <strong>{{ session('success') }}</strong>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error" role="alert">
                    <span style="font-size: 1.25rem;">&#9888;</span>
                    <div>
                        <strong>{{ session('error') }}</strong>
                    </div>
                </div>
            @endif
        </div>

        @yield('content')
    </main>


    <footer class="app-footer">
        <div class="footer-inner">
            <div>
                <strong>DEL Lab Equipment & Asset Tracking System (DEL-LEATS)</strong>
                <p style="margin-top: 0.25rem; font-size: 0.8rem;">
                    Laboratorium Rekayasa Perangkat Lunak dan Jaringan &bull; Institut Teknologi Del
                </p>
            </div>
            <div class="footer-links">
                <span>Praktikum PPW (12S3101) &bull; 12S24001 - Fredrick Laurensius Aritonang</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
