<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\LoanRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DelLeatsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run seeders for clean test state
        $this->seed();
    }

    /**
     * Test Case 1: Pendaftaran Inventaris Baru yang Valid (Skenario Positif)
     */
    public function test_kasus_uji_1_pendaftaran_inventaris_baru_yang_valid(): void
    {
        Storage::fake('public');

        // Create a valid PNG byte stream so fileinfo identifies it as image/png without needing GD extension
        $validPngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $file = UploadedFile::fake()->createWithContent('raspberry_pi4_kit.png', $validPngBytes);

        $response = $this->post(route('equipments.store'), [
            'category_id'      => 2,
            'asset_code'       => 'EQ-IOT-005',
            'name'             => 'Raspberry Pi 4 Model B (8GB RAM) Starter Kit',
            'specification'    => 'Quad-core Cortex-A72 @1.5GHz, 8GB LPDDR4, MicroSD 64GB Class 10, Casing Alumunium Aktif Fan, Power Supply Type-C Resmi',
            'condition'        => 'baik',
            'daily_rental_fee' => 15000,
            'image'            => $file,
        ]);

        // 1. Respons HTTP & Pengalihan
        $response->assertStatus(302);
        $response->assertRedirect(route('equipments.index'));

        // 2. Flash Session Notification
        $response->assertSessionHas(
            'success',
            "[SUKSES] Perangkat 'Raspberry Pi 4 Model B (8GB RAM) Starter Kit' (Kode: EQ-IOT-005) berhasil didaftarkan!"
        );

        // 3. Verifikasi Data pada Basis Data
        $this->assertDatabaseHas('equipments', [
            'id'               => 7,
            'category_id'      => 2,
            'asset_code'       => 'EQ-IOT-005',
            'name'             => 'Raspberry Pi 4 Model B (8GB RAM) Starter Kit',
            'condition'        => 'baik',
            'status'           => 'tersedia',
            'daily_rental_fee' => 15000,
        ]);

        $created = Equipment::find(7);
        $this->assertNotNull($created);
        $this->assertStringContainsString('eq_iot_005', $created->image_path);

        // 4. Tampilan Visual pada Halaman Katalog Aset
        $catalogResponse = $this->get(route('equipments.index'));
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee('EQ-IOT-005');
        $catalogResponse->assertSee('Raspberry Pi 4 Model B (8GB RAM) Starter Kit');
        $catalogResponse->assertSee('Internet of Things');
        $catalogResponse->assertSee('Tersedia');
        $catalogResponse->assertSee('Ajukan Peminjaman');
    }

    /**
     * Test Case 2: Pengajuan Peminjaman dengan Data Tidak Valid (Skenario Negatif)
     */
    public function test_kasus_uji_2_pengajuan_peminjaman_data_tidak_valid(): void
    {
        // Freeze test date to 2026-10-30 for consistent assertion of date logic
        Carbon::setTestNow('2026-10-30');

        $response = $this->from(route('loans.create'))->post(route('loans.store'), [
            'equipment_id'         => 1, // Dell PowerEdge T40 Server (status: 'dipinjam')
            'borrower_name'        => 'Jo', // < 3 chars
            'borrower_nim'         => '12S23', // not 8 chars
            'borrower_email'       => 'johannes.del.ac.id', // invalid email
            'loan_date'            => '2026-10-20', // past date
            'expected_return_date' => '2026-11-20', // 31 days (> 7 days)
            'purpose'              => 'Praktikum',
        ]);

        // 1. Respons HTTP & Pengalihan
        $response->assertStatus(302);
        $response->assertRedirect(route('loans.create'));

        // 2. Flash Alert & Penolakan Bisnis
        $response->assertSessionHas(
            'error',
            '[GAGAL] Pengajuan tidak dapat diproses. Silakan periksa kembali seluruh masukan data Anda.'
        );

        // 3. Pesan Galat Spesifik di Bawah Masing-masing Elemen Input
        $response->assertSessionHasErrors([
            'equipment_id'         => 'Perangkat Dell PowerEdge T40 Server saat ini sedang dipinjam dan tidak tersedia.',
            'borrower_name'        => 'Nama peminjam wajib diisi minimal 3 karakter.',
            'borrower_nim'         => 'Format NIM harus terdiri dari tepat 8 digit angka/huruf institusi.',
            'borrower_email'       => 'Format alamat surel yang Anda masukkan tidak valid.',
            'loan_date'            => 'Tanggal peminjaman tidak boleh sebelum tanggal hari ini.',
            'expected_return_date' => 'Durasi peminjaman maksimal adalah 7 hari kalender.',
        ]);

        // 4. Retensi Nilai Input Form (Form State Retention)
        $this->assertEquals('Jo', session('_old_input.borrower_name'));
        $this->assertEquals('12S23', session('_old_input.borrower_nim'));
        $this->assertEquals('johannes.del.ac.id', session('_old_input.borrower_email'));
    }

    /**
     * Test Case 3: Pencarian & Pemfilteran Katalog Aset (HTTP GET Query String)
     */
    public function test_kasus_uji_3_pencarian_dan_pemfilteran_katalog(): void
    {
        $response = $this->get('/equipments?search=drone&category_id=3&status=tersedia&page=1');

        $response->assertStatus(200);

        // 1. Keterangan Ringkasan
        $response->assertSee('Menampilkan 2 dari 2 hasil pencarian untuk kata kunci "<strong>drone</strong>"', false);

        // 2. Konten Kartu Aset yang Dirender
        // Kartu 1
        $response->assertSee('EQ-DRN-001');
        $response->assertSee('DJI Mavic Air 2 Fly More Combo');
        $response->assertSee('Rp 50.000');

        // Kartu 2
        $response->assertSee('EQ-DRN-003');
        $response->assertSee('FPV Racing Drone BetaFPV Cetus Pro');
        $response->assertSee('Rp 25.000');

        // Aksi tombol
        $response->assertSee('Detail Spesifikasi');
        $response->assertSee('Ajukan Peminjaman');

        // 3. Tautan Paginasi menyertakan parameter query string
        $response->assertSee('search=drone');
        $response->assertSee('category_id=3');
        $response->assertSee('status=tersedia');
    }

    /**
     * Test Case Positive Loan Request & Equipment Status Mutation
     */
    public function test_positive_loan_request_and_status_mutation(): void
    {
        Carbon::setTestNow('2026-10-30');

        // EQ-SRV-002 is 'tersedia', fee: 60000
        $equipment = Equipment::find(2);
        $this->assertEquals('tersedia', $equipment->status);

        $response = $this->post(route('loans.store'), [
            'equipment_id'         => 2,
            'borrower_name'        => 'Fredrick Laurensius Aritonang',
            'borrower_nim'         => '12S24001',
            'borrower_email'       => 'fredrick@students.del.ac.id',
            'loan_date'            => '2026-10-30',
            'expected_return_date' => '2026-11-03', // 4 days duration -> 4 * 60,000 = 240,000
            'purpose'              => 'Pengujian performa server jaringan lokal',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('loans.index'));
        $response->assertSessionHas('success');

        // Verify loan in DB
        $this->assertDatabaseHas('loan_requests', [
            'equipment_id'        => 2,
            'borrower_name'       => 'Fredrick Laurensius Aritonang',
            'borrower_nim'        => '12S24001',
            'total_estimated_fee' => 240000,
            'status'              => 'diajukan',
        ]);

        // Verify equipment status mutated to 'dipinjam'
        $equipment->refresh();
        $this->assertEquals('dipinjam', $equipment->status);
    }
}
