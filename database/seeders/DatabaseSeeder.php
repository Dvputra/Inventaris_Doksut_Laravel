<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Complaint;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\ItemUsage;
use App\Models\Jurusan;
use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Data Jurusan
        $jurusansData = [
            [
                'kode' => 'TKR',
                'nama' => 'Teknik Kendaraan Ringan',
                'kepala_bengkel' => 'Bpk. Budi Santoso, S.Pd',
                'deskripsi' => 'Bengkel praktikum otomotif roda 4 dan sistem mesin kendaraan ringan.',
                'email' => 'tkr@sekolah.sch.id',
            ],
            [
                'kode' => 'TITL',
                'nama' => 'Teknik Instalasi Tenaga Listrik',
                'kepala_bengkel' => 'Bpk. Eko Prasetyo, S.T',
                'deskripsi' => 'Laboratorium instalasi penerangan, tenaga, PLC, dan motor listrik.',
                'email' => 'titl@sekolah.sch.id',
            ],
            [
                'kode' => 'TKI',
                'nama' => 'Teknik Kimia Industri',
                'kepala_bengkel' => 'Ibu Siti Nurhaliza, M.Pd',
                'deskripsi' => 'Laboratorium analisis kimia, proses industri, dan instrumen reaksi.',
                'email' => 'tki@sekolah.sch.id',
            ],
            [
                'kode' => 'TP',
                'nama' => 'Teknik Pemesinan',
                'kepala_bengkel' => 'Bpk. Agus Setiawan, S.T',
                'deskripsi' => 'Bengkel mesin bubut, milling, CNC, dan fabrikasi logam.',
                'email' => 'tp@sekolah.sch.id',
            ],
            [
                'kode' => 'TKP',
                'nama' => 'Teknik Konstruksi dan Perumahan',
                'kepala_bengkel' => 'Bpk. Dedi Kurniawan, S.T',
                'deskripsi' => 'Bengkel konstruksi kayu, batu, beton, dan perencanaan gambar bangunan.',
                'email' => 'tkp@sekolah.sch.id',
            ],
            [
                'kode' => 'SAR',
                'nama' => 'Sarpras Pusat & Fasilitas Umum',
                'kepala_bengkel' => 'Waka Bidang Sarana & Prasarana',
                'deskripsi' => 'Gudang penyimpanan sarpras, Lab Komputer CBT/ANBK umum, ruang guru, ruang TU, aula, dan sarana umum sekolah.',
                'email' => 'sarpras.unit@sekolah.sch.id',
            ],
            [
                'kode' => 'KUR',
                'nama' => 'Kurikulum',
                'kepala_bengkel' => 'Waka Bidang Kurikulum',
                'deskripsi' => 'Unit kerja bidang kurikulum, perencanaan pembelajaran, dan asesmen akademik sekolah.',
                'email' => 'kurikulum@sekolah.sch.id',
            ],
            [
                'kode' => 'WMM',
                'nama' => 'Wakil Manajemen Mutu (WMM)',
                'kepala_bengkel' => 'Ketua WMM',
                'deskripsi' => 'Unit kerja penjaminan mutu pendidikan, audit mutu internal, standarisasi ISO, dan tata kelola mutu sekolah.',
                'email' => 'wmm@sekolah.sch.id',
            ],
            [
                'kode' => 'KES',
                'nama' => 'Kesiswaan',
                'kepala_bengkel' => 'Waka Bidang Kesiswaan',
                'deskripsi' => 'Unit kerja pembinaan karakter, kedisiplinan, OSIS/ekstrakurikuler, dan layanan kesiswaan.',
                'email' => 'kesiswaan@sekolah.sch.id',
            ],
            [
                'kode' => 'HUM',
                'nama' => 'Hubungan Masyarakat (Humas)',
                'kepala_bengkel' => 'Waka Bidang Humas',
                'deskripsi' => 'Unit kerja kerjasama industri, prakerin/PKL, bursa kerja khusus (BKK), dan kemitraan eksternal.',
                'email' => 'humas@sekolah.sch.id',
            ],
            [
                'kode' => 'TU',
                'nama' => 'Tata Usaha (TU)',
                'kepala_bengkel' => 'Kepala Tata Usaha',
                'deskripsi' => 'Unit tata usaha administrasi kepegawaian, persuratan, kearsipan, dan operasional kantor.',
                'email' => 'tu@sekolah.sch.id',
            ],
        ];

        // 2. Akun Admin Sarpras (Pusat)
        $sarprasUser = User::firstOrCreate(
            ['email' => 'sarpras@sekolah.sch.id'],
            [
                'name' => 'Admin Sarpras Pusat',
                'password' => Hash::make('password'),
                'role' => 'sarpras',
                'jurusan_id' => null,
            ]
        );

        // Buat Jurusan & Akun Jurusan
        $createdJurusans = [];
        $createdJurusanUsers = [];
        foreach ($jurusansData as $data) {
            $jurusan = Jurusan::firstOrCreate(
                ['kode' => $data['kode']],
                [
                    'nama' => $data['nama'],
                    'kepala_bengkel' => $data['kepala_bengkel'],
                    'deskripsi' => $data['deskripsi'],
                ]
            );

            $createdJurusans[$data['kode']] = $jurusan;

            $u = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => 'Akun '.$data['kode'].' ('.$data['nama'].')',
                    'password' => Hash::make('password'),
                    'role' => 'jurusan',
                    'jurusan_id' => $jurusan->id,
                ]
            );

            $createdJurusanUsers[$data['kode']] = $u;
        }

        // 3. Kategori Barang dengan Kode Standar
        $categoriesData = [
            ['kode' => 'KOM', 'nama' => 'Komputer & Perangkat IT', 'keterangan' => 'PC Lab, Laptop, Monitor, Printer, Jaringan'],
            ['kode' => 'MSN', 'nama' => 'Mesin & Peralatan Berat', 'keterangan' => 'Mesin industri, bubut, trainer, scanner otomotif'],
            ['kode' => 'TLS', 'nama' => 'Hand Tools / Perkakas Tangan', 'keterangan' => 'Kunci ring pas, obeng, tang, palu, gerinda'],
            ['kode' => 'UKR', 'nama' => 'Alat Ukur & Instrumentasi', 'keterangan' => 'Jangka sorong, mikrometer, multimeter, dial indicator'],
            ['kode' => 'ELK', 'nama' => 'Komponen & Alat Listrik', 'keterangan' => 'Kontaktor, MCB, relay, kabel rol, power supply'],
            ['kode' => 'BHN', 'nama' => 'Bahan Praktik Habis Pakai', 'keterangan' => 'Oli, amplas, elektroda las, reagen kimia, semen/kayu'],
            ['kode' => 'APD', 'nama' => 'Alat Pelindung Diri (K3)', 'keterangan' => 'Helm kerja, kacamata pelindung, sarung tangan, apron'],
            ['kode' => 'KDR', 'nama' => 'Kendaraan & Transportasi', 'keterangan' => 'Sepeda motor dinas, mobil operasional sekolah, bus sekolah, kendaraan praktik'],
            ['kode' => 'MBL', 'nama' => 'Mebel & Furnitur Ruangan', 'keterangan' => 'Meja guru/siswa, kursi, lemari arsip, rak buku, loker, kabinet dan perabot ruang'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['kode']] = Category::firstOrCreate(['kode' => $c['kode']], $c);
        }

        // 4. Sample Barang Awal untuk Jurusan

        // --- TKR ---
        $tkrEngine = Item::create([
            'kode_barang' => 'TKR-MSN-001',
            'nama_barang' => 'Engine Stand Toyota Avanza K3-VE',
            'jurusan_id' => $createdJurusans['TKR']->id,
            'category_id' => $categories['MSN']->id,
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Bengkel Otomotif Zona A',
            'jenis' => 'alat',
            'sumber_dana' => 'BOS Kinerja',
            'tahun_pengadaan' => 2023,
            'spesifikasi' => 'Mesin aktif lengkap dengan wiring harness & ECU simulator',
        ]);
        ItemUnit::create([
            'item_id' => $tkrEngine->id,
            'jurusan_id' => $createdJurusans['TKR']->id,
            'unit_code' => 'TKR-MSN-001-01',
            'nomor_seri' => 'ENG-AVZ-2023-01',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Bengkel Otomotif Bay 1',
        ]);
        ItemUnit::create([
            'item_id' => $tkrEngine->id,
            'jurusan_id' => $createdJurusans['TKR']->id,
            'unit_code' => 'TKR-MSN-001-02',
            'nomor_seri' => 'ENG-AVZ-2023-02',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Bengkel Otomotif Bay 2',
        ]);

        $tkrScanner = Item::create([
            'kode_barang' => 'TKR-UKR-001',
            'nama_barang' => 'OBD2 Scanner Diagnostic Tool MaxiSys',
            'jurusan_id' => $createdJurusans['TKR']->id,
            'category_id' => $categories['UKR']->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Ruang Toolman TKR',
            'jenis' => 'alat',
            'sumber_dana' => 'BOS Reguler',
            'tahun_pengadaan' => 2024,
            'spesifikasi' => 'Autel MaxiSys MS906 Pro Scanner',
        ]);
        ItemUnit::create([
            'item_id' => $tkrScanner->id,
            'jurusan_id' => $createdJurusans['TKR']->id,
            'unit_code' => 'TKR-UKR-001-01',
            'nomor_seri' => 'AUTEL-906-8871',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Lemari A1 Toolman TKR',
        ]);

        // Komputer Lab TKR (Simulasi ECU & Desain)
        $tkrPc = Item::create([
            'kode_barang' => 'TKR-KOM-001',
            'nama_barang' => 'PC Lab Simulasi Otomotif',
            'jurusan_id' => $createdJurusans['TKR']->id,
            'category_id' => $categories['KOM']->id,
            'jumlah' => 3,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Lab Komputer Otomotif',
            'jenis' => 'alat',
            'sumber_dana' => 'DAK Fisik',
            'tahun_pengadaan' => 2024,
            'is_computer' => true,
            'processor' => 'Intel Core i5-12400 (6 Core 12 Thread up to 4.4GHz)',
            'ram' => '16 GB DDR4 Dual Channel 3200MHz',
            'storage' => 'NVMe SSD 512 GB',
            'gpu_vga' => 'NVIDIA GeForce GTX 1650 4GB GDDR6',
            'monitor' => 'Monitor LED Samsung 24 Inch Full HD 75Hz',
            'sistem_operasi' => 'Windows 11 Pro 64-bit',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $unitCode = sprintf('TKR-KOM-001-%02d', $i);
            $meja = sprintf('Meja PC-%02d', $i);
            $kondisi = ($i === 3) ? 'rusak_ringan' : 'baik';
            $status = ($i === 3) ? 'dalam_perbaikan' : 'tersedia';

            $unit = ItemUnit::create([
                'item_id' => $tkrPc->id,
                'jurusan_id' => $createdJurusans['TKR']->id,
                'unit_code' => $unitCode,
                'nomor_seri' => 'SN-PC-TKR-2024-00'.$i,
                'nomor_meja' => $meja,
                'kondisi' => $kondisi,
                'status' => $status,
                'lokasi_penempatan' => 'Lab Komputer Otomotif ('.$meja.')',
                'catatan' => ($i === 3) ? 'Kipas power supply berisik, butuh dicek' : null,
            ]);

            if ($i === 3) {
                MaintenanceLog::create([
                    'item_unit_id' => $unit->id,
                    'jurusan_id' => $createdJurusans['TKR']->id,
                    'user_id' => $createdJurusanUsers['TKR']->id,
                    'tanggal' => now()->subDays(2),
                    'gejala_kerusakan' => 'Kipas power supply berputar tidak stabil dan mengeluarkan bunyi bising saat render simulasi.',
                    'tindakan_perbaikan' => 'Pembersihan debu kipas pendingin dan pelumasan bearing. Menunggu uji coba beban daya.',
                    'biaya' => 50000,
                    'teknisi_pelaksana' => 'Toolman TKR',
                    'status' => 'proses',
                ]);
            }
        }

        // --- TITL ---
        $titlPlc = Item::create([
            'kode_barang' => 'TITL-MSN-001',
            'nama_barang' => 'Trainer PLC Omron CP1E',
            'jurusan_id' => $createdJurusans['TITL']->id,
            'category_id' => $categories['MSN']->id,
            'jumlah' => 2,
            'satuan' => 'set',
            'kondisi' => 'baik',
            'lokasi' => 'Lab PLC & Otomasi',
            'jenis' => 'alat',
            'sumber_dana' => 'DAK Fisik',
            'tahun_pengadaan' => 2022,
        ]);
        ItemUnit::create([
            'item_id' => $titlPlc->id,
            'jurusan_id' => $createdJurusans['TITL']->id,
            'unit_code' => 'TITL-MSN-001-01',
            'nomor_seri' => 'OMRON-CP1E-N20-01',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Lab Otomasi Meja 1',
        ]);
        ItemUnit::create([
            'item_id' => $titlPlc->id,
            'jurusan_id' => $createdJurusans['TITL']->id,
            'unit_code' => 'TITL-MSN-001-02',
            'nomor_seri' => 'OMRON-CP1E-N20-02',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Lab Otomasi Meja 2',
        ]);

        // Bahan Praktik TITL
        $titlKabel = Item::create([
            'kode_barang' => 'TITL-BHN-001',
            'nama_barang' => 'Kabel NYA 1.5mm Supreme (Merah)',
            'jurusan_id' => $createdJurusans['TITL']->id,
            'category_id' => $categories['BHN']->id,
            'jumlah' => 5,
            'satuan' => 'roll',
            'kondisi' => 'baik',
            'lokasi' => 'Gudang Bahan Listrik',
            'jenis' => 'bahan',
            'sumber_dana' => 'BOS Reguler',
            'tahun_pengadaan' => 2024,
            'min_stok' => 2,
        ]);
        // Catat contoh pemakaian kabel
        ItemUsage::create([
            'item_id' => $titlKabel->id,
            'jurusan_id' => $createdJurusans['TITL']->id,
            'user_id' => $createdJurusanUsers['TITL']->id,
            'jumlah' => 1,
            'satuan' => 'roll',
            'tanggal_pemakaian' => now()->subDays(5),
            'nama_guru' => 'Bpk. Eko Prasetyo, S.T',
            'kelas' => 'XI TITL 1',
            'keperluan_jobsheet' => 'Praktik Instalasi Penerangan 2 Saklar Tukar 1 Lampu',
            'stok_sebelum' => 6,
            'stok_sesudah' => 5,
            'catatan' => 'Dipakai oleh kelompok 1 - 4 untuk instalasi di papan hubung bagi.',
        ]);

        // --- TKI ---
        Item::create([
            'kode_barang' => 'TKI-UKR-001',
            'nama_barang' => 'Spectrophotometer UV-Vis Digital',
            'jurusan_id' => $createdJurusans['TKI']->id,
            'category_id' => $categories['UKR']->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Lab Instrumen Kimia',
            'jenis' => 'alat',
            'sumber_dana' => 'DAK Fisik',
            'tahun_pengadaan' => 2021,
        ]);

        $tkiHcl = Item::create([
            'kode_barang' => 'TKI-BHN-001',
            'nama_barang' => 'Asam Klorida (HCl 37% PA) 1 Liter',
            'jurusan_id' => $createdJurusans['TKI']->id,
            'category_id' => $categories['BHN']->id,
            'jumlah' => 8,
            'satuan' => 'botol',
            'kondisi' => 'baik',
            'lokasi' => 'Lemari Asam Khusus Reagen',
            'jenis' => 'bahan',
            'sumber_dana' => 'BOS Reguler',
            'tahun_pengadaan' => 2024,
            'min_stok' => 3,
        ]);
        // Catat pemakaian HCl
        ItemUsage::create([
            'item_id' => $tkiHcl->id,
            'jurusan_id' => $createdJurusans['TKI']->id,
            'user_id' => $createdJurusanUsers['TKI']->id,
            'jumlah' => 2,
            'satuan' => 'botol',
            'tanggal_pemakaian' => now()->subDays(3),
            'nama_guru' => 'Ibu Siti Nurhaliza, M.Pd',
            'kelas' => 'XII TKI 2',
            'keperluan_jobsheet' => 'Jobsheet 4: Analisis Asidimetri & Standarisasi Larutan NaOH',
            'stok_sebelum' => 10,
            'stok_sesudah' => 8,
            'catatan' => 'Larutan diencerkan menjadi 0.1 N untuk 8 kelompok siswa.',
        ]);

        // --- TP ---
        $tpBubut = Item::create([
            'kode_barang' => 'TP-MSN-001',
            'nama_barang' => 'Mesin Bubut Konvensional Krisbow 1000mm',
            'jurusan_id' => $createdJurusans['TP']->id,
            'category_id' => $categories['MSN']->id,
            'jumlah' => 3,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Bengkel Pemesinan Blok B',
            'jenis' => 'alat',
            'sumber_dana' => 'BPOPP',
            'tahun_pengadaan' => 2020,
        ]);
        ItemUnit::create([
            'item_id' => $tpBubut->id,
            'jurusan_id' => $createdJurusans['TP']->id,
            'unit_code' => 'TP-MSN-001-01',
            'nomor_seri' => 'KB-LATHE-2020-01',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Bengkel Bubut Line 1',
        ]);
        ItemUnit::create([
            'item_id' => $tpBubut->id,
            'jurusan_id' => $createdJurusans['TP']->id,
            'unit_code' => 'TP-MSN-001-02',
            'nomor_seri' => 'KB-LATHE-2020-02',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Bengkel Bubut Line 2',
        ]);
        ItemUnit::create([
            'item_id' => $tpBubut->id,
            'jurusan_id' => $createdJurusans['TP']->id,
            'unit_code' => 'TP-MSN-001-03',
            'nomor_seri' => 'KB-LATHE-2020-03',
            'kondisi' => 'rusak_ringan',
            'status' => 'dalam_perbaikan',
            'lokasi_penempatan' => 'Bengkel Bubut Line 3',
            'catatan' => 'Headstock transmisi gigi 3 terdengar getar',
        ]);

        // Komputer CAD/CAM TP
        $tpPc = Item::create([
            'kode_barang' => 'TP-KOM-001',
            'nama_barang' => 'Workstation PC Lab Desain CAD/CAM CNC',
            'jurusan_id' => $createdJurusans['TP']->id,
            'category_id' => $categories['KOM']->id,
            'jumlah' => 4,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Lab CAD/CAM Pemesinan',
            'jenis' => 'alat',
            'sumber_dana' => 'DAK Fisik',
            'tahun_pengadaan' => 2023,
            'is_computer' => true,
            'processor' => 'AMD Ryzen 5 5600G (6 Cores 12 Threads)',
            'ram' => '16 GB DDR4 3200MHz',
            'storage' => 'SSD M.2 NVMe 500 GB',
            'gpu_vga' => 'NVIDIA GTX 1660 Super 6GB GDDR6',
            'monitor' => 'Philips 24 Inch Full HD IPS',
            'sistem_operasi' => 'Windows 10 Pro 64-bit',
        ]);
        for ($i = 1; $i <= 4; $i++) {
            ItemUnit::create([
                'item_id' => $tpPc->id,
                'jurusan_id' => $createdJurusans['TP']->id,
                'unit_code' => sprintf('TP-KOM-001-%02d', $i),
                'nomor_seri' => 'CAD-TP-2023-00'.$i,
                'nomor_meja' => sprintf('Meja PC-%02d', $i),
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'lokasi_penempatan' => sprintf('Lab CAD/CAM (Meja PC-%02d)', $i),
            ]);
        }

        // --- TKP ---
        Item::create([
            'kode_barang' => 'TKP-UKR-001',
            'nama_barang' => 'Total Station Topcon GM-50',
            'jurusan_id' => $createdJurusans['TKP']->id,
            'category_id' => $categories['UKR']->id,
            'jumlah' => 2,
            'satuan' => 'set',
            'kondisi' => 'baik',
            'lokasi' => 'Ruang Ukur Tanah TKP',
            'jenis' => 'alat',
            'sumber_dana' => 'DAK Fisik',
            'tahun_pengadaan' => 2022,
        ]);

        // --- SAR (Sarpras Pusat, Gudang & Fasilitas Umum) ---
        // 1. Komputer Lab CBT / ANBK Umum Sekolah
        $sarPc = Item::create([
            'kode_barang' => 'SAR-KOM-001',
            'nama_barang' => 'PC Client Workstation Lab CBT / ANBK Umum',
            'jurusan_id' => $createdJurusans['SAR']->id,
            'category_id' => $categories['KOM']->id,
            'jumlah' => 5,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Lab Komputer CBT 1 (Umum)',
            'jenis' => 'alat',
            'sumber_dana' => 'BOS Kinerja',
            'tahun_pengadaan' => 2024,
            'is_computer' => true,
            'processor' => 'Intel Core i5-11400 (6 Cores up to 4.40 GHz)',
            'ram' => '16 GB DDR4 3200MHz',
            'storage' => 'SSD 512 GB M.2 NVMe',
            'gpu_vga' => 'Intel UHD Graphics 730',
            'monitor' => 'Monitor LED Acer 21.5 Inch Full HD',
            'sistem_operasi' => 'Windows 11 Pro Edu',
        ]);
        for ($i = 1; $i <= 5; $i++) {
            ItemUnit::create([
                'item_id' => $sarPc->id,
                'jurusan_id' => $createdJurusans['SAR']->id,
                'unit_code' => sprintf('SAR-KOM-001-%02d', $i),
                'nomor_seri' => 'SN-CBT-DOKSUT-2024-0'.$i,
                'nomor_meja' => sprintf('Meja PC-%02d', $i),
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'lokasi_penempatan' => sprintf('Lab Komputer CBT 1 (Meja PC-%02d)', $i),
            ]);
        }

        // 2. Proyektor Aula & Unit Cadangan Gudang Sarpras
        $sarProjector = Item::create([
            'kode_barang' => 'SAR-KOM-002',
            'nama_barang' => 'Proyektor Epson EB-E500 3300 Lumens',
            'jurusan_id' => $createdJurusans['SAR']->id,
            'category_id' => $categories['KOM']->id,
            'jumlah' => 2,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Aula Utama & Gudang Sarpras',
            'jenis' => 'alat',
            'sumber_dana' => 'BOS Reguler',
            'tahun_pengadaan' => 2023,
            'spesifikasi' => '3300 Lumens XGA 3LCD HDMI VGA Portable',
        ]);
        ItemUnit::create([
            'item_id' => $sarProjector->id,
            'jurusan_id' => $createdJurusans['SAR']->id,
            'unit_code' => 'SAR-KOM-002-01',
            'nomor_seri' => 'EPS-E500-AULA-01',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Aula Utama Gedung Serbaguna',
        ]);
        ItemUnit::create([
            'item_id' => $sarProjector->id,
            'jurusan_id' => $createdJurusans['SAR']->id,
            'unit_code' => 'SAR-KOM-002-02',
            'nomor_seri' => 'EPS-E500-GDG-02',
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'lokasi_penempatan' => 'Gudang Sarpras (Unit Cadangan Acara)',
        ]);

        // 3. Genset Fasilitas Lapangan Upacara / Gedung
        Item::create([
            'kode_barang' => 'SAR-MSN-001',
            'nama_barang' => 'Genset Silent Perkins 15 kVA',
            'jurusan_id' => $createdJurusans['SAR']->id,
            'category_id' => $categories['MSN']->id,
            'jumlah' => 1,
            'satuan' => 'unit',
            'kondisi' => 'baik',
            'lokasi' => 'Rumah Genset Belakang Lapangan',
            'jenis' => 'alat',
            'sumber_dana' => 'DAK Fisik',
            'tahun_pengadaan' => 2021,
            'spesifikasi' => 'Diesel Silent 15 kVA 3 Phase 380V Auto Transfer Switch',
        ]);

        // 4. Bahan Stok Gudang Sarpras (Lampu LED untuk Penggantian Rutin)
        $sarLampu = Item::create([
            'kode_barang' => 'SAR-BHN-001',
            'nama_barang' => 'Lampu LED Bulb Philips 14W Putih (Cool Daylight)',
            'jurusan_id' => $createdJurusans['SAR']->id,
            'category_id' => $categories['BHN']->id,
            'jumlah' => 20,
            'satuan' => 'pcs',
            'kondisi' => 'baik',
            'lokasi' => 'Gudang Sarpras Rak Elektrik B1',
            'jenis' => 'bahan',
            'sumber_dana' => 'BOS Reguler',
            'tahun_pengadaan' => 2024,
            'min_stok' => 5,
        ]);

        // Catat mutasi pemakaian lampu dari gudang sarpras
        ItemUsage::create([
            'item_id' => $sarLampu->id,
            'jurusan_id' => $createdJurusans['SAR']->id,
            'user_id' => $sarprasUser->id,
            'jumlah' => 4,
            'satuan' => 'pcs',
            'tanggal_pemakaian' => now()->subDays(1),
            'nama_guru' => 'Pak Joko (Teknisi Sarpras)',
            'kelas' => 'Gedung Teori Lantai 1',
            'keperluan_jobsheet' => 'Pergantian lampu mati di Ruang Kelas X TP 1 dan Selasar',
            'stok_sebelum' => 24,
            'stok_sesudah' => 20,
            'catatan' => 'Pemasangan langsung oleh teknisi sarpras.',
        ]);

        // 5. Perkakas Gudang Sarpras
        Item::create([
            'kode_barang' => 'SAR-TLS-001',
            'nama_barang' => 'Mesin Bor Tangan Impact Drill Bosch GSB 550',
            'jurusan_id' => $createdJurusans['SAR']->id,
            'category_id' => $categories['TLS']->id,
            'jumlah' => 2,
            'satuan' => 'set',
            'kondisi' => 'baik',
            'lokasi' => 'Gudang Sarpras Lemari Perkakas',
            'jenis' => 'alat',
            'sumber_dana' => 'BOS Reguler',
            'tahun_pengadaan' => 2023,
            'spesifikasi' => '13mm 550 Watt Impact Drill Set Aksesoris Mata Bor',
        ]);

        // 5. Data Sampel Pengaduan Kendala Fasilitas dari Guru (Tanpa Login)
        Complaint::create([
            'ticket_code' => 'ADU-2609-01A1',
            'nama_pelapor' => 'Bpk. Hendra Wijaya, S.Kom',
            'kontak' => '081234567801',
            'jurusan_id' => $createdJurusans['TP']->id,
            'lokasi_ruang' => 'Lab Komputer CAD/CAM (Meja PC-03)',
            'kategori' => 'komputer_it',
            'item_id' => $tpPc->id,
            'judul_kendala' => 'Layar Monitor Blank Hitam saat Render Desain 3D',
            'deskripsi' => 'Saat siswa menjalankan software simulasi CNC dan render 3D, monitor tiba-tiba tidak ada sinyal (blank). Kabel HDMI sudah dicoba ganti namun masih kendala.',
            'tingkat_urgensi' => 'sedang',
            'status' => 'diproses',
            'teknisi_penanganan' => 'Toolman TP & Tim IT',
            'tindak_lanjut' => 'Driver GPU sedang di-install ulang dan dilakukan pengetesan port adapter display.',
        ]);

        Complaint::create([
            'ticket_code' => 'ADU-2609-02B2',
            'nama_pelapor' => 'Ibu Rina Astuti, S.Pd',
            'kontak' => '085712345678',
            'jurusan_id' => null,
            'lokasi_ruang' => 'Ruang Guru Lantai 2',
            'kategori' => 'sarana_gedung',
            'judul_kendala' => 'Kipas Angin Dinding Berdecit & Putaran Melambat',
            'deskripsi' => 'Kipas angin dinding bagian belakang ruang guru berbunyi bising dan putaran motor sangat lambat, mohon dibersihkan atau dicek pelumasnya.',
            'tingkat_urgensi' => 'rendah',
            'status' => 'selesai',
            'teknisi_penanganan' => 'Pak Joko (Teknisi Sarpras)',
            'tindak_lanjut' => 'Kapasitor dan bearing kipas telah dibersihkan serta diberi pelumas baru. Kipas sudah normal kembali.',
            'tanggal_selesai' => now()->subDay(),
        ]);

        Complaint::create([
            'ticket_code' => 'ADU-2609-03C3',
            'nama_pelapor' => 'Bpk. Ahmad Fauzi, S.T',
            'kontak' => '087812345679',
            'jurusan_id' => $createdJurusans['TITL']->id,
            'lokasi_ruang' => 'Lab PLC & Instalasi Tenaga',
            'kategori' => 'kelistrikan',
            'judul_kendala' => 'Stopkontak Meja Praktik 4 Tidak Mengalirkan Arus',
            'deskripsi' => 'Tegangan tidak keluar di stopkontak meja 4 saat siswa hendak menghidupkan Trainer PLC. Diduga kabel grounding atau sambungan dalam putus.',
            'tingkat_urgensi' => 'tinggi_darurat',
            'status' => 'menunggu',
        ]);
    }
}
