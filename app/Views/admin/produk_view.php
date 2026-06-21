<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<style>
:root {
    --bg-body: #f8f9fa;
    --card-radius: 16px;
    --primary-green: #10B981;
    --primary-dark: #059669;
}

.wrapper-full {
    width: 100%;
    padding: 16px 24px;
    background-color: var(--bg-body);
    min-height: 90vh;
}

.modern-card {
    border: none;
    border-radius: var(--card-radius);
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    margin-bottom: 24px;
}

.table th {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #6b7280;
    letter-spacing: 0.05em;
    border-bottom: 2px solid #f3f4f6;
}

.table td {
    padding: 16px 12px;
    color: #1f2937;
    font-size: 0.875rem;
    vertical-align: middle;
}

.badge-modern {
    background-color: #ecfdf5;
    color: #059669;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 6px 12px;
    border-radius: 20px;
    border: 1px solid #a7f3d0;
}

/* Konfigurasi Pager / Paginasi Minimalis Menyatu */
.pagination {
    display: inline-flex !important;
    list-style: none !important;
    padding: 0;
    margin: 0;
    border: 1px solid #cbd5e1 !important;
    border-radius: 12px !important;
    overflow: hidden;
    background-color: white;
}

.pagination li {
    margin: 0 !important;
    border-right: 1px solid #cbd5e1 !important;
}

.pagination li:last-child {
    border-right: none !important;
}

.pagination li a,
.pagination li span {
    display: flex !important;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 14px;
    border: none !important;
    border-radius: 0 !important;
    color: #f2f7fc !important;
    text-decoration: none !important;
    font-weight: 500;
    background-color: transparent;
    transition: all 0.2s ease;
}

.pagination li.active span {
    background-color: #eef6ff !important;
    color: white !important;
}

.pagination li a:hover {
    background-color: #f8fafc !important;
}

.input-group:focus-within {
    border-color: var(--primary-green) !important;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
}
</style>

