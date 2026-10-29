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

        $this->seed();
    }


    public function test_kasus_uji_1_pendaftaran_inventaris_baru_yang_valid(): void
    {
        Storage::fake('public');


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


        $response->assertStatus(302);
        $response->assertRedirect(route('equipments.index'));


        $response->assertSessionHas(
            'success',
            "[SUKSES] Perangkat 'Raspberry Pi 4 Model B (8GB RAM) Starter Kit' (Kode: EQ-IOT-005) berhasil didaftarkan!"
        );


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


        $catalogResponse = $this->get(route('equipments.index'));
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee('EQ-IOT-005');
        $catalogResponse->assertSee('Raspberry Pi 4 Model B (8GB RAM) Starter Kit');
        $catalogResponse->assertSee('Internet of Things');
        $catalogResponse->assertSee('Tersedia');
        $catalogResponse->assertSee('Ajukan Peminjaman');
    }


    public function test_kasus_uji_2_pengajuan_peminjaman_data_tidak_valid(): void
    {

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


        $response->assertStatus(302);
        $response->assertRedirect(route('loans.create'));


        $response->assertSessionHas(
            'error',
            '[GAGAL] Pengajuan tidak dapat diproses. Silakan periksa kembali seluruh masukan data Anda.'
        );


        $response->assertSessionHasErrors([
            'equipment_id'         => 'Perangkat Dell PowerEdge T40 Server saat ini sedang dipinjam dan tidak tersedia.',
            'borrower_name'        => 'Nama peminjam wajib diisi minimal 3 karakter.',
            'borrower_nim'         => 'Format NIM harus terdiri dari tepat 8 digit angka/huruf institusi.',
            'borrower_email'       => 'Format alamat surel yang Anda masukkan tidak valid.',
            'loan_date'            => 'Tanggal peminjaman tidak boleh sebelum tanggal hari ini.',
            'expected_return_date' => 'Durasi peminjaman maksimal adalah 7 hari kalender.',
        ]);


        $this->assertEquals('Jo', session('_old_input.borrower_name'));
        $this->assertEquals('12S23', session('_old_input.borrower_nim'));
        $this->assertEquals('johannes.del.ac.id', session('_old_input.borrower_email'));
    }


    public function test_kasus_uji_3_pencarian_dan_pemfilteran_katalog(): void
    {
        $response = $this->get('/equipments?search=drone&category_id=3&status=tersedia&page=1');

        $response->assertStatus(200);


        $response->assertSee('Menampilkan 2 dari 2 hasil pencarian untuk kata kunci "<strong>drone</strong>"', false);


        $response->assertSee('EQ-DRN-001');
        $response->assertSee('DJI Mavic Air 2 Fly More Combo');
        $response->assertSee('Rp 50.000');


        $response->assertSee('EQ-DRN-003');
        $response->assertSee('FPV Racing Drone BetaFPV Cetus Pro');
        $response->assertSee('Rp 25.000');


        $response->assertSee('Detail Spesifikasi');
        $response->assertSee('Ajukan Peminjaman');


        $response->assertSee('search=drone');
        $response->assertSee('category_id=3');
        $response->assertSee('status=tersedia');
    }


    public function test_positive_loan_request_and_status_mutation(): void
    {
        Carbon::setTestNow('2026-10-30');


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


        $this->assertDatabaseHas('loan_requests', [
            'equipment_id'        => 2,
            'borrower_name'       => 'Fredrick Laurensius Aritonang',
            'borrower_nim'        => '12S24001',
            'total_estimated_fee' => 240000,
            'status'              => 'diajukan',
        ]);


        $equipment->refresh();
        $this->assertEquals('dipinjam', $equipment->status);
    }
}
