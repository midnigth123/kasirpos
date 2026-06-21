<?php

/**
 * 
 * @var array $produk
 * @var array $daftar_toko
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-truck-moving"></i> Mutasi Stok Antar Cabang
                    </h3>
                </div>
                <div class="card-body">
                    <div class="bg-light p-4 mb-4 border rounded">
                        <h5><i class="fas fa-paper-plane"></i> Form Kirim Barang</h5>
                        <hr>
                        <form action="<?= base_url('admin/mutasi_barang') ?>" method="post">
                            <?= csrf_field(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Produk (Toko Asal)</label>
                                        <select name="id_produk" class="form-control select2" required>
                                            <option value="">-- Cari Nama / Barcode --</option>
                                            <?php foreach ($produk as $p) : ?>
                                                <option value="<?= $p['produk_id'] ?>">
                                                    <?= $p['barcode'] ?> - <?= $p['nama_produk'] ?> (Stok: <?= $p['stok'] ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Hanya produk yang tersedia di toko ini.</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Cabang Tujuan</label>
                                        <select name="db_tujuan" class="form-control" required>
                                            <option value="">-- Pilih Cabang Penerima --</option>
                                            <?php foreach ($daftar_toko as $t) : ?>
                                                <?php if ($t['nama_database'] != session()->get('db_client')) : ?>
                                                    <option value="<?= $t['nama_database'] ?>"><?= $t['nama_toko'] ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Jumlah</label>
                                        <input type="number" name="jumlah" class="form-control" min="1" placeholder="0" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-info btn-block shadow-sm">
                                        <i class="fas fa-share-square"></i> Kirim Stok
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <h5><i class="fas fa-history"></i> Riwayat Mutasi Terakhir</h5>
                        <table class="table table-hover table-bordered table-striped" id="tabel-mutasi">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Waktu</th>
                                    <th>No. Mutasi</th>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Tujuan</th>
                                    <th>Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($history_mutasi)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted italic">Belum ada aktivitas mutasi barang.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($history_mutasi as $h): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i', strtotime($h['tanggal'])) ?></td>
                                            <td>
                                                <span class="badge border border-secondary text-secondary" style="font-weight: 500;">
                                                    <?= $h['kode_mutasi'] ?>
                                                </span>
                                            </td>
                                            <td><?= $h['nama_produk'] ?></td>
                                            <td><strong><?= $h['jumlah'] ?></strong></td>
                                            <td><i class="fas fa-store"></i> <?= $h['nama_toko_tujuan'] ?></td>
                                            <td><?= $h['admin_pengirim'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk pencarian produk cepat
        $('.select2').select2({
            theme: 'bootstrap4'
        });

        // DataTable untuk tabel riwayat agar bisa di-search/sort
        $('#tabel-mutasi').DataTable({
            "order": [
                [0, "desc"]
            ], // Urutkan dari yang terbaru
            "pageLength": 10
        });
    });
</script>
<?= $this->endSection(); ?>