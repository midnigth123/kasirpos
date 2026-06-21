<?php
/**
 * @var array $riwayat_waste
 * @var array $bahan_baku
 * @var array $p
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-dark">Manajemen Waste</h4>
            <p class="text-muted small">Kelola bahan baku yang rusak atau kadaluwarsa untuk menjaga akurasi stok.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-plus-circle me-2 text-danger"></i>Input Waste Baru
                    </h6>
                </div>
                <div class="card-body pt-0">
                    <form action="<?= site_url('admin/simpan_waste') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Bahan Baku</label>
                            <select name="produk_id" class="form-select shadow-none border-light-subtle"
                                style="border-radius: 10px;" required>
                                <option value="" selected disabled>-- Pilih Bahan --</option>
                                <?php foreach ($bahan_baku as $b) : ?>
                                <option value="<?= $b->produk_id ?>"><?= $b->nama_produk ?> (Stok: <?= $b->stok ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Jumlah (Qty)</label>
                            <input type="number" name="qty_waste" step="0.01"
                                class="form-control shadow-none border-light-subtle" style="border-radius: 10px;"
                                placeholder="0.00" required>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-muted mb-1">Alasan</label>
                            <textarea name="alasan" class="form-control shadow-none border-light-subtle"
                                style="border-radius: 10px;" rows="3" placeholder="Contoh: Barang kadaluwarsa..."
                                required></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold shadow-sm py-2">
                            <i class="fas fa-save me-2"></i>Simpan & Potong Stok
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-history me-2 text-muted"></i>Riwayat Barang Waste
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 180px;">Tanggal & Jam</th>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th>Alasan</th>
                                    <th class="pe-4 text-end">Oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($riwayat_waste)): ?>
                                <?php foreach($riwayat_waste as $w): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="text-dark fw-bold" style="font-size: 13px;">
                                            <?= date('d M Y', strtotime($w->created_at)) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            <i class="far fa-clock me-1 text-info"></i>
                                            <?= date('H:i', strtotime($w->created_at)) ?> WIB
                                        </div>
                                    </td>
                                    <td class="fw-bold text-dark"><?= $w->nama_produk ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">
                                            <?= $w->qty_waste ?>
                                        </span>
                                    </td>
                                    <td class="small text-muted"><?= $w->alasan ?></td>
                                    <td class="pe-4 text-end">
                                        <span class="badge bg-dark px-3 py-2"
                                            style="font-weight: 600; font-size: 10px;">
                                            <?= strtoupper($w->nama_user) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted small">
                                        <i class="fas fa-box-open fa-3x d-block mb-3 opacity-25"></i>
                                        Belum ada data waste yang tercatat hari ini.
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
</div>

<style>
/* Tambahkan ini agar tampilan lebih JEDOR */
.bg-danger-subtle {
    background-color: rgba(220, 53, 69, 0.1);
}

.table thead th {
    border: none;
    letter-spacing: 0.5px;
}

.table tbody td {
    border-color: rgba(0, 0, 0, 0.03);
}
</style>

<?= $this->endSection() ?>