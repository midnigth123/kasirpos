<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --- 1. RUTE AUTH ---
// --- 1. AUTHENTICATION ---
$routes->get('cek_lisensi_toko', 'Auth::cek_status_langganan');
$routes->get('clear_session', 'Auth::clear_session');
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('auth/proses_login', 'Auth::proses_login');
$routes->get('auth/cek_status_langganan', 'Auth::cek_status_langganan');
$routes->get('logout', 'Auth::logout'); // Akan dilempar ke close-kasir oleh Auth Controller

// --- 2. KASIR: AKSES SEBELUM TRANSAKSI (BEBAS FILTER SHIFT) ---
// Rute ini harus di luar filter agar kasir bisa absen dan pulang tanpa hambatan
$routes->get('kasir/absen', 'Kasir::absen_masuk');
$routes->post('kasir/simpan_absen', 'Kasir::simpan_absen');
$routes->get('kasir/absen_pulang', 'Kasir::absen_pulang');
$routes->post('kasir/simpan_absen_pulang', 'Kasir::simpan_absen_pulang');

//Absensi untuk Kru
$routes->post('absensi/absen_pakai_pin', 'Absensi::absen_pakai_pin');
$routes->post('absensi/absen_pulang_pin', 'Absensi::absen_pulang_pin');




// --- 3. KASIR: FITUR TRANSAKSI (DILINDUNGI FILTER SHIFT) ---
$routes->group('kasir', ['filter' => 'shiftKasir'], function ($routes) {

    // Fitur Utama & Notif (AJAX)
    $routes->get('transaksi', 'Kasir::index');
    $routes->get('cek_notif_antrean', 'Kasir::cek_notif_antrean');

    // --- PINDAHKAN KE SINI (DI DALAM GRUP) ---
    // Letakkan di atas operasional transaksi lainnya
    $routes->get('tarik_ke_cart/(:any)', 'Kasir::tarik_ke_cart/$1');
    $routes->post('batal_pesanan_meja/(:num)', 'Kasir::batal_pesanan_meja/$1');

    $routes->get('hapus_item_temp/(:any)', 'Kasir::hapus_item_temp/$1');
    $routes->get('hapus_semua_temp', 'Kasir::hapus_semua_temp');
    // Alur Buka & Tutup Kasir (Shift)
    $routes->get('open-kasir', 'Kasir::index');
    $routes->post('open-kasir/simpan', 'KasirShift::bukaKasir');
    $routes->get('close-kasir', 'KasirShift::formTutupKasir');
    $routes->post('close-kasir/simpan', 'KasirShift::tutupKasir');

    $routes->get('get_meja_status', 'Kasir::get_meja_status');
    $routes->post('kosongkan_meja_manual/(:any)', 'Kasir::kosongkan_meja_manual/$1');
    $routes->post('simpan_reservasi', 'Kasir::simpan_reservasi');
    $routes->get('get_order_by_meja/(:any)', 'Kasir::get_order_by_meja/$1');

    // Operasional Transaksi
    $routes->get('cari/(:any)', 'Kasir::cariProduk/$1');
    $routes->match(['get', 'post'], 'tambah_item_temp', 'Kasir::tambah_item_temp');
    $routes->post('bayar', 'Kasir::bayar');
    $routes->get('tambah_member', 'Kasir::tambah_member');
    $routes->post('tambah_member', 'Kasir::tambah_member');
    $routes->post('cari_member_kasir', 'Kasir::cari_member_kasir');
    $routes->get('pengeluaran', 'Kasir::pengeluaran');
    $routes->post('simpan_pengeluaran', 'Kasir::simpan_pengeluaran');
});

// --- 4. SISI PELANGGAN ---
$routes->get('order', 'Order::index');
$routes->post('order/kirim_pesanan', 'Order::kirim_pesanan');
$routes->get('kasir', 'Kasir::index');

