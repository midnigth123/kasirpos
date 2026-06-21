<?= $this->extend('layout/kasir_layout') ?>
<?= $this->section('content') ?>



<div class="container d-flex justify-content-center align-items-center"
    style="min-height: 80vh; margin-top: 10px; margin-bottom: 30px;">
    <div class="card border-0 shadow-lg col-md-5 p-0 overflow-hidden" style="border-radius: 24px; background: #ffffff;">

        <div class="p-4 text-center text-white position-relative"
            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="bg-white text-success rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3"
                style="width: 70px; height: 70px;">
                <i class="fas fa-cash-register fa-2x animate__animated animate__bounceIn"></i>
            </div>
            <h4 class="fw-bold mb-1">Mulai Shift Kerja</h4>
            <p class="mb-0 small text-white-50">Lengkapi data laci kasir sebelum melayani pelanggan hari ini</p>
        </div>

        <div class="p-4 pt-5">
            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 border-0 shadow-sm mb-4"
                style="border-radius: 12px; background-color: #fef2f2; color: #dc2626;">
                <i class="fas fa-exclamation-circle"></i>
                <div class="small fw-medium"><?= session()->getFlashdata('error') ?></div>
            </div>
            <?php endif; ?>

            <form action="<?= site_url('kasir/open-kasir/simpan') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-3.5">
                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider mb-2">
                        <i class="fas fa-user-tie me-1 text-muted"></i> Petugas Kasir Aktif
                    </label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light px-3 text-muted"
                            style="border-radius: 12px 0 0 12px;">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control border-0 bg-light py-2.5 px-3 fw-semibold text-dark"
                            value="<?= session()->get('nama_user') ?>" readonly
                            style="border-radius: 0 12px 12px 0; focus: none; cursor: not-allowed;">
                    </div>
                </div>

                <div class="mb-3.5 mt-3">
                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider mb-2">
                        <i class="fas fa-clock me-1 text-muted"></i> Pilih Jadwal Shift
                    </label>
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-light px-3 text-muted"
                            style="border-radius: 12px 0 0 12px;">
                            <i class="fas fa-list-ul"></i>
                        </span>
                        <select class="form-select border-0 bg-light py-2.5 px-3 shadow-none fw-medium text-secondary"
                            name="nama_shift" required
                            style="border-radius: 0 12px 12px 0; cursor: pointer; font-size: 0.95rem;">
                            <option value="" class="text-muted">-- Tentukan Shift Kerja --</option>
                            <?php if (!empty($master_shift)): ?>
                            <?php foreach ($master_shift as $s): ?>
                            <option value="<?= $s['nama_shift'] ?>" class="text-dark">
                                <?= $s['nama_shift'] ?> (<?= date('H:i', strtotime($s['jam_mulai'])) ?> -
                                <?= date('H:i', strtotime($s['jam_selesai'])) ?>)
                            </option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4 mt-3">
                    <label class="form-label fw-bold text-secondary small text-uppercase tracking-wider mb-2">
                        <i class="fas fa-wallet me-1 text-muted"></i> Jumlah Modal Laci Awal
                    </label>
                    <div class="input-group shadow-sm"
                        style="border-radius: 12px; overflow: hidden; border: 2px solid #e2e8f0; transition: all 0.3s;">
                        <span class="input-group-text border-0 bg-white px-3 fw-bold text-success fs-5">Rp</span>

                        <input type="text"
                            class="form-control border-0 bg-white py-3 px-2 shadow-none fw-bold fs-5 text-dark text-success"
                            id="modal_tampil" placeholder="0" required
                            onfocus="this.parentElement.style.borderColor='#10b981'"
                            onblur="this.parentElement.style.borderColor='#e2e8f0'">

                        <input type="hidden" name="modal_awal" id="modal_asli" value="0">
                    </div>
                    <div class="form-text text-muted small mt-1.5 px-1">
                        <i class="fas fa-info-circle me-1 text-info"></i> Hitung fisik uang cash di dalam laci sebelum
                        di-input.
                    </div>
                </div>

                <div class="d-grid mt-4 mb-2">
                    <button type="submit"
                        class="btn btn-success py-3 fw-bold shadow d-flex align-items-center justify-content-center gap-2"
                        style="background: linear-gradient(45deg, #10b981, #059669); border: none; border-radius: 14px; transition: all 0.2s; font-size: 1.05rem;">
                        <i class="fas fa-lock-open"></i>
                        <span>Open Kasir</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputTampil = document.getElementById('modal_tampil');
    const inputAsli = document.getElementById('modal_asli');

    inputTampil.addEventListener('input', function(e) {
        // 1. Ambil hanya karaktek angka bersih
        let angkaBersih = this.value.replace(/[^0-9]/g, '');

        // 2. Set nilai asli tanpa titik ke input hidden untuk dikirim ke DB
        inputAsli.value = angkaBersih;

        // 3. Format inputan tampil dengan separator titik (.)
        if (angkaBersih) {
            this.value = formatRibuan(angkaBersih);
        } else {
            this.value = '';
        }
    });

    // Fungsi pembagi ribuan otomatis
    function formatRibuan(angka) {
        let number_string = angka.toString(),
            sisa = number_string.length % 3,
            rupiah = number_string.substr(0, sisa),
            ribuan = number_string.substr(sisa).match(/\d{3}/g);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }
});
</script>
<?= $this->endSection() ?>