<!DOCTYPE html>
<html lang="id">
<?php
/**
 * @var array $produk
 */
?>

<head>
    <title>X Coffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .product-card {
        cursor: pointer;
        border-radius: 15px;
        transition: 0.3s;
        border: 1px solid #eee;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

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

    .badge-stok {
        font-size: 0.8rem;
        color: #666;
    }

    .price-tag {
        color: #198754;
        font-weight: bold;
    }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 py-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold text-success">🛒 Coffee & Eatery</h4>
                    <div class="text-end">
                        <div class="d-flex align-items-center justify-content-end mb-1">
                            <div class="me-3 text-end">
                                <div class="fw-bold text-dark" style="line-height: 1.2;">
                                    <i class="fas fa-user-circle me-1 text-secondary"></i>
                                    <?= session()->get('username') ?>
                                </div>
                                <div id="clock" class="text-muted" style="font-size: 0.75rem;"></div>
                            </div>

                            <a href="<?= base_url('logout') ?>"
                                class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal"
                                data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt me-1"></i> Keluar
                            </a>
                        </div>
                        <?php /* 
                        <?php if (session()->get('role') === 'admin') : ?>
                        <a href="<?= base_url('admin') ?>" class="btn btn-dark btn-xs rounded-pill px-2 py-0 mt-1"
                            style="font-size: 0.7rem;">
                            <i class="fas fa-user-shield me-1"></i> Mode Admin
                        </a>
                        <?php endif; ?>
                        */ ?>
                    </div>
                </div>

                <input type="text" id="search-produk" class="form-control rounded-pill mb-3"
                    placeholder="🔍 Cari produk...">
                <div class="mb-4">
                    <button class="btn btn-dark category-btn" onclick="filterKategori('semua')">Semua</button>
                    <button class="btn btn-outline-dark category-btn"
                        onclick="filterKategori('minuman')">Minuman</button>
                    <button class="btn btn-outline-dark category-btn"
                        onclick="filterKategori('makanan')">Makanan</button>
                    <button class="btn btn-outline-dark category-btn" onclick="filterKategori('snack')">Snack</button>

                    <script>
                    // Variabel untuk menyimpan status filter saat ini
                    let kategoriAktif = 'semua';

                    function filterKategori(kategori) {
                        kategoriAktif = kategori; // Simpan pilihan kategori
                        jalankanFilter(); // Jalankan filter gabungan

                        // Update visual tombol
                        const buttons = document.querySelectorAll('.category-btn');
                        buttons.forEach(btn => {
                            if (btn.getAttribute('onclick').includes(`'${kategori}'`)) {
                                btn.classList.replace('btn-outline-dark', 'btn-dark');
                            } else {
                                btn.classList.replace('btn-dark', 'btn-outline-dark');
                            }
                        });
                    }

                    // Fungsi utama yang menggabungkan Search + Kategori
                    function jalankanFilter() {
                        const keyword = document.getElementById('search-produk').value.toLowerCase();
                        const items = document.querySelectorAll('.product-item');

                        items.forEach(item => {
                            const namaProduk = item.querySelector('h6').innerText.toLowerCase();
                            const katProduk = item.getAttribute('data-kategori');

                            // Cek kecocokan Kategori
                            const cocokKategori = (kategoriAktif === 'semua' || katProduk === kategoriAktif);
                            // Cek kecocokan Nama (Search)
                            const cocokNama = namaProduk.includes(keyword);

                            // Hanya tampilkan jika KEDUANYA cocok
                            if (cocokKategori && cocokNama) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                    }

                    // Pasang event listener untuk Search Bar agar memanggil jalankanFilter()
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

                        // Menggunakan format Indonesia (id-ID)
                        document.getElementById('clock').innerText = now.toLocaleDateString('id-ID', options);
                    }

                    // Jalankan fungsi setiap 1 detik
                    setInterval(updateClock, 1000);

                    // Panggil langsung agar tidak menunggu 1 detik pertama
                    updateClock();
                    </script>
                </div>


                <div class="row g-3">
                    <?php foreach ($produk as $p): ?>
                    <div class="col-md-4 product-item" data-kategori="<?= strtolower($p['kategori']) ?>">
                        <div class="card h-100 product-card p-2 text-center"
                            onclick="addToCart(<?= $p['produk_id'] ?>, '<?= $p['nama_produk'] ?>', <?= $p['harga_jual'] ?>)">
                            <div class="py-3 fs-1">📦</div>
                            <div class="card-body p-1">
                                <h6 class="mb-1 fw-bold"><?= $p['nama_produk'] ?></h6>
                                <p class="price-tag mb-0">Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></p>
                                <span class="badge-stok">Stok: <?= $p['stok'] ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <script>
                let cart = [];
                document.addEventListener('DOMContentLoaded', function() {
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

                function addToCart(id, nama, harga) {
                    const existingItem = cart.find(item => item.id === id);
                    if (existingItem) {
                        existingItem.qty++;
                    } else {
                        cart.push({
                            id,
                            nama,
                            harga,
                            qty: 1
                        });
                    }
                    renderCart();
                }

                function removeItem(index) {
                    cart.splice(index, 1);
                    renderCart();
                }

                function renderCart() {
                    const cartContainer = document.getElementById('cart-items');
                    if (!cartContainer) return;

                    cartContainer.innerHTML = '';
                    let subtotal = 0;

                    cart.forEach((item, index) => {
                        const itemSubtotal = item.harga * item.qty;
                        subtotal += itemSubtotal;

                        cartContainer.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <div>
                            <span class="fw-bold d-block">${item.nama}</span>
                            <small class="text-muted">${item.qty} x Rp ${item.harga.toLocaleString('id-ID')}</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold">Rp ${itemSubtotal.toLocaleString('id-ID')}</span>
                            <button class="btn btn-sm text-danger d-block ms-auto" onclick="removeItem(${index})">×</button>
                        </div>
                    </div>
                `;
                    });

                    let tax = subtotal * 0.11;
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

                function bukaModalBayar() {
                    if (cart.length === 0) return alert("Keranjang masih kosong!");

                    // Reset Promo setiap kali buka modal baru agar bersih
                    resetPromo();

                    let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
                    let pajak = Math.ceil(subtotal * 0.11); // Pakai 11% sesuai request bos
                    let totalFix = subtotal + pajak;

                    // Gunakan ID display-total-modal agar sinkron dengan HTML revisi kita
                    document.getElementById('display-total-modal').innerText = 'Rp ' + totalFix.toLocaleString('id-ID');
                    document.getElementById('input-uang-diterima').value = '';
                    document.getElementById('display-kembali').innerText = 'Rp 0';

                    const modal = new bootstrap.Modal(document.getElementById('modalBayar'));
                    modal.show();
                }

                function setUang(nominal) {
                    document.getElementById('input-uang-diterima').value = nominal;
                    hitungTotalAkhir(); // Panggil hitungTotalAkhir agar kembalian update
                }

                // FUNGSI INI ADALAH PUSAT PERHITUNGAN (Subtotal + Pajak - Diskon)
                function hitungTotalAkhir() {
                    // Ambil subtotal + pajak (misal pajak 11%)
                    let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
                    let pajak = Math.ceil(subtotal * 0.11);
                    let totalAwal = subtotal + pajak;

                    // AMBIL NILAI DISKON (Ini kuncinya!)
                    let diskon = parseInt(document.getElementById('input-diskon').value) || 0;

                    // Hitung Total Akhir
                    let totalSetelahDiskon = totalAwal - diskon;
                    if (totalSetelahDiskon < 0) totalSetelahDiskon = 0;

                    // Update Tampilan Harga Hijau
                    document.getElementById('display-total-modal').innerText = 'Rp ' + totalSetelahDiskon
                        .toLocaleString('id-ID');

                    // Update Kembalian
                    let bayar = parseInt(document.getElementById('input-uang-diterima').value) || 0;
                    let sisa = bayar - totalSetelahDiskon;
                    document.getElementById('display-kembali').innerText = 'Rp ' + (sisa > 0 ? sisa.toLocaleString(
                        'id-ID') : 0);
                }

                function konfirmasiPembayaran() {
                    if (cart.length === 0) {
                        return Swal.fire({
                            icon: 'warning',
                            title: 'Keranjang Kosong',
                            text: 'Silahkan pilih produk terlebih dahulu!'
                        });
                    }

                    let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
                    let pajak = Math.ceil(subtotal * 0.11);
                    let diskon = parseInt(document.getElementById('input-diskon').value) || 0;

                    // Total Akhir yang harus dibayar
                    let totalFix = (subtotal + pajak) - diskon;
                    let uangDiterima = parseInt(document.getElementById('input-uang-diterima').value) || 0;

                    if (uangDiterima < totalFix) {
                        return Swal.fire({
                            icon: 'error',
                            title: 'Uang Tidak Cukup',
                            html: `Total: <b>Rp ${totalFix.toLocaleString()}</b><br>Kurang: <span style="color:red">Rp ${(totalFix - uangDiterima).toLocaleString()}</span>`
                        });
                    }

                    Swal.fire({
                        title: 'Konfirmasi Bayar?',
                        text: `Total belanja Rp ${totalFix.toLocaleString()}`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Bayar!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.showLoading();

                            // KIRIM DATA LENGKAP KE SERVER
                            const data = {
                                total: totalFix,
                                bayar: uangDiterima,
                                diskon: diskon, // Potongan diskon masuk sini
                                id_promo: document.getElementById('id-promo-terpilih')
                                    .value, // ID Promo masuk sini
                                cart: cart
                            };

                            fetch('<?= site_url('kasir/bayar') ?>', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify(data)
                                })
                                .then(async res => {
                                    const response = await res.json();
                                    if (res.ok && response.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Transaksi telah disimpan.',
                                            timer: 2000,
                                            showConfirmButton: false
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire('Gagal', response.message, 'error');
                                    }
                                })
                                .catch(err => {
                                    Swal.fire('Error', 'Terjadi kesalahan koneksi ke server', 'error');
                                });
                        }
                    });
                }
                </script>
            </div>
            <div class="col-md-4 bg-white cart-section d-flex flex-column p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Keranjang Belanja</h5>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalHapusSemua">
                        <i class="bi bi-trash me-1"></i> Hapus Semua
                    </button>
                </div>
                <script>
                function eksekusiHapusSemua() {
                    // 1. Kosongkan data di memori
                    cart = [];

                    // 2. Update tampilan tabel keranjang
                    renderCart();

                    // 3. Reset input nominal uang jika ada
                    const inputBayar = document.getElementById('input-uang-diterima');
                    if (inputBayar) inputBayar.value = '';

                    // 4. Tutup modal secara otomatis
                    const modalEl = document.getElementById('modalHapusSemua');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance.hide();

                    // 5. Notifikasi singkat (Opsional)
                    console.log("Keranjang berhasil dikosongkan.");
                }
                </script>

                <div class="flex-grow-1 text-center text-muted py-5" id="empty-cart">
                    <p>Belum ada produk dipilih</p>
                </div>

                <div id="cart-items" class="mb-3">
                </div>

                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between">
                        <p>Subtotal</p>
                        <p id="subtotal">Rp 0</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p>PPN 11%</p>
                        <p id="tax">Rp 0</p>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-4">
                        <p>Total</p>
                        <p id="total">Rp 0</p>
                    </div>
                    <button onclick="bukaModalBayar()"
                        class="btn btn-dark w-100 py-3 mt-2 rounded-3 fw-bold">Bayar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBayar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Proses Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="text-muted mb-1">Total Pembayaran</p>
                    <small class="text-muted text-decoration-line-through" id="label-subtotal-awal"
                        style="display:none;"></small>
                    <h2 class="text-success fw-bold mb-4" id="display-total-modal">Rp 0</h2>

                    <div class="row g-2 mb-4">
                        <div class="col-6"><button id="btn-tunai" class="btn btn-success w-100 py-3"
                                onclick="pilihMetode('Tunai')">Tunai</button></div>
                        <div class="col-6"><button id="id-qris" class="btn btn-outline-secondary w-100 py-3"
                                onclick="pilihMetode('QRIS')">QRIS</button></div>
                        <div class="col-6"><button id="id-trans" class="btn btn-outline-secondary w-100 py-3"
                                onclick="pilihMetode('Transfer')">Transfer</button></div>
                        <div class="col-6"><button id="id-edc" class="btn btn-outline-secondary w-100 py-3"
                                onclick="pilihMetode('EDC')">EDC</button></div>
                    </div>

                    <div class="text-start mb-3">
                        <label class="small text-muted fw-bold">Kode Promo (Opsional)</label>
                        <div class="input-group">
                            <input type="text" id="input-kode-promo" class="form-control"
                                placeholder="Contoh: SENJASORE">
                            <button class="btn btn-primary" type="button" onclick="cekPromoAjax()">Cek</button>
                        </div>
                        <small id="pesan-promo" class="d-block mt-1"></small>
                        <a href="javascript:void(0)" class="small text-danger text-decoration-none" id="btn-batal-promo"
                            style="display:none;" onclick="resetPromo()">
                            <i class="bi bi-x-circle"></i> Batal Pakai Promo
                        </a>
                        <input type="hidden" id="id-promo-terpilih" value="">
                    </div>

                    <div class="text-start mb-3">
                        <label class="small text-danger fw-bold">Diskon (Potongan Harga)</label>
                        <input type="number" id="input-diskon" class="form-control text-center text-danger fw-bold"
                            placeholder="0" oninput="hitungTotalAkhir()">
                    </div>

                    <div class="text-start mb-3">
                        <label class="small text-muted">Uang Diterima</label>
                        <input type="number" id="input-uang-diterima"
                            class="form-control form-control-lg text-center fw-bold" placeholder="0"
                            oninput="hitungTotalAkhir()">
                    </div>

                    <div class="d-flex gap-2 mb-4">
                        <button class="btn btn-light border flex-grow-1" onclick="setUang(10000)">10rb</button>
                        <button class="btn btn-light border flex-grow-1" onclick="setUang(50000)">50rb</button>
                        <button class="btn btn-light border flex-grow-1" onclick="setUang(100000)">100rb</button>
                    </div>

                    <div class="bg-light p-3 rounded-3 d-flex justify-content-between align-items-center mb-4">
                        <span class="text-success fw-bold">Kembalian</span>
                        <h4 class="mb-0 fw-bold text-success" id="display-kembali">Rp 0</h4>
                    </div>

                    <button class="btn btn-dark w-100 py-3 rounded-3 fw-bold"
                        onclick="konfirmasiPembayaran()">Konfirmasi Pembayaran</button>
                </div>
            </div>
        </div>
    </div>
    <script>
    function eksekusiSimpan(total, bayar, diskon) {
        const idMember = $('#id_member_hidden').val();
        const idPromo = $('#id-promo-terpilih').val(); // AMBIL ID PROMO DI SINI
        const data = {
            total: total,
            bayar: bayar,
            diskon: diskon,
            id_promo: idPromo, // TAMBAHKAN INI
            metode: metodeTerpilih,
            order_at: waktuOrder,
            cart: cart,
            member_id: idMember
        };

        fetch('<?= site_url('kasir/bayar') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(async res => {
                const result = await res.json();
                if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Transaksi disimpan. Klik OK untuk cetak struk.',
                        allowOutsideClick: false
                    }).then(() => {
                        // PANGGIL CETAK DISINI
                        cetakStruk(total, bayar);
                    });
                } else {
                    Swal.fire('Gagal', result.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Gagal terhubung ke server', 'error'));
    }
    //Memilih Metode Pembayaran
    let metodeTerpilih = 'Tunai'; // Default

    function konfirmasiPembayaran() {
        let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
        let pajak = Math.ceil(subtotal * 0.11);
        let totalAwal = subtotal + pajak;

        // AMBIL DISKON DARI INPUT
        let diskon = parseInt(document.getElementById('input-diskon').value) || 0;

        // HITUNG TOTAL AKHIR (Setelah Diskon)
        let totalFix = totalAwal - diskon;
        if (totalFix < 0) totalFix = 0;

        let uangDiterimaInput = parseInt(document.getElementById('input-uang-diterima').value) || 0;
        let uangDiterima;

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
        } else {
            uangDiterima = totalFix;
        }

        Swal.fire({
            title: 'Konfirmasi Transaksi',
            html: `
        <table class="table table-sm text-start">
            <tr><td>Metode</td><td>: <b>${metodeTerpilih}</b></td></tr>
            <tr><td>Total Akhir</td><td>: <b>Rp ${totalFix.toLocaleString()}</b></td></tr>
            ${diskon > 0 ? `<tr><td>Hemat</td><td>: <b class="text-success">Rp ${diskon.toLocaleString()}</b></td></tr>` : ''}
            <tr><td>Bayar</td><td>: <b>Rp ${uangDiterima.toLocaleString()}</b></td></tr>
        </table>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Ya, Simpan & Cetak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // KIRIM DATA DISKON KE FUNGSI SIMPAN
                eksekusiSimpan(totalFix, uangDiterima, diskon);
            }
        });
    }

    // 3. Fungsi Kirim Data ke Database (Fetch API)

    function pilihMetode(metode) {
        metodeTerpilih = metode;

        const btnTunai = document.getElementById('btn-tunai');
        const btnQris = document.getElementById('id-qris');

        if (metode === 'Tunai') {
            // Update UI Tombol
            btnTunai.classList.replace('btn-outline-success', 'btn-success');
            btnQris.classList.replace('btn-success', 'btn-outline-secondary');

            // Reset nilai input jika perlu
            $('#metode_pembayaran').val('Tunai');
        } else {
            // Update UI Tombol
            btnQris.classList.replace('btn-outline-secondary', 'btn-success');
            btnTunai.classList.replace('btn-success', 'btn-outline-success');

            tampilkanQRIS();
        }
    }
    // Metode QRIS
    function tampilkanQRIS() {
        let totalTagihan = "27750"; // Sesuaikan dengan cara Anda mengambil total di sistem

        Swal.fire({
            title: 'Pembayaran QRIS',
            text: 'Silahkan scan kode QR di bawah ini',
            imageUrl: '<?= base_url('assets/img/qris.jpg') ?>',
            imageWidth: 400,
            imageHeight: 500,
            confirmButtonText: 'OKE',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#uang_diterima').val(totalTagihan);
                $('#metode_pembayaran').val('QRIS');
                konfirmasiPembayaran();
            }
        });
    }
    //Diskon
    function cekPromoAjax() {
        const kode = $('#input-kode-promo').val();
        const pesanEl = $('#pesan-promo');

        // Hitung subtotal & total belanja (Sesuai PPN 11%)
        let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
        let totalBelanja = Math.ceil(subtotal + (subtotal * 0.11));

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
                console.log("Respon Server:", res); // CEK DI CONSOLE F12
                if (res.status === 'success') {
                    pesanEl.removeClass('text-danger').addClass('text-success').text("Promo aktif: " + res
                        .nama_promo);
                    $('#btn-batal-promo').show();

                    // 1. ISI NOMINAL KE INPUT DISKON
                    $('#input-diskon').val(res.potongan);
                    // 2. SIMPAN ID PROMO
                    $('#id-promo-terpilih').val(res.id_promo);
                    // 3. KUNCI INPUTNYA
                    $('#input-diskon').attr('readonly', true).addClass('bg-light');

                    // 4. JALANKAN HITUNGAN AKHIR (Kunci Utama!)
                    hitungTotalAkhir();
                } else {
                    Swal.fire("Gagal", res.msg, "error");
                    resetPromo();
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert("Gagal konek ke server!");
            }
        });
    }

    function simpanTransaksi() {
        Swal.fire({
            title: 'Memproses Transaksi...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var formData = $('#form-transaksi').serialize();

        $.ajax({
            url: "<?= base_url('kasir/bayar') ?>",
            type: "POST",
            data: formData,
            dataType: "JSON",
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Transaksi Berhasil Disimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Optional: Arahkan ke cetak struk dulu sebelum reload
                        // window.open("<?= base_url('kasir/cetak_struk') ?>/" + response.transaksi_id);
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', response.message || 'Terjadi kesalahan.', 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Gagal terhubung ke server. Cek koneksi atau routes.', 'error');
            }
        });
    }
    // Tampilang QRIS
    function tampilkanQRIS() {
        Swal.fire({
            title: 'Pembayaran QRIS',
            text: 'Silahkan scan kode QR di bawah ini',
            imageUrl: '<?= base_url('assets/img/qris.jpg') ?>', // Ganti dengan path gambar Anda
            imageWidth: 500,
            imageHeight: 600,
            imageAlt: 'Scan QR Code',
            confirmButtonText: 'OKE',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                // Setelah scan, lanjut ke konfirmasi simpan
                konfirmasiPembayaran();
            }
        });
    }

    // 3. Fungsi Cetak Struk
    function cetakStruk(total, bayar) {
        const win = window.open('', 'PRINT', 'height=600,width=400');
        let subtotal = cart.reduce((sum, i) => sum + (i.harga * i.qty), 0);
        let pajak = Math.ceil(subtotal * 0.11);

        let strukHTML = `
    <html>
    <head>
        <title>Print Struk</title>
        <style>
            /* 1. Paksa ukuran kertas dan hilangkan margin browser */
            @page {
                size: 80mm auto;
                margin: 0;
            }
            
            /* 2. Body sebagai kontainer luar untuk centering */
            body { 
                margin: 0; 
                padding: 0; 
                display: flex; 
                justify-content: center; 
                background-color: #eee; /* Warna background agar terlihat di layar browser */
            }

            /* 3. Kontainer Struk yang sebenarnya */
            .struk-container { 
                font-family: 'Courier New', Courier, monospace; 
                width: 80mm; 
                min-height: 100vh;
                font-size: 12px; 
                padding: 5mm; /* Margin internal kertas */
                background-color: white;
                box-sizing: border-box;
            }

            table { width: 100%; border-collapse: collapse; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .line { border-top: 1px dashed #000; margin: 8px 0; }
            
            /* Agar saat print background abu-abu hilang */
            @media print {
                body { background-color: white; }
                .struk-container { width: 80mm; padding: 2mm; }
            }
        </style>
    </head>
    <body>
        <div class="struk-container">
            <div class="text-center">
                <h2 style="margin:0; font-size: 16px; margin-top:30px; ">Senja Coffee & Eatery</h2>
                <small>Perum PT Sumbar Mas Blok B2</small><br>
                <br>
                <div class="line"></div>
                <table style="font-size: 11px;">
                    <tr>
                        <td>Tgl: ${new Date().toLocaleDateString('id-ID')}</td>
                        <td class="text-right">${new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})}</td>
                    </tr>
                    <tr>
                        <td>Kasir: Admin</td>
                        <td class="text-right">INV-${Date.now().toString().slice(-6)}</td>
                    </tr>
                </table>
            </div>
            <div class="line"></div>
            <table>
`;

        cart.forEach(item => {
            strukHTML += `
            <tr><td colspan="2">${item.nama}</td></tr>
            <tr>
                <td>${item.qty} x ${item.harga.toLocaleString()}</td>
                <td class="text-right">${(item.qty * item.harga).toLocaleString()}</td>
            </tr>
        `;
        });

        strukHTML += `
            </table>
            <div class="line"></div>
            <table>
                <tr><td>Subtotal</td><td class="text-right">${subtotal.toLocaleString()}</td></tr>
                <tr><td>PPN 11%</td><td class="text-right">${pajak.toLocaleString()}</td></tr>
                <tr style="font-weight:bold;"><td>TOTAL</td><td class="text-right">${total.toLocaleString()}</td></tr>
                <tr><td>Bayar</td><td class="text-right">${bayar.toLocaleString()}</td></tr>
                <tr><td>Kembali</td><td class="text-right">${(bayar - total).toLocaleString()}</td></tr>
            </table>
            <div class="line"></div>
            <div class="text-center">
                <small>Wifi: .... </small><br>
                <small>Pass: ....</small><br>
                <strong>Terima Kasih Atas Kunjungan Anda</strong><br>
                <strong>ig : @senjacoffee</strong>
            </div>

            <script>
                // Ini kunci agar otomatis muncul dialog printer
                window.onload = function() {
                    window.print();
                    // Menunggu sebentar setelah print dipicu, lalu tutup jendela dan reload asal
                    setTimeout(() => { 
                        window.close(); 
                    }, 500);
                };
            <\/script>
        </body>
        </html>
    `;

        win.document.write(strukHTML);
        win.document.close();

        // Listener tambahan untuk memastikan reload di halaman utama setelah jendela cetak ditutup
        const checkWindow = setInterval(() => {
            if (win.closed) {
                clearInterval(checkWindow);
                window.location.reload();
            }
        }, 1000);
    }
    </script>
    <div class="modal fade" id="modalHapusSemua" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-5">
                    <div class="text-danger mb-4">
                        <i class="bi bi-exclamation-triangle" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold">Kosongkan Keranjang?</h4>
                    <p class="text-muted">Semua barang yang sudah Anda pilih akan dihapus dari daftar belanja ini.</p>

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
                    <p class="text-muted small">Semua sesi aktif Anda akan dihentikan. Pastikan tidak ada transaksi yang
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
</body>

</html>