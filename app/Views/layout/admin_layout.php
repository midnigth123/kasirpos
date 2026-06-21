<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('icon_kasir.ico') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Kasirpos - KasirKita</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
    :root {
        --sidebar-width: 270px;
        --bg-body: #f4f6f9;
        --bg-sidebar: #ffffff;
        --text-main: #343a40;
        --text-muted: #6c757d;
        --border-color: #ebedf2;
        --hover-bg: #f0fdf4;
        --primary-color: #0f9d58;
        --transition-speed: 0.3s;
    }

    [data-bs-theme="dark"] {
        --bg-body: #1a1a27;
        --bg-sidebar: #1e1e2d;
        --text-main: #a2a3b7;
        --text-muted: #6d6d80;
        --border-color: #2b2b40;
        --hover-bg: rgba(25, 135, 84, 0.1);
        --primary-color: #2dba7b;
    }

    body {
        background-color: var(--bg-body);
        color: var(--text-main);
        font-family: 'Poppins', sans-serif;
        overflow-x: hidden;
    }

    #wrapper {
        display: flex;
        width: 100%;
        min-height: 100vh;
    }

    /* --- SIDEBAR CUSTOM --- */
    #sidebar {
        min-width: var(--sidebar-width);
        max-width: var(--sidebar-width);
        background: var(--bg-sidebar);
        border-right: 1px solid var(--border-color);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        z-index: 1000;
        box-shadow: 5px 0 15px rgba(0, 0, 0, 0.02);
    }

    #sidebar.active {
        margin-left: calc(-1 * var(--sidebar-width));
    }

    .sidebar-header {
        padding: 25px;
        border-bottom: 1px solid var(--border-color);
        background: linear-gradient(45deg, var(--bg-sidebar), var(--bg-body));
    }

    #sidebar ul.components {
        padding: 15px 10px;
        list-style: none;
    }

    /* --- MENU UTAMA --- */
    #sidebar ul li a {
        padding: 12px 15px;
        font-size: 0.88rem;
        display: flex;
        align-items: center;
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.2s;
        font-weight: 500;
        border-radius: 10px;
        margin-bottom: 4px;
    }

    #sidebar ul li a i:not(.fa-sm) {
        width: 35px;
        font-size: 1.1rem;
        color: var(--primary-color);
    }

    #sidebar ul li a:hover {
        background: var(--hover-bg);
        color: var(--primary-color);
        transform: translateX(5px);
    }

    #sidebar ul li a.active {
        background: var(--primary-color) !important;
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(25, 135, 84, 0.2);
    }

    #sidebar ul li a.active i {
        color: #fff !important;
    }

    /* --- SUBMENU STYLING (PRO) --- */
    #sidebar ul li ul.submenu {
        list-style: none;
        padding: 5px 0 10px 15px;
        margin-left: 22px;
        border-left: 2px solid var(--border-color);
    }

    #sidebar ul li ul.submenu li a {
        padding: 8px 15px !important;
        font-size: 0.82rem !important;
        background: transparent !important;
    }

    #sidebar ul li ul.submenu li a i {
        width: 25px !important;
        font-size: 0.8rem !important;
        opacity: 0.7;
    }

    /* Panah Dropdown Custom */
    .dropdown-toggle::after {
        display: inline-block;
        margin-left: auto;
        content: "\f107";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        border: none;
        transition: 0.3s;
    }

    .dropdown-toggle[aria-expanded="true"]::after {
        transform: rotate(180deg);
    }

    /* --- CONTENT AREA --- */
    #content {
        flex: 1;
        width: 100%;
        transition: all 0.3s;
    }

    /* 🚀 REVISI NAVBAR: GARIS REKAT INTEGRAL 100% MENEMPEL KE SIDEBAR */
    .navbar {
        background-color: var(--bg-sidebar) !important;
        border-bottom: 1px solid var(--border-color) !important;
        padding-top: 15px !important;
        padding-bottom: 15px !important;
        padding-left: 0 !important;
        padding-right: 25px !important;
        margin: 0 !important;
        width: 100% !important;
    }

    .navbar .container-fluid,
    .navbar .d-flex {
        padding-left: 25px !important;
    }

    .main-container {
        padding: 25px;
    }

    @media (max-width: 992px) {
        #sidebar {
            position: fixed;
            height: 100%;
            margin-left: calc(-1 * var(--sidebar-width));
        }

        #sidebar.active {
            margin-left: 0;
        }
    }
    </style>
