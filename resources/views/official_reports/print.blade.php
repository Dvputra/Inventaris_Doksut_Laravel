<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $officialReport->nomor_surat }} - Berita Acara Sarpras</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }

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
            width: 210mm; /* A4 Portrait */
            min-height: 297mm;
            margin: 0 auto;
            padding: 15mm 18mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 2px;
            color: #000000;
        }

        .kop-surat {
            width: 100%;
            margin-bottom: 8px;
        }

        .kop-img {
            width: 100%;
            height: auto;
            display: block;
        }

        .title-block {
            text-align: center;
            margin: 12px 0 16px;
        }

        .title-block h4 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .title-block .nomor {
            font-size: 11pt;
            font-weight: bold;
        }

        p, .text-justify {
            text-align: justify;
            text-justify: inter-word;
            margin-bottom: 8px;
        }

        .parties-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }

        .parties-table td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 10.5pt;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            margin: 12px 0 16px;
        }

        .table-items th {
            background-color: #f2f2f2 !important;
            border: 1px solid #000000 !important;
            padding: 6px 4px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .table-items td {
            border: 1px solid #000000 !important;
            padding: 5px 6px;
            vertical-align: top;
        }

        /* Signature block */
        .ttd-wrapper {
            margin-top: 24px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            font-size: 10.5pt;
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
                background: none !important;
                padding: 0 !important;
            }
            .paper {
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
            .action-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Bar -->
    <div class="action-bar d-print-none">
        <a href="{{ route('official-reports.show', $officialReport) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm font-semibold">
            <i class="bi bi-printer-fill me-1"></i> Cetak / Simpan PDF
        </button>
    </div>

    <!-- Paper Lembar Surat -->
    <div class="paper">
        <!-- Kop Surat Resmi -->
        <div class="kop-surat">
            <img src="{{ asset('images/kop-surat-header.png') }}" alt="Kop Surat Resmi SMK Dr. Sutomo Temanggung" class="kop-img">
        </div>

        <!-- Judul Berita Acara -->
        <div class="title-block">
            <h4>
                @if(str_contains($officialReport->judul, 'Pemeriksaan dan Penghapusan Barang Rusak Berat'))
                    {{ str_replace('Berita Acara Pemeriksaan dan Penghapusan Barang Rusak Berat', 'Berita Acara Barang Rusak', $officialReport->judul) }}
                @else
                    {{ $officialReport->judul }}
                @endif
            </h4>
            <div class="nomor">Nomor: {{ $officialReport->nomor_surat }}</div>
        </div>

        <!-- Kalimat Pembuka Resmi -->
        <p class="text-justify">
            Pada hari ini, <strong>{{ $officialReport->tanggal->translatedFormat('l') }}</strong> tanggal <strong>{{ $officialReport->tanggal->translatedFormat('d') }}</strong> bulan <strong>{{ $officialReport->tanggal->translatedFormat('F') }}</strong> tahun <strong>{{ $officialReport->tanggal->translatedFormat('Y') }}</strong> ({{ $officialReport->tanggal->format('d/m/Y') }}), bertempat di SMK Dr. Sutomo Temanggung, kami yang bertanda tangan di bawah ini:
        </p>

        <!-- Identitas Pihak-Pihak -->
        <table class="parties-table">
            <tr>
                <td style="width: 4%;">1.</td>
                <td style="width: 26%;">Nama</td>
                <td style="width: 2%;">:</td>
                <td style="width: 68%;"><strong>{{ $officialReport->pihak_pertama_nama }}</strong></td>
            </tr>
            @if($officialReport->pihak_pertama_nip)
            <tr>
                <td></td>
                <td>NIP / NUPTK</td>
                <td>:</td>
                <td>{{ $officialReport->pihak_pertama_nip }}</td>
            </tr>
            @endif
            <tr>
                <td></td>
                <td>Jabatan / Unit</td>
                <td>:</td>
                <td>{{ $officialReport->pihak_pertama_jabatan }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="3" style="padding-top: 2px; padding-bottom: 8px;">
                    Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.
                </td>
            </tr>

            <tr>
                <td>2.</td>
                <td>Nama</td>
                <td>:</td>
                <td><strong>{{ $officialReport->pihak_kedua_nama }}</strong></td>
            </tr>
            @if($officialReport->pihak_kedua_nip)
            <tr>
                <td></td>
                <td>NIP / NIY</td>
                <td>:</td>
                <td>{{ $officialReport->pihak_kedua_nip }}</td>
            </tr>
            @endif
            <tr>
                <td></td>
                <td>Jabatan / Status</td>
                <td>:</td>
                <td>{{ $officialReport->pihak_kedua_jabatan }}</td>
            </tr>
            @if($officialReport->pihak_kedua_instansi)
            <tr>
                <td></td>
                <td>Instansi / Alamat</td>
                <td>:</td>
                <td>{{ $officialReport->pihak_kedua_instansi }}</td>
            </tr>
            @endif
            @if($officialReport->pihak_kedua_kontak)
            <tr>
                <td></td>
                <td>No. Telepon / HP</td>
                <td>:</td>
                <td>{{ $officialReport->pihak_kedua_kontak }}</td>
            </tr>
            @endif
            <tr>
                <td></td>
                <td colspan="3" style="padding-top: 2px; padding-bottom: 8px;">
                    Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.
                </td>
            </tr>
        </table>

        <!-- Kronologi / Dasar Berita Acara -->
        <p class="text-justify">
            {{ $officialReport->latar_belakang ?? 'Menyatakan bahwa dengan mempertimbangkan kondisi fisik aset sarana dan prasarana yang ada pada lingkungan sekolah, bersama ini telah dilakukan pemeriksaan fisik bersama terhadap barang-barang inventaris dengan rincian sebagai berikut:' }}
        </p>

        <!-- Tabel Rincian Barang -->
        <table class="table-items">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 32%;">Nama Barang / Identitas Aset</th>
                    <th style="width: 18%;">Kode / No. Seri</th>
                    <th style="width: 12%;">Jumlah</th>
                    <th style="width: 16%;">Kondisi</th>
                    @if($officialReport->jenis === 'penjualan')
                        <th style="width: 16%;">Nilai (Rp)</th>
                    @else
                        <th style="width: 16%;">Keterangan</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($officialReport->items as $i => $item)
                    <tr>
                        <td style="text-align: center;">{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ $item->nama_barang }}</strong>
                            @if($item->unit_code)
                                <div style="font-size: 8.5pt; color: #333;">Unit: {{ $item->unit_code }}</div>
                            @endif
                        </td>
                        <td style="font-family: monospace; font-size: 9pt;">
                            {{ $item->kode_barang ?? ($item->nomor_seri ?? '-') }}
                        </td>
                        <td style="text-align: center;">
                            {{ $item->jumlah }} {{ $item->satuan }}
                        </td>
                        <td style="text-align: center;">
                            {{ ucwords(str_replace('_', ' ', $item->kondisi_saat_lapor)) }}
                        </td>
                        @if($officialReport->jenis === 'penjualan')
                            <td style="text-align: right;">
                                {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        @else
                            <td>
                                {{ $item->keterangan ?? '-' }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            @if($officialReport->jenis === 'penjualan')
                <tfoot>
                    <tr style="font-weight: bold; background-color: #f9f9f9;">
                        <td colspan="5" style="text-align: right; text-transform: uppercase;">Total Hasil Penjualan :</td>
                        <td style="text-align: right;">Rp {{ number_format($officialReport->total_nominal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <!-- Penutup Berita Acara -->
        <p class="text-justify">
            Demikian Berita Acara ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dapat dipergunakan sebagaimana mestinya dan sebagai bukti pertanggungjawaban pengelolaan aset sarana dan prasarana di lingkungan SMK Dr. Sutomo Temanggung.
        </p>

        <!-- Tanda Tangan Resmi 3 Pihak -->
        <div class="ttd-wrapper">
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <tr>
                    <td style="width: 45%; vertical-align: top;">
                        Pihak Kedua,<br>
                        <strong>{{ $officialReport->pihak_kedua_jabatan }}</strong>
                        <div style="height: 70px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            @if($officialReport->ttd_pihak_kedua)
                                <img src="{{ Storage::url($officialReport->ttd_pihak_kedua) }}" alt="TTD Pihak Kedua" style="max-height: 55px; max-width: 160px; object-contain: contain;">
                                <div style="font-size: 6.5pt; color: #64748b; font-style: italic; margin-top: 2px; line-height: 1.1;">
                                    Ditandatangani elektronik: {{ $officialReport->ttd_pihak_kedua_at ? $officialReport->ttd_pihak_kedua_at->translatedFormat('d/m/Y H:i') : '' }}
                                </div>
                            @endif
                        </div>
                        <strong><u>{{ $officialReport->pihak_kedua_nama }}</u></strong>
                        @if($officialReport->pihak_kedua_nip)
                            <div style="font-size: 9.5pt;">NIP/NIY: {{ $officialReport->pihak_kedua_nip }}</div>
                        @elseif($officialReport->pihak_kedua_instansi)
                            <div style="font-size: 9.5pt;">{{ $officialReport->pihak_kedua_instansi }}</div>
                        @endif
                    </td>
                    <td style="width: 10%;"></td>
                    <td style="width: 45%; vertical-align: top;">
                        Temanggung, {{ $officialReport->tanggal->translatedFormat('d F Y') }}<br>
                        Pihak Pertama,<br>
                        <strong>{{ $officialReport->pihak_pertama_jabatan }}</strong>
                        <div style="height: 70px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            @if($officialReport->ttd_pihak_pertama)
                                <img src="{{ Storage::url($officialReport->ttd_pihak_pertama) }}" alt="TTD Sarpras" style="max-height: 55px; max-width: 160px; object-contain: contain;">
                                <div style="font-size: 6.5pt; color: #64748b; font-style: italic; margin-top: 2px; line-height: 1.1;">
                                    Ditandatangani elektronik: {{ $officialReport->ttd_pihak_pertama_at ? $officialReport->ttd_pihak_pertama_at->translatedFormat('d/m/Y H:i') : '' }}
                                </div>
                            @endif
                        </div>
                        <strong><u>{{ $officialReport->pihak_pertama_nama }}</u></strong>
                        @if($officialReport->pihak_pertama_nip)
                            <div style="font-size: 9.5pt;">NIP: {{ $officialReport->pihak_pertama_nip }}</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center; padding-top: 20px;">
                        Mengetahui / Mengesahkan,<br>
                        <strong>{{ $officialReport->mengetahui_jabatan }}</strong>
                        <div style="height: 75px; display: flex; flex-direction: column; align-items: center; justify-content: center; margin-top: 3px;">
                            @if($officialReport->ttd_mengetahui)
                                <img src="{{ Storage::url($officialReport->ttd_mengetahui) }}" alt="TTD Kepsek" style="max-height: 55px; max-width: 160px; object-contain: contain;">
                                <div style="font-size: 6.5pt; color: #64748b; font-style: italic; margin-top: 2px; line-height: 1.1;">
                                    Disetujui &amp; TTD elektronik: {{ $officialReport->ttd_mengetahui_at ? $officialReport->ttd_mengetahui_at->translatedFormat('d/m/Y H:i') : '' }}
                                </div>
                            @elseif($officialReport->status_approval === 'disetujui')
                                <div style="border: 1px solid #10b981; color: #047857; padding: 3px 8px; font-size: 7.5pt; border-radius: 4px; font-weight: bold;">
                                    DISETUJUI SECARA ELEKTRONIK OLEH KEPALA SEKOLAH
                                </div>
                            @endif
                        </div>
                        <strong><u>{{ $officialReport->mengetahui_nama }}</u></strong>
                        @if($officialReport->mengetahui_nip)
                            <div style="font-size: 9.5pt;">NIP: {{ $officialReport->mengetahui_nip }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
