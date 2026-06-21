<?php

/**
 * @var string $chart_labels
 * @var string $chart_data
 * @var string $pie_labels
 * @var string $pie_data
 * @var array $presentase
 * @var array $riwayat
 * @var \CodeIgniter\Pager\Pager $pager_transaksi
 */
?>

<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Manajemen Pengguna & Shift</h4>
            <p class="text-muted mb-0">Kelola data pengguna, hak akses, log aktivitas, dan shift kasir</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahUserModal">
                <i class="fas fa-plus me-1"></i> Tambah User
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahShiftModal">
                <i class="fas fa-plus-circle me-1"></i> Tambah Shift
            </button>
        </div>
    </div>

    <ul class="nav nav-pills mb-4 gap-2" id="userTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 rounded-pill border-0 shadow-sm" id="data-user-tab" data-bs-toggle="tab"
                data-bs-target="#data-user" type="button" role="tab">
                <i class="fas fa-users me-2"></i> Data User
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 rounded-pill border-0 shadow-sm" id="hak-akses-tab" data-bs-toggle="tab"
                data-bs-target="#hak-akses" type="button" role="tab">
                <i class="fas fa-user-shield me-2"></i> Hak Akses
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 rounded-pill border-0 shadow-sm" id="shift-kasir-tab" data-bs-toggle="tab"
                data-bs-target="#shift-kasir" type="button" role="tab">
                <i class="fas fa-business-time me-2"></i> Pengaturan Shift
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 rounded-pill border-0 shadow-sm" id="aktivitas-tab" data-bs-toggle="tab"
                data-bs-target="#aktivitas" type="button" role="tab">
                <i class="fas fa-history me-2"></i> Log Aktivitas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 rounded-pill border-0 shadow-sm" id="riwayat-shift-tab" data-bs-toggle="tab"
                data-bs-target="#riwayat-shift" type="button" role="tab">
                <i class="fas fa-history me-2"></i> Riwayat Shift
            </button>
        </li>
    </ul>

    <div id="alert-wrapper">
        <?php if (session()->getFlashdata('pesan_sukses')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
            style="border-left: 5px solid #198754;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Berhasil!</strong> <?= session()->getFlashdata('pesan_sukses'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert"
            style="border-left: 5px solid #dc3545;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terjadi Kesalahan!</strong> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
    </div>

    <div class="tab-content" id="userTabContent">
        <div class="tab-pane fade show active" id="data-user" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama User</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">No. HP</th>
                                    <th scope="col">Alamat</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($user)) : ?>
                                <?php foreach ($user as $index => $usr) : ?>
                                <tr>
                                    <th scope="row"><?= $index + 1 ?></th>
                                    <td class="fw-bold"><?= $usr['nama_user'] ?></td>
                                    <td><?= $usr['username'] ?></td>
                                    <td>
                                        <?php
                                                $badgeClass = 'bg-secondary';
                                                if ($usr['role'] == 'admin') $badgeClass = 'bg-danger';
                                                elseif ($usr['role'] == 'kasir') $badgeClass = 'bg-success';
                                                elseif ($usr['role'] == 'manajer') $badgeClass = 'bg-primary';
                                                elseif ($usr['role'] == 'owner') $badgeClass = 'bg-warning';
                                                ?>
                                        <span class="badge <?= $badgeClass ?> text-white rounded-pill px-3">
                                            <?= ucfirst($usr['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= $usr['no_hp'] ?></td>
                                    <td><?= $usr['alamat'] ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal"
                                            data-bs-target="#editUserModal" data-id="<?= $usr['id_user'] ?>"
                                            data-username="<?= $usr['username'] ?>" data-nama="<?= $usr['nama_user'] ?>"
                                            data-role="<?= $usr['role'] ?>" data-hp="<?= $usr['no_hp'] ?>"
                                            data-alamat="<?= $usr['alamat'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#hapusModal" data-nama="<?= $usr['nama_user'] ?>"
                                            data-url="<?= site_url('admin/manajemen-user/hapus/' . $usr['id_user']) ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">Belum ada data user yang
                                        terdaftar.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!empty($pager)) : ?>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Menampilkan data pengguna
                        </div>
                        <div>
                            <?= $pager->links('user_group', 'default') ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="hak-akses" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success-subtle p-3 rounded-3 text-success me-3">
                            <i class="fas fa-user-shield fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Pengaturan Hak Akses (Permission)</h5>
                            <p class="text-muted small mb-0">Atur batasan akses menu atau modul untuk masing-masing
                                peran pengguna.</p>
                        </div>
                    </div>

                    <form action="<?= site_url('admin/simpan-hak-akses') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="table-responsive rounded-3 border shadow-sm">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-dark text-white text-center">
                                    <tr>
                                        <th class="text-start ps-4 py-3">Menu / Modul Sistem</th>
                                        <th style="width: 130px;">Admin</th>
                                        <th style="width: 130px;">Owner</th>
                                        <th style="width: 130px;">Kasir</th>
                                        <th style="width: 130px;">Manajer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5"
                                            style="background-color: #198754 !important; color: white !important; padding: 15px 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem;">
                                            <i class="fas fa-star me-2"></i> MODUL UTAMA SISTEM
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Dashboard & Statistik</span>
                                            <small class="text-muted">Ringkasan performa toko & grafik penjualan
                                                harian</small>
                                        </td>
                                        <?= render_switch('dashboard') ?>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Kasir (Point of Sale)</span>
                                            <small class="text-muted">Akses utama transaksi penjualan barang</small>
                                        </td>
                                        <?= render_switch('transaksi') ?>
                                    </tr>

                                    <tr>
                                        <td colspan="5"
                                            style="background-color: #198754 !important; color: white !important; padding: 15px 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; border-top: 4px solid #0d5a35;">
                                            <i class="fas fa-box me-2"></i> PRODUK & INVENTORI
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Database Produk</span>
                                            <small class="text-muted">Kelola data master barang, harga, dan
                                                kategori</small>
                                        </td>
                                        <?= render_switch('produk') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Kelola Resep</span>
                                                    <small class="text-muted small">Atur komposisi bahan baku setiap
                                                        produk</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('resep') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Penerimaan Barang</span>
                                                    <small class="text-muted small">Catat stok masuk dari
                                                        supplier/vendor</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('penerimaan') ?>
                                    </tr>

                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Pengeluaran</span>
                                                    <small class="text-muted small">Pengeluaran Harian Outlet</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('pengeluaran') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Waste Manejemen</span>
                                                    <small class="text-muted small">Waste Manajemen</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('waste') ?>
                                    </tr>

                                    <tr>
                                        <td colspan="5"
                                            style="background-color: #198754 !important; color: white !important; padding: 15px 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; border-top: 4px solid #0d5a35;">
                                            <i class="fas fa-box me-2"></i> KELOLA STOK OPNAME
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Stok Opname</span>
                                                    <small class="text-muted small">Audit fisik stok di gudang dan
                                                        outlet</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('stokopname') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Mutasi Barang</span>
                                                    <small class="text-muted small">Kelola Mutasi Barang</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('mutasi_barang') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Kartu Stok</span>
                                                    <small class="text-muted small">Kelola Kartu Stok Barang</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('kartu_stok') ?>
                                    </tr>


                                    <tr>
                                        <td colspan="5"
                                            style="background-color: #198754 !important; color: white !important; padding: 15px 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; border-top: 4px solid #0d5a35;">
                                            <i class="fas fa-truck me-2"></i> SUPPLIER & MANAJEMEN ASET
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Data Supplier</span>
                                                    <small class="text-muted small">Kelola kontak dan informasi pemasok
                                                        barang</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('supplier') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Daftar Aset</span>
                                                    <small class="text-muted small">Kelola kontak dan informasi pemasok
                                                        barang</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('aset') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Maintenance Aset</span>
                                                    <small class="text-muted small">Riwayat servis dan perawatan aset
                                                        berkala</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('aset_maintenance') ?>
                                    </tr>



                                    <tr>
                                        <td colspan="5"
                                            style="background-color: #198754 !important; color: white !important; padding: 15px 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; border-top: 4px solid #0d5a35;">
                                            <i class="fas fa-file-invoice-dollar me-2"></i> LAPORAN & KEUANGAN
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Laporan Transaksi</span>
                                            <small class="text-muted">Detail histori penjualan harian dan
                                                bulanan</small>
                                        </td>
                                        <?= render_switch('laporan_transaksi') ?>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Laporan Closing Kasir</span>
                                            <small class="text-muted">Laporan pertanggungjawaban shift kasir</small>
                                        </td>
                                        <?= render_switch('closing') ?>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Laporan Laba Rugi</span>
                                            <small class="text-muted">Analisis keuangan dan margin laba bersih</small>
                                        </td>
                                        <?= render_switch('laporan_laba_rugi') ?>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Laporan Maintenance</span>
                                            <small class="text-muted">Rekap pengeluaran biaya perbaikan aset</small>
                                        </td>
                                        <?= render_switch('laporan_maintenance') ?>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Laporan Waste Manajemen</span>
                                            <small class="text-muted">Rekap Waste Manajemen</small>
                                        </td>
                                        <?= render_switch('laporan_waste') ?>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Laporan Stock Opname</span>
                                            <small class="text-muted">Laporan Stok Opname</small>
                                        </td>
                                        <?= render_switch('laporan_opname') ?>
                                    </tr>


                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Laporan Penerimaan</span>
                                            <small class="text-muted">Laporan Penerimaan</small>
                                        </td>
                                        <?= render_switch('laporan_penerimaan') ?>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Laporan Pengeluaran
                                                Harian</span>
                                            <small class="text-muted">Laporan Pengeluaran Harian</small>
                                        </td>
                                        <?= render_switch('laporan_pengeluaran') ?>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 py-3" style="background-color: #ffffff;">
                                            <span class="fw-bold d-block text-dark fs-6">Laporan Cash Flow
                                            </span>
                                            <small class="text-muted">Laporan Cash Flow</small>
                                        </td>
                                        <?= render_switch('laporan_cashflow') ?>
                                    </tr>

                                    <tr>
                                        <td colspan="5"
                                            style="background-color: #198754 !important; color: white !important; padding: 15px 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; border-top: 4px solid #0d5a35;">
                                            <i class="fas fa-box me-2"></i> Manajemen Table
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Master Table</span>
                                                    <small class="text-muted small">Master Tabel</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('master_table') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Penggunaan Table</span>
                                                    <small class="text-muted small">Kelola Penggunaan Table</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('monitoring_table') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Dashboard Table</span>
                                                    <small class="text-muted small">Kelola Dashboard Table</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('dashboard_table') ?>
                                    </tr>

                                    <tr>
                                        <td colspan="5"
                                            style="background-color: #198754 !important; color: white !important; padding: 15px 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; border-top: 4px solid #0d5a35;">
                                            <i class="fas fa-tools me-2"></i> Gaji & Manage Karyawan
                                        </td>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Absensi Pegawai
                                                    </span>
                                                    <small class="text-muted small">Monitoring kehadiran dan jam kerja
                                                        staf</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('absensi') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Laporan Absensi
                                                        Karyawan</span>
                                                    <small class="text-muted small">Laporan Absensi Karyawan</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('monitoring_absensi') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Gaji Pegawai
                                                    </span>
                                                    <small class="text-muted small">Monitoring Gaji Pegawai</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('master_gaji') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Lembur Pegawai
                                                    </span>
                                                    <small class="text-muted small">Monitoring Lembur Pegawai</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('master_lembur') ?>
                                    </tr>

                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Laporan Gaji Karyawan
                                                    </span>
                                                    <small class="text-muted small">Laporan Gaji Karyawan</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('laporan_gaji') ?>
                                    </tr>

                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Kasbon
                                                    </span>
                                                    <small class="text-muted small">kasbon Karyawan</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('kasbon') ?>
                                    </tr>

                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Pengajuan Kasbon Karyawan
                                                    </span>
                                                    <small class="text-muted small">Pengajuan Kasbon Karyawan</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('pengajuan_kasbon') ?>
                                    </tr>

                                    <tr>
                                        <td colspan="5"
                                            style="background-color: #198754 !important; color: white !important; padding: 15px 20px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 0.9rem; border-top: 4px solid #0d5a35;">
                                            <i class="fas fa-tools me-2"></i> OPERASIONAL & LAINNYA
                                        </td>
                                    </tr>

                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Member
                                                    </span>
                                                    <small class="text-muted small">Daftar Member</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('member') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Promo
                                                    </span>
                                                    <small class="text-muted small">Daftar Promo</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('promo') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Pesanan
                                                    </span>
                                                    <small class="text-muted small">Daftar Pesanan</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('pesanan_meja') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Retur Transaksi
                                                    </span>
                                                    <small class="text-muted small">Retur Transaksi</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('retur') ?>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Pengaturan User
                                                    </span>
                                                    <small class="text-muted small">Pengaturan User</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('user_client') ?>
                                    </tr>

                                    <tr style="background-color: #f8f9fa;">
                                        <td class="ps-5 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-success me-3 fs-5"></i>
                                                <div>
                                                    <span class="fw-bold d-block text-dark">Pengaturan
                                                    </span>
                                                    <small class="text-muted small">Pengaturan Outlet</small>
                                                </div>
                                            </div>
                                        </td>
                                        <?= render_switch('pengaturan') ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-4 border-0 shadow-sm rounded-3 d-flex align-items-center">
                            <i class="fas fa-info-circle me-3 fs-4 text-primary"></i>
                            <div>
                                <span class="fw-bold d-block">Catatan Keamanan</span>
                                <small>Akses untuk <strong>Admin</strong> dikunci otomatis (Hard-locked) untuk mencegah
                                    kegagalan kontrol sistem utama.</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 gap-2">
                            <button type="reset" class="btn btn-light rounded-pill px-4 border">Reset Default</button>
                            <button type="submit" class="btn btn-success rounded-pill px-5 shadow fw-bold">
                                <i class="fas fa-save me-2"></i> Terapkan Hak Akses Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php
            /**
             * Fungsi Render Switch Hak Akses Multi-Role KasirKita
             */
            function render_switch($modul, $adminAttr = '')
            {
                $roles = ['owner', 'kasir', 'manajer'];
                
                $html = '<td class="text-center align-middle bg-light/30">';
                $html .= '<div class="form-check form-switch d-flex justify-content-center">';
                $html .= '<input class="form-check-input" type="checkbox" checked disabled>';
                $html .= '<input type="hidden" name="akses[admin][' . $modul . ']" value="1">';
                $html .= '</div></td>';

                // 2. Looping untuk kolom Owner, Kasir, dan Manajer (Semuanya aktif, bisa dicentang, tanpa disabled)
                foreach ($roles as $role) {
                    $checked = (function_exists('cek_akses') && cek_akses($role, $modul)) ? 'checked' : '';
                    
                    $html .= '<td class="text-center align-middle">';
                    $html .= '<div class="form-check form-switch d-flex justify-content-center">';
                    // Atribut name diikat dinamis sesuai $role, tanpa ada tambahan variabel $disabled
                    $html .= '<input class="form-check-input" type="checkbox" name="akses[' . $role . '][' . $modul . ']" value="1" ' . $checked . '>';
                    $html .= '</div></td>';
                }
                
                return $html;
            }
        ?>

        <div class="tab-pane fade" id="shift-kasir" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Shift</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($shift) && is_array($shift)): ?>
                                <?php foreach ($shift as $index => $row): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td class="fw-bold"><?= $row['nama_shift'] ?></td>
                                    <td><i class="fas fa-clock text-success me-1"></i> <?= $row['jam_mulai'] ?></td>
                                    <td><i class="fas fa-clock text-danger me-1"></i> <?= $row['jam_selesai'] ?></td>
                                    <td>
                                        <?php if (isset($row['status_aktif']) && $row['status_aktif'] == 1): ?>
                                        <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                style="border-radius: 8px;" data-bs-toggle="modal"
                                                data-bs-target="#editShiftModal<?= $row['shift_id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger me-1" data-bs-toggle="modal"
                                                data-bs-target="#hapusShiftModal<?= $row['shift_id'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="hapusShiftModal<?= $row['shift_id'] ?>" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-sm" style="border-radius: 16px;">
                                            <div class="modal-header bg-danger text-white border-0 p-4">
                                                <h5 class="modal-title fw-bold">Konfirmasi Hapus</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-4 px-4 text-center text-dark">
                                                <p class="mb-0">Apakah Anda yakin ingin menghapus
                                                    <strong><?= $row['nama_shift'] ?></strong>?
                                                </p>
                                            </div>
                                            <div class="modal-footer border-0 justify-content-center gap-2 p-4">
                                                <button type="button" class="btn btn-outline-secondary px-4"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <a href="<?= site_url('admin/hapus-shift/' . $row['shift_id']) ?>"
                                                    class="btn btn-danger px-4">Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Belum ada data shift yang
                                        ditambahkan.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="aktivitas" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-1">Log Aktivitas User</h6>
                            <p class="text-muted small mb-0">Pantau log akses yang dilakukan oleh para pengguna ke
                                sistem.</p>
                        </div>

                        <form method="GET" action="<?= site_url('admin/user') . '#aktivitas' ?>"
                            class="d-flex gap-2 align-items-center">
                            <label class="form-label text-muted small mb-0 d-none d-md-block">Filter:</label>
                            <input type="date" name="tgl_mulai" class="form-control form-control-sm"
                                value="<?= $tgl_mulai ?? '' ?>">
                            <span class="text-muted">s/d</span>
                            <input type="date" name="tgl_selesai" class="form-control form-control-sm"
                                value="<?= $tgl_selesai ?? '' ?>">
                            <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>

                            <?php if (!empty($tgl_mulai)): ?>
                            <a href="<?= site_url('admin/user') . '#aktivitas' ?>"
                                class="btn btn-sm btn-outline-secondary">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Aktivitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($log_aktivitas) && is_array($log_aktivitas)): ?>
                                <?php
                                    $perPage = 10;
                                    $start = (isset($currentLogPage) && $currentLogPage > 1) ? ($currentLogPage - 1) * $perPage : 0;

                                    foreach ($log_aktivitas as $index => $log): ?>
                                <tr>
                                    <td><?= $start + $index + 1 ?></td>
                                    <td class="text-muted small"><?= date('d M Y, H:i', strtotime($log['waktu'])) ?>
                                    </td>

                                    <td class="fw-bold"><?= !empty($log['user']) ? $log['user'] : 'Admin Utama' ?></td>

                                    <td>
                                        <?php
                                        // Pastikan membaca field 'role' dan toleran terhadap huruf besar/kecil (SaaS Safe)
                                        $currentRole = isset($log['role']) ? strtolower(trim($log['role'])) : 'admin';

                                        $badgeClass = 'bg-secondary';
                                        if ($currentRole == 'admin') {
                                            $badgeClass = 'bg-danger';
                                        } elseif ($currentRole == 'kasir') {
                                            $badgeClass = 'bg-success';
                                        } elseif ($currentRole == 'manajer') {
                                            $badgeClass = 'bg-primary';
                                        } elseif ($currentRole == 'owner') {
                                            $badgeClass = 'bg-warning';
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?> text-white rounded-pill px-3">
                                            <?= ucfirst($currentRole) ?>
                                        </span>
                                    </td>
                                    <td><?= $log['aktivitas'] ?? 'Aktivitas sistem' ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Tidak ada log aktivitas yang ditemukan.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($pager_log)) : ?>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            Menampilkan log aktivitas
                        </div>
                        <div>
                            <?= $pager_log->links('log', 'default') ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="riwayat-shift" role="tabpanel" aria-labelledby="riwayat-shift-tab">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="m-0 font-weight-bold text-success">Riwayat Shift Transaksi Kasir</h6>
                        </div>
                        <div class="col-md-6 mt-2 mt-md-0 d-flex justify-content-md-end">
                            <div class="input-group input-group-sm" style="max-width: 300px;">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="autoSearchShift" class="form-control border-start-0 bg-light"
                                    placeholder="Ketik nama kasir atau shift...">
                                <button class="btn btn-danger border-start-0" type="button" id="clearShiftSearch"
                                    style="border-radius: 0 10px 10px 0; border: 1px solid #dc3545;">
                                    <i class="bi bi-x-lg text-white"></i>
                                </button>
                                <style>
                                #clearShiftSearch:hover {
                                    color: #dc3545 !important;
                                    /* Berubah merah saat hover */
                                    background-color: #cc1925 !important;
                                }
                                </style>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" width="100%" cellspacing="0"
                            id="tableRiwayatShift">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kasir</th>
                                    <th>Nama Shift</th>
                                    <th>Waktu Buka</th>
                                    <th>Waktu Tutup</th>
                                    <th>Modal Awal</th>
                                    <th>Uang Fisik Akhir</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($shift_transaksi)): ?>
                                <?php
                                    $page = request()->getGet('page_shift') ?? 1;
                                    $no = 1 + (10 * ($page - 1));
                                    foreach ($shift_transaksi as $row):
                                    ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td class="fw-bold"><?= esc($row['nama_user'] ?? $row['id_user']); ?></td>
                                    <td><?= esc($row['nama_shift']); ?></td>
                                    <td><?= date('d M Y H:i', strtotime($row['tanggal_buka'])); ?></td>
                                    <td>
                                        <?= $row['tanggal_tutup'] ? date('d M Y H:i', strtotime($row['tanggal_tutup'])) : '<span class="text-muted small italic">Sedang Berlangsung</span>'; ?>
                                    </td>
                                    <td>Rp <?= number_format($row['modal_awal'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?= !empty($row['uang_fisik_akhir']) ? 'Rp ' . number_format($row['uang_fisik_akhir'], 0, ',', '.') : '-'; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'Buka'): ?>
                                        <span class="badge bg-success rounded-pill px-3">Buka</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3">Tutup</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">Belum ada data transaksi shift.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($pager_shift)) : ?>
                    <div id="paginationAreaShift" class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">Menampilkan riwayat shift transaksi</div>
                        <div><?= $pager_shift->links('shift', 'default') ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script>
        $(document).ready(function() {
            $("#autoSearchShift").on("keyup", function() {
                var value = $(this).val().toLowerCase();

                // Filter baris tabel
                $("#tableRiwayatShift tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });

                // Sembunyikan pagination saat mencari agar tidak membingungkan
                if (value.length > 0) {
                    $("#paginationAreaShift").hide();
                } else {
                    $("#paginationAreaShift").show();
                }

                // Tampilkan pesan jika hasil kosong
                var visibleRows = $("#tableRiwayatShift tbody tr:visible").length;
                if (visibleRows === 0) {
                    if ($("#emptySearchMsg").length === 0) {
                        $("#tableRiwayatShift tbody").append(
                            '<tr id="emptySearchMsg"><td colspan="8" class="text-center py-4 text-muted">Data tidak ditemukan untuk pencarian tersebut.</td></tr>'
                        );
                    }
                } else {
                    $("#emptySearchMsg").remove();
                }
            });

            // Tombol X untuk reset pencarian
            $("#clearShiftSearch").click(function() {
                $("#autoSearchShift").val("").keyup();
            });
        });
        </script>

    </div>
</div>

<div class="modal fade" id="tambahUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> Tambah User Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/manajemen-user/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_user" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Role / Hak Akses</label>
                        <select class="form-select" name="role" required>
                            <option value="" selected disabled>Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                            <option value="kasir">Kasir</option>
                            <option value="manajer">Manajer</option>
                            <option value="barist">Barista</option>
                            <option value="chef">Chef</option>
                            <option value="waiter">Waiter</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">No. HP</label>
                        <input type="text" class="form-control" name="no_hp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Input Pin</label>
                        <input type="password" class="form-control" name="pin_user" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm" style="border-radius: 20px;">
            <div class="modal-header bg-warning text-dark border-0 p-4">
                <h5 class="modal-title fw-bold" id="editModalLabel">
                    <i class="fas fa-edit me-2"></i> Edit Data User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" action="" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body py-4 px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required
                            style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit_nama_user" name="nama_user" required
                            style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role / Hak Akses</label>
                        <select class="form-select" id="edit_role" name="role" required style="border-radius: 10px;">
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                            <option value="manajer">Manajer</option>
                            <option value="kasir">Kasir</option>
                            <option value="barist">Barista</option>
                            <option value="chef">Chef</option>
                            <option value="waiter">Waiter</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. HP</label>
                        <input type="text" class="form-control" id="edit_no_hp" name="no_hp"
                            style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="2"
                            style="border-radius: 10px;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <small class="text-muted">(Kosongkan jika tidak
                                diubah)</small></label>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan password baru"
                            style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Input Pin <small class="text-muted">(Kosongkan jika tidak
                                diubah)</small></label>
                        <input type="password" class="form-control" name="pin_user" placeholder="Masukkan pin baru"
                            style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-warning px-4 py-2 text-dark shadow-sm"
                        style="border-radius: 10px;">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm" style="border-radius: 16px;">
            <div class="modal-header bg-success text-white border-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Tambah Shift Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/shift-kasir/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Shift</label>
                        <input type="text" class="form-control" name="nama_shift" placeholder="Contoh: Shift Pagi"
                            required style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jam Mulai</label>
                        <input type="time" class="form-control" name="jam_mulai" required style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jam Selesai</label>
                        <input type="time" class="form-control" name="jam_selesai" required
                            style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-success px-4 text-white" style="border-radius: 10px;">Simpan
                        Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="hapusModal" tabindex="-1" aria-labelledby="hapusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm" style="border-radius: 20px;">
            <div class="modal-header bg-danger text-white border-0 p-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 px-4 text-center text-dark">
                <p class="mb-0">Apakah Anda yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.</p>
                <strong id="hapus_nama_user"></strong>
            </div>
            <div class="modal-footer border-0 p-4 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal"
                    style="border-radius: 10px;">Batal</button>
                <a id="btnHapus" href="" class="btn btn-danger px-4 py-2 text-white shadow-sm"
                    style="border-radius: 10px;">
                    <i class="fas fa-trash me-1"></i> Ya, Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($shift)): ?>
