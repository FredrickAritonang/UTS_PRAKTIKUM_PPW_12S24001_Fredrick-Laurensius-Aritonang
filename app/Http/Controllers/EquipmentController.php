<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EquipmentController extends Controller
{

    public function index(Request $request)
    {
        $query = Equipment::with('category');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('specification', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('status') && $request->input('status') !== 'semua') {
            $query->where('status', $request->input('status'));
        }

        
        $equipments = $query->orderBy('id', 'asc')
            ->paginate(6)
            ->appends($request->query());

        $categories = Category::orderBy('name')->get();

        return view('equipments.index', compact('equipments', 'categories'));
    }

    
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('equipments.create', compact('categories'));
    }

    
    public function store(Request $request)
    {
        $rules = [
            'asset_code'       => 'required|string|max:20|unique:equipments,asset_code',
            'name'             => 'required|string|min:5|max:150',
            'category_id'      => 'required|exists:categories,id',
            'specification'    => 'required|string',
            'condition'        => 'required|in:baik,rusak_ringan,rusak_berat',
            'daily_rental_fee' => 'required|numeric|min:0',
            'image'            => 'required|image|mimes:jpeg,png,webp|max:2048',
        ];

        $messages = [
            'asset_code.required'       => 'Kode aset wajib diisi.',
            'asset_code.unique'         => 'Kode aset inventaris sudah terdaftar di sistem.',
            'asset_code.max'            => 'Kode aset maksimal 20 karakter.',
            'name.required'             => 'Nama perangkat wajib diisi.',
            'name.min'                  => 'Nama perangkat minimal 5 karakter.',
            'name.max'                  => 'Nama perangkat maksimal 150 karakter.',
            'category_id.required'      => 'Kategori perangkat wajib dipilih.',
            'category_id.exists'        => 'Kategori yang dipilih tidak valid.',
            'specification.required'    => 'Spesifikasi teknis wajib diisi.',
            'condition.required'        => 'Kondisi fisik perangkat wajib dipilih.',
            'condition.in'              => 'Pilihan kondisi fisik tidak valid.',
            'daily_rental_fee.required' => 'Tarif kompensasi harian wajib diisi.',
            'daily_rental_fee.numeric'  => 'Tarif kompensasi harian harus berupa angka.',
            'daily_rental_fee.min'      => 'Tarif kompensasi harian tidak boleh bernilai negatif.',
            'image.required'            => 'Foto fisik perangkat wajib diunggah.',
            'image.image'               => 'Berkas harus berupa gambar.',
            'image.mimes'               => 'Format berkas foto harus berupa JPEG, PNG, atau WEBP.',
            'image.max'                 => 'Ukuran berkas foto maksimal 2048 KB (2 MB).',
        ];

        $validated = $request->validate($rules, $messages);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $codeSlug = Str::slug($request->asset_code, '_');
            $fileName = time() . '_' . $codeSlug . '.' . $extension;
            $imagePath = $file->storeAs('equipments', $fileName, 'public');
        }

        $equipment = Equipment::create([
            'category_id'      => $validated['category_id'],
            'asset_code'       => strtoupper(trim($validated['asset_code'])),
            'name'             => trim($validated['name']),
            'specification'    => trim($validated['specification']),
            'condition'        => $validated['condition'],
            'status'           => 'tersedia',
            'daily_rental_fee' => (int) $validated['daily_rental_fee'],
            'image_path'       => $imagePath,
        ]);

        return redirect()->route('equipments.index')->with(
            'success',
            "[SUKSES] Perangkat '{$equipment->name}' (Kode: {$equipment->asset_code}) berhasil didaftarkan!"
        );
    }

    /**
     * Display the specified equipment details.
     */
    public function show(string $id)
    {
        $equipment = Equipment::with(['category', 'loanRequests' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        return view('equipments.show', compact('equipment'));
    }
}
