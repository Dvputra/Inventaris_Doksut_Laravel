<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengaduan & Servis Fasilitas - SMK Dr. Sutomo Temanggung</title>
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

        /* Parameter / Metadata Table */
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
            page-break-inside: avoid;
            font-size: 10pt;
            color: #000000;
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
            <h5>REKAPITULASI PENGADUAN, SERVIS & PEMELIHARAAN SARANA PRASARANA</h5>
            <div class="text-muted small">
                Rekap Penanganan Kendala Fasilitas, Ruang Belajar, dan Laboratorium Praktik
            </div>
        </div>

        <!-- FILTER & PARAMETER DOKUMEN -->
        <table class="meta-table">
            <tr>
                <td style="width: 18%;"><strong>Jurusan / Unit</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 40%;">{{ $selectedJurusan ? $selectedJurusan->nama . ' (' . $selectedJurusan->kode . ')' : 'Semua Fasilitas & Unit Kerja' }}</td>
                <td style="width: 18%;"><strong>Status Disaring</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 20%;">{{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh</strong></td>
                <td>:</td>
                <td>{{ $user->name }} ({{ $user->role }})</td>
                <td><strong>Waktu Cetak</strong></td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
        </table>

        <!-- TABEL DATA PENGADUAN & SERVIS -->
        <table class="table-report">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 10%;">No. Tiket</th>
                    <th style="width: 8%;">Tgl Lapor</th>
                    <th style="width: 12%;">Nama Guru / Pelapor</th>
                    <th style="width: 13%;">Lokasi / Ruang</th>
                    <th style="width: 20%;">Uraian Masalah / Kendala</th>
                    <th style="width: 7%;">Urgensi</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 19%;">Teknisi & Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                @forelse($complaints as $index => $c)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <strong>{{ $c->ticket_code }}</strong>
                        </td>
                        <td class="text-center">
                            {{ $c->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <strong>{{ $c->nama_pelapor }}</strong>
                            @if($c->kontak)
                                <div><small>{{ $c->kontak }}</small></div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $c->lokasi_ruang }}</div>
                            <div><small>{{ $c->jurusan->kode ?? 'Umum / Sekolah' }}</small></div>
                        </td>
                        <td>
                            <strong>{{ $c->judul_kendala }}</strong>
                            <div style="font-size: 8pt; line-height: 1.3; margin-top: 2px;">
                                {{ Str::limit($c->deskripsi, 100) }}
                            </div>
                        </td>
                        <td class="text-center text-capitalize">
                            {{ str_replace('_', ' ', $c->tingkat_urgensi) }}
                        </td>
                        <td class="text-center text-capitalize">
                            {{ $c->status }}
                        </td>
                        <td>
                            @if($c->teknisi_penanganan)
                                <div><strong>Teknisi:</strong> {{ $c->teknisi_penanganan }}</div>
                            @endif
                            @if($c->tindak_lanjut)
                                <div style="font-size: 8pt;">{{ Str::limit($c->tindak_lanjut, 80) }}</div>
                            @endif
                            @if($c->tanggal_selesai)
                                <div style="font-size: 7.5pt;">Selesai: {{ \Carbon\Carbon::parse($c->tanggal_selesai)->format('d/m/Y') }}</div>
                            @endif
                            @if(!$c->teknisi_penanganan && !$c->tindak_lanjut)
                                <span style="font-size: 8pt; font-style: italic;">(Belum ada tindakan)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-3">
                            <em>Tidak ada data tiket pengaduan yang sesuai dengan filter yang dipilih.</em>
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
                        Total Tiket Masuk: <strong>{{ $complaints->count() }} Laporan</strong>
                    </td>
                    <td style="border: none; padding: 0; text-align: right;">
                        Selesai: <strong>{{ $complaints->where('status', 'selesai')->count() }}</strong> |
                        Diproses: <strong>{{ $complaints->where('status', 'diproses')->count() }}</strong> |
                        Menunggu: <strong>{{ $complaints->where('status', 'menunggu')->count() }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- LEMBAR TANDA TANGAN / PENGESAHAN -->
        <div class="ttd-wrapper">
            <table style="width: 100%; font-size: 10pt; text-align: center;">
                <tr>
                    <td style="width: 50%;">
                        Mengetahui / Memeriksa,<br>
                        <strong>Koordinator Teknisi Sarpras</strong>
                        <div style="height: 65px;"></div>
                        <strong>( ..................................................... )</strong><br>
                        <span>NIP/NPY: .......................................</span>
                    </td>
                    <td style="width: 50%;">
                        Temanggung, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                        <strong>Waka Bidang Sarana & Prasarana</strong>
                        <div style="height: 65px;"></div>
                        <strong>( ..................................................... )</strong><br>
                        <span>NIP/NPY: .......................................</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
