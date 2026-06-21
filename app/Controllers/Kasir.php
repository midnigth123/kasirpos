<?php

namespace App\Controllers;

use App\Models\ProdukModel;

class Kasir extends BaseController
{

    public function index()
    {
        $db = $this->db;
        $model = new \App\Models\ProdukModel();

        // JEDOR! Pastikan Waktu Jakarta agar filter $today akurat
        date_default_timezone_set('Asia/Jakarta');
        $today  = date('Y-m-d');
        $userId = session()->get('id_user');
        $role   = session()->get('role');

        // 1. CEK SESSION LOGIN
        if (!$userId) {
            return redirect()->to(site_url('login'));
        }

        // Ambil setting (Termasuk nilai PAJAK di sini)
        $setting = $db->table('pengaturan')->where('id', 1)->get()->getRowArray();

        $uri = service('request')->getUri();
        $currentPath = rtrim($uri->getPath(), '/');

        // --- 1. VALIDASI BERLAPIS KHUSUS ROLE KASIR ---
        if ($role === 'kasir') {
            $cekAbsen = $db->table('absensi')
                ->where(['id_user' => $userId, 'tanggal' => $today])
                ->get()->getRowArray();

            if (!$cekAbsen) {
                return redirect()->to(site_url('kasir/absen'));
            }

            $cekShift = $db->table('kasir_shift_transaksi')
                ->where(['id_user' => $userId, 'status' => 'open', 'tanggal_buka' => $today])
                ->get()->getRowArray();

            if (!$cekShift) {
                if (strpos($currentPath, 'open-kasir') === false) {
                    return redirect()->to(site_url('kasir/open-kasir'));
                }

                $dataOpen = [
                    'master_shift' => $db->table('shift_kasir')->get()->getResultArray(),
                    'nama_user'    => session()->get('nama_user'),
                    'setting'      => $setting, // Pajak juga terbawa ke halaman open kasir jika butuh
                    'title'        => 'Buka Kasir - Input Saldo Awal'
                ];
                return view('kasir/open_kasir', $dataOpen);
            }
        } else {
            $cekShift = null;
        }

        // --- 2. LOGIKA RESEP ---
        $dataResepMentah = $db->table('resep')
            ->select('resep.id_produk_jual, resep.id_bahan_baku, resep.jumlah_kebutuhan, produk.stok as stok_bahan, produk.nama_produk as nama_bahan')
            ->join('produk', 'resep.id_bahan_baku = produk.produk_id')
            ->get()->getResultArray();

        $resepMapped = [];
        $listIdResep = [];
        foreach ($dataResepMentah as $row) {
            $listIdResep[] = $row['id_produk_jual'];
            $resepMapped[$row['id_produk_jual']][] = [
                'nama_bahan' => $row['nama_bahan'],
                'butuh'      => (float)$row['jumlah_kebutuhan'],
                'stok_bahan' => (float)$row['stok_bahan']
            ];
        }
        $listIdResep = array_values(array_unique($listIdResep));

        // --- 3. REKAP PENDAPATAN ---
        $builder = $db->table('transaksi');
        $builder->select("
            SUM(CASE WHEN status = 'Lunas' THEN total_bayar ELSE 0 END) as total_pendapatan,
            SUM(CASE WHEN status = 'Lunas' AND metode_pembayaran = 'Tunai' THEN total_bayar ELSE 0 END) as total_tunai,
            SUM(CASE WHEN status = 'Lunas' AND metode_pembayaran = 'QRIS' THEN total_bayar ELSE 0 END) as total_qris,
            SUM(CASE WHEN status = 'Lunas' AND metode_pembayaran = 'Transfer' THEN total_bayar ELSE 0 END) as total_transfer,
            SUM(CASE WHEN status = 'Lunas' AND metode_pembayaran = 'EDC' THEN total_bayar ELSE 0 END) as total_edc,
            SUM(CASE WHEN status = 'Lunas' AND metode_pembayaran LIKE 'Online%' THEN total_bayar ELSE 0 END) as total_online,
            
            COUNT(CASE WHEN status = 'Batal' THEN 1 END) as jumlah_batal,
            COUNT(CASE WHEN status = 'Lunas' THEN 1 END) as total_transaksi
        ", false);

        if ($role === 'kasir') {
            $builder->where('id_user', $userId);
        }
        $builder->where("DATE(created_at)", $today);
        $rekap = $builder->get()->getRowArray();

        // --- 4. DATA ANTREAN MEJA ---
        $antrean = $db->table('pesanan_temp pt')
            ->select('pt.*, m.nomor_meja')
            ->join('meja m', 'm.id_meja = pt.id_meja')
            ->where('pt.status_order', 'Pending')
            ->orderBy('pt.created_at', 'DESC')
            ->get()->getResultArray();

        // --- 5. SINKRONISASI KERANJANG ---
        $keranjangDb = $db->table('penjualan_temp pt')
            ->select('pt.id_temp, pt.produk_id, pt.jumlah, p.nama_produk, p.harga_jual')
            ->join('produk p', 'p.produk_id = pt.produk_id')
            ->where('pt.id_user', $userId)
            ->get()->getResultArray();


        // --- 6. PREPARASI DATA VIEW (Lengkap dengan data pajak dari $setting) ---
        $data = [
            'produk'            => $model->whereIn('jenis_stok', ['Kering', 'Basah'])->findAll(),
            'list_id_resep'     => $listIdResep,
            'data_resep'        => $resepMapped,
            'rekap'             => $rekap,
            'today'             => $today,
            'shift'             => $cekShift,
            'setting'           => $setting, // <--- Ini yang bawa data PAJAK ke View Kasir
            'master_shift'      => $db->table('shift_kasir')->get()->getResultArray(),
            'nama_user'         => session()->get('nama_user'),
            'role'              => $role,
            'antrean_meja'      => $antrean,
            'jumlah_antrean'    => count($antrean),
            'cart_database'     => $keranjangDb,
            'total_pengeluaran' => $db->table('pengeluaran_harian')->selectSum('total_bayar')->where('tanggal', $today)->get()->getRow()->total_bayar ?? 0,
            'title'             => "Kasir"
        ];

        return view('kasir/kasir_view', $data);
    }

    public function bayar()
    {
        $db = $this->db;
        $json = $this->request->getJSON();

        if (!$json || empty($json->cart)) {
            return $this->response->setJSON(['status' => 'fail', 'message' => 'Keranjang kosong']);
        }

        $userId = session()->get('id_user');
        $invoice = 'INV-' . time();
        $waktuSekarang = date('Y-m-d H:i:s');

        // --- 1. DETEKSI NOMOR MEJA ---
        $nomorMejaRaw = $json->nomor_meja ?? null;
        if (empty($nomorMejaRaw)) {
            $track = $db->table('pesanan_temp')->orderBy('created_at', 'DESC')->get()->getRowArray();
            if ($track) {
                $nomorMejaRaw = $track['nomor_meja'] ?? $track['id_meja'] ?? null;
            }
        }
        $mejaFix = (!empty($nomorMejaRaw)) ? (int) preg_replace('/[^0-9]/', '', (string)$nomorMejaRaw) : null;

        $db->transBegin();

        try {
            // --- 2. SIMPAN HEADER TRANSAKSI ---
            $db->table('transaksi')->insert([
                'invoice'           => $invoice,
                'id_user'           => $userId,
                'id_meja'           => $mejaFix,
                'nomor_meja'        => $mejaFix,
                'total_bayar'       => (int)$json->total,
                'diskon'            => (int)($json->diskon ?? 0),
                'tipe_pesanan'      => $json->tipe_pesanan ?? 'Dine In',
                'nominal_uang'      => (int)$json->bayar,
                'kembalian'         => (int)$json->bayar - (int)$json->total,
                'metode_pembayaran' => $json->metode ?? 'Tunai',
                'status'            => 'Lunas',
                'order_at'          => $waktuSekarang,
                'created_at'        => $waktuSekarang
            ]);

            $transId = $db->insertID();

            // --- 3. PROSES MEMBER ---
            if (isset($json->id_member) && !empty($json->id_member)) {
                $total_bayar_angka = (int)$json->total;
                $poin_dipakai = (int)($json->poin_dipakai ?? 0);
                $poin_baru = 1;

                $db->table('member')
                    ->where('id_member', $json->id_member)
                    ->set('total_poin', "COALESCE(total_poin, 0) - $poin_dipakai + $poin_baru", false)
                    ->set('total_transaksi', "COALESCE(total_transaksi, 0) + $total_bayar_angka", false)
                    ->update();
            }

            // --- 4. PROSES KERANJANG, STOK (KARTU STOK), DAN HARGA BELI ---
            foreach ($json->cart as $item) {
                // Ambil data produk terbaru (getRow agar jadi objek)
                $p = $db->table('produk')->where('produk_id', $item->id)->get()->getRow();
                if (!$p) throw new \Exception("Produk ID {$item->id} tidak ditemukan!");

                $hargaModal = $p->harga_beli ?? $p->harga_pokok ?? 0;

                // Cek Resep
                $resep = $db->table('resep')->where('id_produk_jual', $item->id)->get()->getResultArray();

                if (empty($resep)) {
                    // --- A. PRODUK LANGSUNG ---
                    if ((float)$p->stok < (float)$item->qty) throw new \Exception("Stok " . $p->nama_produk . " habis!");

                    $stok_awal_riil = (float)$p->stok;
                    $qty_jual       = (float)$item->qty;
                    $stok_akhir     = $stok_awal_riil - $qty_jual;

                    $db->table('kartu_stok')->insert([
                        'produk_id'      => $item->id,
                        'tipe'           => 'keluar',
                        'kode_referensi' => $invoice,
                        'stok_awal'      => $stok_awal_riil,
                        'stok_masuk'     => 0,
                        'stok_keluar'    => $qty_jual,
                        'stok_akhir'     => $stok_akhir,
                        'keterangan'     => 'Penjualan Kasir',
                        'tanggal'        => $waktuSekarang
                    ]);

                    // Update stok produk
                    $db->table('produk')->where('produk_id', $item->id)->update(['stok' => $stok_akhir]);
                } else {
                    // --- B. POTONG BAHAN BAKU (RESEP) ---
                    foreach ($resep as $r) {
                        $bahan = $db->table('produk')->where('produk_id', $r['id_bahan_baku'])->get()->getRow();
                        $jumlahPotong = (float)$r['jumlah_kebutuhan'] * (int)$item->qty;

                        if ((float)$bahan->stok < $jumlahPotong) throw new \Exception("Bahan baku " . $bahan->nama_produk . " kurang!");

                        $stok_awal_bahan = (float)$bahan->stok;
                        $stok_akhir_bahan = $stok_awal_bahan - $jumlahPotong;

                        $db->table('kartu_stok')->insert([
                            'produk_id'      => $r['id_bahan_baku'],
                            'tipe'           => 'keluar',
                            'kode_referensi' => $invoice,
                            'stok_awal'      => $stok_awal_bahan,
                            'stok_masuk'     => 0,
                            'stok_keluar'    => $jumlahPotong,
                            'stok_akhir'     => $stok_akhir_bahan,
                            'keterangan'     => 'Bahan Resep: ' . $p->nama_produk,
                            'tanggal'        => $waktuSekarang
                        ]);

                        // Update stok bahan baku
                        $db->table('produk')->where('produk_id', $r['id_bahan_baku'])->update(['stok' => $stok_akhir_bahan]);
                    }
                }

                // SIMPAN KE DETAIL TRANSAKSI
                $db->table('transaksi_detail')->insert([
                    'transaksi_id' => $transId,
                    'produk_id'    => $item->id,
                    'qty'          => $item->qty,
                    'harga_satuan' => $item->harga,
                    'harga_beli'   => $hargaModal,
                    'subtotal'     => (int)$item->harga * (int)$item->qty,
                    'catatan'      => $item->catatan ?? null
                ]);
            }

            // --- 5. PEMBERSIHAN DATA TEMPORARY ---
            $db->table('penjualan_temp')->where('id_user', $userId)->delete();

            if (!empty($mejaFix)) {
                $db->table('pesanan_temp')->where('nomor_meja', $mejaFix)->orWhere('id_meja', $mejaFix)->delete();

                // --- 6. UPDATE STATUS MEJA ---
                $db->table('meja')
                    ->where('nomor_meja', $mejaFix)
                    ->orWhere('id_meja', $mejaFix)
                    ->update(['status_meja' => 'Terisi']);

                // --- 7. UPDATE RESERVASI ---
                $db->table('reservasi')
                    ->where('nomor_me_ja', $mejaFix)
                    ->orWhere('nomor_meja', $mejaFix)
                    ->where('status_reservasi', 'Pending')
                    ->update(['status_reservasi' => 'Check-in']);
            }

            // --- JEDOR! 8. OTOMATIS CATAT OMZET KE TABEL CASH FLOW ---
            $lastCashflow = $db->table('cash_flow')->orderBy('id_cashflow', 'DESC')->limit(1)->get()->getRow();
            $saldoTerakhir = $lastCashflow ? $lastCashflow->saldo_akhir : 0;

            $nominalMasuk = (int)$json->total;
            $saldoBaru = $saldoTerakhir + $nominalMasuk;

            $db->table('cash_flow')->insert([
                'tanggal'     => $waktuSekarang,
                'kategori'    => 'Operasional',
                'keterangan'  => 'Pendapatan Penjualan Kasir - Invoice: ' . $invoice,
                'masuk'       => $nominalMasuk,
                'keluar'      => 0,
                'saldo_akhir' => $saldoBaru,
                'created_at'  => $waktuSekarang
            ]);

            $db->transCommit();
            return $this->response->setJSON(['status' => 'success', 'message' => 'Pembayaran Berhasil! Kartu Stok & Cash Flow Terupdate!']);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    // // FUNGSI ABSEN
    // public function absen_masuk()
    // {
    //     return view('kasir/absen_masuk_view');
    // }

    public function absen_masuk()
    {
        $db = $this->db;
        $session = session();

        // 🎯 FIX 1: Kunci koneksi database outlet aktif
        $nama_db = $session->get('nama_database') ?? $session->get('db_client');
        if ($nama_db) {
            $db->setDatabase($nama_db);
        }

        $userId = $session->get('id_user');
        $today  = date('Y-m-d');

        if (!$userId) {
            return redirect()->to(site_url('login'));
        }

        // 🎯 KUNCI UTAMA 1: Periksa apakah kasir sudah pernah absen hari ini
        $sudahAbsen = $db->table('absensi')
            ->where(['id_user' => $userId, 'tanggal' => $today])
            ->get()->getRowArray();

        // 🚀 BYPASS HALAMAN: Kalau sudah absen hari ini, langsung terbangkan ke dashboard!
        if ($sudahAbsen) {
            $session->set('is_absen', true);
            return redirect()->to(site_url('admin/dashboard'))->with('info', 'Anda sudah melakukan absen masuk hari ini.');
        }

        return view('kasir/absen_masuk_view');
    }

    public function simpan_absen()
    {
        $db = $this->db;
        $session = session();

        // 🎯 FIX 2: Amankan database outlet aktif untuk jalur AJAX
        $nama_db = $session->get('nama_database') ?? $session->get('db_client');
        if ($nama_db) {
            $db->setDatabase($nama_db);
        }

        // 1. Ambil data session
        $userId = $session->get('id_user');
        $today  = date('Y-m-d');

        // PENGAMAN: Jika session hilang / tidak login, tendang balik atau kasih eror
        if (!$userId) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Sesi login habis! Silakan login ulang.'
            ]);
        }

        // 🎯 KUNCI UTAMA 2: Hadang di gerbang simpan AJAX (Mencegah double click / bypass link manual)
        $sudahAbsen = $db->table('absensi')
            ->where(['id_user' => $userId, 'tanggal' => $today])
            ->get()->getRowArray();

        if ($sudahAbsen) {
            $session->set('is_absen', true);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Anda sudah tercatat absen hari ini. Mengalihkan ke dashboard...'
            ]);
        }

        $shift = $this->request->getPost('shift');
        $fotoData = $this->request->getPost('image_tag');

        // 2. Siapkan data untuk masuk DB
        $dataInsert = [
            'id_user'    => $userId,
            'nama_shift' => $shift,
            'tanggal'    => $today,
            'jam_masuk'  => date('H:i:s'),
            'foto'       => $fotoData,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // 3. Eksekusi ke DB dengan pengaman Try-Catch
        try {
            $db->table('absensi')->insert($dataInsert);
            $session->set('is_absen', true);

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Selamat bekerja! Absen masuk kamu sudah tercatat.'
            ]);
        } catch (\Exception $e) {
            // Jika DB menolak, kirim pesan erornya ke JSON
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal masuk DB: ' . $e->getMessage()
            ]);
        }
    }
    public function tutupKasir()
    {
        $db = $this->db;
        $session = session();

        // 🎯 FIX 1: Kunci database outlet aktif sebelum query berjalan (Anti Eror #1046)
        $nama_db = $session->get('nama_database') ?? $session->get('db_client');
        if ($nama_db) {
            $db->setDatabase($nama_db);
        }

        $userId = $session->get('id_user');
        $today  = date('Y-m-d');

        if (!$userId) {
            return redirect()->to(site_url('login'));
        }

        // 1. Ambil data shift yang masih 'open'
        $shiftAktif = $db->table('kasir_shift_transaksi')
            ->where(['id_user' => $userId, 'status' => 'open', 'tanggal_buka' => $today])
            ->get()->getRowArray();

        if (!$shiftAktif) {
            return redirect()->to(site_url('kasir'))->with('error', 'Tidak ada shift aktif.');
        }

        // 2. TANGKAP INPUT DARI VIEW & bersihkan karakter non-angka (titik ribuan)
        $uangFisik = preg_replace('/[^0-9]/', '', $this->request->getPost('uang_fisik_akhir'));

        // 3. PROSES UPDATE KE DATABASE
        $dataUpdate = [
            'tanggal_tutup'    => date('Y-m-d H:i:s'),
            'uang_fisik_akhir' => $uangFisik,
            'status'           => 'closed' // Mengunci shift laci kasir menjadi closed
        ];

        try {
            $db->table('kasir_shift_transaksi')
                ->where('id', $shiftAktif['id'])
                ->update($dataUpdate);

            if (!$session->get('is_absen')) {
                $session->set('is_absen', true);
            }

            $pesanSukses = 'Shift kasir berhasil ditutup dengan aman!';

            return redirect()->to(site_url('admin/dashboard'))->with('pesan', $pesanSukses);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menutup shift: ' . $e->getMessage());
        }
    }

    public function laporan_closing()
    {
        $db = $this->db;

        $data = [
            'title'   => 'Laporan Closing',
            // Ambil data pengaturan
            'setting' => $db->table('pengaturan')->where('id', 1)->get()->getRowArray(),
            // ... data laporan lainnya ...
        ];

        return view('admin/laporan_closing_view', $data);
    }
    public function formTutupKasir()
    {
        // Cek apakah session masih ada
        if (!session()->get('id_user')) {
            return redirect()->to(site_url('login'));
        }

        return view('kasir/close_kasir');
    }
    public function absen_pulang()
    {
        // Pastikan file view 'kasir/absen_pulang_view' sudah ada ya Gan!
        return view('kasir/absen_pulang_view');
    }

    public function simpan_absen_pulang()
    {
        $db = $this->db;
        $session = session();

        $nama_db = $session->get('nama_database');
        if ($nama_db) {
            $db->setDatabase($nama_db);
        }

        $userId = $session->get('id_user');
        $today  = date('Y-m-d');

        // 1. Update jam pulang
        $updateAbsen = $db->table('absensi')
            ->where(['id_user' => $userId, 'tanggal' => $today])
            ->update(['jam_pulang' => date('H:i:s')]);

        if ($updateAbsen) {
            // --- JANGAN PAKAI $session->destroy() ---

            // 2. Ambil pesan yang mau dikirim
            $pesanSukses = 'Absen pulang berhasil. Anda telah keluar dari sistem.';
            $session->remove(['id_user', 'username', 'nama_user', 'role', 'logged_in', 'is_absen', 'nama_database']);

            // 4. Redirect ke login dengan flashdata
            return redirect()->to(base_url('login'))->with('pesan', $pesanSukses);
        } else {
            return redirect()->back()->with('error', 'Gagal memproses absen pulang.');
        }
    }

    public function cari_member()
    {
        $keyword = $this->request->getGet('keyword');

        $db = $this->db;
        $member = $db->table('member')
            ->like('no_telepon', $keyword)
            ->orLike('nama_member', $keyword)
            ->get()->getRow();

        // Hapus paksa semua output buffer yang tertunda (termasuk comment DEBUG-VIEW)
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Set header secara manual
        header('Content-Type: application/json; charset=utf-8');

        // Kirim data dan MATIKAN sistem detik ini juga
        echo json_encode($member);
        exit;
    }

    public function tambah_member()
    {
        $db = $this->db;

        // TANGKAP DATA (Gunakan getPost)
        // Pastikan string di dalam getPost('...') sama dengan name="..." di HTML
        $nama    = $this->request->getPost('nama_member');
        $telepon = $this->request->getPost('no_telepon');

        // Debugging: Jika datanya NULL, berarti getPost gagal menangkap data
        if (empty($nama) || empty($telepon)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg'    => 'Data kosong bos! Periksa input name di HTML.',
                'token'  => csrf_hash()
            ]);
        }

        $data = [
            'nama_member' => $nama,
            'no_telepon'  => $telepon, // Pastikan ini nama kolom di DB bos
            'total_poin'  => 0,
            'total_transaksi' => 0,
            'created_at'  => date('Y-m-d')
        ];

        if ($db->table('member')->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'msg'    => 'Member ' . $nama . ' berhasil didaftarkan!',
                'token'  => csrf_hash()
            ]);
        }
    }

    public function pengeluaran()
    {
        $db = $this->db;
        // Tampilkan halaman input pengeluaran (Pakai Glassmorphism bos)
        return view('kasir/pengeluaran_view');
    }

    public function simpan_pengeluaran()
    {
        $db = $this->db;
        $data = [
            'id_user'        => session()->get('id_user'),
            'tanggal'        => date('Y-m-d'),
            'nama_keperluan' => $this->request->getPost('keperluan'),
            'jumlah'         => $this->request->getPost('jumlah'),
            'total_bayar'    => $this->request->getPost('total'),
            'created_at'     => date('Y-m-d H:i:s')
        ];

        $db->table('pengeluaran_harian')->insert($data);

        // Set flashdata biar muncul SweetAlert sukses
        session()->setFlashdata('sukses_pengeluaran', 'Data tersimpan!');
        return redirect()->to(site_url('admin/pengeluaran'));
    }
    public function cek_promo()
    {
        $kode = $this->request->getPost('kode_promo');
        $totalBelanja = $this->request->getPost('total_belanja');
        $today = date('Y-m-d');

        $db = $this->db;
        $promo = $db->table('promo')
            ->where('kode_promo', $kode)
            ->where('is_active', 1)
            ->where('start_date <=', $today)
            ->where('end_date >=', $today)
            ->get()->getRowArray();

        if (!$promo) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Kode promo tidak valid atau kadaluwarsa!']);
        }

        if ($totalBelanja < $promo['min_belanja']) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Minimal belanja untuk promo ini adalah Rp ' . number_format($promo['min_belanja'], 0, ',', '.')
            ]);
        }

        // Hitung Nominal Potongan
        $potongan = 0;
        if ($promo['tipe_promo'] === 'nominal') {
            $potongan = $promo['nilai_promo'];
        } else {
            $potongan = ($totalBelanja * $promo['nilai_promo'] / 100);
        }

        return $this->response->setJSON([
            'status'    => 'success',
            'id_promo'  => $promo['id_promo'],
            'nama_promo' => $promo['nama_promo'],
            'potongan'  => (int)$potongan // PASTIKAN MENGIRIM ANGKA INI
        ]);
    }
    public function cari_member_kasir()
    {
        $keyword = $this->request->getPost('keyword');
        $db = $this->db;

        $member = $db->table('member')
            ->like('nama_member', $keyword)
            ->orLike('no_telepon', $keyword)
            ->get()
            ->getRowArray();

        if ($member) {
            // ==========================================
            // LOGIKA LEVEL VIP BERDASARKAN TOTAL BELANJA
            // ==========================================
            $total = (int)$member['total_transaksi'];
            $level = 'Silver ⚪'; // Default level

            if ($total >= 2000000) {
                $level = 'Platinum 👑';
            } elseif ($total >= 500000) {
                $level = 'Gold 🥇';
            }

            // Masukkan level ke dalam data yang dikirim ke JS
            $member['level_vip'] = $level;
            // ==========================================

            return $this->response->setJSON(['status' => 'success', 'data' => $member]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Member tidak ditemukan']);
        }
    }

    

    //Tarik Orderan Meja ke Cart
    public function tarik_ke_cart($id_temp = null)
    {
        if (!$id_temp) return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak valid']);

        $db = $this->db;
        $userId = session()->get('id_user');

        // 1. Ambil data pesanan meja
        $pesanan = $db->table('pesanan_temp')->where('id_temp', $id_temp)->get()->getRowArray();
        if (!$pesanan) return $this->response->setJSON(['status' => 'error', 'message' => 'Data meja tidak ketemu']);

        $itemsArray = json_decode($pesanan['item_json'], true);

        $db->transBegin();
        try {
            // A. Bersihkan keranjang kasir user ini dulu biar gak double
            $db->table('penjualan_temp')->where('id_user', $userId)->delete();

            // B. Masukkan item baru
            foreach ($itemsArray as $item) {
                $p_id = $item['produk_id'] ?? $item['id'] ?? null;
                $p_qty = $item['qty'] ?? $item['jumlah'] ?? 0;

                if (!$p_id) continue;

                $produk = $db->table('produk')->where('produk_id', $p_id)->get()->getRowArray();
                $p_harga = $produk ? $produk['harga_jual'] : ($item['harga'] ?? 0);

                $db->table('penjualan_temp')->insert([
                    'produk_id'  => $p_id,
                    'jumlah'     => $p_qty,
                    'harga_jual' => $p_harga,
                    'id_user'    => $userId,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            $db->table('pesanan_temp')
                ->where('id_temp', $id_temp)
                ->update(['status_order' => 'Ditarik']);

            $db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data meja berhasil ditarik! Halaman akan dimuat ulang.'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function tambah_item_temp()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        // Menggunakan properti database yang sudah ada di class Kasir ($this->db)
        $db = $this->db;
        $userId = session()->get('id_user');

        $data = [
            'produk_id'  => $this->request->getPost('produk_id'),
            'jumlah'     => $this->request->getPost('qty'),
            'harga_jual' => $this->request->getPost('harga'),
            'catatan'    => $this->request->getPost('catatan'), // Pastikan kolom ini ada di tabel penjualan_temp
            'id_user'    => $userId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            // Pastikan tabelnya sesuai
            $db->table('penjualan_temp')->insert($data);
            
            return $this->response->setJSON([
                'status'  => 'success', 
                'id_temp' => $db->insertID()
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }

    //Cek Notif Masuk dari Meja
    public function cek_notif_antrean()
    {
        // Sterilisasi agar respon murni JSON
        if (ob_get_level() > 0) ob_end_clean();

        $db = $this->db;

        // Pastikan session nama_database benar-benar ada
        $nama_db = session()->get('nama_database');
        if ($nama_db) {
            $db->setDatabase($nama_db);
        }

        // Hitung hanya yang Pending
        $query = $db->table('pesanan_temp')
            ->where('status_order', 'Pending') // Hanya ambil yang belum ditarik
            ->orderBy('id_temp', 'DESC')
            ->get();

        $data_antrean = $query->getResultArray();
        $jumlah = count($data_antrean);

        header('Content-Type: application/json');
        // Kirimkan jumlah DAN data-nya sekalian agar JS bisa render list & total sekaligus
        echo json_encode([
            'jumlah' => (int)$jumlah,
            'data'   => $data_antrean
        ]);
        exit;
    }

    //Hapus Pesanan di Keranjang
    public function hapus_item_temp($id)
    {
        $db = $this->db;
        $db->table('penjualan_temp')->where('id_temp', $id)->delete();
        return $this->response->setJSON(['status' => 'success']);
    }
    public function hapus_semua_temp()
    {
        $db = $this->db;
        $userId = session()->get('id_user');

        $db->table('penjualan_temp')->where('id_user', $userId)->delete();

        return $this->response->setJSON(['status' => 'success']);
    }

    //Batal Pesanan
    public function batal_pesanan_meja($id_temp = null)
    {
        if (!$id_temp) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak ditemukan!']);
        }

        $db = $this->db;

        // Update status_order menjadi 'Dibatalkan'
        // Data ini akan otomatis muncul di Tab Admin bagian 'Dibatalkan'
        $update = $db->table('pesanan_temp')
            ->where('id_temp', $id_temp)
            ->update([
                'status_order' => 'Dibatalkan',
                'update_at'   => date('Y-m-d H:i:s')
            ]);

        if ($update) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibatalkan dan masuk riwayat admin.'
            ]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal update database.']);
        }
    }

    public function data_meja()
    {
        $db = $this->db;
        $data['meja'] = $db->table('meja')->get()->getResultArray();
        $data['title'] = "Manajemen Meja";

        // Ambil kode_toko dari session
        $data['kode_toko'] = session()->get('kode_toko');

        return view('admin/meja_view', $data);
    }
    public function get_meja_status()
    {
        $db = $this->db;
        // Ambil data terbaru langsung dari tabel meja
        $data = $db->table('meja')->orderBy('nomor_meja', 'ASC')->get()->getResultArray();
        return $this->response->setJSON($data);
    }
    public function tarik_pesanan_by_nomor($nomor)
    {
        $db = $this->db;
        // Cari pesanan paling baru yang statusnya masih 'Pending' di meja tersebut
        $pesanan = $db->table('pesanan_temp')
            ->where('nomor_meja', $nomor)
            ->where('status_order', 'Pending')
            ->orderBy('id_temp', 'DESC')
            ->get()->getRowArray();

        if ($pesanan) {
            // Panggil fungsi tarik_ke_cart yang sudah bos buat tadi
            return $this->tarik_ke_cart($pesanan['id_temp']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Pesanan tidak ditemukan']);
    }
    public function get_order_by_meja($nomor_meja)
    {
        $db = $this->db;

        // 1. Ambil ID Transaksi TERBARU (Paling Besar) milik meja tersebut
        // Kita kunci ID-nya supaya tidak nyasar ke meja lain
        $transaksi = $db->table('transaksi')
            ->select('id') // Kita ambil ID-nya, karena ini yang disimpan di detail
            ->where('nomor_meja', (int)$nomor_meja)
            ->orWhere('id_meja', (int)$nomor_meja)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (!$transaksi) {
            return $this->response->setJSON([]);
        }

        $idTarget = $transaksi['id'];

        // 2. Ambil detail produk KHUSUS untuk ID transaksi tersebut
        // Kita pakai GROUP BY agar tampilan di modal rapi
        $data = $db->table('transaksi_detail td')
            ->select('p.nama_produk, SUM(td.qty) as qty, SUM(td.subtotal) as subtotal')
            ->join('produk p', 'p.produk_id = td.produk_id')
            ->where('td.transaksi_id', $idTarget) // KUNCI MATI DISINI
            ->groupBy('p.produk_id')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }

    public function kosongkan_meja_manual($id)
    {
        $db = $this->db;

        // Proses kosongkan meja...
        $db->table('meja')->where('id_meja', $id)->update(['status_meja' => 'Tersedia']);

        return $this->response->setJSON([
            'status'    => 'success',
            'csrf_hash' => csrf_hash() // Kunci baru untuk aksi selanjutnya
        ]);
    }
    public function simpan_reservasi()
    {
        $db = $this->db;

        // 1. Ambil data dari POST
        $nomor_meja      = $this->request->getPost('nomor_meja');
        $nama_pelanggan  = $this->request->getPost('nama_pelanggan');
        $telpon          = $this->request->getPost('telepon');

        $jam_raw         = $this->request->getPost('jam_booking');
        // Ubah "2023-12-30T14:00" menjadi "2023-12-30 14:00:00"
        $jam_booking     = str_replace('T', ' ', $jam_raw);
        if (strlen($jam_booking) == 16) {
            $jam_booking .= ':00';
        }
        // -------------------------------

        $jumlah_orang    = $this->request->getPost('jumlah_orang');

        // Validasi... (tetap sama)
        if (empty($nomor_meja) || empty($nama_pelanggan)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap!']);
        }

        $db->transBegin();
        try {
            // 2. Insert ke tabel reservasi
            $dataReservasi = [
                'nomor_meja'       => $nomor_meja,
                'nama_pelanggan'   => $nama_pelanggan,
                'telepon'          => $telpon,
                'jam_booking'      => $jam_booking, // Gunakan hasil str_replace tadi
                'jumlah_orang'     => (int)$jumlah_orang,
                'status_reservasi' => 'Pending'
            ];
            $db->table('reservasi')->insert($dataReservasi);

            // 3. Update status meja... (tetap sama)
            $db->table('meja')->where('nomor_meja', $nomor_meja)->update(['status_meja' => 'Reservasi']);

            $db->transCommit();
            return $this->response->setJSON(['status' => 'success', 'message' => 'Berhasil!']);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}