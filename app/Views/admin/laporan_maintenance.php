<?php
/**
 * @var array $filter
 * @var array $daftar_aset
 * @var array $laporan
 */
?>

<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
    <div>
        <h4 class="fw-bold text-success mb-1"><i class="fas fa-file-alt me-2"></i> Laporan Maintenance</h4>
        <p class="text-muted small mb-0">Cetak riwayat pemeliharaan aset</p>
    </div>
    <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 shadow-sm">
        <i class="fas fa-print me-1"></i> Cetak Laporan
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4 d-print-none">
    <div class="card-body p-3">
        <form action="" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">DARI TANGGAL</label>
                <input type="date" name="start_date" class="form-control bg-light border-0"
                    value="<?= $filter['start_date'] ?>">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">SAMPAI TANGGAL</label>
                <input type="date" name="end_date" class="form-control bg-light border-0"
                    value="<?= $filter['end_date'] ?>">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">PILIH ASET (OPSIONAL)</label>
                <select name="id_aset" class="form-select bg-light border-0">
                    <option value="">Semua Aset</option>
                    <?php foreach($daftar_aset as $a): ?>
                    <option value="<?= $a['id_aset'] ?>" <?= $filter['id_aset'] == $a['id_aset'] ? 'selected' : '' ?>>
                        <?= $a['nama_aset'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success w-100 fw-bold">Filter Laporan</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <h3 class="fw-bold mb-0"></h3>
            <p class="mb-0">Laporan Pemeliharaan Aset</p>
            <?php if($filter['start_date']): ?>
            <small class="text-muted">Periode: <?= date('d/m/Y', strtotime($filter['start_date'])) ?> -
                <?= date('d/m/Y', strtotime($filter['end_date'])) ?></small>
            <?php endif; ?>
            <hr>
        </div>

        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Tanggal</th>
                    <th>Nama Aset</th>
                    <th>Jenis</th>
                    <th>Teknisi</th>
                    <th class="text-end">Biaya</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; $no = 1; foreach($laporan as $l): $total += $l['biaya']; ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($l['tgl_maintenance'])) ?></td>
                    <td>
                        <div class="fw-bold"><?= $l['nama_aset'] ?></div>
                        <small class="text-muted"><?= $l['kode_aset'] ?></small>
                    </td>
                    <td><?= $l['jenis_tindakan'] ?></td>
                    <td><?= $l['teknisi'] ?></td>
                    <td class="text-end">Rp <?= number_format($l['biaya'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <td colspan="5" class="text-center">TOTAL PENGELUARAN MAINTENANCE</td>
                    <td class="text-end text-success">Rp <?= number_format($total, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="row mt-5 d-none d-print-flex">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p class="mb-5">Padang, <?= date('d F Y') ?></p>
                <br><br>
                <p class="fw-bold text-decoration-underline">Manager KasiKita</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background: white !important;
    }

    .card {
        box-shadow: none !important;
        /* Ganti shadow jadi box-shadow */
        border: none !important;
    }

    .d-print-none {
        display: none !important;
    }

    .d-print-flex {
        display: flex !important;
    }
}
</style>
<?= $this->endSection() ?>