</head>

<body>
    <div id="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex align-items-center justify-content-center"
                        style="width: 55px; height: 55px; overflow: hidden;">
                        <img src="<?= base_url('assets/img/icon_kasir.png') ?>" alt="Logo Toko"
                            style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                    <div>
                        <h6 class="fw-bold m-0 text-success">KasirKita</h6>
                        <?php $role = session()->get('role'); ?>
                        <small class="text-muted text-uppercase"
                            style="font-size: 0.65rem; letter-spacing: 1px; text-decoration: none !important; border-bottom: none !important;">
                            <?php
                            $displayRole = isset($role) ? $role : 'Admin';
                            echo esc(ucfirst((string)$displayRole));
                            ?>
                        </small>
                    </div>
                </div>
            </div>

            <ul class="list-unstyled components" id="sidebarMenu">

                <?php if ($role === 'admin' || cek_akses($role, 'dashboard')): ?>
                <li>
                    <a href="<?= site_url('admin/dashboard') ?>"
                        class="<?= (uri_string() == 'admin/dashboard') ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <?php endif; ?>

                <?php if ($role === 'admin' || cek_akses($role, 'transaksi')): ?>
                <li>
                    <a href="<?= site_url('kasir') ?>">
                        <i class="fas fa-cash-register"></i> Buka Kasir
                    </a>
                </li>
                <?php endif; ?>

                <?php if (
                    $role === 'admin'
                    || cek_akses($role, 'produk')
                    || cek_akses($role, 'resep')
                    || cek_akses($role, 'penerimaan')
                    || cek_akses($role, 'stokopname')
                    || cek_akses($role, 'waste')
                    || cek_akses($role, 'pengeluaran')
                ): ?>
                <li>
                    <a href="#produkSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle collapsed">
                        <i class="fas fa-box-open"></i> Produk & Stok
                    </a>
                    <ul class="collapse list-unstyled submenu" id="produkSubmenu" data-bs-parent="#sidebarMenu">
                        <?php if ($role === 'admin' || cek_akses($role, 'produk')): ?>
                        <li><a href="<?= site_url('admin/produk') ?>"><i class="fas fa-tags fa-sm"></i> Kelola
                                Produk</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'resep')): ?>
                        <li><a href="<?= site_url('admin/resep') ?>"><i class="fas fa-mortar-pestle fa-sm"></i> Kelola
                                Resep</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'penerimaan')): ?>
                        <li><a href="<?= site_url('admin/penerimaan') ?>"><i class="fas fa-truck-loading fa-sm"></i>
                                Penerimaan</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'pengeluaran')): ?>
                        <li><a href="<?= site_url('admin/pengeluaran') ?>"><i class="fas fa-money-bill-wave fa-sm"></i>
                                Pengeluaran</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'waste')): ?>
                        <li><a href="<?= site_url('admin/waste') ?>"><i class="fas fa-trash-alt fa-sm"></i> Waste
                                Management</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (
                    $role === 'admin'
                    || cek_akses($role, 'stokopname')
                    || cek_akses($role, 'mutasi_barang')
                    || cek_akses($role, 'kartu_stok')
                ): ?>
                <li>
                    <a href="#opnameSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle collapsed">
                        <i class="fas fa-tools"></i> Kelola Opname
                    </a>
                    <ul class="collapse list-unstyled submenu" id="opnameSubmenu" data-bs-parent="#sidebarMenu">
                        <?php if ($role === 'admin' || cek_akses($role, 'stokopname')): ?>
                        <li><a href="<?= site_url('admin/stokopname') ?>"><i class="fas fa-clipboard-check fa-sm"></i>
                                Stok Opname</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'mutasi_barang')): ?>
                        <li>
                            <a href="<?= base_url('admin/mutasi_barang') ?>">
                                <i class="fas fa-exchange-alt fa-sm"></i> Mutasi Barang
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'kartu_stok')): ?>
                        <li>
                            <a href="<?= base_url('admin/kartu_stok') ?>">
                                <i class="fas fa-address-card fa-sm"></i> Kartu Stok
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php
                if (
                    $role === 'admin' ||
                    cek_akses($role, 'laporan_transaksi') ||
                    cek_akses($role, 'closing') ||
                    cek_akses($role, 'laporan_laba_rugi') ||
                    cek_akses($role, 'laporan_maintenance') ||
                    cek_akses($role, 'laporan_waste') ||
                    cek_akses($role, 'laporan_opname') ||
                    cek_akses($role, 'laporan_gaji') ||
                    cek_akses($role, 'laporan_pengeluaran') ||
                    cek_akses($role, 'laporan_penerimaan') ||
                    cek_akses($role, 'laporan_cashflow') ||
                    cek_akses($role, 'absensi')
                ): ?>
                <li>
                    <a href="#laporanSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle collapsed">
                        <i class="fas fa-file-invoice-dollar"></i> Laporan
                    </a>
                    <ul class="collapse list-unstyled submenu" id="laporanSubmenu" data-bs-parent="#sidebarMenu">
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_transaksi')): ?>
                        <li><a href="<?= site_url('admin/transaksi') ?>"><i class="fas fa-receipt fa-sm"></i> Laporan
                                Transaksi</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'closing')): ?>
                        <li><a href="<?= site_url('admin/closing') ?>"><i class="fas fa-book fa-sm"></i> Laporan Closing
                                Kasir</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_laba_rugi')): ?>
                        <li><a href="<?= site_url('admin/laporan_laba_rugi') ?>"><i class="fas fa-chart-line fa-sm"></i>
                                Laporan Laba Rugi</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_maintenance')): ?>
                        <li><a href="<?= site_url('admin/laporan_maintenance') ?>"><i class="fas fa-wrench fa-sm"></i>
                                Laporan Maintenance</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_waste')): ?>
                        <li><a href="<?= site_url('admin/laporan_waste') ?>"><i class="fas fa-trash fa-sm"></i> Laporan
                                Waste</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_opname')): ?>
                        <li><a href="<?= site_url('admin/laporan_opname') ?>"><i class="fas fa-boxes fa-sm"></i> Laporan
                                Stok Opname</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_penerimaan')): ?>
                        <li><a href="<?= site_url('admin/laporan_penerimaan') ?>"><i class="fas fa-download fa-sm"></i>
                                Laporan Penerimaan</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_pengeluaran')): ?>
                        <li><a href="<?= site_url('admin/laporan_pengeluaran') ?>"><i class="fas fa-upload fa-sm"></i>
                                Laporan Pengeluaran</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_cashflow')): ?>
                        <li><a href="<?= site_url('admin/laporan_cashflow') ?>"><i class="fas fa-upload fa-sm"></i>
                                Laporan Cash Flow</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php
                if (
                    $role === 'admin' ||
                    cek_akses($role, 'pesanan_meja')
                ): ?>
                <li>
                    <a href="<?= site_url('admin/pesanan_meja') ?>">
                        <i class="fas fa-cutlery"></i> Pesanan
                    </a>
                </li>
                <?php endif; ?>

                <?php
                if (
                    $role === 'admin' ||
                    cek_akses($role, 'retur')
                ): ?>
                <li>
                    <a href="<?= site_url('admin/retur') ?>">
                        <i class="fas fa-undo-alt"></i>Retur Transaksi
                    </a>
                </li>
                <?php endif; ?>

                <?php
                if (
                    $role === 'admin' ||
                    cek_akses($role, 'master_table') ||
                    cek_akses($role, 'monitoring_table') ||
                    cek_akses($role, 'dashboard_table')
                ): ?>
                <li>
                    <a href="#mejaSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle collapsed">
                        <i class="fas fa-table"></i> Manajemen Table
                    </a>
                    <ul class="collapse list-unstyled submenu" id="mejaSubmenu" data-bs-parent="#sidebarMenu">
                        <?php if ($role === 'admin' || cek_akses($role, 'master_table')): ?>
                        <li><a href="<?= site_url('admin/master_table') ?>"><i class="fas fa-table"></i> Master Table
                            </a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'monitoring_table')): ?>
                        <li><a href="<?= site_url('admin/monitoring_table') ?>"><i class="fas fa-table fa-sm"></i>
                                Penggunaan Table</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'dashboard_table')): ?>
                        <li><a href="<?= site_url('admin/dashboard_table') ?>"><i class="fas fa-display fa-sm"></i>
                                Dashboard Table</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <?php
                if (
                    $role === 'admin' ||
                    cek_akses($role, 'supplier')
                ): ?>
                <li>
                    <a href="<?= site_url('admin/supplier') ?>">
                        <i class="fas fa-truck"></i> Supplier
                    </a>
                </li>
                <?php endif; ?>

                <?php if (
                    $role === 'admin'
                    || cek_akses($role, 'aset')
                    || cek_akses($role, 'aset_maintenance')
                ): ?>
                <li>
                    <a href="#asetSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle collapsed">
                        <i class="fas fa-tools"></i> Manajemen Aset
                    </a>
                    <ul class="collapse list-unstyled submenu" id="asetSubmenu" data-bs-parent="#sidebarMenu">
                        <?php if ($role === 'admin' || cek_akses($role, 'aset')): ?>
                        <li><a href="<?= site_url('admin/aset') ?>"><i class="fas fa-list-ul fa-sm"></i> Daftar Aset</a>
                        </li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'aset_maintenance')): ?>
                        <li><a href="<?= site_url('admin/aset_maintenance') ?>"><i class="fas fa-wrench fa-sm"></i>
                                Maintenance Aset</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <div class="px-4 mt-4 mb-2 small text-muted fw-bold" style="font-size: 0.7rem; opacity: 0.5;">LAINNYA
                </div>

                <?php if (
                    $role === 'admin'
                    || cek_akses($role, 'absensi')
                ): ?>
                <li><a href="<?= site_url('admin/absensi') ?>"><i class="fas fa-clock"></i> Absensi Pegawai</a></li>
                <?php endif; ?>

                <?php if (
                    $role === 'admin'
                    || cek_akses($role, 'master_gaji')
                    || cek_akses($role, 'laporan_gaji')
                    || cek_akses($role, 'pengajuan_kasbon')
                    || cek_akses($role, 'kasbon')
                ): ?>
                <li>
                    <a href="#gajiSubmenu" data-bs-toggle="collapse" aria-expanded="false"
                        class="dropdown-toggle collapsed">
                        <i class="fas fa-money-bill-wave"></i> Gaji Karyawan
                    </a>
                    <ul class="collapse list-unstyled submenu" id="gajiSubmenu" data-bs-parent="#sidebarMenu">
                        <?php if ($role === 'admin' || cek_akses($role, 'master_gaji')): ?>
                        <li><a href="<?= site_url('admin/master_gaji') ?>"><i class="fas fa-wallet"></i> Master Gaji
                                Pegawai</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'master_lembur')): ?>
                        <li><a href="<?= site_url('admin/master_lembur') ?>"><i class="fas fa-wallet"></i> Lembur
                                Pegawai</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_absensi')): ?>
                        <li><a href="<?= site_url('admin/laporan_absensi') ?>"><i class="fas fa-user-check fa-sm"></i>
                                Laporan
                                Absensi</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'laporan_gaji')): ?>
                        <li><a href="<?= site_url('admin/laporan_gaji') ?>"><i class="fas fa-wallet fa-sm"></i> Laporan
                                Gaji</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'pengajuan_kasbon')): ?>
                        <li><a href="<?= site_url('admin/pengajuan_kasbon') ?>"><i class="fas fa-money-bill fa-sm"></i>
                                Pengajuan Kasbon</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'kasbon')): ?>
                        <li><a href="<?= site_url('admin/kasbon') ?>"><i class="fas fa-money-bill fa-sm"></i>
                                Kasbon Karyawan</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'pengajuan_izin')): ?>
                        <li><a href="<?= site_url('admin/pengajuan_izin') ?>"><i class="fas fa-money-bill fa-sm"></i>
                                Pengajuan Izin/Cuti/Sakit</a></li>
                        <?php endif; ?>
                        <?php if ($role === 'admin' || cek_akses($role, 'izin_approval')): ?>
                        <li><a href="<?= site_url('admin/izin_approval') ?>"><i class="fas fa-money-bill fa-sm"></i>
                                Approval Izin/Cuti/Sakit</a></li>
                        <?php endif; ?>

                    </ul>
                </li>
                <?php endif; ?>

                <?php if ($role === 'admin' || cek_akses($role, 'member')): ?>
                <li><a href="<?= site_url('admin/member') ?>"><i class="fas fa-users"></i> Member</a></li>
                <?php endif; ?>

                <?php if ($role === 'admin' || cek_akses($role, 'promo')): ?>
                <li><a href="<?= site_url('admin/promo') ?>"><i class="fas fa-percentage"></i> Promo</a></li>
                <?php endif; ?>

                <?php if ($role === 'admin' || cek_akses($role, 'user_client')): ?>
                <li><a href="<?= site_url('admin/user_client') ?>"><i class="fas fa-user"></i> User</a></li>
                <?php endif; ?>

                <?php if ($role === 'admin' || cek_akses($role, 'pengaturan')): ?>
                <li><a href="<?= site_url('admin/pengaturan') ?>"><i class="fas fa-cog"></i> Pengaturan</a></li>
                <?php endif; ?>

                <?php if ($role === 'admin'): ?>
                <div class="px-4 mt-4 mb-2 small text-muted fw-bold" style="font-size: 0.7rem; opacity: 0.5;">
                    ADMINISTRATOR</div>
                <li><a href="<?= site_url('admin/dashboard_saas') ?>"><i class="fas fa-dashboard"></i> Dashboard
                        Tenant</a></li>
                <li><a href="<?= site_url('admin/manajemen-user') ?>"><i class="fas fa-users-cog"></i> User
                        Manajemen</a></li>
                <li><a href="<?= site_url('admin/perpanjang_langganan') ?>"><i class="fas fa-cog"></i> Manajemen
                        Tenant</a></li>
                <li><a href="<?= site_url('admin/broadcast') ?>"><i class="fa-solid fa-bullhorn"></i>Broadcast</a></li>
                <?php endif; ?>

            </ul>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand shadow-sm">
                <div class="container-fluid">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" id="sidebarCollapse" class="btn btn-light rounded-circle shadow-sm">
                            <i class="bi bi-list text-success"></i>
                        </button>
                        <button class="btn btn-light rounded-circle shadow-sm border-0" id="darkModeToggle">
                            <i class="bi bi-moon-stars-fill"></i>
                        </button>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end d-none d-lg-block">
                            <p class="mb-0 fw-bold small text-main text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="bi bi-person-badge me-1"></i>
                                <?php $session = session();
                                $namaUser = $session->get('nama_user') ?? 'Guest';
                                echo esc($namaUser); ?>
                            </p>
                            <small id="clockAdmin" class="text-success fw-medium" style="font-size: 0.75rem;"></small>
                        </div>
                        <div class="vr d-none d-lg-block opacity-25" style="height: 30px;"></div>
                        <a href="javascript:void(0)" id="btnLogout"
                            class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">Keluar</a>
                    </div>
                </div>
            </nav>

            <div class="main-container">
                <div class="bg-white rounded-4 shadow-sm p-4 border" style="min-height: 80vh;">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {

        // ========================================================================
        // 🔒 SUNTIKAN SAKTI: DETEKTOR AUTO-LOCK MAINTENANCE (JSON RADAR PREMIUM)
        // ========================================================================
        setInterval(function() {
            // Membaca root path index.php murni agar bebas hambatan dari rute Filter Auth login
            var rootPath = window.location.pathname.split('index.php')[0] + 'index.php';
            var URL_Final = window.location.origin + rootPath + '?check_status_maintenance=1&t=' +
                new Date().getTime();

            fetch(URL_Final, {
                    cache: 'no-store'
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    // Jika gembok digital di pusat aktif (true), langsung paksa reload dashboard ini!
                    if (data.maintenance === true) {
                        window.location.reload();
                    }
                })
                .catch(function(error) {
                    console.log("Memantau radar gembok KasirKita...");
                });
        }, 4000); // ⏱️ Scan dipercepat ke 4 detik sekali agar sinkron dengan halaman kasir

        // ========================================================================
        // Sidebar Toggle
        // ========================================================================
        document.getElementById('sidebarCollapse').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // ========================================================================
        // 🕒 Realtime Clock
        // ========================================================================
        function updateClock() {
            const d = new Date();
            const clockEl = document.getElementById('clockAdmin');
            if (clockEl) {
                const dateStr = d.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                const timeStr = d.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                clockEl.innerHTML =
                    `<i class="bi bi-calendar3 me-1"></i> ${dateStr} • <i class="bi bi-clock me-1"></i> ${timeStr}`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ========================================================================
        // 🌓 Dark/Light Mode
        // ========================================================================
        const themeToggle = document.getElementById('darkModeToggle');
        const htmlElement = document.documentElement;

        function setTheme(theme) {
            htmlElement.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
            const icon = themeToggle.querySelector('i');
            icon.className = theme === 'dark' ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
        }
        setTheme(localStorage.getItem('theme') || 'light');
        themeToggle.addEventListener('click', () => {
            const current = htmlElement.getAttribute('data-bs-theme');
            setTheme(current === 'light' ? 'dark' : 'light');
        });

        // ========================================================================
        // 🚪 Logout Confirmation
        // ========================================================================
        document.getElementById('btnLogout').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Apakah Anda yakin ingin mengakhiri sesi?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                borderRadius: '15px'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = "<?= base_url('logout') ?>";
            });
        });
    });

    // ========================================================================
    // 🛡️ JEMBATAN PENYELAMAT GAIB: TERJEMAHKAN FUNGSI MODAL J JQUERY JADUL KE BS5
    // ========================================================================
    // Ditaruh di luar DOMContentLoaded agar langsung aktif mencegat crash tombol aksi
    if (typeof jQuery !== 'undefined') {
        jQuery.fn.modal = function(action) {
            return this.each(function() {
                var element = this;
                // Deteksi atau buat instance modal Bootstrap 5 baru secara dynamic
                var modalInstance = bootstrap.Modal.getInstance(element);
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(element);
                }

                // Eksekusi aksi show / hide tanpa merusak jalannya JavaScript layout
                if (action === 'show') {
                    modalInstance.show();
                } else if (action === 'hide') {
                    modalInstance.hide();
                }
            });
        };
    }
    <?php if (session()->get('role') !== 'admin_pusat') : ?>
    setInterval(function() {
        var URL_Pantau = "<?= site_url('cek_lisensi_toko') ?>?t=" + new Date().getTime();

        fetch(URL_Pantau, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                }
            })
            .then(res => res.json())
            .then(data => {
                // JIKA MASA BERLAKU HABIS ATAU DINONAKTIFKAN, LANGSUNG KUNCI DETIK ITU JUGA
                if (data.status_aktif === 'N' || data.apakah_expired === true) {
                    window.location.href =
                        "<?= site_url('admin/tagihan') ?>"; // Sesuaikan ke URL rute halaman merah Bos
                }
            })
            .catch(err => console.log("Menyelaraskan waktu lisensi..."));
    }, 7000); // Mengecek secara gaib setiap 7 detik
    <?php endif; ?>
    </script>
</body>

</html>