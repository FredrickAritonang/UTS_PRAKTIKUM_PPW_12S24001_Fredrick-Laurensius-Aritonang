@extends('layouts.app')

@section('title', 'Pengajuan Peminjaman Perangkat')

@section('content')

<section class="form-section form-card">
    <div class="section-header">
        <div>
            <h2 class="section-title">Formulir Pengajuan Peminjaman Aset Laboratorium</h2>
            <p class="section-subtitle">Lengkapi formulir berikut dengan data valid. Batas peminjaman maksimal adalah 7 hari kalender.</p>
        </div>
        <div>
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">
                &larr;
            </a>
        </div>
    </div>


    <form action="{{ route('loans.store') }}" method="POST">
        @csrf


        <div class="form-group">
            <label for="equipment_id">Pilih Perangkat Laboratorium <span style="color: #ef4444;">*</span></label>
            <select name="equipment_id" id="equipment_id" class="form-control @error('equipment_id') is-invalid @enderror">
                <option value="">-- Pilih Perangkat yang Ingin Dipinjam --</option>
                @foreach ($equipments as $eq)
                    <option value="{{ $eq->id }}"
                            data-fee="{{ $eq->daily_rental_fee }}"
                            data-status="{{ $eq->status }}"
                            {{ old('equipment_id', $selectedEquipmentId) == $eq->id ? 'selected' : '' }}>
                        {{ $eq->asset_code }} - {{ $eq->name }} (Rp {{ number_format($eq->daily_rental_fee, 0, ',', '.') }}/hari) [Status: {{ strtoupper(str_replace('_', ' ', $eq->status)) }}]
                    </option>
                @endforeach
            </select>
            <div class="form-hint">Hanya perangkat berstatus 'Tersedia' yang dapat disetujui peminjamannya.</div>
            @error('equipment_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-grid-2" style="margin-top: 1rem;">

            <div class="form-group">
                <label for="borrower_name">Nama Lengkap Peminjam <span style="color: #ef4444;">*</span></label>
                <input type="text"
                       name="borrower_name"
                       id="borrower_name"
                       class="form-control @error('borrower_name') is-invalid @enderror"
                       placeholder="Contoh: Fredrick Laurensius Aritonang"
                       value="{{ old('borrower_name') }}">
                @error('borrower_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <div class="form-group">
                <label for="borrower_nim">Nomor Induk Mahasiswa (NIM) <span style="color: #ef4444;">*</span></label>
                <input type="text"
                       name="borrower_nim"
                       id="borrower_nim"
                       maxlength="8"
                       class="form-control @error('borrower_nim') is-invalid @enderror"
                       placeholder="Contoh: 12S24001"
                       value="{{ old('borrower_nim') }}">
                <div class="form-hint">Harus terdiri dari tepat 8 digit angka/huruf institusi Del</div>
                @error('borrower_nim')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div class="form-group" style="margin-top: 1rem;">
            <label for="borrower_email">Alamat Surel Aktif Mahasiswa <span style="color: #ef4444;">*</span></label>
            <input type="text"
                   name="borrower_email"
                   id="borrower_email"
                   class="form-control @error('borrower_email') is-invalid @enderror"
                   placeholder="Contoh: fredrick.aritonang@students.del.ac.id"
                   value="{{ old('borrower_email') }}">
            @error('borrower_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-grid-2" style="margin-top: 1rem;">

            <div class="form-group">
                <label for="loan_date">Tanggal Peminjaman <span style="color: #ef4444;">*</span></label>
                <input type="date"
                       name="loan_date"
                       id="loan_date"
                       class="form-control @error('loan_date') is-invalid @enderror"
                       value="{{ old('loan_date') }}">
                <div class="form-hint">Tidak boleh sebelum tanggal hari ini</div>
                @error('loan_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <div class="form-group">
                <label for="expected_return_date">Rencana Tanggal Pengembalian <span style="color: #ef4444;">*</span></label>
                <input type="date"
                       name="expected_return_date"
                       id="expected_return_date"
                       class="form-control @error('expected_return_date') is-invalid @enderror"
                       value="{{ old('expected_return_date') }}">
                <div class="form-hint">Maksimal durasi peminjaman adalah 7 hari kalender</div>
                @error('expected_return_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div id="fee-estimate-box" style="margin-top: 1.25rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: var(--radius-sm); padding: 0.85rem 1.15rem; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="font-size: 0.825rem; color: #1e40af; font-weight: 600;">Kalkulasi Estimasi Kompensasi Peminjaman:</span>
                <div id="fee-formula" style="font-size: 0.8rem; color: #3b82f6;">Pilih perangkat dan tanggal untuk menghitung</div>
            </div>
            <div id="fee-total" style="font-size: 1.25rem; font-weight: 800; color: #1e3a8a;">Rp 0</div>
        </div>


        <div class="form-group" style="margin-top: 1rem;">
            <label for="purpose">Tujuan & Urgensi Peminjaman <span style="color: #ef4444;">*</span></label>
            <textarea name="purpose"
                      id="purpose"
                      rows="3"
                      class="form-control @error('purpose') is-invalid @enderror"
                      placeholder="Jelaskan mata kuliah, judul riset, atau keperluan penggunaan unit laboratorium...">{{ old('purpose') }}</textarea>
            @error('purpose')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="form-actions">
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <span>&#128221;</span> Ajukan Permohonan Peminjaman
            </button>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const eqSelect = document.getElementById('equipment_id');
        const loanDateInput = document.getElementById('loan_date');
        const returnDateInput = document.getElementById('expected_return_date');
        const feeFormula = document.getElementById('fee-formula');
        const feeTotal = document.getElementById('fee-total');

        function updateEstimate() {
            const selectedOption = eqSelect.options[eqSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                feeFormula.textContent = 'Pilih perangkat dan tanggal untuk menghitung';
                feeTotal.textContent = 'Rp 0';
                return;
            }

            const dailyFee = parseInt(selectedOption.getAttribute('data-fee')) || 0;
            const loanDateStr = loanDateInput.value;
            const returnDateStr = returnDateInput.value;

            if (!loanDateStr || !returnDateStr) {
                feeFormula.textContent = 'Tarif harian: Rp ' + dailyFee.toLocaleString('id-ID') + ' / hari (tentukan rentang tanggal)';
                feeTotal.textContent = 'Rp 0';
                return;
            }

            const start = new Date(loanDateStr);
            const end = new Date(returnDateStr);
            const diffTime = end - start;
            const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays < 0) {
                feeFormula.textContent = 'Tanggal rencana kembali tidak boleh sebelum tanggal pinjam!';
                feeTotal.textContent = 'Rp 0';
                return;
            }

            const duration = diffDays === 0 ? 1 : diffDays;
            const total = duration * dailyFee;

            feeFormula.textContent = duration + ' hari x Rp ' + dailyFee.toLocaleString('id-ID') + (diffDays > 7 ? ' (Melebihi batas 7 hari!)' : '');
            feeTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        eqSelect.addEventListener('change', updateEstimate);
        loanDateInput.addEventListener('input', updateEstimate);
        returnDateInput.addEventListener('input', updateEstimate);

        updateEstimate();
    });
</script>
@endpush
