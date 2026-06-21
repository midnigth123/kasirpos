<?= $this->extend('layout/kasir_layout') ?>
<?= $this->section('content') ?>

<div class="container d-flex justify-content-center align-items-center"
    style="min-height: 80vh; margin-top: 10px; margin-bottom: 30px;">
    <div class="card border-0 shadow-lg col-md-5 p-0 overflow-hidden" style="border-radius: 24px; background: #ffffff;">

        <div class="p-4 text-center text-white position-relative"
            style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
            <div class="bg-white text-danger rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3"
                style="width: 70px; height: 70px;">
                <i class="fas fa-sign-out-alt fa-2x animate__animated animate__pulse animate__infinite"></i>
            </div>
            <h4 class="fw-bold mb-1">Tutup Shift Kasir</h4>
            <p class="mb-0 small text-white-50">Pastikan data uang fisik di laci sesuai sebelum serah terima</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
        <input type="hidden" id="autoShowModal" value="1">
        <?php endif; ?>

        <div class="p-4 pt-5">
            <form action="<?= site_url('kasir/close-kasir/simpan') ?>" method="POST" id="formTutupShift">
                <?= csrf_field() ?>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider mb-2">
                        <i class="fas fa-user-shield me-1 text-muted"></i> Petugas Kasir Logged-In
                    </label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light px-3 text-muted"
                            style="border-radius: 12px 0 0 12px;">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control border-0 bg-light py-2.5 px-3 fw-semibold text-dark"
                            value="<?= session()->get('username') ?>" readonly
                            style="border-radius: 0 12px 12px 0; cursor: not-allowed;">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider mb-2">
                        <i class="fas fa-calculator me-1 text-muted"></i> Total Perhitungan Uang Fisik Akhir
                    </label>
                    <div class="input-group shadow-sm"
                        style="border-radius: 12px; overflow: hidden; border: 2px solid #e2e8f0; transition: all 0.3s;"
                        id="inputUangBox">
                        <span class="input-group-text border-0 bg-white px-3 fw-bold text-danger fs-5">Rp</span>

                        <input type="text"
                            class="form-control border-0 bg-white py-3 px-2 shadow-none fw-bold fs-5 text-dark text-danger"
                            id="uang_tampil" placeholder="0" required
                            onfocus="document.getElementById('inputUangBox').style.borderColor='#ef4444'"
                            onblur="document.getElementById('inputUangBox').style.borderColor='#e2e8f0'">

                        <input type="hidden" name="uang_fisik_akhir" id="uang_asli" value="0">
                    </div>
                    <div class="form-text text-muted small mt-2 px-1">
                        <i class="fas fa-info-circle me-1 text-warning"></i> Masukkan total keseluruhan lembaran & koin
                        fisik tunai di laci saat shift ini berakhir.
                    </div>
                </div>

                <div class="d-grid mt-4 gap-2">
                    <button type="button"
                        class="btn btn-danger py-3 fw-bold shadow d-flex align-items-center justify-content-center gap-2"
                        style="background: linear-gradient(45deg, #ef4444, #dc2626); border:none; border-radius: 14px; font-size: 1.05rem;"
                        data-bs-toggle="modal" data-bs-target="#konfirmasiModal">
                        <i class="fas fa-lock"></i> Selesaikan & Tutup Shift
                    </button>

                    <a href="<?= site_url('kasir/transaksi') ?>"
                        class="btn btn-light py-2.5 fw-semibold text-secondary d-flex align-items-center justify-content-center gap-2 border"
                        style="border-radius: 14px; font-size: 0.95rem; background-color: #f8fafc;">
                        <i class="fas fa-arrow-left"></i> Kembali ke Transaksi
                    </a>
                </div>

                <div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg"
                            style="border-radius: 20px; border: none; overflow: hidden;">

                            <div class="modal-header border-0 bg-danger text-white py-3 px-4">
                                <h5 class="modal-title fw-bold" id="konfirmasiModalLabel">
                                    <i
                                        class="fas fa-exclamation-triangle me-2 text-warning animate__animated animate__flash animate__infinite"></i>
                                    Konfirmasi Final
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body text-center py-4 px-4">
                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 80px; height: 80px;">
                                    <i class="fas fa-user-lock fa-3x"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Yakin Ingin Mengunci Shift?</h5>
                                <p class="text-secondary small mb-0 px-2">Sistem akan otomatis mengunci laci kasir untuk
                                    shift ini dan mengalihkan halaman Anda kembali ke dashboard utama.</p>
                            </div>

                            <div class="modal-footer border-0 pb-4 justify-content-center gap-2">
                                <button type="button" class="btn btn-light px-4 fw-semibold text-secondary border"
                                    data-bs-dismiss="modal" style="border-radius: 12px;">Batalkan</button>
                                <button type="submit" class="btn btn-danger px-4 fw-bold"
                                    style="border-radius: 12px; background-color: #dc2626;">Ya, Tutup Shift</button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header border-0 bg-warning text-dark py-3 px-4">
                <h5 class="modal-title fw-bold" id="errorModalLabel">
                    <i class="fas fa-bell me-2"></i> Peringatan Sistem
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4 px-4">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                    style="width: 80px; height: 80px;">
                    <i class="fas fa-exclamation-circle fa-3x"></i>
                </div>
                <p class="fw-medium text-dark mb-0 px-2"><?= session()->getFlashdata('error') ?></p>
            </div>
            <div class="modal-footer border-0 pb-4 justify-content-center">
                <button type="button" class="btn btn-secondary px-5 fw-bold text-white border-0" data-bs-dismiss="modal"
                    style="border-radius: 12px; background: #64748b;">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Logika pemicu Pop-Up Error Otomatis
    var autoShow = document.getElementById('autoShowModal');
    if (autoShow && autoShow.value === '1') {
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    }

    // 2. Logika Pembuat Format Titik Rupiah Otomatis Pas Diketik Kasir
    const uangTampil = document.getElementById('uang_tampil');
    const uangAsli = document.getElementById('uang_asli');

    uangTampil.addEventListener('input', function(e) {
        let bersih = this.value.replace(/[^0-9]/g, '');
        uangAsli.value = bersih;

        if (bersih) {
            this.value = formatRibuan(bersih);
        } else {
            this.value = '';
        }
    });

    function formatRibuan(angka) {
        let num_str = angka.toString(),
            sisa = num_str.length % 3,
            rupiah = num_str.substr(0, sisa),
            ribuan = num_str.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }
});
</script>

<?= $this->endSection() ?>