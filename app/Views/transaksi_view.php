<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Laporan Transaksi</h4>
            <p class="text-muted mb-0">Riwayat seluruh aktivitas pembayaran</p>
        </div>
    </div>

    <div class="card p-4 mb-4">
        <h6 class="fw-bold mb-3">Filter Riwayat Transaksi</h6>
        <form method="GET" action="<?= site_url('admin/transaksi') ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Tanggal Mulai</label>
                <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?? '' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small">Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?? '' ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success me-2">Filter Data</button>
                <a href="<?= site_url('admin/transaksi') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="card p-4">
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
                            <a href="javascript:void(0)" class="text-decoration-none fw-bold text-primary view-detail"
                                data-id="<?= $row['id'] ?>" data-invoice="<?= $row['invoice'] ?>">
                                #<?= $row['invoice'] ?>
                            </a>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= $row['subtotal'] ?? 0 ?> Item</td>
                        <td class="fw-bold">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                        <td><span class="badge bg-success-subtle text-success rounded-pill px-3">Lunas</span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada transaksi untuk tanggal ini.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            <?php 
    if (!empty($pager_transaksi)) {
        echo $pager_transaksi->links('transaksi', 'custom_pager');
    }
    ?>
        </div>
    </div>

    <?= $this->endSection() ?>