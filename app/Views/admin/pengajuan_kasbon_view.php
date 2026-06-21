<?php
/**
 * @var array $riwayat
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-3">
                            <i class="fas fa-hand-holding-usd fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Ajukan Kasbon</h5>
                            <small class="text-muted">Senja Coffee & Eatery</small>
                        </div>
                    </div>

                    <form action="<?= base_url('admin/simpan_pengajuan') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Nominal Pinjaman</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light fw-bold text-dark"
                                    style="border-radius: 12px 0 0 12px;">Rp</span>
                                <input type="text" id="nominal_display" class="form-control border-0 bg-light p-3"
                                    placeholder="0" style="border-radius: 0 12px 12px 0;" required>

                                <input type="hidden" name="nominal" id="nominal_asli">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary">Alasan Kasbon</label>
                            <textarea name="keterangan" class="form-control border-0 bg-light p-3" rows="3"
                                placeholder="Contoh: Untuk keperluan mendesak..." style="border-radius: 12px;"
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm"
                            style="border-radius: 15px;">
                            <i class="fas fa-paper-plane me-2"></i> Kirim Pengajuan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <h5 class="fw-bold mb-0">Riwayat Pengajuan Anda</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0 py-3 text-secondary small fw-bold">TANGGAL</th>
                                    <th class="border-0 py-3 text-secondary small fw-bold">NOMINAL</th>
                                    <th class="border-0 py-3 text-secondary small fw-bold text-center">STATUS</th>
                                    <th class="border-0 py-3 pe-4 text-secondary small fw-bold text-end">INFO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($riwayat)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">Belum ada pengajuan kasbon.</td>
                                </tr>
                                <?php endif; ?>

                                <?php foreach($riwayat as $r): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark">
                                            <?= date('d M Y', strtotime($r['tanggal_pinjam'])) ?></div>
                                        <small class="text-muted"><?= date('H:i', strtotime($r['created_at'])) ?>
                                            WIB</small>
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-bold text-dark">Rp
                                            <?= number_format($r['nominal'], 0, ',', '.') ?></span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <?php if($r['status'] == 'Pending'): ?>
                                        <span
                                            class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                            <i class="fas fa-clock me-1"></i> Menunggu
                                        </span>
                                        <?php elseif($r['status'] == 'Disetujui'): ?>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Disetujui
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                            <i class="fas fa-times-circle me-1"></i> Ditolak
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <span class="text-muted small" title="<?= $r['keterangan'] ?>">
                                            <i class="fas fa-info-circle"></i>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS Tambahan agar lebih "Senja" */
.bg-light {
    background-color: #f8fafc !important;
}

.form-control:focus {
    background-color: #f1f5f9 !important;
    box-shadow: none;
    border-color: #3b82f6;
}

.table thead th {
    letter-spacing: 0.05em;
}

.table tbody tr:last-child td {
    border: 0;
}

.animate__pulse {
    animation-duration: 2s;
}
</style>
<script>
$(document).ready(function() {
    const inputDisplay = $('#nominal_display');
    const inputAsli = $('#nominal_asli');

    inputDisplay.on('input', function(e) {
        // 1. Ambil angka saja dari input
        let value = $(this).val().replace(/[^0-9]/g, '');

        // 2. Simpan angka murni ke input hidden untuk dikirim ke Controller
        inputAsli.val(value);

        // 3. Format angka dengan titik untuk tampilan
        if (value !== "") {
            let formatted = new Intl.NumberFormat('id-ID').format(value);
            $(this).val(formatted);
        } else {
            $(this).val("");
        }
    });
});
</script>

<?= $this->endSection() ?>