<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <div id="broadcast-container" class="mb-3">
        <?php if (!empty($broadcast_pusat)) : ?>
        <div id="box-broadcast-pusat" class="card bg-danger text-white border-0 shadow-sm"
            style="border-radius: 8px; overflow: hidden;">
            <div class="card-body p-0 d-flex align-items-center">
                <div class="bg-dark text-white p-2 px-3 fw-bold text-uppercase d-flex align-items-center gap-2"
                    style="flex-shrink: 0; font-size: 0.85rem;">
                    <i class="fas fa-bullhorn animate__animated animate__flash animate__infinite text-warning"></i>
                    <span>INFO PUSAT:</span>
                </div>
                <div class="flex-grow-1 px-3 fs-6 fw-medium overflow-hidden">
                    <marquee behavior="scroll" direction="left" scrollamount="6">
                        <strong>[<?= esc($broadcast_pusat['judul']) ?>]</strong> &mdash;
                        <?= esc($broadcast_pusat['isi_pesan']) ?>
                    </marquee>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-primary text-white p-4"
                style="border-radius: 15px; background: linear-gradient(45deg, #4e73df, #224abe);">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <div>
                        <h3 class="fw-bold mb-1">Selamat Datang, <?= $nama_user ?? 'Kasir' ?>! 👋</h3>
                        <p class="mb-0 opacity-75">Anda login sebagai <span
                                class="badge bg-light text-primary fw-bold text-uppercase">Kasir</span> di
                            <strong><?= $nama_toko ?? 'Toko Cabang' ?></strong>.
                        </p>
                    </div>
                    <div class="text-end mt-2 mt-md-0">
                        <span class="badge bg-dark bg-opacity-25 px-3 py-2 rounded-pill fs-6">
                            <i class="far fa-calendar-alt me-2"></i><?= date('d M Y') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-rocket text-info me-2"></i>Akses Pintasan Cepat
                    </h5>
                    <p class="text-muted small mb-0">Klik tombol di bawah untuk langsung menuju halaman kerja Anda.</p>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 mb-3 border rounded d-flex align-items-center justify-content-between"
                        style="border-radius: 10px; background-color: #f8f9fc;">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-circle me-3 text-success" style="background-color: #e8f5e9;">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Aplikasi Kasir (POS)</h6>
                                <p class="text-muted small mb-0">Mulai transaksi penjualan baru.</p>
                            </div>
                        </div>
                        <a href="<?= site_url('kasir/transaksi') ?>"
                            class="btn btn-success btn-sm px-3 fw-bold rounded-pill">
                            Buka <i class="fas fa-chevron-right ms-1 text-xs"></i>
                        </a>
                    </div>

                    <div class="p-3 border rounded d-flex align-items-center justify-content-between"
                        style="border-radius: 10px; background-color: #f8f9fc;">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-circle me-3 text-warning" style="background-color: #fffde7;">
                                <i class="fas fa-user-check fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Presensi / Absensi</h6>
                                <p class="text-muted small mb-0">Cek status kehadiran shift kerja.</p>
                            </div>
                        </div>
                        <a href="<?= site_url('kasir/absen') ?>"
                            class="btn btn-warning text-dark btn-sm px-3 fw-bold rounded-pill">
                            Buka <i class="fas fa-chevron-right ms-1 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-clipboard-list text-warning me-2"></i>Panduan
                        Standar Operasional (SOP)</h5>
                    <p class="text-muted small mb-0">Harap diperhatikan sebelum dan saat melayani pelanggan.</p>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 d-flex align-items-start gap-3">
                        <span class="badge bg-light text-dark p-2 rounded-circle border fw-bold">1</span>
                        <p class="text-secondary mb-0 small pt-1">Pastikan nominal uang modal awal di dalam laci laci
                            kasir sudah dihitung dan sesuai sebelum memulai transaksi.</p>
                    </div>
                    <div class="mb-3 d-flex align-items-start gap-3">
                        <span class="badge bg-light text-dark p-2 rounded-circle border fw-bold">2</span>
                        <p class="text-secondary mb-0 small pt-1">Wajib menyapa pelanggan dengan ramah dan selalu
                            tanyakan apakah memiliki *member/aliansi* toko sebelum memproses scan barang.</p>
                    </div>
                    <div class="mb-0 d-flex align-items-start gap-3">
                        <span class="badge bg-light text-dark p-2 rounded-circle border fw-bold">3</span>
                        <p class="text-secondary mb-0 small pt-1">Periksa kembali kesesuaian struk belanja fisik dengan
                            total pembayaran konsumen sebelum mereka meninggalkan area meja kasir.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="suksesTutupShiftModal" tabindex="-1" aria-labelledby="suksesTutupShiftLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg"
            style="border-radius: 24px; border: none; overflow: hidden; background: #ffffff;">

            <div class="p-4 text-center text-white position-relative"
                style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                <button type="button" class="btn-close btn-close-white position-absolute end-0 top-0 m-3"
                    data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="bg-white text-success rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-2"
                    style="width: 75px; height: 75px;">
                    <i class="fas fa-check-circle fa-3x animate__animated animate__bounceIn"></i>
                </div>
                <h4 class="fw-bold mb-0">Laporan Shift Terkunci!</h4>
            </div>

            <div class="modal-body text-center py-4 px-4">
                <h5 class="fw-bold text-dark mb-2">Kerja Bagus Hari Ini!</h5>
                <p class="text-secondary small mb-0 px-2">
                    <?= session()->getFlashdata('pesan') ?? 'Data perhitungan saldo akhir shift Anda telah berhasil disimpan ke database pusat outlet.' ?>
                </p>
                <div
                    class="mt-3 p-2 bg-light rounded-3 d-flex align-items-center justify-content-center gap-2 text-success small fw-semibold">
                    <i class="fas fa-shield-alt"></i> Status Laci: CLOSED & SECURED
                </div>
            </div>

            <div class="modal-footer border-0 pb-4 justify-content-center">
                <button type="button" class="btn btn-success px-5 py-2.5 fw-bold text-white shadow-sm"
                    data-bs-dismiss="modal"
                    style="border-radius: 14px; background: linear-gradient(45deg, #10b981, #059669); border: none; font-size: 0.95rem; min-width: 180px;">
                    <i class="fas fa-thumbs-up me-1"></i> Siap, Terima Kasih!
                </button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if (session()->getFlashdata('pesan')): ?>
    var suksesModal = new bootstrap.Modal(document.getElementById('suksesTutupShiftModal'));
    suksesModal.show();
    setTimeout(function() {
        suksesModal.hide();
    }, 2000);
    <?php endif; ?>
});
document.addEventListener("DOMContentLoaded", function() {

    setInterval(function() {
        fetch("<?= site_url('admin/cek_broadcast_realtime') ?>?_=" + new Date()
                .getTime())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Jalur radar AJAX terganggu.');
                }
                return response.json();
            })
            .then(data => {
                const container = document.getElementById(
                    'broadcast-container');
                if (!container) return;

                let boxBroadcast = document.getElementById(
                    'box-broadcast-pusat');

                // 🟢 KONDISI A: JIKA SIARAN AKTIF DI PUSAT
                if (data.aktif === true) {

                    if (!boxBroadcast) {
                        // 🎯 BERSIH: Di dalam template string ini juga teks tanggalnya sudah dipangkas
                        container.innerHTML = `
                            <div id="box-broadcast-pusat" class="card bg-danger text-white border-0 shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                <div class="card-body p-0 d-flex align-items-center">
                                    <div class="bg-dark text-white p-2 px-3 fw-bold text-uppercase d-flex align-items-center gap-2" style="flex-shrink: 0; font-size: 0.85rem;">
                                        <i class="fas fa-bullhorn animate__animated animate__flash animate__infinite text-warning"></i>
                                        <span>INFO PUSAT:</span>
                                    </div>
                                    <div class="flex-grow-1 px-3 fs-6 fw-medium overflow-hidden">
                                        <marquee behavior="scroll" direction="left" scrollamount="6">
                                            <strong>[${data.judul}]</strong> &mdash; ${data.pesan}
                                        </marquee>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else {
                        let marqueeElement = boxBroadcast.querySelector(
                            'marquee');
                        let kontenTerbaru =
                            `<strong>[${data.judul}]</strong> &mdash; ${data.pesan}`;

                        if (marqueeElement && marqueeElement.innerHTML !==
                            kontenTerbaru) {
                            marqueeElement.innerHTML = kontenTerbaru;
                        }
                    }

                }
                // 🔴 KONDISI B: JIKA SIARAN DIMATIKAN ADMIN
                else {
                    if (boxBroadcast) {
                        boxBroadcast.style.transition = "all 0.4s ease";
                        boxBroadcast.style.opacity = "0";
                        boxBroadcast.style.transform = "translateY(-5px)";

                        setTimeout(() => {
                            boxBroadcast.remove();
                        }, 400);
                    }
                }
            })
            .catch(error => console.log("Radar sync standby..."));
    }, 5000);
});
</script>
<?= $this->endSection() ?>