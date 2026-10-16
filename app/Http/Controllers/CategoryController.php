<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar seluruh kategori barang inventaris.
     */
    public function index(Request $request): View
    {
        $query = Category::withCount('items')->orderBy('nama');

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $categories = $query->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Simpan kategori barang baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'min:2', 'max:10', 'unique:categories,kode'],
            'nama' => ['required', 'string', 'max:255', 'unique:categories,nama'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'kode.required' => 'Kode / singkatan kategori wajib diisi.',
            'kode.min' => 'Kode kategori minimal 2 karakter.',
            'kode.max' => 'Kode kategori maksimal 10 karakter.',
            'kode.unique' => 'Kode kategori ini sudah digunakan oleh kategori lain.',
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori ini sudah terdaftar.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['nama'] = trim($validated['nama']);
        $validated['keterangan'] = $validated['keterangan'] ? trim($validated['keterangan']) : null;

        $category = Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$category->nama}' ({$category->kode}) berhasil ditambahkan.");
    }

    /**
     * Perbarui data kategori barang.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'min:2', 'max:10', Rule::unique('categories', 'kode')->ignore($category->id)],
            'nama' => ['required', 'string', 'max:255', Rule::unique('categories', 'nama')->ignore($category->id)],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'kode.required' => 'Kode / singkatan kategori wajib diisi.',
            'kode.min' => 'Kode kategori minimal 2 karakter.',
            'kode.max' => 'Kode kategori maksimal 10 karakter.',
            'kode.unique' => 'Kode kategori ini sudah digunakan oleh kategori lain.',
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori ini sudah terdaftar.',
            'keterangan.max' => 'Keterangan maksimal 500 karakter.',
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['nama'] = trim($validated['nama']);
        $validated['keterangan'] = $validated['keterangan'] ? trim($validated['keterangan']) : null;

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$category->nama}' ({$category->kode}) berhasil diperbarui.");
    }

    /**
     * Hapus kategori barang.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Proteksi jika kategori masih digunakan oleh barang
        $itemsCount = $category->items()->count();
        if ($itemsCount > 0) {
            return back()->with('error', "Gagal menghapus kategori '{$category->nama}': Masih terdapat {$itemsCount} barang inventaris yang terdaftar menggunakan kategori ini. Silakan ubah kategori barang tersebut terlebih dahulu.");
        }

        $nama = $category->nama;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$nama}' berhasil dihapus.");
    }
}
