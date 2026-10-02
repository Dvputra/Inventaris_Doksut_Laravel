<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JurusanController extends Controller
{
    /**
     * Tampilkan daftar seluruh unit kerja dan jurusan.
     */
    public function index(Request $request): View
    {
        $query = Jurusan::withCount(['items', 'users'])->orderBy('kode');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('kepala_bengkel', 'like', "%{$search}%");
            });
        }

        $jurusans = $query->get();

        return view('jurusans.index', compact('jurusans'));
    }

    /**
     * Formulir tambah unit kerja / jurusan baru.
     */
    public function create(): View
    {
        return view('jurusans.create');
    }

    /**
     * Simpan unit kerja / jurusan baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:jurusans,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'kepala_bengkel' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'create_user_account' => ['nullable', 'boolean'],
            'user_email' => ['nullable', 'required_if:create_user_account,1', 'email', 'max:255', 'unique:users,email'],
            'user_password' => ['nullable', 'required_if:create_user_account,1', 'string', 'min:6'],
        ], [
            'kode.required' => 'Kode / singkatan unit kerja wajib diisi.',
            'kode.unique' => 'Kode unit ini sudah terdaftar.',
            'nama.required' => 'Nama unit kerja / jurusan wajib diisi.',
            'user_email.required_if' => 'Email login wajib diisi jika membuat akun.',
            'user_email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'user_password.min' => 'Password minimal 6 karakter.',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));

        $jurusan = Jurusan::create([
            'kode' => $validated['kode'],
            'nama' => $validated['nama'],
            'kepala_bengkel' => $validated['kepala_bengkel'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        if ($request->boolean('create_user_account') && ! empty($validated['user_email'])) {
            $prefix = strtolower(explode('@', $validated['user_email'])[0]);
            $prefix = preg_replace('/[^a-z0-9_]/', '_', $prefix);
            $base = $prefix;
            $counter = 1;
            while (User::where('username', $prefix)->exists()) {
                $prefix = $base . $counter;
                $counter++;
            }

            User::create([
                'name' => 'Akun '.$jurusan->nama,
                'username' => $prefix,
                'email' => $validated['user_email'],
                'password' => Hash::make($validated['user_password']),
                'display_password' => $validated['user_password'],
                'role' => 'jurusan',
                'jurusan_id' => $jurusan->id,
            ]);
        }

        return redirect()->route('jurusans.index')
            ->with('success', "Unit Kerja / Jurusan '{$jurusan->nama}' ({$jurusan->kode}) berhasil ditambahkan.");
    }

    /**
     * Formulir edit data unit kerja / jurusan.
     */
    public function edit(Jurusan $jurusan): View
    {
        return view('jurusans.edit', compact('jurusan'));
    }

    /**
     * Perbarui data unit kerja / jurusan.
     */
    public function update(Request $request, Jurusan $jurusan): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', Rule::unique('jurusans', 'kode')->ignore($jurusan->id)],
            'nama' => ['required', 'string', 'max:255'],
            'kepala_bengkel' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ], [
            'kode.required' => 'Kode unit kerja wajib diisi.',
            'kode.unique' => 'Kode unit sudah terdaftar untuk unit lain.',
            'nama.required' => 'Nama unit kerja / jurusan wajib diisi.',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));

        $jurusan->update($validated);

        return redirect()->route('jurusans.index')
            ->with('success', "Data Unit Kerja '{$jurusan->nama}' ({$jurusan->kode}) berhasil diperbarui.");
    }

    /**
     * Update nama kepala bengkel / deskripsi unit kerja oleh pengguna unit tersebut sendiri.
     */
    public function updateMyUnit(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isJurusan() || ! $user->jurusan_id) {
            abort(403, 'Akses ditolak.');
        }

        $jurusan = $user->jurusan;

        $validated = $request->validate([
            'kepala_bengkel' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $jurusan->update($validated);

        return back()->with('success', 'Informasi Kepala Unit / Bengkel berhasil diperbarui.');
    }

    /**
     * Hapus data unit kerja / jurusan.
     */
    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        // Proteksi: jangan hapus unit SAR (Sarpras Pusat)
        if ($jurusan->kode === 'SAR') {
            return back()->with('error', 'Unit Sarpras Pusat & Fasilitas Umum (SAR) adalah komponen sistem dan tidak boleh dihapus.');
        }

        // Proteksi jika masih memiliki barang
        if ($jurusan->items()->exists()) {
            $itemCount = $jurusan->items()->count();

            return back()->with('error', "Gagal menghapus unit '{$jurusan->nama}': Unit ini masih memiliki {$itemCount} barang inventaris. Silakan hapus atau pindahkan barang terlebih dahulu di menu Data Barang.");
        }

        // Proteksi jika masih memiliki akun pengguna
        if ($jurusan->users()->exists()) {
            $userCount = $jurusan->users()->count();

            return back()->with('error', "Gagal menghapus unit '{$jurusan->nama}': Unit ini masih terhubung dengan {$userCount} akun login. Silakan hapus atau ubah penempatan unit akun tersebut terlebih dahulu di menu Kelola Akun.");
        }

        // Proteksi jika masih memiliki riwayat peminjaman
        if ($jurusan->borrowings()->exists()) {
            $borrowCount = $jurusan->borrowings()->count();

            return back()->with('error', "Gagal menghapus unit '{$jurusan->nama}': Masih terdapat {$borrowCount} riwayat peminjaman terkait unit ini.");
        }

        // Proteksi jika masih memiliki usulan pengadaan
        if ($jurusan->procurements()->exists()) {
            $procurementCount = $jurusan->procurements()->count();

            return back()->with('error', "Gagal menghapus unit '{$jurusan->nama}': Masih terdapat {$procurementCount} berkas usulan pengadaan terkait unit ini.");
        }

        $namaJurusan = $jurusan->nama;
        $jurusan->delete();

        return redirect()->route('jurusans.index')
            ->with('success', "Unit Kerja / Jurusan '{$namaJurusan}' berhasil dihapus.");
    }
}
