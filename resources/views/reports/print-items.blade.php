<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Inventaris Aset & Sarpras - SMK Dr. Sutomo Temanggung</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Tinos:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f1f5f9;
            color: #000000;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 20px 0;
        }

        .paper {
            background: #ffffff;
            width: 297mm; /* Standard A4 Landscape */
            min-height: 210mm;
            margin: 0 auto;
            padding: 15mm 15mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 2px;
            color: #000000;
        }

        /* Kop Surat Resmi (Template DOCX) */
        .kop-surat {
            padding-bottom: 4px;
            margin-bottom: 12px;
            width: 100%;
        }

        .kop-img {
            width: 100%;
            height: auto;
            display: block;
        }

        .report-title {
            text-align: center;
            margin: 10px 0 16px;
        }

        .report-title h5 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            color: #000000;
        }

        .report-title .sub-title {
            font-size: 10pt;
            color: #000000;
        }

        /* Parameter / Metadata Table (Word style: no borders, clean tabs) */
        .meta-table {
            width: 100%;
            margin-bottom: 14px;
            font-size: 10pt;
            color: #000000;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
            color: #000000;
        }

        /* Table Styling - Clean Formal Word/Docs Grid Table */
        .table-report {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 14px;
            color: #000000;
        }

        .table-report th {
            background-color: #f2f2f2 !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
            padding: 6px 5px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .table-report td {
            border: 1px solid #000000 !important;
            padding: 5px;
            vertical-align: top;
            color: #000000;
        }

        /* Summary Box - Word Formal Style */
        .summary-box {
            border: 1px solid #000000;
            padding: 6px 10px;
            font-size: 9pt;
            margin-bottom: 14px;
            background: #fafafa;
            color: #000000;
        }

        /* Signature block */
        .ttd-wrapper {
            margin-top: 24px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            font-size: 10pt;
            color: #000000;
        }

        .ttd-wrapper table, .ttd-wrapper tr, .ttd-wrapper td {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .action-bar {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            background: #ffffff;
            padding: 10px 16px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            display: flex;
            gap: 10px;
            align-items: center;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                color: #000000;
            }

            .action-bar {
                display: none !important;
            }

            .paper {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .table-report th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            @page {
                size: A4 landscape;
                margin: 12mm 12mm 12mm 12mm;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Bar for User -->
    <div class="action-bar">
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold shadow-sm">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen (A4)
        </button>
    </div>

    <div class="paper">
        <!-- KOP SURAT RESMI SEKOLAH (TEMPLATE RESMI DARI DOKUMEN DOCX) -->
        <div class="kop-surat mb-3">
            <span class="visually-hidden">YAYASAN PENDIDIKAN TEKNIK SEKOLAH MENENGAH KEJURUAN DR SUTOMO SMK DR. SUTOMO TEMANGGUNG</span>
            <img src="{{ asset('images/kop-surat-header.png') }}" alt="Kop Surat Resmi SMK Dr. Sutomo Temanggung" class="kop-img">
        </div>

        <!-- JUDUL LAPORAN -->
        <div class="report-title">
            <h5>{{ $viewMode === 'unit' ? 'LAPORAN RINCIAN UNIT FISIK ASET & SARANA PRASARANA' : 'LAPORAN REKAPITULASI INVENTARIS ASET & SARANA PRASARANA' }}</h5>
            <div class="text-muted small">
                Status Data per: <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</strong>
            </div>
        </div>

        <!-- FILTER & PARAMETER DOKUMEN -->
        <table class="meta-table">
            <tr>
                <td style="width: 18%;"><strong>Jurusan / Unit</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;">{{ $selectedJurusan ? $selectedJurusan->nama . ' (' . $selectedJurusan->kode . ')' : 'Seluruh Jurusan & Unit Kerja' }}</td>
                <td style="width: 18%;"><strong>Format Tampilan</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">
                    <strong>{{ $viewMode === 'unit' ? 'Rincian Per Unit Fisik' : 'Rekapitulasi Per Barang' }}</strong>
                </td>
            </tr>
            <tr>
                <td><strong>Kondisi Disaring</strong></td>
                <td>:</td>
                <td>{{ request('kondisi') ? ucwords(str_replace('_', ' ', request('kondisi'))) : 'Semua Kondisi' }}</td>
                <td><strong>Dicetak Oleh</strong></td>
                <td>:</td>
                <td>{{ $user->name }} ({{ $user->role }})</td>
            </tr>
            @php
                if (request('tgl_mulai') && request('tgl_selesai')) {
                    $periodeLabel = 'Rentang Tanggal: ' . \Carbon\Carbon::parse(request('tgl_mulai'))->format('d/m/Y') . ' s/d ' . \Carbon\Carbon::parse(request('tgl_selesai'))->format('d/m/Y');
                } elseif (request('tgl_mulai')) {
                    $periodeLabel = 'Mulai Tanggal: ' . \Carbon\Carbon::parse(request('tgl_mulai'))->format('d/m/Y');
                } elseif (request('tgl_selesai')) {
                    $periodeLabel = 'Sampai Tanggal: ' . \Carbon\Carbon::parse(request('tgl_selesai'))->format('d/m/Y');
                } else {
                    $periodeLabel = match(request('periode')) {
                        'hari_ini' => 'Harian (Hari Ini - ' . \Carbon\Carbon::today()->format('d/m/Y') . ')',
                        'minggu_ini' => 'Mingguan (Minggu Ini: ' . \Carbon\Carbon::now()->startOfWeek()->format('d/m') . ' s/d ' . \Carbon\Carbon::now()->endOfWeek()->format('d/m/Y') . ')',
                        'bulan_ini' => 'Bulanan (' . \Carbon\Carbon::now()->translatedFormat('F Y') . ')',
                        default => 'Semua Periode Pengadaan',
                    };
                }
            @endphp
            <tr>
                <td><strong>Periode Pengadaan</strong></td>
                <td>:</td>
                <td colspan="4">{{ $periodeLabel }}</td>
            </tr>
        </table>

        @if($viewMode === 'unit')
            <!-- TABEL DATA RINCIAN PER UNIT FISIK -->
            @php
                $allUnits = collect();
                foreach ($items as $item) {
                    if ($item->units->count() > 0) {
                        foreach ($item->units as $unit) {
                            $allUnits->push((object)[
                                'unit_code' => $unit->unit_code,
                                'nomor_seri' => $unit->nomor_seri,
                                'nomor_meja' => $unit->nomor_meja,
                                'kondisi' => $unit->kondisi ?: $item->kondisi,
                                'status' => $unit->status ?: 'tersedia',
                                'lokasi' => $unit->lokasi_penempatan ?: ($item->lokasi ?? '-'),
                                'item_kode' => $item->kode_barang,
                                'item_nama' => $item->nama_barang,
                                'jurusan' => $item->jurusan->kode ?? 'Umum',
                                'is_computer' => $item->is_computer,
                                'tanggal_masuk' => $unit->tanggal_masuk ? $unit->tanggal_masuk->format('d/m/Y') : ($item->tahun_pengadaan ? 'Thn '.$item->tahun_pengadaan : '-'),
                                'processor' => $unit->processor ?: $item->processor,
                                'ram' => $unit->ram ?: $item->ram,
                                'storage' => $unit->storage ?: $item->storage,
                                'gpu_vga' => $unit->gpu_vga ?: $item->gpu_vga,
                                'sistem_operasi' => $unit->sistem_operasi ?: $item->sistem_operasi,
                                'spesifikasi' => $item->spesifikasi,
                                'catatan' => $unit->catatan,
                            ]);
                        }
                    } else {
                        // Barang bulk / tanpa unit individual
                        $allUnits->push((object)[
                            'unit_code' => $item->kode_barang . ' (Bulk: ' . $item->jumlah . ' ' . $item->satuan . ')',
                            'nomor_seri' => '-',
                            'nomor_meja' => '-',
                            'kondisi' => $item->kondisi,
                            'status' => 'tersedia',
                            'lokasi' => $item->lokasi ?? '-',
                            'item_kode' => $item->kode_barang,
                            'item_nama' => $item->nama_barang,
                            'jurusan' => $item->jurusan->kode ?? 'Umum',
                            'is_computer' => $item->is_computer,
                            'tanggal_masuk' => $item->tahun_pengadaan ? 'Thn '.$item->tahun_pengadaan : ($item->created_at ? $item->created_at->format('d/m/Y') : '-'),
                            'processor' => $item->processor,
                            'ram' => $item->ram,
                            'storage' => $item->storage,
                            'gpu_vga' => $item->gpu_vga,
                            'sistem_operasi' => $item->sistem_operasi,
                            'spesifikasi' => $item->spesifikasi,
                            'catatan' => 'Pencatatan bulk/akumulasi stok',
                        ]);
                    }
                }
            @endphp
            <table class="table-report">
                <thead>
                    <tr>
                        <th style="width: 3%;">No</th>
                        <th style="width: 14%;">Kode Unit Fisik</th>
                        <th style="width: 18%;">Barang Induk & Kode</th>
                        <th style="width: 9%;">No. Meja / Seri</th>
                        <th style="width: 9%;">Jurusan / Lokasi</th>
                        <th style="width: 8%;">Tgl Masuk</th>
                        <th style="width: 7%;">Kondisi</th>
                        <th style="width: 7%;">Status</th>
                        <th style="width: 25%;">Spesifikasi Teknis & Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allUnits as $index => $u)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $u->unit_code }}</strong>
                                @if($u->is_computer)
                                    <div><small>[PC / Workstation]</small></div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $u->item_nama }}</strong>
                                <div><small>{{ $u->item_kode }}</small></div>
                            </td>
                            <td class="text-center">
                                @if($u->nomor_meja)<strong>{{ $u->nomor_meja }}</strong>@endif
                                @if($u->nomor_seri)<div><small>SN: {{ $u->nomor_seri }}</small></div>@endif
                                @if(!$u->nomor_meja && !$u->nomor_seri)-@endif
                            </td>
                            <td>
                                <div>{{ $u->jurusan }}</div>
                                <div><small>{{ $u->lokasi }}</small></div>
                            </td>
                            <td class="text-center">
                                {{ $u->tanggal_masuk }}
                            </td>
                            <td class="text-center">
                                @if($u->kondisi == 'baik')
                                    Baik
                                @elseif($u->kondisi == 'rusak_ringan')
                                    Rusak Ringan
                                @else
                                    Rusak Berat
                                @endif
                            </td>
                            <td class="text-center text-capitalize">
                                {{ str_replace('_', ' ', $u->status) }}
                            </td>
                            <td>
                                @if($u->is_computer)
                                    <div style="font-size: 8pt; line-height: 1.3;">
                                        @if($u->processor)Proc: {{ $u->processor }} | @endif
                                        @if($u->ram)RAM: {{ $u->ram }} | @endif
                                        @if($u->storage)Disk: {{ $u->storage }} | @endif
                                        @if($u->gpu_vga)GPU: {{ $u->gpu_vga }} | @endif
                                        @if($u->sistem_operasi)OS: {{ $u->sistem_operasi }}@endif
                                    </div>
                                @elseif($u->spesifikasi)
                                    <div style="font-size: 8pt;">{{ $u->spesifikasi }}</div>
                                @endif
                                @if($u->catatan)
                                    <div style="font-size: 8pt; font-style: italic;">Ket: {{ $u->catatan }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-3">
                                <em>Tidak ada data unit fisik yang sesuai dengan filter.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- RINGKASAN DATA UNIT FISIK -->
            <div class="summary-box">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="border: none; padding: 0;">
                            <strong>Ringkasan Unit Fisik:</strong>
                            Total Terdaftar: <strong>{{ $allUnits->count() }} Unit</strong> |
                            Komputer/PC: <strong>{{ $allUnits->where('is_computer', true)->count() }} Unit</strong>
                        </td>
                        <td style="border: none; padding: 0; text-align: right;">
                            Kondisi Baik: <strong>{{ $allUnits->where('kondisi', 'baik')->count() }}</strong> |
                            Rusak Ringan: <strong>{{ $allUnits->where('kondisi', 'rusak_ringan')->count() }}</strong> |
                            Rusak Berat: <strong>{{ $allUnits->where('kondisi', 'rusak_berat')->count() }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        @else
            <!-- TABEL DATA INVENTARIS PER BARANG (DEFAULT) -->
            <table class="table-report">
                <thead>
                    <tr>
                        <th style="width: 3%;">No</th>
                        <th style="width: 13%;">Kode Barang</th>
                        <th style="width: 20%;">Nama Barang & Spesifikasi Teknis</th>
                        <th style="width: 9%;">Kategori</th>
                        <th style="width: 10%;">Jurusan / Ruang</th>
                        <th style="width: 8%;">Thn/Tgl Masuk</th>
                        <th style="width: 6%;">Jenis</th>
                        <th style="width: 6%;">Stok</th>
                        <th style="width: 7%;">Kondisi</th>
                        <th style="width: 18%;">Rincian Unit Fisik</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->kode_barang }}</strong>
                                @if($item->is_computer)
                                    <div><small>[PC / Workstation]</small></div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->nama_barang }}</strong>
                                @if($item->is_computer)
                                    <div style="font-size: 8pt; line-height: 1.3; margin-top: 2px;">
                                        @if($item->processor)Proc: {{ $item->processor }} | @endif
                                        @if($item->ram)RAM: {{ $item->ram }} | @endif
                                        @if($item->storage)Disk: {{ $item->storage }} | @endif
                                        @if($item->gpu_vga)GPU: {{ $item->gpu_vga }} | @endif
                                        @if($item->sistem_operasi)OS: {{ $item->sistem_operasi }}@endif
                                    </div>
                                @elseif($item->spesifikasi)
                                    <div style="font-size: 8pt; margin-top: 2px;">{{ $item->spesifikasi }}</div>
                                @endif
                            </td>
                            <td>{{ $item->category->nama ?? '-' }}</td>
                            <td>
                                <div>{{ $item->jurusan->kode ?? 'Umum' }}</div>
                                <div><small>{{ $item->lokasi ?? '-' }}</small></div>
                            </td>
                            <td class="text-center">
                                @if($item->tahun_pengadaan)
                                    {{ $item->tahun_pengadaan }}
                                @elseif($item->created_at)
                                    {{ $item->created_at->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center text-capitalize">{{ $item->jenis }}</td>
                            <td class="text-center">
                                <strong>{{ $item->jumlah }}</strong> {{ $item->satuan }}
                            </td>
                            <td class="text-center">
                                @if($item->kondisi == 'baik')
                                    Baik
                                @elseif($item->kondisi == 'rusak_ringan')
                                    Rusak Ringan
                                @else
                                    Rusak Berat
                                @endif
                            </td>
                            <td>
                                @if($item->units->count() > 0)
                                    <div style="font-size: 8pt; line-height: 1.3;">
                                        @foreach($item->units->take(6) as $unit)
                                            <span>
                                                {{ $unit->unit_code }}@if($unit->nomor_meja) ({{ $unit->nomor_meja }})@endif;
                                            </span>
                                        @endforeach
                                        @if($item->units->count() > 6)
                                            <span>...+{{ $item->units->count() - 6 }} lainnya</span>
                                        @endif
                                    </div>
                                @else
                                    <span style="font-size: 8pt; font-style: italic;">(Pencatatan bulk/stok)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-3">
                                <em>Tidak ada data inventaris yang sesuai dengan filter yang dipilih.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- RINGKASAN DATA (REKAP KECIL) -->
            <div class="summary-box">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="border: none; padding: 0;">
                            <strong>Ringkasan Rekapitulasi:</strong>
                            Total Item: <strong>{{ $items->count() }} Item</strong> |
                            Total Komputer/PC: <strong>{{ $items->where('is_computer', true)->count() }} Item</strong> |
                            Total Akumulasi Fisik/Stok: <strong>{{ $items->sum('jumlah') }} Unit/Pcs</strong>
                        </td>
                        <td style="border: none; padding: 0; text-align: right;">
                            Kondisi Baik: <strong>{{ $items->where('kondisi', 'baik')->count() }}</strong> |
                            Rusak Ringan: <strong>{{ $items->where('kondisi', 'rusak_ringan')->count() }}</strong> |
                            Rusak Berat: <strong>{{ $items->where('kondisi', 'rusak_berat')->count() }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <!-- LEMBAR TANDA TANGAN / PENGESAHAN -->
        <div class="ttd-wrapper">
            <table style="width: 100%; font-size: 10pt; text-align: center;">
                <tr>
                    <td style="width: 50%;">
                        Mengetahui,<br>
                        <strong>{{ $selectedJurusan ? 'Kepala ' . $selectedJurusan->nama : 'Kepala Program Keahlian / Unit Kerja' }}</strong>
                        <div style="height: 65px;"></div>
                        <strong>( {{ $selectedJurusan->kepala_bengkel ?? '.....................................................' }} )</strong><br>
                        <span>NIP/NIY: {{ $selectedJurusan->nip ?? '.......................................' }}</span>
                    </td>
                    <td style="width: 50%;">
                        Temanggung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                        <strong>Waka Bidang Sarana &amp; Prasarana</strong>
                        <div style="height: 65px;"></div>
                        <strong>( {{ $sarprasUnit->kepala_bengkel ?? '.....................................................' }} )</strong><br>
                        <span>NIP/NIY: {{ $sarprasUnit->nip ?? '.......................................' }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
