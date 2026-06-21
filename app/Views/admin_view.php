<?php
/**
 * @var string $chart_labels
 * @var string $chart_data
 * @var string $pie_labels
 * @var string $pie_data
 * @var array $riwayat
 * @var \CodeIgniter\Pager\Pager $pager_transaksi
 * @var \CodeIgniter\Pager\Pager $pager_produk
 */
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senja Coffee - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    :root {
        --bg-body: #f1f2ed;
        --card-radius: 12px;
        --primary-green: #198754;
    }

    body {
        background-color: var(--bg-body);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .table {
        color: var(--text-main) !important;
    }

    [data-bs-theme="dark"] .table {
        --bs-table-bg: var(--card-bg);
        --bs-table-border-color: var(--border-color);
        color: var(--text-main) !important;
    }

    /* Memperbaiki header tabel yang memutih di dark mode */
    [data-bs-theme="dark"] .table thead th {
        background-color: #333 !important;
        color: #ffffff !important;
        border-color: var(--border-color);
    }

    /* Memperbaiki input tanggal agar teksnya tidak hilang */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: #2b2b2b !important;
        border-color: #444 !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .form-control::placeholder {
        color: #888 !important;
    }

    .wrapper-full {
        width: 100%;
        padding: 20px 30px;
    }

    .card {
        border: none;
        border-radius: var(--card-radius);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        margin-bottom: 20px;
    }

    .nav-pill-custom {
        background: white;
        border-radius: 10px;
        padding: 5px;
        display: inline-flex;
        border: 1px solid #dee2e6;
    }

    .nav-pill-custom .nav-link {
        border-radius: 8px;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .nav-pill-custom .nav-link.active {
        background: var(--primary-green) !important;
        color: white !important;
        box-shadow: 0 4px 8px rgba(25, 135, 84, 0.2);
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
    }

    .stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        margin-top: 5px;
    }

    .stat-sub {
        font-size: 0.75rem;
        font-weight: 600;
    }

    .tab-pane {
        animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Styling Pagination: First, Last, dan Nomor sama besar */
    .pagination {
        display: flex !important;
        list-style: none !important;
        gap: 8px !important;
        padding: 0;
        justify-content: center;
    }

    .pagination li a,
    .pagination li span {
        display: flex !important;
        align-items: center;
        justify-content: center;
        /* Ini kuncinya: tinggi tetap, lebar minimal sama */
        min-width: 45px;
        height: 45px;
        padding: 0 15px;
        /* Memberi ruang untuk teks First/Last */

        border-radius: 10px !important;
        border: 1px solid #dee2e6 !important;
        color: #198754 !important;
        text-decoration: none !important;
        font-weight: bold;
        background-color: white;
        transition: all 0.2s ease;
    }

    /* Warna Hijau untuk Halaman Aktif */
    .pagination li.active span {
        background-color: #198754 !important;
        color: white !important;
        border-color: #198754 !important;
    }

    /* Efek Hover */
    .pagination li a:hover {
        background-color: #f8f9fa !important;
        border-color: #198754 !important;
        transform: translateY(-2px);
    }

    /* Sembunyikan panah << >> jika Anda hanya ingin First, Last, dan Nomor */
    .pagination li a[aria-label="Previous"],
    .pagination li a[aria-label="Next"] {
        display: none !important;
    }
    </style>
</head>

<body>

    <div class="wrapper-full">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-success text-white p-2 rounded-3">🛒</div>
                <h4 class="fw-bold mb-0">Senja Coffee Management</h4>
            </div>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <div class="fw-bold text-dark" style="line-height: 1.2;">
                        <i class="fas fa-user-shield me-1 text-primary"></i>
                        Administrator: <?= session()->get('username') ?>
                    </div>
                    <small class="text-muted" id="clockAdmin"></small>
                </div>
                <a href="#" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal"
                    data-bs-target="#logoutModalAdmin">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </div>
        </div>


        <ul class="nav nav-pills nav-pill-custom mb-4" id="pills-tab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="tab-ringkasan-btn" data-bs-toggle="pill"
                    data-bs-target="#tab-ringkasan" type="button">Ringkasan</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-transaksi-btn" data-bs-toggle="pill" data-bs-target="#tab-transaksi"
                    type="button">Daftar Transaksi</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tab-produk-btn" data-bs-toggle="pill" data-bs-target="#tab-produk"
                    type="button">Data Produk</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-ringkasan" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card p-4">
                            <div class="stat-label text-uppercase">Total Pendapatan</div>
                            <div class="stat-value text-success">Rp
                                <?= number_format($total_pendapatan ?? 0, 0, ',', '.') ?></div>
                            <div class="stat-sub text-success">+12% vs kemarin</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-4">
                            <div class="stat-label text-uppercase">Jumlah Transaksi</div>
                            <div class="stat-value"><?= $jumlah_transaksi ?? 0 ?></div>
                            <div class="stat-sub text-muted">Nota sukses</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-4">
                            <div class="stat-label text-uppercase">Rata-rata</div>
                            <div class="stat-value">Rp <?= number_format($rata_rata ?? 0, 0, ',', '.') ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-4">
                            <div class="stat-label text-uppercase">Item Terjual</div>
                            <div class="stat-value"><?= $item_terjual ?? 0 ?> <span class="fs-6 fw-normal">Pcs</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="card p-4">
                            <h6 class="fw-bold mb-4 text-success">
                                <i class="bi bi-graph-up-arrow me-2"></i>Grafik Pendapatan Mingguan
                            </h6>
                            <div style="height: 300px; position: relative;">
                                <canvas id="revenueChart" data-labels='<?php echo $chart_labels; ?>'
                                    data-values='<?php echo $chart_data; ?>'>
                                </canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="card p-4 h-100">
                            <h6 class="fw-bold mb-4">Persentase Produk Terlaris</h6>
                            <div style="height: 450px; position: relative;">
                                <canvas id="productPieChart" data-labels='<?php echo $pie_labels; ?>'
                                    data-values='<?php echo $pie_data; ?>'>
                                </canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="card p-4 text-center">
                            <h6 class="fw-bold mb-3 text-start">Metode Pembayaran</h6>
                            <p class="mb-0 fs-2 fw-bold text-dark mt-3">85% Tunai</p>
                            <small class="text-muted">15% QRIS/Transfer</small>
                        </div>
                        <div class="card p-4 mt-3">
                            <h6 class="fw-bold mb-3">Produk Terlaris</h6>
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($produk_terlaris)): ?>
                                <?php foreach ($produk_terlaris as $p): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <span class="text-dark fw-bold"><?= $p['nama_produk'] ?></span><br>
                                        <small class="text-muted">ID: <?= $p['produk_id'] ?></small>
                                    </div>
                                    <span
                                        class="badge bg-success-subtle text-success rounded-pill"><?= $p['total_laku'] ?>x
                                        terjual</span>
                                </li>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <li class="list-group-item text-center py-3 text-muted small">Data tidak tersedia</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-transaksi" role="tabpanel">
                <div class="card p-4">
                    <h6 class="fw-bold mb-3">Filter Riwayat Transaksi</h6>
                    <form method="GET" action="<?= site_url('admin') ?>" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?? '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai" class="form-control"
                                value="<?= $tgl_selesai ?? '' ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter Data</button>
                            <a href="<?= site_url('admin') ?>" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0">Riwayat Transaksi</h5>
                        <button class="btn btn-sm btn-outline-secondary">Download Excel</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Transaksi</th>
                                    <th>Waktu</th>
                                    <th>Item</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($riwayat)): ?>
                                <?php foreach ($riwayat as $row): ?>
                                <tr>
                                    <td class="py-3 fw-medium">
                                        <a href="javascript:void(0)"
                                            class="text-decoration-none fw-bold text-primary view-detail"
                                            data-id="<?= $row['id'] ?>" data-invoice="<?= $row['invoice'] ?>">
                                            #<?= $row['invoice'] ?>
                                        </a>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td><?= $row['subtotal'] ?> Item</td>
                                    <td class="fw-bold">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                    <td><span
                                            class="badge bg-success-subtle text-success rounded-pill px-3">Lunas</span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada transaksi</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 d-flex justify-content-center">
                        <?php 
                        echo $pager_transaksi->links('transaksi', 'default_full'); 
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-produk" role="tabpanel">
            <div class="card p-4 mb-4 text-center">
                <h4 class="fw-bold">Manajemen Produk</h4>
                <p class="text-muted">Kelola harga, stok, dan menu kategori di sini.</p>
                <div>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#modalTambahProduk">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Barcode</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Harga Beli</th>
                                    <th class="text-end">Harga Jual</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($semua_produk)): ?>
                                <?php foreach($semua_produk as $p): ?>
                                <tr>
                                    <td><code><?= $p['barcode'] ?></code></td>
                                    <td class="fw-bold"><?= $p['nama_produk'] ?></td>
                                    <td><span
                                            class="badge bg-secondary-subtle text-secondary"><?= $p['kategori'] ?? 'Umum' ?></span>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($p['harga_beli'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $p['stok'] < 10 ? 'bg-danger' : 'bg-success' ?>">
                                            <?= $p['stok'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary btn-edit" data-bs-toggle="modal"
                                            data-bs-target="#modalEdit" data-produk_id="<?= $p['produk_id'] ?>"
                                            data-barcode="<?= $p['barcode'] ?>"
                                            data-nama_produk="<?= $p['nama_produk'] ?>"
                                            data-harga_beli="<?= $p['harga_beli'] ?>"
                                            data-harga_jual="<?= $p['harga_jual'] ?>" data-stok="<?= $p['stok'] ?>"
                                            data-kategori="<?= $p['kategori'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="<?= site_url('admin/hapus_produk/' . $p['produk_id']) ?>"
                                            class="btn btn-sm btn-outline-danger btn-hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data produk.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Produk Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('admin/simpan_produk') ?>" method="POST">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Barcode / SKU</label>
                                <input type="text" name="barcode" class="form-control" placeholder="Scan barcode"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Produk</label>
                                <input type="text" name="nama_produk" class="form-control" placeholder="Nama produk"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Harga Beli</label>
                                <input type="text" name="harga_beli" class="form-control rupiah-input" placeholder="0"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Harga Jual</label>
                                <input type="text" name="harga_jual" class="form-control rupiah-input" placeholder="0"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Stok Awal</label>
                                <input type="number" name="stok" class="form-control" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori</label>
                                <select name="kategori" class="form-select">
                                    <option value="Makanan">Makanan</option>
                                    <option value="Minuman">Minuman</option>
                                    <option value="Snack">Snack</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Update Data Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?= site_url('admin/edit_produk') ?>" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="produk_id" id="edit_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Barcode</label>
                                <input type="text" name="barcode" id="edit_barcode" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Produk</label>
                                <input type="text" name="nama_produk" id="edit_nama" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Harga Beli</label>
                                <input type="text" name="harga_beli" id="edit_hargabeli"
                                    class="form-control rupiah-input" placeholder="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Harga Jual</label>
                                <input type="text" name="harga_jual" id="edit_hargajual"
                                    class="form-control rupiah-input" placeholder="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Stok</label>
                                <input type="number" name="stok" id="edit_stok" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori</label>
                                <select name="kategori" id="edit_kategori" class="form-select">
                                    <option value="Makanan">Makanan</option>
                                    <option value="Minuman">Minuman</option>
                                    <option value="Snack">Snack</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning fw-bold">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        const canvasEl = document.getElementById('revenueChart');
        if (canvasEl) {
            const rawLabels = canvasEl.getAttribute('data-labels');
            const rawValues = canvasEl.getAttribute('data-values');

            if (rawLabels && rawValues) {
                const labelsDariDB = JSON.parse(rawLabels);
                const dataDariDB = JSON.parse(rawValues);

                const ctx = canvasEl.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labelsDariDB, // Gunakan data dari DB
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: dataDariDB, // Gunakan data dari DB
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            borderColor: '#198754',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#198754'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
        const pieCanvas = document.getElementById('productPieChart');
        if (pieCanvas) {
            const labelsPie = JSON.parse(pieCanvas.getAttribute('data-labels'));
            const dataPie = JSON.parse(pieCanvas.getAttribute('data-values'));

            const ctxPie = pieCanvas.getContext('2d');
            new Chart(ctxPie, {
                type: 'pie', // Tipe diagram lingkaran
                data: {
                    labels: labelsPie,
                    datasets: [{
                        data: dataPie,
                        backgroundColor: [
                            '#198754', '#20c997', '#ffc107', '#fd7e14', '#dc3545', '#6f42c1'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1) + "%";
                                    return label + ': ' + value + ' item (' + percentage + ')';
                                }
                            }
                        }
                    }
                }
            });
        }

        // --- SISA CODING ASLI ANDA (Tab, Rupiah, Edit, Hapus, dll) ---
        const lastTab = localStorage.getItem('activeAdminTab');
        if (lastTab) {
            const targetBtn = document.querySelector(`button[data-bs-target="${lastTab}"]`);
            if (targetBtn) {
                const tabInstance = new bootstrap.Tab(targetBtn);
                tabInstance.show();
            }
        }

        $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
            localStorage.setItem('activeAdminTab', $(e.target).data('bs-target'));
        });

        $(document).on('input', '.rupiah-input', function() {
            let nilai = $(this).val().replace(/[^0-9]/g, '');
            if (nilai !== "") {
                let formatted = new Intl.NumberFormat('id-ID').format(nilai);
                $(this).val(formatted);
            } else {
                $(this).val('');
            }
        });

        $(document).on('click', '.btn-edit', function() {
            $('#edit_id').val($(this).data('produk_id'));
            $('#edit_barcode').val($(this).data('barcode'));
            $('#edit_nama').val($(this).data('nama_produk'));
            $('#edit_hargabeli').val($(this).data('harga_beli'));
            $('#edit_hargajual').val($(this).data('harga_jual'));
            $('#edit_stok').val($(this).data('stok'));
            $('#edit_kategori').val($(this).data('kategori'));
        });

        $(document).on('click', '.btn-hapus', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: "Data tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

        <?php if (session()->getFlashdata('pesan_sukses')) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= session()->getFlashdata('pesan_sukses') ?>',
            timer: 2000,
            showConfirmButton: false
        });
        <?php endif; ?>

        <?php if (session()->getFlashdata('pesan_error')) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?= session()->getFlashdata('pesan_error') ?>'
        });
        <?php endif; ?>
    });

    function updateClockAdmin() {
        const now = new Date();
        const options = {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        const el = document.getElementById('clockAdmin');
        if (el) el.innerText = now.toLocaleDateString('id-ID', options);
    }
    setInterval(updateClockAdmin, 1000);
    updateClockAdmin();
    $(document).ready(function() {
        $('.view-detail').on('click', function() {
            const id = $(this).data('id');
            const invoice = $(this).data('invoice');

            $('#text-invoice').text('#' + invoice);
            $('#modalDetail').modal('show');

            // Ambil data detail via AJAX
            $.ajax({
                url: "<?= base_url('admin/transaksi_detail') ?>/" + id,
                type: "GET",
                success: function(response) {
                    $('#isi-detail').html(response);
                }
            });
        });
    });
    </script>

    <div class="modal fade" id="logoutModalAdmin" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-5 px-5">
                    <div class="display-1 text-danger mb-3">
                        <i class="fas fa-door-open" style="opacity: 0.2;"></i>
                    </div>
                    <h3 class="fw-bold">Keluar Panel Admin?</h3>
                    <p class="text-muted">Anda harus login kembali untuk mengelola data produk, user, dan laporan.</p>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tetap di
                            Sini</button>
                        <a href="<?= base_url('logout') ?>" class="btn btn-danger rounded-pill px-4 shadow">Ya, Keluar
                            Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Detail Transaksi <span id="text-invoice"
                            class="text-success"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="isi-detail">
                        <p class="text-center">Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


</html>