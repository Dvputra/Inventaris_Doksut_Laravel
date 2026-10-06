<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Usulan Pengadaan - {{ $procurement->nomor_usulan ?? 'UP-' . $procurement->id }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Reset & Page Settings */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f1f5f9;
            color: #000000;
            font-size: 11pt;
            line-height: 1.45;
            padding: 24px 0 40px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Lembar A4 (Format Dokumen Word / Google Docs) */
        .paper {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 20mm 20mm 20mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            color: #000000;
            position: relative;
        }

        /* Floating Action Bar (Hanya tampil di layar browser) */
        .action-bar {
            position: fixed;
            top: 18px;
            right: 24px;
            background: #ffffff;
            padding: 8px 14px;
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.15);
            display: flex;
            gap: 10px;
            z-index: 9999;
            align-items: center;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }

        .btn-back {
            background-color: #f8fafc;
            color: #334155;
            border-color: #cbd5e1;
        }
        .btn-back:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .btn-print {
            background-color: #0284c7;
            color: #ffffff;
        }
        .btn-print:hover {
            background-color: #0369a1;
        }

        /* Kop Surat Resmi (Maksimal Ukuran A4 dengan Margin Normal) */
        .kop-surat {
            width: 100%;
            max-width: 180mm;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 12px;
            padding-bottom: 2px;
        }

        .kop-img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Judul Surat Resmi */
        .header-surat {
            text-align: center;
            margin: 10px 0 16px;
        }

        .header-surat h1 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            text-decoration: underline;
            color: #000000;
        }

        .header-surat .nomor {
            font-size: 11pt;
            font-weight: normal;
            color: #000000;
        }

        /* Paragraf & Teks Formal */
        p.formal-text {
            font-size: 11pt;
            text-align: justify;
            line-height: 1.5;
            margin-bottom: 10px;
            color: #000000;
        }

        /* Tabel Identitas / Pengantar Surat (Format Dokumen Dinas Resmi) */
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 11pt;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 4px;
            vertical-align: top;
            color: #000000;
        }

        /* Tabel Rincian Barang (Standar Word Table Grid) */
        .table-barang {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin: 12px 0 14px;
            color: #000000;
        }

        .table-barang th {
            background-color: #f2f2f2 !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
            padding: 6px 5px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .table-barang td {
            border: 1px solid #000000 !important;
            padding: 5px 6px;
            vertical-align: top;
            color: #000000 !important;
            background-color: #ffffff;
        }

        .table-barang tfoot td {
            font-weight: bold;
            background-color: #f8f8f8 !important;
            border: 1px solid #000000 !important;
            padding: 6px 6px;
        }

        /* Bagian Catatan & Urgensi (Gaya Dokumen Word) */
        .section-catatan {
            margin: 12px 0 16px;
            font-size: 11pt;
            line-height: 1.5;
        }

        .section-catatan h2 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 3px;
            color: #000000;
        }

        .section-catatan .isi-catatan {
            text-align: justify;
            text-indent: 1.5em;
            color: #000000;
        }

        .status-box {
            display: inline-block;
            padding: 2px 8px;
            font-size: 9.5pt;
            font-weight: bold;
            border: 1px solid #000000;
            text-transform: uppercase;
            margin-left: 6px;
        }

        /* Lembar Tanda Tangan Resmi (Format Surat Dinas Rapi - Persis Berita Acara) */
        .ttd-wrapper {
            margin-top: 28px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            font-size: 10.5pt;
        }

        .ttd-wrapper table, .ttd-wrapper tr, .ttd-wrapper td {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .ttd-table td {
            padding: 0 10px;
            vertical-align: top;
            border: none;
            color: #000000;
            line-height: 1.4;
        }

        .ttd-heading {
            font-size: 10.5pt;
            color: #000000;
            margin-bottom: 2px;
        }

        .ttd-role {
            font-size: 10.5pt;
            font-weight: bold;
            color: #000000;
            min-height: 42px;
            display: block;
        }

        .ttd-space {
            min-height: 75px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
        }

        .ttd-status-stamp {
            display: inline-block;
            border: 1.5px solid #059669;
            color: #047857;
            padding: 4px 8px;
            font-size: 8pt;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #ecfdf5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .ttd-stamp-info {
            font-size: 7pt;
            color: #475569;
            font-style: italic;
            margin-top: 2px;
            line-height: 1.2;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
            color: #000000;
            letter-spacing: 0.2px;
        }

        .ttd-nip {
            font-size: 9.5pt;
            margin-top: 3px;
            color: #1e293b;
        }

        /* Pengaturan Cetak / Print Media */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 11pt;
            }

            .action-bar {
                display: none !important;
            }

            .paper {
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 15mm 20mm 15mm 20mm;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Bar untuk pengguna -->
    <div class="action-bar">
        <a href="{{ route('procurements.index') }}" class="btn-action btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn-action btn-print">
            <i class="bi bi-printer"></i> Cetak Dokumen (A4)
        </button>
    </div>

    <div class="paper">
        <!-- KOP SURAT RESMI SEKOLAH -->
        <div class="kop-surat">
            <img src="{{ asset('images/kop-surat-header.png') }}" alt="Kop Surat Resmi SMK Dr. Sutomo Temanggung" class="kop-img">
        </div>

        <!-- JUDUL & NOMOR SURAT -->
        <div class="header-surat">
            <h1>SURAT USULAN PENGADAAN BARANG &amp; BAHAN</h1>
            <div class="nomor">
                Nomor: <strong>{{ $procurement->nomor_usulan ?? 'UP-' . str_pad($procurement->id, 4, '0', STR_PAD_LEFT) }}</strong>
            </div>
        </div>

        <!-- PENGANTAR & IDENTITAS PEMOHON -->
        <p class="formal-text">
            Yang bertanda tangan di bawah ini mengajukan permohonan usulan pengadaan barang dan bahan praktik untuk kebutuhan operasional bengkel/unit kerja dengan rincian data sebagai berikut:
        </p>

        <table class="meta-table">
            <tr>
                <td style="width: 25%;">Unit Kerja / Jurusan</td>
                <td style="width: 2%;">:</td>
                <td style="width: 73%;"><strong>{{ $procurement->jurusan->nama }} ({{ $procurement->jurusan->kode }})</strong></td>
            </tr>
            <tr>
                <td>Kepala Program / Pemohon</td>
                <td>:</td>
                <td>{{ $procurement->jurusan->kepala_bengkel ?? ($procurement->user->name ?? '-') }}</td>
            </tr>
            <tr>
                <td>Agenda / Perihal Usulan</td>
                <td>:</td>
                <td><strong>{{ $procurement->judul_pengadaan ?: $procurement->summary_barang }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Diajukan</td>
                <td>:</td>
                <td>{{ $procurement->created_at->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td>Status Persetujuan</td>
                <td>:</td>
                <td>
                    <strong>
                        @if($procurement->status === 'disetujui')
                            DISETUJUI OLEH SARPRAS
                        @elseif($procurement->status === 'ditolak')
                            DITOLAK
                        @else
                            MENUNGGU VERIFIKASI SARPRAS
                        @endif
                    </strong>
                </td>
            </tr>
        </table>

        <!-- TABEL RINCIAN BARANG YANG DIUSULKAN -->
        <table class="table-barang">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 27%;">Nama Barang / Bahan</th>
                    <th style="width: 23%;">Spesifikasi / Merk</th>
                    <th style="width: 7%;">Jml</th>
                    <th style="width: 8%;">Satuan</th>
                    <th style="width: 15%;">Harga Satuan (Rp)</th>
                    <th style="width: 16%;">Subtotal Biaya (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = $procurement->items->count() > 0 ? $procurement->items : collect([
                        (object)[
                            'nama_barang' => $procurement->nama_barang,
                            'spesifikasi' => $procurement->spesifikasi,
                            'jumlah' => $procurement->jumlah,
                            'satuan' => $procurement->satuan,
                            'harga_satuan' => $procurement->perkiraan_biaya && $procurement->jumlah ? ($procurement->perkiraan_biaya / $procurement->jumlah) : null,
                            'perkiraan_biaya' => $procurement->perkiraan_biaya,
                            'keterangan' => null
                        ]
                    ]);
                    $totalEstimasi = 0;
                @endphp

                @foreach($items as $idx => $item)
                    @php
                        $subtotal = $item->perkiraan_biaya ?: (($item->harga_satuan && $item->jumlah) ? ($item->harga_satuan * $item->jumlah) : null);
                        $totalEstimasi += ($subtotal ?: 0);
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->nama_barang }}</strong>
                            @if(!empty($item->keterangan))
                                <div style="font-size: 8.5pt; font-style: italic; color: #333; margin-top: 1px;">
                                    Ket: {{ $item->keterangan }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $item->spesifikasi ?: '-' }}</td>
                        <td style="text-align: center;">{{ number_format($item->jumlah) }}</td>
                        <td style="text-align: center;">{{ $item->satuan }}</td>
                        <td style="text-align: right;">
                            {{ $item->harga_satuan ? 'Rp ' . number_format($item->harga_satuan, 0, ',', '.') : '-' }}
                        </td>
                        <td style="text-align: right;">
                            {{ $subtotal ? 'Rp ' . number_format($subtotal, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 8px;">
                        TOTAL ESTIMASI ANGGARAN:
                    </td>
                    <td style="text-align: right;">
                        {{ $totalEstimasi > 0 ? 'Rp ' . number_format($totalEstimasi, 0, ',', '.') : '-' }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- ALASAN / JUSTIFIKASI URGENSI KEBUTUHAN -->
        <div class="section-catatan">
            <h2>Justifikasi &amp; Urgensi Kebutuhan:</h2>
            <div class="isi-catatan">
                {{ $procurement->alasan }}
            </div>
        </div>

        @if($procurement->catatan_sarpras)
            <!-- CATATAN DARI SARPRAS -->
            <div class="section-catatan" style="margin-top: 8px;">
                <h2>Catatan Verifikasi Sarana &amp; Prasarana:</h2>
                <div class="isi-catatan">
                    {{ $procurement->catatan_sarpras }}
                    @if($procurement->tanggal_persetujuan)
                        <em>(Diverifikasi pada: {{ $procurement->tanggal_persetujuan->translatedFormat('d F Y') }})</em>
                    @endif
                </div>
            </div>
        @endif

        <!-- KALIMAT PENUTUP RESMI -->
        <p class="formal-text" style="margin-top: 10px;">
            Demikian usulan pengadaan barang dan bahan ini kami sampaikan untuk dapat ditindaklanjuti sebagaimana mestinya. Atas perhatian dan persetujuan yang diberikan, kami ucapkan terima kasih.
        </p>

        <!-- Tanda Tangan Resmi 3 Pihak (Sama seperti Berita Acara) -->
        <div class="ttd-wrapper">
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <tr>
                    <td style="width: 45%; vertical-align: top;">
                        Diajukan Oleh,<br>
                        <strong>Kepala {{ $procurement->jurusan->nama }}</strong>
                        <div style="height: 70px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            @if($procurement->ttd_pemohon)
                                <img src="{{ Storage::url($procurement->ttd_pemohon) }}" alt="TTD Pemohon" style="max-height: 55px; max-width: 160px; object-fit: contain;">
                                <div style="font-size: 6.5pt; color: #64748b; font-style: italic; margin-top: 2px; line-height: 1.1;">
                                    Ditandatangani elektronik: {{ $procurement->ttd_pemohon_at ? $procurement->ttd_pemohon_at->translatedFormat('d/m/Y H:i') : ($procurement->created_at ? $procurement->created_at->translatedFormat('d/m/Y') : '') }}
                                </div>
                            @endif
                        </div>
                        <strong><u>{{ $procurement->jurusan->kepala_bengkel ?? ($procurement->user->name ?? '................................................') }}</u></strong>
                        <div style="font-size: 9.5pt;">NIP/NIY: {{ $procurement->jurusan->nip ?? ($procurement->user->nip ?? '.......................................') }}</div>
                    </td>
                    <td style="width: 10%;"></td>
                    <td style="width: 45%; vertical-align: top;">
                        Temanggung, {{ ($procurement->tanggal_persetujuan ?? $procurement->created_at)->translatedFormat('d F Y') }}<br>
                        Diverifikasi Oleh,<br>
                        <strong>Waka Bidang Sarana &amp; Prasarana</strong>
                        <div style="height: 70px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            @if($procurement->ttd_sarpras)
                                <img src="{{ Storage::url($procurement->ttd_sarpras) }}" alt="TTD Sarpras" style="max-height: 55px; max-width: 160px; object-fit: contain;">
                                <div style="font-size: 6.5pt; color: #64748b; font-style: italic; margin-top: 2px; line-height: 1.1;">
                                    Diverifikasi elektronik: {{ $procurement->ttd_sarpras_at ? $procurement->ttd_sarpras_at->translatedFormat('d/m/Y H:i') : ($procurement->tanggal_persetujuan ? $procurement->tanggal_persetujuan->translatedFormat('d/m/Y') : '') }}
                                </div>
                            @elseif($procurement->status === 'disetujui')
                                <div style="border: 1px solid #10b981; color: #047857; padding: 3px 8px; font-size: 7.5pt; border-radius: 4px; font-weight: bold;">
                                    TELAH DIVERIFIKASI SARPRAS
                                </div>
                            @endif
                        </div>
                        <strong><u>{{ $sarprasUnit->kepala_bengkel ?? ($procurement->verifier->name ?? ($sarprasUser->name ?? 'Waka Bidang Sarana & Prasarana')) }}</u></strong>
                        <div style="font-size: 9.5pt;">NIP/NPY: {{ $sarprasUnit->nip ?? ($procurement->verifier->nip ?? ($sarprasUser->nip ?? '.......................................')) }}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center; padding-top: 20px; vertical-align: top;">
                        Mengetahui / Menyetujui,<br>
                        <strong>Kepala SMK Dr. Sutomo Temanggung</strong>
                        <div style="height: 75px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-top: 3px;">
                            @if($procurement->ttd_kepsek)
                                <img src="{{ Storage::url($procurement->ttd_kepsek) }}" alt="TTD Kepsek" style="max-height: 55px; max-width: 160px; object-fit: contain;">
                                <div style="font-size: 6.5pt; color: #64748b; font-style: italic; margin-top: 2px; line-height: 1.1;">
                                    Disetujui &amp; TTD elektronik: {{ $procurement->ttd_kepsek_at ? $procurement->ttd_kepsek_at->translatedFormat('d/m/Y H:i') : '' }}
                                </div>
                            @elseif($procurement->status_kepsek === 'disetujui')
                                <div style="border: 1px solid #10b981; color: #047857; padding: 3px 8px; font-size: 7.5pt; border-radius: 4px; font-weight: bold;">
                                    DISETUJUI SECARA ELEKTRONIK OLEH KEPALA SEKOLAH
                                </div>
                            @endif
                        </div>
                        <strong><u>{{ $kepsekUser->name ?? ($procurement->approverKepsek->name ?? 'Bpk. Kepala Sekolah, M.Pd') }}</u></strong>
                        <div style="font-size: 9.5pt;">NIP/NPY: {{ $kepsekUser->nip ?? ($procurement->approverKepsek->nip ?? '.......................................') }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
