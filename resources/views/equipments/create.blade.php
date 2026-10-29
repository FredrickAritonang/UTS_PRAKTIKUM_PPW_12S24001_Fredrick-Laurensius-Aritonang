@extends('layouts.app')

@section('title', 'Pendaftaran Inventaris Baru')

@section('content')
<!-- Section Semantik HTML5 untuk Pendaftaran Aset Baru -->
<section class="form-section form-card">
    <div class="section-header">
        <div>
            <h2 class="section-title">Pendaftaran Inventaris Perangkat Laboratorium</h2>
            <p class="section-subtitle">Daftarkan aset perangkat laboratorium baru dengan mengunggah foto dan spesifikasi lengkap.</p>
        </div>
        <div>
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">
                &larr; Kembali ke Katalog
            </a>
        </div>
    </div>

    <!-- Formulir Pendaftaran Aset Baru (POST dengan CSRF & Multipart Enctype) -->
    <form action="{{ route('equipments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-grid-2">
            <!-- Dropdown Kategori Perangkat -->
            <div class="form-group">
                <label for="category_id">Kategori Perangkat <span style="color: #ef4444;">*</span></label>
                <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->code }})
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Input Kode Aset Inventaris -->
            <div class="form-group">
                <label for="asset_code">Kode Aset Inventaris <span style="color: #ef4444;">*</span></label>
                <input type="text"
                       name="asset_code"
                       id="asset_code"
                       class="form-control @error('asset_code') is-invalid @enderror"
                       placeholder="Contoh: EQ-IOT-005"
                       value="{{ old('asset_code') }}">
                <div class="form-hint">Format standar: EQ-[KODE_KAT]-[NOMOR]</div>
                @error('asset_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Input Nama Perangkat -->
        <div class="form-group" style="margin-top: 1rem;">
            <label for="name">Nama Perangkat Laboratorium <span style="color: #ef4444;">*</span></label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Contoh: Raspberry Pi 4 Model B (8GB RAM) Starter Kit"
                   value="{{ old('name') }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Input Spesifikasi Teknis -->
        <div class="form-group" style="margin-top: 1rem;">
            <label for="specification">Spesifikasi Teknis Perangkat <span style="color: #ef4444;">*</span></label>
            <textarea name="specification"
                      id="specification"
                      rows="4"
                      class="form-control @error('specification') is-invalid @enderror"
                      placeholder="Jelaskan spesifikasi prosesor, RAM, penyimpanan, sensor, dan kelengkapan unit...">{{ old('specification') }}</textarea>
            @error('specification')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-grid-2" style="margin-top: 1rem;">
            <!-- Dropdown Kondisi Fisik -->
            <div class="form-group">
                <label for="condition">Kondisi Fisik Saat Didaftarkan <span style="color: #ef4444;">*</span></label>
                <select name="condition" id="condition" class="form-control @error('condition') is-invalid @enderror">
                    <option value="baik" {{ old('condition', 'baik') === 'baik' ? 'selected' : '' }}>Baik (Normal & Berfungsi)</option>
                    <option value="rusak_ringan" {{ old('condition') === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ old('condition') === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
                @error('condition')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Input Tarif Kompensasi Harian -->
            <div class="form-group">
                <label for="daily_rental_fee">Tarif Kompensasi Harian (Rupiah) <span style="color: #ef4444;">*</span></label>
                <input type="number"
                       name="daily_rental_fee"
                       id="daily_rental_fee"
                       min="0"
                       class="form-control @error('daily_rental_fee') is-invalid @enderror"
                       placeholder="Contoh: 15000"
                       value="{{ old('daily_rental_fee') }}">
                <div class="form-hint">Besaran tarif per hari kalender peminjaman</div>
                @error('daily_rental_fee')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Upload Berkas Foto Fisik -->
        <div class="form-group" style="margin-top: 1rem;">
            <label for="image">Berkas Foto Fisik Perangkat <span style="color: #ef4444;">*</span></label>
            <input type="file"
                   name="image"
                   id="image"
                   accept="image/jpeg,image/png,image/webp"
                   class="form-control @error('image') is-invalid @enderror">
            <div class="form-hint">Format yang didukung: JPEG, PNG, WEBP. Ukuran maksimal: 2048 KB (2 MB).</div>
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Tombol Aksi Formulir -->
        <div class="form-actions">
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <span>&#128190;</span> Simpan & Daftarkan Aset
            </button>
        </div>
    </form>
</section>
@endsection
