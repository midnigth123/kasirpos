<?php
/**
 * @var object $toko
 * @var object $info_meja
 * @var object $produk
 * @var string $kode_toko
 * @var int $id_meja
 * */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('icon_kasir.ico') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $toko->nama_toko ?> - Exclusive Digital Menu</title>

    <!-- Font Premium Jakarta Sans & Playfair Display untuk judul mewah -->
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,800;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
    :root {
        --bg-dark-premium: #0f1115;
        /* Background gelap mewah */
        --card-bg-premium: #161920;
        /* Background kartu */
        --gold-primary: #e5c07b;
        /* Emas Premium */
        --gold-gradient: linear-gradient(135deg, #f3d9a2, #c69c54);
        --text-muted-gray: #9ca3af;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--bg-dark-premium);
        color: #ffffff;
        padding-bottom: 160px;
        margin: 0;
        overflow-x: hidden;
    }

    /* Pembatas Lebar Layar di PC */
    .container-luxury {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* HEADER PREMIUM */
    .header-luxury {
        text-align: center;
        padding: 50px 20px 30px;
        background: linear-gradient(to bottom, rgba(22, 25, 32, 0.8), transparent);
        border-bottom: 1px solid rgba(229, 192, 123, 0.1);
        margin-bottom: 30px;
    }

    .shop-logo-luxury {
        width: 75px;
        height: 75px;
        background: var(--gold-gradient);
        border-radius: 50%;
        /* Bulat sempurna lebih elegan */
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #0f1115;
        box-shadow: 0 10px 25px rgba(198, 156, 84, 0.3);
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .brand-title {
        font-family: 'Playfair Display', serif;
        font-weight: 800;
        letter-spacing: 1px;
        color: #ffffff;
        margin-bottom: 10px;
    }

    .table-badge-luxury {
        background: rgba(229, 192, 123, 0.1);
        color: var(--gold-primary);
        padding: 6px 20px;
        border-radius: 100px;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 2px;
        display: inline-block;
        border: 1px solid rgba(229, 192, 123, 0.3);
        text-transform: uppercase;
    }

    /* CATEGORY PILLS */
    .category-container {
        display: flex;
        overflow-x: auto;
        gap: 12px;
        padding: 20px 5px 10px;
        scrollbar-width: none;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .category-container {
            justify-content: flex-start;
        }
    }

    .category-container::-webkit-scrollbar {
        display: none;
    }

    .category-pill {
        padding: 10px 24px;
        border-radius: 100px;
        background: #1e222b;
        border: 1px solid rgba(255, 255, 255, 0.05);
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted-gray);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .category-pill.active {
        background: var(--gold-gradient);
        color: #0f1115;
        font-weight: 700;
        box-shadow: 0 5px 15px rgba(198, 156, 84, 0.3);
        border-color: transparent;
    }

    /* CARD MENU PREMIUM */
    .card-menu-luxury {
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 24px;
        background: var(--card-bg-premium);
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        position: relative;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    @media (min-width: 992px) {
        .card-menu-luxury:hover {
            transform: translateY(-10px);
            border-color: rgba(229, 192, 123, 0.4);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }
    }

    .img-container-luxury {
        width: 100%;
        aspect-ratio: 1 / 1;
        position: relative;
        overflow: hidden;
        background: #1e222b;
    }

    .img-menu-luxury {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .card-menu-luxury:hover .img-menu-luxury {
        transform: scale(1.06);
    }

    .badge-habis-luxury {
        position: absolute;
        inset: 0;
        background: rgba(15, 17, 21, 0.8);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 4;
    }

    .btn-plus-luxury {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: var(--gold-gradient);
        color: #0f1115;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        z-index: 5;
        transition: 0.2s;
    }

    .btn-plus-luxury:active {
        transform: scale(0.9);
    }

    .card-body-luxury {
        flex-grow: 1;
        padding: 18px !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .menu-title-luxury {
        font-weight: 600;
        font-size: 14px;
        line-height: 1.4;
        color: #ffffff;
        margin-bottom: 8px;
        min-height: 38px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .price-luxury {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        font-size: 16px;
        color: var(--gold-primary);
    }

    /* CART FLOAT GLASSMORPHISM */
    .cart-float {
        position: fixed;
        bottom: 35px;
        left: 0;
        right: 0;
        margin: 0 auto;
        width: 90%;
        max-width: 420px;
        background: rgba(22, 25, 32, 0.85);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        color: white;
        border: 1px solid rgba(229, 192, 123, 0.25);
        border-radius: 22px;
        padding: 16px 24px;
        display: none;
        justify-content: space-between;
        align-items: center;
        z-index: 1000;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    #qCount {
        background: var(--gold-gradient);
        color: #0f1115;
        min-width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        margin-right: 15px;
    }

    /* MODAL PREMIUM (DARK LUXURY) */
    .modal-content {
        background-color: var(--card-bg-premium) !important;
        color: #ffffff;
        border: 1px solid rgba(229, 192, 123, 0.2) !important;
        border-radius: 28px !important;
    }

    .form-control,
    .form-select {
        background-color: #1e222b !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
        border-radius: 14px !important;
        padding: 12px;
    }

    .form-control::placeholder {
        color: #6b7280;
    }

    .btn-confirm-luxury {
        background: var(--gold-gradient);
        color: #0f1115;
        font-weight: 700;
        letter-spacing: 1px;
        border: none;
        border-radius: 16px;
        padding: 14px;
        transition: 0.3s;
    }

    .btn-confirm-luxury:hover {
        opacity: 0.9;
        color: #0f1115;
    }

    /* SweetAlert Custom Dark Theme */
    .swal2-popup {
        background-color: var(--card-bg-premium) !important;
        color: #ffffff !important;
        border-radius: 24px !important;
    }
    </style>
</head>

<body>

    <div class="container-pos">
        <div class="container-luxury">

            <!-- HEADER -->
            <div class="header-luxury animate__animated animate__fadeInDown">
                <div class="shop-logo-luxury">
                    <i class="fa fa-coffee"></i>
                </div>
                <h2 class="brand-title"><?= $toko->nama_toko ?></h2>
                <div class="table-badge-luxury">
                    <i class="fa fa-chair me-1"></i> Lounge Meja <?= $info_meja->nomor_meja ?? '0' ?>
                </div>

                <!-- CATEGORIES -->
                <div class="category-container">
                    <div class="category-pill active" onclick="filterMenu('all', this)">🍽️ Semua</div>
                    <div class="category-pill" onclick="filterMenu('Makanan', this)">🍔 Makanan</div>
                    <div class="category-pill" onclick="filterMenu('Minuman', this)">🍹 Minuman</div>
                    <div class="category-pill" onclick="filterMenu('Snack', this)">🍟 Snack</div>
                </div>
            </div>

            <!-- MENU GRID -->
            <div class="animate__animated animate__fadeInUp">
                <div class="row g-3 g-md-4 justify-content-center" id="menuContainer">
                    <?php foreach ($produk as $p) : ?>
                    <div class="col-6 col-md-4 col-lg-3 menu-item" data-kategori="<?= $p['kategori'] ?>">
                        <div class="card-menu-luxury">
                            <div class="img-container-luxury">
                                <?php if ($p['stok_realtime'] <= 0) : ?>
                                <div class="badge-habis-luxury">
                                    <span class="badge rounded-pill bg-danger px-3 py-2"
                                        style="font-size: 11px; letter-spacing: 1px;">SOLD OUT</span>
                                </div>
                                <?php endif; ?>

                                <img src="<?= base_url('uploads/produk/' . $p['img']) ?>" class="img-menu-luxury"
                                    onerror="this.src='https://placehold.co/400x400?text=Food'">

                                <?php if ($p['stok_realtime'] > 0) : ?>
                                <button class="btn-plus-luxury"
                                    onclick="addToCart('<?= $p['produk_id'] ?>', '<?= $p['nama_produk'] ?>', <?= $p['harga_jual'] ?>, <?= $p['stok_realtime'] ?>)">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            <div class="card-body-luxury">
                                <div class="menu-title-luxury"><?= $p['nama_produk'] ?></div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div class="price-luxury">Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 10px; font-weight: 500;">Sisa:
                                        <?= (int)$p['stok_realtime'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div> <!-- End container-luxury -->
    </div>

    <!-- FLOATING CART -->
    <div class="cart-float" id="cartFloat" onclick="showCheckout()">
        <div class="d-flex align-items-center">
            <div id="qCount">0</div>
            <div>
                <div class="fw-800 h5 mb-0 text-white" id="totalLabel" style="font-weight: 800;">Rp 0</div>
                <div class="small" style="font-size: 10px; color: var(--text-muted-gray);">Review order list</div>
            </div>
        </div>
        <i class="fa fa-chevron-right opacity-50 text-white"></i>
    </div>

    <!-- MODAL CHECKOUT (Diletakkan paling bawah agar pas di tengah PC & HP) -->
    <div class="modal fade" id="modalCheckout" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered mx-auto" style="max-width: 440px; padding: 15px;">
            <div class="modal-content shadow-lg">
                <div class="modal-body p-4">
                    <h5 class="text-center mb-4"
                        style="font-family: 'Playfair Display', serif; font-weight: 700; font-size: 20px; letter-spacing: 0.5px;">
                        Pesanan Saya</h5>

                    <div class="mb-3">
                        <label class="small fw-bold mb-2 text-white-50">Nama Pemesan</label>
                        <input type="text" id="nama_pemesan" class="form-control" placeholder="Masukkan nama Anda...">
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold mb-2 text-white-50">Metode Pembayaran</label>
                        <select id="metode_bayar" class="form-select">
                            <option value="Tunai">💵 Tunai (Bayar di Kasir)</option>
                            <option value="QRIS">📱 QRIS (Bayar di Kasir)</option>
                        </select>
                    </div>

                    <div id="listCheckout" class="mb-4" style="max-height: 220px; overflow-y: auto;"></div>

                    <div class="p-3 rounded-4 mb-4 d-flex justify-content-between align-items-center border"
                        style="background-color: #1e222b; border-color: rgba(229,192,123,0.15) !important;">
                        <span class="fw-bold text-white-50 small">SUBTOTAL</span>
                        <span class="h4 mb-0" id="grandTotal"
                            style="font-family: 'Playfair Display', serif; font-weight: 800; color: var(--gold-primary);">Rp
                            0</span>
                    </div>

                    <button class="btn btn-confirm-luxury w-100 py-3 shadow" onclick="kirimPesanan()">KONFIRMASI
                        PESANAN</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    let cart = [];

    function filterMenu(kat, el) {
        $('.category-pill').removeClass('active');
        $(el).addClass('active');
        if (kat === 'all') {
            $('.menu-item').fadeIn(400);
        } else {
            $('.menu-item').hide();
            $('.menu-item[data-kategori="' + kat + '"]').fadeIn(400);
        }
    }

    function addToCart(id, nama, harga, stokTersedia) {
        let item = cart.find(x => x.produk_id === id);
        let qtySekarang = item ? item.qty : 0;
        if (qtySekarang + 1 > stokTersedia) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Terbatas',
                text: 'Maaf, stok item ini tidak mencukupi.'
            });
            return;
        }
        if (item) {
            item.qty++;
        } else {
            cart.push({
                produk_id: id,
                nama: nama,
                harga: harga,
                qty: 1
            });
        }
        renderCart();
    }

    function renderCart() {
        let totalQty = cart.reduce((acc, curr) => acc + curr.qty, 0);
        let totalPrice = cart.reduce((acc, curr) => acc + (curr.harga * curr.qty), 0);
        if (totalQty > 0) {
            $('#cartFloat').fadeIn(300).css('display', 'flex');
            $('#qCount').text(totalQty);
            $('#totalLabel').text('Rp ' + totalPrice.toLocaleString('id-ID'));
        } else {
            $('#cartFloat').fadeOut(200);
        }
    }

    function showCheckout() {
        $('#listCheckout').empty();
        let total = 0;
        cart.forEach((item, index) => {
            total += (item.harga * item.qty);
            $('#listCheckout').append(`
                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2" style="border-color: rgba(255,255,255,0.05) !important;">
                        <div style="flex: 1;">
                            <div class="fw-bold small text-white">${item.nama}</div>
                            <div class="text-white-50" style="font-size: 11px;">${item.qty} x Rp ${item.harga.toLocaleString('id-ID')}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm text-danger border-0" onclick="removeItem(${index})"><i class="fa fa-minus-circle"></i></button>
                            <div class="fw-bold ms-2 small" style="color: var(--gold-primary);">Rp ${(item.harga * item.qty).toLocaleString('id-ID')}</div>
                        </div>
                    </div>
                `);
        });
        $('#grandTotal').text('Rp ' + total.toLocaleString('id-ID'));
        $('#modalCheckout').modal('show');
    }

    function removeItem(index) {
        if (cart[index].qty > 1) {
            cart[index].qty--;
        } else {
            cart.splice(index, 1);
        }
        renderCart();
        showCheckout();
        if (cart.length === 0) $('#modalCheckout').modal('hide');
    }

    function kirimPesanan() {
        const nama = $('#nama_pemesan').val();
        const total = cart.reduce((acc, curr) => acc + (curr.harga * curr.qty), 0);
        if (!nama) {
            Swal.fire({
                icon: 'warning',
                text: 'Nama pemesan wajib diisi!'
            });
            return;
        }

        Swal.fire({
            title: 'Kirim Pesanan?',
            text: "Pesanan akan langsung diteruskan ke dapur restoran.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#c69c54',
            cancelButtonColor: '#374151',
            confirmButtonText: 'Ya, Kirim',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: '<?= base_url("order/kirim_pesanan") ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        kode_toko: '<?= $kode_toko ?>',
                        id_meja: '<?= $id_meja ?>',
                        nomor_meja: '<?= $info_meja->nomor_meja ?? "0" ?>',
                        nama_pemesan: nama,
                        metode_pembayaran: $('#metode_bayar').val(),
                        cart_data: JSON.stringify(cart),
                        total_bayar: total
                    }
                }).fail(err => Swal.showValidationMessage(`Gagal: ${err.statusText}`));
            }
        }).then((result) => {
            if (result.isConfirmed && result.value.status === 'success') {
                Swal.fire({
                        icon: 'success',
                        title: 'Pesanan Terkirim!',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    .then(() => location.reload());
            }
        });
    }
    </script>
</body>

</html>