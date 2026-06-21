<?php

/**
 * @var array $data_resep
 * @var array $nama_user
 * @var string $jumlah_antrean
 * @var array $list_id_resep
 * 
 * */
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('icon_kasir.ico') ?>">
    <title>KasirKita </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <style>
    /* ============================================================
       1. LAYOUT & ROOT
       ============================================================ */
    :root {
        --primary-color: #198754;
        --secondary-bg: #f8f9fa;
        --card-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        --card-shadow-hover: 0 1rem 2rem rgba(0, 0, 0, 0.12);
        --radius-lg: 1.25rem;
        --radius-md: 12px;
    }

    .category-btn {
        border-radius: 10px;
        margin-right: 5px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }

    .cart-section {
        border-left: 1px solid #dee2e6;
        height: 100vh;
        position: sticky;
        top: 0;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        z-index: 1020;
    }

    /* ============================================================
       2. PRODUCT CARD
       ============================================================ */
    .product-card {
        border: none;
        border-radius: var(--radius-lg);
        background: #ffffff;
        box-shadow: var(--card-shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--card-shadow-hover);
    }

    /* IMG OPTIMIZATION */
    .img-wrapper {
        position: relative;
        height: 160px;
        background: var(--secondary-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-bottom: 1px solid #f1f1f1;
    }

    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* Anti-Blur & Sharp Rendering */
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
        backface-visibility: hidden;
        transform: translateZ(0);
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .product-card:hover .img-wrapper img {
        transform: scale(1.15);
    }

    /* PRICE BADGE GLASSMORPHISM */
    .price-badge-modern {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: var(--radius-md);
        font-weight: 800;
        color: var(--primary-color);
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 2;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* ============================================================
       3. FORM & INPUTS
       ============================================================ */
    .input-qty-modern {
        border-radius: 10px 0 0 10px !important;
        border: 1px solid #e9ecef;
        font-weight: 700;
        max-width: 65px;
        text-align: center;
    }

    .btn-add-modern {
        border-radius: 0 10px 10px 0 !important;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding-left: 15px;
        padding-right: 15px;
    }

    .custom-textarea {
        border-radius: var(--radius-md);
        background-color: var(--secondary-bg);
        border: 1px solid transparent;
        font-size: 0.8rem;
        transition: all 0.2s ease;
        resize: none;
    }

    .custom-textarea:focus {
        background-color: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15);
        outline: none;
    }

    /* ============================================================
       4. MODAL & SWEETALERT CUSTOM
       ============================================================ */
    .modal-content {
        border-radius: 1.5rem;
        border: none;
        max-height: 95vh;
    }

    .swal2-popup-custom {
        width: 500px !important;
        border-radius: var(--radius-lg) !important;
        padding: 1.5rem !important;
    }

    .responsive-qris-img {
        width: 100% !important;
        max-width: 320px !important;
        /* Standar ukuran QRIS agar mudah scan */
        height: auto !important;
        margin: 1.5rem auto !important;
        border: 1px solid #eee;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        display: block;
    }

    /* ============================================================
       5. KERANJANG ITEM & CLOCK
       ============================================================ */
    .cart-item {
        transition: background 0.2s ease;
        border-radius: 8px;
    }

    .cart-item:hover {
        background-color: var(--secondary-bg);
    }

    .cart-item small {
        line-height: 1.3;
        display: block;
    }

    #clock {
        font-variant-numeric: tabular-nums;
        letter-spacing: 1px;
    }

    #modalTable .modal-body {
        max-height: 70vh;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 20px;
    }

    #modalTable .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    #modalTable .modal-body::-webkit-scrollbar-thumb {
        background-color: #ddd;
        border-radius: 10px;
    }
    .footer-struk {
        white-space: pre-line; 
        display: block;
        margin-top: 5px;
    }

    /* ============================================================
       6. RESPONSIVE TABLET (MAX 1024px)
       ============================================================ */
    @media (max-width: 1024px) {
        .cart-section {
            width: 300px !important;
            flex: 0 0 300px !important;
        }

        .img-wrapper {
            height: 120px;
        }

        .price-badge-modern {
            font-size: 0.75rem;
            padding: 4px 10px;
        }

        .fw-bold.text-dark {
            font-size: 0.85rem !important;
        }

        h4.fw-bold.text-success {
            font-size: 1.2rem !important;
        }

        .btn-add-modern {
            font-size: 0.7rem !important;
            padding-left: 8px;
            padding-right: 8px;
        }

        .swal2-popup-custom {
            width: 90% !important;
        }
    }

    .btn-mitra-ol {
        border-radius: 12px !important;
        border-color: #e2e8f0 !important;
        transition: all 0.2s ease-in-out !important;
    }

    .img-logo-mitra {
        max-width: 100%;
        display: block;
    }

    .text-mitra {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        margin-top: 2px;
    }

    /* --- WARNA AKTIF SAAT KASIR KLIK --- */
    input[name="mitra_online"]#mitra_gojek:checked+.btn-gojek {
        border-color: #00AA13 !important;
        background-color: #f0fdf4 !important;
        box-shadow: 0 0 0 3px rgba(0, 170, 19, 0.15) !important;
    }

    input[name="mitra_online"]#mitra_gojek:checked+.btn-gojek .text-mitra {
        color: #00AA13;
    }

    input[name="mitra_online"]#mitra_gojek:checked+.btn-gojek svg text {
        fill: #00AA13 !important;
    }

    input[name="mitra_online"]#mitra_grab:checked+.btn-grab {
        border-color: #00B14F !important;
        background-color: #f0fdf4 !important;
        box-shadow: 0 0 0 3px rgba(0, 177, 79, 0.15) !important;
    }

    input[name="mitra_online"]#mitra_grab:checked+.btn-grab .text-mitra {
        color: #00B14F;
    }

    input[name="mitra_online"]#mitra_shopee:checked+.btn-shopee {
        border-color: #EE4D2D !important;
        background-color: #fff7ed !important;
        box-shadow: 0 0 0 3px rgba(238, 77, 45, 0.15) !important;
    }

    input[name="mitra_online"]#mitra_shopee:checked+.btn-shopee .text-mitra {
        color: #EE4D2D;
    }
    </style>
    </style>