<div class="wrapper-full">

    <?php if (session()->getFlashdata('pesan_sukses')) : ?>
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" id="autoDismissAlert"
        role="alert" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('pesan_sukses'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row align-items-center g-3 mb-4">
        <div class="col-12 col-lg-4">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex justify-content-center align-items-center text-white"
                    style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #10B981, #059669); flex-shrink: 0;">
                    <i class="fas fa-boxes fa-lg"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Manajemen Produk</h4>
                    <p class="text-muted small mb-0">Atur stok, kategori, dan harga menu.</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 col-lg-5">
            <form action="<?= site_url('admin/produk') ?>" method="get">
                <div class="input-group shadow-sm"
                    style="background: white; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                    <span class="input-group-text border-0 bg-white ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" name="keyword" id="searchInput" class="form-control border-0 shadow-none py-2"
                        placeholder="Cari kode atau nama produk..." value="<?= esc(request()->getGet('keyword')) ?>"
                        style="font-size: 0.9rem;">

                    <?php if(request()->getGet('keyword')): ?>
                    <a href="<?= site_url('admin/produk') ?>" class="btn border-0 bg-white text-muted">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-light border-0 bg-white px-3 text-success">Cari</button>
                </div>
            </form>
        </div>

        <!-- <div class="col-12 col-md-4 col-lg-3 text-md-end">
            <button type="button" class="btn text-white shadow-sm px-4 py-2 w-100 w-md-auto" data-bs-toggle="modal"
                data-bs-target="#modalTambahProduk"
                style="background-color: #10B981; border-radius: 10px; font-weight: 600;">
                <i class="fas fa-plus me-2"></i>Tambah Produk
            </button>
        </div> -->
    </div>

    <div class="card modern-card border-0 p-3 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-secondary" style="font-size: 0.85rem;">
                        <th class="ps-3">Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Foto</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($produk)): ?>
                    <?php foreach ($produk as $p): ?>
                    <tr>
                        <td class="ps-3 fw-bold text-secondary" style="font-size: 0.8rem;">
                            #<?= $p['barcode'] ?? '-' ?>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?= esc($p['nama_produk']) ?></div>
                        </td>
                        <td>
                            <span class="badge-modern"><?= esc($p['kategori'] ?? 'Umum') ?></span>
                        </td>
                        <td>
                            <span class="badge-modern"><?= esc($p['jenis_stok'] ?? '-') ?></span>
                        </td>
                        <td class="text-secondary small">
                            Rp <?= number_format($p['harga_beli'] ?? 0, 0, ',', '.') ?>
                        </td>
                        <td class="fw-bold text-success">
                            Rp <?= number_format($p['harga_jual'] ?? 0, 0, ',', '.') ?>
                        </td>
                        <td>
                            <?php 
                                $stok = $p['stok'] ?? 0;
                                $badge_color = ($stok <= 10) ? 'bg-danger' : (($stok <= 30) ? 'bg-warning text-dark' : 'bg-success text-white');
                                ?>
                            <span class="badge <?= $badge_color ?> px-2 py-1 shadow-sm"
                                style="border-radius: 6px; font-size: 0.75rem;">
                                <?= $stok ?> Pcs
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($p['img'])): ?>
                            <img src="<?= base_url('uploads/produk/' . $p['img']) ?>" class="shadow-sm border"
                                style="width: 42px; height: 42px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                            <div class="d-flex justify-content-center align-items-center bg-light text-muted border"
                                style="width: 42px; height: 42px; border-radius: 8px;">
                                <i class="fas fa-image" style="font-size: 0.8rem;"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-light text-warning" data-bs-toggle="modal"
                                    data-bs-target="#modalEditProduk<?= $p['produk_id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" data-bs-toggle="modal"
                                    data-bs-target="#modalHapusProduk<?= $p['produk_id'] ?>">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="py-4">
                                <i class="fas fa-search fa-3x mb-3 text-light"></i>
                                <p class="text-muted">Tidak ada produk yang ditemukan.</p>
                                <a href="<?= site_url('admin/produk') ?>" class="btn btn-sm btn-success px-4">Tampilkan
                                    Semua</a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pager)) : ?>
        <div class="d-flex justify-content-between align-items-center mt-4 px-2">
            <div class="small text-muted">
                Menampilkan data produk
            </div>
            <div>
                <?php
            // Pastikan mengarah ke route yang benar
            $pager->setPath('admin/produk'); 
            
            // Gunakan 'default' karena itu nama kunci di Config/Pager.php Anda
            echo $pager->links('default', 'default'); 
        ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="modalTambahProduk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 p-4"
                style="background: linear-gradient(135deg, #10B981, #059669); color: #fff;">
                <h5 class="modal-title fw-bold">Tambah Produk Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/simpan_produk') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                            style="font-size: 0.725rem;">Barcode</label>
                        <input type="text" class="form-control border-0 bg-white py-3 px-3 shadow-none" name="barcode"
                            required placeholder="Contoh: BR-001" style="border-radius: 12px; font-size: 0.875rem;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                            style="font-size: 0.725rem;">Nama Produk</label>
                        <input type="text" class="form-control border-0 bg-white py-3 px-3 shadow-none"
                            name="nama_produk" required placeholder="Masukkan nama produk"
                            style="border-radius: 12px; font-size: 0.875rem;">
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                                style="font-size: 0.725rem;">Harga Beli (Rp)</label>
                            <input type="number" class="form-control border-0 bg-white py-3 px-3 shadow-none"
                                name="harga_beli" required placeholder="10000"
                                style="border-radius: 12px; font-size: 0.875rem;">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                                style="font-size: 0.725rem;">Harga Jual (Rp)</label>
                            <input type="number" class="form-control border-0 bg-white py-3 px-3 shadow-none"
                                name="harga_jual" required placeholder="15000"
                                style="border-radius: 12px; font-size: 0.875rem;">
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                            style="font-size: 0.725rem;">Stok</label>
                        <input type="number" class="form-control border-0 bg-white py-3 px-3 shadow-none" name="stok"
                            required placeholder="Contoh: 50" style="border-radius: 12px; font-size: 0.875rem;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                            style="font-size: 0.725rem;">Kategori</label>
                        <select class="form-select border-0 bg-white py-3 px-3 shadow-none" name="kategori" required
                            style="border-radius: 12px; font-size: 0.875rem;">
                            <option value="" selected disabled>Pilih Kategori...</option>
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Minuman">Coffee Based</option>
                            <option value="Minuman">Non Coffee</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Snack">Signature</option>
                            <option value="Promo">Promo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                            style="font-size: 0.725rem;">Jenis Stok</label>
                        <select class="form-select border-0 bg-white py-3 px-3 shadow-none" name="jenis_stok" required
                            style="border-radius: 12px; font-size: 0.875rem;">
                            <option value="" selected disabled>Pilih Jenis Stok...</option>
                            <option value="Kering">Kering</option>
                            <option value="Basah">Basah</option>
                            <option value="Bahan">Bahan</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                            style="font-size: 0.725rem;">Foto Produk</label>
                        <input type="file" class="form-control border-0 bg-white py-3 px-3 shadow-none" name="img"
                            accept="image/*" style="border-radius: 12px; font-size: 0.875rem;">
                        <small class="text-muted d-block mt-1" style="font-size: 0.725rem;">Format yang diizinkan: JPG,
                            JPEG, PNG (Maks. 2MB).</small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn px-4 py-2 text-white shadow-sm"
                        style="background-color: #10B981; border-radius: 10px;">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($produk)): ?>
