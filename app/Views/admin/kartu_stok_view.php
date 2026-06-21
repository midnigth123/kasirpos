<?php

/**
 * @var string $tgl_mulai
 * @var string $tgl_selesai
 * @var array $list_produk
 * @var array $riwayat
 * @var string $produk_terpilih
 * @var object $pager
 * @var int $total
 * @var int $perPage
 * @var int $currentPage
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-md-12">
            <!-- Filter Card -->
            <div class="card border-0 shadow-lg mb-4" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-light py-3 border-bottom">
                    <h3 class="card-title font-weight-bold text-dark mb-0">
                        <i class="fas fa-layer-group mr-2 text-primary"></i> Log Mutasi Stok Global
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info p-2 shadow-sm">Barang Kering</span>
                    </div>
                </div>
                <div class="card-body bg-white p-4">
                    <form action="<?= base_url('admin/kartu_stok') ?>" method="get">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Periode Awal</label>
                                <div class="input-group custom-input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0"><i
                                                class="fas fa-calendar-alt text-primary"></i></span>
                                    </div>
                                    <input type="date" name="tgl_mulai" class="form-control border-0 bg-light"
                                        value="<?= $tgl_mulai ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Periode Akhir</label>
                                <div class="input-group custom-input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0"><i
                                                class="fas fa-calendar-check text-primary"></i></span>
                                    </div>
                                    <input type="date" name="tgl_selesai" class="form-control border-0 bg-light"
                                        value="<?= $tgl_selesai ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-xs font-weight-bold text-uppercase text-muted">Filter Produk</label>
                                <select name="produk_id" class="form-control select2 custom-select2">
                                    <option value="">-- Semua Produk --</option>
                                    <?php foreach ($list_produk as $p): ?>
                                    <option value="<?= $p['produk_id'] ?>"
                                        <?= ($produk_terpilih == $p['produk_id']) ? 'selected' : '' ?>>
                                        <?= $p['barcode'] ?> - <?= $p['nama_produk'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block btn-jedor shadow">
                                    <i class="fas fa-search-plus mr-2"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Luxury Table Card -->
            <div class="card border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped-custom mb-0" id="table-luxury">
                            <thead>
                                <tr>
                                    <th class="text-center text-header-custom">NO</th>
                                    <th class="text-header-custom">WAKTU & PRODUK</th>
                                    <th class="text-center text-header-custom">AKTIVITAS</th>
                                    <th class="text-header-custom">REFERENSI</th>
                                    <th class="text-center text-header-custom">STOK AWAL</th>
                                    <th class="text-center text-header-custom text-white">MASUK</th>
                                    <th class="text-center text-header-custom text-white">KELUAR</th>
                                    <th class="text-center text-header-custom text-header-akhir">AKHIR</th>
                                    <th class="text-header-custom">KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1 + ($perPage * ($currentPage - 1));
                                foreach ($riwayat as $r):
                                ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-muted"
                                        style="width: 60px;"><?= $no++ ?></td>
                                    <td class="align-middle py-3" style="min-width: 250px;">
                                        <div class="d-flex align-items-center">
                                            <div class="time-badge mr-3">
                                                <span
                                                    class="d-block small text-muted"><?= date('d M', strtotime($r['tanggal'])) ?></span>
                                                <span
                                                    class="d-block font-weight-bold text-time-hour"><?= date('H:i', strtotime($r['tanggal'])) ?></span>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-item-title h6 mb-0">
                                                    <?= $r['nama_produk'] ?></div>
                                                <span class="text-xs text-muted"><i class="fas fa-barcode mr-1"></i>
                                                    <?= $r['barcode'] ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php
                                            $tipe = trim(strtolower($r['tipe']));
                                            $ref  = trim(strtolower($r['kode_referensi']));

                                            $class = 'badge-soft-secondary';
                                            $icon = 'info-circle';
                                            $text_tampil = strtoupper($r['tipe']);

                                            if ($tipe == 'waste' || strpos($tipe, 'waste') !== false || strpos($ref, 'wst-') !== false) {
                                                $class = 'badge-soft-waste';
                                                $icon = 'trash-alt';
                                                $text_tampil = 'WASTE';
                                            } elseif (strpos($tipe, 'masuk') !== false || $tipe == 'tambah' || strpos($ref, 'pn-') !== false) {
                                                $class = 'badge-soft-success';
                                                $icon = 'arrow-down';
                                            } elseif (strpos($tipe, 'keluar') !== false || $tipe == 'kurang' || strpos($ref, 'inv-') !== false) {
                                                $class = 'badge-soft-danger';
                                                $icon = 'arrow-up';
                                            } elseif (strpos($tipe, 'opname') !== false || strpos($ref, 'opn-') !== false) {
                                                $class = 'badge-soft-warning';
                                                $icon = 'clipboard-check';
                                            }

                                            if (empty($text_tampil)) {
                                                $text_tampil = 'MUTASI';
                                            }
                                            ?>
                                        <span class="badge <?= $class ?> badge-pill px-3 py-2 shadow-sm">
                                            <i class="fas fa-<?= $icon ?> mr-1"></i> <?= $text_tampil ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="ref-code"><?= $r['kode_referensi'] ?></span>
                                    </td>
                                    <td class="text-center align-middle font-weight-bold text-muted">
                                        <?= number_format($r['stok_awal']) ?></td>
                                    <td class="text-center align-middle">
                                        <?php if ($r['stok_masuk'] > 0): ?>
                                        <div class="in-out-badge bg-success-soft text-success">
                                            +<?= number_format($r['stok_masuk']) ?></div>
                                        <?php else: ?>
                                        <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?php if ($r['stok_keluar'] > 0): ?>
                                        <div class="in-out-badge bg-danger-soft text-danger">
                                            -<?= number_format($r['stok_keluar']) ?></div>
                                        <?php else: ?>
                                        <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td
                                        class="text-center align-middle font-weight-bold text-primary bg-primary-soft h5 mb-0">
                                        <?= number_format($r['stok_akhir']) ?>
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted italic"><?= $r['keterangan'] ?: '-' ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-muted small font-weight-bold text-footer-info">
                            RECORD: <?= number_format($total) ?> DATA MUTASI
                        </div>
                        <div class="col-md-6">
                            <div class="float-md-right mt-2 mt-md-0">
                                <?= $pager->makeLinks($currentPage, $perPage, $total, 'default') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Typography & Core Helpers */
