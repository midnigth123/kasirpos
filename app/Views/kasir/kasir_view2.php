<?php
/**
 * @var array $data_resep
 * @var array $nama_user
 * @var array $list_id_resep
 * */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" type="image/png" href="<?= base_url('icon_kasir.ico') ?>">
    <title>KasirKita </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    /* --- CSS EXISTING ANDA --- */
    .category-btn {
        border-radius: 10px;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .cart-section {
        border-left: 1px solid #ddd;
        height: 100vh;
        position: sticky;
        top: 0;
    }

    .modal-content {
        max-height: 95vh;
    }

    /* Penyesuaian khusus mode Landscape Tablet/HP */
    @media (orientation: landscape) and (max-height: 550px) {
        .modal-header {
            padding-top: 10px !important;
            padding-bottom: 5px !important;
        }

        #display-total-modal {
            font-size: 1.5rem !important;
            margin-bottom: 10px !important;
        }

        .py-3 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        .mb-4 {
            margin-bottom: 10px !important;
        }

        .modal-body {
            padding: 15px !important;
        }
    }

    /* Mempercantik Scrollbar */
    .modal-body::-webkit-scrollbar {
        width: 4px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    .responsive-qris-img {
        max-height: 45vh !important;
        width: auto !important;
        object-fit: contain !important;
        margin: 10px auto !important;
        display: block;
        border: 1px solid #eee;
        border-radius: 8px;
    }

    /* SweetAlert Custom */
    .swal2-popup-custom {
        padding: 0.5rem !important;
        max-height: 95vh !important;
        overflow-y: auto !important;
    }

    .swal2-title {
        font-size: 1.1rem !important;
        margin-top: 5px !important;
    }

    .swal2-html-container {
        margin: 5px 0 !important;
        font-size: 0.9rem !important;
    }

    .swal2-actions {
        margin-top: 5px !important;
    }

    /* --- DESAIN MODERN BARU UNTUK PRODUCT CARD --- */
    .product-card {
        border: none;
        border-radius: 1.25rem;
        background: #ffffff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.12);
    }

    .img-wrapper {
        position: relative;
        height: 140px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .product-card:hover .img-wrapper img {
        transform: scale(1.15);
    }

    .price-badge-modern {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(4px);
        padding: 4px 12px;
        border-radius: 12px;
        font-weight: 800;
        color: #198754;
        font-size: 0.85rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }

    .sold-out-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: grayscale(1);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
    }

    .input-qty-modern {
        border-radius: 10px 0 0 10px !important;
        border: 1px solid #e9ecef;
        font-weight: 700;
        max-width: 60px;
    }

    .btn-add-modern {
        border-radius: 0 10px 10px 0 !important;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .custom-textarea {
        border-radius: 12px;
        background-color: #f8f9fa;
        border: 1px solid transparent;
        font-size: 0.75rem;
        transition: 0.2s;
    }

    .custom-textarea:focus {
        background-color: #fff;
        border-color: #198754;
        box-shadow: none;
    }

    .filter-grayscale {
        filter: grayscale(100%);
        opacity: 0.6;
    }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 py-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold text-success"><i class="fas fa-mug-hot me-2"></i>Coffee & Eatery</h4>
                    <div class="text-end">
                        <div class="d-flex align-items-center justify-content-end mb-1">
                            <div class="me-3 text-end">
                                <div class="fw-bold text-dark" style="line-height: 1.2;">
                                    <i class="fas fa-user-circle me-1 text-secondary"></i>
                                    <?= esc(session()->get('nama_user')) ?>
                                </div>
                                <div id="clock" class="text-muted" style="font-size: 0.75rem;"></div>
                            </div>

                            <a href="<?= site_url('admin/dashboard') ?>"
                                class="btn btn-success btn-sm rounded-pill px-3 fw-bold me-2 shadow-sm">
                                <i class="fas fa-home me-1"></i> Dashboard
                            </a>

                            <button type="button"
                                class="btn btn-primary btn-sm rounded-pill px-3 fw-bold me-2 shadow-sm"
                                data-bs-toggle="modal" data-bs-target="#cetakClosingModal">
                                <i class="fas fa-print me-1"></i> Cetak Closing
                            </button>

                            <button type="button"
                                class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-none fw-bold me-2"
                                data-bs-toggle="modal" data-bs-target="#modalAbsenKru">
                                <i class="fas fa-user-clock me-1"></i> Absen Kru Resto
                            </button>

                            <button type="button"
                                class="btn btn-outline-warning btn-sm rounded-pill px-3 shadow-none fw-bold me-2 position-relative"
                                data-bs-toggle="modal" data-bs-target="#modalAntrean">
                                <i class="fa fa-bell me-1"></i> Antrean Meja
                                <span id="badge-antrean"
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-size: 10px; padding: 0.35em 0.65em;">
                                    <?= $jumlah_antrean ?>
                                </span>
                            </button>

                            <a href="<?= base_url('logout') ?>"
                                class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal"
                                data-bs-target="#logoutModal">
                                <i class="fas fa-sign-out-alt me-1"></i> Keluar
                            </a>
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
                ?>

                <?php if ($cek_opname && $cek_opname->status == 1) : ?>
                <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
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
                <?php endif; ?>

                <script>
                document.addEventListener('DOMContentLoaded',
                    function() {
                        const inputBayar = document.getElementById('input-uang-diterima');
                        if (inputBayar) {
                            inputBayar.addEventListener('input', hitungKembalian);
                        }

                        const inputSearch = document.getElementById('search-produk');
                        if (inputSearch) {
                            inputSearch.addEventListener('keyup', function() {
                                const keyword = inputSearch.value.toLowerCase();
                                const productCards = document.querySelectorAll('.product-card');

                                productCards.forEach(card => {
                                    const namaProduk = card.querySelector('h6').innerText
                                        .toLowerCase();

                                    if (namaProduk.includes(keyword)) {
                                        card.parentElement.style.display = 'block';
                                    } else {
                                        card.parentElement.style.display = 'none';
                                    }
                                });
                            });
                        }
                    });
                // Tangkap data dari PHP
                // 1. Ambil data resep yang dikirim dari Controller
                const dataResepFull = <?= json_encode($data_resep) ?>;
                const daftarIdResep = <?= json_encode($list_id_resep) ?>;

                function addWithQty(id, nama, harga, kategori, stok, jenis) {
                    // Ambil elemen input quantity berdasarkan ID produk
                    const qtyInput = document.getElementById('qty_input_' + id);
                    const jumlahInput = parseInt(qtyInput.value);

                    // Validasi standar: Jangan sampai input kosong atau di bawah 1
                    if (isNaN(jumlahInput) || jumlahInput < 1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Input Tidak Valid',
                            text: 'Masukkan jumlah minimal 1',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        qtyInput.value = 1;
                        return;
                    }

                    // Cek apakah produk ini punya resep
                    const punyaResep = daftarIdResep.includes(id.toString()) || daftarIdResep.includes(parseInt(id));

                    if (punyaResep) {
                        // --- VALIDASI STOK BAHAN BAKU BERDASARKAN JUMLAH INPUT ---
                        const bahanList = dataResepFull[id];
                        let aman = true;
                        let pesan = "";

                        if (bahanList) {
                            bahanList.forEach(b => {
                                // Kalkulasi: Kebutuhan x jumlah yang diinput
                                const totalButuh = parseFloat(b.butuh) * jumlahInput;
                                if (parseFloat(b.stok_bahan) < totalButuh) {
                                    aman = false;
                                    pesan =
                                        `Gagal! Stok ${b.nama_bahan} tidak cukup untuk membuat ${jumlahInput} ${nama}. (Tersedia: ${b.stok_bahan})`;
                                }
                            });
                        }

                        if (!aman) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Bahan Tidak Cukup',
                                text: pesan
                            });
                            return;
                        }
                    } else {
                        // --- VALIDASI RETAIL ---
                        if (stok < jumlahInput) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Stok Kurang',
                                text: `Stok "${nama}" hanya sisa ${stok}.`
                            });
                            return;
                        }
                    }

                    // Jika lolos semua validasi, panggil addToCart satu kali dengan membawa jumlahInput
                    addToCart(id, nama, harga, kategori, stok, jenis, jumlahInput);

                    // Reset input qty kembali ke 1
                    qtyInput.value = 1;
                }


                // PASTIKAN let waktuOrder = null; ada di paling atas script, di luar fungsi
                let cart = [];
                let waktuOrder = null;

                // Tambahkan parameter 'jenis' di akhir
                function addToCart(id, nama, harga, kategori, stok, jenis, qtyInput = 1) {
                    try {
                        // Cek apakah produk ini punya resep
                        const punyaResep = daftarIdResep.includes(id.toString()) || daftarIdResep.includes(parseInt(
                            id));

                        if (punyaResep) {
                            // JIKA PUNYA RESEP: Cek stok bahan bakunya
                            const bahanList = dataResepFull[id];
                            let aman = true;
                            let pesan = "";

                            if (bahanList) {
                                bahanList.forEach(b => {
                                    // VALIDASI STOK: Kebutuhan x jumlah yang diinput (qtyInput)
                                    const totalKebutuhan = parseFloat(b.butuh) * qtyInput;
                                    if (parseFloat(b.stok_bahan) < totalKebutuhan) {
                                        aman = false;
                                        pesan =
                                            `Maaf, stok ${b.nama_bahan} tidak cukup untuk membuat ${qtyInput} ${nama}. (Tersedia: ${b.stok_bahan})`;
                                    }
                                });
                            }

                            if (!aman) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Bahan Habis',
                                    text: pesan
                                });
                                return; // BLOKIR: Jangan lanjut ke bawah
                            }
                        } else {
                            // JIKA PRODUK RETAIL (Kering/Snack): Cek stok fisik produk itu sendiri
                            if (stok < qtyInput) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Stok Habis',
                                    text: `Maaf, stok "${nama}" tidak mencukupi untuk jumlah tersebut.`,
                                });
                                return;
                            }
                        }

                        // --- 2. LOGIKA NAMA FINAL (Hot/Ice) ---
                        let namaFinal = nama;
                        if (kategori === 'minuman') {
                            const suhuDipilih = document.querySelector(`input[name="suhu_${id}"]:checked`);
                            if (suhuDipilih) {
                                namaFinal += ` - ${suhuDipilih.value}`;
                            }
                        }

                        // --- 3. AMBIL CATATAN ---
                        const catatanInput = document.getElementById('catatan_' + id);
                        const catatan = catatanInput ? catatanInput.value : '';

                        // --- 4. CARI DI CART (TAMBAHKAN QTY) ---
                        const existingItem = cart.find(item =>
                            item.id === id &&
                            item.catatan === catatan &&
                            item.nama === namaFinal
                        );

                        if (existingItem) {
                            // Tambahkan qty sesuai dengan inputan kasir
                            existingItem.qty += qtyInput;
                        } else {
                            // Masukkan data baru dengan qty sesuai inputan
                            cart.push({
                                id: id,
                                nama: namaFinal,
                                harga: harga,
                                qty: qtyInput,
                                catatan: catatan
                            });
                        }

                        if (catatanInput) catatanInput.value = '';

                        // --- 5. UPDATE TAMPILAN ---
                        renderCart();

                        // Opsional: Notifikasi kecil agar kasir tahu barang masuk
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: `${qtyInput} ${namaFinal} masuk keranjang`,
                            showConfirmButton: false,
                            timer: 1500
                        });

                    } catch (error) {
                        console.error("Error pada addToCart:", error);
                    }
                }

                function removeItem(index) {
                    cart.splice(index, 1);
                    if (cart.length === 0) {
                        waktuOrder = null; // Reset jika keranjang benar-benar kosong
                    }
                    renderCart();
                }

                function renderCart() {
                    const cartContainer = document.getElementById('cart-items');
                    if (!cartContainer) return;

                    cartContainer.innerHTML = '';
                    let subtotal = 0;

                    cart.forEach((item, index) => {
                        // HITUNG HARGA: (Harga Produk + Tambahan Harga) x Qty
                        const extraHarga = parseInt(item.extra_harga || 0);
                        const totalHargaSatuan = parseInt(item.harga) + extraHarga;
                        const itemSubtotal = totalHargaSatuan * item.qty;

                        subtotal += itemSubtotal;

                        cartContainer.innerHTML += `
                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                            <div style="flex: 1;">
                                <span class="fw-bold d-block">${item.nama}</span>
                                <small class="text-muted">
                                    Rp ${totalHargaSatuan.toLocaleString('id-ID')} 
                                    ${extraHarga > 0 ? `<span class="text-success">(+${extraHarga.toLocaleString('id-ID')})</span>` : ''}
                                </small>
                                
                                ${item.catatan ? `<br><small class="text-danger fst-italic">"${item.catatan}"</small>` : ''}
                                
                                <br>
                                <button class="btn btn-link btn-sm p-0 text-decoration-none text-primary" onclick="bukaKustomisasi(${index})">
                                    <i class="bi bi-pencil-square"></i> Kustom
                                </button>
                            </div>
            
                    <div class="d-flex align-items-center gap-2 px-3">
                        <button class="btn btn-outline-secondary btn-sm rounded-circle" 
                            onclick="updateQty(${index}, -1)" 
                            style="width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-dash"></i>
                        </button>
                        
                        <span class="fw-bold" style="min-width: 20px; text-align: center;">${item.qty}</span>
                        
                        <button class="btn btn-outline-secondary btn-sm rounded-circle" 
                            onclick="updateQty(${index}, 1)" 
                            style="width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>

                    <div class="text-end" style="min-width: 100px;">
                        <span class="fw-bold d-block mb-1">Rp ${itemSubtotal.toLocaleString('id-ID')}</span>
                        <button class="btn btn-link btn-sm text-danger p-0 text-decoration-none" onclick="removeItem(${index})">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                `;
                    });

                    let tax = subtotal * 0;
                    let totalAkhir = subtotal + tax;

                    const elSubtotal = document.getElementById('subtotal');
                    const elTax = document.getElementById('tax');
                    const elTotal = document.getElementById('total');
                    const elEmpty = document.getElementById('empty-cart');

                    if (elSubtotal) elSubtotal.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
                    if (elTax) elTax.innerText = 'Rp ' + tax.toLocaleString('id-ID');
                    if (elTotal) elTotal.innerText = 'Rp ' + totalAkhir.toLocaleString('id-ID');
                    if (elEmpty) elEmpty.style.display = cart.length > 0 ? 'none' : 'block';
                }

                function updateQty(index, change) {
                    // 1. Ambil item dari array cart berdasarkan index
                    let item = cart[index];
                    let newQty = item.qty + change;

                    // 2. Jika dikurangi sampai di bawah 1, panggil fungsi hapus
                    if (newQty < 1) {
                        removeItem(index);
                        return;
                    }

                    // 3. Jika menambah (+), cek validasi stok bahan baku (SIMRS Logic)
                    if (change > 0) {
                        // Cek apakah item ini jenis produk resep
                        const punyaResep = daftarIdResep.includes(item.id.toString()) || daftarIdResep.includes(
                            parseInt(item.id));

                        if (punyaResep) {
                            const bahanList = dataResepFull[item.id];
                            let stokCukup = true;
                            let pesanGagal = "";

                            if (bahanList) {
                                bahanList.forEach(b => {
                                    let butuhSekarang = parseFloat(b.butuh) * newQty;
                                    if (parseFloat(b.stok_bahan) < butuhSekarang) {
                                        stokCukup = false;
                                        pesanGagal = `Bahan ${b.nama_bahan} tidak cukup untuk ${newQty} porsi.`;
                                    }
                                });
                            }

                            if (!stokCukup) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Stok Tidak Cukup',
                                    text: pesanGagal
                                });
                                return; // Berhenti di sini, jangan update qty
                            }
                        }
                    }

                    // 4. Update qty di array dan gambar ulang tampilan
                    item.qty = newQty;
                    renderCart();
                }

                function bukaModalBayar() {
                    if (cart.length === 0) return alert("Keranjang masih kosong!");

                    // Pastikan fungsi resetPromo sudah ada di bawah sebelum dipanggil di sini
                    resetPromo();

                    let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
                    let pajak = Math.ceil(subtotal * 0); // //Sewaktu" jika ada PPN 
                    let totalFix = subtotal + pajak;

                    // Menggunakan JQuery agar lebih stabil
                    $('#display-total-modal').text('Rp ' + totalFix.toLocaleString('id-ID'));
                    $('#input-uang-diterima').val('');
                    $('#display-kembali').text('Rp 0');

                    const modal = new bootstrap.Modal(document.getElementById('modalBayar'));
                    modal.show();
                }

                function hitungTotalAkhir() {
                    let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
                    let pajak = Math.ceil(subtotal * 0); // Jika ada PPN 
                    let totalAwal = subtotal + pajak;

                    // AMBIL NILAI DISKON MANUAL
                    let diskonManual = parseInt(document.getElementById('input-diskon').value) || 0;

                    // AMBIL NILAI REDEEM POIN
                    let poinDipakai = parseInt(document.getElementById('input-poin-dipakai').value) || 0;
                    let maxPoin = parseInt(document.getElementById('max-poin-member').innerText) || 0;

                    // Validasi poin melebihi saldo
                    if (poinDipakai > maxPoin) {
                        $('#error-poin').show();
                        poinDipakai = maxPoin;
                        $('#input-poin-dipakai').val(maxPoin);
                    } else {
                        $('#error-poin').hide();
                    }

                    // HITUNG DISKON POIN (1 Poin = Rp 1000)
                    let nilaiDiskonPoin = poinDipakai * 1000;

                    // HITUNG TOTAL SETELAH DISKON KESELURUHAN
                    let totalSetelahDiskon = totalAwal - diskonManual - nilaiDiskonPoin;
                    if (totalSetelahDiskon < 0) totalSetelahDiskon = 0;

                    // Update Tampilan Harga di Modal
                    $('#display-total-modal').text('Rp ' + totalSetelahDiskon.toLocaleString('id-ID'));

                    // Update Kembalian
                    let bayar = parseInt(document.getElementById('input-uang-diterima').value) || 0;
                    let sisa = bayar - totalSetelahDiskon;
                    $('#display-kembali').text('Rp ' + (sisa > 0 ? sisa.toLocaleString('id-ID') : 0));
                }

                // Bikin fungsi setUang langsung manggil hitungTotalAkhir
                function setUang(nominal) {
                    document.getElementById('input-uang-diterima').value = nominal;
                    hitungTotalAkhir(); // <-- Pastikan ini memanggil hitungTotalAkhir
                }

                function hitungKembalian() {
                    let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
                    let totalFix = subtotal + (subtotal * 0); //Sewaktu" jika ada PPN
                    let bayar = parseInt(document.getElementById('input-uang-diterima').value) || 0;
                    let sisa = bayar - totalFix;

                    document.getElementById('label-kembalian').innerText = 'Rp ' + (sisa > 0 ? sisa.toLocaleString(
                        'id-ID') : 0);
                }
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

                <div id="cart-container" class="flex-grow-1 overflow-auto mb-3">
                    <div class="text-center text-muted py-5" id="empty-cart">
                        <p>Belum ada produk dipilih</p>
                    </div>

                    <div id="cart-items">
                    </div>
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
                        class="btn btn-dark w-100 py-3 mt-2 rounded-3 fw-bold shadow-sm">Bayar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Proses Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="text-center">
                        <p class="text-muted mb-1">Total Pembayaran</p>
                        <small class="text-muted text-decoration-line-through" id="label-subtotal-awal"
                            style="display:none;"></small>
                        <h2 class="text-success fw-bold mb-4" id="display-total-modal">Rp 0</h2>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6 col-sm-3 col-md-6"><button id="btn-tunai"
                                class="btn btn-success w-100 py-2 py-md-3" onclick="pilihMetode('Tunai')">Tunai</button>
                        </div>
                        <div class="col-6 col-sm-3 col-md-6"><button id="id-qris"
                                class="btn btn-outline-secondary w-100 py-2 py-md-3"
                                onclick="pilihMetode('QRIS')">QRIS</button></div>
                        <div class="col-6 col-sm-3 col-md-6"><button id="id-trans"
                                class="btn btn-outline-secondary w-100 py-2 py-md-3"
                                onclick="pilihMetode('Transfer')">Transfer</button></div>
                        <div class="col-6 col-sm-3 col-md-6"><button id="id-edc"
                                class="btn btn-outline-secondary w-100 py-2 py-md-3"
                                onclick="pilihMetode('EDC')">EDC</button></div>
                    </div>

                    <div class="text-start mb-3">
                        <label class="small text-muted fw-bold">Kode Promo</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="input-kode-promo" class="form-control" placeholder="SENJASORE">
                            <button class="btn btn-primary" type="button" onclick="cekPromoAjax()">Cek</button>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="small text-danger fw-bold">Diskon</label>
                            <input type="number" id="input-diskon"
                                class="form-control form-control-sm text-danger fw-bold" placeholder="0"
                                oninput="hitungTotalAkhir()">
                        </div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Uang Diterima</label>
                            <input type="number" id="input-uang-diterima" class="form-control form-control-sm fw-bold"
                                placeholder="0" oninput="hitungTotalAkhir()">
                        </div>
                    </div>

                    <div id="area-redeem-poin" class="mt-3" style="display: none;">
                        <label class="small text-success fw-bold">Tukar Poin (<span id="max-poin-member">0</span>
                            Pts)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" id="input-poin-dipakai" class="form-control text-success fw-bold"
                                placeholder="0" oninput="hitungTotalAkhir()">
                            <span class="input-group-text bg-success text-white" style="font-size: 0.7rem;">1
                                Pts=1rb</span>
                        </div>
                    </div>

                    <div class="d-flex gap-1 mt-3">
                        <button class="btn btn-sm btn-light border flex-grow-1" onclick="setUang(10000)">10rb</button>
                        <button class="btn btn-sm btn-light border flex-grow-1" onclick="setUang(50000)">50rb</button>
                        <button class="btn btn-sm btn-light border flex-grow-1" onclick="setUang(100000)">100rb</button>
                    </div>

                    <div class="bg-light p-2 rounded-3 d-flex justify-content-between align-items-center mt-3">
                        <span class="text-success small fw-bold">Kembalian</span>
                        <h5 class="mb-0 fw-bold text-success" id="display-kembali">Rp 0</h5>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button class="btn btn-dark w-100 py-3 rounded-3 fw-bold shadow-sm"
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

                <form id="formAbsenKru" method="POST" action="<?= base_url('admin/absensi/absen_pakai_pin') ?>">
                    <?= csrf_field() ?>
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
                        <button type="submit"
                            class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm transition-all hover-scale">
                            KONFIRMASI ABSENSI <i class="fas fa-check-circle ms-1"></i>
                        </button>
                        <button type="button" class="btn btn-link w-100 text-muted text-decoration-none mt-1 small"
                            data-bs-dismiss="modal" style="font-size: 11px;">
                            Batalkan
                        </button>
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
                        <span class="badge rounded-pill bg-white text-dark border border-warning"
                            style="font-size: 10px;">
                            Total: <span id="count-antrean-modal"><?= $jumlah_antrean ?></span>
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
                                <?php if(!empty($antrean_meja)): ?>
                                <?php foreach($antrean_meja as $a): ?>
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
                                        <a href="<?= base_url('kasir/tarik_order_meja/'.$a['id_temp']) ?>"
                                            class="btn btn-warning btn-xs rounded-pill px-2 fw-bold shadow-sm"
                                            style="font-size: 11px; padding: 2px 8px;">
                                            Tarik
                                        </a>
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
        const keyword = $('#cari-member').val();

        if (keyword === "") {
            return Swal.fire("Ops!", "Masukkan nama atau nomor telp!", "warning");
        }

        $.ajax({
            url: "<?= base_url('admin/cari_member_kasir') ?>",
            type: "POST",
            data: {
                keyword: keyword
            },
            dataType: "JSON",
            success: function(res) {
                if (res.status === 'success') {
                    $('#id-member-terpilih').val(res.data.id_member);
                    $('#nama-member-kasir').html(
                        res.data.nama_member +
                        " <span class='badge bg-warning text-dark ms-2'>" + res.data.level_vip +
                        "</span>" +
                        " <span class='badge bg-info text-dark ms-1'>" + res.data.total_poin +
                        " Poin</span>"
                    );
                    $('#info-member-terpilih').show();
                    $('#cari-member').hide();

                    // 👇👇 BAGIAN TAMBAHAN UNTUK MENGAKTIFKAN REDEEM POIN 👇👇
                    $('#max-poin-member').text(res.data.total_poin); // Update teks batas maksimal poin
                    $('#input-poin-dipakai').attr('max', res.data.total_poin).val(
                        ''); // Set atribut max dan kosongkan input
                    $('#area-redeem-poin').show(); // Munculkan kolom redeem poin di modal bayar
                    // 👆👆 ================================================== 👆👆

                    Swal.fire({
                        icon: 'success',
                        title: 'Member Ditemukan',
                        text: 'Member: ' + res.data.nama_member,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire("Gagal", "Member tidak ditemukan!", "error");
                }
            },
            error: function() {
                Swal.fire("Eror", "Gagal menghubungi server!", "error");
            }
        });
    }

    // ==========================================
    // JQUERY DOCUMENT READY (Dijalankan saat halaman selesai loading)
    // ==========================================
    $(document).ready(function() {

        // ------------------------------------------
        // 1. LOGIKA ABSENSI KRU (Yang bos minta tadi)
        // ------------------------------------------
        const urlMasuk = "<?= base_url('admin/absensi/absen_pakai_pin') ?>";
        const urlPulang = "<?= base_url('admin/absensi/absen_pulang_pin') ?>";

        $('input[name="mode_absen"]').on('change', function() {
            if ($(this).val() === 'pulang') {
                $('#div_shift').slideUp();
                $('#formAbsenKru').attr('action', urlPulang);
            } else {
                $('#div_shift').slideDown();
                $('#formAbsenKru').attr('action', urlMasuk);
            }
        });

        // ------------------------------------------
        // 2. SIMPAN MEMBER BARU VIA AJAX
        // ------------------------------------------
        $('#formTambahMember').on('submit', function(e) {
            e.preventDefault();

            let btnSubmit = $(this).find('button[type="submit"]');
            btnSubmit.prop('disabled', true).text('Menyimpan...');

            $.ajax({
                url: "<?= base_url('kasir/tambah_member') ?>",
                type: "POST",
                data: $(this).serialize(),
                dataType: "JSON",
                success: function(response) {
                    btnSubmit.prop('disabled', false).text('Simpan Member');

                    if (response.status === 'success') {
                        Swal.fire('Berhasil!', response.msg, 'success');

                        let noHpBaru = $('#formTambahMember input[name="no_telepon"]')
                            .val();
                        $('#pencarian_member').val(noHpBaru);
                        $('#modalTambahMember').modal('hide');
                        $('#formTambahMember')[0].reset();
                        $('#pencarian_member').trigger('keyup');
                    } else {
                        Swal.fire('Gagal!', response.msg, 'error');
                    }
                },
                error: function() {
                    btnSubmit.prop('disabled', false).text('Simpan Member');
                    Swal.fire('Error!', 'Waduh bos, sistem eror pas simpan member!',
                        'error');
                }
            });
        });

        // ------------------------------------------
        // 3. TOMBOL BATALKAN MEMBER
        // ------------------------------------------
        $('#batal_member').on('click', function() {
            $('#pencarian_member').val('');
            $('#id_member_hidden').val('');
            $('#hasil_member').addClass('d-none');

            // Notifikasi kecil di pojok kanan atas
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Member dibatalkan',
                showConfirmButton: false,
                timer: 1500
            });
        });

    });

    function resetMemberKasir() {

        $('#cari-member').val('').show();
        $('#id-member-terpilih, #id_member_hidden').val('');
        $('#nama-member-kasir').text('');
        $('#info-member-terpilih').hide();
        $('#area-redeem-poin').hide();
        $('#input-poin-dipakai').val('');

        // (Opsional) Munculkan notifikasi kecil
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Member dibatalkan',
            showConfirmButton: false,
            timer: 1000
        });
    }
    </script>

    <?php if (session()->getFlashdata('absen_sukses')) : 
    $data_sukses = session()->getFlashdata('absen_sukses'); ?>
    <div class="modal fade" id="modalSuksesAbsen" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center"
                style="border-radius: 15px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                <div class="modal-body p-4">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center text-success bg-success bg-opacity-10 rounded-circle"
                        style="width: 60px; height: 60px;">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">Absen Berhasil!</h5>
                    <p class="small text-muted mb-3">Selamat Bekerja & Jaga Kesehatan</p>

                    <div class="p-3 bg-light rounded-3 text-start mb-2" style="font-size: 13px;">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Nama:</span>
                            <span class="fw-bold text-dark"><?= $data_sukses['nama'] ?? '-' ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Tercatat Jam:</span>
                            <span class="fw-bold text-success"><?= $data_sukses['jam'] ?? '-' ?> WIB</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Shift:</span>
                            <span class="fw-bold text-primary">
                                <?php 
                                // Gunakan null coalescing (??) agar tidak error jika key tidak ada
                                    $shift = $data_sukses['shift'] ?? $data_sukses['nama_shift'] ?? '-';
                                    echo strtoupper($shift);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('peringatan_absen')) : 
    $data_warn = session()->getFlashdata('peringatan_absen'); ?>
    <<div class="modal fade" id="modalPeringatanAbsen" tabindex="-1" aria-hidden="true">
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
            // Ambil ID Member (pastikan ID elemennya sesuai dengan HTML bos)
            const idMember = $('#id_member_hidden').val() || $('#id-member-terpilih').val();
            const idPromo = $('#id-promo-terpilih').val();

            const poinDipakai = parseInt($('#input-poin-dipakai').val()) || 0;

            const data = {
                total: total,
                bayar: bayar,
                diskon: diskon,
                id_promo: idPromo,
                metode: metodeTerpilih,
                order_at: typeof waktuOrder !== 'undefined' ? waktuOrder : '',
                cart: cart,
                id_member: idMember,
                poin_dipakai: poinDipakai
            };

            fetch('<?= base_url('kasir/bayar') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pembayaran telah diproses.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Cetak struk dengan membawa data diskon
                            cetakStruk(total, bayar, diskon);

                            // =========================================
                            // 👇👇 RESET MEMBER SETELAH BAYAR SUKSES 👇👇
                            // =========================================
                            // Sembunyikan info member dan tampilkan kembali input cari
                            $('#info-member-terpilih, #hasil_member').hide().addClass('d-none');
                            $('#cari-member, #pencarian_member').show().val('');

                            // Kosongkan ID dan Nama
                            $('#id-member-terpilih, #id_member_hidden').val('');
                            $('#nama-member-kasir, #nama_member_terpilih').text('');

                            // (Catatan: Kalau bos punya fungsi reset keranjang, panggil di sini juga)
                            // contoh: cart = []; updateCartUI();
                            // =========================================
                        });
                    } else {
                        Swal.fire('Gagal', result.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                });
        }
        let metodeTerpilih = 'Tunai';


        function konfirmasiPembayaran() {
            let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
            let totalPajak = Math.ceil(subtotal + (subtotal * 0));

            // AMBIL NILAI DISKON & POIN
            let diskonManual = parseInt(document.getElementById('input-diskon').value) || 0;
            let poinDipakai = parseInt(document.getElementById('input-poin-dipakai').value) || 0;
            let diskonPoin = poinDipakai * 1000; // 1 Poin = 1000

            let totalDiskonKeseluruhan = diskonManual + diskonPoin;

            // TOTAL AKHIR (Fix)
            let totalFix = totalPajak - totalDiskonKeseluruhan;
            if (totalFix < 0) totalFix = 0;

            let uangDiterimaInput = parseInt(document.getElementById('input-uang-diterima').value) || 0;
            let uangDiterima;
            let uangKembalian = 0;

            if (metodeTerpilih === 'Tunai') {
                if (uangDiterimaInput < totalFix) {
                    return Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Gagal',
                        html: `Uang kurang: <b style="color:red">Rp ${(totalFix - uangDiterimaInput).toLocaleString()}</b>`,
                        confirmButtonColor: '#d33'
                    });
                }
                uangDiterima = uangDiterimaInput;
                uangKembalian = uangDiterima - totalFix;
            } else {
                uangDiterima = totalFix;
                uangKembalian = 0;
            }

            Swal.fire({
                title: 'Konfirmasi Transaksi',
                html: `
        <table class="table table-sm text-start">
            <tr><td>Metode</td><td>: <b>${metodeTerpilih}</b></td></tr>
            <tr><td>Total Awal</td><td>: <b>Rp ${totalPajak.toLocaleString('id-ID')}</b></td></tr>
            ${diskonManual > 0 ? `<tr class="text-danger"><td>Diskon Manual</td><td>: -<b>Rp ${diskonManual.toLocaleString('id-ID')}</b></td></tr>` : ''}
            ${diskonPoin > 0 ? `<tr class="text-success"><td>Tukar Poin (${poinDipakai} Pts)</td><td>: -<b>Rp ${diskonPoin.toLocaleString('id-ID')}</b></td></tr>` : ''}
            <tr class="table-active"><td>TOTAL AKHIR</td><td>: <b class="text-success">Rp ${totalFix.toLocaleString('id-ID')}</b></td></tr>
            <tr><td>Bayar</td><td>: <b>Rp ${uangDiterima.toLocaleString('id-ID')}</b></td></tr>
            <tr><td>Kembalian</td><td>: <b>Rp ${uangKembalian.toLocaleString('id-ID')}</b></td></tr>
        </table>
    `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Ya, Simpan & Cetak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kita oper total diskon keseluruhan ke fungsi simpan
                    eksekusiSimpan(totalFix, uangDiterima, totalDiskonKeseluruhan);
                }
            });
        }

        function pilihMetode(metode) {
            metodeTerpilih = metode;

            const btnTunai = document.getElementById('btn-tunai');
            const btnQris = document.getElementById('id-qris');
            const btnTrf = document.getElementById('id-trans');
            const btnEti = document.getElementById('id-edc');

            // Reset semua tombol ke warna sekunder
            btnTunai.className = 'btn btn-outline-secondary w-100 py-3';
            if (btnQris) btnQris.className = 'btn btn-outline-secondary w-100 py-3';
            if (btnTrf) btnTrf.className = 'btn btn-outline-secondary w-100 py-3';
            if (btnEti) btnEti.className = 'btn btn-outline-secondary w-100 py-3';

            // Berikan kelas success pada tombol yang dipilih
            if (metode === 'Tunai') {
                btnTunai.className = 'btn btn-success w-100 py-3';
            } else if (metode === 'QRIS') {
                btnQris.className = 'btn btn-success w-100 py-3';
                tampilkanQRIS();
            } else if (metode === 'EDC') {
                btnEti.className = 'btn btn-success w-100 py-3';
                tampilkanEDC();
            } else if (metode === 'Transfer') {
                btnTrf.className = 'btn btn-success w-100 py-3';
                tampilkanTransfer();
            }
        }

        function tampilkanQRIS() {
            Swal.fire({
                title: 'Pembayaran QRIS',
                text: 'Silahkan scan kode QR di bawah ini',
                imageUrl: '<?= base_url('assets/img/qris.jpg') ?>',
                // Hapus imageWidth & imageHeight dari sini agar dikontrol penuh oleh CSS
                imageAlt: 'QRIS Code',
                confirmButtonText: 'OKE',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                customClass: {
                    image: 'responsive-qris-img', // Memanggil CSS di atas
                    popup: 'swal2-popup-custom' // Memanggil CSS di atas
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    konfirmasiPembayaran();
                } else {
                    pilihMetode('Tunai');
                }
            });
        }

        function tampilkanEDC() {
            Swal.fire({
                title: 'Pembayaran Mesin EDC',
                text: 'Silahkan gesek/masukkan kartu pelanggan ke mesin EDC',
                icon: 'info',
                confirmButtonText: 'OKE',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    konfirmasiPembayaran();
                } else {
                    pilihMetode('Tunai');
                }
            });
        }

        function tampilkanTransfer() {
            Swal.fire({
                title: 'Pembayaran Transfer',
                text: 'Silahkan transfer ke nomor rekening yang tersedia',
                imageUrl: '<?= base_url('assets/img/transfer.png') ?>',
                imageWidth: 500,
                imageHeight: 300,
                confirmButtonText: 'OKE',
                showCancelButton: true,
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    konfirmasiPembayaran();
                } else {
                    pilihMetode('Tunai');
                }
            });
        }

        function cetakStruk(total, bayar, diskon = 0) {
            let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
            let pajak = Math.ceil(subtotal * 0); //Jika Sewaktu" ada PPN
            let totalSebelumDiskon = subtotal + pajak;

            let metodeStruk = metodeTerpilih || 'Tunai';
            let invoiceNumber = `INV-${Date.now().toString().slice(-6)}`;
            let tgl = new Date().toLocaleDateString('id-ID');
            let jam = new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });

            // AMBIL WAKTU ORDER (Tanpa Detik)
            let jamOrderTampil = waktuOrder ? waktuOrder.split('.').slice(0, 2).join('.') : jam;
            let jamBayarTampil = jam.replace(':', '.'); // Samakan format pemisah jam

            // 1. Fungsi Konten Struk Pembayaran
            const buatStrukBayar = (label) => {
                let html = `
        <div class="struk-container">
            <div class="text-center">
                <div style="border: 1px solid #000; padding: 2px; font-size: 8px; margin-bottom: 5px;">*** ${label} ***</div>

                <?php if (!empty($setting['logo'])) : ?>
                <img src="<?= base_url('uploads/img/' . $setting['logo']) ?>" 
                     style="max-width: 60px; height: auto; margin-bottom: 5px; filter: grayscale(100%);">
                <?php endif; ?> 

                <h2 style="margin:0; font-size: 14px;">KasirKita</h2>
                <small style="font-size: 10px;">Perum PT Sumbar Mas Blok B2</small><br>
                <div class="line"></div>
                <table style="font-size: 10px; width: 100%;">
                    <tr><td style="width: 40%;">Tgl</td><td>: ${tgl}</td></tr>
                    <tr><td>Kasir</td><td>: <?= esc(session()->get('nama_user')) ?></td></tr>
                    <tr><td>Pelayan</td><td>: <?= esc(session()->get('nama_user')) ?></td></tr>
                    <tr><td>Order Masuk</td><td>: ${jamOrderTampil}</td></tr>
                    <tr><td>Waktu Bayar</td><td>: ${jamBayarTampil}</td></tr>
                    <tr>
                        <td colspan="2" class="text-center" style="font-weight:bold; padding-top:15px; font-size: 18px;">
                            No. Order: ${invoiceNumber}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="line"></div>
            <table>
        `;

                cart.forEach(item => {
                    html += `
            <tr><td colspan="2">${item.nama}</td></tr>
            <tr>
                <td>${item.qty} x ${item.harga.toLocaleString()}</td>
                <td class="text-right">${(item.qty * item.harga).toLocaleString()}</td>
            </tr>`;
                });

                html += `
            </table>
            <div class="line"></div>
            <table>
                <tr><td>Subtotal + PPN</td><td class="text-right">${totalSebelumDiskon.toLocaleString()}</td></tr>
                ${diskon > 0 ? `<tr class="text-danger"><td>Diskon</td><td class="text-right"> ${diskon.toLocaleString()}</td></tr>` : ''}
                <tr style="font-weight:bold; font-size:14px;"><td>TOTAL AKHIR</td><td class="text-right">${total.toLocaleString()}</td></tr>
                <tr><td>Bayar (${metodeStruk})</td><td class="text-right">${bayar.toLocaleString()}</td></tr>
                <tr><td>Kembali</td><td class="text-right">${(bayar - total).toLocaleString()}</td></tr>
            </table>
            <div class="line"></div>
            <div class="text-center"><strong>Terima Kasih</strong><br><small>ig : @senjacoffee</small></div>
        </div>`;
                return html;
            };

            // 2. Fungsi Konten Struk Dapur
            const buatStrukDapur = () => {
                let html = `
        <div class="struk-container">
            <div class="text-center">
                <div style="border: 2px solid #000; padding: 5px; font-weight: bold; margin-bottom: 10px;">*** PESANAN DAPUR ***</div>
                <table style="font-size: 11px;">
                    <tr><td>No: ${invoiceNumber}</td><td class="text-right">Masuk: ${jamOrderTampil}</td></tr>
                </table>
            </div>
            <div class="line"></div>
            <table style="font-size: 14px; font-weight: bold;">
        `;
                cart.forEach(item => {
                    html +=
                        `<tr><td style="padding: 5px 0; vertical-align:top;">${item.qty} x</td><td>${item.nama}</td></tr>`;
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
            body { margin: 0; padding: 0; background-color: white; }
            .struk-container { 
                font-family: 'Courier New', Courier, monospace; 
                width: 72mm; font-size: 10px; padding: 5mm 2mm; margin: 0 auto;
            }
            table { width: 100%; border-collapse: collapse; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .line { border-top: 1px dashed #000; margin: 5px 0; }
            @media print { .page-break { page-break-after: always; display: block; height: 1px; } }
        </style>
    </head>
    <body>
        ${buatStrukBayar('STRUK PELANGGAN')}
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
            var printContents = document.getElementById('printArea').innerHTML;

            // Gunakan Iframe tersembunyi agar halaman utama tidak ter-reload/rusak
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
            doc.write('<style>body{margin:0;padding:20px;font-family:monospace;}</style>');
            doc.write('</head><body>' + printContents + '</body></html>');
            doc.close();

            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();

            setTimeout(function() {
                document.body.removeChild(printFrame);
            }, 1000);
        }

        function bukaKustomisasi(index) {
            let item = cart[index];

            // Set isi form sesuai data di keranjang
            $('#index-cart-kustom').val(index);
            $('#nama-produk-kustom').text(item.nama); // Pastikan item.nama sesuai dengan key di array cart bos

            // Kalau sebelumnya udah ada catatan/harga kustom, tampilkan lagi
            $('#input-catatan-kustom').val(item.catatan || '');
            $('#input-harga-kustom').val(item.extra_harga || '');

            $('#modalKustomisasi').modal('show');
        }

        // Simpan data kustomisasi kembali ke keranjang
        function simpanKustomisasi() {
            // 1. Ambil data dari input modal
            let index = $('#index-cart-kustom').val();
            let catatan = $('#input-catatan-kustom').val();

            // Pastikan ini diubah jadi angka dengan parseInt
            let extraHarga = parseInt($('#input-harga-kustom').val()) || 0;

            // 2. Simpan ke dalam array cart sesuai indexnya
            cart[index].catatan = catatan;
            cart[index].extra_harga = extraHarga; // Sekarang tersimpan sebagai angka

            // 3. Tutup modal
            $('#modalKustomisasi').modal('hide');

            // 4. PENTING: Refresh tampilan keranjang agar nominal berubah
            // Di sini kita panggil renderCart() karena fungsi itu yang menghitung subtotal
            if (typeof renderCart === "function") {
                renderCart();
            }

            // 5. Update juga total akhir di modal bayar (jika sedang terbuka)
            if (typeof hitungTotalAkhir === "function") {
                hitungTotalAkhir();
            }

            // Notifikasi kecil biar kasir tahu sudah tersimpan
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Kustomisasi disimpan',
                showConfirmButton: false,
                timer: 1000
            });
        }

        var counterLama = parseInt($('#badge-antrean').text()) || 0;

        function alarmOtomatis() {
            $.ajax({
                url: '<?= base_url("kasir/cek_notif_antrean") ?>',
                type: 'GET',
                data: {
                    v: Date.now()
                },
                dataType: 'json',
                success: function(respon) {
                    var angkaSkrg = parseInt(respon.jumlah) || 0;

                    // 1. Update Badge Luar
                    updateTampilanBadge(angkaSkrg);

                    // 2. Jika ada perubahan data nyata dari server
                    if (angkaSkrg !== counterLama) {
                        console.log("Ada perubahan data dari background...");

                        // Jika modal lagi kebuka, update isinya
                        if ($('#modalAntrean').is(':visible')) {
                            refreshKontenModal();
                        }
                        counterLama = angkaSkrg;
                    }
                }
            });
        }

        function refreshKontenModal() {
            $("#isi-antrean-tabel-raw").load(window.location.href + " #isi-antrean-tabel-raw > *", function() {
                var barisNyata = $('#isi-antrean-tabel-raw').find('.baris-antrean').length;

                $('#count-antrean-modal').text(barisNyata);
                updateTampilanBadge(barisNyata);
                counterLama = barisNyata; // Sinkronkan variabel penanda
            });
        }

        function updateTampilanBadge(jumlah) {
            var badge = $('#badge-antrean');
            if (badge.length) {
                badge.text(jumlah);
                if (jumlah > 0) {
                    badge.removeClass('bg-secondary').addClass('bg-danger').show();
                } else {
                    badge.removeClass('bg-danger').addClass('bg-secondary');
                }
            }
        }

        // EKSEKUSI
        $(document).ready(function() {
            // Jalankan interval tiap 3 detik
            setInterval(alarmOtomatis, 3000);

            // Langsung refresh pas modal dibuka
            $('#modalAntrean').on('show.bs.modal', function() {
                refreshKontenModal();
            });
        });
        </script>
        <div class="modal fade" id="modalTambahMember" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-success text-white border-0 py-3" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i> Registrasi Member
                            Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="formTambahMember">
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nama Lengkap Member</label>
                                <input type="text" name="nama_member" class="form-control rounded-3 py-2"
                                    placeholder="Masukkan nama pelanggan..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nomor WhatsApp (Aktif)</label>
                                <input type="number" name="no_telepon" id="no_hp_baru"
                                    class="form-control rounded-3 py-2" placeholder="Contoh: 08123456789" required>
                            </div>
                            <div class="bg-light p-3 rounded-3">
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Member baru akan
                                    langsung
                                    mendapatkan <b>0 Poin</b> secara otomatis setelah terdaftar.</small>
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
                            <hr>
                            <div class="d-flex justify-content-between small fw-bold">
                                <span>GRAND TOTAL:</span>
                                <span class="text-primary">Rp
                                    <?= number_format($rekap['total_pendapatan'] ?? 0, 0, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
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
                    <strong style="font-size: 16px;">SENJA COFFEE</strong><br>
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
                        <td style="text-align: right;">Rp <?= number_format($rekap['total_tunai'] ?? 0, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr>
                        <td>QRIS</td>
                        <td style="text-align: right;">Rp <?= number_format($rekap['total_qris'] ?? 0, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr>
                        <td>TRANSFER</td>
                        <td style="text-align: right;">Rp
                            <?= number_format($rekap['total_transfer'] ?? 0, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <tr>
                        <td>EDC</td>
                        <td style="text-align: right;">Rp <?= number_format($rekap['total_edc'] ?? 0, 0, ',', '.') ?>
                        </td>
                    </tr>
                </table>
                --------------------------------<br>
                <table style="width: 100%; font-size: 12px;">
                    <tr style="font-weight: bold;">
                        <td>TOTAL</td>
                        <td style="text-align: right;">Rp
                            <?= number_format($rekap['total_pendapatan'] ?? 0, 0, ',', '.') ?>
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
</body>

</html>