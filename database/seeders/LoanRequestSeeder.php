<?php

namespace Database\Seeders;

use App\Models\LoanRequest;
use Illuminate\Database\Seeder;

class LoanRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loans = [
            [
                'id' => 1,
                'equipment_id' => 1,
                'borrower_name' => 'Saut Daniel Martua Simanjuntak',
                'borrower_nim' => '12S22015',
                'borrower_email' => 'saut.simanjuntak@students.del.ac.id',
                'loan_date' => '2026-10-25',
                'expected_return_date' => '2026-10-31',
                'purpose' => 'Penelitian Komputasi Paralel dan Cloud Lab untuk Tugas Akhir',
                'status' => 'disetujui',
                'total_estimated_fee' => 450000,
            ],
        ];

        foreach ($loans as $loan) {
            LoanRequest::updateOrCreate(['id' => $loan['id']], $loan);
        }
    }
}
