<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white py-3" style="border-radius: 15px 15px 0 0;">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="fas fa-store me-2"></i> Pengaturan Identitas Toko
                    </h5>
                </div>
                <div class="card-body p-4">

                    <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success border-0 shadow-sm mb-4">
                        <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/pengaturan/update') ?>" method="POST"
                        enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <input type="hidden" name="logo_lama" value="<?= $setting['logo'] ?? '' ?>">
                        <input type="hidden" name="qris_lama"
                            value="<?= $setting['foto_qris'] ?? $setting['qris'] ?? '' ?>">
                        <input type="hidden" name="rekening_lama" value="<?= $setting['rekening'] ?? '' ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted mb-1">NAMA TOKO</label>
                                <input type="text" name="nama_toko" class="form-control"
                                    value="<?= $setting['nama_toko'] ?? '' ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted mb-1">SLOGAN / TAGLINE</label>
                                <input type="text" name="slogan" class="form-control"
                                    value="<?= $setting['slogan'] ?? '' ?>"
                                    placeholder="Misal: Kopi Nikmat Harga Sahabat">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted mb-1">NO. TELEPON / WHATSAPP</label>
                                <input type="text" name="no_telp" class="form-control"
                                    value="<?= $setting['no_telp'] ?? '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-muted mb-1">EMAIL TOKO</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= $setting['email'] ?? '' ?>">
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold text-muted mb-1">ALAMAT LENGKAP</label>
                                <textarea name="alamat" class="form-control"
                                    rows="2"><?= $setting['alamat'] ?? '' ?></textarea>
                            </div>

                            <div class="col-12 mt-4 mb-2">
                                <h6 class="fw-bold text-primary border-bottom pb-2">Konfigurasi Transaksi & Cetak</h6>
                            </div>

                            <div class="col-md-4">
                                <label class="small fw-bold text-muted mb-1">PAJAK (PPN %)</label>
                                <div class="input-group">
                                    <input type="number" name="ppn" class="form-control"
                                        value="<?= $setting['ppn'] ?? 0 ?>">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="small fw-bold text-muted mb-1">IZINKAN STOK MINUS?</label>
                                <select name="stok_minus" class="form-select">
                                    <option value="Y"
                                        <?= (isset($setting['stok_minus']) && $setting['stok_minus'] == 'Y') ? 'selected' : '' ?>>
                                        Ya (Boleh Jual)</option>
                                    <option value="N"
                                        <?= (isset($setting['stok_minus']) && $setting['stok_minus'] == 'N') ? 'selected' : '' ?>>
                                        Tidak (Blokir)</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="small fw-bold text-muted mb-1">LOGO BARU (OPSIONAL)</label>
                                <?php if (!empty($setting['logo'])) : ?>
                                <div class="mb-1">
                                    <small class="text-primary"><i class="fas fa-image"></i> Logo Aktif</small>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                            </div>

                            <div class="col-md-4">
                                <label class="small fw-bold text-muted mb-1">FOTO QRIS (OPSIONAL)</label>
                                <?php if (!empty($setting['foto_qris']) || !empty($setting['qris'])) : ?>
                                <div class="mb-1">
                                    <small class="text-success"><i class="fas fa-qrcode"></i> QRIS Aktif</small>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="qris" class="form-control" accept="image/*">
                            </div>

                            <div class="col-md-4">
                                <label class="small fw-bold text-muted mb-1">FOTO REKENING (OPSIONAL)</label>
                                <?php if (!empty($setting['rekening'])) : ?>
                                <div class="mb-1">
                                    <small class="text-info"><i class="fas fa-wallet"></i> Rekening Aktif</small>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="rekening" class="form-control" accept="image/*">
                            </div>

                            <div class="col-md-8">
                                <label class="small fw-bold text-muted mb-1">FOOTER STRUK (PESAN BAWAH)</label>
                                <textarea name="footer_struk" class="form-control" rows="3"
                                    placeholder="Contoh: Terima Kasih&#10;Silahkan Datang Kembali&#10;IG: @tokokopi_bos"><?= esc($setting['footer_struk'] ?? '') ?></textarea>
                                <small class="text-muted small">Gunakan tombol Enter untuk menambah baris baru pada
                                    struk.</small>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm fw-bold"
                                    style="border-radius: 10px;">
                                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-center mb-3" style="border-radius: 15px;">
                <label class="small fw-bold text-muted mb-3 text-uppercase">Logo Saat Ini</label>
                <div class="d-flex align-items-center justify-content-center border rounded-3 bg-light"
                    style="height: 150px; overflow: hidden;">
                    <?php if (!empty($setting['logo'])): ?>
                    <img src="<?= base_url('uploads/img/' . $setting['logo']) ?>" class="img-fluid p-2"
                        style="max-height: 100%;">
                    <?php else: ?>
                    <div class="text-muted"><i class="fas fa-image fa-3x mb-2 opacity-25"></i><br><span
                            class="small">Belum ada</span></div>
                    <?php endif; ?>
                </div>
                <div class="mt-3 alert alert-info border-0 small py-2" style="border-radius: 10px;">
                    <i class="fas fa-info-circle me-1"></i> Logo Untuk Struk
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 text-center mb-3" style="border-radius: 15px;">
                <label class="small fw-bold text-muted mb-3 text-uppercase">QRIS Saat Ini</label>
                <div class="d-flex align-items-center justify-content-center border rounded-3 bg-light"
                    style="height: 150px; overflow: hidden;">
                    <?php if (!empty($setting['foto_qris']) || !empty($setting['qris'])): ?>
                    <img src="<?= base_url('uploads/img/' . ($setting['foto_qris'] ?? $setting['qris'])) ?>"
                        class="img-fluid p-2" style="max-height: 100%;">
                    <?php else: ?>
                    <div class="text-muted"><i class="fas fa-qrcode fa-3x mb-2 opacity-25"></i><br><span
                            class="small">Belum ada</span></div>
                    <?php endif; ?>
                </div>
                <div class="mt-3 alert alert-success border-0 small py-2" style="border-radius: 10px;">
                    <i class="fas fa-qrcode me-1"></i> Tampilan Pop-up QRIS
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 text-center mb-3" style="border-radius: 15px;">
                <label class="small fw-bold text-muted mb-3 text-uppercase">Rekening Bank Saat Ini</label>
                <div class="d-flex align-items-center justify-content-center border rounded-3 bg-light"
                    style="height: 150px; overflow: hidden;">
                    <?php if (!empty($setting['rekening'])): ?>
                    <img src="<?= base_url('uploads/img/' . $setting['rekening']) ?>" class="img-fluid p-2"
                        style="max-height: 100%;">
                    <?php else: ?>
                    <div class="text-muted"><i class="fas fa-university fa-3x mb-2 opacity-25"></i><br><span
                            class="small">Belum ada</span></div>
                    <?php endif; ?>
                </div>
                <div class="mt-3 alert alert-secondary border-0 small py-2" style="border-radius: 10px;">
                    <i class="fas fa-money-check-alt me-1"></i> Tampilan Pop-up Kasir
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>