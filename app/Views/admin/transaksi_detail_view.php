<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Laporan Transaksi Detail</h4>
            <p class="text-muted mb-0">Rekapitulasi rincian produk terjual (Hanya Transaksi Lunas)</p>
        </div>
    </div>

    <div class="card p-4 mb-4 border-0 shadow-sm rounded-4">
        <h6 class="fw-bold mb-3 text-main">Filter Periode Laporan</h6>
        <form method="GET" action="<?= site_url('admin/transaksi/detail') ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted">Tanggal Mulai</label>
                <input type="date" name="tgl_mulai" class="form-control rounded-3" value="<?= $tgl_mulai ?? '' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" class="form-control rounded-3" value="<?= $tgl_selesai ?? '' ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-success px-4 rounded-pill">
                    <i class="bi bi-filter me-1"></i> Tampilkan
                </button>
                <a href="<?= site_url('admin/transaksi/detail') ?>"
                    class="btn btn-outline-secondary px-4 rounded-pill">Reset</a>
            </div>
        </form>
    </div>

    <div class="card p-4 border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Nama Produk</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-center">Total Qty</th>
                        <th class="text-end">Total Pendapatan (Subtotal)</th>
                    </tr>
                </thead>
                <tbody class="text-main">
                    <?php if (!empty($detail_transaksi)): ?>
                        <?php $no = 1; $grand_total = 0; $grand_qty = 0; ?>
                        <?php foreach ($detail_transaksi as $row): ?>
                            <?php 
                                $grand_qty += $row['total_qty'];
                                $grand_total += $row['total_subtotal'];
                                // Mengambil harga satuan
                                $harga_satuan = $row['total_qty'] > 0 ? ($row['total_subtotal'] / $row['total_qty']) : 0;
                            ?>
                            <tr>
                                <td class="text-center text-muted"><?= $no++; ?></td>
                                <td class="fw-bold text-main"><?= esc($row['nama_produk']); ?></td>
                                <td class="text-end text-muted">Rp <?= number_format($harga_satuan, 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                        <?= $row['total_qty']; ?> Terjual
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp <?= number_format($row['total_subtotal'], 0, ',', '.'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="table-light border-top border-dark border-2">
                            <td colspan="3" class="text-end fw-bold text-main">Total Keseluruhan :</td>
                            <td class="text-center fw-bold text-main"><?= $grand_qty; ?></td>
                            <td class="text-end fw-bold text-success fs-5">
                                Rp <?= number_format($grand_total, 0, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-search fs-2 mb-2"></i>
                                <p class="mb-0">Tidak ada data untuk rentang tanggal yang dipilih.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-4 gap-2">
            <a href="<?= site_url('admin/transaksi/cetak') . '?' . http_build_query(['tgl_mulai' => $tgl_mulai ?? '', 'tgl_selesai' => $tgl_selesai ?? '']) ?>"
                target="_blank" class="btn btn-dark rounded-pill px-4">
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>