<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh akun pengguna.
     */
    public function index(Request $request): View
    {
        $query = User::with('jurusan')->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $jurusans = Jurusan::all();

        return view('users.index', compact('users', 'jurusans'));
    }

    /**
     * Formulir pembuatan akun baru.
     */
    public function create(): View
    {
        $jurusans = Jurusan::all();

        return view('users.create', compact('jurusans'));
    }

    /**
     * Simpan akun baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:sarpras,jurusan,kepala_sekolah'],
            'jurusan_id' => [
                'nullable',
                Rule::requiredIf($request->role === 'jurusan'),
                'exists:jurusans,id',
            ],
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'jurusan_id.required' => 'Untuk akun Jurusan / Unit Kerja, penempatan unit wajib dipilih.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        if (in_array($validated['role'], ['sarpras', 'kepala_sekolah'], true)) {
            $validated['jurusan_id'] = null;
        }

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', "Akun '{$validated['name']}' ({$validated['email']}) berhasil ditambahkan.");
    }

    /**
     * Tampilkan formulir edit akun.
     */
    public function edit(User $user): View
    {
        $jurusans = Jurusan::all();

        return view('users.edit', compact('user', 'jurusans'));
    }

    /**
     * Perbarui data akun pengguna.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:sarpras,jurusan,kepala_sekolah'],
            'jurusan_id' => [
                'nullable',
                Rule::requiredIf($request->role === 'jurusan'),
                'exists:jurusans,id',
            ],
        ], [
            'name.required' => 'Nama lengkap pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'jurusan_id.required' => 'Untuk akun Jurusan / Unit Kerja, penempatan unit wajib dipilih.',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (in_array($validated['role'], ['sarpras', 'kepala_sekolah'], true)) {
            $validated['jurusan_id'] = null;
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', "Data akun '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(User $user, Request $request): RedirectResponse
    {
        // Proteksi: jangan hapus akun sendiri yang sedang login
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang Anda gunakan untuk login.');
        }

        $namaUser = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Akun '{$namaUser}' berhasil dihapus.");
    }
}