<?php foreach ($produk as $p): ?>
<div class="modal fade" id="modalEditProduk<?= $p['produk_id'] ?? '' ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 p-4"
                style="background: linear-gradient(135deg, #3B82F6, #1D4ED8); color: #fff;">
                <h5 class="modal-title fw-bold">Edit Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/update_produk/' . ($p['produk_id'] ?? '')) ?>" method="POST"
                enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body p-4" style="background-color: #f8fafc;">

                    <div class="mb-4 text-center">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary d-block text-start"
                            style="font-size: 0.725rem;">Foto Produk</label>
                        <div class="mt-2 position-relative d-inline-block">
                            <?php 
                                $foto = !empty($p['img']) ? base_url('uploads/produk/' . $p['img']) : base_url('assets/img/no-image.png');
                            ?>
                            <img id="previewFoto<?= $p['produk_id'] ?>" src="<?= $foto ?>"
                                class="img-thumbnail shadow-sm"
                                style="width: 120px; height: 120px; object-fit: cover; border-radius: 15px;">

                            <div class="mt-2">
                                <input type="file" class="form-control form-control-sm border-0 shadow-none" name="img"
                                    accept="image/*" onchange="previewImage(this, 'previewFoto<?= $p['produk_id'] ?>')"
                                    style="border-radius: 8px; font-size: 0.75rem; background: #fff;">
                                <small class="text-muted" style="font-size: 0.7rem;">*Kosongkan jika tidak ingin
                                    mengubah foto</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                            style="font-size: 0.725rem;">Kode Produk</label>
                        <input type="text" class="form-control border-0 bg-white py-3 px-3 shadow-none" name="barcode"
                            value="<?= $p['barcode'] ?? '' ?>" required placeholder="Contoh: BR-001"
                            style="border-radius: 12px; font-size: 0.875rem;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                            style="font-size: 0.725rem;">Nama Produk</label>
                        <input type="text" class="form-control border-0 bg-white py-3 px-3 shadow-none"
                            name="nama_produk" value="<?= $p['nama_produk'] ?? '' ?>" required
                            placeholder="Masukkan nama produk" style="border-radius: 12px; font-size: 0.875rem;">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                                style="font-size: 0.725rem;">Harga Beli (Rp)</label>
                            <input type="number" class="form-control border-0 bg-white py-3 px-3 shadow-none"
                                name="harga_beli" value="<?= $p['harga_beli'] ?? '' ?>" required placeholder="10000"
                                style="border-radius: 12px; font-size: 0.875rem;" readonly>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                                style="font-size: 0.725rem;">Harga Jual (Rp)</label>
                            <input type="number" class="form-control border-0 bg-white py-3 px-3 shadow-none"
                                name="harga_jual" value="<?= $p['harga_jual'] ?? '' ?>" required placeholder="15000"
                                style="border-radius: 12px; font-size: 0.875rem;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                            style="font-size: 0.725rem;">Stok</label>
                        <input type="number" class="form-control border-0 bg-white py-3 px-3 shadow-none" name="stok"
                            value="<?= $p['stok'] ?? '' ?>" required placeholder="Contoh: 50"
                            style="border-radius: 12px; font-size: 0.875rem;" readonly>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                                style="font-size: 0.725rem;">Kategori</label>
                            <select class="form-select border-0 bg-white py-3 px-3 shadow-none" name="kategori" required
                                style="border-radius: 12px; font-size: 0.875rem;">
                                <option value="" disabled>Pilih Kategori...</option>
                                <option value="Makanan" <?= ($p['kategori'] ?? '') === 'Makanan' ? 'selected' : '' ?>>
                                    Makanan</option>
                                <option value="Minuman" <?= ($p['kategori'] ?? '') === 'Minuman' ? 'selected' : '' ?>>
                                    Minuman</option>
                                <option value="Snack" <?= ($p['kategori'] ?? '') === 'Snack' ? 'selected' : '' ?>>Snack
                                </option>
                                <option value="Promo" <?= ($p['kategori'] ?? '') === 'Promo' ? 'selected' : '' ?>>Promo
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-xs fw-bold text-uppercase text-secondary"
                                style="font-size: 0.725rem;">Jenis Stok</label>
                            <select class="form-select border-0 bg-white py-3 px-3 shadow-none" name="jenis_stok"
                                required style="border-radius: 12px; font-size: 0.875rem;">
                                <option value="" disabled>Pilih Jenis...</option>
                                <option value="Kering" <?= ($p['jenis_stok'] ?? '') === 'Kering' ? 'selected' : '' ?>>
                                    Kering</option>
                                <option value="Basah" <?= ($p['jenis_stok'] ?? '') === 'Basah' ? 'selected' : '' ?>>
                                    Basah</option>
                                <option value="Bahan" <?= ($p['jenis_stok'] ?? '') === 'Bahan' ? 'selected' : '' ?>>
                                    Bahan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn px-4 py-2 text-white shadow-sm"
                        style="background-color: #3B82F6; border-radius: 10px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php endforeach; ?>
