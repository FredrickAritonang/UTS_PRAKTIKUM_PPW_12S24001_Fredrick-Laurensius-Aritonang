@extends('layouts.app')

@section('title', 'Katalog Aset Laboratorium')

@section('content')

<section class="catalog-section">
    <div class="section-header">
        <div>
            <h2 class="section-title"></h2>
            <p class="section-subtitle"></p>
        </div>
        <div>
            <a href="{{ route('equipments.create') }}" class="btn btn-primary">
                <span>&#10133;</span>
            </a>
        </div>
    </div>


    <form action="{{ route('equipments.index') }}" method="GET" class="filter-form">
        <div class="filter-grid">
            <div class="form-group">
                <label for="search"></label>
                <input type="text"
                       name="search"
                       id="search"
                       class="form-control"
                       placeholder="Cari nama atau kode aset (misal: drone, EQ-IOT)..."
                       value="{{ request('search') }}">
            </div>

            <div class="form-group">
                <label for="category_id"></label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value=""></option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status"></label>
                <select name="status" id="status" class="form-control">
                    <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}></option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}></option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}></option>
                    <option value="dalam_perbaikan" {{ request('status') == 'dalam_perbaikan' ? 'selected' : '' }}></option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <span>&#128269;</span>
            </button>

            @if (request()->hasAny(['search', 'category_id', 'status']))
                <a href="{{ route('equipments.index') }}" class="btn btn-secondary" title="Reset filter">
                    Reset
                </a>
            @endif
        </div>
    </form>


    @if (request('search'))
        <div class="search-summary-box">
            Menampilkan {{ $equipments->count() }} dari {{ $equipments->total() }} hasil pencarian untuk kata<strong>{{ request('search') }}</strong>"
        </div>
    @elseif (request('category_id') || (request('status') && request('status') !== 'semua'))
        <div class="search-summary-box">
            Menampilkan {{ $equipments->total() }} perangkat yang sesuai dengan filter yang dipilih.
        </div>
    @endif


    @if ($equipments->count() > 0)
        <div class="equipment-grid">
            @foreach ($equipments as $equipment)
                <article class="equipment-card">
                    <div class="card-image-box">
                        @if ($equipment->image_path)
                            <img src="{{ asset('storage/' . $equipment->image_path) }}" alt="{{ $equipment->name }}">
                        @else
                            <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-weight: 500;">
                                Tidak ada foto
                            </div>
                        @endif


                        <div class="card-badge-overlay">
                            @if ($equipment->status === 'tersedia')
                                <span class="status-badge badge-tersedia"></span>
                            @elseif ($equipment->status === 'dipinjam')
                                <span class="status-badge badge-dipinjam"></span>
                            @else
                                <span class="status-badge badge-dalam_perbaikan"></span>
                            @endif
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="card-code">{{ $equipment->asset_code }}</div>
                        <h3 class="card-title">{{ $equipment->name }}</h3>
                        
                        <div class="card-category-meta">
                            <span>&#128193;</span> {{ $equipment->category->name ?? 'Kategori Umum' }}
                            &bull;
                            <span>Kondisi: <strong>{{ ucfirst(str_replace('_', ' ', $equipment->condition)) }}</strong></span>
                        </div>

                        <div class="card-price-box">
                            <span class="card-price-label"></span>
                            <span class="card-price-value">Rp {{ number_format($equipment->daily_rental_fee, 0, ',', '.') }}</span>
                        </div>

                        <div class="card-actions">
                            <a href="{{ route('equipments.show', $equipment->id) }}" class="btn btn-secondary btn-sm">
                                Detail Spesifikasi
                            </a>
                            <a href="{{ route('loans.create', ['equipment_id' => $equipment->id]) }}"
                               class="btn {{ $equipment->status === 'tersedia' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                                Ajukan Peminjaman
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>


        <div class="pagination-wrapper">
            {{ $equipments->links('pagination.custom') }}
        </div>
    @else
        <div class="empty-state">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">&#128269;</div>
            <h3></h3>
            <p></p>
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">
                Lihat Seluruh Katalog
            </a>
        </div>
    @endif
</section>
@endsection