// --- 3. RUTE ADMIN (GRUP DENGAN FILTER) ---
$routes->group('admin', ['filter' => 'roleAdmin'], function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('dashboard', 'Admin::index');

    $routes->get('penerimaan', 'Admin::penerimaan');
    $routes->post('penerimaan/simpan', 'Admin::simpanPenerimaan');
    $routes->post('penerimaan/konfirmasi/(:num)', 'Admin::konfirmasiPenerimaan/$1');

    // Produk
    $routes->get('produk', 'Admin::produk');
    $routes->post('simpan_produk', 'Admin::simpan_produk');
    $routes->get('hapus_produk/(:any)', 'Admin::hapus_produk/$1');
    $routes->post('update_produk/(:segment)', 'Admin::update_produk/$1');

    // Transaksi & Shift
    $routes->get('transaksi', 'Admin::transaksi');
    $routes->get('transaksi/detail/(:num)', 'Admin::transaksi_detail/$1');
    $routes->get('transaksi/detail', 'Admin::laporan_detail');

    $routes->get('closing', 'Admin::closing_kasir');
    $routes->get('shift-kasir', 'Admin::shift_index');
    $routes->post('update-shift/(:num)', 'Admin::update_shift/$1');
    $routes->post('shift-kasir/simpan', 'Admin::shift_simpan');

    $routes->get('transaksi/cetak', 'Admin::cetak_transaksi');
    $routes->get('transaksi_batal/(:num)', 'Admin::transaksi_batal/$1');

    // User & Absensi
    $routes->get('manajemen-user', 'Admin::user');
    $routes->get('user', 'Admin::user');
    $routes->post('manajemen-user/simpan', 'Admin::simpan_user');
    $routes->post('manajemen-user/update/(:num)', 'Admin::update_user/$1');
    $routes->get('manajemen-user/hapus/(:num)', 'Admin::hapus_user/$1');

    //User untuk Client
    $routes->get('user_client', 'Admin::user_client'); 
    $routes->post('user_client/simpan', 'Admin::simpan_user_client');
    $routes->post('user_client/update/(:num)', 'Admin::update_user_client/$1');
    $routes->get('user_client/hapus/(:num)', 'Admin::hapus_user_client/$1');

    $routes->post('simpan-hak-akses', 'Admin::simpan_hak_akses');
    $routes->get('absensi', 'Admin::absensi');
    $routes->get('laporan_absensi', 'Admin::laporan_absensi');
    $routes->get('export_excel', 'Admin::export_excel');


    //Stok Opname
    $routes->get('stokopname', 'Admin::stokopname');
    $routes->get('toggle_opname/(:num)', 'Admin::toggle_opname/$1');
    $routes->get('cek_status_hold', 'Admin::cek_status_hold');
    $routes->match(['get', 'post'], 'proses_opname_borongan', 'Admin::proses_opname_borongan');
    $routes->get('detail_opname/(:any)', 'Admin::detail_opname/$1');

    //Laporan Opname
    $routes->get('laporan_opname', 'Admin::laporan_opname');
    $routes->get('cetak_opname/(:any)', 'Admin::cetak_opname/$1');

    //Laporan Mutasi
    $routes->get('mutasi_barang', 'Admin::mutasi_barang');
    $routes->post('mutasi_barang', 'Admin::proses_mutasi');

    //Kartu Stok
    $routes->get('kartu_stok', 'Admin::kartu_stok');

    //Resep
    $routes->get('resep', 'Admin::resep');
    $routes->post('simpan_resep', 'Admin::simpan_resep');
    $routes->get('hapus_resep/(:num)', 'Admin::hapus_resep/$1');

    //Settingan 
    $routes->get('pengaturan', 'Admin::pengaturan');
    $routes->post('pengaturan/update', 'Admin::update_pengaturan');

    $routes->get('absensi/export_excel_native', '\App\Controllers\Absensi::export_excel_native');

    //Waste (Terbuang)
    $routes->get('waste', 'Admin::waste');
    $routes->post('simpan_waste', 'Admin::simpan_waste');
    $routes->get('laporan_waste', 'Admin::laporan_waste');

    //Laba rugi
    $routes->get('laporan_laba_rugi', 'Admin::laporan_laba_rugi');

    //Gaji
    $routes->get('master_gaji', 'Admin::hitung_gaji_kru');
    $routes->post('simpan_gaji', 'Admin::simpan_gaji');
    $routes->get('hapus_gaji/(:num)', 'Admin::hapus_gaji/$1');
    $routes->get('cetak_slip/(:any)/(:any)/(:any)', 'Admin::cetak_slip/$1/$2/$3');
    $routes->get('laporan_gaji', 'Admin::laporan_gaji');

    $routes->get('master_lembur', 'Admin::master_lembur');
    $routes->post('simpan_lembur_aksi', 'Admin::simpan_lembur_aksi');
    $routes->get('hapus_lembur/(:num)', 'Admin::hapus_lembur/$1');
    $routes->post('update_tarif_aksi', 'Admin::update_tarif_aksi');
    $routes->post('tambah_jabatan_aksi', 'Admin::tambah_jabatan_aksi');
    $routes->get('hapus_jabatan/(:num)', 'Admin::hapus_jabatan/$1');

    $routes->get('pengeluaran', 'Admin::pengeluaran');
    $routes->get('laporan_pengeluaran', 'Admin::laporan_pengeluaran');

    // Tambahkan juga rute POST untuk simpan datanya
    $routes->post('simpan_pengeluaran', 'Admin::simpan_pengeluaran');
    $routes->get('hapus_pengeluaran/(:num)', 'Admin::hapus_pengeluaran/$1');
    $routes->post('update_pengeluaran', 'Admin::update_pengeluaran');

    $routes->get('promo', 'Admin::promo');
    $routes->post('simpan_promo', 'Admin::simpan_promo');
    $routes->get('hapus_promo/(:num)', 'Admin::hapus_promo/$1');
    $routes->post('cek_promo', 'Admin::cek_promo');
    $routes->post('update_promo', 'Admin::update_promo');

    $routes->get('member', 'Admin::member');
    $routes->post('update_member', 'Admin::update_member');
    $routes->get('hapus_member/(:num)', 'Admin::hapus_member/$1');
    $routes->post('cari_member_kasir', 'Admin::cari_member_kasir');

    //Manajemen Aset
    $routes->get('aset', 'Admin::aset');
    $routes->post('simpan_aset', 'Admin::simpan_aset');
    $routes->get('hapus_aset/(:num)', 'Admin::hapus_aset/$1');
    $routes->get('detail_aset/(:num)', 'Admin::detail_aset/$1');
    $routes->post('update_aset', 'Admin::update_aset');

    //Manajemen Supplier
    $routes->get('supplier', 'Admin::supplier');
    $routes->post('simpan_supplier', 'Admin::simpan_supplier');
    $routes->post('update_supplier', 'Admin::update_supplier');
    $routes->get('hapus_supplier/(:num)', 'Admin::hapus_supplier/$1');

    $routes->get('aset_maintenance', 'Admin::aset_maintenance');
    $routes->post('simpan_maintenance', 'Admin::simpan_maintenance');
    $routes->get('hapus_maintenance/(:num)', 'Admin::hapus_maintenance/$1');
    $routes->post('update_maintenance', 'Admin::update_maintenance');

    $routes->get('laporan_maintenance', 'Admin::laporan_maintenance');
    $routes->get('laporan_penerimaan', 'Admin::laporanPenerimaan');
    $routes->get('laporan_penerimaan/excel', 'Admin::exportExcelPenerimaan');

    $routes->get('pesanan_meja', 'Admin::pesanan_meja');
    $routes->get('konfirmasi_pesanan_meja/(:any)', 'Admin::konfirmasi_pesanan_meja/$1');
    $routes->get('batal_reservasi/(:any)', 'Admin::batal_reservasi/$1');
    $routes->get('cek_notif_meja', 'Admin::cek_notif_meja');

    //Master Meja
    $routes->get('master_table', 'Admin::master_table');
    $routes->post('meja/simpan', 'Admin::simpan_meja');
    $routes->post('meja/update/(:num)', 'Admin::update_meja/$1');
    $routes->get('meja/hapus/(:num)', 'Admin::hapus_meja/$1');

    $routes->get('monitoring_table', 'Admin::monitoring_table');
    $routes->get('kosongkan_meja/(:num)', 'Admin::kosongkan_meja/$1');
    $routes->post('simpan_reservasi', 'Admin::simpan_reservasi');
    $routes->get('buka_meja/(:any)', 'Admin::buka_meja/$1');

    $routes->get('dashboard_table', 'Admin::dashboard_table');
    $routes->get('live_display', 'Admin::live_display');

    $routes->get('kasbon', 'Admin::kasbon');
    $routes->post('kasbon/simpan', 'Admin::simpan_kasbon');
    $routes->get('kasbon/hapus/(:num)', 'Admin::hapus_kasbon/$1');
    $routes->get('kasbon/approve/(:num)', 'Admin::approve_kasbon/$1');
    $routes->get('kasbon/reject/(:num)', 'Admin::reject_kasbon/$1');

    $routes->get('pengajuan_kasbon', 'Admin::pengajuan_kasbon');
    $routes->post('simpan_pengajuan', 'Admin::simpan_pengajuan');

    $routes->get('pengajuan_izin', 'Admin::pengajuan_izin'); 
    $routes->post('simpan_izin', 'Admin::simpan_izin');
    $routes->get('hapus_izin/(:num)', 'Admin::hapus_izin/$1');
    $routes->post('update_izin', 'Admin::update_izin');
    $routes->get('izin_approval', 'Admin::izin_approval');
    $routes->post('proses_izin/(:segment)/(:segment)', 'Admin::proses_izin/$1/$2');

    $routes->get('laporan_cashflow', 'Admin::cashflow');
    $routes->post('simpan_cashflow', 'Admin::simpan_cashflow');

    $routes->get('laporan_cashflow', 'Admin::cashflow');
    $routes->post('simpan_cashflow', 'Admin::simpan_cashflow');
    $routes->get('export_cashflow', 'Admin::export_cashflow');
    $routes->get('cetak_cashflow', 'Admin::cetak_cashflow');

    $routes->post('proses_retur', 'Admin::proses_retur');

    $routes->get('retur', 'Admin::retur');
    $routes->get('retur/detail/(:num)', 'Admin::retur_detail/$1');

    //Route untuk Tagihan(Jatuh tempo)
    $routes->get('tagihan', 'Admin::tagihan');

    $routes->get('perpanjang_langganan', 'Admin::perpanjang_langganan');
    $routes->post('perpanjang_toko_aksi', 'Admin::perpanjang_toko_aksi');
    $routes->post('tambah_toko_aksi', 'Admin::tambah_toko_aksi');

    //Sync akun superadmin masuk ke seluruh tenant
    $routes->get('sync_developer_akun', 'Admin::sync_developer_akun');

    //Dashboard seluruh tenant
    $routes->get('dashboard_saas', 'Admin::dashboard_saas');

    //Broadcast Client untuk kebutuhan maintenance rutin
    $routes->get('broadcast', 'Admin::broadcast_pengumuman');
    $routes->get('broadcast_pengumuman', 'Admin::broadcast_pengumuman');
    $routes->post('simpan_pengumuman_aksi', 'Admin::simpan_pengumuman_aksi');
    $routes->get('status_broadcast/(:num)/(:any)', 'Admin::status_broadcast/$1/$2');
    $routes->post('update_broadcast/(:num)', 'Admin::update_broadcast/$1');
    $routes->get('cek_broadcast_realtime', 'Admin::cek_broadcast_realtime');
    $routes->get('hapus_broadcast/(:num)', 'Admin::hapus_broadcast/$1');

    //Fitur Maintenance
    $routes->get('maintenance/toggle/(:segment)', 'MaintenanceController::toggle/$1');
    $routes->get('toggleMaintenance', 'Admin::toggleMaintenance');
});