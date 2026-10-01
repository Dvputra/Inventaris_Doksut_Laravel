@extends('layouts.app')

@section('title', 'Kelola Akun Pengguna')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 ring-1 ring-blue-500/20">
                    <i class="bi bi-people text-base"></i>
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Kelola Akun Pengguna</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Manajemen hak akses untuk Admin Sarpras Pusat, Program Keahlian, dan Unit Kerja Sekolah.</p>
        </div>
        <div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xs transition-colors">
                <i class="bi bi-person-plus-fill text-sm"></i>
                <span>Tambah Akun Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cari Nama / Email</label>
                <div class="relative rounded-xl shadow-xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-slate-400 text-xs"></i>
                    </div>
                    <input type="text" name="q" class="w-full pl-8 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" placeholder="Ketik nama atau email..." value="{{ request('q') }}">
                </div>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Peran / Role</label>
                <select name="role" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Semua Peran</option>
                    <option value="kepala_sekolah" {{ request('role') == 'kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                    <option value="sarpras" {{ request('role') == 'sarpras' ? 'selected' : '' }}>Sarpras (Pusat)</option>
                    <option value="jurusan" {{ request('role') == 'jurusan' ? 'selected' : '' }}>Jurusan / Unit Kerja</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Jurusan</label>
                <select name="jurusan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $j)
                        <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->kode }} - {{ $j->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs sm:text-sm shadow-xs transition-colors">
                    <i class="bi bi-filter"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center w-10 h-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Users -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 text-xs font-semibold uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Pengguna</th>
                        <th class="py-3 px-4">Alamat Email</th>
                        <th class="py-3 px-4">Role / Hak Akses</th>
                        <th class="py-3 px-4">Jurusan / Bengkel</th>
                        <th class="py-3 px-4">Tgl Dibuat</th>
                        <th class="py-3 px-4 text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-mono">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs {{ $u->isSarpras() ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === Auth::id())
                                                <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Akun Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs text-slate-600">
                                {{ $u->email }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($u->isKepalaSekolah())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        <i class="bi bi-mortarboard-fill text-xs text-amber-700"></i>
                                        <span>Kepala Sekolah</span>
                                    </span>
                                @elseif($u->isSarpras())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="bi bi-shield-check text-xs"></i>
                                        <span>Sarpras (Pusat)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        <i class="bi bi-building text-xs"></i>
                                        <span>Jurusan / Unit Kerja</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($u->jurusan)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-blue-600 text-white">
                                        {{ $u->jurusan->kode }}
                                    </span>
                                    <span class="text-xs text-slate-600 ml-1.5">{{ $u->jurusan->nama }}</span>
                                @else
                                    <span class="text-slate-400 text-xs">- (Semua Jurusan)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $u->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('users.edit', $u) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/60 transition-colors" title="Edit Akun">
                                        <i class="bi bi-pencil text-xs"></i>
                                    </a>
                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline"
                                              data-confirm="Apakah Anda yakin ingin menghapus akun pengguna {{ addslashes($u->name) }} ({{ $u->email }})?"
                                              data-confirm-title="Hapus Akun Pengguna"
                                              data-confirm-type="danger"
                                              data-confirm-btn="Ya, Hapus Akun">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200/60 transition-colors" title="Hapus Akun">
                                                <i class="bi bi-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <i class="bi bi-people text-2xl"></i>
                                </div>
                                <p class="text-slate-500 text-sm font-medium">Tidak ada akun pengguna yang sesuai dengan filter pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (Reflow on small screens < 768px) -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($users as $index => $u)
                <div class="p-4 hover:bg-slate-50/75 transition-colors">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $u->isSarpras() ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5 truncate">
                                    <span class="truncate">{{ $u->name }}</span>
                                    @if($u->id === Auth::id())
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Anda</span>
                                    @endif
                                </div>
                                <p class="font-mono text-xs text-slate-500 truncate mt-0.5">{{ $u->email }}</p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            @if($u->isSarpras())
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Sarpras
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    Jurusan
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs mt-2.5 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Unit / Bengkel:</span>
                            <span class="font-medium text-slate-800">{{ $u->jurusan ? '['.$u->jurusan->kode.'] '.$u->jurusan->nama : 'Pusat (Semua)' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-200/60">
                            <span class="text-slate-400">Dibuat: {{ $u->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div class="flex items-center justify-end gap-2 mt-3 pt-2.5 border-t border-slate-100">
                        <a href="{{ route('users.edit', $u) }}" class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-colors shadow-xs" title="Edit Akun">
                            <i class="bi bi-pencil text-xs"></i>
                        </a>
                        @if($u->id !== Auth::id())
                            <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline"
                                  data-confirm="Apakah Anda yakin ingin menghapus akun pengguna {{ addslashes($u->name) }} ({{ $u->email }})?"
                                  data-confirm-title="Hapus Akun Pengguna"
                                  data-confirm-type="danger"
                                  data-confirm-btn="Ya, Hapus Akun">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 transition-colors shadow-xs" title="Hapus Akun">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-12 px-4 text-center text-slate-400">
                    <i class="bi bi-people text-3xl block mb-2 text-slate-300"></i>
                    <p class="text-sm font-semibold text-slate-600">Tidak ada akun pengguna yang sesuai dengan filter pencarian.</p>
                </div>
            @endforelse
        </div>

        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
