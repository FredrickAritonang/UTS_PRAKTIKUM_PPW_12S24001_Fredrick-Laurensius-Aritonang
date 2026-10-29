@extends('layouts.app')

@section('title', 'Riwayat Peminjaman Inventaris')

@section('content')

<section class="history-section">
    <div class="section-header">
        <div>
            <h2 class="section-title">Riwayat Pengajuan Peminjaman Laboratorium</h2>
            <p class="section-subtitle">Daftar transaksi pengajuan peminjaman perangkat laboratorium terpadu Del.</p>
        </div>
        <div>
            <a href="{{ route('loans.create') }}" class="btn btn-primary">
                <span>&#10133;</span> Ajukan Peminjaman Baru
            </a>
        </div>
    </div>

    @if ($loans->count() > 0)
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode & Perangkat</th>
                        <th>Peminjam (NIM)</th>
                        <th>Surel Mahasiswa</th>
                        <th>Periode Peminjaman</th>
                        <th>Estimasi Biaya</th>
                        <th>Status</th>
                        <th>Tanggal Pengajuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loans as $index => $loan)
                        <tr>
                            <td>{{ $loans->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $loan->equipment->asset_code ?? 'N/A' }}</strong><br>
                                <span style="color: #334155; font-size: 0.825rem;">
                                    {{ $loan->equipment->name ?? 'Perangkat tidak ditemukan' }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $loan->borrower_name }}</strong><br>
                                <span style="color: var(--text-muted); font-size: 0.8rem;">NIM: {{ $loan->borrower_nim }}</span>
                            </td>
                            <td>
                                <a href="mailto:{{ $loan->borrower_email }}" style="color: #2563eb; text-decoration: none;">
                                    {{ $loan->borrower_email }}
                                </a>
                            </td>
                            <td>
                                <div><strong>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}</strong> s/d</div>
                                <div><strong>{{ \Carbon\Carbon::parse($loan->expected_return_date)->format('d M Y') }}</strong></div>
                            </td>
                            <td>
                                <strong style="color: #1e3a8a;">
                                    Rp {{ number_format($loan->total_estimated_fee, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td>
                                @if ($loan->status === 'disetujui' || $loan->status === 'selesai')
                                    <span class="status-badge badge-tersedia">{{ ucfirst($loan->status) }}</span>
                                @elseif ($loan->status === 'diajukan')
                                    <span class="status-badge badge-dipinjam">{{ ucfirst($loan->status) }}</span>
                                @else
                                    <span class="status-badge badge-dalam_perbaikan">{{ ucfirst($loan->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $loan->created_at ? $loan->created_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $loans->links('pagination.custom') }}
        </div>
    @else
        <div class="empty-state">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">&#128220;</div>
            <h3>Belum Ada Transaksi Peminjaman</h3>
            <p>Belum ada riwayat permohonan peminjaman aset laboratorium yang tercatat.</p>
            <a href="{{ route('loans.create') }}" class="btn btn-primary">

            </a>
        </div>
    @endif
</section>
@endsection
