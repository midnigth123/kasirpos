<?php
/**
 * 
 * @var string $tgl_mulai
 * @var string $tgl_selesai
 */
?>

<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>


<div class="container-fluid py-4">
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0"><i class="fas fa-file-invoice me-2 text-success"></i> Laporan Barang Masuk</h5>
        </div>
        <div>
            <a href="<?= site_url('admin/laporan_penerimaan/excel?tgl_mulai=' . $tgl_mulai . '&tgl_selesai=' . $tgl_selesai) ?>"
                class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search"></i> Filter</button>

                    <a href="<?= site_url('admin/laporan_penerimaan') ?>" class="btn btn-light border">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Supplier</th>
                            <th>Nama Produk</th>
                            <th>Qty</th>
                            <th>Harga Beli</th>
                            <th>Subtotal</th>
                            <th>Expired</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grandTotal = 0;
                        if (!empty($laporan)): 
                            foreach ($laporan as $row): 
                                $subtotal = $row['jumlah_masuk'] * $row['harga_beli_baru'];
                                $grandTotal += $subtotal;
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_masuk'])) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= $row['kode_penerimaan'] ?></span></td>
                            <td><?= $row['supplier'] ?></td>
                            <td class="fw-bold"><?= $row['nama_produk'] ?></td>
                            <td><?= $row['jumlah_masuk'] ?></td>
                            <td>Rp <?= number_format($row['harga_beli_baru'], 0, ',', '.') ?></td>
                            <td class="fw-bold">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                            <td>
                                <small class="text-danger">
                                    <?= $row['tgl_expired'] ? date('d/m/Y', strtotime($row['tgl_expired'])) : '-' ?>
                                </small>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">Data tidak ditemukan untuk periode ini.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="6" class="text-end">TOTAL NILAI BARANG MASUK:</th>
                            <th colspan="2" class="text-success" style="font-size: 1.1rem;">
                                Rp <?= number_format($grandTotal, 0, ',', '.') ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>