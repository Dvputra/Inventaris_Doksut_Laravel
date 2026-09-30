@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm">
        {{-- Mobile View: Simple Next / Previous --}}
        <div class="flex items-center justify-between w-full sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed font-medium text-xs">
                    <i class="bi bi-chevron-left text-[10px]"></i>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs shadow-xs transition-colors">
                    <i class="bi bi-chevron-left text-[10px]"></i>
                    <span>Sebelumnya</span>
                </a>
            @endif

            <span class="text-xs text-slate-500 font-medium">
                Hal {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs shadow-xs transition-colors">
                    <span>Berikutnya</span>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-400 cursor-not-allowed font-medium text-xs">
                    <span>Berikutnya</span>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                </span>
            @endif
        </div>

        {{-- Desktop View: Full Results Info & Page Numbers --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full gap-4">
            <div>
                <p class="text-xs sm:text-sm text-slate-600">
                    Menampilkan
                    <span class="font-bold text-slate-900">{{ $paginator->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-bold text-slate-900">{{ $paginator->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-bold text-slate-900">{{ $paginator->total() }}</span>
                    total hasil
                </p>
            </div>

            <div>
                <ul class="inline-flex items-center -space-x-px rounded-xl shadow-xs overflow-hidden border border-slate-200 bg-white">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li>
                            <span class="inline-flex items-center justify-center w-9 h-9 text-slate-300 bg-slate-50/50 cursor-not-allowed border-r border-slate-200" aria-disabled="true" aria-label="Sebelumnya">
                                <i class="bi bi-chevron-left text-xs"></i>
                            </span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors border-r border-slate-200" aria-label="Sebelumnya">
                                <i class="bi bi-chevron-left text-xs"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li>
                                <span class="inline-flex items-center justify-center w-9 h-9 text-slate-400 font-semibold border-r border-slate-200 bg-slate-50/50 text-xs">
                                    {{ $element }}
                                </span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li aria-current="page">
                                        <span class="inline-flex items-center justify-center w-9 h-9 text-white font-bold bg-blue-600 border-r border-blue-600 text-xs shadow-inner">
                                            {{ $page }}
                                        </span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-slate-700 hover:bg-slate-50 hover:text-blue-600 font-semibold text-xs border-r border-slate-200 transition-colors">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors" aria-label="Berikutnya">
                                <i class="bi bi-chevron-right text-xs"></i>
                            </a>
                        </li>
                    @else
                        <li>
                            <span class="inline-flex items-center justify-center w-9 h-9 text-slate-300 bg-slate-50/50 cursor-not-allowed" aria-disabled="true" aria-label="Berikutnya">
                                <i class="bi bi-chevron-right text-xs"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endif
