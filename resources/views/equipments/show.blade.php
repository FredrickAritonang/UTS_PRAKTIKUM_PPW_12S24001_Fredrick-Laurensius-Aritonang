@extends('layouts.app')

@section('title', 'Detail Perangkat - ' . $equipment->name)

@section('content')

<section class="detail-section">
    <div class="section-header">
        <div>
            <div class="card-code">{{ $equipment->asset_code }}</div>
            <h2 class="section-title">{{ $equipment->name }}</h2>
            <p class="section-subtitle">Kategori: {{ $equipment->category->name ?? 'Umum' }} ({{ $equipment->category->code ?? '-' }})</p>
        </div>
        <div>
            <a href="{{ route('equipments.index') }}" class="btn btn-secondary">
                &larr;
            </a>
        </div>
    </div>

    <div class="detail-grid">

        <div>
            <div class="detail-image-box">
                @if ($equipment->image_path)
                    <img src="{{ asset('storage/' . $equipment->image_path) }}" alt="{{ $equipment->name }}">
                @else
                    <div style="height: 320px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-weight: 500;">

                    </div>
                @endif
            </div>

            <div style="margin-top: 1.5rem; text-align: center;">
                @if ($equipment->status === 'tersedia')
                    <a href="{{ route('loans.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-primary btn-block" style="padding: 0.8rem 1.5rem; font-size: 1rem;">
                        <span>&#128221;</span>
                    </a>
                @else
                    <a href="{{ route('loans.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-secondary btn-block" style="padding: 0.8rem 1.5rem; font-size: 0.95rem;">
                        <span>&#9888;</span>
                    </a>
                @endif
            </div>
        </div>


        <div>
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.75rem;">

            </h3>

            <table class="spec-table">
                <tbody>
                    <tr>
                        <th></th>
                        <td>
                            @if ($equipment->status === 'tersedia')
                                <span class="status-badge badge-tersedia"></span>
                            @elseif ($equipment->status === 'dipinjam')
                                <span class="status-badge badge-dipinjam"></span>
                            @else
                                <span class="status-badge badge-dalam_perbaikan"></span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <strong>{{ ucfirst(str_replace('_', ' ', $equipment->condition)) }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <strong style="color: #1e3a8a; font-size: 1.1rem;">
                                Rp {{ number_format($equipment->daily_rental_fee, 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>{{ $equipment->category->name }} ({{ $equipment->category->code }})</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>{{ $equipment->created_at ? $equipment->created_at->format('d M Y, H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>{{ $equipment->updated_at ? $equipment->updated_at->format('d M Y, H:i') : '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">

                </h4>
                <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1rem; font-size: 0.9rem; line-height: 1.6; white-space: pre-line;">
{{ $equipment->specification }}
                </div>
            </div>
        </div>
    </div>


    <div style="margin-top: 2.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.75rem;">

        </h3>

        @if ($equipment->loanRequests && $equipment->loanRequests->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Peminjam</th>
                            <th>NIM</th>
                            <th>Tanggal Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Total Biaya</th>
                            <th>Status Pengajuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($equipment->loanRequests as $loan)
                            <tr>
                                <td>#{{ $loan->id }}</td>
                                <td><strong>{{ $loan->borrower_name }}</strong></td>
                                <td>{{ $loan->borrower_nim }}</td>
                                <td>{{ \Carbon\Carbon::parse($loan->loan_date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($loan->expected_return_date)->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($loan->total_estimated_fee, 0, ',', '.') }}</td>
                                <td>
                                    <span class="status-badge {{ $loan->status === 'disetujui' ? 'badge-tersedia' : ($loan->status === 'diajukan' ? 'badge-dipinjam' : 'badge-dalam_perbaikan') }}">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;">

            </p>
        @endif
    </div>
</section>
@endsection
