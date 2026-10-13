<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Hasil Audit - {{ $inventoryAudit->audit_code }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f1f5f9;
            color: #000000;
            font-size: 10pt;
            line-height: 1.4;
            margin: 0;
            padding: 24px 0;
        }

        .paper {
            background: #ffffff;
            width: 297mm; /* Format A4 Landscape untuk lembar kerja audit */
            min-height: 210mm;
            margin: 0 auto;
            padding: 16mm 18mm;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            color: #000000;
            box-sizing: border-box;
            border-radius: 4px;
        }

        /* Kop Surat Resmi - Lebar maksimal proporsional & presisi di tengah */
        .kop-surat {
            padding-bottom: 4px;
            margin-bottom: 12px;
            width: 100%;
            max-width: 195mm;
            margin-left: auto;
            margin-right: auto;
        }

        .kop-img {
            width: 100%;
            height: auto;
            display: block;
        }

        .title-block {
            text-align: center;
            margin: 10px 0 16px;
        }

        .title-block h4 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            text-decoration: underline;
        }

        .title-block .nomor {
            font-size: 10pt;
            color: #111111;
            font-weight: bold;
            margin: 0;
        }

        /* Tabel Identitas & Informasi Sesi Audit (Format Microsoft Word) */
        .info-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }

        /* Ringkasan Rekapitulasi Hasil Fisik */
        .summary-box {
            border: 1.5px solid #000000;
            padding: 6px 12px;
            font-size: 9pt;
            margin-bottom: 14px;
            background: #fcfcfc;
        }

        .summary-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-box td {
            padding: 3px 6px;
        }

        /* Tabel Utama Hasil Checklist Verifikasi Fisik (Formal Standard Word) */
        .table-report {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }

        .table-report th {
            background-color: #f0f0f0 !important;
            color: #000000 !important;
            border: 1.2px solid #000000 !important;
            padding: 6px 4px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .table-report td {
            border: 1px solid #000000 !important;
            padding: 4.5px 5px;
            vertical-align: top;
            color: #000000;
        }

        /* Kotak Catatan Khusus Sarpras */
        .catatan-box {
            font-size: 9pt;
            margin-bottom: 16px;
            border: 1.2px solid #000000;
            padding: 8px 12px;
            background-color: #fafafa;
        }

        .catatan-title {
            text-transform: uppercase;
            font-size: 8.5pt;
            letter-spacing: 0.5px;
            font-weight: bold;
        }

        .catatan-content {
            margin-top: 3px;
            font-style: italic;
            line-height: 1.45;
        }

        /* Lembar Tanda Tangan */
        .ttd-wrapper {
            margin-top: 18px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            font-size: 9.5pt;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .ttd-col {
            width: 45%;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-container {
            height: 75px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 3px 0 2px;
        }

        .signature-img {
            max-height: 58px;
            max-width: 170px;
            object-fit: contain;
        }

        .signature-verify-badge {
            font-size: 6.5pt;
            color: #475569;
            font-style: italic;
            margin-top: 2px;
            line-height: 1.1;
        }

        /* Floating Action Bar (Hanya di Layar) */
        .action-bar {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1050;
            background: #ffffff;
            padding: 10px 18px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18);
            display: flex;
            gap: 10px;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .action-bar {
                display: none !important;
            }
            .paper {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                padding: 6mm 8mm !important;
                border-radius: 0 !important;
            }
            @page {
                size: A4 landscape;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Bar -->
    <div class="action-bar d-print-none">
        <a href="{{ route('inventory-audits.show', $inventoryAudit) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Lembar Dokumen Resmi Kertas A4 Landscape -->
    <div class="paper">
        <!-- Kop Surat Resmi -->
        <div class="kop-surat">
            <img src="{{ asset('images/kop-surat-header.png') }}" alt="Kop Surat Resmi SMK Dr. Sutomo Temanggung" class="kop-img">
        </div>

        <!-- Judul Berita Acara -->
        <div class="title-block">
            <h4>BERITA ACARA HASIL AUDIT &amp; VERIFIKASI FISIK INVENTARIS</h4>
            <div class="nomor">Nomor Registrasi: {{ $inventoryAudit->audit_code }}</div>
        </div>

        @php
            $auditeeNip = $inventoryAudit->auditee_nip ?: ($inventoryAudit->jurusan->nip ?? null);
            $auditeeNama = $inventoryAudit->auditee_nama ?: ($inventoryAudit->jurusan->kepala_bengkel ?? 'Penanggung Jawab Unit');
            $auditeeJabatan = $inventoryAudit->auditee_jabatan ?: ('Kepala ' . ($inventoryAudit->jurusan->nama ?? 'Unit Kerja'));
        @endphp

        <!-- Metadata Informasi Sesi Audit (Format Standar Surat Dinas) -->
        <table class="info-table">
            <tr>
                <td style="width: 18%;"><strong>Judul / Sesi Audit</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 45%;">{{ $inventoryAudit->judul }}</td>
                <td style="width: 16%;"><strong>Tanggal Audit</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 17%;">{{ $inventoryAudit->tanggal_audit ? $inventoryAudit->tanggal_audit->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td><strong>Unit Kerja / Jurusan</strong></td>
                <td>:</td>
                <td>{{ $inventoryAudit->jurusan->nama ?? '-' }} (Kode: {{ $inventoryAudit->jurusan->kode ?? '-' }})</td>
                <td><strong>Status Dokumen</strong></td>
                <td>:</td>
                <td><strong>{{ strtoupper($inventoryAudit->status) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Auditor Pemeriksa</strong></td>
                <td>:</td>
                <td>
                    <strong>{{ $inventoryAudit->auditor_nama }}</strong> 
                    ({{ $inventoryAudit->auditor_jabatan }})
                    @if($inventoryAudit->auditor_nip) • <span style="font-family: monospace;">NIP: {{ $inventoryAudit->auditor_nip }}</span>@endif
                </td>
                <td><strong>Penanggung Jawab</strong></td>
                <td>:</td>
                <td>
                    <strong>{{ $auditeeNama }}</strong>
                    @if($auditeeNip) <br><span style="font-size: 8.5pt; font-family: monospace;">NIP: {{ $auditeeNip }}</span>@endif
                </td>
            </tr>
        </table>

        <!-- Ringkasan Rekapitulasi Hasil Fisik -->
        <div class="summary-box">
            <table>
                <tr>
                    <td style="width: 25%;">Total Barang Terdaftar: <strong>{{ $inventoryAudit->items->count() }} Item</strong></td>
                    <td style="width: 25%;">Fisik Sesuai: <strong class="text-success">{{ $inventoryAudit->total_barang_sesuai }} Item</strong></td>
                    <td style="width: 25%;">Kondisi Rusak: <strong class="text-warning">{{ $inventoryAudit->total_barang_rusak }} Item</strong></td>
                    <td style="width: 25%;">Selisih / Hilang: <strong class="text-danger">{{ $inventoryAudit->total_barang_selisih + $inventoryAudit->total_barang_hilang }} Item</strong></td>
                </tr>
            </table>
        </div>

        <!-- Tabel Lembar Verifikasi Fisik Barang -->
        <table class="table-report">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 12%;">Kode Barang</th>
                    <th style="width: 21%;">Nama Barang &amp; Spesifikasi</th>
                    <th style="width: 11%;">Kategori</th>
                    <th style="width: 10%;">Ruang / Lokasi</th>
                    <th style="width: 7%;">Qty Sistem</th>
                    <th style="width: 7%;">Qty Fisik</th>
                    <th style="width: 9%;">Kondisi Fisik</th>
                    <th style="width: 9%;">Status</th>
                    <th style="width: 11%;">Catatan Temuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventoryAudit->items as $idx => $item)
                    <tr>
                        <td class="text-center" style="font-weight: 500;">{{ $idx + 1 }}</td>
                        <td class="font-monospace" style="font-weight: 600; font-size: 8pt;">{{ $item->kode_barang }}</td>
                        <td>
                            <strong>{{ $item->nama_barang }}</strong>
                        </td>
                        <td class="text-center">
                            {{ $item->kategori_nama ?: 'Umum' }}
                        </td>
                        <td>{{ $item->lokasi_sistem ?: '-' }}</td>
                        <td class="text-center">@formatJumlah($item->jumlah_sistem) {{ $item->satuan }}</td>
                        <td class="text-center" style="font-weight: bold; {{ $item->jumlah_fisik != $item->jumlah_sistem ? 'color: #c2410c;' : '' }}">
                            @formatJumlah($item->jumlah_fisik) {{ $item->satuan }}
                        </td>
                        <td class="text-center">
                            @if($item->kondisi_fisik === 'baik')
                                Baik
                            @elseif($item->kondisi_fisik === 'rusak_ringan')
                                Rusak Ringan
                            @else
                                Rusak Berat
                            @endif
                        </td>
                        <td class="text-center" style="font-weight: bold;">
                            @if($item->status_verifikasi === 'sesuai')
                                <span class="text-success">Sesuai</span>
                            @elseif($item->status_verifikasi === 'rusak')
                                <span class="text-warning">Rusak</span>
                            @elseif($item->status_verifikasi === 'selisih')
                                <span class="text-primary">Selisih</span>
                            @elseif($item->status_verifikasi === 'hilang')
                                <span class="text-danger">Hilang</span>
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td>{{ $item->catatan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-3">Tidak ada rincian data barang dalam sesi audit ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Catatan Khusus / Rekomendasi Sarpras -->
        @if($inventoryAudit->catatan_umum)
            <div class="catatan-box">
                <div class="catatan-title">Catatan Khusus / Rekomendasi Tim Sarpras:</div>
                <div class="catatan-content">{{ $inventoryAudit->catatan_umum }}</div>
            </div>
        @endif

        <!-- Lembar Tanda Tangan Dua Belah Pihak (Dilengkapi Verifikasi Elektronik Kecil) -->
        <div class="ttd-wrapper">
            <table class="ttd-table">
                <tr>
                    <!-- Kolom Tanda Tangan Auditee (Kiri) -->
                    <td class="ttd-col">
                        <span>Menyetujui &amp; Mengetahui,</span><br>
                        <strong>Penanggung Jawab Unit Kerja (Auditee)</strong><br>
                        <span style="font-size: 8.5pt; color: #444444;">{{ $auditeeJabatan }}</span>
                        
                        <div class="signature-container">
                            @if($inventoryAudit->ttd_auditee)
                                @php
                                    $auditeeSigSrc = file_exists(public_path('storage/' . $inventoryAudit->ttd_auditee))
                                        ? asset('storage/' . $inventoryAudit->ttd_auditee)
                                        : Storage::url($inventoryAudit->ttd_auditee);
                                @endphp
                                <img src="{{ $auditeeSigSrc }}" alt="TTD Auditee" class="signature-img">
                                <div class="signature-verify-badge">
                                    Ditandatangani elektronik: {{ $inventoryAudit->ttd_auditee_at ? $inventoryAudit->ttd_auditee_at->translatedFormat('d/m/Y H:i') : date('d/m/Y H:i') }}
                                </div>
                            @else
                                <span style="color: #94a3b8; font-style: italic; font-size: 8pt;">( Belum Ditandatangani )</span>
                            @endif
                        </div>

                        <strong><u>{{ $auditeeNama }}</u></strong><br>
                        <span>NIP/NPY: {{ $auditeeNip ?: '.....................................................' }}</span>
                    </td>

                    <!-- Spacer Tengah -->
                    <td style="width: 10%;"></td>

                    <!-- Kolom Tanda Tangan Auditor Sarpras (Kanan) -->
                    <td class="ttd-col">
                        <span>Temanggung, {{ $inventoryAudit->tanggal_audit ? $inventoryAudit->tanggal_audit->translatedFormat('d F Y') : date('d F Y') }}</span><br>
                        <strong>Petugas Pemeriksa / Auditor Sarpras</strong><br>
                        <span style="font-size: 8.5pt; color: #444444;">{{ $inventoryAudit->auditor_jabatan ?: 'Staff Sarana dan Prasarana' }}</span>

                        <div class="signature-container">
                            @if($inventoryAudit->ttd_auditor)
                                @php
                                    $auditorSigSrc = file_exists(public_path('storage/' . $inventoryAudit->ttd_auditor))
                                        ? asset('storage/' . $inventoryAudit->ttd_auditor)
                                        : Storage::url($inventoryAudit->ttd_auditor);
                                @endphp
                                <img src="{{ $auditorSigSrc }}" alt="TTD Auditor" class="signature-img">
                                <div class="signature-verify-badge">
                                    Ditandatangani elektronik: {{ $inventoryAudit->ttd_auditor_at ? $inventoryAudit->ttd_auditor_at->translatedFormat('d/m/Y H:i') : date('d/m/Y H:i') }}
                                </div>
                            @else
                                <span style="color: #94a3b8; font-style: italic; font-size: 8pt;">( Belum Ditandatangani )</span>
                            @endif
                        </div>

                        <strong><u>{{ $inventoryAudit->auditor_nama }}</u></strong><br>
                        <span>NIP/NPY: {{ $inventoryAudit->auditor_nip ?: '.....................................................' }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
