@extends('layouts.app')

@section('title', 'Pendaftaran Inventaris Baru')

@section('content')

<section class="form-section form-card">
    <div class="section-header">
        <div>
            <h2 class="section-title"></h2>
            <p class="section-subtitle"></p>
        </div>
        <div>
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">
                &larr; Kembali ke Katalog
            </a>
        </div>
    </div>


    <form action="{{ route('equipments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-grid-2">

            <div class="form-group">
                <label for="category_id"><span style="color: #ef4444;">*</span></label>
                <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror">
                    <option value=""></option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->code }})
                        </option>
                    @endforeach
                </select>
                @error
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <div class="form-group">
                <label for="asset_code"><span style="color: #ef4444;">*</span></label>
                <input type="text"
                       name="asset_code"
                       id="asset_code"
                       class="form-control @error('asset_code') is-invalid @enderror"
                       placeholder="Contoh: EQ-IOT-005"
                       value="{{ old('asset_code') }}">
                <div class="form-hint"></div>
                @error
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div class="form-group" style="margin-top: 1rem;">
            <label for="name"><span style="color: #ef4444;">*</span></label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Contoh: Raspberry Pi 4 Model B (8GB RAM) Starter Kit"
                   value="{{ old('name') }}">
            @error
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-group" style="margin-top: 1rem;">
            <label for="specification"><span style="color: #ef4444;">*</span></label>
            <textarea name="specification"
                      id="specification"
                      rows="4"
                      class="form-control @error('specification') is-invalid @enderror"
                      placeholder="Jelaskan spesifikasi prosesor, RAM, penyimpanan, sensor, dan kelengkapan unit...">{{ old('specification') }}</textarea>
            @error
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-grid-2" style="margin-top: 1rem;">

            <div class="form-group">
                <label for="condition"><span style="color: #ef4444;">*</span></label>
                <select name="condition" id="condition" class="form-control @error('condition') is-invalid @enderror">
                    <option value="baik" {{ old('condition', 'baik') === 'baik' ? 'selected' : '' }}></option>
                    <option value="rusak_ringan" {{ old('condition') === 'rusak_ringan' ? 'selected' : '' }}></option>
                    <option value="rusak_berat" {{ old('condition') === 'rusak_berat' ? 'selected' : '' }}></option>
                </select>
                @error
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <div class="form-group">
                <label for="daily_rental_fee"><span style="color: #ef4444;">*</span></label>
                <input type="number"
                       name="daily_rental_fee"
                       id="daily_rental_fee"
                       min="0"
                       class="form-control @error('daily_rental_fee') is-invalid @enderror"
                       placeholder="Contoh: 15000"
                       value="{{ old('daily_rental_fee') }}">
                <div class="form-hint"></div>
                @error
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div class="form-group" style="margin-top: 1rem;">
            <label for="image"><span style="color: #ef4444;">*</span></label>
            <input type="file"
                   name="image"
                   id="image"
                   accept="image/jpeg,image/png,image/webp"
                   class="form-control @error('image') is-invalid @enderror">
            <div class="form-hint"></div>
            @error
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-actions">
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary"></a>
            <button type="submit" class="btn btn-primary">
                <span>&#128190;</span>
            </button>
        </div>
    </form>
</section>
@endsection
