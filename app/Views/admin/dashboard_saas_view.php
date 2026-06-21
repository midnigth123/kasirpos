<?php

/**
 * @var string $total_omset
 * @var string $total_transaksi
 * @var string $total_tenant_aktif
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<style>
.card-stat {
    border-radius: 15px;
    border: none;
    transition: all 0.3s ease;
}

.card-stat:hover {
    transform: translateY(-5px);
}

.icon-shape {
    width: 48px;
    height: 48px;
    background-color: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
}

.table-dashboard thead {
    background-color: #f8f9fa;
    color: #4e73df;
}

.table-dashboard th {
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    padding: 15px;
}

.table-dashboard td {
    padding: 15px;
    vertical-align: middle;
}
</style>

<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Dashboard Tenant KasirKita</h3>
            <p class="text-muted small mb-0">Monitoring akumulasi bisnis, performa transaksi, dan omset seluruh tenant
                Multi-Branch secara real-time.</p>
        </div>
        <div>
            <span class="badge bg-dark px-3 py-2" style="border-radius: 10px;">
                <i class="fas fa-clock me-1"></i> Terakhir Diperbarui: <?= date('d M Y - H:i') ?> WIB
            </span>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-stat bg-primary text-white shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small text-uppercase fw-bold">Total Omset Semua</span>
                        <h2 class="fw-bold mt-2 mb-0">Rp <?= number_format($total_omset, 0, ',', '.') ?></h2>
                    </div>
                    <div class="icon-shape">
                        <i class="fas fa-wallet fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stat bg-success text-white shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small text-uppercase fw-bold">Total Nota Terbit</span>
                        <h2 class="fw-bold mt-2 mb-0"><?= number_format($total_transaksi, 0, ',', '.') ?> Transaksi</h2>
                    </div>
                    <div class="icon-shape">
                        <i class="fas fa-receipt fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stat bg-info text-white shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-white-50 small text-uppercase fw-bold">Tenant Aktif Beroperasi</span>
                        <h2 class="fw-bold mt-2 mb-0"><?= $total_tenant_aktif ?> Outlet</h2>
                    </div>
                    <div class="icon-shape">
                        <i class="fas fa-store fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-none shadow-sm p-4" style="border-radius: 15px;">
                <div class="border-bottom pb-3 mb-3">
                    <h5 class="fw-bold text-dark mb-1"><i class="fas fa-chart-line text-primary me-2"></i>Rangking
                        Performa Finansial Cabang</h5>
                    <p class="text-muted small mb-0">Rincian data penarikan otomatis dari masing-masing database client.
                    </p>
                </div>

                <div class="table-responsive">
                    <table class="table table-dashboard table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 8%;">Rank</th>
                                <th>Nama Outlet / Tenant</th>
                                <th>Nama Database Local</th>
                                <th class="text-center">Total Penjualan (Nota)</th>
                                <th class="text-end">Kontribusi Omset</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($laporan_cabang)) : ?>
                            <?php 
                                // Urutkan laporan_cabang berdasarkan omset tertinggi ke terendah (Rangking)
                                usort($laporan_cabang, function($a, $b) {
                                    return $b['omset'] <=> $a['omset'];
                                });
                                $no = 1;
                                foreach ($laporan_cabang as $cabang) : 
                                ?>
                            <tr>
                                <td>
                                    <?php if ($no == 1) : ?>
                                    <span class="badge bg-warning text-dark px-2.5 py-1.5 rounded-circle fw-bold"><i
                                            class="fas fa-crown"></i> 1</span>
                                    <?php else : ?>
                                    <span
                                        class="badge bg-light text-secondary px-2.5 py-1.5 rounded-circle fw-bold"><?= $no ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-secondary"><?= $cabang['nama_toko'] ?></td>
                                <td><code
                                        class="px-2 py-1 bg-light text-danger rounded small"><?= $cabang['nama_database'] ?></code>
                                </td>
                                <td class="text-center fw-medium text-dark"><i
                                        class="fas fa-shopping-basket text-muted me-1"></i>
                                    <?= number_format($cabang['total_nota'], 0, ',', '.') ?> Nota</td>
                                <td class="text-end fw-bold text-success" style="font-size: 1.05rem;">Rp
                                    <?= number_format($cabang['omset'], 0, ',', '.') ?></td>
                            </tr>
                            <?php $no++; endforeach; ?>
                            <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-database fa-3x mb-3 text-light"></i><br>
                                    Tidak ada data transaksi yang berhasil ditarik. Pastikan database client aktif dan
                                    memiliki tabel transaksi, Bos!
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>