</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 py-3">
                <div id="broadcast-container" class="mb-3">
                    <?php if (!empty($broadcast_pusat)) : ?>
                    <div id="box-broadcast-pusat" class="card bg-danger text-white border-0 shadow-sm"
                        style="border-radius: 8px; overflow: hidden;">
                        <div class="card-body p-0 d-flex align-items-center">
                            <div class="bg-dark text-white p-2 px-3 fw-bold text-uppercase d-flex align-items-center gap-2"
                                style="flex-shrink: 0; font-size: 0.85rem;">
                                <i
                                    class="fas fa-bullhorn animate__animated animate__flash animate__infinite text-warning"></i>
                                <span>INFO PUSAT:</span>
                            </div>
                            <div class="flex-grow-1 px-3 fs-6 fw-medium overflow-hidden">
                                <marquee behavior="scroll" direction="left" scrollamount="6">
                                    <strong>[<?= esc($broadcast_pusat['judul']) ?>]</strong> &mdash;
                                    <?= esc($broadcast_pusat['isi_pesan']) ?>
                                </marquee>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-inline-flex align-items-center p-0 m-0">
                        <img src="<?= base_url('assets/img/icon_kasir.png') ?>" alt="Logo Toko"
                            style="height: 55px; width: auto; object-fit: contain; display: block; margin: 0; padding: 0;">

                        <h4 class="fw-bold text-success m-0 p-0"
                            style="font-size: 25px; line-height: 55px; display: inline-block;">
                            KasirKita
                        </h4>
                    </div>

                    <div class="text-end">
                        <div class="d-flex align-items-center justify-content-end mb-1">
                            <div class="me-3 text-end d-none d-sm-block">
                                <div class="fw-bold text-dark" style="line-height: 1.2;">
                                    <i class="fas fa-user-circle me-1 text-secondary"></i>
                                    <?= esc(session()->get('nama_user')) ?>
                                </div>
                                <div id="clock" class="text-muted" style="font-size: 0.75rem;"></div>
                            </div>
                            <div class="d-flex gap-1 align-items-center">
                                <a href="<?= site_url('admin/dashboard') ?>"
                                    class="btn btn-outline-success btn-sm rounded-0 px-2 fw-bold shadow-sm">
                                    <i class="fas fa-home"></i>
                                    <span class="d-none d-xl-inline ms-1">Home</span>
                                </a>

                                <button class="btn btn-outline-primary btn-sm rounded-0 px-2 fw-bold shadow-sm"
                                    data-bs-toggle="modal" data-bs-target="#cetakClosingModal">
                                    <i class="fas fa-print"></i>
                                    <span class="d-none d-xl-inline ms-1">Closing</span>
                                </button>

                                <button class="btn btn-outline-primary btn-sm rounded-0 px-2 fw-bold shadow-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalAbsenKru">
                                    <i class="fas fa-user-clock"></i>
                                    <span class="d-none d-xl-inline ms-1">Absen</span>
                                </button>

                                <button
                                    class="btn btn-outline-warning btn-sm rounded-0 px-2 fw-bold shadow-sm position-relative"
                                    data-bs-toggle="modal" data-bs-target="#modalAntrean">
                                    <i class="fa fa-bell"></i>

                                    <span id="badge-antrean"
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-0 bg-danger"
                                        style="font-size: 10px; padding: 0.35em 0.65em; display: none;">0</span>

                                    <span class="d-none d-xl-inline ms-1">Antrean</span>
                                </button>

                                <button class="btn btn-outline-primary btn-sm rounded-0 px-2 fw-bold shadow-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalTable">
                                    <i class="fas fa-table"></i>
                                    <span class="d-none d-xl-inline ms-1">Table</span>
                                </button>

                                <input type="hidden" id="selected-nomor-meja" name="nomor_meja">

                                <button class="btn btn-outline-danger btn-sm rounded-0 px-2 fw-bold shadow-sm"
                                    data-bs-toggle="modal" data-bs-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span class="d-none d-xl-inline ms-1">Keluar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>



                <input type="text" id="search-produk" class="form-control rounded-pill mb-3"
                    placeholder="Cari produk...">
                <div class="mb-4">
                    <button class="btn btn-dark category-btn" onclick="filterKategori('semua')">Semua</button>
                    <button class="btn btn-outline-dark category-btn"
                        onclick="filterKategori('minuman')">Minuman</button>
                    <button class="btn btn-outline-dark category-btn"
                        onclick="filterKategori('makanan')">Makanan</button>
                    <button class="btn btn-outline-dark category-btn" onclick="filterKategori('snack')">Snack</button>
                    <button class="btn btn-outline-dark category-btn" onclick="filterKategori('promo')">Promo</button>

                    <script>
                    let kategoriAktif = 'semua';

                    function filterKategori(kategori) {
                        kategoriAktif = kategori;
                        jalankanFilter();

                        const buttons = document.querySelectorAll('.category-btn');
                        buttons.forEach(btn => {
                            if (btn.getAttribute('onclick').includes(`'${kategori}'`)) {
                                btn.classList.replace('btn-outline-dark', 'btn-dark');
                            } else {
                                btn.classList.replace('btn-dark', 'btn-outline-dark');
                            }
                        });
                    }

                    function jalankanFilter() {
                        const keyword = document.getElementById('search-produk').value.toLowerCase();
                        const items = document.querySelectorAll('.product-item');

                        items.forEach(item => {
                            const namaProduk = item.querySelector('h6').innerText.toLowerCase();
                            const katProduk = item.getAttribute('data-kategori');

                            const cocokKategori = (kategoriAktif === 'semua' || katProduk === kategoriAktif);
                            const cocokNama = namaProduk.includes(keyword);

                            if (cocokKategori && cocokNama) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        const searchInput = document.getElementById('search-produk');
                        if (searchInput) {
                            searchInput.addEventListener('keyup', jalankanFilter);
                        }
                    });

                    function updateClock() {
                        const now = new Date();
                        const options = {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        };

                        document.getElementById('clock').innerText = now.toLocaleDateString('id-ID', options);
                    }

                    setInterval(updateClock, 1000);
                    updateClock();

                    // JS Untuk Broadcast
                    document.addEventListener("DOMContentLoaded", function() {

                        setInterval(function() {
                            fetch("<?= site_url('admin/cek_broadcast_realtime') ?>?_=" + new Date()
                                    .getTime())
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Jalur radar AJAX terganggu.');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    const container = document.getElementById(
                                        'broadcast-container');
                                    if (!container) return;

                                    let boxBroadcast = document.getElementById(
                                        'box-broadcast-pusat');

                                    // 🟢 KONDISI A: JIKA SIARAN AKTIF DI PUSAT
                                    if (data.aktif === true) {

                                        if (!boxBroadcast) {
                                            // 🎯 BERSIH: Di dalam template string ini juga teks tanggalnya sudah dipangkas
                                            container.innerHTML = `
                            <div id="box-broadcast-pusat" class="card bg-danger text-white border-0 shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-dark text-white p-2 px-3 fw-bold text-uppercase d-flex align-items-center gap-2" style="flex-shrink: 0; font-size: 0.85rem;">
                                        <i class="fas fa-bullhorn animate__animated animate__flash animate__infinite text-warning"></i>
                                        <span>INFO PUSAT:</span>
                                    </div>
                                    <div class="flex-grow-1 px-3 fs-6 fw-medium overflow-hidden">
                                        <marquee behavior="scroll" direction="left" scrollamount="6">
                                            <strong>[${data.judul}]</strong> &mdash; ${data.pesan}
                                        </marquee>
                                    </div>
                                </div>
                            </div>
                        `;
                                        } else {
                                            let marqueeElement = boxBroadcast.querySelector(
                                                'marquee');
                                            let kontenTerbaru =
                                                `<strong>[${data.judul}]</strong> &mdash; ${data.pesan}`;

                                            if (marqueeElement && marqueeElement.innerHTML !==
                                                kontenTerbaru) {
                                                marqueeElement.innerHTML = kontenTerbaru;
                                            }
                                        }

                                    }
                                    // 🔴 KONDISI B: JIKA SIARAN DIMATIKAN ADMIN
                                    else {
                                        if (boxBroadcast) {
                                            boxBroadcast.style.transition = "all 0.4s ease";
                                            boxBroadcast.style.opacity = "0";
                                            boxBroadcast.style.transform = "translateY(-5px)";

                                            setTimeout(() => {
                                                boxBroadcast.remove();
                                            }, 400);
                                        }
                                    }
                                })
                                .catch(error => console.log("Radar sync standby..."));
                        }, 5000);
                    });
                    </script>
                </div>

                <div class="row g-4">
                    <?php if (isset($produk) && !empty($produk)): ?>
                    <?php foreach ($produk as $p): ?>
                    <?php
                            $bolehKlik = ($p['jenis_stok'] == 'Basah' || $p['stok'] > 0);
                            $namaFileGambar = !empty($p['img']) ? $p['img'] : '';
                            $pathLokal = FCPATH . 'uploads/produk/' . $namaFileGambar;
                            ?>
                    <div class="col-md-4 col-6 product-item" data-kategori="<?= strtolower($p['kategori'] ?? '') ?>">
                        <div class="card h-100 product-card">

                            <div class="img-wrapper" <?php if ($bolehKlik): ?>
                                onclick="addToCart(<?= $p['produk_id'] ?>, '<?= $p['nama_produk'] ?>', <?= $p['harga_jual'] ?>, '<?= strtolower($p['kategori'] ?? '') ?>', <?= $p['stok'] ?>, '<?= $p['jenis_stok'] ?>')"
                                style="cursor: pointer;" <?php endif; ?>>

                                <div class="price-badge-modern">
                                    Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?>
                                </div>

                                <?php if (!$bolehKlik): ?>
                                <div class="sold-out-overlay">
                                    <span class="badge bg-danger rounded-pill px-3 py-2 shadow">HABIS</span>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($namaFileGambar) && file_exists($pathLokal)): ?>
                                <img src="<?= base_url('uploads/produk/' . $namaFileGambar) ?>"
                                    class="<?= (!$bolehKlik) ? 'filter-grayscale' : '' ?>"
                                    alt="<?= $p['nama_produk'] ?>">
                                <?php else: ?>
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-3x opacity-25"></i>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-body p-3 d-flex flex-column">
                                <div class="mb-2">
                                    <h6 class="fw-bold text-dark mb-1 text-truncate"><?= $p['nama_produk'] ?></h6>
                                    <small class="text-muted d-block" style="font-size: 11px;">
                                        <i class="fas fa-box-open me-1"></i> Stok: <b><?= $p['stok'] ?></b>
                                    </small>
                                </div>

                                <?php if (isset($p['kategori']) && strtolower($p['kategori']) === 'minuman'): ?>
                                <div class="mb-2">
                                    <div class="btn-group btn-group-sm w-100 btn-group-suhu" role="group">
                                        <input type="radio" class="btn-check" name="suhu_<?= $p['produk_id'] ?>"
                                            id="hot_<?= $p['produk_id'] ?>" value="Hot" checked>
                                        <label class="btn btn-outline-danger"
                                            for="hot_<?= $p['produk_id'] ?>">HOT</label>
                                        <input type="radio" class="btn-check" name="suhu_<?= $p['produk_id'] ?>"
                                            id="ice_<?= $p['produk_id'] ?>" value="Ice">
                                        <label class="btn btn-outline-primary"
                                            for="ice_<?= $p['produk_id'] ?>">ICE</label>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <textarea class="form-control custom-textarea" id="catatan_<?= $p['produk_id'] ?>"
                                        placeholder="Tambahkan request..." rows="1" style="resize: none;"></textarea>
                                </div>

                                <div class="mt-auto">
                                    <div class="d-flex align-items-stretch">
                                        <input type="number" id="qty_input_<?= $p['produk_id'] ?>"
                                            class="form-control text-center input-qty-modern shadow-none" value="1"
                                            min="1">

                                        <button class="btn btn-success btn-add-modern flex-grow-1 shadow-sm"
                                            <?= $bolehKlik ? '' : 'disabled' ?>
                                            onclick="addWithQty(<?= $p['produk_id'] ?>, '<?= $p['nama_produk'] ?>', <?= $p['harga_jual'] ?>, '<?= strtolower($p['kategori'] ?? '') ?>', <?= $p['stok'] ?>, '<?= $p['jenis_stok'] ?>')">
                                            <i class="fas fa-plus-circle me-1"></i>
                                            <?= $bolehKlik ? 'Tambah' : 'Habis' ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="col-12 text-center text-muted py-5 animate__animated animate__fadeIn">
                        <i class="fas fa-folder-open fa-4x mb-3 opacity-25"></i>
                        <h5 class="fw-bold">Menu Kosong</h5>
                        <p>Sepertinya tidak ada produk di kategori ini.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <?php
                $db = \Config\Database::connect();
                $cek_opname = $db->table('sistem_kontrol')
                    ->where('nama_fitur', 'stok_opname_hold')
                    ->get()->getRow();

                // Atur class awal berdasarkan database saat halaman di-load/refresh
                // Jika status 1 maka langsung 'd-flex' (tampil), jika 0 maka 'd-none' (sembunyi)
                $display_class = ($cek_opname && $cek_opname->status == 1) ? 'd-flex' : 'd-none';
                ?>

                <!-- JEDOR! Tambahkan id="modalHoldKasir" dan class dinamis dari PHP -->
                <div id="modalHoldKasir"
                    class="position-fixed top-0 start-0 w-100 h-100 <?= $display_class ?> align-items-center justify-content-center"
                    style="background: rgba(255,255,255,0.9); z-index: 9999; backdrop-filter: blur(5px);">
                    <div class="text-center p-5 shadow-lg rounded-5 bg-white border border-danger"
                        style="max-width: 500px;">
                        <div class="mb-4">
                            <i
                                class="fas fa-hand-paper text-danger fa-5x animate__animated animate__pulse animate__infinite"></i>
                        </div>
                        <h2 class="fw-bold text-dark">KASIR DI-HOLD</h2>
                        <p class="text-muted">Mohon maaf, saat ini sedang dilakukan <strong>Stok Opname</strong> oleh
                            tim admin. Transaksi tidak dapat dilakukan sampai proses selesai.</p>
                        <div class="spinner-border text-danger mt-3" role="status"></div>
                        <div class="mt-4">
                            <small class="text-secondary">Silakan hubungi admin untuk info lebih lanjut.</small>
                        </div>
                    </div>
                </div>

                <script>
                // JS Untuk Hold Kasir saat Opname - Sinkronisasi Real-time
                $(document).ready(function() {
                    function cekOtomatisOpname() {
                        $.ajax({
                            url: '<?= base_url('admin/cek_status_hold') ?>',
                            method: 'GET',
                            dataType: 'json', // Wajib ada
                            success: function(data) {
                                console.log("Respon server:", data); // Intip di F12
                                const modalHold = $('#modalHoldKasir');

                                // JEDOR! Gunakan pembanding yang pasti
                                if (data.is_hold === true) {
                                    modalHold.removeClass('d-none').addClass('d-flex');
                                } else {
                                    modalHold.removeClass('d-flex').addClass('d-none');
                                }
                            }
                        });
                    }

                    // Cek setiap 5 detik
                    setInterval(cekOtomatisOpname, 5000);
                });
                // ==========================================
                // 1. DEKLARASI VARIABEL & DATA PHP
                // ==========================================
                // Deklarasi cart harus paling atas supaya tidak "is not defined"
                if (typeof cart === 'undefined') {
                    var cart = [];
                } else {
                    cart = [];
                }
                var waktuOrder = null;

                // Tangkap data resep & member dari PHP
                const dataResepFull = <?= json_encode($data_resep) ?>;
                const daftarIdResep = <?= json_encode($list_id_resep) ?>;

                // ==========================================
                // 2. SINKRONISASI DATA DATABASE (Sangat Penting!)
                // Agar saat refresh atau tarik meja, keranjang tidak kosong
                // ==========================================
                <?php if (!empty($cart_database)): ?>
                <?php
                        $seen = [];
                        foreach ($cart_database as $row):
                            if (in_array($row['id_temp'], $seen)) continue;
                            $seen[] = $row['id_temp'];
                        ?>
                cart.push({
                    id_db: '<?= $row['id_temp'] ?>',
                    id: '<?= $row['produk_id'] ?>',
                    nama: '<?= addslashes($row['nama_produk']) ?>',
                    harga: parseFloat('<?= $row['harga_jual'] ?>'),
                    qty: parseInt('<?= $row['jumlah'] ?>'),
                    catatan: '', // Catatan dari DB bisa ditambahkan jika ada kolomnya
                    extra_harga: 0
                });
                <?php endforeach; ?>
                <?php endif; ?>

                // ==========================================
                // 3. FUNGSI UTAMA: addWithQty (Validasi Stok & Resep)
                // ==========================================
                function addWithQty(id, nama, harga, kategori, stok, jenis) {
                    const qtyInputElem = document.getElementById('qty_input_' + id);
                    const jumlahInput = parseInt(qtyInputElem ? qtyInputElem.value : 1);

                    // Validasi Angka
                    if (isNaN(jumlahInput) || jumlahInput < 1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Input Tidak Valid',
                            text: 'Minimal order 1 bos!'
                        });
                        if (qtyInputElem) qtyInputElem.value = 1;
                        return;
                    }

                    // A. Cek Resep (Jika produk olahan)
                    const punyaResep = daftarIdResep.includes(id.toString()) || daftarIdResep.includes(parseInt(id));
                    if (punyaResep) {
                        const bahanList = dataResepFull[id];
                        let aman = true;
                        let pesan = "";
                        if (bahanList) {
                            bahanList.forEach(b => {
                                const butuh = parseFloat(b.butuh) * jumlahInput;
                                if (parseFloat(b.stok_bahan) < butuh) {
                                    aman = false;
                                    pesan = `Stok ${b.nama_bahan} tidak cukup! (Tersedia: ${b.stok_bahan})`;
                                }
                            });
                        }
                        if (!aman) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Bahan Habis',
                                text: pesan
                            });
                            return;
                        }
                    }
                    // B. Cek Stok Retail (Jika produk snack/kering)
                    else if (parseFloat(stok) < jumlahInput) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Stok Habis',
                            text: `Stok "${nama}" hanya sisa ${stok}`
                        });
                        return;
                    }

                    // Lolos validasi? Langsung eksekusi addToCart
                    addToCart(id, nama, harga, kategori, stok, jenis, jumlahInput);
                    if (qtyInputElem) qtyInputElem.value = 1; // Reset input angka ke 1
                }

                // ==========================================
                // 4. FUNGSI EKSEKUSI: addToCart (Simpan JS & AJAX DB)
                // ==========================================
                function addToCart(id, nama, harga, kategori, stok, jenis, qtyVal = 1) {
                    try {
                    const qtyInput = parseInt(qtyVal);
                    let namaFinal = nama;

                    if (kategori === 'minuman') {
                        const suhuDipilih = document.querySelector(`input[name="suhu_${id}"]:checked`);
                        if (suhuDipilih) namaFinal += ` - ${suhuDipilih.value}`;
                    }

                    const catatanInput = document.getElementById('catatan_' + id);
                    const catatan = catatanInput ? catatanInput.value : '';

                    // Update Array Lokal
                    const existing = cart.find(i => i.id === id && i.nama === namaFinal && i.catatan === catatan);
                    if (existing) {
                        existing.qty += qtyInput;
                    } else {
                        cart.push({ id_db: null, id: id, nama: namaFinal, harga: parseFloat(harga), qty: qtyInput, catatan: catatan });
                    }

                    if (catatanInput) catatanInput.value = '';
                    renderCart();

                    // AJAX Ke Server (Pastikan route di Routes.php adalah: $routes->post('kasir/tambah_item_temp', 'Kasir::tambah_item_temp');)
                    $.ajax({
                        url: '<?= base_url("kasir/tambah_item_temp") ?>',
                        type: 'POST',
                        data: { produk_id: id, qty: qtyInput, harga: harga, catatan: catatan },
                        dataType: 'json',
                        success: function(res) {
                            if (res.status === 'success' && res.id_temp) {
                                const item = cart.find(i => i.id === id && i.nama === namaFinal && i.catatan === catatan);
                                if (item) item.id_db = res.id_temp;
                            }
                        }
                    });

                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: `${qtyInput} ${namaFinal} masuk!`, timer: 1500, showConfirmButton: false });
                    } catch (e) {
                        console.error("Error addToCart:", e);
                    }
                }

                // ==========================================
                // 5. OPERASIONAL KERANJANG (Hapus & Edit Qty)
                // ==========================================
                function removeItem(index) {
                    const item = cart[index];
                    const idDb = item.id_db;
                    cart.splice(index, 1);
                    renderCart();
                    if (idDb) {
                        $.ajax({
                            url: '<?= site_url("kasir/hapus_item_temp") ?>/' + idDb,
                            type: 'GET'
                        });
                    }
                }

                function updateQty(index, change) {
                    if (cart[index].qty + change > 0) {
                        cart[index].qty += change;
                        renderCart();
                        // Optional: Tambahkan AJAX update_qty_temp di sini jika ingin DB real-time
                    } else {
                        removeItem(index);
                    }
                }

                function hapusSemua() {
                    if (cart.length === 0) return;
                    Swal.fire({
                        title: 'Kosongkan Keranjang?',
                        icon: 'warning',
                        showCancelButton: true
                    }).then((res) => {
                        if (res.isConfirmed) {
                            cart = [];
                            renderCart();
                            $.ajax({
                                url: '<?= site_url("kasir/hapus_semua_temp") ?>',
                                type: 'GET',
                                success: () => location.reload()
                            });
                        }
                    });
                }

                function SimpanReservasi() {
                    const csrfName = '<?= csrf_token() ?>';
                    const csrfHash = '<?= csrf_hash() ?>';

                    let jamRaw = $('#res_jam_booking').val();

                    // Pastikan jam tidak kosong sebelum kirim
                    if (!jamRaw) {
                        Swal.fire('Opps!', 'Jadwal Booking wajib diisi bos!', 'warning');
                        return;
                    }

                    const dataKirim = {
                        nomor_meja: $('#res_nomor_meja').val(),
                        nama_pelanggan: $('#res_nama').val(),
                        telepon: $('#res_telepon').val(),
                        jam_booking: jamRaw,
                        jumlah_orang: $('#res_jumlah').val() || 1,
                        [csrfName]: csrfHash
                    };

                    console.log("Data dikirim:", dataKirim); // Cek di F12, pastikan jam_booking tidak "undefined"

                    $.ajax({
                        url: "<?= site_url('kasir/simpan_reservasi') ?>",
                        type: "POST",
                        data: dataKirim,
                        dataType: "json",
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('BERHASIL!', 'Meja berhasil di-booking.', 'success').then(() => {
                                    $('#modalReservasi').modal('hide');
                                    // Pastikan fungsi ini ada untuk refresh halaman/meja
                                    if (typeof fetchMejaMini === "function") fetchMejaMini();
                                    else location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Security Error', 'Token expired atau koneksi putus. Refresh (F5).',
                                'error');
                        }
                    });
                }

                // ==========================================
                // 6. LOGIKA PEMBAYARAN (Hitung Total & Poin)
                // ==========================================
                let totalFinalYangDibayar = 0;

                function hitungTotalAkhir() {
                    // 1. Ambil Rate PPN dari database (sudah kita definisikan di atas)
                    const ratePPN = <?= $setting['ppn'] ?? 0 ?>;

                    // 2. Hitung Subtotal dari Cart
                    let subtotal = cart.reduce((sum, i) => {
                        let harga = typeof i.harga === 'string' ?
                            parseFloat(i.harga.replace(/[^0-9]/g, '')) :
                            parseFloat(i.harga);
                        return sum + (harga * i.qty);
                    }, 0);

                    // 3. JEDOR! Hitung PPN & Total Awal (Subtotal + PPN)
                    let nominalPPN = Math.ceil(subtotal * (ratePPN / 100));
                    let totalAwalPlusPajak = subtotal + nominalPPN;

                    // 4. Ambil Nilai Diskon & Poin Member
                    let diskonManual = parseInt($('#input-diskon').val()) || 0;
                    let poinDipakai = parseInt($('#input-poin-dipakai').val()) || 0;
                    let maxPoin = parseInt($('#max-poin-member').text()) || 0;

                    // 5. Validasi Poin Member
                    if (poinDipakai > maxPoin) {
                        $('#error-poin').show();
                        poinDipakai = maxPoin;
                        $('#input-poin-dipakai').val(maxPoin);
                    } else {
                        $('#error-poin').hide();
                    }

                    // 6. Hitung Nilai Tukar Poin (1 poin = Rp 1.000)
                    let nilaiDiskonPoin = poinDipakai * 1000;

                    // 7. TOTAL AKHIR (Tagihan Real)
                    // Sekarang totalAwal sudah termasuk PPN, jadi hasilnya pasti sinkron 13.200
                    totalFinalYangDibayar = Math.max(0, totalAwalPlusPajak - diskonManual - nilaiDiskonPoin);

                    // Tampilkan ke layar Modal (Box Hijau Mint)
                    $('#display-total-modal').text('Rp ' + totalFinalYangDibayar.toLocaleString('id-ID'));

                    // 8. Hitung Kembalian
                    let bayar = parseInt($('#input-uang-diterima').val()) || 0;
                    let sisa = bayar - totalFinalYangDibayar;

                    // Update Label Kembalian
                    const labelKembali = sisa >= 0 ? sisa.toLocaleString('id-ID') : '0';
                    $('#display-kembali').text('Rp ' + labelKembali);
                    $('#label-kembalian').text('Rp ' + labelKembali);

                    // Update variabel global agar saat simpan ke database angkanya benar
                    globalTotal = totalFinalYangDibayar;
                }

                function bukaModalBayar() {
                    if (cart.length === 0) return Swal.fire('Kosong!', 'Keranjang masih kosong.', 'warning');
                    $('#input-diskon').val(0);
                    $('#input-poin-dipakai').val(0);
                    $('#input-uang-diterima').val('');
                    hitungTotalAkhir();
                    const modal = new bootstrap.Modal(document.getElementById('modalBayar'));
                    modal.show();
                }

                function setUang(nominal) {
                    $('#input-uang-diterima').val(nominal);
                    hitungTotalAkhir();
                }

                // ==========================================
                // 7. RENDER UI & EVENT LISTENERS
                // ==========================================
                // 1. Definisikan Rate PPN dari database di paling atas agar bisa dibaca semua fungsi
                const pajakRate = <?= $setting['ppn'] ?? 0 ?>;

                function renderCart() {
                    const cartContainer = document.getElementById('cart-items');
                    const emptyCartLabel = document.getElementById('empty-cart');

                    if (!cartContainer) return;

                    cartContainer.innerHTML = '';
                    let subtotalTotal = 0;

                    if (cart.length === 0) {
                        if (emptyCartLabel) emptyCartLabel.style.display = 'block';
                        updateTotalDisplay(0);
                        return;
                    }

                    if (emptyCartLabel) emptyCartLabel.style.display = 'none';

                    cart.forEach((item, index) => {
                        let hargaMurni = typeof item.harga === 'string' ?
                            parseFloat(item.harga.replace(/[^0-9.-]+/g, "")) :
                            parseFloat(item.harga);

                        let qtyMurni = parseInt(item.qty);
                        let itemSubtotal = hargaMurni * qtyMurni;
                        subtotalTotal += itemSubtotal;

                        let viewCatatan = "";
                        if (item.catatan && item.catatan.trim() !== "") {
                            viewCatatan = `<small class="text-danger d-block fw-bold" style="font-style: italic; font-size: 0.75rem;">
                                <i class="fas fa-edit me-1"></i>Notes: ${item.catatan}
                           </small>`;
                        }

                        cartContainer.innerHTML += `
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <div style="flex: 1;">
                                <span class="fw-bold d-block">${item.nama}</span>
                                ${viewCatatan} 
                                <small class="text-muted">Rp ${hargaMurni.toLocaleString('id-ID')}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2 px-3">
                                <button class="btn btn-outline-secondary btn-sm rounded-circle" onclick="updateQty(${index}, -1)" style="width:25px;height:25px;padding:0">-</button>
                                <span class="fw-bold">${qtyMurni}</span>
                                <button class="btn btn-outline-secondary btn-sm rounded-circle" onclick="updateQty(${index}, 1)" style="width:25px;height:25px;padding:0">+</button>
                            </div>
                            <div class="text-end" style="min-width: 100px;">
                                <span class="fw-bold d-block">Rp ${itemSubtotal.toLocaleString('id-ID')}</span>
                                <button class="btn btn-link btn-sm text-danger p-0" onclick="removeItem(${index})">Hapus</button>
                            </div>
                        </div>`;
                    });

                    // Panggil fungsi update display dengan total subtotal terbaru
                    updateTotalDisplay(subtotalTotal);
                }

                function updateTotalDisplay(subtotal) {
                    let nominalPajak = Math.ceil(subtotal * (pajakRate / 100));
                    let totalAkhir = subtotal + nominalPajak;

                    globalTotal = totalAkhir;
                    $('#total').text('Rp ' + totalAkhir.toLocaleString('id-ID'));
                    $('#display-total-modal').text('Rp ' + totalAkhir.toLocaleString('id-ID'));

                    const tipeAktif = $('input[name="tipe_pesanan"]:checked').val();
                    if (tipeAktif === 'Online') {
                        $('#input-uang-diterima').val(globalTotal);
                        hitungTotalAkhir();
                    }
                }

                $(document).ready(function() {
                    // Jalankan render cart pertama kali
                    renderCart();

                    // Fitur Pencarian Produk
                    $('#search-produk').on('keyup', function() {
                        const key = $(this).val().toLowerCase();
                        $('.product-card').each(function() {
                            const nama = $(this).find('h6').text().toLowerCase();
                            $(this).closest('.col-md-3').toggle(nama.includes(key));
                        });
                    });

                    // Trigger hitung total saat input modal berubah (Pembayaran/Diskon)
                    $('#input-uang-diterima, #input-diskon, #input-poin-dipakai').on('input', function() {
                        if (typeof hitungTotalAkhir === "function") {
                            hitungTotalAkhir();
                        }
                    });
                });
                </script>
            </div>



            <div class="col-md-4 bg-white cart-section d-flex flex-column p-4" style="height: 100vh;">
                <div id="hasil_member" class="d-none mt-2">
                    <div class="p-2 rounded text-start" style="background-color: #e9ecef; border: 1px solid #ced4da;">
                        <span id="nama_member_terpilih" class="small fw-bold text-dark d-block"></span>
                        <span id="poin_member" class="text-muted d-block" style="font-size: 11px;"></span>

                        <input type="hidden" name="member_id" id="id_member_hidden">

                        <button type="button" id="batal_member" class="btn btn-sm btn-danger py-0 px-1 mt-1"
                            style="font-size: 11px;">Batal</button>
                    </div>
                </div>
                <div class="card border-0 bg-light rounded-3 mb-3">
                    <div class="card-body p-3">
                        <label class="fw-bold text-muted mb-2 d-block" style="font-size: 0.75rem;">
                            <i class="bi bi-person-badge me-1"></i> PILIH MEMBER
                        </label>

                        <div class="input-group input-group-lg mb-2 shadow-sm">
                            <input type="text" id="cari-member" class="form-control border-primary-subtle"
                                placeholder="Nama / No. Telp..."
                                style="font-size: 0.95rem; border-radius: 8px 0 0 8px !important;">

                            <button class="btn btn-primary px-3" type="button" onclick="cariMemberAjax()" title="Cari">
                                <i class="bi bi-search"></i>
                            </button>

                            <button class="btn btn-success px-3" type="button" data-bs-toggle="modal"
                                data-bs-target="#modalTambahMember" title="Tambah"
                                style="border-radius: 0 8px 8px 0 !important;">
                                <i class="bi bi-person-plus-fill"></i>
                            </button>
                        </div>

                        <div id="info-member-terpilih" class="mt-2" style="display:none;">
                            <div
                                class="d-flex justify-content-between align-items-center bg-primary-subtle p-2 px-3 rounded-3 border border-primary-subtle">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-primary" id="nama-member-kasir"
                                        style="font-size: 0.95rem;"></span>
                                    <span id="badge-info-member"></span>
                                </div>
                                <button
                                    class="btn btn-sm btn-danger rounded-circle p-0 d-flex align-items-center justify-content-center"
                                    onclick="resetMemberKasir()" style="width: 24px; height: 24px; font-size: 1rem;">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <input type="hidden" id="id-member-terpilih" value="">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Keranjang Belanja</h5>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalHapusSemua">
                        <i class="bi bi-trash me-1"></i> Hapus Semua
                    </button>
                </div>

                <div id="cart-container" class="flex-grow-1 overflow-auto mb-3" style="min-height: 200px;">
                    <div class="text-center text-muted py-5" id="empty-cart">
                        <p>Belum ada produk dipilih</p>
                    </div>

                    <div id="cart-items"></div>
                </div>
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between">
                        <p class="mb-1">Subtotal</p>
                        <p id="subtotal" class="mb-1">Rp 0</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="mb-1">PPN 10%</p>
                        <p id="tax" class="mb-1">Rp 0</p>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-4">
                        <p>Total</p>
                        <p id="total">Rp 0</p>
                    </div>
                    <button onclick="bukaModalBayar()"
                        class="btn btn-bayar-glow w-100 py-3 mt-2 rounded-3 fw-bold shadow-lg d-flex align-items-center justify-content-center">
                        <i class="fas fa-money-bill-wave me-2"></i> BAYAR SEKARANG
                    </button>

                    <style>
                    .btn-bayar-glow {
                        background: linear-gradient(45deg, #0f0f0f, #2c3e50);
                        color: #12f024;
                        /* Warna cyan khas Senja Coffee */
                        border: 1px solid rgba(18, 240, 210, 0.3);
                        letter-spacing: 1px;
                        transition: all 0.3s ease;
                    }

                    .btn-bayar-glow:hover {
                        color: #fff;
                        background: #05ac6cc2;
                        box-shadow: 0 0 20px rgba(18, 240, 210, 0.6);
                        transform: translateY(-2px);
                    }
                    </style>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran Final Premium - Rapi, Tidak Bocor, & Pas di Tablet -->
    <div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
        <!-- JEDOR! Kita kembalikan modal-dialog-scrollable agar konten mengunci di dalam bingkai putih -->
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; background: #ffffff;">

                <!-- Header Menggunakan Struktur Bootstrap Asli Bos agar Menahan Konten -->
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" style="color: #2d3748; font-size: 1.15rem; margin: 0;">
                        <i class="fas fa-wallet text-success me-2"></i>Proses Pembayaran
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        style="margin: 0;"></button>
                </div>

                <!-- Di dalam modal-body kita beri padding yang pas -->
                <div class="modal-body p-4 pt-3">

                    <!-- Tampilan Total Tagihan Premium (Di dalam body, dijamin tidak akan melompati header) -->
                    <div class="text-center p-3 mb-3 shadow-inner"
                        style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-radius: 16px; border: 1px solid #bbf7d0;">
                        <p class="text-success small mb-1 text-uppercase fw-bold"
                            style="letter-spacing: 0.5px; font-size: 0.72rem;">Total Pembayaran</p>
                        <small class="text-muted text-decoration-line-through small" id="label-subtotal-awal"
                            style="display:none;"></small>
                        <h2 class="text-success fw-bolder mb-0" id="display-total-modal"
                            style="font-size: 2.3rem; letter-spacing: -0.5px; line-height: 1.2;">Rp 0</h2>
                    </div>

                    <!-- Tipe Pesanan Control -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small mb-1">Tipe Pesanan:</label>
                        <div class="btn-group w-100 p-1 bg-light shadow-inner" style="border-radius: 12px;"
                            role="group">
                            <input type="radio" class="btn-check" name="tipe_pesanan" id="dine_in" value="Dine In"
                                checked>
                            <label class="btn btn-outline-success border-0 rounded-3 py-2 fw-bold text-xs"
                                for="dine_in">Dine In</label>

                            <input type="radio" class="btn-check" name="tipe_pesanan" id="take_away" value="Take Away">
                            <label class="btn btn-outline-success border-0 rounded-3 py-2 fw-bold text-xs"
                                for="take_away">Take Away</label>

                            <input type="radio" class="btn-check" name="tipe_pesanan" id="ojol" value="Online">
                            <label class="btn btn-outline-success border-0 rounded-3 py-2 fw-bold text-xs"
                                for="ojol">Online</label>
                        </div>
                    </div>
                    <!-- Untuk Pilihan metode Online -->
                    <div id="wadah-mitra-online" class="mb-3 p-3 bg-light border border-dashed rounded-4"
                        style="display: none; border-color: #198754 !important;">
                        <label class="form-label fw-bold text-success small mb-2" style="font-size: 0.75rem;">
                            <i class="fas fa-motorcycle me-1"></i> Pilih Mitra Ojek Online:
                        </label>
                        <div class="row g-2">

                            <div class="col-4">
                                <input type="radio" class="btn-check" name="mitra_online" id="mitra_gojek"
                                    value="GoFood" autocomplete="off">
                                <label
                                    class="btn btn-mitra-ol w-100 py-2 d-flex flex-column align-items-center justify-content-center border bg-white shadow-sm btn-gojek"
                                    for="mitra_gojek">
                                    <svg class="img-logo-mitra mb-1" viewBox="0 0 100 30" style="height: 24px;">
                                        <g fill="#00AA13">
                                            <circle cx="15" cy="15" r="10" />
                                            <circle cx="15" cy="15" r="4" fill="#fff" />
                                        </g>
                                        <text x="32" y="21" font-family="Arial, sans-serif" font-weight="bold"
                                            font-size="16" fill="#000">GoFood</text>
                                    </svg>
                                    <span class="text-mitra">GoFood</span>
                                </label>
                            </div>

                            <div class="col-4">
                                <input type="radio" class="btn-check" name="mitra_online" id="mitra_grab"
                                    value="GrabFood" autocomplete="off">
                                <label
                                    class="btn btn-mitra-ol w-100 py-2 d-flex flex-column align-items-center justify-content-center border bg-white shadow-sm btn-grab"
                                    for="mitra_grab">
                                    <svg class="img-logo-mitra mb-1" viewBox="0 0 100 30" style="height: 24px;">
                                        <path d="M10,10 C25,5 25,25 40,20" stroke="#00B14F" stroke-width="4" fill="none"
                                            stroke-linecap="round" />
                                        <path d="M10,18 C25,13 25,33 40,28" stroke="#00B14F" stroke-width="4"
                                            fill="none" stroke-linecap="round" />
                                        <text x="45" y="22" font-family="Arial, sans-serif" font-weight="800"
                                            font-size="14" fill="#00B14F" italic>Grab</text>
                                    </svg>
                                    <span class="text-mitra">GrabFood</span>
                                </label>
                            </div>

                            <div class="col-4">
                                <input type="radio" class="btn-check" name="mitra_online" id="mitra_shopee"
                                    value="ShopeeFood" autocomplete="off">
                                <label
                                    class="btn btn-mitra-ol w-100 py-2 d-flex flex-column align-items-center justify-content-center border bg-white shadow-sm btn-shopee"
                                    for="mitra_shopee">
                                    <svg class="img-logo-mitra mb-1" viewBox="0 0 100 30" style="height: 24px;">
                                        <path d="M15,12 L15,8 A5,5 0 0,1 25,8 L25,12" stroke="#EE4D2D" stroke-width="2"
                                            fill="none" />
                                        <rect x="11" y="11" width="18" height="14" rx="3" fill="#EE4D2D" />
                                        <text x="35" y="21" font-family="Arial, sans-serif" font-weight="bold"
                                            font-size="15" fill="#EE4D2D">Shopee</text>
                                    </svg>
                                    <span class="text-mitra">Shopee</span>
                                </label>
                            </div>

                        </div>
                    </div>

                    <!-- 4 Grid Utama Tombol Metode Pembayaran -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <button id="btn-tunai" class="btn btn-payment w-100 py-3" onclick="pilihMetode('Tunai')">
                                <i class="fas fa-money-bill-wave d-block mb-1 fs-5"></i><span
                                    class="text-xs">Tunai</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button id="id-qris" class="btn btn-payment w-100 py-3" onclick="pilihMetode('QRIS')">
                                <i class="fas fa-qrcode d-block mb-1 fs-5"></i><span class="text-xs">QRIS</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button id="id-trans" class="btn btn-payment w-100 py-3" onclick="pilihMetode('Transfer')">
                                <i class="fas fa-university d-block mb-1 fs-5"></i><span class="text-xs">Transfer</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button id="id-edc" class="btn btn-payment w-100 py-3" onclick="pilihMetode('EDC')">
                                <i class="fas fa-credit-card d-block mb-1 fs-5"></i><span class="text-xs">EDC</span>
                            </button>
                        </div>
                    </div>

                    <!-- Input Kode Promo -->
                    <div class="text-start mb-3">
                        <label class="small text-muted fw-bold mb-1">Kode Promo</label>
                        <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-light border-0 text-muted"><i
                                    class="fas fa-ticket-alt"></i></span>
                            <input type="text" id="input-kode-promo"
                                class="form-control border-0 bg-light text-uppercase fw-bold" placeholder="SENJASORE">
                            <button class="btn btn-success px-3 border-0 fw-bold" type="button"
                                onclick="cekPromoAjax()">Cek</button>
                        </div>
                    </div>

                    <!-- Input Diskon & Uang Diterima -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="small text-danger fw-bold mb-1">Diskon</label>
                            <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-0 text-danger fw-bold"><i
                                        class="fas fa-tags"></i></span>
                                <input type="number" id="input-diskon"
                                    class="form-control border-0 bg-light text-danger fw-bold" placeholder="0"
                                    oninput="hitungTotalAkhir()">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold mb-1">Uang Diterima</label>
                            <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-0 text-success fw-bold"><i
                                        class="fas fa-wallet"></i></span>
                                <input type="number" id="input-uang-diterima"
                                    class="form-control border-0 bg-light text-success fw-bold" placeholder="0"
                                    oninput="hitungTotalAkhir()">
                            </div>
                        </div>
                    </div>

                    <!-- Area Redeem Poin Member -->
                    <div id="area-redeem-poin" class="mb-3 p-2 rounded-3 bg-light border border-dashed border-success"
                        style="display: none;">
                        <label class="small text-success fw-bold d-block mb-1">Tukar Poin (<span
                                id="max-poin-member">0</span> Pts)</label>
                        <div class="input-group input-group-sm rounded-2 overflow-hidden shadow-sm">
                            <input type="number" id="input-poin-dipakai"
                                class="form-control border-0 text-success fw-bold" placeholder="0"
                                oninput="hitungTotalAkhir()">
                            <span class="input-group-text bg-white border-0 text-muted small fw-bold"
                                style="font-size: 0.65rem;">1 Pts = 1rb</span>
                        </div>
                    </div>

                    <!-- Shortcut Uang Cepat & Uang Pas -->
                    <div class="d-flex gap-1 mb-3">
                        <button class="btn btn-sm btn-quick flex-grow-1 py-2" onclick="setUang(10000)">10rb</button>
                        <button class="btn btn-sm btn-quick flex-grow-1 py-2" onclick="setUang(50000)">50rb</button>
                        <button class="btn btn-sm btn-quick flex-grow-1 py-2" onclick="setUang(100000)">100rb</button>
                        <button class="btn btn-sm btn-success flex-grow-1 py-2 fw-bold text-xs"
                            onclick="setUangPas()">Uang Pas</button>
                    </div>

                    <!-- Tampilan Kembalian -->
                    <div class="p-3 d-flex justify-content-between align-items-center shadow-sm"
                        style="background: #f4fbf7; border-radius: 14px; border: 1px solid #d1fae5;">
                        <span class="text-success small fw-bold ms-1"
                            style="font-size: 0.8rem; letter-spacing: 0.5px;">KEMBALIAN</span>
                        <h4 class="mb-0 fw-bolder text-success me-1" id="display-kembali">Rp 0</h4>
                    </div>
                </div>

                <!-- JEDOR! Menggunakan Footer Bawaan Asli agar Tombol tidak Amblas ke luar bingkai -->
                <div class="modal-footer border-0 p-4 pt-0">
                    <button class="btn btn-success w-100 py-3 shadow-md fw-bolder"
                        style="border-radius: 14px; font-size: 1.05rem; background: #10b981; border: none; letter-spacing: 0.5px;"
                        onclick="konfirmasiPembayaran()">
                        Konfirmasi Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalAbsenKru" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

                <div class="modal-header border-0 bg-primary bg-gradient py-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm me-3"
                            style="width: 38px; height: 38px;">
                            <i class="fas fa-fingerprint text-primary fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-white mb-0">Portal Absensi</h6>
                            <small class="text-white-50" style="font-size: 10px;">KasirKita</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"
                        style="font-size: 10px;"></button>
                </div>

                <form id="formAbsenKru" method="POST" action="<?= base_url('absensi/absen_pakai_pin') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="image_tag" id="image_tag_kru">

                    <div class="modal-body p-4">
                        <div class="text-center mb-3">
                            <div class="bg-light p-1 rounded-pill d-flex border" role="group">
                                <input type="radio" class="btn-check" name="mode_absen" id="mode_masuk" value="masuk"
                                    checked>
                                <label
                                    class="btn btn-outline-primary rounded-pill border-0 fw-bold w-100 py-2 shadow-none btn-sm"
                                    for="mode_masuk">
                                    <i class="fas fa-sign-in-alt me-1"></i> MASUK
                                </label>

                                <input type="radio" class="btn-check" name="mode_absen" id="mode_pulang" value="pulang">
                                <label
                                    class="btn btn-outline-danger rounded-pill border-0 fw-bold w-100 py-2 shadow-none btn-sm"
                                    for="mode_pulang">
                                    <i class="fas fa-sign-out-alt me-1"></i> PULANG
                                </label>
                            </div>
                        </div>

                        <div id="div_shift" class="mb-3 bg-light p-2 px-3 rounded-3 border border-dashed">
                            <label class="fw-bold text-muted mb-1 d-block" style="font-size: 10px;">
                                <i class="fas fa-clock me-1 text-primary"></i> SHIFT KERJA
                            </label>
                            <select name="shift"
                                class="form-select border-0 bg-transparent fw-bold text-dark shadow-none p-0"
                                style="font-size: 13px;">
                                <option value="Pagi">SHIFT PAGI (08:00 - 16:00)</option>
                                <option value="Sore">SHIFT SORE (16:00 - 23:00)</option>
                                <option value="Malam">SHIFT MALAM (23:00 - Selesai)</option>
                            </select>
                        </div>

                        <div class="mb-3 text-center">
                            <label class="fw-bold text-muted mb-2 d-block" style="font-size: 10px;">VERIFIKASI
                                WAJAH</label>
                            <div id="camera_kru" class="mx-auto rounded-3 border border-2 border-primary-subtle"
                                style="width: 240px; height: 180px; overflow: hidden; background: #eee;"></div>
                            <small class="text-muted mt-1 d-block" style="font-size: 9px;">Pastikan wajah terlihat jelas
                                di kotak</small>
                        </div>

                        <div class="mb-2 text-center">
                            <label class="fw-bold text-muted mb-2 d-block" style="font-size: 10px;">INPUT PIN
                                RAHASIA</label>
                            <input type="password" name="pin_pegawai"
                                class="form-control text-center fw-bold border-2 border-primary-subtle rounded-3 shadow-none"
                                placeholder="••••••" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                style="font-size: 24px; letter-spacing: 10px; height: 55px; background-color: #f8faff;"
                                required>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm">
                            KONFIRMASI ABSENSI <i class="fas fa-check-circle ms-1"></i>
                        </button>
                        <button type="button" class="btn btn-link w-100 text-muted text-decoration-none mt-1 small"
                            data-bs-dismiss="modal" style="font-size: 11px;">Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAntrean" tabindex="-1" aria-labelledby="modalAntreanLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-warning text-dark border-0 py-2">
                    <h6 class="modal-title fw-bold" id="modalAntreanLabel">
                        <i class="fas fa-bell me-1"></i> Antrean Meja
                    </h6>
                    <button type="button" class="btn-close" style="font-size: 10px;" data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>

                <div class="modal-body p-0">
                    <div class="bg-light px-3 py-1 border-bottom d-flex justify-content-between align-items-center">
                        <small class="text-muted fw-bold" style="font-size: 10px;">DAFTAR TUNGGU</small>
                        <span id="total-antrean-modal"
                            class="badge rounded-pill bg-white text-dark border border-warning"
                            style="font-size: 10px;">
                            Total: <?= count($antrean ?? []) ?>
                        </span>
                    </div>

                    <div class="table-responsive" style="max-height: 350px;">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr style="font-size: 11px;">
                                    <th class="ps-3" width="80">MEJA</th>
                                    <th>PEMESAN</th>
                                    <th class="text-center">JAM</th>
                                    <th class="text-end pe-3">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="isi-antrean-tabel-raw" style="font-size: 13px;">
                                <?php if (!empty($antrean_meja)): ?>
                                <?php foreach ($antrean_meja as $a): ?>
                                <tr class="baris-antrean border-start border-3 border-warning">
                                    <td class="ps-3">
                                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                            style="width: 32px; height: 32px; font-size: 14px;">
                                            <?= $a['nomor_meja'] ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold d-block mb-0"
                                            style="line-height: 1.2;"><?= $a['nama_pemesan'] ?></span>
                                        <small class="text-muted" style="font-size: 10px;">ID:
                                            #<?= $a['id_temp'] ?></small>
                                    </td>
                                    <td class="text-center">
                                        <small class="badge bg-light text-dark border fw-normal"
                                            style="font-size: 10px;">
                                            <?= date('H:i', strtotime($a['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button"
                                            class="btn btn-warning btn-xs rounded-pill px-3 fw-bold shadow-sm btn-detail-antrean"
                                            style="font-size: 11px; padding: 4px 10px;" data-id="<?= $a['id_temp'] ?>"
                                            data-nama="<?= $a['nama_pemesan'] ?>" data-meja="<?= $a['nomor_meja'] ?>"
                                            data-items='<?= htmlspecialchars($a['item_json'], ENT_QUOTES, 'UTF-8') ?>'
                                            data-total="<?= number_format($a['total_harga'], 0, ',', '.') ?>">
                                            <i class="fa fa-list-ul me-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr class="pesan-kosong">
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <small>Tidak ada antrean.</small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-1">
                    <button type="button" class="btn btn-outline-secondary btn-xs rounded-pill px-3"
                        style="font-size: 11px;" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- //Modal Detail Pesanan -->
    <div class="modal fade" id="modalDetailAntrean" tabindex="-1" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <input type="hidden" id="id_temp_hidden">
                <div class="modal-header border-0 pb-0">
                    <h6 class="fw-bold mb-0">Rincian Meja <span id="detMeja"></span></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2 small text-muted">Pemesan: <b id="detNama" class="text-dark"></b></div>
                    <hr class="border-dashed my-2">

                    <div id="detListItems" class="mb-3">
                    </div>

                    <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3 mb-3 border">
                        <span class="small fw-bold">Total Tagihan</span>
                        <span class="fw-bold text-success" id="detTotal">Rp 0</span>
                    </div>


                    <div class="modal-footer border-0 p-4 pt-0">
                        <div class="w-100">
                            <button type="button" id="btnTarikFinal"
                                class="btn btn-success w-100 mb-2 py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                style="border-radius: 10px; font-weight: 700; background: #10B981; border: none;">
                                <i class="fas fa-cart-plus"></i>
                                <span>Add To Cart</span>
                            </button>

                            <button type="button" id="btnBatalPesanan"
                                class="btn btn-light w-100 py-2 d-flex align-items-center justify-content-center gap-2"
                                style="border-radius: 10px; font-weight: 600; color: #DC2626; background: #FEF2F2; border: 1px solid #FEE2E2;">
                                <i class="fas fa-times"></i>
                                <span>Cancel Order</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Untuk Detail Table -->
    <div class="modal fade" id="modalTable" tabindex="-1" aria-labelledby="modalTableLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modalTableLabel"><i class="fas fa-table me-2"></i>Pilih Meja /
                        Monitoring</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 d-flex gap-3 justify-content-center">
                        <small><span class="badge bg-success">&nbsp;</span> Tersedia</small>
                        <small><span class="badge bg-danger">&nbsp;</span> Terisi</small>
                        <small><span class="badge bg-warning">&nbsp;</span> Reservasi</small>
                    </div>

                    <div id="load-meja-mini" class="row g-2">
                        <div class="text-center p-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p>Loading Meja...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Reservasi Table -->
    <div class="modal fade" id="modalReservasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius: 20px; border: none; box-shadow: 0 15px 30px rgba(0,0,0,0.1);">
                <div class="modal-header bg-warning border-0" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-calendar-check me-2"></i>Form Booking Meja
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formSimpanReservasi">
                    <div class="modal-body p-4">
                        <input type="hidden" id="res_id_meja" name="id_meja">

                        <div class="mb-4 text-center">
                            <label class="text-muted small text-uppercase fw-bold" style="letter-spacing: 1px;">Nomor
                                Meja</label>
                            <input type="text" id="res_nomor_meja" name="nomor_meja"
                                class="form-control-plaintext fw-bold text-center text-dark p-0"
                                style="font-size: 3.5rem; line-height: 1;" readonly>
                            <hr class="w-25 mx-auto mt-2" style="border-top: 3px solid #ffc107; opacity: 1;">
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold small text-muted mb-1">NAMA PELANGGAN</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i
                                        class="fas fa-user text-warning"></i></span>
                                <input type="text" id="res_nama" name="nama_pelanggan"
                                    class="form-control bg-light border-0" style="border-radius: 0 10px 10px 0;"
                                    placeholder="Nama : " required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold small text-muted mb-1">NO. TELEPON / WA</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i
                                        class="fas fa-phone text-warning"></i></span>
                                <input type="number" id="res_telepon" name="telpon"
                                    class="form-control bg-light border-0" style="border-radius: 0 10px 10px 0;"
                                    placeholder="0812xxxx" required>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="fw-bold small text-muted mb-1">JADWAL BOOKING</label>
                                <input type="datetime-local" id="res_jam_booking" name="jam_booking"
                                    class="form-control bg-light border-0 rounded-3" required>
                            </div>
                            <div class="col-6">
                                <label class="fw-bold small text-muted mb-1">JUMLAH ORANG</label>
                                <input type="number" id="res_jumlah" name="jumlah_orang"
                                    class="form-control bg-light border-0 rounded-3" placeholder="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" onclick="SimpanReservasi()"
                            class="btn btn-warning w-100 fw-bold py-3 shadow-sm"
                            style="border-radius: 15px; font-size: 1rem; letter-spacing: 1px;">
                            <i class="fas fa-save me-2"></i>SIMPAN RESERVASI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetailOrder" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header bg-danger text-white" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-receipt me-2"></i>Detail Pesanan Meja <span id="detail_nomor_meja"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="isi_detail_order">
                        <div class="text-center p-3">
                            <i class="fas fa-spinner fa-spin fa-2x text-danger"></i>
                            <p>Memuat data...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f8f9fa; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary fw-bold rounded-pill"
                        data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <style>
    .hover-scale:active {
        transform: scale(0.98);
    }

    .border-dashed {
        border-style: dashed !important;
    }
    </style>
    <script>
    // ==========================================
    // FUNGSI GLOBAL (Dipanggil lewat onclick di HTML)
    // ==========================================
    function cariMemberAjax() {
        const keyword = $('#cari-member').val(); // Sesuaikan ID input pencarian bos

        if (keyword === "") {
            return Swal.fire("Ops!", "Masukkan nama atau nomor telp!", "warning");
        }

        $.ajax({
            url: "<?= base_url('kasir/cari_member_kasir') ?>",
            type: "POST",
            data: {
                keyword: keyword,
                "<?= csrf_token() ?>": $('input[name="<?= csrf_token() ?>"]').val()
            },
            dataType: "JSON",
            success: function(res) {
                if (res.status === 'success') {
                    // ... logic tampilkan data bos ...
                    Swal.fire({
                        icon: 'success',
                        title: 'Member Ditemukan',
                        text: 'Member: ' + res.data.nama_member,
                        timer: 1000,
                        showConfirmButton: false,
                        timerProgressBar: true,
                    });

                    // Munculkan info member
                    $('#id-member-terpilih').val(res.data.id_member);
                    $('#nama-member-kasir').html(res.data.nama_member + " [" + res.data.level_vip + "]");
                    $('#info-member-terpilih').show();
                    $('#cari-member').hide();
                } else {
                    Swal.fire("Gagal", res.message, "error");
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                Swal.fire("Error", "Gagal menghubungi server!", "error");
            }
        });
    }

    // ==========================================
    // JQUERY DOCUMENT READY (Dijalankan saat halaman selesai loading)
    // ==========================================
    $(document).ready(function() {

        // A. Logika Switch URL (Masuk vs Pulang)
        const urlMasuk = "<?= base_url('absensi/absen_pakai_pin') ?>";
        const urlPulang = "<?= base_url('absensi/absen_pulang_pin') ?>";

        $('input[name="mode_absen"]').on('change', function() {
            if ($(this).val() === 'pulang') {
                $('#div_shift').slideUp();
                $('#formAbsenKru').attr('action', urlPulang);
            } else {
                $('#div_shift').slideDown();
                $('#formAbsenKru').attr('action', urlMasuk);
            }
        });

        // B. Logika Ambil Foto Otomatis saat Klik Submit
        $('#formAbsenKru').on('submit', function(e) {
            e.preventDefault(); // Tahan form sebentar

            // Jepret foto dari webcam
            Webcam.snap(function(data_uri) {
                // Masukkan string base64 ke input hidden
                $('#image_tag_kru').val(data_uri);

                document.getElementById('formAbsenKru').submit();
            });
        });

        // C. Penangkap Alert SweetAlert2 (Session Flashdata)
        <?php if (session()->getFlashdata('success')) : ?>
        Swal.fire({
            title: 'BERHASIL!',
            text: '<?= session()->getFlashdata('success') ?>',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            borderRadius: '15px'
        });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
        Swal.fire({
            title: 'OPPS!',
            text: '<?= session()->getFlashdata('error') ?>',
            icon: 'error',
            confirmButtonColor: '#0d6efd',
            borderRadius: '15px'
        });
        <?php endif; ?>

    });

    function resetMemberKasir() {

        $('#cari-member').val('').show();
        $('#id-member-terpilih, #id_member_hidden').val('');
        $('#nama-member-kasir').text('');
        $('#info-member-terpilih').hide();
        $('#area-redeem-poin').hide();
        $('#input-poin-dipakai').val('');

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Member dibatalkan',
            showConfirmButton: false,
            timer: 1000
        });
    }
    // ------------------------------------------
    // 3. TOMBOL Modal Absen
    // ------------------------------------------

    $(document).ready(function() {
        // 1. Inisialisasi Kamera saat modal Portal Absensi dibuka
        $('#modalAbsenKru').on('shown.bs.modal', function() {
            if (typeof Webcam !== "undefined") {
                Webcam.set({
                    width: 240,
                    height: 180,
                    dest_width: 640,
                    dest_height: 480,
                    image_format: 'jpeg',
                    jpeg_quality: 90,
                    facingMode: "user"
                });
                Webcam.attach('#camera_kru');
            } else {
                alert("Library Kamera (WebcamJS) belum dimuat!");
            }
        });

        // 2. Matikan kamera saat modal ditutup agar tidak berat
        $('#modalAbsenKru').on('hidden.bs.modal', function() {
            Webcam.reset();
            // Reset form juga biar bersih
            document.getElementById('formAbsenKru').reset();
            document.getElementById('div_shift').style.display = 'block';
        });

        // 3. Logika Ganti Mode MASUK / PULANG
        $('input[name="mode_absen"]').on('change', function() {
            const mode = $(this).val();
            const form = document.getElementById('formAbsenKru');
            const divShift = document.getElementById('div_shift');

            if (mode === 'pulang') {
                divShift.style.display = 'none'; // Sembunyikan shift kalau pulang
                form.action = "<?= base_url('absensi/absen_pulang_pin') ?>";
            } else {
                divShift.style.display = 'block'; // Munculkan shift kalau masuk
                form.action = "<?= base_url('absensi/absen_pakai_pin') ?>";
            }
        });

        // 4. Proses Submit (Ambil Foto Selfie)
        document.getElementById('formAbsenKru').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');

            // Validasi PIN sederhana
            const pin = form.querySelector('input[name="pin_pegawai"]').value;
            if (pin.length < 4) {
                alert("Masukkan PIN dengan benar bos!");
                return;
            }

            // Kunci tombol biar gak double klik
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> MEMPROSES...';

            try {
                Webcam.snap(function(data_uri) {
                    // Simpan hasil foto ke input hidden image_tag_kru
                    document.getElementById('image_tag_kru').value = data_uri;

                    // Kirim form ke Controller
                    form.submit();
                });
            } catch (err) {
                alert("Kamera belum siap! Pastikan gambar wajah sudah muncul.");
                btn.disabled = false;
                btn.innerHTML = 'KONFIRMASI ABSENSI <i class="fas fa-check-circle ms-1"></i>';
            }
        });
    });
    </script>

    <?php if (session()->getFlashdata('absen_sukses')) :
    $data_sukses = session()->getFlashdata('absen_sukses'); ?>
    <div class="modal fade" id="modalSuksesAbsen" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 30px; overflow: hidden;">
                <div class="modal-body p-4 text-center">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center text-success bg-success bg-opacity-10 rounded-circle"
                        style="width: 90px; height: 90px; border: 4px solid #f0fff4;">
                        <i class="fas fa-check-circle fa-4x"></i>
                    </div>

                    <h3 class="fw-bold text-dark mb-1">Berhasil Absen!</h3>
                    <p class="text-muted mb-4" style="font-size: 14px;">Selamat bekerja & jaga kesehatan selalu</p>

                    <div class="p-3 mb-4 text-start"
                        style="background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 20px;">
                        <div
                            class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-light">
                            <span class="text-muted small">Nama Pegawai</span>
                            <span class="fw-bold text-dark"><?= $data_sukses['nama'] ?? '-' ?></span>
                        </div>
                        <div
                            class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-light">
                            <span class="text-muted small">Jam Masuk</span>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">
                                <i class="far fa-clock me-1"></i> <?= $data_sukses['jam'] ?? '-' ?> WIB
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Shift Kerja</span>
                            <span class="fw-bold text-primary">
                                <?php 
                                $shift = $data_sukses['shift'] ?? $data_sukses['nama_shift'] ?? '-';
                                echo strtoupper($shift);
                            ?>
                            </span>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 d-flex align-items-center mb-0"
                        style="background:#e0f7fa; border-radius:15px; color:#006064; font-size: 12px; text-align: left;">
                        <i class="fas fa-info-circle me-3 fa-lg"></i>
                        <div>Jangan lupa berikan pelayanan terbaik untuk pelanggan hari ini!</div>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-success w-100 py-3 fw-bold shadow-sm" data-bs-dismiss="modal"
                        style="border-radius: 18px; background: #10b981; border: none;">
                        OKE, MENGERTI
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('peringatan_absen')) :
        $data_warn = session()->getFlashdata('peringatan_absen'); ?>
    <div class="modal fade" id="modalPeringatanAbsen" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">

                <div class="modal-body p-4 text-center">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center text-warning bg-warning bg-opacity-10 rounded-circle"
                        style="width: 80px; height: 80px; border: 4px solid #fff5e6;">
                        <i class="fas fa-exclamation-circle fa-3x"></i>
                    </div>

                    <h4 class="fw-bold text-dark mb-2">Gagal Absen!</h4>
                    <p class="text-muted px-2 mb-4" style="font-size: 14px; line-height: 1.6;">
                        <?= $data_warn['pesan'] ?>
                    </p>

                    <div class="p-3 mb-4 text-start"
                        style="background-color: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 18px;">
                        <div
                            class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-light">
                            <span class="text-muted small fw-medium">Nama Pegawai</span>
                            <span class="fw-bold text-dark"><?= $data_warn['nama'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-medium">Waktu Tercatat</span>
                            <span class="badge bg-warning-subtle text-warning fw-bold px-3 py-2 rounded-pill">
                                <i class="far fa-clock me-1"></i> <?= $data_warn['jam'] ?> WIB
                            </span>
                        </div>
                    </div>

                    <div class="alert alert-secondary border-0 bg-light rounded-3 py-2 px-3 mb-0"
                        style="font-size: 12px; border-left: 4px solid #6c757d !important;">
                        <i class="fas fa-info-circle me-1"></i>
                        Silakan pilih <b>Absen Pulang</b> jika tugas Anda sudah selesai hari ini.
                    </div>
                </div>

                <div class="modal-footer p-4 pt-0 border-0 justify-content-center">
                    <button type="button" class="btn btn-dark w-100 py-3 fw-bold shadow-sm" data-bs-dismiss="modal"
                        style="border-radius: 16px; letter-spacing: 1px;">
                        MENGERTI
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Efek Hover Button */
    #modalPeringatanAbsen .btn-dark {
        transition: all 0.3s ease;
    }

    #modalPeringatanAbsen .btn-dark:hover {
        background-color: #000;
        transform: translateY(-2px);
    }
    </style>
    <?php endif; ?>
    <?php if (session()->getFlashdata('peringatan_pulang')) :
            $data_warn_p = session()->getFlashdata('peringatan_pulang'); ?>
    <div class="modal fade" id="modalPeringatanPulang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">

                <div class="modal-body p-4 text-center">
                    <div class="mx-auto mb-4 d-flex align-items-center justify-content-center text-info bg-info bg-opacity-10 rounded-circle"
                        style="width: 80px; height: 80px; border: 4px solid #f0faff;">
                        <i class="fas fa-door-open fa-3x"></i>
                    </div>

                    <h4 class="fw-bold text-dark mb-2">Sudah Pulang!</h4>
                    <p class="text-muted px-2 mb-4" style="font-size: 14px; line-height: 1.6;">
                        <?= $data_warn_p['pesan'] ?>
                    </p>

                    <div class="p-3 mb-4 text-start"
                        style="background-color: #fcfcfc; border: 1px solid #f0f0f0; border-radius: 18px;">
                        <div
                            class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-light">
                            <span class="text-muted small fw-medium">Nama Pegawai</span>
                            <span class="fw-bold text-dark"><?= $data_warn_p['nama'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small fw-medium">Jam Pulang</span>
                            <span class="badge bg-info-subtle text-info fw-bold px-3 py-2 rounded-pill">
                                <i class="far fa-clock me-1"></i> <?= $data_warn_p['jam'] ?> WIB
                            </span>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 bg-light rounded-3 py-2 px-3 mb-0 text-dark"
                        style="font-size: 12px; border-left: 4px solid #0dcaf0 !important;">
                        <i class="fas fa-check-circle me-1 text-info"></i>
                        Sesi kerja Anda untuk hari ini telah berakhir. Selamat beristirahat!
                    </div>
                </div>

                <div class="modal-footer p-4 pt-0 border-0 justify-content-center">
                    <button type="button" class="btn btn-info w-100 py-3 fw-bold text-white shadow-sm"
                        data-bs-dismiss="modal" style="border-radius: 16px; letter-spacing: 1px;">
                        OK, MENGERTI
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Efek Hover Button Info */
    #modalPeringatanPulang .btn-info {
        transition: all 0.3s ease;
        background-color: #0dcaf0;
        border: none;
    }

    #modalPeringatanPulang .btn-info:hover {
        background-color: #0baccc;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 202, 240, 0.3) !important;
    }
    </style>
    <?php endif; ?>

    <script>
    $(document).ready(function() {
        // Jalankan modal Sukses jika ada
        if ($('#modalSuksesAbsen').length > 0) {
            var mSukses = new bootstrap.Modal(document.getElementById('modalSuksesAbsen'));
            mSukses.show();
            setTimeout(function() {
                mSukses.hide();
            }, 3000);
        }

        // Jalankan modal Peringatan jika ada (Ini tambahannya)
        if ($('#modalPeringatanAbsen').length > 0) {
            var mWarn = new bootstrap.Modal(document.getElementById('modalPeringatanAbsen'));
            mWarn.show();
        }
        if ($('#modalPeringatanPulang').length > 0) {
            var mWarnP = new bootstrap.Modal(document.getElementById('modalPeringatanPulang'));
            mWarnP.show();
        }
        // if ($('#modalSuksesAbsen').length > 0) triggerFlashModal('modalSuksesAbsen');
        // if ($('#modalPeringatanAbsen').length > 0) triggerFlashModal('modalPeringatanAbsen');
        // if ($('#modalPeringatanPulang').length > 0) triggerFlashModal('modalPeringatanPulang');
    });

    function eksekusiHapusSemua() {
        // 1. Kosongkan data array di balik layar
        cart = [];

        // 2. Coba panggil fungsi render otomatis (siapa tahu salah satu dari ini namanya)
        if (typeof updateCartUI === "function") updateCartUI();
        else if (typeof updateCart === "function") updateCart();
        else if (typeof renderCart === "function") renderCart();
        else if (typeof tampilkanKeranjang === "function") tampilkanKeranjang();
        else if (typeof renderKeranjang === "function") renderKeranjang();

        // 3. SAPU JAGAT (Bersihkan HTML secara paksa)
        // Coba tebak ID tabel bos, kalau namanya beda, tinggal disesuaikan ya!
        $('#cart-body').empty();
        $('#list-keranjang').empty();
        $('#tbody-keranjang').empty();

        // Reset angka total di luar modal (kalau ada)
        $('#total-belanja').text('Rp 0');
        $('#display-total').text('Rp 0');

        // 4. Tutup modal konfirmasi
        $('.modal').modal('hide');

        // 5. Munculkan notifikasi sukses
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Keranjang dikosongkan',
            showConfirmButton: false,
            timer: 1500
        });
    }

    function checkWaktuOrder() {
        if (cart.length === 0 || waktuOrder === null) {
            let sekarang = new Date();
            waktuOrder = sekarang.toLocaleDateString('id-ID') + ' ' +
                sekarang.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                }).replace(/:/g, '.');
        }
    }

    function resetPromo() {
        $('#input-kode-promo').val('');
        $('#input-diskon').val(0).attr('readonly', false).removeClass('bg-light');
        $('#id-promo-terpilih').val('');
        $('#pesan-promo').text('');
        $('#btn-batal-promo').hide();
        hitungTotalAkhir();
    }

    function cekPromoAjax() {
        const kode = $('#input-kode-promo').val();
        const pesanEl = $('#pesan-promo');

        let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
        let totalBelanja = Math.ceil(subtotal + (subtotal *
            0.11)); // Harus 11% agar sinkron dengan minimal belanja di database

        if (kode === "") {
            Swal.fire("Ops!", "Masukkan kode promo dulu bos!", "warning");
            return;
        }

        $.ajax({
            url: "<?= base_url('admin/cek_promo') ?>",
            type: "POST",
            data: {
                kode_promo: kode,
                total_belanja: totalBelanja
            },
            dataType: "JSON",
            success: function(res) {
                if (res.status === 'success') {
                    pesanEl.removeClass('text-danger').addClass('text-success').text("Promo aktif: " +
                        res
                        .nama_promo);
                    $('#btn-batal-promo').show();
                    $('#input-diskon').val(res.potongan).attr('readonly', true).addClass('bg-light');
                    $('#id-promo-terpilih').val(res.id_promo);

                    // Memicu hitungan agar harga langsung berubah
                    hitungTotalAkhir();

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Promo diterapkan.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire("Gagal", res.msg, "error");
                    resetPromo();
                }
            },
            error: function() {
                alert("Gagal konek ke server! Periksa koneksi internet atau rute admin/cek_promo.");
            }
        });
    }

    function eksekusiSimpan(total, bayar, diskon) {
        // 1. Bersihkan angka agar murni integer
        const totalBersih = Math.round(parseFloat(total.toString().replace(/[^0-9.]/g, ''))) || 0;
        const diskonBersih = Math.round(parseFloat(diskon.toString().replace(/[^0-9.]/g, ''))) || 0;

        // 2. Ambil Tipe Pesanan Terlebih Dahulu
        const tipePesananEl = document.querySelector('input[name="tipe_pesanan"]:checked');
        const tipePesanan = tipePesananEl ? tipePesananEl.value : 'Dine In';

        let bayarBersih;
        if (tipePesanan === 'Online') {
            // Jika Online, otomatis bayar dianggap sama dengan total (Lunas E-Wallet)
            bayarBersih = totalBersih;
        } else {
            // Jika Dine In/Take Away, ambil dari parameter bayar yang diinput kasir
            bayarBersih = Math.round(parseFloat(bayar.toString().replace(/[^0-9.]/g, ''))) || 0;
        }

        // 4. Ambil ID Member & Poin
        const idMember = $('#id-member-terpilih').val() || "";
        const poinDipakai = parseInt($('#input-poin-dipakai').val()) || 0;

        // 5. Ambil Nomor Meja
        const urlParams = new URLSearchParams(window.location.search);
        let nomorMeja = urlParams.get('meja');
        if (!nomorMeja) {
            nomorMeja = $('input[name="nomor_meja"]').val();
        }

        // 6. Bungkus Data
        const data = {
            total: totalBersih,
            bayar: bayarBersih,
            diskon: diskonBersih,
            tipe_pesanan: tipePesanan,
            id_member: idMember,
            poin_dipakai: poinDipakai,
            cart: cart,
            nomor_meja: nomorMeja,
            // Jika Online, metode otomatis diset sesuai sistem e-wallet (misal QRIS/Online)
            metode: (tipePesanan === 'Online') ? 'Online/E-Wallet' : (typeof metodeTerpilih !== 'undefined' ?
                metodeTerpilih : 'Tunai'),
            pajak: Math.ceil(cart.reduce((sum, i) => sum + (i.harga * i.qty), 0) * (
                <?= $setting['ppn'] ?? 0 ?> / 100))
        };

        console.log("Mengirim Data Transaksi: ", data);

        // 7. Security Check (CSRF CI4)
        const csrfHash = '<?= csrf_hash() ?>';

        fetch('<?= base_url('kasir/bayar') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfHash
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw err;
                    });
                }
                return response.json();
            })
            .then(result => {
                if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil!',
                        text: (tipePesanan === 'Online') ? 'Pesanan Online Berhasil Diproses' :
                            'Pembayaran Telah Disimpan',
                        timer: 1200,
                        showConfirmButton: false,
                        timerProgressBar: true
                    }).then(() => {
                        if (typeof cetakStruk === 'function') {
                            cetakStruk(totalBersih, bayarBersih, diskonBersih, tipePesanan);
                        }

                        const urlBersih = '<?= base_url('kasir') ?>';
                        window.onafterprint = function() {
                            window.location.href = urlBersih;
                        };
                        setTimeout(function() {
                            window.location.href = urlBersih;
                        }, 2000);
                    });
                } else {
                    Swal.fire('Gagal', result.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error Detail:', error);
                let msg = error.message || 'Terjadi kesalahan sistem!';
                Swal.fire('Error', msg, 'error');
            });
    }
    let metodeTerpilih = 'Tunai';

    function konfirmasiPembayaran() {
        const ratePPN = <?= $setting['ppn'] ?? 0 ?>;
        let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
        let nominalPPN = Math.ceil(subtotal * (ratePPN / 100));
        let totalPlusPajak = subtotal + nominalPPN;

        // Input-input
        let diskonManual = parseInt($('#input-diskon').val()) || 0;
        let poinDipakai = parseInt($('#input-poin-dipakai').val()) || 0;
        let diskonPoin = poinDipakai * 1000;
        let totalDiskonKeseluruhan = diskonManual + diskonPoin;

        let totalFix = totalPlusPajak - totalDiskonKeseluruhan;
        if (totalFix < 0) totalFix = 0;

        // JEDOR! Pastikan angka besar di modal ter-update ke angka final
        $('#display-total-modal').text('Rp ' + totalFix.toLocaleString('id-ID'));

        let uangDiterimaInput = parseInt($('#input-uang-diterima').val()) || 0;
        let uangDiterima = (metodeTerpilih === 'Online') ? totalFix : uangDiterimaInput;
        let uangKembalian = (metodeTerpilih === 'Online') ? 0 : (uangDiterima - totalFix);

        // 🎯 VALIDASI & PENGAMBILAN DATA MITRA OJOL DINAMIS
        const tipePesanan = $('input[name="tipe_pesanan"]:checked').val();
        let mitraOjol = $('input[name="mitra_online"]:checked').val() || '';

        // Cegat kasir jika dia pilih tipe Online tapi belum memilih boks logonya
        if (tipePesanan === 'Online' && mitraOjol === '') {
            return Swal.fire({
                icon: 'warning',
                title: 'Mitra Ojol Belum Dipilih',
                text: 'Silakan pilih GoFood, GrabFood, atau ShopeeFood terlebih dahulu bos!',
                confirmButtonColor: '#ffc107'
            });
        }

        // 🎯 SINKRONISASI LABEL METODE: Jika Online, formatnya menjadi "Online / GoFood", dll.
        let labelMetodeTampil = (tipePesanan === 'Online') ? `${metodeTerpilih} / ${mitraOjol}` : metodeTerpilih;

        // Validasi Tunai
        if (metodeTerpilih === 'Tunai' && uangDiterimaInput < totalFix) {
            return Swal.fire({
                icon: 'error',
                title: 'Pembayaran Gagal',
                text: `Uang kurang: Rp ${(totalFix - uangDiterimaInput).toLocaleString('id-ID')}`,
                confirmButtonColor: '#d33'
            });
        }

        Swal.fire({
            title: 'Konfirmasi Transaksi',
            html: `
        <table class="table table-sm text-start" style="font-size: 0.9rem;">
            <tr><td>Metode</td><td>: <b>${labelMetodeTampil}</b></td></tr>
            <tr><td>Subtotal</td><td>: <b>Rp ${subtotal.toLocaleString('id-ID')}</b></td></tr>
            <tr><td>PPN (${ratePPN}%)</td><td>: <b>Rp ${nominalPPN.toLocaleString('id-ID')}</b></td></tr>
            ${diskonManual > 0 ? `<tr class="text-danger"><td>Diskon</td><td>: -<b>Rp ${diskonManual.toLocaleString('id-ID')}</b></td></tr>` : ''}
            ${diskonPoin > 0 ? `<tr class="text-success"><td>Poin</td><td>: -<b>Rp ${diskonPoin.toLocaleString('id-ID')}</b></td></tr>` : ''}
            <tr class="table-active"><td>TOTAL AKHIR</td><td>: <b class="text-success" style="font-size:1.2rem">Rp ${totalFix.toLocaleString('id-ID')}</b></td></tr>
            <tr><td>Bayar</td><td>: <b>Rp ${uangDiterima.toLocaleString('id-ID')}</b></td></tr>
            <tr><td>Kembalian</td><td>: <b class="${uangKembalian > 0 ? 'text-primary' : ''}">Rp ${uangKembalian.toLocaleString('id-ID')}</b></td></tr>
        </table>
        `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Ya, Simpan & Cetak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                eksekusiSimpan(totalFix, uangDiterima, totalDiskonKeseluruhan);
            }
        });
    }

    //Tipe Pesanan
    $(document).ready(function() {
        $('input[name="tipe_pesanan"]').on('change', function() {
            const tipe = $(this).val();

            if (tipe === 'Online') {
                // JEDOR! Set variabel metode global menjadi "Online"
                metodeTerpilih = 'Online';

                // 🎯 SUNTIKAN BARU: Tampilkan boks pilihan mitra ojol secara smooth slide down
                $('#wadah-mitra-online').slideDown(200);

                // Visual tombol: Matikan semua highlight metode agar tidak bingung
                $('.btn-payment').removeClass('active btn-success').addClass(
                    'btn-outline-secondary');

                // Otomatis set uang pas
                $('#input-uang-diterima').val(globalTotal).prop('readonly', true);
                $('.btn-quick').addClass('disabled').prop('disabled', true);

                hitungTotalAkhir();

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1000
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Mode Online Aktif'
                });
            } else {
                // 🎯 SUNTIKAN BARU: Sembunyikan boks ojol jika kembali ke Dine In / Take Away
                $('#wadah-mitra-online').slideUp(200);

                // Bersihkan pilihan radio button ojol yang sempat diklik agar datanya kembali kosong (reset)
                $('input[name="mitra_online"]').prop('checked', false);

                // Jika balik ke Dine In, kembalikan ke Tunai
                pilihMetode('Tunai');
                $('#input-uang-diterima').prop('readonly', false);
                $('.btn-quick').removeClass('disabled').prop('disabled', false);
            }
        });
    });

    function pilihMetode(metode) {
        metodeTerpilih = metode;

        const btnTunai = document.getElementById('btn-tunai');
        const btnQris = document.getElementById('id-qris');
        const btnTrf = document.getElementById('id-trans');
        const btnEti = document.getElementById('id-edc');

        // 1. Reset semua tombol ke style default
        const allBtns = [btnTunai, btnQris, btnTrf, btnEti];
        allBtns.forEach(btn => {
            if (btn) {
                btn.className = 'btn btn-payment w-100 py-3';
                $(btn).removeClass('active');
            }
        });

        // Cek tipe pesanan saat ini
        const tipePesanan = $('input[name="tipe_pesanan"]:checked').val();

        // 2. Berikan efek aktif & Munculkan Modal (Hanya jika BUKAN Online)
        let targetBtn = null;
        if (metode === 'Tunai') {
            targetBtn = btnTunai;
        } else if (metode === 'QRIS') {
            targetBtn = btnQris;
            // JEDOR! Modal gambar hanya muncul kalau kasir klik manual (bukan mode Online)
            if (typeof tampilkanQRIS === "function" && tipePesanan !== 'Online') tampilkanQRIS();
        } else if (metode === 'EDC') {
            targetBtn = btnEti;
            if (typeof tampilkanEDC === "function" && tipePesanan !== 'Online') tampilkanEDC();
        } else if (metode === 'Transfer') {
            targetBtn = btnTrf;
            if (typeof tampilkanTransfer === "function" && tipePesanan !== 'Online') tampilkanTransfer();
        }

        if (targetBtn) {
            targetBtn.className = 'btn btn-payment active w-100 py-3';
        }

        // 3. Logika Otomatisasi Uang Pas
        if (metode !== 'Tunai' || tipePesanan === 'Online') {
            // Otomatis isi uang pas (Misal Rp 13.200)
            // JEDOR! Pastikan variabel globalTotal Bos sudah terisi dari fungsi hitungTotalAkhir
            $('#input-uang-diterima').val(typeof globalTotal !== 'undefined' ? globalTotal : 0);

            // Jika Online, kunci input agar tidak bisa diubah-ubah
            if (tipePesanan === 'Online') {
                $('#input-uang-diterima').prop('readonly', true);
                $('.btn-quick').addClass('disabled').prop('disabled', true);
            }
        } else {
            // Jika balik ke Tunai, buka kunci
            $('#input-uang-diterima').val('').prop('readonly', false);
            $('.btn-quick').removeClass('disabled').prop('disabled', false);
        }

        // Update tampilan kembalian
        if (typeof hitungTotalAkhir === "function") {
            hitungTotalAkhir();
        }
    }

    function tampilkanQRIS() {
        const namaFileQris = "<?= $setting['foto_qris'] ?? '' ?>";
        const urlQris = namaFileQris !== "" ? "<?= base_url('uploads/img/') ?>" + namaFileQris :
            "<?= base_url('assets/img/no-qris.jpg') ?>";

        Swal.fire({
            title: '<span class="fw-bold" style="color:#2d3748;">Scan QRIS Pembayaran</span>',
            html: '<p class="text-muted small mb-0">Silahkan scan untuk menyelesaikan transaksi</p>',
            imageUrl: urlQris,
            imageAlt: 'QRIS Code',
            confirmButtonText: 'OKE, SUDAH SCAN',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981', // Emerald
            cancelButtonColor: '#6c757d',
            customClass: {
                image: 'responsive-qris-img',
                popup: 'swal2-popup-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                konfirmasiPembayaran();
            } else {
                // JEDOR! Cek tipe dulu sebelum reset ke tunai
                const tipe = $('input[name="tipe_pesanan"]:checked').val();
                if (tipe !== 'Online') pilihMetode('Tunai');
            }
        });
    }

    function tampilkanEDC() {
        Swal.fire({
            title: '<span class="fw-bold">Pembayaran Mesin EDC</span>',
            text: 'Silahkan gesek atau masukkan kartu pelanggan ke mesin EDC',
            icon: 'info',
            confirmButtonText: 'OKE, BERHASIL',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981', // Samakan warna emerald
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                konfirmasiPembayaran();
            } else {
                const tipe = $('input[name="tipe_pesanan"]:checked').val();
                if (tipe !== 'Online') pilihMetode('Tunai');
            }
        });
    }

    function tampilkanTransfer() {
        // 🎯 SUNTIKAN UTAMA: Ambil nama file rekening secara dinamis dari database (PHP Variable)
        const namaFileRekening = "<?= $setting['rekening'] ?? '' ?>";
        const urlRekening = namaFileRekening !== "" ? "<?= base_url('uploads/img/') ?>" + namaFileRekening :
            "<?= base_url('assets/img/no-qris.jpg') ?>"; // Bisa diganti ke asset/img/no-rekening.jpg jika Bos punya file khusus

        Swal.fire({
            title: '<span class="fw-bold" style="color:#2d3748;">Pembayaran Transfer Bank</span>',
            html: '<p class="text-muted small mb-0">Pastikan dana sudah masuk ke rekening sebelum konfirmasi bos!</p>',
            imageUrl: urlRekening, // 🟢 Gambar otomatis mengikuti upload-an owner
            imageAlt: 'Rekening Bank Code',
            confirmButtonText: 'OKE, SUDAH MASUK',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981', // Emerald premium
            cancelButtonColor: '#6c757d',
            customClass: {
                image: 'responsive-qris-img', // 🟢 Memakai class responsif yang sama dengan QRIS
                popup: 'swal2-popup-custom'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                konfirmasiPembayaran();
            } else {
                // JEDOR! Tetap amankan tipe sebelum reset ke tunai
                const tipe = $('input[name="tipe_pesanan"]:checked').val();
                if (tipe !== 'Online') pilihMetode('Tunai');
            }
        });
    }

    function cetakStruk(totalParam, bayar, diskon = 0, tipePesanan) {
    const ratePPN = <?= $setting['ppn'] ?? 0 ?>;
    let subtotalMurni = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
    let nominalPPN = Math.ceil(subtotalMurni * (ratePPN / 100));
    let totalReal = (subtotalMurni + nominalPPN) - diskon;

    // --- LOGIKA UTAMA (PERBAIKAN) ---
    let mitraOjol = $('input[name="mitra_online"]:checked').val() || '';
    // Gunakan toLowerCase() agar case-insensitive (tidak peduli huruf besar/kecil)
    let isOnline = (metodeTerpilih?.toLowerCase() === 'online' || tipePesanan?.toLowerCase() === 'online');

    let metodeStruk = '';
    if (isOnline && mitraOjol !== '') {
        // Jika mode Online aktif, paksa format seragam
        metodeStruk = `Online (${mitraOjol})`;
        tipePesanan = `Online / ${mitraOjol}`; // Update tipePesanan agar sinkron
    } else {
        // Jika bukan online, gunakan apa yang dipilih atau default Tunai
        metodeStruk = metodeTerpilih || 'Tunai';
    }

    let invoiceNumber = `INV-${Date.now().toString().slice(-6)}`;
    let tgl = new Date().toLocaleDateString('id-ID');
    let jam = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    let jamOrderTampil = waktuOrder ? waktuOrder.split('.').slice(0, 2).join('.') : jam.replace(':', '.');
    let jamBayarTampil = jam.replace(':', '.');

    // Footer yang diformat dengan aman
    let footerTeks = <?= json_encode(esc($setting['footer_struk'] ?? 'Selamat Menikmati')) ?>;
    let formattedFooter = footerTeks.replace(/\n/g, '<br>');

    // --- FUNGSI BUAT STRUK ---
    const buatStrukBayar = (label) => {
        let html = `
        <div class="struk-container">
        <div class="text-center">
            ${label !== '' ? `<div style="border: 1px solid #000; padding: 2px; font-size: 8px; margin-bottom: 5px; font-weight: bold;">*** ${label} ***</div>` : ''}
            <?php if (!empty($setting['logo'])) : ?>
            <img src="<?= base_url('uploads/img/' . $setting['logo']) ?>" style="max-width: 80px; height: auto; margin-bottom: 5px; filter: grayscale(100%);">
            <?php endif; ?> 
            <h2 style="margin:0; font-size: 14px;"><?= esc($setting['nama_toko'] ?? 'Nama Toko') ?></h2>
            <small style="font-size: 10px;"><?= esc($setting['alamat'] ?? 'Alamat Belum Diatur') ?></small><br>
            <div class="line"></div>
            <table style="font-size: 10px; width: 100%;">
                <tr><td style="width: 40%;">Tgl</td><td>: ${tgl}</td></tr>
                <tr><td>Kasir</td><td>: <?= esc(session()->get('nama_user')) ?></td></tr>
                <tr><td>Tipe Pesanan</td><td>: <b>${tipePesanan}</b></td></tr>
                <tr><td>Order Masuk</td><td>: ${jamOrderTampil}</td></tr>
                <tr><td>Waktu Bayar</td><td>: ${jamBayarTampil}</td></tr>
                <tr><td colspan="2" class="text-center" style="font-weight:bold; padding-top:15px; font-size: 18px;">No. Order: ${invoiceNumber}</td></tr>
            </table>
        </div>
        <div class="line"></div>
        <table>`;

        cart.forEach(item => {
            html += `
            <tr><td colspan="2" style="font-weight:bold;">${item.nama}</td></tr>
            <tr><td>${item.qty} x ${item.harga.toLocaleString()}</td><td class="text-right">${(item.qty * item.harga).toLocaleString()}</td></tr>
            ${item.catatan ? `<tr><td colspan="2" style="font-size: 9px; font-style: italic; padding-bottom: 2px;">* Req: ${item.catatan}</td></tr>` : ''}
            <tr><td colspan="2" style="height: 4px;"></td></tr>`;
        });

        html += `
        </table>
        <div class="line"></div>
        <table style="font-size: 10px;">
            <tr><td>Subtotal</td><td class="text-right">${subtotalMurni.toLocaleString()}</td></tr>
            <tr><td>PPN (${ratePPN}%)</td><td class="text-right">${nominalPPN.toLocaleString()}</td></tr>
            ${diskon > 0 ? `<tr class="text-danger"><td>Diskon</td><td class="text-right"> -${diskon.toLocaleString()}</td></tr>` : ''}
            <tr style="font-weight:bold; font-size:14px;"><td>TOTAL AKHIR</td><td class="text-right">${totalReal.toLocaleString()}</td></tr>
            <tr><td>Bayar (${metodeStruk})</td><td class="text-right">${bayar.toLocaleString()}</td></tr>
            <tr><td>Kembali</td><td class="text-right">${(bayar - totalReal).toLocaleString()}</td></tr>
        </table>
        <div class="line"></div>
        <div class="text-center">
            <strong>Terima Kasih</strong><br>
            <small style="display: block; line-height: 1.3;">${formattedFooter}</small>
        </div>
        </div>`;
        return html;
    };

        // --- FUNGSI BUAT STRUK DAPUR (DIPERKETAT DATA OJOLNYA AGAR BARISTA/DAPUR GA SALAH PACKING) ---
        const buatStrukDapur = () => {
            let html = `
        <div class="struk-container">
            <div class="text-center">
                <div style="border: 2px solid #000; padding: 5px; font-weight: bold; margin-bottom: 5px; font-size: 13px;">*** PESANAN DAPUR ***</div>
                <table style="font-size: 11px; width: 100%;">
                    <tr><td>No: <b>${invoiceNumber}</b></td><td class="text-right"><b>${tipePesanan}</b></td></tr>
                    <tr><td>Masuk: ${jamOrderTampil}</td><td class="text-right">Tgl: ${tgl}</td></tr>
                </table>
            </div>
        <div class="line"></div>
        <table style="font-size: 14px; font-weight: bold;">`;

            cart.forEach(item => {
                html += `
                <tr>
                    <td style="padding: 5px 0; vertical-align:top; width: 15%;">${item.qty} x</td>
                    <td style="padding: 5px 0;">
                        ${item.nama}
                        ${item.catatan ? `<div style="font-size: 11px; font-weight: normal; font-style: italic; margin-top: 2px;">>> Req: ${item.catatan}</div>` : ''}
                    </td>
                </tr>`;
            });

            html += `
            </table>
            <div class="line"></div>
            <div class="text-center" style="margin-top: 10px;">
                <small>Dicetak: ${tgl} ${jamBayarTampil}</small>
            </div>
        </div>`;
            return html;
        };

        // Gabungkan Semua Struk
        let strukHTML = `
        <html>
            <head>
                <title>Print Struk</title>
                <style>
                    @page { size: 80mm auto; margin: 0; }
                    body { margin: 0; padding: 0; background-color: white; -webkit-print-color-adjust: exact; }
                    .struk-container { 
                        font-family: 'Courier New', Courier, monospace; 
                        width: 72mm; font-size: 10px; padding: 5mm 2mm; margin: 0 auto;
                    }
                    table { width: 100%; border-collapse: collapse; }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .line { border-top: 1px dashed #000; margin: 5px 0; }
                    .text-danger { color: black !important; }
                    @media print { .page-break { page-break-after: always; display: block; height: 1px; } }
                </style>
            </head>
            <body>
                ${buatStrukBayar('')}
                <div class="page-break"></div>
                ${buatStrukBayar('COPY KASIR')}
                <div class="page-break"></div>
                ${buatStrukDapur()}
            </body>
            </html>`;

        // Proses Print via Iframe
        let iframe = document.getElementById('print-frame');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'print-frame';
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
        }

        let doc = iframe.contentWindow.document;
        doc.open();
        doc.write(strukHTML);
        doc.close();

        setTimeout(() => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
            setTimeout(() => {
                cart = [];
                waktuOrder = null;
                window.location.reload();
            }, 1000);
        }, 500);
    }

    function printClosing() {
        var printArea = document.getElementById('printArea');
        if (!printArea) return;

        // 1. Ambil isi HTML dasar dari printArea
        var printContents = printArea.innerHTML;
        var printFrame = document.createElement('iframe');

        printFrame.style.position = 'fixed';
        printFrame.style.right = '0';
        printFrame.style.bottom = '0';
        printFrame.style.width = '0';
        printFrame.style.height = '0';
        printFrame.style.border = '0';
        document.body.appendChild(printFrame);

        var doc = printFrame.contentWindow.document;
        doc.open();
        doc.write('<html><head><title>Cetak Closing</title>');
        // 🎯 SUNTIKAN STYLING: Tambahkan warna hitam pekat (#000) agar hasil print thermal tajam
        doc.write('<style>body{margin:0;padding:20px;font-family:monospace;color:#000;}</style>');
        doc.write('</head><body>' + printContents + '</body></html>');
        doc.close();

        // 2. Event Handler Setelah Proses Cetak Selesai
        printFrame.contentWindow.onafterprint = function() {
            if (document.body.contains(printFrame)) {
                document.body.removeChild(printFrame);
            }
            $('#modalClosing').modal('hide');
            $('#cetakClosingModal').modal('hide'); // Pastikan modal konfirmasi ikut menutup bersih
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Laporan dicetak!',
                showConfirmButton: false,
                timer: 1000
            });
        };
        setTimeout(function() {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
        }, 250);
    }

    function bukaKustomisasi(index) {
        let item = cart[index];
        $('#index-cart-kustom').val(index);
        $('#nama-produk-kustom').text(item.nama);
        $('#input-catatan-kustom').val(item.catatan || '');
        $('#input-harga-kustom').val(item.extra_harga || '');
        $('#modalKustomisasi').modal('show');
    }

    function simpanKustomisasi() {
        let index = $('#index-cart-kustom').val();
        let catatan = $('#input-catatan-kustom').val();
        let extraHarga = parseInt($('#input-harga-kustom').val()) || 0;

        cart[index].catatan = catatan;
        cart[index].extra_harga = extraHarga;

        $('#modalKustomisasi').modal('hide');

        if (typeof renderCart === "function") {
            renderCart();
        }
        if (typeof hitungTotalAkhir === "function") {
            hitungTotalAkhir();
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Kustomisasi disimpan',
            showConfirmButton: false,
            timer: 1000
        });
    }

    var jumlahTerakhir = -1;
    var isChecking = false;
    var audioNotif = new Audio('<?= base_url("assets/sounds/notif.mp3") ?>');
    audioNotif.load();

    function cekUpdateTombol() {
        if (isChecking) return;
        isChecking = true;

        $.ajax({
            url: '<?= site_url("kasir/cek_notif_antrean") ?>',
            type: 'GET',
            cache: false,
            dataType: 'json',
            success: function(data) {
                try {
                    var jmlSkrg = parseInt(data.jumlah) || 0;
                    var badge = $('#badge-antrean');
                    badge.text(jmlSkrg);
                    $('#total-antrean-modal').text('Total: ' + jmlSkrg);

                    if (jmlSkrg > 0) {
                        badge.show().removeClass('bg-secondary').addClass('bg-danger');
                    } else {
                        badge.hide();
                    }

                    if (jmlSkrg !== jumlahTerakhir) {
                        if (jmlSkrg > jumlahTerakhir && jumlahTerakhir !== -1) {
                            audioNotif.play().catch(e => {
                                console.log("Browser blokir auto-play suara");
                            });
                        }
                        if ($('#modalAntrean').hasClass('show')) {
                            refreshTabelModal();
                        }
                        jumlahTerakhir = jmlSkrg;
                    }
                } catch (e) {
                    console.error("Gagal memproses data notif: " + e);
                }
            },
            error: function(xhr) {
                console.log("Cek notif gagal, mencoba lagi nanti...");
            },
            complete: function() {
                isChecking = false;
            }
        });
    }

    function refreshTabelModal() {
        var area = "#isi-antrean-tabel-raw";
        if ($(area).length) {
            $(area).load(window.location.href + " " + area + " > *");
        }
    }

    //Detail pesanan            
    $(document).on('click', '.btn-detail-antrean', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const meja = $(this).data('meja');
        const total = $(this).data('total');
        let itemsRaw = $(this).attr('data-items');

        $('#id_temp_hidden').val(id);
        $('#modalAntrean').modal('hide');
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');

        $('#detListItems').empty();

        try {
            const items = JSON.parse(itemsRaw);
            let htmlItems = '';
            items.forEach(item => {
                htmlItems += `
            <div class="d-flex justify-content-between mb-2 small border-bottom pb-1">
                <span><b class="text-primary">${item.qty}x</b> ${item.nama}</span>
                <span class="fw-bold">Rp ${(item.harga * item.qty).toLocaleString('id-ID')}</span>
            </div>`;
            });
            $('#detListItems').html(htmlItems ||
                '<div class="text-center text-muted">Tidak ada item</div>');
        } catch (e) {
            $('#detListItems').html(
                '<div class="text-center text-danger small">Gagal memuat detail item</div>');
        }

        $('#detMeja').text(meja);
        $('#detNama').text(nama);
        $('#detTotal').text('Rp ' + total);

        $('#btnTarikFinal').data('id-target', id);

        var modalDet = new bootstrap.Modal(document.getElementById('modalDetailAntrean'));
        modalDet.show();
    });

    //Batal pesanan
    $(document).on('click', '#btnBatalPesanan', function() {
        const idTemp = $('#id_temp_hidden').val();

        // 1. Tutup modal rincian dulu biar bersih
        $('#modalDetailAntrean').modal('hide');

        // 2. Kasih jeda dikit agar animasi modal tutup selesai
        setTimeout(function() {
            Swal.fire({
                title: 'Batalkan Pesanan?',
                text: "Pesanan akan ditolak dan dipindahkan ke riwayat Batal.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626', // Warna Merah Tegas
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {

                    // Tampilkan loading pas proses kirim data ke database
                    Swal.fire({
                        title: 'Memproses Pembatalan...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "<?= site_url('kasir/batal_pesanan_meja/') ?>" +
                            idTemp,
                        type: "POST",
                        dataType: "JSON",
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pesanan Dibatalkan!',
                                    text: 'Pesanan telah berhasil dipindahkan ke riwayat batal.',
                                    showConfirmButton: false,
                                    timer: 2000, // Alert muncul selama 2 detik
                                    timerProgressBar: true
                                }).then(() => {
                                    location
                                        .reload(); // Refresh halaman setelah alert hilang
                                });
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Eror!', 'Terjadi kesalahan pada sistem.',
                                'error');
                        }
                    });
                } else {
                    // Jika kasir berubah pikiran (klik Kembali), buka lagi modal rinciannya
                    $('#modalDetailAntrean').modal('show');
                }
            });
        }, 300);
    });

    $(document).on('click', '#btnTarikFinal', function(e) {
        e.preventDefault();
        const idPesanan = $(this).data('id-target');

        if (!idPesanan) {
            alert("ID Pesanan tidak ditemukan!");
            return;
        }

        $.ajax({
            url: '<?= site_url("kasir/tarik_ke_cart") ?>/' + idPesanan,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#btnTarikFinal').prop('disabled', true).text('Sedang Menarik...');
            },
            success: function(res) {
                $('#modalDetailAntrean').modal('hide');
                $('.modal-backdrop').remove();

                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pesanan ditarik ke keranjang.',
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert("Gagal: " + res.message);
                    $('#btnTarikFinal').prop('disabled', false).text('TARIK KE KERANJANG');
                }
            }
        });
    });

    $(document).ready(function() {
        $(document).one('click', function() {
            audioNotif.play().then(() => {
                audioNotif.pause();
                audioNotif.currentTime = 0;
            }).catch(e => {});
        });

        var myModalEl = document.getElementById('modalAntrean');
        if (myModalEl) {
            myModalEl.addEventListener('show.bs.modal', function() {
                var currentBadge = $('#badge-antrean').text();
                $('#total-antrean-modal').text('Total: ' + currentBadge);
                refreshTabelModal();
            });
        }

        cekUpdateTombol();
        setInterval(cekUpdateTombol, 3000);
    });

    //Ajax untuk memanggil List table
    $(document).ready(function() {
        // Saat modal dibuka, muat data meja terbaru
        console.log("Menarik data meja...");
        $('#modalTable').on('show.bs.modal', function() {
            fetchMejaMini();
        });
    });

    function fetchMejaMini() {
        $.ajax({
            url: "<?= site_url('kasir/get_meja_status') ?>",
            method: "GET",
            dataType: "json",
            cache: false,
            success: function(response) {
                console.log("CEK DATA DARI DB:",
                    response); // LIHAT DI F12 APAKAH STATUSNYA BENAR 'Reservasi'
                let html = '';

                response.forEach(function(m) {
                    // Ambil status asli dan versi huruf kecil
                    let sRaw = m.status_meja;
                    let sLow = sRaw.toLowerCase();

                    // Logika Penentuan Status
                    let isTerisi = sLow.includes('terisi');
                    let isReserved = sLow.includes('reservasi');

                    // Warna Dasar
                    let colorClass = 'success'; // Default Hijau
                    if (isTerisi) colorClass = 'danger';
                    if (isReserved) colorClass = 'warning';

                    // Ikon
                    let iconMeja = 'fa-couch';
                    if (isTerisi) iconMeja = 'fa-users';
                    if (isReserved) iconMeja = 'fa-calendar-check';

                    html += `
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 shadow-sm border-2 border-${colorClass}" 
                                style="border-top-width: 5px; border-radius: 15px; background-color: ${isReserved ? '#fff9c4' : (isTerisi ? '#fff5f5' : '#fff')};">
                                <div class="card-body p-3">
                                    <span class="badge bg-${colorClass} float-end text-uppercase" style="font-size: 0.6rem;">${m.status_meja}</span>
                                    
                                    <div class="my-3 text-center" 
                                        style="color: ${isTerisi ? '#dc3545' : (isReserved ? '#ffc107' : '#198754')}; margin-left: 50px;">
                                        <i class="fas ${iconMeja} fa-2x"></i>
                                    </div>
                                    
                                    <div class="text-center">
                                        <h2 class="fw-bold m-0 text-dark">${m.nomor_meja}</h2>
                                        <p class="text-muted small mb-2">Meja Resto</p>
                                    </div>
                                    
                                    <button class="btn btn-${isTerisi ? 'danger' : (isReserved ? 'warning' : 'success')} btn-sm w-100 rounded-pill mb-2 shadow-sm fw-bold text-${isReserved ? 'dark' : 'white'}" 
                                            onclick="pilihMejaDariModal('${m.nomor_meja}', '${m.status_meja}')">
                                        <i class="fas ${isTerisi ? 'fa-search' : (isReserved ? 'fa-user-clock' : 'fa-plus')} me-1"></i> 
                                        ${isTerisi ? 'Lihat Order' : (isReserved ? 'Check-in' : 'Buka Meja')}
                                    </button>

                                    ${(!isTerisi && !isReserved) ? `
                                        <button class="btn btn-warning btn-sm w-100 rounded-pill mb-2 fw-bold text-dark shadow-sm" 
                                                onclick="bukaFormReservasi('${m.id_meja}', '${m.nomor_meja}')">
                                            <i class="fas fa-calendar-alt me-1"></i> Booking Meja
                                        </button>
                                    ` : ''}

                                    ${(isTerisi || isReserved) ? `
                                        <button class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold" 
                                                style="border-style: dashed; border-width: 2px; font-size: 0.75rem;" 
                                                onclick="eksekusiKosongkanMeja('${m.id_meja}', '${m.nomor_meja}')">
                                            <i class="fas fa-broom me-1"></i> Kosongkan Meja
                                        </button>
                                    ` : ''}

                                </div>
                            </div>
                        </div>`;
                });
                $('#load-meja-mini').html(html);
            }
        });
    }

    function pilihMejaDariModal(nomor, status) {
        // Normalisasi status ke huruf kecil agar pengecekan tidak error
        let statusLow = status.toLowerCase();

        if (statusLow === 'tersedia' || statusLow === 'reservasi') {
            const pesan = (statusLow === 'reservasi') ? 'Proses Check-in Reservasi?' : 'Buka meja baru?';

            Swal.fire({
                title: 'Konfirmasi Meja ' + nomor,
                text: pesan,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Masuk Transaksi'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= site_url('kasir/transaksi') ?>?meja=" + nomor;
                }
            });
        } else if (statusLow === 'terisi') {
            tampilkanDetailOrder(nomor);
        }
    }

    function tampilkanDetailOrder(nomor_meja) {
        // 1. Set judul modal
        $('#detail_nomor_meja').text(nomor_meja);

        // 2. Tampilkan modalnya
        $('#modalDetailOrder').modal('show');

        // 3. Kosongkan isi modal dan kasih loading
        $('#isi_detail_order').html(
            '<div class="text-center p-3"><i class="fas fa-spinner fa-spin fa-2x text-danger"></i><br>Loading Pesanan...</div>'
        );

        $.ajax({
            url: "<?= site_url('kasir/get_order_by_meja/') ?>" + nomor_meja,
            method: "GET",
            dataType: "json",
            success: function(res) {
                console.log("Data diterima JS:", res); // Cek di F12 Console

                if (res && res.length > 0) {
                    let htmlTabel = `
                    <table class="table table-bordered table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Menu</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>`;

                    let grandTotal = 0;

                    // Loop data dari array res
                    $.each(res, function(i, item) {
                        let sub = parseInt(item.subtotal);
                        htmlTabel += `
                        <tr>
                            <td>${item.nama_produk}</td>
                            <td class="text-center">${item.qty}</td>
                            <td class="text-end">Rp ${sub.toLocaleString('id-ID')}</td>
                        </tr>`;
                        grandTotal += sub;
                    });

                    htmlTabel += `
                        </tbody>
                        <tfoot class="fw-bold bg-light">
                            <tr>
                                <td colspan="2" class="text-center">TOTAL</td>
                                <td class="text-end text-danger">Rp ${grandTotal.toLocaleString('id-ID')}</td>
                            </tr>
                        </tfoot>
                    </table>`;

                    $('#isi_detail_order').html(htmlTabel);
                } else {
                    $('#isi_detail_order').html(
                        '<div class="alert alert-warning text-center">Data pesanan kosong.</div>');
                }
            },
            error: function(xhr) {
                console.log("Error AJAX:", xhr.responseText);
                $('#isi_detail_order').html(
                    '<div class="alert alert-danger">Gagal memuat data dari server.</div>');
            }
        });
    }

    function bukaFormReservasi(id, nomor) {
        // 1. Reset isi form biar bersih dari data lama
        $('#formSimpanReservasi')[0].reset();

        // 2. Tampilkan modalnya
        $('#modalReservasi').modal('show');

        // 3. Isi kembali ID dan Nomor Meja (karena ini data dinamis)
        $('#res_id_meja').val(id);
        $('#res_nomor_meja').val(nomor);

        // 4. Set ulang jadwal booking ke waktu sekarang (biar gak kosong)
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        $('#res_jam_booking').val(now.toISOString().slice(0, 16));
    }


    function tarikDataKeKeranjang(nomor) {
        $.ajax({
            url: "<?= site_url('kasir/tarik_pesanan_by_nomor/') ?>" + nomor,
            method: "GET",
            success: function(res) {
                if (res.status == 'success') {
                    // Reload halaman agar penjualan_temp yang ditarik muncul di keranjang
                    location.reload();
                } else {
                    Swal.fire('Info', 'Tidak ada pesanan QR aktif di meja ini.', 'info');
                }
            }
        });
    }

    // 1. Simpan token di variabel yang bisa diubah (Global)
    let currentCsrfHash = '<?= csrf_hash() ?>';
    const csrfName = '<?= csrf_token() ?>';

    function eksekusiKosongkanMeja(id, nomor) {
        Swal.fire({
            title: 'Kosongkan Meja ' + nomor + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kosongkan!',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= site_url('kasir/kosongkan_meja_manual/') ?>" + id,
                    type: "POST",
                    dataType: "json",
                    data: {
                        // 2. Gunakan variabel currentCsrfHash, jangan PHP tag langsung
                        [csrfName]: currentCsrfHash
                    },
                    success: function(res) {
                        if (res.csrf_hash) {
                            currentCsrfHash = res.csrf_hash;
                        }

                        if (res.status == 'success') {
                            Swal.fire('Berhasil!', 'Meja ' + nomor + ' kini kosong.',
                                'success');
                            fetchMejaMini();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Error', 'Sesi keamanan habis, silakan refresh halaman.',
                            'error');
                    }
                });
            }
        });
    }
    </script>
    <div class="modal fade" id="modalTambahMember" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-success text-white border-0 py-3" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Registrasi Member
                        Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="formTambahMember">
                    <?= csrf_field() ?>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Lengkap Member</label>
                            <input type="text" name="nama_member" class="form-control rounded-3 py-2"
                                placeholder="Masukkan nama pelanggan..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nomor WhatsApp (Aktif)</label>
                            <input type="number" name="no_telepon" id="no_hp_baru" class="form-control rounded-3 py-2"
                                placeholder="Contoh: 08123456789" required>
                        </div>
                        <div class="bg-light p-3 rounded-3">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Member baru akan
                                langsung mendapatkan <b>0 Poin</b> secara otomatis setelah terdaftar.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSimpanMember"
                            class="btn btn-success rounded-pill px-4 fw-bold">Daftarkan & Pilih</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHapusSemua" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-5">
                    <div class="text-danger mb-4">
                        <i class="bi bi-exclamation-triangle" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold">Kosongkan Keranjang?</h4>
                    <p class="text-muted">Semua barang yang sudah Anda pilih akan dihapus dari daftar belanja ini.
                    </p>

                    <div class="d-grid gap-2 mt-4">
                        <button type="button" class="btn btn-danger btn-lg fw-bold" onclick="eksekusiHapusSemua()">
                            Ya, Hapus Semua
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"
                style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Yakin ingin keluar?</h4>
                    <p class="text-muted small">Semua sesi aktif Anda akan dihentikan. Pastikan tidak ada transaksi
                        yang
                        menggantung.</p>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Batal</button>
                        <a href="<?= base_url('logout') ?>" class="btn btn-danger rounded-pill px-4 fw-bold">Ya,
                            Keluar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cetakClosingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="fw-bold">Konfirmasi Closing</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-printer text-primary display-4 mb-3"></i>
                    <p class="text-muted small">Ringkasan pendapatan kasir hari ini:</p>

                    <div class="bg-light p-3 rounded-4 mb-3 text-start">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Total Transaksi:</span>
                            <span class="fw-bold text-success">
                                <?= number_format($rekap['total_transaksi'] ?? 0, 0, ',', '.') ?> Nota</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Total Tunai:</span>
                            <span class="fw-bold text-success">Rp
                                <?= number_format($rekap['total_tunai'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Total QRIS:</span>
                            <span class="fw-bold text-dark">Rp
                                <?= number_format($rekap['total_qris'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Total Transfer:</span>
                            <span class="fw-bold text-dark">Rp
                                <?= number_format($rekap['total_transfer'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Total EDC:</span>
                            <span class="fw-bold text-dark">Rp
                                <?= number_format($rekap['total_edc'] ?? 0, 0, ',', '.') ?></span>
                        </div>

                        <div class="d-flex justify-content-between small mb-1">
                            <span>Total Online (Ojol):</span>
                            <span class="fw-bold text-dark">Rp
                                <?= number_format($rekap['total_online'] ?? 0, 0, ',', '.') ?></span>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between small fw-bold">
                            <span>GRAND TOTAL:</span>
                            <span class="text-primary">Rp
                                <?= number_format($rekap['total_pendapatan'] ?? 0, 0, ',', '.') ?></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" onclick="printClosing()">
                        <i class="fas fa-print me-1"></i> Cetak Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalKustomisasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow border-0">
                <div class="modal-header border-0 bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark">Kustomisasi Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="badge bg-primary-subtle text-primary mb-2 px-3 py-2" style="font-size: 0.9rem;">
                            Item
                            Terpilih:</div>
                        <h4 class="fw-bold text-dark" id="nama-produk-kustom"></h4>
                    </div>

                    <input type="hidden" id="index-cart-kustom">

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">CATATAN KHUSUS (REQUEST)</label>
                        <textarea id="input-catatan-kustom" class="form-control form-control-lg fs-6" rows="2"
                            placeholder="Contoh: Less Sugar, Es dipisah, dll..."></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">TAMBAHAN BIAYA / ADD-ON (RP)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-white fw-bold text-success">Rp</span>
                            <input type="number" id="input-harga-kustom" class="form-control fw-bold text-success"
                                placeholder="0" min="0">
                        </div>
                        <div class="form-text mt-2" style="font-size: 0.75rem;">
                            <i class="bi bi-info-circle me-1"></i> Isi jika ada biaya tambahan (misal: Extra Shot
                            +5000)
                        </div>
                    </div>

                    <div class="pt-2">
                        <button class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm"
                            onclick="simpanKustomisasi()">
                            Simpan Perubahan
                        </button>
                        <button class="btn btn-link w-100 mt-2 text-decoration-none text-muted small"
                            data-bs-dismiss="modal">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="printArea" style="display: none;">
        <div
            style="width: 80mm; font-family: 'Courier New', Courier, monospace; font-size: 12px; color: #000; padding: 5mm; background: #fff;">
            <center>
                <strong style="font-size: 16px;">
                    <?= strtoupper(esc($setting['nama_toko'] ?? '')) ?>
                </strong><br>
                <span style="font-size: 12px;"><?= esc($setting['alamat'] ?? '') ?></span><br>
                LAPORAN CLOSING KASIR<br>
                ================================
            </center>
            <br>
            <table style="width: 100%; font-size: 12px;">
                <tr>
                    <td>Tanggal</td>
                    <td>: <?= date('d/m/Y H:i') ?></td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td>: <?= (string) (session()->get('nama_user') ?? 'Kasir') ?></td>
                </tr>
            </table>
            --------------------------------<br>
            <table style="width: 100%; font-size: 12px;">
                <tr>
                    <td>TUNAI</td>
                    <td style="text-align: right;">Rp <?= number_format($rekap['total_tunai'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>QRIS</td>
                    <td style="text-align: right;">Rp <?= number_format($rekap['total_qris'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>TRANSFER</td>
                    <td style="text-align: right;">Rp <?= number_format($rekap['total_transfer'] ?? 0, 0, ',', '.') ?>
                    </td>
                </tr>
                <tr>
                    <td>EDC</td>
                    <td style="text-align: right;">Rp <?= number_format($rekap['total_edc'] ?? 0, 0, ',', '.') ?></td>
                </tr>

                <tr>
                    <td>TOTAL ONLINE</td>
                    <td style="text-align: right;">Rp <?= number_format($rekap['total_online'] ?? 0, 0, ',', '.') ?>
                    </td>
                </tr>
            </table>
            --------------------------------<br>
            <table style="width: 100%; font-size: 12px;">
                <tr style="font-weight: bold;">
                    <td>TOTAL</td>
                    <td style="text-align: right;">Rp <?= number_format($rekap['total_pendapatan'] ?? 0, 0, ',', '.') ?>
                    </td>
                </tr>
                <tr>
                    <td>TOTAL TRANSAKSI</td>
                    <td style="text-align: right;"><?= $rekap['total_transaksi'] ?? 0 ?> Nota</td>
                </tr>
                <tr>
                    <td>BATAL</td>
                    <td style="text-align: right;"><?= $rekap['jumlah_batal'] ?? 0 ?> Transaksi</td>
                </tr>
            </table>
            ================================<br>
            <center>Laporan ini dicetak otomatis<br>oleh sistem kasir.</center>
        </div>
    </div>
    </div>
    </div>
    <div class="modal fade" id="suksesBukaShiftModal" tabindex="-1" aria-labelledby="suksesBukaShiftLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg"
                style="border-radius: 24px; border: none; overflow: hidden; background: #ffffff;">

                <div class="p-4 text-center text-white position-relative"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>

                    <div class="bg-white text-success rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-2"
                        style="width: 75px; height: 75px;">
                        <i class="fas fa-cash-register fa-2x animate__animated animate__jackInTheBox"></i>
                    </div>
                    <h4 class="fw-bold mb-0">Laci Kasir Dibuka!</h4>
                </div>

                <div class="modal-body text-center py-4 px-4">
                    <h5 class="fw-bold text-dark mb-2">Selamat Bertugas!</h5>
                    <p class="text-secondary small mb-0 px-2">
                        <?= session()->getFlashdata('pesan') ?? 'Modal awal berhasil dicatat dan shift transaksi resmi diaktifkan.' ?>
                    </p>
                    <div
                        class="mt-3 p-2 bg-light rounded-3 d-flex align-items-center justify-content-center gap-2 text-success small fw-semibold">
                        <i class="fas fa-check-circle"></i> Status POS: OPEN & READY TO SELL
                    </div>
                </div>

                <div class="modal-footer border-0 pb-4 justify-content-center">
                    <button type="button" class="btn btn-success px-5 py-2.5 fw-bold text-white shadow-sm"
                        data-bs-dismiss="modal"
                        style="border-radius: 14px; background: linear-gradient(45deg, #10b981, #059669); border: none; font-size: 0.95rem; min-width: 180px;">
                        <i class="fas fa-rocket me-1"></i> Mulai Transaksi
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php if (session()->getFlashdata('pesan')): ?>
        var bukaModal = new bootstrap.Modal(document.getElementById('suksesBukaShiftModal'));
        bukaModal.show();
        setTimeout(function() {
            bukaModal.hide();
        }, 3000);
        <?php endif; ?>
    });
    document.addEventListener("DOMContentLoaded", function() {
        // 🚀 CETAK JALUR ABSOLUT: Menggunakan site_url() agar tidak tersesat di Ngrok maupun Localhost
        var URL_Radar_Gembok = "<?= site_url('?check_status_maintenance=1') ?>";

        setInterval(function() {
            // Tambahkan cache-buster stempel waktu (?t=) biar browser tidak mengambil data kadaluarsa
            var URL_Final = URL_Radar_Gembok + "&t=" + new Date().getTime();

            fetch(URL_Final, {
                    cache: 'no-store'
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    //Jika sistem mendeteksi Bos sedang mengaktifkan maintenance.flag (TRUE)
                    if (data.maintenance === true) {
                        // Paksa halaman kasir ini reload total agar langsung mental masuk ke tampilan gembok!
                        window.location.reload();
                    }
                })
                .catch(function(error) {
                    console.log("Memantau radar gembok KasirKita...");
                });
        }, 4000); // ⏱️ Scan status server setiap 4 detik sekali
    });
    </script>
</body>

</html>