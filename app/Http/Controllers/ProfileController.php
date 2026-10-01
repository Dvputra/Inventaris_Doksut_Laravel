<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna yang sedang login.
     */
    public function show(Request $request): View
    {
        $user = $request->user()->load('jurusan');

        return view('profile.show', compact('user'));
    }

    /**
     * Perbarui data profil (Nama, Email, dan informasi penanggung jawab jika jurusan/unit).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'kepala_bengkel' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Jika akun adalah jurusan/unit kerja dan mengirim pembaruan penanggung jawab
        if ($user->jurusan_id && $user->jurusan) {
            $user->jurusan->update([
                'kepala_bengkel' => $validated['kepala_bengkel'] ?? $user->jurusan->kepala_bengkel,
                'deskripsi' => $validated['deskripsi'] ?? $user->jurusan->deskripsi,
            ]);
        }

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Perbarui kata sandi (password).
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak cocok.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }
}