<?php endif; ?>

<div class="modal fade" id="modalSukses" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-body text-center p-5">
                <div class="d-inline-flex justify-content-center align-items-center mb-4"
                    style="width: 80px; height: 80px; border-radius: 50%; background: #E6F4EA; color: #10B981;">
                    <i class="fas fa-check-circle fa-3x"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Berhasil Disimpan!</h4>
                <p class="text-muted small px-2 mb-4">Data produk telah berhasil diubah/disimpan di dalam database.
                </p>
                <button type="button" class="btn btn-success w-100 py-2 text-white fw-bold shadow-sm"
                    data-bs-dismiss="modal" style="background: #10B981; border-radius: 12px; border: none;">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($produk)): ?>
<?php foreach ($produk as $p): ?>
<div class="modal fade" id="modalSuksesHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 350px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body text-center p-4">
                <div class="d-inline-flex justify-content-center align-items-center mb-3"
                    style="width: 60px; height: 60px; border-radius: 50%; background: #D1FAE5; color: #059669;">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Berhasil Dihapus!</h5>
                <p class="text-muted small mb-3">
                    Data produk telah berhasil dihapus dari dalam database.
                </p>
                <button type="button" class="btn btn-success w-100 py-2 text-white fw-bold shadow-sm"
                    data-bs-dismiss="modal" style="background: #059669; border-radius: 10px; border: none;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapusProduk<?= $p['produk_id'] ?? '' ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-body text-center p-5">
                <div class="d-inline-flex justify-content-center align-items-center mb-4"
                    style="width: 80px; height: 80px; border-radius: 50%; background: #FEE2E2; color: #DC2626;">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Hapus Produk?</h4>
                <p class="text-muted small px-2 mb-4">
                    Apakah Anda yakin ingin menghapus produk <strong><?= $p['nama_produk'] ?></strong>? Data yang
                    dihapus tidak dapat dikembalikan.
                </p>
                <div class="d-flex flex-column gap-2">
                    <a href="<?= site_url('admin/hapus_produk/' . ($p['produk_id'] ?? '')) ?>"
                        class="btn btn-danger w-100 py-2 text-white fw-bold shadow-sm"
                        style="background: #DC2626; border-radius: 12px; border: none;">
                        Ya, Hapus Data
                    </a>
                    <button type="button" class="btn btn-light w-100 py-2 text-secondary fw-bold"
                        data-bs-dismiss="modal"
                        style="border-radius: 12px; border: 1px solid #E5E7EB; background: #F9FAFB;">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // --- Logika Flashdata / Modal (Sudah Ada) ---
    <?php if (session()->getFlashdata('pesan_sukses')): ?>
    var pesan = "<?php echo (string) session()->getFlashdata('pesan_sukses'); ?>";
    if (pesan.toLowerCase().indexOf('hapus') !== -1) {
        var myModal = new bootstrap.Modal(document.getElementById('modalSuksesHapus'));
        myModal.show();
    } else {
        var myModal = new bootstrap.Modal(document.getElementById('modalSukses'));
        myModal.show();
    }
    <?php endif; ?>

    // --- Logika Auto Dismiss Alert (Sudah Ada) ---
    setTimeout(function() {
        var alertNode = document.getElementById('autoDismissAlert');
        if (alertNode) {
            var alert = new bootstrap.Alert(alertNode);
            alert.close();
        }
    }, 3000);

    // --- LOGIKA BARU: Pencarian Otomatis (Debounce Search) ---
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let timeout = null;

        searchInput.addEventListener('keyup', function() {
            clearTimeout(timeout);

            // Tunggu 500ms setelah berhenti mengetik baru reload
            timeout = setTimeout(function() {
                const keyword = searchInput.value;
                const url = "<?= site_url('admin/produk') ?>";

                // Redirect ke URL dengan parameter keyword
                window.location.href = url + '?keyword=' + encodeURIComponent(keyword);
            }, 200);
        });

        // Menangani Auto-Focus agar kursor tidak hilang setelah reload
        if (searchInput.value !== "") {
            searchInput.focus();
            // Trik agar kursor berada di akhir karakter teks
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }
    }
});
</script>

<?= $this->endSection() ?>