<?php foreach ($shift as $row): ?>
<div class="modal fade" id="editShiftModal<?= $row['shift_id'] ?>" tabindex="-1"
    aria-labelledby="editShiftModalLabel<?= $row['shift_id'] ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

            <div class="modal-header border-0 text-white" style="background-color: #0d8246; padding: 24px;">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-edit fa-lg"></i>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="editShiftModalLabel<?= $row['shift_id'] ?>">
                            Edit Shift Kasir</h5>
                        <p class="opacity-75 small mb-0">Ubah pengaturan shift dan waktu operasional</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form action="<?= site_url('admin/update-shift/' . $row['shift_id']) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Nama Shift</label>
                        <input type="text" class="form-control bg-light border-0 py-2 px-3" name="nama_shift"
                            value="<?= $row['nama_shift'] ?>" required style="border-radius: 10px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Jam Mulai</label>
                        <input type="time" class="form-control bg-light border-0 py-2 px-3" name="jam_mulai"
                            value="<?= date('H:i', strtotime($row['jam_mulai'])) ?>" required
                            style="border-radius: 10px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Jam Selesai</label>
                        <input type="time" class="form-control bg-light border-0 py-2 px-3" name="jam_selesai"
                            value="<?= date('H:i', strtotime($row['jam_selesai'])) ?>" required
                            style="border-radius: 10px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">Status</label>
                        <select class="form-select bg-light border-0 py-2 px-3" name="status_aktif" required
                            style="border-radius: 10px;">
                            <option value="1" <?= $row['status_aktif'] == 1 ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= $row['status_aktif'] == 0 ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal"
                        style="border-radius: 10px; font-weight: 500;">
                        Batal
                    </button>
                    <button type="submit" class="btn px-5 py-2 text-white shadow-sm"
                        style="background-color: #0d8246; border-radius: 10px; font-weight: 500;">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Menutup alert otomatis
    setTimeout(function() {
        var alerts = document.querySelectorAll('#alert-wrapper .alert');
        alerts.forEach(function(alertElement) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                try {
                    var bsAlert = new bootstrap.Alert(alertElement);
                    bsAlert.close();
                } catch (e) {
                    // Abaikan jika error
                }
            }
        });
    }, 3000);

    // 2. Mengambil parameter active_tab dari URL (Query String) atau Hash
    var urlParams = new URLSearchParams(window.location.search);
    var activeTabParam = urlParams.get('active_tab');
    var hash = '';

    if (activeTabParam === 'aktivitas') {
        hash = '#aktivitas';
    } else if (activeTabParam === 'riwayat-shift') {
        hash = '#riwayat-shift';
    } else if (activeTabParam === 'data-user') {
        hash = '#data-user';
    } else {
        // Fallback jika tidak ada, baca dari hash URL
        hash = window.location.hash;
    }

    // Default ke #data-user jika masih kosong
    if (!hash) {
        hash = '#data-user';
    }

    // Aktifkan tab berdasarkan hash
    var triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
    if (triggerEl) {
        var tab = new bootstrap.Tab(triggerEl);
        tab.show();
    }

    // 3. Simpan state tab pada URL saat user menekan tombol tab
    var triggerTabList = [].slice.call(document.querySelectorAll('#userTab button'));
    triggerTabList.forEach(function(triggerEl) {
        triggerEl.addEventListener('click', function(event) {
            var targetId = triggerEl.getAttribute('data-bs-target');

            // Mengubah URL Hash browser tanpa mereload
            history.pushState(null, null, targetId);

            // Ubah tab Bootstrap
            var tab = new bootstrap.Tab(triggerEl);
            tab.show();
        });
    });

    // 4. Modal Edit User
    var editModal = document.getElementById('editUserModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var username = button.getAttribute('data-username');
            var nama = button.getAttribute('data-nama');
            var role = button.getAttribute('data-role');
            var hp = button.getAttribute('data-hp');
            var alamat = button.getAttribute('data-alamat');

            var form = document.getElementById('formEdit');
            form.action = '<?= site_url('admin/manajemen-user/update/') ?>' + id;

            document.getElementById('edit_username').value = username;
            document.getElementById('edit_nama_user').value = nama;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_no_hp').value = hp;
            document.getElementById('edit_alamat').value = alamat;
        });
    }

    // 5. Modal Konfirmasi Hapus
    var hapusModal = document.getElementById('hapusModal');
    if (hapusModal) {
        hapusModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var namaUser = button.getAttribute('data-nama');
            var urlHapus = button.getAttribute('data-url');

            document.getElementById('hapus_nama_user').textContent = 'User: ' + namaUser;
            document.getElementById('btnHapus').href = urlHapus;
        });
    }
});
$(document).ready(function() {
    // Cek jika ada flashdata success
    <?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        title: 'Berhasil!',
        text: '<?= session()->getFlashdata('success'); ?>',
        icon: 'success',
        timer: 2500, // Hilang otomatis dalam 2.5 detik
        showConfirmButton: false,
        borderRadius: '20px',
        didOpen: () => {
            // Opsional: Kasih feedback suara atau efek lainnya
        }
    });
    <?php endif; ?>

    // Opsional: SweetAlert saat klik tombol simpan (Loading)
    $('form[action*="simpan-hak-akses"]').on('submit', function() {
        Swal.fire({
            title: 'Memproses...',
            text: 'Sedang mengatur hak akses pengguna',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    });
});
</script>

<?= $this->endSection() ?>