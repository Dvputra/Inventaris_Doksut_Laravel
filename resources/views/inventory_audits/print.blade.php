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
            line-height: 1.35;
            margin: 0;
            padding: 20px 0;
        }

        .paper {
            background: #ffffff;
            width: 297mm; /* A4 Landscape agar tabel checklist muat rapi */
            min-height: 210mm;
            margin: 0 auto;
            padding: 15mm 15mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            color: #000000;
            box-sizing: border-box;
        }

        /* Kop Surat Resmi (Maksimal Ukuran A4 dengan Margin Normal di Tengah) */
        .kop-surat {
            padding-bottom: 4px;
            margin-bottom: 12px;
            width: 100%;
            max-width: 180mm;
            margin-left: auto;
            margin-right: auto;
        }

        .kop-img {
            width: 100%;
            height: auto;
            display: block;
        }

        .report-title {
            text-align: center;
            margin: 8px 0 14px;
        }

        .report-title h5 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-decoration: underline;
        }

        .report-title p {
            font-size: 10pt;
            color: #000000;
            margin: 0;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
        }

        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .table-report {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 14px;
        }

        .table-report th {
            background-color: #f2f2f2 !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
            padding: 5px 4px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .table-report td {
            border: 1px solid #000000 !important;
            padding: 4px 5px;
            vertical-align: top;
            color: #000000;
        }

        .summary-box {
            border: 1px solid #000000;
            padding: 6px 10px;
            font-size: 9pt;
            margin-bottom: 14px;
            background: #fafafa;
        }

        .ttd-wrapper {
            margin-top: 18px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            font-size: 9.5pt;
        }

        .ttd-box {
            text-align: center;
            width: 45%;
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
            }
            .action-bar {
                display: none !important;
            }
            .paper {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                padding: 10mm 10mm !important;
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
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            Tutup
        </button>
    </div>

    <div class="paper">
        <!-- Kop Surat Resmi -->
        <div class="kop-surat">
            <img src="{{ asset('images/kop-surat-header.png') }}" alt="Kop Surat Resmi SMK Dr. Sutomo Temanggung" class="kop-img">
        </div>

        <!-- Judul Berita Acara -->
        <div class="report-title">
            <h5>BERITA ACARA HASIL AUDIT &amp; VERIFIKASI FISIK INVENTARIS</h5>
            <p>Nomor Registrasi Audit: <strong>{{ $inventoryAudit->audit_code }}</strong></p>
        </div>

        <!-- Metadata Informasi Sesi Audit -->
        <table class="meta-table">
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
                <td>{{ $inventoryAudit->jurusan->nama ?? '-' }} ({{ $inventoryAudit->jurusan->kode ?? '' }})</td>
                <td><strong>Status Dokumen</strong></td>
                <td>:</td>
                <td><strong>{{ strtoupper($inventoryAudit->status) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Auditor Pemeriksa</strong></td>
                <td>:</td>
                <td>{{ $inventoryAudit->auditor_nama }} ({{ $inventoryAudit->auditor_jabatan }})</td>
                <td><strong>Penanggung Jawab</strong></td>
                <td>:</td>
                <td>{{ $inventoryAudit->auditee_nama ?: ($inventoryAudit->jurusan->kepala_bengkel ?? '-') }}</td>
            </tr>
        </table>

        <!-- Ringkasan Hasil Temuan Fisik -->
        <div class="summary-box">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 25%;">Total Barang Terdaftar: <strong>{{ $inventoryAudit->items->count() }} Item</strong></td>
                    <td style="width: 25%;">Fisik Sesuai: <strong class="text-success">{{ $inventoryAudit->total_barang_sesuai }} Item</strong></td>
                    <td style="width: 25%;">Kondisi Rusak: <strong class="text-warning">{{ $inventoryAudit->total_barang_rusak }} Item</strong></td>
                    <td style="width: 25%;">Selisih / Hilang: <strong class="text-danger">{{ $inventoryAudit->total_barang_selisih + $inventoryAudit->total_barang_hilang }} Item</strong></td>
                </tr>
            </table>
        </div>

        <!-- Tabel Lembar Verifikasi Fisik -->
        <table class="table-report">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 13%;">Kode Barang</th>
                    <th style="width: 22%;">Nama Barang &amp; Spesifikasi</th>
                    <th style="width: 10%;">Ruang / Lokasi</th>
                    <th style="width: 7%;">Qty Sistem</th>
                    <th style="width: 7%;">Qty Fisik</th>
                    <th style="width: 9%;">Kondisi Fisik</th>
                    <th style="width: 10%;">Status Verifikasi</th>
                    <th style="width: 19%;">Catatan Temuan Lapangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventoryAudit->items as $idx => $item)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="font-monospace fw-bold">{{ $item->kode_barang }}</td>
                        <td>
                            <strong>{{ $item->nama_barang }}</strong>
                            <div style="font-size: 8pt; color: #555;">Kategori: {{ $item->kategori_nama }}</div>
                        </td>
                        <td>{{ $item->lokasi_sistem ?: '-' }}</td>
                        <td class="text-center">@formatJumlah($item->jumlah_sistem) {{ $item->satuan }}</td>
                        <td class="text-center fw-bold">@formatJumlah($item->jumlah_fisik) {{ $item->satuan }}</td>
                        <td class="text-center">
                            @if($item->kondisi_fisik === 'baik')
                                Baik
                            @elseif($item->kondisi_fisik === 'rusak_ringan')
                                Rusak Ringan
                            @else
                                Rusak Berat
                            @endif
                        </td>
                        <td class="text-center fw-bold">
                            @if($item->status_verifikasi === 'sesuai')
                                <span class="text-success">Sesuai</span>
                            @elseif($item->status_verifikasi === 'rusak')
                                <span class="text-warning">Rusak</span>
                            @elseif($item->status_verifikasi === 'selisih')
                                <span class="text-primary">Selisih</span>
                            @elseif($item->status_verifikasi === 'hilang')
                                <span class="text-danger">Tidak Ditemukan</span>
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td>{{ $item->catatan ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-3">Tidak ada rincian data barang dalam sesi audit ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($inventoryAudit->catatan_umum)
            <div style="font-size: 9pt; margin-bottom: 12px;">
                <strong>Catatan / Kesimpulan Pemeriksa:</strong><br>
                <em>{{ $inventoryAudit->catatan_umum }}</em>
            </div>
        @endif

        <!-- Lembar Tanda Tangan Dua Belah Pihak -->
        <div class="ttd-wrapper">
            <table style="width: 100%;">
                <tr>
                    <td class="ttd-box">
                        <span>Menyetujui &amp; Mengetahui,</span><br>
                        <strong>Penanggung Jawab Unit Kerja / Auditee</strong><br>
                        <span style="font-size: 8.5pt; color: #555;">{{ $inventoryAudit->auditee_jabatan ?: ('Kepala ' . ($inventoryAudit->jurusan->nama ?? 'Unit Kerja')) }}</span>
                        
                        <div style="height: 70px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                            @if($inventoryAudit->ttd_auditee && file_exists(public_path('storage/' . $inventoryAudit->ttd_auditee)))
                                <img src="{{ asset('storage/' . $inventoryAudit->ttd_auditee) }}" alt="TTD Auditee" style="max-height: 65px; max-width: 180px; object-contain: contain;">
                            @elseif($inventoryAudit->ttd_auditee)
                                <img src="{{ Storage::url($inventoryAudit->ttd_auditee) }}" alt="TTD Auditee" style="max-height: 65px; max-width: 180px; object-contain: contain;">
                            @else
                                <span style="color: #999; font-style: italic; font-size: 8pt;">( Belum Ditandatangani )</span>
                            @endif
                        </div>

                        <strong><u>{{ $inventoryAudit->auditee_nama ?: ($inventoryAudit->jurusan->kepala_bengkel ?? '.....................................................') }}</u></strong><br>
                        <span>NIP/NPY: {{ $inventoryAudit->auditee_nip ?: '.....................................................' }}</span>
                    </td>

                    <td style="width: 10%;"></td>

                    <td class="ttd-box">
                        <span>Temanggung, {{ $inventoryAudit->tanggal_audit ? $inventoryAudit->tanggal_audit->translatedFormat('d F Y') : date('d F Y') }}</span><br>
                        <strong>Petugas Pemeriksa / Auditor Sarpras</strong><br>
                        <span style="font-size: 8.5pt; color: #555;">{{ $inventoryAudit->auditor_jabatan ?: 'Staff Sarana dan Prasarana' }}</span>

                        <div style="height: 70px; display: flex; align-items: center; justify-content: center; margin: 4px 0;">
                            @if($inventoryAudit->ttd_auditor && file_exists(public_path('storage/' . $inventoryAudit->ttd_auditor)))
                                <img src="{{ asset('storage/' . $inventoryAudit->ttd_auditor) }}" alt="TTD Auditor" style="max-height: 65px; max-width: 180px; object-contain: contain;">
                            @elseif($inventoryAudit->ttd_auditor)
                                <img src="{{ Storage::url($inventoryAudit->ttd_auditor) }}" alt="TTD Auditor" style="max-height: 65px; max-width: 180px; object-contain: contain;">
                            @else
                                <span style="color: #999; font-style: italic; font-size: 8pt;">( Belum Ditandatangani )</span>
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
