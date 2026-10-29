<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\LoanRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    
    public function index()
    {
        $loans = LoanRequest::with(['equipment.category'])
            ->latest()
            ->paginate(10);

        return view('loans.index', compact('loans'));
    }

    
    public function create(Request $request)
    {
        $equipments = Equipment::with('category')
            ->orderBy('name')
            ->get();

        $selectedEquipmentId = $request->query('equipment_id');

        return view('loans.create', compact('equipments', 'selectedEquipmentId'));
    }

    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'equipment_id'         => ['required', 'exists:equipments,id'],
            'borrower_name'        => ['required', 'string', 'min:3', 'max:100'],
            'borrower_nim'         => ['required', 'string', 'regex:/^[a-zA-Z0-9]{8}$/'],
            'borrower_email'       => ['required', 'email', 'max:100'],
            'loan_date'            => ['required', 'date', 'after_or_equal:today'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:loan_date'],
            'purpose'              => ['required', 'string', 'max:1000'],
        ], [
            'equipment_id.required'               => 'Pilihan perangkat wajib dipilih.',
            'equipment_id.exists'                 => 'Perangkat yang dipilih tidak terdaftar di sistem.',
            'borrower_name.required'              => 'Nama peminjam wajib diisi minimal 3 karakter.',
            'borrower_name.min'                   => 'Nama peminjam wajib diisi minimal 3 karakter.',
            'borrower_nim.required'               => 'Format NIM harus terdiri dari tepat 8 digit angka/huruf institusi.',
            'borrower_nim.regex'                  => 'Format NIM harus terdiri dari tepat 8 digit angka/huruf institusi.',
            'borrower_email.required'             => 'Format alamat surel yang Anda masukkan tidak valid.',
            'borrower_email.email'                => 'Format alamat surel yang Anda masukkan tidak valid.',
            'loan_date.required'                  => 'Tanggal peminjaman wajib diisi.',
            'loan_date.after_or_equal'            => 'Tanggal peminjaman tidak boleh sebelum tanggal hari ini.',
            'expected_return_date.required'       => 'Tanggal rencana kembali wajib diisi.',
            'expected_return_date.after_or_equal' => 'Tanggal rencana kembali tidak boleh sebelum tanggal peminjaman.',
            'purpose.required'                    => 'Tujuan peminjaman wajib diisi.',
        ]);

        $validator->after(function ($validator) use ($request) {
            
            if ($request->filled('equipment_id')) {
                $equipment = Equipment::find($request->equipment_id);
                if ($equipment && $equipment->status !== 'tersedia') {
                    $statusLabel = $equipment->status === 'dalam_perbaikan' ? 'dalam perbaikan' : $equipment->status;
                    $validator->errors()->add(
                        'equipment_id',
                        "Perangkat {$equipment->name} saat ini sedang {$statusLabel} dan tidak tersedia."
                    );
                }
            }

            
            if ($request->filled('loan_date') && $request->filled('expected_return_date')) {
                try {
                    $loanDate = Carbon::parse($request->loan_date);
                    $returnDate = Carbon::parse($request->expected_return_date);

                    if ($returnDate->gte($loanDate)) {
                        $diffInDays = $loanDate->diffInDays($returnDate);
                        if ($diffInDays > 7) {
                            $validator->errors()->add(
                                'expected_return_date',
                                'Durasi peminjaman maksimal adalah 7 hari kalender.'
                            );
                        }
                    }
                } catch (\Exception $e) {

                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', '[GAGAL] Pengajuan tidak dapat diproses. Silakan periksa kembali seluruh masukan data Anda.');
        }

        $equipment = Equipment::findOrFail($request->equipment_id);
        $loanDate = Carbon::parse($request->loan_date);
        $returnDate = Carbon::parse($request->expected_return_date);
        $diff = $loanDate->diffInDays($returnDate);
        $calculatedDays = $diff == 0 ? 1 : $diff;
        $totalEstimatedFee = $calculatedDays * $equipment->daily_rental_fee;

        $loan = LoanRequest::create([
            'equipment_id'        => $equipment->id,
            'borrower_name'       => trim($request->borrower_name),
            'borrower_nim'        => strtoupper(trim($request->borrower_nim)),
            'borrower_email'      => trim($request->borrower_email),
            'loan_date'           => $request->loan_date,
            'expected_return_date'=> $request->expected_return_date,
            'purpose'             => trim($request->purpose),
            'status'              => 'diajukan',
            'total_estimated_fee' => $totalEstimatedFee,
        ]);


        $equipment->update(['status' => 'dipinjam']);

        return redirect()->route('loans.index')->with(
            'success',
            "[SUKSES] Pengajuan peminjaman untuk perangkat '{$equipment->name}' berhasil dibuat! Estimasi biaya: Rp " . number_format($totalEstimatedFee, 0, ',', '.')
        );
    }
}

