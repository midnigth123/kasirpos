<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\UserModel;
use App\Models\ProdukModel;
use App\Models\StokOpnameModel;
use App\Models\TransaksiModel;

class Admin extends BaseController
{
    // Deklarasi Properti Model
    protected $ModelUser;
    protected $ProdukModel;
    protected $StokModel;
    protected $TransaksiModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        // Jalankan Induk (BaseController) dulu supaya DB dipindah
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);

        // JEDOR! Masukkan koneksi yang sudah dipindah ($this->db) ke dalam model
        $this->ModelUser   = new \App\Models\UserModel($this->db);
        $this->ProdukModel = new \App\Models\ProdukModel($this->db);
        $this->StokModel   = new \App\Models\StokOpnameModel($this->db);
        $this->TransaksiModel = new \App\Models\TransaksiModel($this->db);
    }

    public function index()
    {
        // ========================================================================
        // 🛡️ 0. PROTEKSI TOTAL SESSION (PENGAMAN DARI LOGOUT / ABSEN PULANG TANPA SESI)
        // ========================================================================
        if (!session()->get('logged_in') || (!session()->get('nama_database') && !session()->get('db_client'))) {
            return redirect()->to(base_url('login'))->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        // Deklarasi gembok waktu global
        $dbConfig = config('Database');
        $hariIni  = date('Y-m-d');

        // ========================================================================
        // 🛡️ 1. PENCEGATAN KEAMANAN KASIR
        // ========================================================================
        if (strtolower(trim(session()->get('role'))) === 'kasir') {
            $data['title']     = 'Dashboard Kasir';
            $data['nama_user'] = session()->get('nama_user');
            $data['nama_toko'] = session()->get('nama_toko');
            
            $broadcastPusat = null;
            try {
                $configPusat = $dbConfig->default;
                $configPusat['database'] = 'db_kasir'; 
                $dbPusat = \Config\Database::connect($configPusat, false);
                
                $broadcastPusat = $dbPusat->table('master_pengumuman')
                    ->where('status_aktif', 'Y')
                    ->where('tgl_mulai <=', $hariIni)
                    ->where('tgl_selesai >=', $hariIni)
                    ->orderBy('id_pengumuman', 'DESC')
                    ->get()
                    ->getRowArray();
                
                $dbPusat->close();
            } catch (\Exception $e) {
                $broadcastPusat = null; 
            }
            
            $data['broadcast_pusat'] = $broadcastPusat;

            return view('admin/dashboard', $data); 
        }
        // ========================================================================


        // ========================================================================
        // 📊 2. KODINGAN UTAMA OWNER / ADMIN (HANYA JALAN JIKA LOGIN ADMIN & SESI ADA)
        // ========================================================================
        $db = $this->db;
        
        // Kunci nama database outlet aktif dari session
        $nama_db = session()->get('nama_database') ?? session()->get('db_client');
        $db->setDatabase($nama_db);

        $check = $db->query("SELECT DATABASE() as db_aktif")->getRow();
        $sessionDb = session()->get('db_client');
        
        // 1. Ambil Data Chart (Harian - Pendapatan)
        $queryChart = $db->query("SELECT DATE(created_at) as tanggal, SUM(total_bayar) as total 
                               FROM transaksi 
                               GROUP BY DATE(created_at) 
                               ORDER BY tanggal DESC LIMIT 7");
        $resultsChart = array_reverse($queryChart->getResultArray());
        $labels = [];
        $totals = [];
        foreach ($resultsChart as $row) {
            $labels[] = date('d M', strtotime($row['tanggal']));
            $totals[] = (int)$row['total'];
        }

        $data['chart_labels'] = json_encode($labels);
        $data['chart_data']   = json_encode($totals);

        // 2. Ambil data statistik dasar Harian
        $pendapatanHariIni = $db->table('transaksi')
            ->selectSum('total_bayar', 'total')
            ->where('DATE(created_at)', $hariIni)
            ->get()
            ->getRow()->total ?? 0;

        $jumlahTransaksiHariIni = $db->table('transaksi')
            ->where('DATE(created_at)', $hariIni)
            ->countAllResults();

        $data['total_pendapatan'] = $pendapatanHariIni;
        $data['jumlah_transaksi'] = $jumlahTransaksiHariIni;
        $data['rata_rata'] = ($jumlahTransaksiHariIni > 0) ? ($pendapatanHariIni / $jumlahTransaksiHariIni) : 0;
        $data['item_terjual'] = $db->table('transaksi_detail td')
            ->join('transaksi t', 't.id = td.transaksi_id')
            ->where('DATE(t.created_at)', $hariIni)
            ->selectSum('qty')
            ->get()->getRow()->qty ?? 0;

        // 3. Persentase Pendapatan Harian
        $kemarin = date('Y-m-d', strtotime('-1 days'));
        $pendapatanKemarin = $db->table('transaksi')
            ->selectSum('total_bayar', 'total')
            ->where('DATE(created_at)', $kemarin)
            ->get()
            ->getRow()->total ?? 0;

        $persentase = ($pendapatanKemarin > 0) ? (($pendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100 : ($pendapatanHariIni > 0 ? 100 : 0);
        $data['persentase'] = round($persentase);

        // 4. Akumulasi Mingguan
        $today        = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-6 days'));
        $data['total_pendapatan_mingguan'] = $db->table('transaksi')
            ->selectSum('total_bayar', 'total')
            ->where('DATE(created_at) >=', $sevenDaysAgo)
            ->get()->getRow()->total ?? 0;

        $data['jumlah_transaksi_mingguan'] = $db->table('transaksi')
            ->where('DATE(created_at) >=', $sevenDaysAgo)
            ->countAllResults();

        $data['rata_rata_mingguan'] = ($data['jumlah_transaksi_mingguan'] > 0) ?
            ($data['total_pendapatan_mingguan'] / $data['jumlah_transaksi_mingguan']) : 0;

        $data['item_terjual_mingguan'] = $db->table('transaksi_detail td')
            ->join('transaksi t', 't.id = td.transaksi_id')
            ->where('DATE(t.created_at) >=', $sevenDaysAgo)
            ->selectSum('qty')
            ->get()->getRow()->qty ?? 0;

        $sevenDaysBeforeEnd = date('Y-m-d', strtotime('-7 days'));
        $sevenDaysBeforeAgo = date('Y-m-d', strtotime('-13 days'));
        $totalMingguanLalu = $db->table('transaksi')
            ->selectSum('total_bayar', 'total')
            ->where('DATE(created_at) >=', $sevenDaysBeforeAgo)
            ->where('DATE(created_at) <=', $sevenDaysBeforeEnd)
            ->get()->getRow()->total ?? 0;

        $persentase_mingguan = ($totalMingguanLalu > 0) ? (($data['total_pendapatan_mingguan'] - $totalMingguanLalu) / $totalMingguanLalu) * 100 : ($data['total_pendapatan_mingguan'] > 0 ? 100 : 0);
        $data['persentase_mingguan'] = round($persentase_mingguan);

        // 5. Inventarisasi
        $penerimaanModel = new \App\Models\PenerimaanModel();
        $data['notif_pending'] = $penerimaanModel->select('penerimaan_barang.*, produk.nama_produk, penerimaan_detail.jumlah_masuk')
            ->join('penerimaan_detail', 'penerimaan_detail.penerimaan_id = penerimaan_barang.penerimaan_id')
            ->join('produk', 'produk.produk_id = penerimaan_detail.produk_id')
            ->where('penerimaan_barang.status', 'Pending')
            ->findAll();

        $data['total_produk']    = $db->table('produk')->countAllResults();
        $data['total_stok']      = $db->table('produk')->selectSum('stok')->get()->getRow()->stok ?? 0;
        $data['stok_menipis']    = $db->table('produk')->where('stok <=', 25)->where('stok >', 0)->countAllResults();
        $data['stok_habis']      = $db->table('produk')->where('stok', 0)->countAllResults();

        // Menghitung nilai aset barang
        $query_inventory = $db->query("SELECT SUM(harga_beli * stok) as total FROM produk");
        $data['nilai_inventory'] = $query_inventory->getRow()->total ?? 0;

        $data['notif_menipis']   = $db->table('produk')->where('stok <=', 25)->where('stok >', 0)->get()->getResultArray();

        // 6. Data Terlaris
        $data['produk_terlaris'] = $db->table('transaksi_detail td')
            ->select('p.produk_id, p.nama_produk, SUM(td.qty) as total_laku')
            ->join('produk p', 'p.produk_id = td.produk_id')
            ->groupBy('p.produk_id, p.nama_produk')
            ->orderBy('total_laku', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        $queryPie = $db->query("SELECT p.nama_produk, SUM(td.qty) as qty 
                            FROM transaksi_detail td
                            JOIN produk p ON p.produk_id = td.produk_id
                            GROUP BY p.produk_id, p.nama_produk
                            ORDER BY qty DESC");
        $resultsPie = $queryPie->getResultArray();

        $labelsPie = [];
        $dataPie   = [];
        foreach ($resultsPie as $row) {
            $labelsPie[] = $row['nama_produk'];
            $dataPie[]   = (int)$row['qty'];
        }

        $data['pie_labels'] = json_encode($labelsPie);
        $data['pie_data']   = json_encode($dataPie);
        $data['riwayat']    = $db->table('transaksi')->orderBy('created_at', 'DESC')->limit(10)->get()->getResultArray();


        // 7. Pergerakan Stok (Masuk vs Keluar)
        $sqlStokMovement = "SELECT tanggal, SUM(total_masuk) as masuk, SUM(total_keluar) as keluar 
                    FROM (
                        SELECT DATE(p.tanggal_masuk) as tanggal, pd.jumlah_masuk as total_masuk, 0 as total_keluar
                        FROM penerimaan_detail pd
                        JOIN penerimaan_barang p ON p.penerimaan_id = pd.penerimaan_id
                        
                        UNION ALL
                        
                        SELECT DATE(t.created_at) as tanggal, 0 as total_masuk, td.qty as total_keluar
                        FROM transaksi_detail td
                        JOIN transaksi t ON t.id = td.transaksi_id
                        WHERE t.status = 'Lunas'
                    ) as gabungan 
                    GROUP BY tanggal ORDER BY tanggal ASC LIMIT 7";

        $resultsStok = $db->query($sqlStokMovement)->getResultArray();
        $stokLabels = [];
        $stokMasuk  = [];
        $stokKeluar = [];

        foreach ($resultsStok as $row) {
            $stokLabels[] = date('d M', strtotime($row['tanggal']));
            $stokMasuk[]  = (int)$row['masuk'];
            $stokKeluar[] = (int)$row['keluar'];
        }

        $data['chart_stok_labels'] = json_encode($stokLabels);
        $data['chart_stok_masuk']  = json_encode($stokMasuk);
        $data['chart_stok_keluar'] = json_encode($stokKeluar);


        // 8. Pengaturan (Identitas Toko/Logo)
        $modelSetting = new \App\Models\PengaturanModel();
        $data['setting'] = $modelSetting->first();

        // 9. Data Top Produk Keluar
        $data['top_produk_keluar'] = $db->table('transaksi_detail td')
            ->select('p.nama_produk, SUM(td.qty) as total_keluar')
            ->join('produk p', 'p.produk_id = td.produk_id')
            ->join('transaksi t', 't.id = td.transaksi_id')
            ->where('t.status', 'Lunas')
            ->groupBy('td.produk_id, p.nama_produk')
            ->orderBy('total_keluar', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // 10. Data Produk Hampir Expired
        $data['produk_expired'] = $db->table('penerimaan_detail pd')
            ->select('p.nama_produk, pd.tgl_expired, SUM(pd.jumlah_masuk) as total_qty') 
            ->join('produk p', 'p.produk_id = pd.produk_id')
            ->where('pd.tgl_expired <=', date('Y-m-d', strtotime('+30 days')))
            ->where('pd.tgl_expired >=', date('Y-m-d'))
            ->groupBy('pd.produk_id, pd.tgl_expired') 
            ->orderBy('pd.tgl_expired', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // 11. Menghitung item yang akan expire
        $data['total_item_expired'] = $db->table('penerimaan_detail')
            ->where('tgl_expired <=', date('Y-m-d', strtotime('+30 days')))
            ->where('tgl_expired >=', date('Y-m-d'))
            ->countAllResults();

        $metode_bayar_raw = $db->table('transaksi')
            ->select('metode_pembayaran, COUNT(id) as total_penggunaan')
            ->where('status !=', 'Batal')
            ->groupBy('metode_pembayaran')
            ->orderBy('total_penggunaan', 'DESC')
            ->get()
            ->getResultArray();

        $total_tx = array_sum(array_column($metode_bayar_raw, 'total_penggunaan'));

        $data['metode_populer'] = [];
        foreach ($metode_bayar_raw as $row) {
            $persen = ($total_tx > 0) ? ($row['total_penggunaan'] / $total_tx) * 100 : 0;
            $data['metode_populer'][] = [
                'metode'  => $row['metode_pembayaran'], 
                'persen'  => round($persen, 1),
                'jumlah'  => $row['total_penggunaan']
            ];
        }

        // ========================================================================
        // 🚀 3. INJECT BROADCAST PUSAT UNTUK OWNER / ADMIN VIEW
        // ========================================================================
        $broadcastPusat = null;
        try {
            $configPusat = $dbConfig->default;
            $configPusat['database'] = 'db_kasir'; 
            $dbPusat = \Config\Database::connect($configPusat, false);

            $broadcastPusat = $dbPusat->table('master_pengumuman')
                ->where('status_aktif', 'Y')
                ->where('tgl_mulai <=', $hariIni) 
                ->where('tgl_selesai >=', $hariIni)
                ->orderBy('id_pengumuman', 'DESC')
                ->get()
                ->getRowArray();

            $dbPusat->close(); 
        } catch (\Exception $e) {
            $broadcastPusat = null; 
        }

        $data['broadcast_pusat'] = $broadcastPusat;
        // ========================================================================

        return view('admin/admin_view', $data);
    }
    public function user()
    {
        $db = $this->db;

        // 1. Data User dengan Pagination
        $userModel = new \App\Models\UserModel();
        $data['user'] = $userModel->paginate(10, 'user_group');
        $data['pager'] = $userModel->pager;

        $data['shift'] = $db->table('shift_kasir')->get()->getResultArray();

        $perPageShift = 10;
        $totalShift   = $db->table('kasir_shift_transaksi')->countAllResults();

        $currentShiftPage = (int)$this->request->getVar('page_shift') ?: 1;
        $offsetShift      = ($currentShiftPage - 1) * $perPageShift;

        $data['shift_transaksi'] = $db->table('kasir_shift_transaksi')
            ->select('kasir_shift_transaksi.*, user.username')
            ->join('user', 'user.id_user = kasir_shift_transaksi.id_user', 'left')
            ->orderBy('kasir_shift_transaksi.id', 'DESC')
            ->get($perPageShift, $offsetShift)
            ->getResultArray();

        $pager = service('pager');
        $data['pager_shift'] = $pager;
        $data['pager_shift']->store('shift', $currentShiftPage, $perPageShift, $totalShift);



        // 4. Data log aktivitas
        $tgl_mulai   = $this->request->getGet('tgl_mulai');
        $tgl_selesai = $this->request->getGet('tgl_selesai');

        $builder = $db->table('log_aktivitas');

        if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
            $builder->where('DATE(waktu) >=', $tgl_mulai)
                ->where('DATE(waktu) <=', $tgl_selesai);
        }

        $perPage = 10;
        $totalRows = $builder->countAllResults(false);
        $currentLogPage = (int)$this->request->getVar('page_log') ?: 1;

        $data['log_aktivitas'] = $builder->orderBy('waktu', 'DESC')
            ->get($perPage, ($currentLogPage - 1) * $perPage)
            ->getResultArray();

        $pagerService = \Config\Services::pager();
        $data['pager_log'] = $pagerService;
        $data['pager_log']->store('log', $currentLogPage, $perPage, $totalRows);

        $data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;
        $data['currentLogPage'] = $currentLogPage;

        $data['active_tab'] = $this->request->getGet('active_tab') ?? $this->request->getPost('active_tab');

        return view('admin/user_view', $data);
    }
    public function simpan_produk()
    {
        $session = session();
        $data = [
            'barcode'     => $this->request->getPost('barcode'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga_beli'  => str_replace('.', '', $this->request->getPost('harga_beli')),
            'harga_jual'  => str_replace('.', '', $this->request->getPost('harga_jual')),
            'stok'        => $this->request->getPost('stok'),
            'kategori'    => $this->request->getPost('kategori'),
            'jenis_stok'  => $this->request->getPost('jenis_stok'),
        ];

        $fileFoto = $this->request->getFile('img');
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            // Gunakan FCPATH agar aman di Hosting
            $fileFoto->move(FCPATH . 'uploads/produk', $namaFoto);
            $data['img'] = $namaFoto;
        }

        // JEDORRR! Masukkan $this->db di sini agar mengarah ke database toko yang aktif
        $produkModel = new \App\Models\ProdukModel($this->db);
        $produkModel->save($data);

        return redirect()->to('admin/produk')->with('pesan_sukses', 'Produk Berhasil JEDOR!');
    }

    public function hapus_produk($id = null)
    {
        $session = session();
        $role = $session->get('role');

        // Batasi hanya untuk Admin atau Manajer
        if ($role !== 'admin' && $role !== 'owner' && $role !== 'manajer') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $db = $this->db;
        $db->table('produk')->where('produk_id', $id)->delete();

        // Simpan Log Aktivitas
        $this->tambah_log($session->get('username'), $role, 'Menghapus produk ID: ' . $id);

        return redirect()->to(site_url('admin/produk'))->with('pesan_sukses', 'Produk berhasil dihapus!');
    }

    public function update_produk($id)
    {
        $session = session();
        $role = $session->get('role');

        if ($role !== 'admin' && $role !== 'owner' && $role !== 'manajer') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $db = $this->db;

        // 1. AMBIL DATA LAMA SEBELUM DIUPDATE (PENTING!)
        $produkLama = $db->table('produk')->where('produk_id', $id)->get()->getRow();

        $file = $this->request->getFile('img');
        $harga_beli = str_replace('.', '', $this->request->getPost('harga_beli'));
        $harga_jual = str_replace('.', '', $this->request->getPost('harga_jual'));
        $stok_baru = $this->request->getPost('stok'); // Ambil input stok baru

        $payload = [
            'barcode'     => $this->request->getPost('barcode'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'harga_beli'  => $harga_beli,
            'harga_jual'  => $harga_jual,
            'stok'        => $stok_baru,
            'kategori'    => $this->request->getPost('kategori'),
            'jenis_stok'  => $this->request->getPost('jenis_stok'),
        ];

        // --- LOGIKA KARTU STOK JEDOR! ---
        // Cek apakah ada perubahan angka stok
        if ($produkLama->stok != $stok_baru) {
            $selisih = $stok_baru - $produkLama->stok;

            $this->db->table('kartu_stok')->insert([
                'produk_id'      => $id,
                'tipe'           => 'opname', // Karena diedit manual oleh admin
                'kode_referensi' => 'EDIT-' . time(),
                'stok_awal'      => $produkLama->stok, // Angka 100 tadi
                'stok_masuk'     => ($selisih > 0) ? $selisih : 0,
                'stok_keluar'    => ($selisih < 0) ? abs($selisih) : 0,
                'stok_akhir'     => $stok_baru, // Angka 99 tadi
                'keterangan'     => 'Update stok manual oleh admin',
                'tanggal'        => date('Y-m-d H:i:s')
            ]);
        }

        // --- LOGIKA UPLOAD FOTO ---
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $namaFotoBaru = $file->getRandomName();
            if ($file->move(FCPATH . 'uploads/produk/', $namaFotoBaru)) {
                if (!empty($produkLama->img) && file_exists(FCPATH . 'uploads/produk/' . $produkLama->img)) {
                    unlink(FCPATH . 'uploads/produk/' . $produkLama->img);
                }
                $payload['img'] = $namaFotoBaru;
            }
        }

        // Update data ke database Toko
        $db->table('produk')->where('produk_id', $id)->update($payload);

        $this->tambah_log($session->get('username'), $role, 'Memperbarui produk: ' . $payload['nama_produk']);

        return redirect()->to(site_url('admin/produk'))->with('pesan_sukses', 'Data produk berhasil diperbarui!');
    }

    public function transaksi_detail($transaksi_id)
    {
        $db = $this->db;
        $items = $db->table('transaksi_detail')
            ->select('transaksi_detail.*, produk.nama_produk')
            ->join('produk', 'produk.produk_id = transaksi_detail.produk_id')
            ->where('transaksi_detail.transaksi_id', $transaksi_id)
            ->get()
            ->getResultArray();

        if (empty($items)) return '<div class="text-center p-3 text-muted">Item tidak ditemukan.</div>';

        $html = '<table class="table table-sm table-borderless">';
        $html .= '<thead class="text-muted small"><tr><th>PRODUK</th><th class="text-center">QTY</th><th class="text-end">SUBTOTAL</th></tr></thead>';
        $html .= '<tbody>';
        foreach ($items as $item) {
            $html .= '<tr>';
            $html .= '<td><span class="fw-bold d-block">' . $item['nama_produk'] . '</span><small class="text-muted">Rp ' . number_format($item['harga_satuan'], 0, ',', '.') . '</small></td>';
            $html .= '<td class="text-center align-middle">' . $item['qty'] . '</td>';
            $html .= '<td class="text-end align-middle fw-bold">Rp ' . number_format($item['subtotal'], 0, ',', '.') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return $html;
    }

    public function transaksi()
    {
        $transaksiModel = new \App\Models\TransaksiModel();

        $tgl_mulai   = $this->request->getGet('tgl_mulai');
        $tgl_selesai = $this->request->getGet('tgl_selesai');

        if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
            $transaksiModel->where('created_at >=', $tgl_mulai . ' 00:00:00')
                ->where('created_at <=', $tgl_selesai . ' 23:59:59');
        }

        $data['riwayat'] = $transaksiModel->select('transaksi.*, SUM(transaksi_detail.qty) as subtotal')
            ->join('transaksi_detail', 'transaksi_detail.transaksi_id = transaksi.id', 'left')
            ->groupBy('transaksi.id')
            ->orderBy('created_at', 'DESC')
            ->paginate(10, 'transaksi');

        $data['pager_transaksi'] = $transaksiModel->pager;
        $data['tgl_mulai']   = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;

        return view('admin/transaksi_view', $data);
    }

    public function produk()
    {
        // JEDOR! Jangan pakai 'new' lagi, pakai yang sudah ada di $this
        $keyword = $this->request->getGet('keyword');

        if ($keyword) {
            $this->ProdukModel->groupStart()
                ->like('nama_produk', $keyword)
                ->orLike('barcode', $keyword)
                ->groupEnd();
        }

        // Menggunakan properti class yang sudah terkoneksi ke DB Toko
        $data['produk'] = $this->ProdukModel->paginate(10, 'default');
        $data['pager']  = $this->ProdukModel->pager;

        return view('admin/produk_view', $data);
    }

    public function penerimaan()
    {
        $db = $this->db;

        $data['produk'] = $db->table('produk')->get()->getResultArray();
        $data['supplier'] = $db->table('supplier')->orderBy('nama_supplier', 'ASC')->get()->getResultArray();

        // Ambil master yang statusnya Pending
        $data['penerimaan_pending'] = $db->table('penerimaan_barang')
            ->where('status', 'Pending')
            ->orderBy('tanggal_masuk', 'DESC')
            ->get()->getResultArray();

        // JEDOR! Gunakan LEFT JOIN agar jika produk dihapus, data penerimaan tidak ikut hilang di tabel
        $data['penerimaan_detail'] = $db->table('penerimaan_detail pd')
            ->select('pd.*, p.nama_produk')
            ->join('produk p', 'p.produk_id = pd.produk_id', 'left')
            ->get()->getResultArray();

        return view('admin/penerimaan_view', $data);
    }

    public function simpanPenerimaan()
    {
        $session = session();
        $db = $this->db;

        // Proteksi Role
        if (!in_array($session->get('role'), ['admin', 'manajer'])) {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $db->transBegin();
        try {
            // DATA MASTER
            $data_penerimaan = [
                'kode_penerimaan' => 'PN-' . date('YmdHis'),
                'tanggal_masuk'   => $this->request->getPost('tanggal_masuk'),
                'supplier'        => $this->request->getPost('supplier'),
                'status'          => 'Pending',
                'created_at'      => date('Y-m-d H:i:s')
            ];

            $db->table('penerimaan_barang')->insert($data_penerimaan);
            $penerimaan_id = $db->insertID();

            // DATA DETAIL
            $produk_ids   = $this->request->getPost('produk_id');
            $jumlah_masuk = $this->request->getPost('jumlah_masuk');
            $harga_beli   = $this->request->getPost('harga_beli_baru');
            $tgl_expired  = $this->request->getPost('tgl_expired');

            if (!empty($produk_ids)) {
                foreach ($produk_ids as $index => $p_id) {
                    if (!empty($p_id)) {
                        $db->table('penerimaan_detail')->insert([
                            'penerimaan_id'   => $penerimaan_id,
                            'produk_id'       => $p_id,
                            'jumlah_masuk'    => $jumlah_masuk[$index] ?? 0,
                            'harga_beli_baru' => $harga_beli[$index] ?? 0,
                            'tgl_expired'     => (!empty($tgl_expired[$index])) ? $tgl_expired[$index] : null
                        ]);
                    }
                }
            }

            $db->transCommit();
            return redirect()->to(site_url('admin/penerimaan'))->with('pesan', 'Data Penerimaan Berhasil Disimpan!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal Simpan: ' . $e->getMessage());
        }
    }


    public function konfirmasiPenerimaan($id)
    {
        $session = session();
        $role = $session->get('role');
        $db = $this->db;

        if ($role !== 'admin' && $role !== 'owner' && $role !== 'manajer') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        // Ambil data master penerimaan untuk ambil kode_penerimaan (sebagai referensi)
        $master = $db->table('penerimaan_barang')->where('penerimaan_id', $id)->get()->getRow();
        // Ambil data detail penerimaan
        $detail_barang = $db->table('penerimaan_detail')->where('penerimaan_id', $id)->get()->getResultArray();

        $db->transBegin();
        try {
            // Update stok produk & Catat Kartu Stok
            foreach ($detail_barang as $item) {
                $produk = $db->table('produk')->where('produk_id', $item['produk_id'])->get()->getRowArray();

                if ($produk) {
                    $stok_awal = (float)$produk['stok'];
                    $jumlah_masuk = (float)$item['jumlah_masuk'];
                    $stok_akhir = $stok_awal + $jumlah_masuk;

                    // 1. JEDOR! Catat ke Kartu Stok
                    $db->table('kartu_stok')->insert([
                        'produk_id'      => $item['produk_id'],
                        'tipe'           => 'masuk',
                        'kode_referensi' => $master->kode_penerimaan,
                        'stok_awal'      => $stok_awal,
                        'stok_masuk'     => $jumlah_masuk,
                        'stok_keluar'    => 0,
                        'stok_akhir'     => $stok_akhir,
                        'keterangan'     => 'Penerimaan Barang Supplier: ' . $master->supplier,
                        'tanggal'        => date('Y-m-d H:i:s')
                    ]);

                    // 2. Update stok di tabel produk
                    $db->table('produk')->where('produk_id', $item['produk_id'])->update(['stok' => $stok_akhir]);

                    // 3. Opsional: Update harga beli produk jika ada harga beli baru
                    if ($item['harga_beli_baru'] > 0) {
                        $db->table('produk')->where('produk_id', $item['produk_id'])->update(['harga_beli' => $item['harga_beli_baru']]);
                    }
                }
            }

            // Update status penerimaan menjadi Disetujui
            $db->table('penerimaan_barang')->where('penerimaan_id', $id)->update(['status' => 'Disetujui']);

            $db->transCommit();

            // Simpan log aktivitas
            $this->tambah_log($session->get('username'), $role, 'Mengkonfirmasi penerimaan barang ID: ' . $id);

            return redirect()->to(site_url('admin/penerimaan'))->with('pesan_sukses', 'Stok berhasil dikonfirmasi dan dicatat di Kartu Stok.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal Konfirmasi: ' . $e->getMessage());
        }
    }
    public function laporanPenerimaan()
    {
        $db = $this->db;

        // Ambil filter tanggal jika ada
        $tgl_mulai = $this->request->getGet('tgl_mulai') ?? date('Y-m-01');
        $tgl_selesai = $this->request->getGet('tgl_selesai') ?? date('Y-m-d');

        // Query tarik data barang masuk yang sudah disetujui
        $data['laporan'] = $db->table('penerimaan_detail pd')
            ->select('pb.kode_penerimaan, pb.tanggal_masuk, pb.supplier, p.nama_produk, pd.jumlah_masuk, pd.harga_beli_baru, pd.tgl_expired')
            ->join('penerimaan_barang pb', 'pb.penerimaan_id = pd.penerimaan_id')
            ->join('produk p', 'p.produk_id = pd.produk_id')
            ->where('pb.status', 'Disetujui')
            ->where('pb.tanggal_masuk >=', $tgl_mulai)
            ->where('pb.tanggal_masuk <=', $tgl_selesai)
            ->orderBy('pb.tanggal_masuk', 'DESC')
            ->get()->getResultArray();

        $data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;

        return view('admin/laporan_penerimaan_view', $data);
    }
    public function exportExcelPenerimaan()
    {
        $db = $this->db;

        // Ambil filter tanggal yang sama dengan tampilan laporan
        $tgl_mulai = $this->request->getGet('tgl_mulai') ?? date('Y-m-01');
        $tgl_selesai = $this->request->getGet('tgl_selesai') ?? date('Y-m-d');

        $laporan = $db->table('penerimaan_detail pd')
            ->select('pb.kode_penerimaan, pb.tanggal_masuk, pb.supplier, p.nama_produk, pd.jumlah_masuk, pd.harga_beli_baru, pd.tgl_expired')
            ->join('penerimaan_barang pb', 'pb.penerimaan_id = pd.penerimaan_id')
            ->join('produk p', 'p.produk_id = pd.produk_id')
            ->where('pb.status', 'Disetujui')
            ->where('pb.tanggal_masuk >=', $tgl_mulai)
            ->where('pb.tanggal_masuk <=', $tgl_selesai)
            ->orderBy('pb.tanggal_masuk', 'ASC')
            ->get()->getResultArray();

        // JEDOR! HEADER EXCEL NATIVE
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Penerimaan_" . $tgl_mulai . "_sd_" . $tgl_selesai . ".xls");
        echo '
    <center><h3>Laporan Barang Masuk</h3></center>
    <table border="1">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Supplier</th>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Harga Beli</th>
                <th>Subtotal</th>
                <th>Expired</th>
            </tr>
        </thead>
        <tbody>';

        $no = 1;
        $totalSemua = 0;
        foreach ($laporan as $row) {
            $subtotal = $row['jumlah_masuk'] * $row['harga_beli_baru'];
            $totalSemua += $subtotal;
            echo '
            <tr>
                <td>' . $no++ . '</td>
                <td>' . date('d/m/Y', strtotime($row['tanggal_masuk'])) . '</td>
                <td>' . $row['kode_penerimaan'] . '</td>
                <td>' . $row['supplier'] . '</td>
                <td>' . $row['nama_produk'] . '</td>
                <td>' . $row['jumlah_masuk'] . '</td>
                <td>' . $row['harga_beli_baru'] . '</td>
                <td>' . $subtotal . '</td>
                <td>' . ($row['tgl_expired'] ?? '-') . '</td>
            </tr>';
        }
        echo '
        <tr>
            <th colspan="7">GRAND TOTAL NILAI BARANG</th>
            <th>' . $totalSemua . '</th>
            <th></th>
        </tr>
    </tbody>
    </table>';
    }

    public function tambah_log($user, $role, $log_aktivitas)
    {
        $db = $this->db;
        $session = session();
        
        $dbClient = $session->get('db_client');

        if (!empty($dbClient)) {
            try {
                $db->setDatabase($dbClient);
            } catch (\Exception $e) {
                return false;
            }
        }

        $fixUser = !empty($user) ? $user : ($session->get('username') ?: ($session->get('nama_user') ?: 'User Sistem'));
        $fixRole = !empty($role) ? $role : ($session->get('role') ?: 'owner');
        // ----------------------------------------

        $data = [
            'user'      => $fixUser,
            'role'      => $fixRole,
            'aktivitas' => $log_aktivitas,
            'waktu'     => date('Y-m-d H:i:s')
        ];

        $db->table('log_aktivitas')->insert($data);
    }

    public function simpan_hak_akses()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('admin/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk mengubah konfigurasi sistem.');
        }

        $db = $this->db;
        $postAkses = $this->request->getPost('akses'); 
        $roles = ['admin', 'kasir', 'manajer', 'owner'];

        foreach ($roles as $role) {
            if ($role !== 'admin') {
                $db->table('pengaturan_akses')->where('role', $role)->update(['status' => 0]);
            } else {
                $db->table('pengaturan_akses')->where('role', 'admin')->update(['status' => 1]);
                continue; // Skip looping modul untuk admin karena sudah di-set 1
            }

            if (isset($postAkses[$role])) {
                foreach ($postAkses[$role] as $modul => $nilai) {

                    // Cari apakah modul sudah ada di DB untuk role tersebut
                    $cek = $db->table('pengaturan_akses')
                        ->where(['role' => $role, 'modul' => $modul])
                        ->get()->getRow();

                    if ($cek) {
                        // Jika ada, update jadi aktif (1)
                        $db->table('pengaturan_akses')
                            ->where('id_akses', $cek->id_akses)
                            ->update(['status' => 1]);
                    } else {
                        // Jika belum ada (modul baru), masukkan data baru
                        $db->table('pengaturan_akses')->insert([
                            'role'   => $role,
                            'modul'  => $modul,
                            'status' => 1
                        ]);
                    }
                }
            }
        }
        return redirect()->to('admin/manajemen-user#hak-akses')->with('success', 'Hak akses berhasil diatur!');
    }
    public function simpan_user()
    {
        $session = session();
        $role = $session->get('role');

        // Batasi hanya admin yang bisa menambah user
        if ($role !== 'admin') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $db = $this->db;

        $data = [
            'username'   => $this->request->getPost('username'),
            'nama_user'  => $this->request->getPost('nama_user'), // Disesuaikan dengan tabel Anda
            'role'       => $this->request->getPost('role'),
            'alamat'     => $this->request->getPost('alamat'),
            'no_hp'      => $this->request->getPost('no_hp'),
            'pin_user'   => $this->request->getPost('pin_user'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        ];

        $db->table('user')->insert($data);
        $this->tambah_log(session()->get('username'), session()->get('role'), 'Menambah user baru: ' . $this->request->getPost('nama_user'));
        return redirect()->to(site_url('admin/manajemen-user'))->with('pesan_sukses', 'Data user berhasil disimpan.');
    }

    public function update_user($id)
    {
        $session = session();
        $role = $session->get('role');

        // Batasi hanya admin yang bisa mengubah user
        if ($role !== 'admin') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $db = $this->db;

        $data = [
            'username'  => $this->request->getPost('username'),
            'nama_user' => $this->request->getPost('nama_user'),
            'role'      => $this->request->getPost('role'),
            'alamat'    => $this->request->getPost('alamat'),
            'no_hp'     => $this->request->getPost('no_hp'),
            'pin_user'  => $this->request->getPost('pin_user'),
            // KUNCI: Paksa kembali ke status aktif saat disimpan
            'is_active' => 1,
        ];

        // Cek apakah password diisi atau tidak
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Gunakan id_user sebagai primary key untuk update
        $db->table('user')->where('id_user', $id)->update($data);

        // Tambah log aktivitas
        $this->tambah_log(session()->get('username'), session()->get('role'), 'Mengubah data & mengaktifkan kembali user: ' . $this->request->getPost('nama_user'));

        return redirect()->to(site_url('admin/manajemen-user'))->with('pesan_sukses', 'Data user berhasil diperbarui dan akun diaktifkan.');
    }
    public function hapus_user($id)
    {
        $session = session();
        $role = $session->get('role');

        // Batasi hanya admin yang bisa menonaktifkan user
        if ($role !== 'admin') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $db = $this->db;

        // SOFT DELETE: Ubah is_active menjadi 0 alih-alih menghapus baris data
        $db->table('user')
            ->where('id_user', $id)
            ->update(['is_active' => 0]);

        // Catat ke log aktivitas
        $this->tambah_log(session()->get('username'), session()->get('role'), 'Menonaktifkan (Soft Delete) user ID: ' . $id);

        return redirect()->to(site_url('admin/manajemen-user'))->with('pesan_sukses', 'User berhasil dinonaktifkan.');
    }

    public function user_client()
{
    $role = session()->get('role');

    if ($role !== 'admin' && $role !== 'owner') {
        return redirect()->to('admin/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk membuka halaman ini.');
    }

    // Ambil nama database toko dari session login yang aktif
    $dbClient = session()->get('db_client'); 
    
    if (!$dbClient) {
        return redirect()->to('/login')->with('error', 'Session database toko kadaluarsa. Silakan login ulang.');
    }

    // Paksa koneksi utama bertukar ke database tenant/outlet milik klien
    $db = $this->db;
    try {
        $db->setDatabase($dbClient);
    } catch (\Exception $e) {
        return redirect()->to('admin/dashboard')->with('error', 'Gagal menyambung ke database operasional toko.');
    }

    $pagerService = \Config\Services::pager();

    // ==========================================
    // 1. DATA USER (Saring Admin jika Owner login)
    // ==========================================
    $perPageUser = 10;
    $currentUserPage = (int)$this->request->getVar('page_user_group') ?: 1;
    $offsetUser      = ($currentUserPage - 1) * $perPageUser;

    $userBuilder = $db->table('user');
    if ($role === 'owner') {
        $userBuilder->where('role !=', 'admin');
    }

    $totalUser = $userBuilder->countAllResults(false); 
    $data['user'] = $userBuilder->get($perPageUser, $offsetUser)->getResultArray();
    $data['pager'] = $pagerService;
    $data['pager']->store('user_group', $currentUserPage, $perPageUser, $totalUser);


    // ==========================================
    // 2. DATA DAFTAR SHIFT KASIR 
    // ==========================================
    $data['shift'] = $db->table('shift_kasir')->get()->getResultArray();


    // ==========================================
    // 3. DATA RIWAYAT SHIFT KASIR
    // ==========================================
    $perPageShift = 10;
    $totalShift   = $db->table('kasir_shift_transaksi')->countAllResults();

    $currentShiftPage = (int)$this->request->getVar('page_shift') ?: 1;
    $offsetShift      = ($currentShiftPage - 1) * $perPageShift;

    // Ambil data dari database tenant + join nama lengkap / username
    $data['shift_transaksi'] = $db->table('kasir_shift_transaksi')
        ->select('kasir_shift_transaksi.*, user.username, user.nama_user')
        ->join('user', 'user.id_user = kasir_shift_transaksi.id_user', 'left')
        ->orderBy('kasir_shift_transaksi.id', 'DESC')
        ->get($perPageShift, $offsetShift)
        ->getResultArray();

    $data['pager_shift'] = $pagerService;
    $data['pager_shift']->store('shift', $currentShiftPage, $perPageShift, $totalShift);
    $data['currentShiftPage'] = $currentShiftPage;


    // ==========================================
    // 4. DATA LOG AKTIVITAS
    // ==========================================
    $tgl_mulai   = $this->request->getGet('tgl_mulai');
    $tgl_selesai = $this->request->getGet('tgl_selesai');

    $builder = $db->table('log_aktivitas');

    if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
        $builder->where('DATE(waktu) >=', $tgl_mulai)
                ->where('DATE(waktu) <=', $tgl_selesai);
    }

    $perPage = 10;
    $totalRows = $builder->countAllResults(false);
    $currentLogPage = (int)$this->request->getVar('page_log') ?: 1;

    $data['log_aktivitas'] = $builder->orderBy('waktu', 'DESC')
        ->get($perPage, ($currentLogPage - 1) * $perPage)
        ->getResultArray();

    $data['pager_log'] = $pagerService;
    $data['pager_log']->store('log', $currentLogPage, $perPage, $totalRows);

    $data['tgl_mulai'] = $tgl_mulai;
    $data['tgl_selesai'] = $tgl_selesai;
    $data['currentLogPage'] = $currentLogPage;

    // Kirimkan data tab aktif jika ada di URL
    $data['active_tab'] = $this->request->getGet('active_tab') ?? $this->request->getPost('active_tab');

    return view('admin/user_client_view', $data);
}
    public function simpan_user_client()
    {
    $session = session();
    $role = $session->get('role');

    // REVISI SAAS: Izinkan 'admin' (Developer) DAN 'owner' (Klien Pemilik Toko) untuk menambah user
    if ($role !== 'admin' && $role !== 'owner') {
        return redirect()->to('login')->with('error', 'Akses ditolak.');
    }

    // Sambungkan ke database tenant/outlet secara dinamis
    $dbClient = $session->get('db_client');
    $db = $this->db;
    try {
        $db->setDatabase($dbClient);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menyambung ke database operasional.');
    }

    $data = [
        'username'   => $this->request->getPost('username'),
        'nama_user'  => $this->request->getPost('nama_user'), 
        'role'       => $this->request->getPost('role'),
        'alamat'     => $this->request->getPost('alamat'),
        'no_hp'      => $this->request->getPost('no_hp'),
        'pin_user'   => $this->request->getPost('pin_user'),
        'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
        'is_active'  => 1 
    ];

    $db->table('user')->insert($data);
    
    // Log Aktivitas internal
    try {
        $db->table('log_aktivitas')->insert([
            'username'  => $session->get('username'),
            'role'      => $role,
            'aktivitas' => 'Menambah user baru via Client Menu: ' . $this->request->getPost('nama_user'),
            'waktu'     => date('Y-m-d H:i:s')
        ]);
    } catch (\Exception $e) {}

    // REDIRECT: Kembali ke halaman user_client
    return redirect()->to(site_url('admin/user_client'))->with('pesan_sukses', 'Data user berhasil disimpan.');
    }

    public function update_user_client($id)
    {
    $session = session();
    $role = $session->get('role');

    // REVISI SAAS: Izinkan 'admin' dan 'owner' untuk mengubah user
    if ($role !== 'admin' && $role !== 'owner') {
        return redirect()->to('login')->with('error', 'Akses ditolak.');
    }

    // Sambungkan ke database tenant/outlet secara dinamis
    $dbClient = $session->get('db_client');
    $db = $this->db;
    try {
        $db->setDatabase($dbClient);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menyambung ke database operasional.');
    }

    $data = [
        'username'  => $this->request->getPost('username'),
        'nama_user' => $this->request->getPost('nama_user'),
        'role'      => $this->request->getPost('role'),
        'alamat'    => $this->request->getPost('alamat'),
        'no_hp'     => $this->request->getPost('no_hp'),
        'pin_user'  => $this->request->getPost('pin_user'),
        'is_active' => 1, // Paksa aktif kembali saat di-update
    ];

    // Cek password baru jika diubah
    $password = $this->request->getPost('password');
    if (!empty($password)) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $db->table('user')->where('id_user', $id)->update($data);

    // Log Aktivitas internal
    try {
        $db->table('log_aktivitas')->insert([
            'username'  => $session->get('username'),
            'role'      => $role,
            'aktivitas' => 'Mengubah data & mengaktifkan kembali user via Client Menu: ' . $this->request->getPost('nama_user'),
            'waktu'     => date('Y-m-d H:i:s')
        ]);
    } catch (\Exception $e) {}

    // REDIRECT: Kembali ke halaman user_client
    return redirect()->to(site_url('admin/user_client'))->with('pesan_sukses', 'Data user berhasil diperbarui.');
    }

    public function hapus_user_client($id)
    {
    $session = session();
    $role = $session->get('role');

    // REVISI SAAS: Izinkan 'admin' dan 'owner' untuk melakukan soft delete karyawan
    if ($role !== 'admin' && $role !== 'owner') {
        return redirect()->to('login')->with('error', 'Akses ditolak.');
    }

    // Sambungkan ke database tenant/outlet secara dinamis
    $dbClient = $session->get('db_client');
    $db = $this->db;
    try {
        $db->setDatabase($dbClient);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menyambung ke database operasional.');
    }

    // SOFT DELETE: Set is_active = 0
    $db->table('user')
        ->where('id_user', $id)
        ->update(['is_active' => 0]);

    // Log Aktivitas internal
    try {
        $db->table('log_aktivitas')->insert([
            'username'  => $session->get('username'),
            'role'      => $role,
            'aktivitas' => 'Menonaktifkan (Soft Delete) user ID: ' . $id . ' via Client Menu',
            'waktu'     => date('Y-m-d H:i:s')
        ]);
    } catch (\Exception $e) {}

    // REDIRECT: Kembali ke halaman user_client
    return redirect()->to(site_url('admin/user_client'))->with('pesan_sukses', 'User berhasil dinonaktifkan.');
}

    //Shift Kasir
    public function shift_index()
    {
        $session = session();
        // Akses diperbolehkan untuk Admin dan Manajer
        if ($session->get('role') !== 'admin' && $session->get('role') !== 'owner' && $session->get('role') !== 'manajer') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $db = $this->db;
        $data['shift'] = $db->table('shift_kasir')->get()->getResultArray();

        return view('admin/shift_view', $data);
    }

    public function shift_simpan()
    {
        $session = session();
        $role = $session->get('role');

        if ($role !== 'admin' && $role !== 'owner') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $dbClient = $session->get('db_client');
        $db = $this->db;
        try {
            $db->setDatabase($dbClient);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyambung ke database operasional.');
        }

        $nama_shift = $this->request->getPost('nama_shift');
        $data = [
            'nama_shift'   => $nama_shift,
            'jam_mulai'    => $this->request->getPost('jam_mulai'),
            'jam_selesai'  => $this->request->getPost('jam_selesai'),
            'status_aktif' => 1
        ];

        $db->table('shift_kasir')->insert($data);

        // Jejak Log Aktivitas
        try {
            $db->table('log_aktivitas')->insert([
                'username'  => $session->get('username'),
                'role'      => $role,
                'aktivitas' => 'Menambah shift kasir baru: ' . $nama_shift,
                'waktu'     => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}

        // KUNCI REDIRECT: Lempar kembali ke menu user_client langsung ke tab shift
        return redirect()->to(site_url('admin/user_client?active_tab=shift'))->with('pesan_sukses', 'Data shift berhasil ditambahkan.');
    }

    public function update_shift($shift_id)
    {
        $session = session();
        $role = $session->get('role');

        if ($role !== 'admin' && $role !== 'owner') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $dbClient = $session->get('db_client');
        $db = $this->db;
        try {
            $db->setDatabase($dbClient);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyambung ke database operasional.');
        }

        $nama_shift = $this->request->getPost('nama_shift');
        $data = [
            'nama_shift'   => $nama_shift,
            'jam_mulai'    => $this->request->getPost('jam_mulai'),
            'jam_selesai'  => $this->request->getPost('jam_selesai'),
            'status_aktif' => $this->request->getPost('status_aktif'),
        ];

        $db->table('shift_kasir')->where('shift_id', $shift_id)->update($data);

        // Jejak Log Aktivitas
        try {
            $db->table('log_aktivitas')->insert([
                'username'  => $session->get('username'),
                'role'      => $role,
                'aktivitas' => 'Mengubah pengaturan shift kasir: ' . $nama_shift,
                'waktu'     => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}

        // KUNCI REDIRECT: Lempar kembali ke menu user_client langsung ke tab shift
        return redirect()->to(site_url('admin/user_client?active_tab=shift'))->with('pesan_sukses', 'Data shift berhasil diubah.');
    }

    public function hapus_shift($shift_id)
    {
        $session = session();
        $role = $session->get('role');

        if ($role !== 'admin' && $role !== 'owner') {
            return redirect()->to('login')->with('error', 'Akses ditolak.');
        }

        $dbClient = $session->get('db_client');
        $db = $this->db;
        try {
            $db->setDatabase($dbClient);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyambung ke database operasional.');
        }

        $shift = $db->table('shift_kasir')->where('shift_id', $shift_id)->get()->getRow();
        $nama_shift = $shift ? $shift->nama_shift : 'ID ' . $shift_id;

        $db->table('shift_kasir')->where('shift_id', $shift_id)->delete();

        // Jejak Log Aktivitas
        try {
            $db->table('log_aktivitas')->insert([
                'username'  => $session->get('username'),
                'role'      => $role,
                'aktivitas' => 'Menghapus shift kasir: ' . $nama_shift,
                'waktu'     => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {}

        return redirect()->to(site_url('admin/user_client?active_tab=shift'))->with('pesan_sukses', 'Data shift berhasil dihapus.');
    }
    public function laporan_detail()
    {
        $tgl_mulai   = $this->request->getGet('tgl_mulai');
        $tgl_selesai = $this->request->getGet('tgl_selesai');

        $db = $this->db;
        $builder = $db->table('transaksi_detail td');

        // Melakukan join ke tabel produk untuk mengambil nama produk
        $builder->select('
            td.produk_id, 
            produk.nama_produk, 
            SUM(td.qty) as total_qty, 
            SUM(td.subtotal) as total_subtotal
        ');
        $builder->join('produk', 'produk.produk_id = td.produk_id');

        // Jika parameter tanggal terisi
        if ($tgl_mulai && $tgl_selesai) {
            $builder->join('transaksi', 'transaksi.id = td.transaksi_id');
            $builder->where('transaksi.created_at >=', $tgl_mulai . ' 00:00:00');
            $builder->where('transaksi.created_at <=', $tgl_selesai . ' 23:59:59');
        }

        $builder->groupBy('td.produk_id, produk.nama_produk');
        $query = $builder->get()->getResultArray();

        $data['detail_transaksi'] = $query;
        $data['tgl_mulai']        = $tgl_mulai;
        $data['tgl_selesai']      = $tgl_selesai;

        return view('admin/transaksi_detail_view', $data);
    }
    public function cetak_transaksi()
    {
        $tgl_mulai   = $this->request->getGet('tgl_mulai');
        $tgl_selesai = $this->request->getGet('tgl_selesai');

        $db = $this->db;
        $builder = $db->table('transaksi_detail td');

        // Sesuaikan dengan struktur tabel Anda
        $builder->select('
            td.produk_id, 
            produk.nama_produk, 
            SUM(td.qty) as total_qty, 
            SUM(td.subtotal) as total_subtotal
        ');
        $builder->join('produk', 'produk.produk_id = td.produk_id');

        // Jika filter tanggal diisi
        if ($tgl_mulai && $tgl_selesai) {
            $builder->join('transaksi', 'transaksi.id = td.transaksi_id');
            $builder->where('transaksi.created_at >=', $tgl_mulai . ' 00:00:00');
            $builder->where('transaksi.created_at <=', $tgl_selesai . ' 23:59:59');
        }

        $builder->groupBy('td.produk_id, produk.nama_produk');
        $query = $builder->get()->getResultArray();

        // Hitung harga satuan per item
        $data['detail_transaksi'] = array_map(function ($row) {
            $row['harga_satuan'] = $row['total_qty'] > 0 ? ($row['total_subtotal'] / $row['total_qty']) : 0;
            return $row;
        }, $query);

        $data['tgl_mulai']   = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;

        return view('admin/transaksi_detail_print', $data);
    }

    public function transaksi_batal($id)
{
    $db = $this->db;
    $alasan = $this->request->getGet('alasan') ?? 'Pembatalan Transaksi oleh Admin';
    $waktuSekarang = date('Y-m-d H:i:s');
    $kodeRetur     = 'RET-BATAL-' . date('Ymd-His');

    // 1. Ambil data transaksi induk
    $transaksi = $db->table('transaksi')->where('id', $id)->get()->getRow();
    if (!$transaksi) {
        echo "<h1>Error: Data Transaksi ID $id tidak ditemukan!</h1>"; die();
    }

    // JEDOR! SEMUA CODES TRANSAKSI DIBAWAH INI JALAN POLOSAN TANPA PEMBUNGKUS
    // JIKA ADA ERROR, CODINGAN AKAN LANGSUNG BERHENTI DAN MENGELUARKAN LAYAR MERAH CI4!

    // 2. Update Transaksi
    $db->table('transaksi')->where('id', $id)->update(['status' => 'Batal', 'alasan_batal' => $alasan]);

    // 3. Insert Header Retur
    $db->table('retur')->insert([
        'kode_retur'   => $kodeRetur,
        'invoice_asal' => $transaksi->invoice,
        'total_refund' => (int)$transaksi->total_bayar,
        'id_user'      => 1,
        'created_at'   => $waktuSekarang
    ]);

    $idReturBaru = $db->insertID();

    // 4. Ambil Detail
    $allDetail = $db->table('transaksi_detail')->where('transaksi_id', $transaksi->id)->get()->getResultArray();

    // KITA CEK APAKAH DETAILNYA ADA ATAU KOSONG
    if (empty($allDetail)) {
        echo "<h1>💥 KETEMU BIANG KEROKNYA, BOS!</h1>";
        echo "<p>Data di tabel <b>transaksi_detail</b> untuk transaksi_id = <b>" . $transaksi->id . "</b> ternyata KOSONG/TIDAK ADA di database PC baru Bos!</p>";
        echo "<p>Pantas saja sistem bingung mau meretur barang apa.</p>";
        die();
    }

    foreach ($allDetail as $item) {
        $produkId = $item['produk_id'];
        $qtyBatal = (float)$item['qty'];
        $hargaSatuan = (int)$item['harga_satuan'];

        // Coba insert detail retur
        $db->table('retur_detail')->insert([
            'id_retur'        => $idReturBaru,
            'produk_id'       => $produkId,
            'qty_retur'       => $qtyBatal,
            'harga_satuan'    => $hargaSatuan,
            'subtotal_refund' => $hargaSatuan * $qtyBatal,
            'alasan'          => 'Pembatalan Nota: ' . $alasan,
            'kembali_ke_stok' => 'Ya'
        ]);

        $produk = $db->table('produk')->where('produk_id', $produkId)->get()->getRow();
        if ($produk) {
            $stokAwal = (float)$produk->stok;
            $stokAkhir = $stokAwal + $qtyBatal;

            $db->table('produk')->where('produk_id', $produkId)->update(['stok' => $stokAkhir]);

            // Coba insert kartu stok
            $db->table('kartu_stok')->insert([
                'produk_id'      => $produkId,
                'tanggal'        => $waktuSekarang,
                'tipe'           => 'masuk',
                'kode_referensi' => $kodeRetur,
                'stok_awal'      => $stokAwal,
                'stok_masuk'     => $qtyBatal,
                'stok_keluar'    => 0,
                'stok_akhir'     => $stokAkhir,
                'keterangan'     => 'Batal Transaksi (Invoice: ' . $transaksi->invoice . ')',
            ]);
        }
    }

    // 5. Insert Cash Flow
    $totalRefund = (int)$transaksi->total_bayar;
    if ($totalRefund > 0) {
        $lastCashflow = $db->table('cash_flow')->orderBy('id_cashflow', 'DESC')->limit(1)->get()->getRow();
        $saldoTerakhir = $lastCashflow ? $lastCashflow->saldo_akhir : 0;
        
        $db->table('cash_flow')->insert([
            'tanggal'     => $waktuSekarang,
            'kategori'    => 'Operasional',
            'keterangan'  => 'Refund Pembatalan Transaksi - Invoice: ' . $transaksi->invoice,
            'masuk'       => 0,
            'keluar'      => $totalRefund,
            'saldo_akhir' => $saldoTerakhir - $totalRefund,
            'created_at'  => $waktuSekarang
        ]);
    }

    session()->setFlashdata('success', 'Transaksi berhasil dibatalkan.');
    return redirect()->to(base_url('admin/transaksi'));
}

    public function closing_kasir()
    {
        $db = $this->db;
        date_default_timezone_set('Asia/Jakarta');

        // 1. Ambil tanggal dari input GET, jika kosong gunakan tanggal hari ini
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $data['rekap'] = $db->table('transaksi')
            ->select('
                SUM(CASE WHEN status = "Lunas" THEN total_bayar ELSE 0 END) as total_pendapatan,
                SUM(CASE WHEN status = "Lunas" AND metode_pembayaran = "Tunai" THEN total_bayar ELSE 0 END) as total_tunai,
                SUM(CASE WHEN status = "Lunas" AND metode_pembayaran = "Transfer" THEN total_bayar ELSE 0 END) as total_transfer,
                SUM(CASE WHEN status = "Lunas" AND metode_pembayaran = "EDC" THEN total_bayar ELSE 0 END) as total_edc,
                SUM(CASE WHEN status = "Lunas" AND metode_pembayaran = "QRIS" THEN total_bayar ELSE 0 END) as total_qris,
                SUM(CASE WHEN status = "Lunas" AND metode_pembayaran LIKE "Online%" THEN total_bayar ELSE 0 END) as total_online,
                COUNT(CASE WHEN status = "Batal" THEN 1 END) as jumlah_batal
            ')
            // 2. Gunakan variabel $tanggal yang dinamis
            ->where('DATE(created_at)', $tanggal)
            ->get()->getRowArray();

        return view('admin/closing_view', $data);
    }
    public function detail($id)
    {
        $db = $this->db;
        $data['transaksi'] = $db->table('transaksi')->where('id', $id)->get()->getRowArray();

        // JEDOR! Pastikan kolom join sesuai dengan produk_id
        $data['items'] = $db->table('transaksi_detail')
            ->join('produk', 'produk.produk_id = transaksi_detail.produk_id') // Sesuaikan nama kolom
            ->where('transaksi_id', $id) // Sesuaikan nama kolom
            ->get()->getResultArray();

        return view('admin/transaksi_detail_modal', $data);
    }
    public function absensi()
    {
        $db = $this->db;

        $tgl_mulai = $this->request->getGet('tgl_mulai') ?: date('Y-m-01');
        $tgl_selesai = $this->request->getGet('tgl_selesai') ?: date('Y-m-d');

        $builder = $db->table('absensi');
        $builder->select('absensi.*, user.nama_user as username');
        $builder->join('user', 'user.id_user = absensi.id_user', 'left');
        $builder->where('absensi.tanggal >=', $tgl_mulai);
        $builder->where('absensi.tanggal <=', $tgl_selesai);
        $builder->orderBy('absensi.tanggal', 'DESC');

        $data = [
            'absensi'     => $builder->get()->getResultArray(),
            'tgl_mulai'   => $tgl_mulai,
            'tgl_selesai' => $tgl_selesai
        ];

        return view('admin/monitoring_absensi', $data);
    }
    public function laporan_absensi()
    {
        $db = \Config\Database::connect();
        $tgl_awal = $this->request->getGet('tgl_awal') ?? date('Y-m-01');
        $tgl_akhir = $this->request->getGet('tgl_akhir') ?? date('Y-m-d');

        $builder = $db->table('user');
        $builder->select('user.nama_user, 
            COUNT(absensi.id_absensi) as total_hadir,
            SUM(CASE WHEN absensi.jam_masuk > "08:00:00" THEN 1 ELSE 0 END) as total_terlambat,
            (SELECT COUNT(*) FROM pengajuan_izin WHERE id_user = user.id_user AND status = "Disetujui" 
            AND tgl_mulai BETWEEN "'.$tgl_awal.'" AND "'.$tgl_akhir.'") as total_izin_cuti');
        $builder->join('absensi', 'absensi.id_user = user.id_user AND absensi.tanggal BETWEEN "'.$tgl_awal.'" AND "'.$tgl_akhir.'"', 'left');
        $builder->groupBy('user.id_user');
        
        $data['rekap'] = $builder->get()->getResultArray();
        $data['tgl_awal'] = $tgl_awal;
        $data['tgl_akhir'] = $tgl_akhir;
        
        return view('admin/laporan_absensi_view', $data);
    }
    public function export_excel()
    {
        $db = $this->db;
        $data = $db->table('user')
            ->select('nama_user, COUNT(absensi.id_absensi) as total_hadir, ...') // sesuaikan query yang sama
            ->get()->getResultArray();

        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Absensi.xls");

        echo "<table border='1'><tr><th>Nama</th><th>Hadir</th><th>Terlambat</th><th>Izin/Cuti</th></tr>";
        foreach ($data as $r) {
            echo "<tr><td>{$r['nama_user']}</td><td>{$r['total_hadir']}</td><td>{$r['total_terlambat']}</td><td>{$r['total_izin_cuti']}</td></tr>";
        }
        echo "</table>";
    }
    public function stokopname()
    {
        // 1. Panggil Model-model yang dibutuhin
        $this->StokModel = new \App\Models\StokOpnameModel();
        $this->ProdukModel = new \App\Models\ProdukModel(); // Pastikan nama model produk sesuai

        $db = $this->db;

        // 2. Ambil semua data yang dibutuhin View
        $data = [
            'title'      => 'Stok Opname',
            'stok'       => $this->StokModel->findAll(), // Data riwayat opname
            'produk'     => $this->ProdukModel->findAll(), // <--- INI YANG TADI KURANG, BOS!
            'cek_opname' => $db->table('sistem_kontrol')->where('nama_fitur', 'stok_opname_hold')->get()->getRow(),
        ];

        // 3. Kirim ke View
        return view('admin/stok_opname_view', $data);
    }
    // Pastikan namanya sama persis: toggle_opname
    public function toggle_opname($status)
    {
        $db = $this->db;

        // Update status di tabel sistem_kontrol
        $db->table('sistem_kontrol')
            ->where('nama_fitur', 'stok_opname_hold')
            ->update(['status' => $status]);

        // Beri pesan sukses
        $pesan = ($status == 1) ? 'Mode Stok Opname AKTIF (Kasir di-hold)' : 'Mode Stok Opname NONAKTIF (Kasir dibuka)';

        return redirect()->back()->with('message', $pesan);
    }
    public function cek_status_hold()
    {
        $db = $this->db;
        $fitur = $db->table('sistem_kontrol')
            ->where('nama_fitur', 'stok_opname_hold')
            ->get()
            ->getRow();

        // JEDOR! Kirim dalam format JSON agar bisa dibaca JavaScript
        return $this->response->setJSON([
            'is_hold' => ($fitur && $fitur->status == 1) ? true : false
        ]);
    }

    //     public function proses_opname()
    // {
    //     // Cek apakah ini request AJAX
    //     if ($this->request->isAJAX()) {
    //         $model = new \App\Models\StokOpnameModel(); // Sesuaikan nama model ente
    //         $db = $this->db;

    //         $produk_id   = $this->request->getPost('produk_id');
    //         $stok_fisik  = $this->request->getPost('stok_fisik');
    //         $stok_sistem = $this->request->getPost('stok_sistem'); // Ambil dari input tersembunyi/post
    //         $keterangan  = $this->request->getPost('keterangan');
    //         $selisih     = $stok_fisik - $stok_sistem;

    //         // 1. Simpan ke riwayat opname
    //         $simpan = $model->save([
    //             'produk_id'   => $produk_id,
    //             'stok_sistem' => $stok_sistem,
    //             'stok_fisik'  => $stok_fisik,
    //             'selisih'     => $selisih,
    //             'keterangan'  => $keterangan,
    //             'username'    => session()->get('username'),
    //             'created_at'  => date('Y-m-d H:i:s')
    //         ]);

    //         if ($simpan) {
    //             // 2. Update stok di tabel produk
    //             $db->table('produk')->where('produk_id', $produk_id)->update(['stok' => $stok_fisik]);

    //             return $this->response->setJSON([
    //                 'status'  => 'success',
    //                 'message' => 'Stok berhasil diperbarui!'
    //             ]);
    //         }

    //         return $this->response->setJSON([
    //             'status'  => 'error',
    //             'message' => 'Gagal memperbarui stok.'
    //         ]);
    //     }
    // }
    public function proses_opname_borongan()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $json = $this->request->getPost('data');
        $items = json_decode($json, true);

        if (empty($items)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak terbaca atau kosong']);
        }

        $db = $this->db;
        $kode_opname = 'OPN-' . date('Ymd-His');
        $username = session()->get('username') ?: '1212'; // Sesuai data di foto Bos pakai '1212'
        $waktuSekarang = date('Y-m-d H:i:s');

        $db->transStart();

        foreach ($items as $item) {
            // Ambil data produk terbaru agar stok_sistem akurat
            $p = $db->table('produk')->where('produk_id', $item['produk_id'])->get()->getRow();

            if ($p) {
                $stok_sistem_riil = (float)$p->stok;
                $stok_fisik       = (float)$item['stok_fisik'];
                $selisih          = $stok_fisik - $stok_sistem_riil;

                // 1. Simpan ke tabel stok_opname (Sesuai field di foto Bos)
                $dataOpname = [
                    'kode_opname' => $kode_opname,
                    'produk_id'   => $item['produk_id'],
                    'stok_sistem' => $stok_sistem_riil,
                    'stok_fisik'  => $stok_fisik,
                    'selisih'     => $selisih,
                    'keterangan'  => $item['keterangan'] ?? '-',
                    'username'    => $username,
                    'created_at'  => $waktuSekarang
                ];
                $db->table('stok_opname')->insert($dataOpname);

                // 2. JEDOR! Catat ke Kartu Stok 
                // Syarat selisih != 0 dihapus agar tetap tercatat di history meskipun stok cocok
                $db->table('kartu_stok')->insert([
                    'produk_id'      => $item['produk_id'],
                    'tanggal'        => $waktuSekarang,
                    'tipe'           => 'opname',
                    'kode_referensi' => $kode_opname,
                    'stok_awal'      => $stok_sistem_riil,
                    'stok_masuk'     => ($selisih > 0) ? $selisih : 0,
                    'stok_keluar'    => ($selisih < 0) ? abs($selisih) : 0,
                    'stok_akhir'     => $stok_fisik,
                    'keterangan'     => 'Stok Opname' . ($item['keterangan'] ?? '-'),
                ]);

                // 3. Update stok master di tabel produk
                $db->table('produk')
                    ->where('produk_id', $item['produk_id'])
                    ->update(['stok' => $stok_fisik]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal simpan database']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Opname berhasil & Kartu Stok Terupdate JEDOR!'
        ]);
    }
    public function mutasi_barang()
    {
        $produkModel = new \App\Models\ProdukModel($this->db);

        // 1. Ambil list semua toko dari DB Master dulu untuk "Kamus Nama Toko"
        $dbMaster = \Config\Database::connect('default');
        $dbMaster->setDatabase('db_kasir');
        $listToko = $dbMaster->table('master_toko')->get()->getResultArray();

        // Buat array bantuan: ['db_kasir' => 'Senja Coffee 1', 'db_kasir2' => 'Senja Coffee 2']
        $namaTokoMap = [];
        foreach ($listToko as $t) {
            $namaTokoMap[$t['nama_database']] = $t['nama_toko'];
        }

        // 2. Ambil Riwayat Mutasi
        $history = $this->db->table('mutasi_barang m')
            ->join('produk p', 'm.id_produk = p.produk_id')
            ->orderBy('m.tanggal', 'DESC')
            ->get()->getResultArray();

        // 3. JEDOR! Ganti nama database jadi Nama Toko asli
        foreach ($history as $key => $h) {
            $history[$key]['nama_toko_tujuan'] = $namaTokoMap[$h['ke_toko']] ?? $h['ke_toko'];
        }

        $data = [
            'title'          => 'Mutasi Barang',
            'produk'         => $produkModel->findAll(),
            'daftar_toko'    => $listToko,
            'history_mutasi' => $history
        ];

        return view('admin/mutasi_barang_view', $data);
    }

    public function proses_mutasi()
    {
        $db_asal   = session()->get('db_client');
        $db_tujuan = $this->request->getPost('db_tujuan');
        $id_produk = $this->request->getPost('id_produk');
        $jumlah    = $this->request->getPost('jumlah');

        // 1. Ambil data produk di DB Pengirim (DB saat ini)
        $produkAsal = $this->db->table('produk')->where('produk_id', $id_produk)->get()->getRow();

        if (!$produkAsal || $produkAsal->stok < $jumlah) {
            return redirect()->back()->with('error', 'Stok tidak cukup atau barang tidak ditemukan.');
        }

        // 2. Buat koneksi "Temporary" ke DB Tujuan
        $dbTarget = \Config\Database::connect('default', false);
        $dbTarget->setDatabase($db_tujuan);

        // 3. Cari barang yang sama di DB Tujuan (pakai Barcode)
        $produkTarget = $dbTarget->table('produk')->where('barcode', $produkAsal->barcode)->get()->getRow();

        if (!$produkTarget) {
            return redirect()->back()->with('error', 'Produk dengan barcode ini belum ada di toko tujuan.');
        }

        $kode_mutasi = 'MUT-' . time();

        // --- EKSEKUSI MUTASI JEDOR ---
        $this->db->transStart();
        $dbTarget->transStart();

        // A. PROSES DI TOKO ASAL (PENGIRIM)
        $stok_awal_asal = $produkAsal->stok;
        $stok_akhir_asal = $stok_awal_asal - $jumlah;

        // Potong Stok
        $this->db->table('produk')->where('produk_id', $id_produk)->decrement('stok', $jumlah);

        // Catat Kartu Stok (Keluar)
        $this->db->table('kartu_stok')->insert([
            'produk_id'      => $id_produk,
            'tipe'           => 'mutasi_keluar',
            'kode_referensi' => $kode_mutasi,
            'stok_awal'      => $stok_awal_asal,
            'stok_masuk'     => 0,
            'stok_keluar'    => $jumlah,
            'stok_akhir'     => $stok_akhir_asal,
            'keterangan'     => 'Mutasi Keluar ke: ' . $db_tujuan,
            'tanggal'        => date('Y-m-d H:i:s')
        ]);

        // B. PROSES DI TOKO TUJUAN (PENERIMA)
        $stok_awal_tujuan = $produkTarget->stok;
        $stok_akhir_tujuan = $stok_awal_tujuan + $jumlah;

        // Tambah Stok
        $dbTarget->table('produk')->where('barcode', $produkAsal->barcode)->increment('stok', $jumlah);

        // Catat Kartu Stok (Masuk)
        $dbTarget->table('kartu_stok')->insert([
            'produk_id'      => $produkTarget->produk_id, // Gunakan ID produk di toko tujuan
            'tipe'           => 'mutasi_masuk',
            'kode_referensi' => $kode_mutasi,
            'stok_awal'      => $stok_awal_tujuan,
            'stok_masuk'     => $jumlah,
            'stok_keluar'    => 0,
            'stok_akhir'     => $stok_akhir_tujuan,
            'keterangan'     => 'Mutasi Masuk dari: ' . ($db_asal['nama_toko'] ?? 'Toko Asal'),
            'tanggal'        => date('Y-m-d H:i:s')
        ]);

        // C. LOG MUTASI (Tetap dicatat di tabel mutasi_barang)
        $log = [
            'kode_mutasi'    => $kode_mutasi,
            'id_produk'      => $id_produk,
            'jumlah'         => $jumlah,
            'dari_toko'      => $db_asal,
            'ke_toko'        => $db_tujuan,
            'admin_pengirim' => session()->get('username')
        ];
        $this->db->table('mutasi_barang')->insert($log);
        $dbTarget->table('mutasi_barang')->insert($log);

        $this->db->transComplete();
        $dbTarget->transComplete();

        if ($this->db->transStatus() === FALSE || $dbTarget->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Gagal memproses mutasi.');
        }

        return redirect()->to('admin/mutasi_barang')->with('pesan_sukses', 'Mutasi & Kartu Stok Berhasil JEDOR!');
    }

    //Laporan Opname
    public function laporan_opname()
    {
        $db = $this->db;

        // Ambil Filter Tanggal
        $tgl_awal  = $this->request->getGet('tgl_awal');
        $tgl_akhir = $this->request->getGet('tgl_akhir');

        $builder = $db->table('stok_opname');
        $builder->select('
        stok_opname.opname_id, 
        stok_opname.kode_opname, 
        stok_opname.created_at, 
        user.nama_user, 
        COUNT(stok_opname.produk_id) as total_item
        ');
        $builder->join('user', 'user.username = stok_opname.username', 'left');

        if ($tgl_awal && $tgl_akhir) {
            $builder->where('DATE(stok_opname.created_at) >=', $tgl_awal);
            $builder->where('DATE(stok_opname.created_at) <=', $tgl_akhir);
        }

        $builder->groupBy('stok_opname.kode_opname'); // Mengelompokkan berdasarkan transaksi
        $builder->orderBy('stok_opname.created_at', 'DESC');

        $data = [
            'opname' => $builder->get()->getResultArray(),
        ];

        return view('admin/laporan_opname_view', $data);
    }
    public function cetak_opname($id)
    {
        $db = $this->db;

        // 1. Ambil data Header (Informasi Transaksi)
        // Kita ambil satu baris saja untuk mendapatkan Kode, Tanggal, dan Petugas
        $header = $db->table('stok_opname')
            ->select('stok_opname.kode_opname, stok_opname.created_at, user.nama_user')
            ->join('user', 'user.username = stok_opname.username', 'left')
            ->where('stok_opname.opname_id', $id) // Mengambil baris utama berdasarkan ID yang diklik
            ->get()->getRowArray();

        if (!$header) {
            return "Data laporan tidak ditemukan.";
        }

        // 2. Ambil semua item yang memiliki KODE_OPNAME yang sama
        // Karena Anda tidak pakai tabel detail, maka kita cari berdasarkan kode_opname-nya
        $items = $db->table('stok_opname')
            ->select('stok_opname.*, produk.nama_produk')
            ->join('produk', 'produk.produk_id = stok_opname.produk_id', 'left')
            ->where('stok_opname.kode_opname', $header['kode_opname'])
            ->get()->getResultArray();

        $data = [
            'header' => $header,
            'items'  => $items
        ];

        return view('admin/cetak_opname_view', $data);
    }

    // Fungsi Ambil Detail (JSON)
    public function detail_opname($kode)
    {
        $db = $this->db;
        $data = $db->table('stok_opname')
            ->select('stok_opname.*, produk.nama_produk')
            ->join('produk', 'produk.produk_id = stok_opname.produk_id')
            ->where('kode_opname', $kode)
            ->get()->getResultArray();

        return $this->response->setJSON($data);
    }

    //Perjalanan barang
    public function kartu_stok()
    {
        // 1. JEDOR! Paksa zona waktu ke Jakarta agar fungsi date() di bawah ini akurat
        date_default_timezone_set('Asia/Jakarta');

        $db = $this->db;

        // Ambil filter dari request
        // date('Y-m-d') sekarang pasti akan mengambil tanggal hari ini di Indonesia
        $tgl_mulai   = $this->request->getGet('tgl_mulai') ?: date('Y-m-d');
        $tgl_selesai = $this->request->getGet('tgl_selesai') ?: date('Y-m-d');
        $produk_id   = $this->request->getGet('produk_id');

        // Setup Paging
        $perPage = 25;
        $currentPage = $this->request->getVar('page') ?: 1;

        $builder = $db->table('kartu_stok ks')
            ->select('ks.*, p.nama_produk, p.barcode')
            ->join('produk p', 'p.produk_id = ks.produk_id')
            ->where('ks.tanggal >=', $tgl_mulai . ' 00:00:00')
            ->where('ks.tanggal <=', $tgl_selesai . ' 23:59:59');

        if (!empty($produk_id)) {
            $builder->where('ks.produk_id', $produk_id);
        }

        // Ambil data dengan limit dan offset untuk pagination manual
        $total = $builder->countAllResults(false);
        $riwayat = $builder->orderBy('ks.tanggal', 'DESC')
            ->limit($perPage, ($currentPage - 1) * $perPage)
            ->get()
            ->getResultArray();

        $data = [
            'riwayat'         => $riwayat,
            'list_produk'     => $db->table('produk')->where('jenis_stok', 'Kering')->get()->getResultArray(),
            'tgl_mulai'       => $tgl_mulai,
            'tgl_selesai'     => $tgl_selesai,
            'produk_terpilih' => $produk_id,
            'pager'           => \Config\Services::pager(),
            'total'           => $total,
            'perPage'         => $perPage,
            'currentPage'     => $currentPage
        ];

        return view('admin/kartu_stok_view', $data);
    }
    public function resep()
    {
        $db = $this->db;

        $data = [
            'title'  => 'Manajemen Resep',
            'menu'   => $db->table('produk')->where('jenis_stok', 'Basah')->get()->getResultArray(),
            'bahan'  => $db->table('produk')->where('jenis_stok', 'Bahan')->get()->getResultArray(),

            // QUERY BARU DENGAN GROUPING
            'resep'  => $db->table('resep')
                ->select('
                            resep.id_produk_jual, 
                            p1.nama_produk as nama_menu, 
                            GROUP_CONCAT(p2.nama_produk SEPARATOR "<br>") as list_bahan, 
                            GROUP_CONCAT(resep.jumlah_kebutuhan SEPARATOR "<br>") as list_qty
                       ')
                ->join('produk p1', 'p1.produk_id = resep.id_produk_jual')
                ->join('produk p2', 'p2.produk_id = resep.id_bahan_baku')
                ->groupBy('resep.id_produk_jual') // Mengelompokkan berdasarkan menu jual
                ->get()->getResultArray()
        ];

        return view('admin/resep_view', $data);
    }

    public function simpan_resep()
    {
        $db = $this->db;

        $data = [
            'id_produk_jual'   => $this->request->getPost('id_produk_jual'),
            'id_bahan_baku'    => $this->request->getPost('id_bahan_baku'),
            'jumlah_kebutuhan' => (float)$this->request->getPost('jumlah_kebutuhan'),
        ];

        $db->table('resep')->insert($data);
        return redirect()->to(base_url('admin/resep'))->with('success', 'Resep berhasil ditambahkan');
    }
    public function hapus_resep($id_produk_jual)
    {
        $db = $this->db;
        $db->table('resep')->where('id_produk_jual', $id_produk_jual)->delete();
        return redirect()->back()->with('pesan', 'Resep berhasil dihapus');
    }
    // Tambahkan di bagian atas

    public function pengaturan()
    {
        $db = $this->db;

        $data = [
            'title'   => 'Pengaturan Toko',
            // Ambil baris pertama dari tabel pengaturan
            'setting' => $db->table('pengaturan')->where('id', 1)->get()->getRowArray()
        ];

        return view('admin/pengaturan_view', $data);
    }

    public function update_pengaturan()
{
    $db = $this->db;
    date_default_timezone_set('Asia/Jakarta');

    // 1. Ambil data teks dari form
    $data = [
        'id'           => 1,
        'nama_toko'    => $this->request->getPost('nama_toko'),
        'slogan'       => $this->request->getPost('slogan'),
        'alamat'       => $this->request->getPost('alamat'),
        'no_telp'      => $this->request->getPost('no_telp'),
        'email'        => $this->request->getPost('email'),
        'ppn'          => $this->request->getPost('ppn'),
        'footer_struk' => $this->request->getPost('footer_struk'),
        'stok_minus'   => $this->request->getPost('stok_minus'),
        'updated_at'   => date('Y-m-d H:i:s'),
    ];

    // --- PROSES UPLOAD 1: LOGO BARU ---
    $fileLogo = $this->request->getFile('logo');
    $oldLogo  = $this->request->getPost('logo_lama');
    
    if ($fileLogo && $fileLogo->isValid() && !$fileLogo->hasMoved()) {
        $namaBaruLogo = $fileLogo->getRandomName();
        if ($fileLogo->move(FCPATH . 'uploads/img/', $namaBaruLogo)) {
            $data['logo'] = $namaBaruLogo;
            if (!empty($oldLogo) && file_exists(FCPATH . 'uploads/img/' . $oldLogo)) {
                @unlink(FCPATH . 'uploads/img/' . $oldLogo);
            }
        }
    } else {
        $data['logo'] = $oldLogo; // Kalau gak upload, tetep pakai logo lama
    }

    // --- PROSES UPLOAD 2: FOTO QRIS ---
    $fileQris = $this->request->getFile('qris');
    $oldQris  = $this->request->getPost('qris_lama');
    
    if ($fileQris && $fileQris->isValid() && !$fileQris->hasMoved()) {
        $namaBaruQris = $fileQris->getRandomName();
        if ($fileQris->move(FCPATH . 'uploads/img/', $namaBaruQris)) {
            $data['foto_qris'] = $namaBaruQris;
            if (!empty($oldQris) && file_exists(FCPATH . 'uploads/img/' . $oldQris)) {
                @unlink(FCPATH . 'uploads/img/' . $oldQris);
            }
        }
    } else {
        $data['foto_qris'] = $oldQris; // Gak upload, tetep pakai QRIS lama
    }

    // --- PROSES UPLOAD 3: FOTO REKENING (LOGIKA MANDIRI) ---
    $fileRekening = $this->request->getFile('rekening');
    $oldRekening  = $this->request->getPost('rekening_lama');
    
    if ($fileRekening && $fileRekening->isValid() && !$fileRekening->hasMoved()) {
        $namaBaruRekening = $fileRekening->getRandomName();
        if ($fileRekening->move(FCPATH . 'uploads/img/', $namaBaruRekening)) {
            $data['rekening'] = $namaBaruRekening;
            // Hapus file lama di folder uploads jika ada ganti baru
            if (!empty($oldRekening) && file_exists(FCPATH . 'uploads/img/' . $oldRekening)) {
                @unlink(FCPATH . 'uploads/img/' . $oldRekening);
            }
        }
    } else {
        $data['rekening'] = $oldRekening; // Gak upload, tetep pakai rekening lama
    }

    // Eksekusi Simpan dengan teknik update spesifik berdasarkan ID = 1
    // (Jauh lebih aman dan presisi dibanding replace untuk tabel konfigurasi tunggal)
    $db->table('pengaturan')->where('id', 1)->update($data);

    return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
}
    public function waste()
    {
        $db = $this->db;

        // Ambil data bahan baku (jenis_stok = bahan)
        $data['bahan_baku'] = $db->table('produk')
            ->where('jenis_stok', 'bahan')
            ->get()->getResult();

        // Ambil riwayat waste, join dengan produk dan user
        $data['riwayat_waste'] = $db->table('waste')
            ->select('waste.*, produk.nama_produk, user.nama_user')
            ->join('produk', 'produk.produk_id = waste.produk_id')
            ->join('user', 'user.id_user = waste.id_user', 'left')
            ->orderBy('waste.created_at', 'DESC')
            ->get()->getResult();

        return view('admin/waste_view', $data);
    }

    public function simpan_waste()
    {
        $db = $this->db;

        // Ambil data dari post (pastikan name di input form juga sama)
        $produk_id = $this->request->getPost('produk_id');
        $qty       = (float)$this->request->getPost('qty_waste');
        $alasan    = $this->request->getPost('alasan');

        $waktuSekarang = date('Y-m-d H:i:s');
        $kode_waste    = 'WST-' . date('Ymd-His'); // Sebagai kode referensi unik

        $db->transStart();

        // 0. Ambil stok awal produk terlebih dahulu demi keakuratan kartu stok
        $produk = $db->table('produk')->where('produk_id', $produk_id)->get()->getRow();

        if ($produk) {
            $stok_awal  = (float)$produk->stok;
            $stok_akhir = $stok_awal - $qty;

            // 1. Insert ke tabel waste bawaan Bos
            $db->table('waste')->insert([
                'produk_id'  => $produk_id,
                'qty_waste'  => $qty,
                'alasan'     => $alasan,
                'id_user'    => session()->get('id_user') ?? 1,
                'created_at' => $waktuSekarang
            ]);

            // 2.Catat ke kartu_stok sebagai barang KELUAR
            $db->table('kartu_stok')->insert([
                'produk_id'      => $produk_id,
                'tanggal'        => $waktuSekarang,
                'tipe'           => 'waste', // Kunci untuk badge warna nanti
                'kode_referensi' => $kode_waste,
                'stok_awal'      => $stok_awal,
                'stok_masuk'     => 0,
                'stok_keluar'    => $qty,
                'stok_akhir'     => $stok_akhir,
                'keterangan'     => 'Waste: ' . $alasan,
            ]);

            // 3. Update stok terpusat di tabel produk menggunakan nilai pasti
            $db->table('produk')
                ->where('produk_id', $produk_id)
                ->update(['stok' => $stok_akhir]);

            // OTOMATIS CATAT KERUGIAN WASTE KE CASH FLOW ---
            // Menghitung total biaya kerugian (Harga Beli/Pokok x Qty Waste)
            // Note: Silakan sesuaikan kolom 'harga_beli' dengan nama kolom harga modal di tabel produk Bos jika berbeda
            $hargaModal = (int)($produk->harga_beli ?? $produk->harga_pokok ?? 0);
            $totalKerugian = $hargaModal * $qty;

            // Kita hanya mencatat ke cash flow jika nilai kerugiannya lebih dari 0
            if ($totalKerugian > 0) {
                // Ambil saldo akhir paling terakhir dari cash_flow
                $lastCashflow = $db->table('cash_flow')->orderBy('id_cashflow', 'DESC')->limit(1)->get()->getRow();
                $saldoTerakhir = $lastCashflow ? $lastCashflow->saldo_akhir : 0;
                $saldoBaru = $saldoTerakhir - $totalKerugian; // Dikurangi karena merupakan kerugian kas/stok

                $db->table('cash_flow')->insert([
                    'tanggal'     => $waktuSekarang,
                    'kategori'    => 'Operasional',
                    'keterangan'  => 'Kerugian Waste: ' . $produk->nama_produk . ' (' . $qty . ' Pcs) - Alasan: ' . $alasan,
                    'masuk'       => 0,
                    'keluar'      => $totalKerugian,
                    'saldo_akhir' => $saldoBaru,
                    'created_at'  => $waktuSekarang
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses data waste. Cek struktur tabel!');
        }

        return redirect()->back()->with('success', 'Berhasil! Stok bahan sudah berkurang, tercatat di kartu stok & cash flow JEDOR!');
    }

    public function laporan_waste()
    {
        $db = $this->db;

        $tgl_mulai = $this->request->getGet('tgl_mulai') ?? date('Y-m-d');
        $tgl_selesai = $this->request->getGet('tgl_selesai') ?? date('Y-m-d');

        $query = $db->table('waste')
            ->select('waste.*, produk.nama_produk, produk.harga_beli, user.nama_user') // Ambil nama_user
            ->join('produk', 'produk.produk_id = waste.produk_id')
            ->join('user', 'user.id_user = waste.id_user', 'left') // Join ke tabel user
            ->where('DATE(waste.created_at) >=', $tgl_mulai)
            ->where('DATE(waste.created_at) <=', $tgl_selesai)
            ->orderBy('waste.created_at', 'DESC')
            ->get()->getResult();

        $data = [
            'waste'       => $query,
            'tgl_mulai'   => $tgl_mulai,
            'tgl_selesai' => $tgl_selesai,
        ];

        return view('admin/laporan_waste_view', $data);
    }
    public function laporan_laba_rugi()
    {
        $tglAwal  = $this->request->getGet('tglAwal') ?? date('Y-m-01');
        $tglAkhir = $this->request->getGet('tglAkhir') ?? date('Y-m-d');

        $db = $this->db;

        // Query yang sudah diperbaiki agar tidak double counting
        $laporan = $db->table('transaksi t')
            ->select('
        DATE(t.order_at) as tanggal, 
        SUM(t.total_bayar) as total_omzet_bersih, 
        SUM(t.potongan_diskon) as total_diskon_diberikan,
        SUM(modal_sub.total_modal_hari_ini) as total_modal
    ')
            /* Subquery untuk hitung modal per transaksi agar tidak double join */
            ->join('(SELECT transaksi_id, SUM(p.harga_beli * td.qty) as total_modal_hari_ini 
             FROM transaksi_detail td 
             JOIN produk p ON p.produk_id = td.produk_id 
             GROUP BY transaksi_id) as modal_sub', 'modal_sub.transaksi_id = t.id')
            ->where('DATE(t.order_at) >=', $tglAwal)
            ->where('DATE(t.order_at) <=', $tglAkhir)
            ->where('t.status !=', 'Batal')
            ->groupBy('DATE(t.order_at)')
            ->get()->getResultArray();

        $data = [
            'laporan'  => $laporan,
            'tglAwal'  => $tglAwal,
            'tglAkhir' => $tglAkhir,
            'title'    => 'Laporan Laba Rugi'
        ];

        return view('admin/laporan_laba_rugi', $data);
    }
    public function hitung_gaji_kru()
    {
        $db = $this->db;

        $data['gaji_pegawai'] = $db->table('master_gaji mg')
            ->select('mg.*, u.nama_user')
            ->join('user u', 'u.id_user = mg.id_user')
            ->get()->getResultArray();

        $data['pegawai_list'] = $db->table('user')->get()->getResultArray();

        // SESUAIKAN DI SINI (tambahkan _view)
        return view('admin/master_gaji_view', $data);
    }

    // PROSES SIMPAN: Digunakan oleh Modal di View Master Gaji
    public function simpan_gaji()
    {
        $db = $this->db;
        $id_master = $this->request->getPost('id_master_gaji');

        // Membersihkan titik agar menjadi angka murni sebelum simpan ke DB
        $nominal = str_replace('.', '', $this->request->getPost('nominal_per_shift'));
        $tunjangan = str_replace('.', '', $this->request->getPost('tunjangan_jabatan'));
        $potongan = str_replace('.', '', $this->request->getPost('potongan_telat'));

        $data = [
            'id_user'           => $this->request->getPost('id_user'),
            'nominal_per_shift' => $nominal,
            'tunjangan_jabatan' => $tunjangan ?: 0,
            'potongan_telat'    => $potongan ?: 0,
        ];

        if ($id_master) {
            $db->table('master_gaji')->where('id_master_gaji', $id_master)->update($data);
            $pesan = "Data gaji berhasil diperbarui!"; // Ini akan dibaca SweetAlert
        } else {
            $db->table('master_gaji')->insert($data);
            $pesan = "Pengaturan gaji berhasil disimpan!"; // Ini akan dibaca SweetAlert
        }

        return redirect()->to(base_url('admin/master_gaji'))->with('success', $pesan);
    }

    // PROSES HAPUS
    public function hapus_gaji($id)
    {
        $db = $this->db;
        $db->table('master_gaji')->where('id_master_gaji', $id)->delete();

        return redirect()->to(base_url('admin/master_gaji'))->with('success', 'Data gaji berhasil dihapus!');
    }

    // MENU 2: LAPORAN GAJI (Hanya tampilkan view dulu)
    // public function laporan_gaji()
    // {
    //     $db = $this->db;
    //     $bulan = $this->request->getGet('bulan') ?? date('m');
    //     $tahun = $this->request->getGet('tahun') ?? date('Y');

    //     // Query dengan penyesuaian filter bulanan pada kasbon
    //     $data['rekap'] = $db->table('user u')
    //         ->select('
    //             u.id_user, 
    //             u.nama_user, 
    //             u.role AS jabatan,
    //             mg.nominal_per_shift, 
    //             mg.tunjangan_jabatan, 
    //             mg.potongan_telat,
    //             (SELECT COUNT(*) FROM absensi a 
    //             WHERE a.id_user = u.id_user 
    //             AND MONTH(a.tanggal) = "' . $bulan . '" 
    //             AND YEAR(a.tanggal) = "' . $tahun . '") as total_hadir,
    //             (SELECT COUNT(*) FROM absensi a 
    //             WHERE a.id_user = u.id_user 
    //             AND MONTH(a.tanggal) = "' . $bulan . '" 
    //             AND YEAR(a.tanggal) = "' . $tahun . '"
    //             AND TIME(a.jam_masuk) > "08:00:00") as total_telat,
                
    //             /* JEDOR! Filter kasbon ditambahkan MONTH dan YEAR agar sinkron dengan periode gaji */
    //             (SELECT SUM(k.nominal) FROM kasbon k 
    //             WHERE k.id_user = u.id_user 
    //             AND k.status = "Disetujui" 
    //             AND k.status_lunas = "0"
    //             AND MONTH(k.tanggal_pinjam) = "' . $bulan . '" 
    //             AND YEAR(k.tanggal_pinjam) = "' . $tahun . '") as total_kasbon
    //         ')
    //         ->join('master_gaji mg', 'mg.id_user = u.id_user', 'left')
    //         /* Filter agar Admin dan Manajer tidak muncul di daftar gaji */
    //         ->whereNotIn('u.role', ['admin', 'manajer'])
    //         ->get()->getResultArray();

    //     $data['bulan_pilih'] = $bulan;
    //     $data['tahun_pilih'] = $tahun;

    //     return view('admin/laporan_gaji_view', $data);
    // }
    public function laporan_gaji()
    {
        $db = $this->db;
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Query Utama Rekap Gaji + Subquery Absensi, Kasbon, dan LEMBUR
        $data['rekap'] = $db->table('user u')
            ->select('
                u.id_user, 
                u.nama_user, 
                u.role AS jabatan,
                mg.nominal_per_shift, 
                mg.tunjangan_jabatan, 
                mg.potongan_telat,
                (SELECT COUNT(*) FROM absensi a 
                WHERE a.id_user = u.id_user 
                AND MONTH(a.tanggal) = "' . $bulan . '" 
                AND YEAR(a.tanggal) = "' . $tahun . '") as total_hadir,
                
                (SELECT COUNT(*) FROM absensi a 
                WHERE a.id_user = u.id_user 
                AND MONTH(a.tanggal) = "' . $bulan . '" 
                AND YEAR(a.tanggal) = "' . $tahun . '"
                AND TIME(a.jam_masuk) > "08:00:00") as total_telat,
                
                (SELECT SUM(k.nominal) FROM kasbon k 
                WHERE k.id_user = u.id_user 
                AND k.status = "Disetujui" 
                AND k.status_lunas = "0"
                AND MONTH(k.tanggal_pinjam) = "' . $bulan . '" 
                AND YEAR(k.tanggal_pinjam) = "' . $tahun . '") as total_kasbon,

                /* 🚀 JEDOR! Ambil total uang lembur sesuai id_user dan periode bulan/tahun yang difilter */
                (SELECT SUM(tl.total_uang_lembur) FROM transaksi_lembur tl 
                WHERE tl.id_user = u.id_user 
                AND MONTH(tl.tanggal_lembur) = "' . $bulan . '" 
                AND YEAR(tl.tanggal_lembur) = "' . $tahun . '") as total_lembur
            ')
            ->join('master_gaji mg', 'mg.id_user = u.id_user', 'left')
            /* Filter agar Admin dan Manajer tidak muncul di daftar gaji */
            ->whereNotIn('u.role', ['admin', 'manajer'])
            ->get()->getResultArray();

        $data['bulan_pilih'] = $bulan;
        $data['tahun_pilih'] = $tahun;

        return view('admin/laporan_gaji_view', $data);
    }
    public function cetak_slip($id_user, $bulan, $tahun)
{
    $db = $this->db;

    // 1. Ambil data Gaji & User (Lengkap dengan Subquery Kasbon & LEMBUR per periode)
    $data['row'] = $db->table('user u')
        ->select('
            u.nama_user, 
            u.id_user,
            u.role,
            mg.nominal_per_shift, 
            mg.tunjangan_jabatan, 
            mg.potongan_telat,
            (SELECT COUNT(*) FROM absensi a 
            WHERE a.id_user = u.id_user 
            AND MONTH(a.tanggal) = ' . (int)$bulan . ' 
            AND YEAR(a.tanggal) = ' . (int)$tahun . ') as total_hadir,
            (SELECT COUNT(*) FROM absensi a 
            WHERE a.id_user = u.id_user 
            AND MONTH(a.tanggal) = ' . (int)$bulan . ' 
            AND YEAR(a.tanggal) = ' . (int)$tahun . '
            AND TIME(a.jam_masuk) > "08:00:00") as total_telat,

            /* JEDOR! Subquery Kasbon disesuaikan agar filter per bulan & tahun saja */
            (SELECT SUM(k.nominal) FROM kasbon k 
            WHERE k.id_user = u.id_user 
            AND k.status = "Disetujui" 
            AND k.status_lunas = "0"
            AND MONTH(k.tanggal_pinjam) = ' . (int)$bulan . ' 
            AND YEAR(k.tanggal_pinjam) = ' . (int)$tahun . ') as total_kasbon,

            /* 🚀 JEDOR JUGA! Subquery Lembur Baru Biar Nilainya Muncul di Cetakan Slip */
            (SELECT SUM(tl.total_uang_lembur) FROM transaksi_lembur tl 
            WHERE tl.id_user = u.id_user 
            AND MONTH(tl.tanggal_lembur) = ' . (int)$bulan . ' 
            AND YEAR(tl.tanggal_lembur) = ' . (int)$tahun . ') as total_lembur
        ')
        ->join('master_gaji mg', 'mg.id_user = u.id_user', 'left')
        ->where('u.id_user', $id_user)
        ->get()->getRowArray();

    // Jika data tidak ditemukan
    if (!$data['row']) {
        return redirect()->back()->with('message', 'Data tidak ditemukan!');
    }

    // 2. Ambil data profil dari tabel pengaturan
    $data['pengaturan'] = $db->table('pengaturan')->get()->getRowArray();

    $data['bulan'] = $bulan;
    $data['tahun'] = $tahun;

    // Tetap mengarah ke nama file asli Bos
    return view('admin/cetakan_slip_view', $data);
}
    public function pengeluaran()
    {
        $db = $this->db;

        $tgl_filter = $this->request->getGet('filter_tgl');
        $tgl_dipilih = (!empty($tgl_filter)) ? $tgl_filter : date('Y-m-d');

        $data['pengeluaran'] = $db->table('pengeluaran_harian')
            ->select('pengeluaran_harian.*, user.nama_user')
            ->join('user', 'user.id_user = pengeluaran_harian.id_user')
            ->where('pengeluaran_harian.tanggal', $tgl_dipilih)
            ->orderBy('pengeluaran_harian.created_at', 'DESC')
            ->get()->getResultArray();

        $data['title'] = 'Pengeluaran Harian';
        $data['tanggal_pilihan'] = $tgl_dipilih;

        return view('admin/pengeluaran_view', $data);
    }

    public function simpan_pengeluaran()
    {
        $db = $this->db;

        // Tangkap input total dan bersihkan dari titik format rupiah
        $total_raw = $this->request->getPost('total');
        $total_bersih = (int)str_replace('.', '', $total_raw);
        $keperluan = $this->request->getPost('keperluan');
        $waktuSekarang = date('Y-m-d H:i:s');

        $data = [
            'id_user'        => session()->get('id_user'),
            'tanggal'        => date('Y-m-d'),
            'nama_keperluan' => $keperluan,
            'jumlah'         => $this->request->getPost('qty'),
            'total_bayar'    => $total_bersih,
            'created_at'     => $waktuSekarang
        ];

        $db->transBegin();

        try {
            // Simpan ke tabel utama pengeluaran harian
            $db->table('pengeluaran_harian')->insert($data);

            // --- JEDOR! OTOMATIS POTONG CASH FLOW ---
            // Ambil saldo akhir paling terakhir
            $lastCashflow = $db->table('cash_flow')->orderBy('id_cashflow', 'DESC')->limit(1)->get()->getRow();
            $saldoTerakhir = $lastCashflow ? $lastCashflow->saldo_akhir : 0;
            $saldoBaru = $saldoTerakhir - $total_bersih; // Dikurang karena pengeluaran

            $db->table('cash_flow')->insert([
                'tanggal'     => $waktuSekarang,
                'kategori'    => 'Operasional',
                'keterangan'  => 'Pengeluaran Harian: ' . $keperluan,
                'masuk'       => 0,
                'keluar'      => $total_bersih,
                'saldo_akhir' => $saldoBaru,
                'created_at'  => $waktuSekarang
            ]);

            $db->transCommit();
            return redirect()->to(base_url('admin/pengeluaran'))->with('pesan', 'Data Berhasil Disimpan & Cash Flow Terupdate!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    public function hapus_pengeluaran($id = null)
    {
        if ($id == null) {
            return redirect()->to(site_url('admin/pengeluaran'));
        }

        $db = $this->db;
        $db->table('pengeluaran_harian')->where('id_pengeluaran', $id)->delete();

        session()->setFlashdata('pesan', 'Data pengeluaran berhasil dihapus!');
        return redirect()->to(site_url('admin/pengeluaran'));
    }
    public function update_pengeluaran()
    {
        $db = $this->db;

        // Ambil ID dari hidden input modal
        $id = $this->request->getPost('id_pengeluaran');

        // Bersihkan format rupiah (titik) agar jadi angka murni sebelum masuk DB
        $total_bersih = preg_replace('/[^0-9]/', '', $this->request->getPost('total'));

        $data = [
            'nama_keperluan' => $this->request->getPost('keperluan'),
            'jumlah'         => $this->request->getPost('jumlah'),
            'total_bayar'    => $total_bersih,
        ];

        $db->table('pengeluaran_harian')->where('id_pengeluaran', $id)->update($data);

        session()->setFlashdata('pesan', 'Data pengeluaran berhasil diperbarui!');
        return redirect()->to(site_url('admin/pengeluaran'));
    }
    public function laporan_pengeluaran()
    {
        $db = $this->db;

        // Ambil data filter (jika ada)
        $tgl_awal  = $this->request->getGet('tgl_awal') ?? date('Y-m-01');
        $tgl_akhir = $this->request->getGet('tgl_akhir') ?? date('Y-m-d');

        $data['laporan'] = $db->table('pengeluaran_harian')
            ->select('pengeluaran_harian.*, user.nama_user')
            ->join('user', 'user.id_user = pengeluaran_harian.id_user')
            ->where('tanggal >=', $tgl_awal)
            ->where('tanggal <=', $tgl_akhir)
            ->orderBy('tanggal', 'DESC')
            ->get()->getResultArray();

        $data['title'] = 'Laporan Pengeluaran';

        // SESUAIKAN DENGAN NAMA FILE BOS
        return view('admin/laporan_pengeluaran_view', $data);
    }
    public function cek_promo()
    {
        $kode = $this->request->getPost('kode_promo');
        $total_belanja = $this->request->getPost('total_belanja');

        $db = $this->db;
        $promo = $db->table('promo')
            ->where('kode_promo', $kode)
            ->where('is_active', 1)
            ->where('start_date <=', date('Y-m-d'))
            ->where('end_date >=', date('Y-m-d'))
            ->get()->getRowArray();

        if ($promo) {
            if ($total_belanja >= $promo['min_belanja']) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'potongan' => $promo['tipe_promo'] == 'persen'
                        ? ($total_belanja * ($promo['nilai_promo'] / 100))
                        : $promo['nilai_promo'],
                    'nama_promo' => $promo['nama_promo'],
                    'id_promo' => $promo['id_promo']
                ]);
            }
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Belum cukup syarat minimal belanja!']);
        }
        return $this->response->setJSON(['status' => 'error', 'msg' => 'Kode promo gaib (tidak ada)!']);
    }
    public function promo()
    {
        $db = $this->db;
        $data = [
            'title' => 'Manajemen Promo',
            'promo' => $db->table('promo')->orderBy('id_promo', 'DESC')->get()->getResultArray()
        ];
        return view('admin/promo', $data);
    }

    public function simpan_promo()
    {
        $db = $this->db;
        $data = [
            'nama_promo'  => $this->request->getPost('nama_promo'),
            'kode_promo'  => strtoupper($this->request->getPost('kode_promo')), // Otomatis Huruf Kapital
            'tipe_promo'  => $this->request->getPost('tipe_promo'),
            'nilai_promo' => $this->request->getPost('nilai_promo'),
            'min_belanja' => $this->request->getPost('min_belanja'),
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date'),
            'is_active'   => 1
        ];

        $db->table('promo')->insert($data);
        return redirect()->back()->with('success', 'Promo baru berhasil dibuat!');
    }

    public function hapus_promo($id)
    {
        $db = $this->db;
        $db->table('promo')->delete(['id_promo' => $id]);
        return redirect()->back()->with('success', 'Promo berhasil dihapus!');
    }
    public function update_promo()
    {
        $db = $this->db;
        $id = $this->request->getPost('id_promo');

        $data = [
            'nama_promo'  => $this->request->getPost('nama_promo'),
            'kode_promo'  => strtoupper($this->request->getPost('kode_promo')),
            'tipe_promo'  => $this->request->getPost('tipe_promo'),
            'nilai_promo' => $this->request->getPost('nilai_promo'),
            'min_belanja' => $this->request->getPost('min_belanja'),
            'start_date'  => $this->request->getPost('start_date'),
            'end_date'    => $this->request->getPost('end_date'),
        ];

        $db->table('promo')->where('id_promo', $id)->update($data);
        return redirect()->back()->with('success', 'Promo berhasil diperbarui!');
    }
    public function member()
    {
        $db = $this->db;
        // Mengambil semua data dari tabel member
        $data['members'] = $db->table('member')->get()->getResultArray();
        $data['title'] = 'Daftar Member';

        // Menampilkan ke view (asumsi folder view admin/member_list)
        return view('admin/member_list', $data);
    }
    public function update_member()
    {
        $db = $this->db;
        $id = $this->request->getPost('id');

        $data = [
            'nama_member' => $this->request->getPost('nama'),
            'no_telepon'  => $this->request->getPost('telp'),
            'total_poin'  => $this->request->getPost('poin'),
            // total_transaksi tidak diupdate karena readonly (otomatis sistem)
        ];

        $db->table('member')->where('id_member', $id)->update($data);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function hapus_member($id)
    {
        $db = $this->db;
        $db->table('member')->where('id_member', $id)->delete();
        return $this->response->setJSON(['status' => 'success']);
    }
    public function aset()
    {
        $db = $this->db;
        $data_aset = $db->table('aset')->get()->getResultArray();

        foreach ($data_aset as &$row) {
            $tgl_beli = new \DateTime($row['tgl_beli']);
            $sekarang = new \DateTime();

            $interval = $tgl_beli->diff($sekarang);
            $bulan_berjalan = ($interval->y * 12) + $interval->m;

            if ($bulan_berjalan > $row['umur_ekonomis']) {
                $bulan_berjalan = $row['umur_ekonomis'];
            }

            // Logic Penyusutan
            $penyusutan_per_bulan = ($row['harga_beli'] - $row['nilai_sisa']) / $row['umur_ekonomis'];
            $akumulasi_penyusutan = $penyusutan_per_bulan * $bulan_berjalan;
            $row['nilai_buku'] = $row['harga_beli'] - $akumulasi_penyusutan;
        }

        $data = [
            'title' => 'Manajemen Aset',
            'aset'  => $data_aset
        ];

        // HANYA PANGGIL SATU VIEW INI SAJA
        return view('admin/aset_view', $data);
    }

    public function simpan_aset()
    {
        $db = $this->db;

        // Proses Upload Foto
        $fileFoto = $this->request->getFile('foto_aset');
        $namaFoto = null;
        if ($fileFoto && $fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $fileFoto->move('assets/uploads/aset/', $namaFoto);
        }

        $data = [
            'kode_aset'     => $this->request->getPost('kode_aset'),
            'nama_aset'     => $this->request->getPost('nama_aset'),
            'kategori'      => $this->request->getPost('kategori'),
            'tgl_beli'      => $this->request->getPost('tgl_beli'),
            'harga_beli'    => $this->request->getPost('harga_beli'),
            'umur_ekonomis' => $this->request->getPost('umur_ekonomis'),
            'nilai_sisa'    => $this->request->getPost('nilai_sisa') ?? 0,
            'lokasi'        => $this->request->getPost('lokasi'),
            'kondisi'       => 'Baik',
            'foto_aset'     => $namaFoto,
            'catatan'       => $this->request->getPost('catatan')
        ];

        $db->table('aset')->insert($data);

        // Tambah Log Aktivitas
        $this->tambah_log(session()->get('username'), session()->get('role'), 'Menambah aset baru: ' . $data['nama_aset']);

        return redirect()->to(base_url('admin/aset'))->with('success', 'Aset baru berhasil disimpan!');
    }
    public function update_aset()
    {
        $id = $this->request->getPost('id_aset');

        // Ambil data dari form (angka murni dari input hidden)
        $data = [
            'nama_aset'     => $this->request->getPost('nama_aset'),
            'kategori'      => $this->request->getPost('kategori'),
            'tgl_beli'      => $this->request->getPost('tgl_beli'),
            'lokasi'        => $this->request->getPost('lokasi'),
            'harga_beli'    => $this->request->getPost('harga_beli'), // Ini dapet angka murni
            'umur_ekonomis' => $this->request->getPost('umur_ekonomis'),
            'nilai_sisa'    => $this->request->getPost('nilai_sisa'),
            'kondisi'       => $this->request->getPost('kondisi'),
        ];

        // Logika upload foto (opsional)
        $fileFoto = $this->request->getFile('foto_aset');
        if ($fileFoto->isValid() && !$fileFoto->hasMoved()) {
            $namaFoto = $fileFoto->getRandomName();
            $fileFoto->move('assets/uploads/aset/', $namaFoto);
            $data['foto_aset'] = $namaFoto;
        }

        $db = $this->db;
        $db->table('aset')->where('id_aset', $id)->update($data);

        return redirect()->to(base_url('admin/aset'))->with('success', 'Data aset berhasil diperbarui!');
    }
    public function hapus_aset($id)
    {
        $db = $this->db;

        // 1. Cari data aset dulu buat ambil nama fotonya
        $aset = $db->table('aset')->where('id_aset', $id)->get()->getRowArray();

        if ($aset) {
            // 2. Jika ada fotonya, hapus file fisiknya di folder
            if ($aset['foto_aset'] != '' && file_exists('assets/uploads/aset/' . $aset['foto_aset'])) {
                unlink('assets/uploads/aset/' . $aset['foto_aset']);
            }

            // 3. Hapus data dari database
            $db->table('aset')->where('id_aset', $id)->delete();

            return redirect()->to(base_url('admin/aset'))->with('success', 'Aset berhasil dihapus secara permanen!');
        } else {
            return redirect()->to(base_url('admin/aset'))->with('error', 'Data aset tidak ditemukan!');
        }
    }

    // Tampilkan Halaman Daftar Supplier
    public function supplier()
    {
        $db = $this->db;
        $data['supplier'] = $db->table('supplier')->get()->getResultArray();
        return view('admin/supplier_view', $data); // Pastikan nama file view-nya sesuai
    }

    // Simpan Supplier Baru
    public function simpan_supplier()
    {
        $data = [
            'kode_supplier'   => $this->request->getPost('kode_supplier'),
            'nama_supplier'   => $this->request->getPost('nama_supplier'),
            'nama_pic'        => $this->request->getPost('nama_pic'),
            'no_telp'         => $this->request->getPost('no_telp'),
            'email'           => $this->request->getPost('email'),
            'kategori_supply' => $this->request->getPost('kategori_supply'),
            'alamat'          => $this->request->getPost('alamat'),
        ];

        $db = $this->db;
        $db->table('supplier')->insert($data);

        return redirect()->to(base_url('admin/supplier'))->with('success', 'Data supplier berhasil ditambahkan!');
    }

    // Update Data Supplier
    public function update_supplier()
    {
        $id = $this->request->getPost('id_supplier');
        $data = [
            'nama_supplier'   => $this->request->getPost('nama_supplier'),
            'nama_pic'        => $this->request->getPost('nama_pic'),
            'no_telp'         => $this->request->getPost('no_telp'),
            'email'           => $this->request->getPost('email'),
            'kategori_supply' => $this->request->getPost('kategori_supply'),
            'alamat'          => $this->request->getPost('alamat'),
        ];

        $db = $this->db;
        $db->table('supplier')->where('id_supplier', $id)->update($data);

        return redirect()->to(base_url('admin/supplier'))->with('success', 'Data supplier berhasil diperbarui!');
    }

    // Hapus Supplier
    public function hapus_supplier($id)
    {
        $db = $this->db;
        $db->table('supplier')->where('id_supplier', $id)->delete();

        return redirect()->to(base_url('admin/supplier'))->with('success', 'Supplier telah dihapus dari daftar mitra.');
    }
    public function aset_maintenance()
    {
        $db = $this->db;

        // 1. Ambil data maintenance dan JOIN dengan tabel aset agar muncul nama barangnya
        $data['maintenance'] = $db->table('aset_maintenance')
            ->select('aset_maintenance.*, aset.nama_aset, aset.kode_aset')
            ->join('aset', 'aset.id_aset = aset_maintenance.id_aset')
            ->orderBy('tgl_maintenance', 'DESC')
            ->get()->getResultArray();

        // 2. Ambil daftar aset untuk isi dropdown di Modal Tambah
        $data['daftar_aset'] = $db->table('aset')->get()->getResultArray();

        // 3. Panggil view (Pastikan nanti buat file view dengan nama ini)
        return view('admin/aset_maintenance', $data);
    }

    public function simpan_maintenance()
    {
        $db = $this->db;

        // 1. Ambil data dari input form
        $biayaServis = (int)$this->request->getPost('biaya');
        $idAset      = $this->request->getPost('id_aset');
        $keterangan  = $this->request->getPost('keterangan');

        $data = [
            'id_aset'         => $idAset,
            'tgl_maintenance' => $this->request->getPost('tgl_maintenance'),
            'jenis_tindakan'  => $this->request->getPost('jenis_tindakan'),
            'biaya'           => $biayaServis,
            'teknisi'         => $this->request->getPost('teknisi'),
            'keterangan'      => $keterangan,
        ];

        // Menggunakan Database Transaction agar jika salah satu gagal, semua dibatalkan (aman!)
        $db->transBegin();

        try {
            // 2. Simpan ke tabel aset_maintenance
            $db->table('aset_maintenance')->insert($data);

            // 3. Update kondisi aset di tabel utama jadi 'Normal'
            $db->table('aset')
                ->where('id_aset', $idAset)
                ->update(['kondisi' => 'Normal']);

            // --- JEDOR! 4. OTOMATIS POTONG SALDO CASH FLOW ---
            // Kita ambil nama aset dari database untuk pelengkap keterangan di cash flow
            $aset = $db->table('aset')->where('id_aset', $idAset)->get()->getRowArray();
            $namaAset = $aset['nama_aset'] ?? 'Aset';

            // Ambil saldo akhir paling terakhir dari tabel cash_flow
            $lastCashflow = $db->table('cash_flow')->orderBy('id_cashflow', 'DESC')->limit(1)->get()->getRow();
            $saldoTerakhir = $lastCashflow ? $lastCashflow->saldo_akhir : 0;

            // Kalkulasi saldo baru (Saldo terakhir dikurangi biaya maintenance)
            $saldoBaru = $saldoTerakhir - $biayaServis;

            // Insert otomatis ke tabel cash_flow sebagai uang keluar
            $db->table('cash_flow')->insert([
                'tanggal'     => $this->request->getPost('tgl_maintenance') . ' ' . date('H:i:s'),
                'kategori'    => 'Operasional',
                'keterangan'  => 'Biaya Maintenance ' . $namaAset . ' - ' . $keterangan,
                'masuk'       => 0,
                'keluar'      => $biayaServis,
                'saldo_akhir' => $saldoBaru,
                'created_at'  => date('Y-m-d H:i:s')
            ]);

            $db->transCommit();
            return redirect()->to(base_url('admin/aset_maintenance'))->with('success', 'Data servis berhasil disimpan & Cash Flow terupdate!');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function hapus_maintenance($id)
    {
        $db = $this->db;

        // 1. Cek apakah datanya ada (Opsional tapi bagus buat keamanan)
        $cek = $db->table('aset_maintenance')->where('id_maintenance', $id)->get()->getRow();

        if ($cek) {
            // 2. Eksekusi Hapus
            $db->table('aset_maintenance')->where('id_maintenance', $id)->delete();

            // 3. Kirim notifikasi sukses
            return redirect()->to(base_url('admin/aset_maintenance'))->with('success', 'Riwayat servis berhasil dihapus!');
        } else {
            // Jika ID tidak ditemukan
            return redirect()->to(base_url('admin/aset_maintenance'))->with('error', 'Data tidak ditemukan!');
        }
    }
    public function update_maintenance()
    {
        $db = $this->db;
        $id = $this->request->getPost('id_maintenance');

        $data = [
            'id_aset'         => $this->request->getPost('id_aset'),
            'tgl_maintenance' => $this->request->getPost('tgl_maintenance'),
            'jenis_tindakan'  => $this->request->getPost('jenis_tindakan'),
            'biaya'           => $this->request->getPost('biaya'),
            'teknisi'         => $this->request->getPost('teknisi'),
            'keterangan'      => $this->request->getPost('keterangan'),
        ];

        $db->table('aset_maintenance')->where('id_maintenance', $id)->update($data);

        return redirect()->to(base_url('admin/aset_maintenance'))->with('success', 'Data servis berhasil diperbarui!');
    }
    public function laporan_maintenance()
    {
        $db = $this->db;

        // Ambil filter dari URL
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $id_aset = $this->request->getGet('id_aset');

        $builder = $db->table('aset_maintenance');
        $builder->select('aset_maintenance.*, aset.nama_aset, aset.kode_aset');
        $builder->join('aset', 'aset.id_aset = aset_maintenance.id_aset');

        // Jalankan filter jika ada inputan
        if ($start_date && $end_date) {
            $builder->where('tgl_maintenance >=', $start_date);
            $builder->where('tgl_maintenance <=', $end_date);
        }
        if ($id_aset) {
            $builder->where('aset_maintenance.id_aset', $id_aset);
        }

        $data['laporan'] = $builder->orderBy('tgl_maintenance', 'DESC')->get()->getResultArray();
        $data['daftar_aset'] = $db->table('aset')->get()->getResultArray();

        // Kirim filter kembali ke view agar inputan tidak hilang setelah klik filter
        $data['filter'] = [
            'start_date' => $start_date,
            'end_date'   => $end_date,
            'id_aset'    => $id_aset
        ];

        return view('admin/laporan_maintenance', $data);
    }
    public function pesanan_meja()
    {
        // 1. Kita hapus baris ->where('pt.status_order', 'Pending')
        // Agar semua status (Pending, Ditarik, Dibatalkan) terangkut ke View
        $data['produk'] = $this->db->table('pesanan_temp pt')
            ->select('pt.*, m.nomor_meja')
            // ->join('meja m', 'm.id_meja = pt.id_meja', 'left')
            ->join('meja m', 'm.nomor_meja = pt.nomor_meja', 'left')
            ->orderBy('pt.created_at', 'DESC')
            ->get()->getResultArray();

        $data['title'] = "Antrean Pesanan Meja";

        // 2. Gunakan nama variabel $data['produk'] agar sinkron dengan 
        // foreach ($produk as $p) di file view yang bos buat tadi
        return view('admin/pesanan_meja_view', $data);
    }
    public function konfirmasi_pesanan_meja($id_temp)
    {
        // JEDOR! Gunakan $this->db agar tetap di jalur DB Client Toko
        $db = $this->db;

        try {
            $temp = $db->table('pesanan_temp')->where('id_temp', $id_temp)->get()->getRowArray();
            if (!$temp) {
                return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
            }

            // Ambil murni angkanya saja
            $mejaFix = preg_replace('/[^0-9]/', '', (string)$temp['id_meja']);
            $items = json_decode($temp['item_json'], true);
            $waktuSekarang = date('Y-m-d H:i:s');
            $invoice = 'INV-QR-' . time();

            $db->transBegin();

            // 1. Simpan Transaksi
            $db->table('transaksi')->insert([
                'invoice'           => $invoice,
                'id_user'           => session()->get('id_user') ?: 1,
                'id_meja'           => (!empty($mejaFix) ? (int)$mejaFix : null),
                'nomor_meja'        => (!empty($mejaFix) ? (int)$mejaFix : null),
                'total_bayar'       => $temp['total_harga'],
                'nominal_uang'      => $temp['total_harga'],
                'kembalian'         => 0,
                'metode_pembayaran' => $temp['metode_pembayaran'] ?? 'Tunai',
                'status'            => 'Lunas',
                'keterangan'        => 'Order Meja ' . $temp['id_meja'],
                'order_at'          => $waktuSekarang,
                'created_at'        => $waktuSekarang
            ]);

            $transaksi_id = $db->insertID();

            // 2. Loop Detail & Potong Stok
            foreach ($items as $item) {
                $p = $db->table('produk')->where('produk_id', $item['produk_id'])->get()->getRowArray();

                $db->table('transaksi_detail')->insert([
                    'transaksi_id' => $transaksi_id,
                    'produk_id'    => $item['produk_id'],
                    'qty'          => $item['qty'],
                    'harga_satuan' => $item['harga'],
                    'harga_beli'   => $p['harga_beli'] ?? 0,
                    'subtotal'     => $item['harga'] * $item['qty']
                ]);

                // Potong Stok (Resep / Produk Langsung)
                $resep = $db->table('resep')->where('id_produk_jual', $item['produk_id'])->get()->getResultArray();
                if (empty($resep)) {
                    $db->table('produk')->where('produk_id', $item['produk_id'])
                        ->set('stok', "stok - " . (float)$item['qty'], false)->update();
                } else {
                    foreach ($resep as $r) {
                        $jumlahPotong = (float)$r['jumlah_kebutuhan'] * (int)$item['qty'];
                        $db->table('produk')->where('produk_id', $r['id_bahan_baku'])
                            ->set('stok', "stok - $jumlahPotong", false)->update();
                    }
                }
            }

            // 3. Update status antrean
            $db->table('pesanan_temp')->where('id_temp', $id_temp)->update(['status_order' => 'Dikonfirmasi']);

            // 4. UPDATE STATUS MEJA
            if (!empty($mejaFix)) {
                $db->query("UPDATE meja SET status_meja = 'Terisi' WHERE nomor_meja = ? OR id_meja = ?", [(int)$mejaFix, (int)$mejaFix]);
                $db->table('reservasi')
                    ->where('nomor_meja', (int)$mejaFix)
                    ->where('status_reservasi', 'Pending')
                    ->update(['status_reservasi' => 'Check-in']);
            }

            // CEK STATUS TRANSAKSI
            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Gagal memproses database (Rollback).');
            } else {
                $db->transCommit();
                return redirect()->to(site_url('admin/monitoring_table'))->with('pesan_sukses', 'Pesanan masuk, meja TERISI.');
            }
        } catch (\Exception $e) {
            // JEDOR! Hapus transEnabled() yang bikin kuning, langsung rollback saja
            $db->transRollback();
            return redirect()->back()->with('error', 'Error Fatal: ' . $e->getMessage());
        }
    }
    public function cek_notif_meja()
    {
        // Cek ada berapa pesanan yang statusnya masih 'Pending'
        $jumlah = $this->db->table('pesanan_temp')
            ->where('status_order', 'Pending')
            ->countAllResults();

        return $this->response->setJSON([
            'jumlah' => $jumlah
        ]);
    }
    public function batal_pesanan_meja($id_temp)
    {
        $this->db->table('pesanan_temp')
            ->where('id_temp', $id_temp)
            ->update(['status_order' => 'Dibatalkan']);

        return redirect()->to(site_url('admin/pesanan_meja'))->with('pesan', 'Pesanan telah dibatalkan.');
    }
    public function data_meja()
    {
        $data['meja'] = $this->db->table('meja')->get()->getResultArray();
        $data['title'] = "Manajemen Meja";

        // Kirim juga kode_toko untuk base URL QR
        $data['kode_toko'] = session()->get('kode_toko');

        return view('admin/meja_view', $data);
    }
    public function master_table()
    {
        $db = $this->db;
        $data['meja'] = $db->table('meja')->orderBy('nomor_meja', 'ASC')->get()->getResultArray();
        return view('admin/master_table_view', $data);
    }
    public function simpan_meja()
    {
        $db = $this->db;

        // Ambil data dari input form
        $nomor_meja = $this->request->getPost('nomor_meja');

        if (!$nomor_meja) {
            return redirect()->back()->with('pesan_error', 'Nomor meja tidak boleh kosong!');
        }

        // Masukkan ke database
        $db->table('meja')->insert([
            'nomor_meja'  => $nomor_meja,
            'status_meja' => 'Tersedia' // Default saat meja baru dibuat
        ]);

        return redirect()->to(site_url('admin/master_table'))->with('pesan_sukses', 'Meja baru berhasil ditambahkan!');
    }
    // --- PROSES UPDATE MEJA ---
    public function update_meja($id_meja)
    {
        $db = $this->db;

        // Ambil data nomor meja baru dari input form modal edit
        $nomor_meja = $this->request->getPost('nomor_meja');

        if (!$nomor_meja) {
            return redirect()->back()->with('pesan_error', 'Nomor meja tidak boleh kosong!');
        }

        // Eksekusi Update
        $db->table('meja')
            ->where('id_meja', $id_meja)
            ->update([
                'nomor_meja' => $nomor_meja // pastikan nama kolom di db benar
            ]);

        return redirect()->to(site_url('admin/master_table'))->with('pesan_sukses', 'Data meja berhasil diperbarui!');
    }

    // --- PROSES HAPUS MEJA ---
    public function hapus_meja($id_meja)
    {
        $db = $this->db;
        $hapus = $db->table('meja')->where('id_meja', $id_meja)->delete();

        if ($hapus) {
            return redirect()->to(site_url('admin/master_table'))->with('pesan_sukses', 'Meja berhasil dihapus!');
        } else {
            return redirect()->to(site_url('admin/master_table'))->with('pesan_error', 'Gagal menghapus meja.');
        }
    }
    public function monitoring_table()
    {
        $db = $this->db;

        // Ambil semua data meja untuk dimonitoring statusnya
        $data['meja'] = $db->table('meja')
            ->orderBy('nomor_meja', 'ASC')
            ->get()
            ->getResultArray();

        // Pastikan file view ini sudah dibuat (lokasi: app/Views/admin/meja/monitoring.php)
        return view('admin/monitoring_table', $data);
    }
    public function kosongkan_meja($id_meja)
    {
        $db = $this->db;

        $db->transBegin();
        try {
            // 1. Ambil info nomor meja dulu
            $meja = $db->table('meja')->where('id_meja', $id_meja)->orWhere('nomor_meja', $id_meja)->get()->getRowArray();
            $nomor_meja = $meja['nomor_meja'];

            // 2. Ubah Meja jadi Hijau (Tersedia)
            $db->table('meja')
                ->where('nomor_meja', $nomor_meja)
                ->update(['status_meja' => 'Tersedia']);

            // 3. JEDOR! Update juga tabel reservasi jika ada yang 'Pending' di meja itu
            $db->table('reservasi')
                ->where('nomor_meja', $nomor_meja)
                ->where('status_reservasi', 'Pending')
                ->update(['status_reservasi' => 'Dibatalkan']);

            $db->transCommit();
            return redirect()->back()->with('pesan_sukses', 'Meja sekarang tersedia & reservasi dibersihkan.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }
    public function simpan_reservasi()
    {
        $db = $this->db;

        // Gunakan getPost() dan pastikan namanya sama dengan attribute 'name' di modal
        $nomor_meja      = $this->request->getPost('nomor_meja');
        $nama_pelanggan  = $this->request->getPost('nama_pelanggan');
        $telpon          = $this->request->getPost('telpon');
        $jam_booking     = $this->request->getPost('jam_booking');
        $jumlah_orang    = $this->request->getPost('jumlah_orang');

        $db->transBegin();
        try {
            $data = [
                'nomor_meja'       => $nomor_meja,
                'nama_pelanggan'   => $nama_pelanggan,
                'telpon'           => $telpon,
                'jam_booking'      => $jam_booking,
                'jumlah_orang'     => (int)$jumlah_orang,
                'status_reservasi' => 'Pending'
            ];

            // JEDOR! Eksekusi simpan
            $db->table('reservasi')->insert($data);

            // JEDOR! Update status meja jadi reservasi
            $db->table('meja')->where('nomor_meja', $nomor_meja)->update(['status_meja' => 'Reservasi']);

            $db->transCommit();
            return $this->response->setJSON(['status' => 'success', 'message' => 'Reservasi berhasi!']);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function batal_reservasi($nomor_meja)
    {
        $db = $this->db;

        $db->transBegin();
        try {
            $db->table('meja')->where('nomor_meja', $nomor_meja)->update(['status_meja' => 'Tersedia']);

            $db->table('reservasi')
                ->where('nomor_meja', $nomor_meja)
                ->where('status_reservasi', 'Pending')
                ->update(['status_reservasi' => 'Dibatalkan']);

            $db->transCommit();

            // JEDOR! Kirim sinyal sukses ke view
            session()->setFlashdata('batal_sukses', 'Booking meja ' . $nomor_meja . ' telah berhasil dibatalkan.');
        } catch (\Exception $e) {
            $db->transRollback();
            session()->setFlashdata('batal_error', 'Gagal membatalkan reservasi.');
        }

        return redirect()->to('admin/monitoring_table');
    }
    public function dashboard_table()
    {
        $db = $this->db;

        // Ambil data semua meja untuk dihitung statistiknya
        $data['meja'] = $db->table('meja')->get()->getResultArray();

        // Render ke file view baru bernama dashboard_table
        return view('admin/dashboard_table', $data);
    }
    public function live_display()
    {
        $db = $this->db;

        // Ambil data semua meja urut berdasarkan nomor meja
        $data['meja'] = $db->table('meja')->orderBy('nomor_meja', 'ASC')->get()->getResultArray();

        // Render ke view baru bernama live_display_meja
        return view('admin/live_display_meja', $data);
    }

    public function kasbon()
    {
        $db = $this->db;

        // Ambil data kasbon join dengan user untuk dapat nama pegawainya
        $builder = $db->table('kasbon');
        $builder->select('kasbon.*, user.nama_user');
        $builder->join('user', 'user.id_user = kasbon.id_user');
        $builder->orderBy('kasbon.tanggal_pinjam', 'DESC');

        $data = [
            'title'  => 'Manajemen Kasbon Pegawai',
            'kasbon' => $builder->get()->getResultArray(),
            'pegawai' => $db->table('user')->get()->getResultArray() // Untuk dropdown pilih pegawai
        ];

        return view('admin/kasbon_view', $data);
    }

    public function simpan_kasbon()
    {
        $db = $this->db;

        $data = [
            'id_user'        => $this->request->getPost('id_user'),
            'nominal'        => $this->request->getPost('nominal'),
            'tanggal_pinjam' => $this->request->getPost('tanggal_pinjam'),
            'keterangan'     => $this->request->getPost('keterangan'),
            'status_lunas'   => '0' // Default belum lunas
        ];

        $db->table('kasbon')->insert($data);

        return redirect()->back()->with('message', 'JEDOR! Kasbon berhasil dicatat.');
    }

    public function hapus_kasbon($id)
    {
        $db = $this->db;
        $db->table('kasbon')->where('id_kasbon', $id)->delete();
        return redirect()->back()->with('message', 'Data kasbon berhasil dihapus!');
    }
    // --- SISI KARYAWAN (PENGAJUAN) ---
    public function pengajuan_kasbon()
    {
        $db = $this->db;
        // Anggap kita ambil ID User dari session login
        $id_user = session()->get('id_user');

        $data = [
            'title' => 'Pengajuan Kasbon Saya',
            'riwayat' => $db->table('kasbon')->where('id_user', $id_user)->orderBy('created_at', 'DESC')->get()->getResultArray()
        ];
        return view('admin/pengajuan_kasbon_view', $data);
    }

    public function simpan_pengajuan()
    {
        $db = $this->db;
        $db->table('kasbon')->insert([
            'id_user'        => session()->get('id_user'),
            'nominal'        => $this->request->getPost('nominal'),
            'tanggal_pinjam' => date('Y-m-d'),
            'keterangan'     => $this->request->getPost('keterangan'),
            'status'         => 'Pending',
            'status_lunas'   => '0'
        ]);
        return redirect()->back()->with('message', 'Pengajuan berhasil dikirim, tunggu approval admin.');
    }

    // --- SISI ADMIN (APPROVAL) ---
    public function approve_kasbon($id)
    {
        $db = $this->db;

        // 1. Ambil data kasbon dan JOIN dengan tabel user untuk ambil nama_user
        $kasbon = $db->table('kasbon')
            ->select('kasbon.nominal, kasbon.id_user, user.nama_user')
            ->join('user', 'user.id_user = kasbon.id_user')
            ->where('id_kasbon', $id)
            ->get()->getRow();

        if (!$kasbon) {
            return redirect()->back()->with('error', 'JEDOR! Data kasbon tidak ditemukan.');
        }

        // 2. Update Status Kasbon jadi Disetujui
        $db->table('kasbon')->where('id_kasbon', $id)->update(['status' => 'Disetujui']);

        // 3. Catat ke Cash Flow dengan keterangan Nama User
        $db->table('cash_flow')->insert([
            'tanggal'    => date('Y-m-d H:i:s'),
            'kategori'   => 'Operasional',
            'keterangan' => 'Kasbon Pegawai: ' . $kasbon->nama_user,
            'masuk'      => 0,
            'keluar'     => $kasbon->nominal,
        ]);

        return redirect()->to(base_url('admin/kasbon'))->with('message', 'Kasbon ' . $kasbon->nama_user . ' disetujui & tercatat di Arus Kas!');
    }

    public function reject_kasbon($id)
    {
        $db = $this->db;
        $db->table('kasbon')->where('id_kasbon', $id)->update(['status' => 'Ditolak']);
        return redirect()->to(base_url('admin/kasbon'))->with('message', 'Kasbon telah ditolak.');
    }
    public function cashflow()
    {
        $db = $this->db; // Menggunakan properti db yang sudah ada di Admin

        // Filter bulan dan tahun
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['title'] = 'Arus Kas (Cash Flow)';
        $data['bulan_pilih'] = $bulan;
        $data['tahun_pilih'] = $tahun;

        // Ambil data cashflow per bulan
        $data['cashflow'] = $db->table('cash_flow')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->orderBy('tanggal', 'ASC')
            ->get()->getResultArray();

        // Hitung total saldo akumulasi (Saldo Akhir Real-time)
        $summary = $db->table('cash_flow')
            ->selectSum('masuk', 'total_masuk')
            ->selectSum('keluar', 'total_keluar')
            ->get()->getRow();

        $data['saldo_saat_ini'] = ($summary->total_masuk ?? 0) - ($summary->total_keluar ?? 0);

        // JEDOR! Tambahkan ini agar data toko & alamat bisa tampil di view/cetakan
        $data['pengaturan'] = $db->table('pengaturan')->get()->getRowArray();

        return view('admin/cashflow_view', $data);
    }

    public function simpan_cashflow()
    {
        $db = $this->db;

        $jenis  = $this->request->getPost('jenis');
        $nominal = $this->request->getPost('nominal');
        $masuk  = ($jenis == 'masuk') ? $nominal : 0;
        $keluar = ($jenis == 'keluar') ? $nominal : 0;

        // Ambil saldo akhir terakhir untuk kalkulasi saldo_akhir baru
        $lastRow = $db->table('cash_flow')->orderBy('id_cashflow', 'DESC')->limit(1)->get()->getRow();
        $saldoTerakhir = $lastRow ? $lastRow->saldo_akhir : 0;
        $saldoBaru = $saldoTerakhir + $masuk - $keluar;

        $db->table('cash_flow')->insert([
            'tanggal'    => $this->request->getPost('tanggal') . ' ' . date('H:i:s'),
            'kategori'   => $this->request->getPost('kategori'),
            'keterangan' => $this->request->getPost('keterangan'),
            'masuk'      => $masuk,
            'keluar'     => $keluar,
            'saldo_akhir' => $saldoBaru,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('message', 'Data Arus Kas Berhasil!');
    }

    // Fungsi Export Excel
    public function export_cashflow()
    {
        $db = $this->db;
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $cashflow = $db->table('cash_flow')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->orderBy('tanggal', 'ASC')
            ->get()->getResultArray();

        $pengaturan = $db->table('pengaturan')->get()->getRowArray();

        // Hapus semua output buffer agar file bersih
        if (ob_get_length()) ob_end_clean();

        // Set header untuk download excel
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Cashflow_" . $bulan . "_" . $tahun . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo view('admin/export_cashflow_excel', [
            'cashflow'   => $cashflow,
            'bulan'      => $bulan,
            'tahun'      => $tahun,
            'pengaturan' => $pengaturan
        ]);
        exit(); // JEDOR! Wajib pakai exit agar tidak ada output tambahan
    }

    // Fungsi Cetak Laporan (Sudah benar, pastikan variabel di view dipanggil tepat)
    public function cetak_cashflow()
    {
        $db = $this->db;
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $data['cashflow'] = $db->table('cash_flow')
            ->where('MONTH(tanggal)', $bulan)
            ->where('YEAR(tanggal)', $tahun)
            ->orderBy('tanggal', 'ASC')
            ->get()->getResultArray();

        // Ini sudah benar
        $data['pengaturan'] = $db->table('pengaturan')->get()->getRowArray();
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;

        return view('admin/cetak_cashflow_view', $data);
    }
    public function proses_retur()
    {
        $db = $this->db;

        // 1. Tangkap input dari form retur
        $invoiceAsal   = $this->request->getPost('invoice_asal');
        $produkId      = $this->request->getPost('produk_id');
        $qtyRetur      = (float)$this->request->getPost('qty_retur');
        $alasan        = $this->request->getPost('alasan');
        $kembaliKeStok = $this->request->getPost('kembali_ke_stok'); // Isinya 'Ya' atau 'Tidak'

        $waktuSekarang = date('Y-m-d H:i:s');
        $kodeRetur     = 'RET-' . date('Ymd-His');

        // 2. Ambil detail transaksi lama untuk tahu harga satuannya dulu
        $transaksi = $db->table('transaksi')->where('invoice', $invoiceAsal)->get()->getRow();
        if (!$transaksi) {
            return redirect()->back()->with('error', 'Nomor invoice asal tidak ditemukan!');
        }

        $detailLama = $db->table('transaksi_detail')
            ->where('transaksi_id', $transaksi->transaksi_id)
            ->where('produk_id', $produkId)
            ->get()->getRow();

        if (!$detailLama) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan dalam invoice tersebut!');
        }

        // Cek agar kasir tidak meretur melebihi jumlah yang dibeli dulu
        if ($qtyRetur > (float)$detailLama->qty) {
            return redirect()->back()->with('error', 'Jumlah retur melebihi jumlah pembelian awal!');
        }

        // Hitung nominal refund
        $hargaSatuan = (int)$detailLama->harga_satuan;
        $totalRefund = $hargaSatuan * $qtyRetur;

        // MULAI TRANSAKSI DATABASE (JEDOR!)
        $db->transStart();

        // 3. Simpan ke tabel HEADER retur
        $db->table('retur')->insert([
            'kode_retur'   => $kodeRetur,
            'invoice_asal' => $invoiceAsal,
            'total_refund' => $totalRefund,
            'id_user'      => session()->get('id_user') ?? 1,
            'created_at'   => $waktuSekarang
        ]);

        $idReturBaru = $db->insertID();

        // 4. Simpan ke tabel DETAIL retur
        $db->table('retur_detail')->insert([
            'id_retur'        => $idReturBaru,
            'produk_id'       => $produkId,
            'qty_retur'       => $qtyRetur,
            'harga_satuan'    => $hargaSatuan,
            'subtotal_refund' => $totalRefund,
            'alasan'          => $alasan,
            'kembali_ke_stok' => $kembaliKeStok
        ]);

        // 5. LOGIKA STOK & KARTU STOK
        $produk = $db->table('produk')->where('produk_id', $produkId)->get()->getRow();
        $stokAwal = (float)$produk->stok;

        if ($kembaliKeStok == 'Ya') {
            // Jika barang masih bagus/salah input, kembalikan ke stok rak
            $stokAkhir = $stokAwal + $qtyRetur;
            $db->table('produk')->where('produk_id', $produkId)->update(['stok' => $stokAkhir]);

            // Catat di kartu stok sebagai barang RETUR MASUK
            $db->table('kartu_stok')->insert([
                'produk_id'      => $produkId,
                'tanggal'        => $waktuSekarang,
                'tipe'           => 'masuk',
                'kode_referensi' => $kodeRetur,
                'stok_awal'      => $stokAwal,
                'stok_masuk'     => $qtyRetur,
                'stok_keluar'    => 0,
                'stok_akhir'     => $stokAkhir,
                'keterangan' => 'Batal Transaksi (Invoice: ' . $transaksi->invoice . ')',
            ]);
        } else {
            // Jika barang rusak/basi, stok produk di rak tidak bertambah, tapi langsung buang ke tabel WASTE
            $db->table('waste')->insert([
                'produk_id'  => $produkId,
                'qty_waste'  => $qtyRetur,
                'alasan'     => 'Dari Retur: ' . $alasan,
                'id_user'    => session()->get('id_user') ?? 1,
                'created_at' => $waktuSekarang
            ]);

            // Catat di kartu stok sebagai WASTE (Stok awal dan akhir tetap sama karena barang langsung dibuang)
            $db->table('kartu_stok')->insert([
                'produk_id'      => $produkId,
                'tanggal'        => $waktuSekarang,
                'tipe'           => 'waste',
                'kode_referensi' => $kodeRetur,
                'stok_awal'      => $stokAwal,
                'stok_masuk'     => 0,
                'stok_keluar'    => 0, // 0 karena tidak mengurangi stok rak utama lagi
                'stok_akhir'     => $stokAwal,
                'keterangan'     => 'Waste Otomatis dari Retur Rusak (' . $alasan . ')',
            ]);
        }

        // 6.OTOMATIS POTONG CASH FLOW KARENA REFUND UANG TUNAI
        if ($totalRefund > 0) {
            $lastCashflow = $db->table('cash_flow')->orderBy('id_cashflow', 'DESC')->limit(1)->get()->getRow();
            $saldoTerakhir = $lastCashflow ? $lastCashflow->saldo_akhir : 0;
            $saldoBaru = $saldoTerakhir - $totalRefund; // Dikurang karena kas keluar untuk refund pembeli

            $db->table('cash_flow')->insert([
                'tanggal'     => $waktuSekarang,
                'kategori'    => 'Operasional',
                'keterangan'  => 'Refund Dana Retur - Kode: ' . $kodeRetur . ' (Invoice Asal: ' . $invoiceAsal . ')',
                'masuk'       => 0,
                'keluar'      => $totalRefund,
                'saldo_akhir' => $saldoBaru,
                'created_at'  => $waktuSekarang
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memproses transaksi retur!');
        }

        return redirect()->back()->with('success', 'Transaksi Retur Berhasil JEDOR Diterapkan! Stok & Cash Flow Sinkron!');
    }
    public function retur()
{
    $db = $this->db;

    // Ambil data filter dari parameter GET URL
    $tglMulai   = $this->request->getGet('tgl_mulai');
    $tglSelesai = $this->request->getGet('tgl_selesai');

    $builder = $db->table('retur')
        ->select('retur.*, user.nama_user as nama_kasir')
        ->join('user', 'user.id_user = retur.id_user', 'left');

    // Jika filter tanggal diisi, lakukan penyaringan berdasarkan rentang waktu
    if (!empty($tglMulai) && !empty($tglSelesai)) {
        // Menggunakan DATE() agar pencarian akurat murni berdasarkan tanggal tanpa terganggu format jam (H:i:s)
        $builder->where("DATE(retur.created_at) >=", $tglMulai);
        $builder->where("DATE(retur.created_at) <=", $tglSelesai);
    }

    $data['all_retur'] = $builder->orderBy('retur.id_retur', 'DESC')
        ->get()
        ->getResultArray();

    return view('admin/retur_view', $data);
}
public function retur_detail($id_retur)
{
    $db = $this->db;

    $data['detail'] = $db->table('retur_detail')
        ->select('retur_detail.*, produk.nama_produk, produk.barcode')
        ->join('produk', 'produk.produk_id = retur_detail.produk_id', 'left')
        ->where('id_retur', $id_retur)
        ->get()
        ->getResultArray();

    // Memaksa pencarian path file langsung ke folders views
    return view('admin/retur_detail_partial.php', $data);
}

//Sewa Aplikasi (Jatuh Tempo)
public function tagihan()
{
    // Ambil pesan kedaluwarsa dari filter tadi
    $data['pesan'] = session()->getFlashdata('expired_msg') ?: 'Masa berlangganan aplikasi Anda telah berakhir.';
    
    // Tampilkan halaman khusus tagihan
    return view('admin/tagihan_view', $data);
}
// Perpanjang Masa Berlaku
public function perpanjang_langganan()
    {
        $dbMaster = \Config\Database::connect(); // Default koneksi ke DB Pusat
        
        // Tarik semua data merchant untuk dioper ke tabel
        $data['semua_toko'] = $dbMaster->table('master_toko')->get()->getResultArray();

        // Panggil view milik Bos
        return view('admin/langganan_toko_view', $data);
    }

    // FUNGSI 2: Untuk mengeksekusi inject update database (POST)
    public function perpanjang_toko_aksi()
    {
        $dbMaster = \Config\Database::connect();
        $dbConfig = config('Database');

        // Tangkap data dari form modal
        $idToko        = $this->request->getPost('id_toko');
        $tglJatuhTempo  = $this->request->getPost('jatuh_tempo');
        $statusAktif    = $this->request->getPost('status_aktif');

        // Cocokkan 'id_toko' agar sesuai primary key DB pusat Bos
        $toko = $dbMaster->table('master_toko')->where('id_toko', $idToko)->get()->getRow();
        if (!$toko) {
            return redirect()->back()->with('error', 'Toko tidak terdaftar.');
        }

        $dbNameClient = $toko->nama_database;

        // KONDISI A: Update data di DB Master Pusat
        $dbMaster->table('master_toko')
            ->where('id_toko', $idToko)
            ->update([
                'jatuh_tempo'  => $tglJatuhTempo,
                'status_aktif' => $statusAktif
            ]);

        // KONDISI B: Menyeberang koneksi steril dan Inject Update langsung ke dalam DB Tenant Client
        try {
            // FIX MUTLAK: Gunakan koneksi steril terpisah agar tidak merusak DB pusat
            $configClient = $dbConfig->default;
            $configClient['database'] = $dbNameClient;
            $dbClient = \Config\Database::connect($configClient, false);

            // Tembak menggunakan 'id_toko' lokal milik client tersebut.
            $dbClient->table('master_toko')
                ->where('id_toko', $idToko)
                ->update([
                    'jatuh_tempo'  => $tglJatuhTempo,
                    'status_aktif' => $statusAktif
                ]);

            $dbClient->close(); // Tutup koneksi client

            return redirect()->back()->with('success', 'Masa aktif toko ' . $toko->nama_toko . ' berhasil diperbarui dan disinkronkan!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update database client: ' . $e->getMessage());
        }
    }
    public function cek_status_langganan()
{
    // Set response agar murni JSON tanpa cache memori browser
    $this->response->setHeader('Content-Type', 'application/json');
    $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
    $this->response->setHeader('Pragma', 'no-cache');

    // Ambil ID Toko dari session internal CodeIgniter
    $id_toko = session()->get('id_toko'); 

    if (!$id_toko) {
        return $this->response->setJSON(['status_aktif' => 'N', 'apakah_expired' => true]);
    }
    
    // Panggil koneksi database standar CI4
    $db = $this->db;
    
    // 🔔 INFO: Ganti 'semua_toko' dan 'id_toko' sesuai nama asli tabel DB master Bos
    $toko = $db->table('semua_toko')->where('id_toko', $id_toko)->get()->getRowArray();

    if ($toko) {
        $hariIni = date('Y-m-d');
        $jatuhTempo = $toko['jatuh_tempo']; // Format tanggal di DB wajib: Y-m-d (Contoh: 2026-06-09)
        
        $apakahExpired = (!empty($jatuhTempo) && $hariIni > $jatuhTempo);

        return $this->response->setJSON([
            'status_aktif'   => $toko['status_aktif'], // Mengembalikan 'Y' atau 'N'
            'apakah_expired' => $apakahExpired        // Mengembalikan true atau false
        ]);
    }

    return $this->response->setJSON(['status_aktif' => 'N', 'apakah_expired' => true]);
}
    // FUNGSI 3: Untuk mengeksekusi tambah tenant baru (POST)
    public function tambah_toko_aksi()
    {
        $dbMaster = \Config\Database::connect(); // Koneksi awal wajib di DB Pusat
        $dbConfig = config('Database');

        // 1. Tangkap semua inputan form
        $namaToko     = $this->request->getPost('nama_toko');
        $kodeToko     = $this->request->getPost('kode_toko');
        $namaDatabase = trim($this->request->getPost('nama_database'));
        $statusAktif  = $this->request->getPost('status_aktif') ?: 'Y';
        $tglDaftar    = $this->request->getPost('tgl_daftar');
        $jatuhTempo   = $this->request->getPost('jatuh_tempo');

        if (preg_match('/[^a-z0-9_]/', $namaDatabase)) {
            return redirect()->back()->with('error', 'Nama database hanya boleh huruf kecil, angka, dan underscore (_) saja!');
        }

        // ==========================================================
        // PROSES 1: WAJIB INSERT KE DATABASE MASTER PUSAT DULU!
        // ==========================================================
        $dataToko = [
            'nama_toko'     => $namaToko,
            'kode_toko'     => $kodeToko,
            'nama_database' => $namaDatabase,
            'status_aktif'  => $statusAktif,
            'tgl_daftar'    => $tglDaftar,
            'jatuh_tempo'   => $jatuhTempo,
        ];

        // Jalankan insert ke DB Pusat
        $insertMaster = $dbMaster->table('master_toko')->insert($dataToko);
        if (!$insertMaster) {
            return redirect()->back()->with('error', 'Gagal mendaftarkan tenant di Database Master Pusat.');
        }

        // Ambil ID utama yang baru saja terbuat di DB Pusat
        $idTokoBaru = $dbMaster->insertID();

        // ==========================================================
        // PROSES 2: BUAT DATABASE BARU DI PHPMYADMIN SECARA OTOMATIS
        // ==========================================================
        try {
            $dbMaster->query("CREATE DATABASE IF NOT EXISTS `{$namaDatabase}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat database otomatis: ' . $e->getMessage());
        }

        // ==========================================================
        // PROSES 3: MENYEBERANG KE DB BARU & MIGRASI STRUKTUR TABEL
        // ==========================================================
        try {
            // FIX MUTLAK: Buka jembatan koneksi steril terpisah ke database baru
            $configBaru = $dbConfig->default;
            $configBaru['database'] = $namaDatabase;
            $dbBaru = \Config\Database::connect($configBaru, false);

            $pathSqlFile = WRITEPATH . 'database.sql';

            if (file_exists($pathSqlFile)) {
                $queryMentah = file_get_contents($pathSqlFile);
                $semuaQuery  = explode(';', $queryMentah);

                foreach ($semuaQuery as $query) {
                    $queryBersih = trim($query);
                    if (!empty($queryBersih)) {
                        $dbBaru->query($queryBersih);
                    }
                }
            } else {
                $dbBaru->close();
                return redirect()->back()->with('error', 'File master struktur tabel tidak ditemukan di folder writable!');
            }

            // ==========================================================
            // PROSES 4: INJECT DATA PROFILE KE TABEL LOKAL DB CLIENT BARU
            // ==========================================================
            // Bersihkan data lama di master_toko lokal milik client (bukan DB pusat!)
            $dbBaru->table('master_toko')->truncate();

            // Gabungkan ID Toko dari pusat agar nilainya kembar identik
            $dataTokoClient = array_merge(['id_toko' => $idTokoBaru], $dataToko);
            
            // Masukkan data ke master_toko lokal client
            $dbBaru->table('master_toko')->insert($dataTokoClient);
            
            // Tutup koneksi database baru
            $dbBaru->close();

            return redirect()->back()->with('success', "Hebat! Database `{$namaDatabase}` berhasil dibuat otomatis dan tenant `{$namaToko}` resmi aktif terdaftar di sistem!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database terbuat, namun gagal menguji profil lokal: ' . $e->getMessage());
        }
    }

    // FUNGSI KHUSUS DEV: Suntik akun developer/admin yang sedang login ke seluruh DB Tenant sekaligus
    public function sync_developer_akun()
    {
        $usernameSesi = session()->get('username'); 
        $roleSesi     = session()->get('role') ?: 'admin';

        if (empty($usernameSesi)) {
            return redirect()->back()->with('error', 'Sesi login tidak ditemukan. Silakan login ulang Bos.');
        }

        $namaTabelUserLokal = 'user'; 
        $dbConfig = config('Database');

        $configPusat = $dbConfig->default;
        $configPusat['database'] = 'db_kasir'; 
        
        $connPusat = \Config\Database::connect($configPusat, false);

        $userPusat = $connPusat->table($namaTabelUserLokal)
            ->where('username', $usernameSesi)
            ->get()
            ->getRow();

        if (!$userPusat || empty($userPusat->password)) {
            $connPusat->close();
            return redirect()->back()->with('error', "Gagal! Akun '{$usernameSesi}' tidak ditemukan atau kolom password kosong di database pusat (`db_kasir`).");
        }

        $passwordAsliPusat = $userPusat->password;
        $namaUserAsliPusat = $userPusat->nama_user;
        $connPusat->close();

        $dataAdminBaru = [
            'username'  => $usernameSesi,
            'password'  => $passwordAsliPusat, 
            'nama_user' => $namaUserAsliPusat,
            'role'      => $roleSesi,
            'is_active' => 1,
        ];

        $dbMaster = \Config\Database::connect();
        $semuaToko = $dbMaster->table('master_toko')
            ->where('status_aktif', 'Y')
            ->get()
            ->getResultArray();

        if (empty($semuaToko)) {
            return redirect()->back()->with('error', 'Tidak ada database tenant aktif.');
        }

        $jumlahSukses = 0;
        $dbGagal = [];

        foreach ($semuaToko as $toko) {
            $dbNameClient = $toko['nama_database'];

            if ($dbNameClient === 'db_kasir') {
                continue;
            }

            try {
                $configClient = $dbConfig->default;
                $configClient['database'] = $dbNameClient;
                $dbClient = \Config\Database::connect($configClient, false);

                if (!$dbClient->tableExists($namaTabelUserLokal)) {
                    $dbGagal[] = $dbNameClient . " (Tabel tidak ada)";
                    $dbClient->close();
                    continue;
                }

                $apakahSudahAda = $dbClient->table($namaTabelUserLokal)
                    ->where('username', $usernameSesi)
                    ->countAllResults();

                if ($apakahSudahAda == 0) {
                    $dbClient->table($namaTabelUserLokal)->insert($dataAdminBaru);
                } else {
                    $dbClient->table($namaTabelUserLokal)
                        ->where('username', $usernameSesi)
                        ->update([
                            'password'  => $passwordAsliPusat,
                            'nama_user' => $namaUserAsliPusat,
                            'role'      => $roleSesi,
                            'is_active' => 1
                        ]);
                }

                $jumlahSukses++;
                $dbClient->close(); 

            } catch (\Exception $e) {
                $dbGagal[] = $dbNameClient;
            }
        }

        return redirect()->back()->with('success', "Berhasil! Sukses menyalin password asli akun '{$usernameSesi}' dari db_kasir ke {$jumlahSukses} Database Tenant dengan status Aktif.");
    }

    //Monitoring seluruh Tenant
    // FUNGSI BARU POIN 5: Agregasi Laporan Semua Tenant ke Dashboard Owner
    // FUNGSI UTAMA: Sudah disesuaikan mutlak dengan query DB Kasir Klien Bos!
    public function dashboard_saas()
    {
        $dbMaster = \Config\Database::connect();
        $dbConfig = config('Database');

        // 1. Tarik semua tenant yang statusnya aktif beroperasi
        $semuaTokoActive = $dbMaster->table('master_toko')
            ->where('status_aktif', 'Y')
            ->get()
            ->getResultArray();

        $totalOmsetSaaS      = 0;
        $totalTransaksiSaaS  = 0;
        $dataLaporanCabang   = [];

        // 2. LOOPING SAKTI: Keliling ke semua DB Tenant
        foreach ($semuaTokoActive as $toko) {
            $dbNameClient = $toko['nama_database'];

            try {
                if ($dbNameClient === 'db_kasir' || $dbNameClient === $dbMaster->getDatabase()) {
                    $dbClient = $dbMaster;
                } else {
                    // Buka jembatan koneksi steril ke DB Klien terpisah
                    $configClient = $dbConfig->default;
                    $configClient['database'] = $dbNameClient;
                    $dbClient = \Config\Database::connect($configClient, false);
                }

                $namaTabelTarget = 'transaksi'; 

                if ($dbClient->tableExists($namaTabelTarget)) {
                    
                    $laporanToko = $dbClient->table($namaTabelTarget)
                        ->select('SUM(total_bayar) as omset, COUNT(id) as jumlah_nota', false)
                        ->where('status !=', 'Batal') 
                        ->get()
                        ->getRow();

                    $omsetToko = $laporanToko->omset ?: 0;
                    $notaToko  = $laporanToko->jumlah_nota ?: 0;

                    // Akumulasikan ke Grand Total Nasional Owner
                    $totalOmsetSaaS     += $omsetToko;
                    $totalTransaksiSaaS += $notaToko;

                    // Tampung list per cabang untuk urutan rangking di tabel dashboard
                    $dataLaporanCabang[] = [
                        'nama_toko'     => $toko['nama_toko'],
                        'nama_database' => $dbNameClient,
                        'omset'         => $omsetToko,
                        'total_nota'    => $notaToko
                    ];
                }

                // Tutup koneksi HANYA jika itu koneksi eksternal (bukan db_kasir pusat)
                if ($dbNameClient !== 'db_kasir' && $dbNameClient !== $dbMaster->getDatabase()) {
                    $dbClient->close();
                }

            } catch (\Exception $e) {
                continue;
            }
        }
        
        $data['total_omset']       = $totalOmsetSaaS;
        $data['total_transaksi']   = $totalTransaksiSaaS;
        $data['laporan_cabang']    = $dataLaporanCabang;
        $data['total_tenant_aktif']= count($semuaTokoActive);

        return view('admin/dashboard_saas_view', $data);
    }
    // Halaman List Pengumuman Broadcast
    public function broadcast_pengumuman()
    {
        $dbMaster = \Config\Database::connect();
        
        $data['semua_pengumuman'] = $dbMaster->table('master_pengumuman')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/broadcast_view', $data);
    }

    // Aksi Simpan Pengumuman Baru (POST)
    public function simpan_pengumuman_aksi()
    {
        $dbMaster = \Config\Database::connect();

        $data = [
            'judul'        => $this->request->getPost('judul'),
            'isi_pesan'    => $this->request->getPost('isi_pesan'),
            'tgl_mulai'    => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'  => $this->request->getPost('tgl_selesai'),
            'status_aktif' => $this->request->getPost('status_aktif') ?: 'Y'
        ];

        $insert = $dbMaster->table('master_pengumuman')->insert($data);

        if ($insert) {
            return redirect()->to(site_url('admin/broadcast_pengumuman'))->with('success', 'Pengumuman massal berhasil disiarkan ke seluruh tenant, Bos!');
        } else {
            return redirect()->back()->with('error', 'Gagal menerbitkan pengumuman.');
        }
    }
    public function update_broadcast($id)
    {
        $db = $this->db;
        
        $data = [
            'judul'        => $this->request->getPost('judul'),
            'isi_pesan'    => $this->request->getPost('isi_pesan'),
            'tgl_mulai'    => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'  => $this->request->getPost('tgl_selesai'),
            'status_aktif' => $this->request->getPost('status_aktif'),
        ];

        $db->table('master_pengumuman')->where('id_pengumuman', $id)->update($data);

        // UBAH INI: Pastikan mengarah ke rute yang benar
        return redirect()->to(site_url('admin/broadcast_pengumuman'))->with('success', 'Data broadcast berhasil diperbarui!');
    }
    public function status_broadcast($id, $status)
    {
        // Hubungkan ke laci database pusat induk (db_kasir tempat master_pengumuman bersarang)
        $dbConfig = config('Database');
        $configPusat = $dbConfig->default;
        $configPusat['database'] = 'db_kasir'; 
        
        $dbPusat = \Config\Database::connect($configPusat, false);

        // Amankan kiriman status parameter url
        $statusBaru = ($status === 'Y') ? 'Y' : 'N';
        $teksStatus = ($statusBaru === 'Y') ? 'diaktifkan kembali!' : 'berhasil dinonaktifkan!';

        // Jalankan perintah update status_aktif ke tabel master_pengumuman pusat
        $updateStatus = $dbPusat->table('master_pengumuman')
            ->where('id_pengumuman', $id)
            ->update(['status_aktif' => $statusBaru]);

        $dbPusat->close(); // Selalu sterilkan koneksi setelah beres query

        if ($updateStatus) {
            return redirect()->back()->with('success', 'Status siaran broadcast ' . $teksStatus);
        } else {
            return redirect()->back()->with('error', 'Gagal memproses perubahan status siaran.');
        }
    }
    public function cek_broadcast_realtime()
    {
        $dbConfig = config('Database');
        $hariIni  = date('Y-m-d');
        
        try {
            $configPusat = $dbConfig->default;
            $configPusat['database'] = 'db_kasir'; 
            $dbPusat = \Config\Database::connect($configPusat, false);
            
            $broadcastPusat = $dbPusat->table('master_pengumuman')
                ->where('status_aktif', 'Y')
                ->where('tgl_mulai <=', $hariIni)
                ->where('tgl_selesai >=', $hariIni)
                ->orderBy('id_pengumuman', 'DESC')
                ->get()
                ->getRowArray();
            
            $dbPusat->close();
        } catch (\Exception $e) {
            $broadcastPusat = null; 
        }

        return $this->response->setJSON([
            'aktif'      => (!empty($broadcastPusat)) ? true : false,
            'judul'      => $broadcastPusat['judul'] ?? '',
            'pesan'      => $broadcastPusat['isi_pesan'] ?? '',
            // 🎯 TAMBAHAN: Kirim tanggal yang sudah matang di sini
            'created_at' => isset($broadcastPusat['created_at']) ? date('d/m/Y H:i', strtotime($broadcastPusat['created_at'])) : date('d/m/Y H:i')
        ]);
    }
    public function hapus_broadcast($id)
    {
        $db = $this->db;
        $db->table('master_pengumuman')->where('id_pengumuman', $id)->delete();
        return redirect()->to(site_url('admin/broadcast_pengumuman'))->with('success', 'Broadcast berhasil dihapus!');
    }

    // Tampilkan Halaman Master Lembur
    // 1. Tampilkan Halaman Master Lembur
    public function master_lembur()
    {
        $db = $this->db; 
        
        // Ambil data semua riwayat lembur
        $data['semua_lembur'] = $db->table('transaksi_lembur')
            ->orderBy('tanggal_lembur', 'DESC')
            ->get()->getResultArray();
            
        // Ambil data kru
        $data['semua_kru'] = $db->table('user') 
            ->whereNotIn('role', ['admin', 'manajer']) 
            ->get()->getResultArray();

        // Ambil master tarif per jam
        $data['tarif_lembur'] = $db->table('master_tarif_lembur')->get()->getResultArray();

        // 🎯 FIX UTAMA: Sesuaikan nama panggilannya dengan file view milik Bos
        return view('admin/master_lembur_view', $data);
    }

    // 2. Aksi Simpan Input Lembur Baru
    public function simpan_lembur_aksi()
    {
        $db = $this->db;

        $id_user = $this->request->getPost('id_user'); 
        $tanggal = $this->request->getPost('tanggal_lembur');
        $jam     = $this->request->getPost('jumlah_jam');
        $ket     = $this->request->getPost('keterangan');

        // Cari info kru ke tabel user berdasarkan id_user
        $user = $db->table('user')->where('id_user', $id_user)->get()->getRowArray();
        $namaKru = $user['nama_user'] ?? 'Kru';
        $roleKru = $user['role'] ?? 'Kasir';

        // Ambil tarif per jam dari master_tarif_lembur sesuai jabatan
        $getTarif = $db->table('master_tarif_lembur')->where('jabatan', $roleKru)->get()->getRowArray();
        $tarifPerJam = $getTarif['tarif_per_jam'] ?? 15000; 

        // Hitung total uang lembur
        $totalUangLembur = $jam * $tarifPerJam;

        // Masukkan ke tabel transaksi_lembur
        $db->table('transaksi_lembur')->insert([
            'id_user'           => $id_user,
            'nama_kru'          => $namaKru,
            'tanggal_lembur'    => $tanggal,
            'jumlah_jam'        => $jam,
            'tarif_per_jam'     => $tarifPerJam,
            'total_uang_lembur' => $totalUangLembur,
            'keterangan'        => $ket
        ]);

        return redirect()->back()->with('success', 'Data lembur ' . $namaKru . ' berhasil dicatat!');
    }
    // Aksi Tambah Master Jabatan Baru
    public function tambah_jabatan_aksi()
    {
        $db = $this->db;

        $jabatan = $this->request->getPost('jabatan');
        $tarif   = $this->request->getPost('tarif_per_jam') ?? 0;

        // 🎯 VALIDASI: Cek dulu apakah nama jabatan sudah pernah didaftarkan biar tidak double
        $cek = $db->table('master_tarif_lembur')->where('jabatan', $jabatan)->get()->getRowArray();
        
        if ($cek) {
            return redirect()->back()->with('error', 'Jabatan ' . $jabatan . ' sudah ada di daftar, Bos!');
        }

        // Jika lolos validasi, langsung gaskeun masukkan ke database
        $db->table('master_tarif_lembur')->insert([
            'jabatan'       => $jabatan,
            'tarif_per_jam' => $tarif
        ]);

        return redirect()->back()->with('success', 'Jabatan baru ' . $jabatan . ' berhasil ditambahkan!');
    }
    // Aksi Update Tarif Lembur Per Jam
    public function update_tarif_aksi()
    {
        $db = $this->db;
        
        $id_tarif = $this->request->getPost('id_tarif');
        $tarif_baru = $this->request->getPost('tarif_per_jam');

        // Update nominal tarif berdasarkan id_tarif
        $db->table('master_tarif_lembur')
            ->where('id_tarif', $id_tarif)
            ->update([
                'tarif_per_jam' => $tarif_baru
            ]);

        return redirect()->back()->with('success', 'Tarif lembur posisi berhasil diperbarui, Bos!');
    }
    // Aksi Hapus Lembur
    public function hapus_lembur($id)
    {
        $db = $this->db;
        $db->table('transaksi_lembur')->where('id_lembur', $id)->delete();
        return redirect()->back()->with('success', 'Data riwayat lembur berhasil dihapus!');
    }
    // Aksi Hapus Master Jabatan
    public function hapus_jabatan($id)
    {
        $db = $this->db;

        // Ambil info nama jabatannya dulu untuk keperluan teks notifikasi sukses
        $row = $db->table('master_tarif_lembur')->where('id_tarif', $id)->get()->getRowArray();
        $nama_jabatan = $row['jabatan'] ?? '';

        if ($nama_jabatan) {
            // Eksekusi hapus dari tabel master_tarif_lembur
            $db->table('master_tarif_lembur')->where('id_tarif', $id)->delete();
            return redirect()->back()->with('success', 'Master jabatan ' . $nama_jabatan . ' berhasil dihapus, Bos!');
        }

        return redirect()->back()->with('error', 'Data jabatan tidak ditemukan!');
    }
    // Menampilkan daftar pengajuan
    // FUNGSI UNTUK MENU PENGAJUAN (Bisa dipakai karyawan)
    public function pengajuan_izin() 
    {
        $db = $this->db;
        $id_login = session()->get('id_user');
        
        $data['riwayat_izin'] = $db->table('pengajuan_izin')
            ->where('id_user', $id_login)
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
                
        // Pastikan file ini ada di: app/Views/admin/pengajuan_izin_view.php
        return view('admin/pengajuan_izin_view', $data);
    }
        public function simpan_izin()
    {
        $db = $this->db;
        $file = $this->request->getFile('file_pendukung');
        $newName = null;

        // Cek apakah ada file yang di-upload dan valid
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/surat/', $newName); // Pastikan folder 'public/uploads/surat/' ada
        }

        $data = [
            'id_user'        => session()->get('id_user'),
            'jenis_izin'     => $this->request->getPost('jenis_izin'),
            'tgl_mulai'      => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'    => $this->request->getPost('tgl_selesai'),
            'alasan'         => $this->request->getPost('alasan'),
            'file_pendukung' => $newName,
            'status'         => 'Pending'
        ];

        $db->table('pengajuan_izin')->insert($data);

        return redirect()->to(site_url('admin/pengajuan_izin'))->with('success', 'Pengajuan berhasil dikirim, Bos!');
    }
    public function update_izin() 
    {
        $db = $this->db;
        
        // Ambil ID dari form hidden
        $id = $this->request->getPost('id_izin');
        
        // Data yang akan diupdate
        $data = [
            'jenis_izin'   => $this->request->getPost('jenis_izin'),
            'tgl_mulai'    => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'  => $this->request->getPost('tgl_selesai'),
            'alasan'       => $this->request->getPost('alasan'),
        ];

        // Lakukan update ke tabel
        $db->table('pengajuan_izin')
        ->where('id_izin', $id)
        ->update($data);
        
        return redirect()->to(site_url('admin/pengajuan_izin'))->with('success', 'Data berhasil diupdate!');
    }
    public function hapus_izin($id) 
    {
        $db = $this->db;
        
        // Hapus data berdasarkan ID
        $db->table('pengajuan_izin')
        ->where('id_izin', $id)
        ->delete();
        
        return redirect()->to(site_url('admin/pengajuan_izin'))->with('success', 'Pengajuan berhasil dihapus!');
    }

    // FUNGSI UNTUK MENU APPROVAL (Hanya Bos yang bisa lihat)
    public function izin_approval() 
    {
        $db = $this->db;
        
        $data['pengajuan'] = $db->table('pengajuan_izin')
            ->select('pengajuan_izin.*, user.nama_user') // Ambil kolom yang diperlukan
            ->join('user', 'user.id_user = pengajuan_izin.id_user', 'left')
            ->orderBy('pengajuan_izin.created_at', 'DESC')
            ->get()->getResultArray();
                
        // Pastikan file ini ada di: app/Views/admin/izin_approval_view.php
        return view('admin/izin_approval_view', $data);
    }
    public function riwayat_izin() 
    {
        $db = $this->db;
        // FILTER BERDASARKAN USER LOGIN agar karyawan tidak bisa lihat data teman lain
        $id_login = session()->get('id_user');
        $data['riwayat_izin'] = $db->table('pengajuan_izin')
            ->where('id_user', $id_login) // <--- KUNCI KEAMANANNYA DI SINI
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
        return view('admin/pengajuan_izin_view', $data);
    }

    public function proses_izin($id, $status) 
    {
        $db = $this->db;
        $updateData = ['status' => $status];
        if ($status == 'Ditolak') {
            $updateData['alasan_tolak'] = $this->request->getPost('alasan_tolak');
        } else {
            $updateData['alasan_tolak'] = null; 
        }
        
        $db->table('pengajuan_izin')
        ->where('id_izin', $id)
        ->update($updateData);
        
        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui!');
    }
    public function toggleMaintenance()
    {
        $file = FCPATH . '../maintenance.flag'; // Lokasi file flag di luar folder public
        
        if (file_exists($file)) {
            // Jika file ADA, berarti lagi maintenance. Hapus file = Buka Toko
            unlink($file);
            return redirect()->back()->with('success', 'Maintenance dimatikan, aplikasi kembali normal.');
        } else {
            // Jika file TIDAK ADA, buat file = Maintenance AKTIF
            file_put_contents($file, 'LOCKED');
            return redirect()->back()->with('success', 'Maintenance diaktifkan, aplikasi terkunci.');
        }
    }
    
}
    