.text-xs {
    font-size: 0.75rem;
}

.bg-primary-soft {
    background-color: rgba(0, 123, 255, 0.08) !important;
}

.bg-success-soft {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

/* Custom Table Header JEDOR */
#table-luxury thead th.text-header-custom {
    background-color: #007bff !important;
    color: #ffffff !important;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 1px;
    padding: 1.2rem 1rem;
    border: none;
}

/* Spesifik Header Kolom AKHIR */
#table-luxury thead th.text-header-akhir {
    background-color: #17a2b8 !important;
    color: #ffffff !important;
}

/* Light Mode Default Colors */
.text-item-title {
    color: #212529 !important;
}

.text-time-hour {
    color: #212529 !important;
}

/* Row Hover & Alternating Backgrounds */
.table-striped-custom tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, .01);
}

#table-luxury tbody tr {
    transition: all 0.2s;
    border-bottom: 1px solid #eee;
}

#table-luxury tbody tr:hover {
    background-color: #f8fbff !important;
    transform: scale(1.001);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
}

/* Badges & Elements Styling */
.time-badge {
    background: #f4f6f9;
    padding: 5px 12px;
    border-radius: 8px;
    text-align: center;
    min-width: 65px;
    border: 1px solid #e9ecef;
}

.ref-code {
    background: #fff;
    border: 1px dashed #ced4da;
    padding: 3px 8px;
    border-radius: 5px;
    color: #6610f2;
    font-family: 'Courier New', Courier, monospace;
    font-weight: bold;
    font-size: 0.85rem;
}

.in-out-badge {
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 800;
    display: inline-block;
    min-width: 55px;
}

/* Badge Soft Pastels */
.badge-soft-success {
    background: #e2f6e9;
    color: #155724;
    font-weight: bold;
}

.badge-soft-danger {
    background: #fbe3e4;
    color: #721c24;
    font-weight: bold;
}

.badge-soft-warning {
    background: #fff3cd;
    color: #856404;
    font-weight: bold;
}

.badge-soft-secondary {
    background: #e2e3e5;
    color: #383d41;
    font-weight: bold;
}

.badge-soft-waste {
    background-color: #fce8e6 !important;
    color: #a82c2c !important;
    border: 1px solid #fbcbc7 !important;
    font-weight: bold;
}

/* Filter Action Button */
.btn-jedor {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: bold;
    transition: all 0.3s;
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
}

.btn-jedor:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
}

.custom-input-group {
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e9ecef;
}

/* JEDOR! Sembunyikan scrollbar di Chrome, Safari, Opera */
.table-responsive::-webkit-scrollbar {
    display: none;
}

/* Sembunyikan scrollbar di IE, Edge, dan Firefox */
.table-responsive {
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}

/* ==========================================================================
       DARK MODE ADAPTATION (JEDOR AUTOMATIC PRO)
       ========================================================================== */
.dark-mode #table-luxury thead th.text-header-custom {
    background-color: #1e2227 !important;
    color: #ffffff !important;
}

.dark-mode #table-luxury thead th.text-header-akhir {
    background-color: #138496 !important;
    color: #ffffff !important;
}

.dark-mode .card-header {
    background-color: #2b3035 !important;
}

.dark-mode .card-body {
    background-color: #2b3035 !important;
}

.dark-mode .bg-light {
    background-color: #343a40 !important;
    color: #fff !important;
}

.dark-mode .card-footer {
    background-color: #2b3035 !important;
    border-top-color: #444 !important;
}

.dark-mode #table-luxury tbody tr {
    border-bottom-color: #444;
}

.dark-mode .time-badge {
    background: #3d444b;
    border-color: #4f5962;
}

.dark-mode .text-item-title {
    color: #ffffff !important;
}

.dark-mode .text-time-hour {
    color: #ffffff !important;
}

.dark-mode .ref-code {
    background: #3d444b;
    border-color: #555;
    color: #a29bfe;
}

.dark-mode .text-footer-info {
    color: #abb2bf !important;
}

.dark-mode .badge-soft-waste {
    background-color: #4c1d1d !important;
    color: #fbcbc7 !important;
    border-color: #722c2c !important;
}
</style>

<script>
$(document).ready(function() {
    if ($('.select2').length) {
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "-- Semua Produk --",
            allowClear: true
        });
    }
});
</script>
<?= $this->endSection(); ?>