<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

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

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
</head>
<style>
:root {
    --bg-body: #f1f2ed;
    --card-radius: 12px;
    --primary-green: #198754;
}

.wrapper-full {
    width: 100%;
    padding: 10px 15px;
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

.card-monitoring {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    transition: transform 0.2s;
}

.card-monitoring:hover {
    transform: translateY(-3px);
}

.monitoring-value {
    font-size: 1.8rem;
    font-weight: 700;
}

.bg-soft-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
    color: #198754;
}

.bg-soft-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
    color: #ffc107;
}

.bg-soft-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
    color: #dc3545;
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
</style>

<div class="wrapper-full">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center justify-content-center"
                style="width: 55px; height: 55px; overflow: hidden;">
                <img src="<?= base_url('assets/img/icon_kasir.png') ?>" alt="Logo Toko"
                    style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <h4 class="fw-bold mb-0">KasirKita Management</h4>
        </div>
    </div>

    <div id="broadcast-container-admin" class="mb-4">
        <?php if (!empty($broadcast_pusat)) : ?>
        <div id="box-broadcast-admin" class="card bg-danger text-white border-0 shadow-sm"
            style="border-radius: 8px; overflow: hidden;">
            <div class="card-body p-0 d-flex align-items-center">
                <div class="bg-dark text-white p-2 px-3 fw-bold text-uppercase d-flex align-items-center gap-2"
                    style="flex-shrink: 0; font-size: 0.85rem;">
                    <i class="fas fa-bullhorn animate__animated animate__flash animate__infinite text-warning"></i>
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

    <ul class="nav nav-pills nav-pill-custom mb-4" id="pills-tab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="tab-ringkasan-btn" data-bs-toggle="pill" data-bs-target="#tab-ringkasan"
                type="button" role="tab">Dashboard Pendapatan</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tab-monitoring-btn" data-bs-toggle="pill" data-bs-target="#tab-monitoring"
                type="button" role="tab">Monitoring Inventory</button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">

        <div class="tab-pane fade show active" id="tab-ringkasan" role="tabpanel">
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card p-4">
                        <div class="stat-label text-uppercase">Pendapatan Hari Ini / Mingguan</div>
                        <div class="stat-value text-success">
                            Rp <?= number_format($total_pendapatan ?? 0, 0, ',', '.') ?> /
                            <span class="text-primary">Rp
                                <?= number_format($total_pendapatan_mingguan ?? 0, 0, ',', '.') ?></span>
                        </div>
                        <?php $persentase_val = $persentase ?? 0; ?>
                        <?php if ($persentase_val > 0): ?>
                        <div class="stat-sub text-success small fw-semibold mt-1">
                            <i class="fas fa-arrow-up"></i> +<?= $persentase_val ?>% vs kemarin
                        </div>
                        <?php elseif ($persentase_val < 0): ?>
                        <div class="stat-sub text-danger small fw-semibold mt-1">
                            <i class="fas fa-arrow-down"></i> <?= $persentase_val ?>% vs kemarin
                        </div>
                        <?php else: ?>
                        <div class="stat-sub text-muted small fw-semibold mt-1">
                            <i class="fas fa-minus"></i> 0% vs kemarin
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-4">
                        <div class="stat-label text-uppercase">Jumlah Transaksi Harian / Mingguan</div>
                        <div class="stat-value text-success fs-4 fw-bold mt-1">
                            <?= $jumlah_transaksi ?? 0 ?> /
                            <span class="text-primary"><?= $jumlah_transaksi_mingguan ?? 0 ?></span>
                        </div>
                        <div class="stat-sub text-muted">Total nota sukses</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-4">
                        <div class="stat-label text-uppercase">Rata-rata / Transaksi</div>
                        <div class="stat-value">Rp <?= number_format($rata_rata ?? 0, 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card p-4">
                        <div class="stat-label text-uppercase">Item Terjual</div>
                        <div class="stat-value"><?= $item_terjual ?? 0 ?> <span class="fs-6 fw-normal">Pcs</span></div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="card p-4">
                        <h6 class="fw-bold mb-4 text-success">
                            <i class="fas fa-chart-line me-2"></i>Grafik Pendapatan Harian
                        </h6>
                        <div style="height: 320px; position: relative;">
                            <canvas id="revenueChart"
                                data-labels="<?= htmlspecialchars($chart_labels ?? '[]', ENT_QUOTES, 'UTF-8') ?>"
                                data-values="<?= htmlspecialchars($chart_data ?? '[]', ENT_QUOTES, 'UTF-8') ?>">
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="card p-4 h-100">
                        <h6 class="fw-bold mb-4">Persentase Produk Terlaris</h6>
                        <div style="height: 420px; position: relative;">
                            <canvas id="productPieChart"
                                data-labels="<?= htmlspecialchars($pie_labels ?? '[]', ENT_QUOTES, 'UTF-8') ?>"
                                data-values="<?= htmlspecialchars($pie_data ?? '[]', ENT_QUOTES, 'UTF-8') ?>">
                            </canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card p-4 border-0 shadow-sm h-100" style="border-radius: 15px;">
                        <h6 class="fw-bold mb-4 text-dark">
                            <i class="fas fa-wallet me-2 text-primary"></i>Metode Bayar Terpopuler
                        </h6>
                        <div class="py-2">
                            <?php if (!empty($metode_populer)): ?>
                            <?php foreach ($metode_populer as $mp): ?>
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="fw-bold d-block mb-0"><?= $mp['metode'] ?></span>
                                        <small class="text-muted" style="font-size: 11px;"><?= $mp['jumlah'] ?>
                                            Transaksi Sukses</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="fw-bold text-dark"><?= $mp['persen'] ?>%</span>
                                    </div>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 10px; background-color: #eee;">
                                    <div class="progress-bar bg-primary progress-animate" role="progressbar"
                                        style="width: 0%; transition: width 1.5s ease-in-out;"
                                        data-width="<?= $mp['persen'] ?>%">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <div class="text-center py-5 text-muted small">
                                Belum ada data transaksi (Bukan Batal).
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-monitoring" role="tabpanel">
            <div class="row g-3 mb-4">
                <div class="col-md">
                    <div class="card p-3 h-100 shadow-sm border-0" style="border-radius: 14px;">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-circle bg-success-subtle text-success me-3"><i
                                    class="fas fa-boxes fa-lg"></i></div>
                            <div>
                                <span class="text-uppercase text-muted small fw-bold">Produk / Stok</span>
                                <div class="monitoring-value fs-4 fw-bold text-dark">
                                    <?= $total_produk ?? 0 ?> / <span
                                        class="text-success"><?= $total_stok ?? 0 ?></span>
                                </div>
                                <small class="text-secondary d-block">Jenis / Total Fisik</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card p-3 h-100 shadow-sm border-0" style="border-radius: 14px;">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-circle bg-warning-subtle text-warning me-3"><i
                                    class="fas fa-exclamation-triangle fa-lg"></i></div>
                            <div>
                                <span class="text-uppercase text-muted small fw-bold">Stok Menipis</span>
                                <div class="monitoring-value fs-4 fw-bold text-warning"><?= $stok_menipis ?? 0 ?></div>
                                <small class="text-secondary d-block">Stok Mendekati Habis</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card p-3 h-100 shadow-sm border-0" style="border-radius: 14px;">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-circle bg-danger-subtle text-danger me-3"><i
                                    class="fas fa-times-circle fa-lg"></i></div>
                            <div>
                                <span class="text-uppercase text-muted small fw-bold">Stok Habis</span>
                                <div class="monitoring-value fs-4 fw-bold text-danger"><?= $stok_habis ?? 0 ?></div>
                                <small class="text-secondary d-block">Produk</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card p-3 h-100 shadow-sm border-0" style="border-radius: 14px;">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-circle bg-success text-white me-3"><i
                                    class="fas fa-wallet fa-lg"></i></div>
                            <div>
                                <span class="text-uppercase text-muted small fw-bold">Nilai Inventory</span>
                                <div class="monitoring-value fs-5 fw-bold text-dark mt-1">
                                    Rp <?= number_format($nilai_inventory ?? 0, 0, ',', '.') ?>
                                </div>
                                <small class="text-secondary d-block">Total Aset Barang</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-8">
                    <div class="card p-4 card-monitoring h-100">
                        <h6 class="fw-bold mb-4"><i class="fas fa-chart-bar text-success me-2"></i>Pergerakan Stok:
                            Masuk vs Keluar</h6>
                        <div style="height: 320px; position: relative;">
                            <canvas id="stokMovementChart"
                                data-labels="<?= htmlspecialchars($chart_stok_labels ?? '[]', ENT_QUOTES, 'UTF-8') ?>"
                                data-masuk="<?= htmlspecialchars($chart_stok_masuk ?? '[]', ENT_QUOTES, 'UTF-8') ?>"
                                data-keluar="<?= htmlspecialchars($chart_stok_keluar ?? '[]', ENT_QUOTES, 'UTF-8') ?>">
                            </canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 card-monitoring h-100">
                        <h6 class="fw-bold mb-3"><i class="fas fa-trophy text-warning me-2"></i>Top Produk Terbanyak
                            Keluar</h6>
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($top_produk_keluar)): ?>
                            <?php foreach ($top_produk_keluar as $index => $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="badge bg-success-subtle text-success me-3 py-1 px-2"><?= $index + 1 ?></span>
                                    <span class="fw-semibold text-dark"><?= $item['nama_produk'] ?></span>
                                </div>
                                <span class="badge bg-success rounded-pill"><?= $item['total_keluar'] ?> pcs</span>
                            </li>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <li class="list-group-item text-center py-3 text-muted small">Data belum tersedia</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card p-4 border-0 card-monitoring h-100"
                        style="background: #fff5f5; border-left: 5px solid #fcf29ccb !important;">
                        <h6 class="fw-bold text-warning mb-3"><i class="fas fa-exclamation-circle me-2"></i>🚨 Produk
                            Hampir Habis</h6>
                        <ul class="list-unstyled mb-0">
                            <?php if (!empty($notif_menipis)): ?>
                            <?php foreach ($notif_menipis as $item): ?>
                            <li class="d-flex justify-content-between border-bottom py-2">
                                <span><?= $item['nama_produk'] ?></span>
                                <span class="badge bg-warning text-dark">Stok: <?= $item['stok'] ?></span>
                            </li>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <li class="text-muted small">Tidak ada produk yang menipis.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 border-0 card-monitoring h-100"
                        style="background: #fff5f5; border-left: 5px solid #dc3545 !important;">
                        <h6 class="fw-bold text-danger mb-3"><i class="fas fa-history me-2"></i>⏰ Kadaluwarsa / Expired
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <?php if (!empty($produk_expired)): ?>
                            <?php foreach ($produk_expired as $item):
                                    $tgl_exp = date_create($item['tgl_expired']);
                                    $sekarang = date_create(date('Y-m-d'));
                                    $selisih = date_diff($sekarang, $tgl_exp)->format("%r%a");
                                ?>
                            <li class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <div style="max-width: 70%;">
                                    <span
                                        class="text-dark fw-medium d-block text-truncate"><?= $item['nama_produk'] ?></span>
                                    <small class="text-danger fw-bold"><?= $item['total_qty'] ?> Pcs</small>
                                    <span class="text-muted small">| Exp:
                                        <?= date('d M Y', strtotime($item['tgl_expired'])) ?></span>
                                </div>
                                <?php if ($selisih <= 0): ?>
                                <span class="badge bg-dark rounded-pill">Expired</span>
                                <?php elseif ($selisih <= 7): ?>
                                <span class="badge bg-danger rounded-pill"><?= $selisih ?> hr lagi</span>
                                <?php else: ?>
                                <span class="badge bg-warning text-dark rounded-pill"><?= $selisih ?> hr lagi</span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <li class="text-center py-4">
                                <i class="fas fa-check-circle text-success mb-2" style="font-size: 1.5rem;"></i>
                                <p class="text-muted small mb-0">Stok aman. Tidak ada produk kadaluwarsa.</p>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 border-0 card-monitoring h-100"
                        style="background: #fff5f5; border-left: 5px solid #174af0cb !important;">
                        <h6 class="fw-bold text-primary mb-3"><i class="fas fa-dolly-flatbed me-2"></i>📥 Barang Masuk
                            Pending</h6>
                        <ul class="list-unstyled mb-0">
                            <?php if (!empty($notif_pending)): ?>
                            <?php foreach ($notif_pending as $item): ?>
                            <li class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span class="text-dark fw-medium"><?= $item['nama_produk'] ?? 'Barang Masuk' ?></span>
                                <div>
                                    <span class="badge bg-warning text-dark me-1"><?= $item['jumlah_masuk'] ?? '0' ?>
                                        Pcs</span>
                                    <span class="badge bg-primary">Pending</span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <li class="text-muted small py-2">Tidak ada barang pending.</li>
                            <?php endif; ?>
                        </ul>
                        <a href="<?= site_url('admin/penerimaan') ?>" class="btn btn-sm btn-primary w-100 mt-3">
                            <i class="fas fa-clipboard-check me-1"></i> Kelola Penerimaan
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Detail Transaksi <span id="text-invoice" class="text-success"></span>
                </h5>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let revenueChart, productPieChart;

    function initCharts() {
        const canvasEl = document.getElementById('revenueChart');
        if (canvasEl) {
            const rawLabels = canvasEl.getAttribute('data-labels');
            const rawValues = canvasEl.getAttribute('data-values');

            if (rawLabels && rawValues && rawLabels !== '[]' && rawValues !== '[]') {
                try {
                    const labelsDariDB = JSON.parse(rawLabels);
                    const dataDariDB = JSON.parse(rawValues);

                    if (revenueChart) revenueChart.destroy();

                    const ctx = canvasEl.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 350);
                    gradient.addColorStop(0, 'rgba(25, 135, 84, 0.2)');
                    gradient.addColorStop(1, 'rgba(25, 135, 84, 0)');

                    revenueChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labelsDariDB,
                            datasets: [{
                                label: 'Pendapatan',
                                data: dataDariDB,
                                backgroundColor: gradient,
                                borderColor: '#198754',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#198754',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 7
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animations: {
                                y: {
                                    type: 'number',
                                    easing: 'easeOutQuart',
                                    duration: 1500,
                                    from: (ctx) => ctx.chart.scales.y.getPixelForValue(0),
                                    delay(ctx) {
                                        return ctx.index * 150;
                                    }
                                },
                                opacity: {
                                    duration: 1000,
                                    from: 0,
                                    to: 1
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    callbacks: {
                                        label: (ctx) => ` Rp ${ctx.parsed.y.toLocaleString('id-ID')}`
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        callback: (val) => 'Rp ' + val.toLocaleString('id-ID')
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                } catch (e) {
                    console.error("Error parsing revenue data", e);
                }
            }
        }

        const pieCanvas = document.getElementById('productPieChart');
        if (pieCanvas) {
            const rawLabelsPie = pieCanvas.getAttribute('data-labels');
            const rawDataPie = pieCanvas.getAttribute('data-values');

            if (rawLabelsPie && rawDataPie && rawLabelsPie !== '[]' && rawDataPie !== '[]') {
                try {
                    const labelsPie = JSON.parse(rawLabelsPie);
                    const dataPie = JSON.parse(rawDataPie);

                    if (productPieChart) productPieChart.destroy();

                    productPieChart = new Chart(pieCanvas.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: labelsPie,
                            datasets: [{
                                data: dataPie,
                                backgroundColor: ['#198754', '#20c997', '#ffc107', '#fd7e14',
                                    '#dc3545', '#6f42c1'
                                ],
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 15
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 2000,
                                easing: 'easeOutQuart',
                                animateRotate: true,
                                animateScale: true
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 20,
                                        usePointStyle: true
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.label || '';
                                            let value = context.parsed;
                                            let total = context.dataset.data.reduce((a, b) => a + b,
                                                0);
                                            let percentage = ((value / total) * 100).toFixed(1) +
                                                "%";
                                            return ` ${label}: ${value} item (${percentage})`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } catch (e) {
                    console.error("Error parsing pie data", e);
                }
            }
        }

        const ctxStok = document.getElementById('stokMovementChart');
        if (ctxStok) {
            const labelsStok = JSON.parse(ctxStok.getAttribute('data-labels') || '[]');
            const dataMasuk = JSON.parse(ctxStok.getAttribute('data-masuk') || '[]');
            const dataKeluar = JSON.parse(ctxStok.getAttribute('data-keluar') || '[]');

            new Chart(ctxStok.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labelsStok,
                    datasets: [{
                            label: 'Stok Masuk',
                            data: dataMasuk,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Stok Keluar',
                            data: dataKeluar,
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animations: {
                        y: {
                            easing: 'easeInOutQuart',
                            duration: 1500
                        },
                        x: {
                            easing: 'easeInOutQuart',
                            duration: 1500
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    }

    initCharts();

    const ringkasanTabEl = document.querySelector('#tab-ringkasan-btn');
    if (ringkasanTabEl) {
        ringkasanTabEl.addEventListener('shown.bs.tab', function() {
            setTimeout(() => {
                initCharts();
            }, 100);
        });
    }

    $(document).on('click', '.view-detail', function() {
        const id = $(this).data('id');
        const invoice = $(this).data('invoice');
        $('#text-invoice').text('#' + invoice);
        $('#modalDetail').modal('show');

        $.ajax({
            url: "<?= base_url('admin/transaksi_detail') ?>/" + id,
            type: "GET",
            success: function(response) {
                $('#isi-detail').html(response);
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // 1. Animasi Progress Bar
    const progressBars = document.querySelectorAll('.progress-animate');
    progressBars.forEach(bar => {
        setTimeout(() => {
            const targetWidth = bar.getAttribute('data-width');
            bar.style.width = targetWidth;
        }, 100);
    });

    // 2. Animasi Angka Persen (Counting Up)
    const counters = document.querySelectorAll('.count-up');
    const speed = 50;

    counters.forEach(counter => {
        const animate = () => {
            const value = +counter.getAttribute('data-target');
            const data = +counter.innerText;
            const time = value / speed;

            if (data < value) {
                counter.innerText = Math.ceil(data + time);
                setTimeout(animate, 20);
            } else {
                counter.innerText = value;
            }
        };
        animate();
    });
});

// ========================================================================
// 🎯 JAVASCRIPT REAL-TIME AUTO SYNC KHUSUS DASHBOARD ADMIN (SUDAH AKTIF!)
// ========================================================================
document.addEventListener("DOMContentLoaded", function() {
    setInterval(function() {
        fetch("<?= site_url('admin/cek_broadcast_realtime') ?>?_=" + new Date().getTime())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Jalur AJAX terganggu.');
                }
                return response.json();
            })
            .then(data => {
                const containerAdmin = document.getElementById('broadcast-container-admin');
                if (!containerAdmin) return;

                let boxBroadcastAdmin = document.getElementById('box-broadcast-admin');

                // 🟢 JIKA SIARAN MASIH AKTIF -> TAMPILKAN / SINKRONKAN
                if (data.aktif === true) {
                    if (!boxBroadcastAdmin) {
                        containerAdmin.innerHTML = `
                            <div id="box-broadcast-admin" class="card bg-danger text-white border-0 shadow-sm" style="border-radius: 8px; overflow: hidden;">
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
                        let marqueeElement = boxBroadcastAdmin.querySelector('marquee');
                        let kontenTerbaru =
                        `<strong>[${data.judul}]</strong> &mdash; ${data.pesan}`;

                        if (marqueeElement && marqueeElement.innerHTML !== kontenTerbaru) {
                            marqueeElement.innerHTML = kontenTerbaru;
                        }
                    }
                }
                // 🔴 JIKA SIARAN DIMATIKAN -> KOTAK HAPUS OTOMATIS TANPA REFRESH
                else {
                    if (boxBroadcastAdmin) {
                        boxBroadcastAdmin.style.transition = "all 0.4s ease";
                        boxBroadcastAdmin.style.opacity = "0";
                        boxBroadcastAdmin.style.transform = "translateY(-5px)";

                        setTimeout(() => {
                            boxBroadcastAdmin.remove();
                        }, 400);
                    }
                }
            })
            .catch(error => console.log("Radar admin standby..."));
    }, 5000);
});
</script>

<?= $this->endSection() ?>