<?php

/**
 * @var array $semua_toko
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<style>
/* ========================================================== */
/* BOM ATOM: PAKSA GAIBKAN SEMUA ALERT BANNER JADUL DARI BUMI */
/* ========================================================== */
.alert,
.alert-success,
.alert-danger,
.alert-dismissible {
    display: none !important;
    opacity: 0 !important;
    visibility: hidden !important;
    height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Style Custom Estetik Milik Bos */
.card-custom {
    border-radius: 15px;
    border: none;
}

.table-custom thead {
    background-color: #4e73df;
    color: white;
}

.table-custom th {
    padding: 15px;
    font-weight: 600;
}

.table-custom td {
    padding: 12px 15px;
    vertical-align: middle;
}

.btn-action {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.modal-custom .modal-content {
    border-radius: 15px;
    border: none;
}

.modal-custom .modal-header {
    background-color: #f8f9fa;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">

            <div class="card card-custom shadow-sm p-4">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div>
                        <h4 class="fw-bold text-dark mb-1"><i
                                class="fas fa-boxes-stacked text-primary me-2"></i>Manajemen Tenant / Outlet</h4>
                        <p class="text-muted small mb-0">Kelola masa aktif, status lisensi, dan monitoring kuncian
                            database client secara real-time.</p>
                        <span class="badge bg-primary px-3 py-2" style="border-radius: 20px;">Total:
                            <?= count($semua_toko) ?> Tenant</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?= site_url('admin/sync_developer_akun') ?>" class="btn btn-primary btn-action px-3"
                            onclick="event.preventDefault(); Swal.fire({
                                title: 'Apakah kamu Yakin?',
                                text: 'Akun login saat ini akan disebarkan ke SELURUH database client!',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#4e73df',
                                cancelButtonColor: '#858796',
                                confirmButtonText: 'Ya, Sinkronkan!',
                                cancelButtonText: 'Batal'
                            }).then((result) => { if (result.isConfirmed) { window.location.href = this.href; } })">
                            <i class="fas fa-sync me-1"></i> Sync Akun Dev
                        </a>

                        <?php $is_maintenance_aktif = file_exists(FCPATH . '../maintenance.flag'); ?>

                        <button type="button"
                            class="btn <?= $is_maintenance_aktif ? 'btn-danger' : 'btn-success' ?> btn-action px-3"
                            data-bs-toggle="modal" data-bs-target="#modalKonfirmasiMaintenance">
                            <i class="fas <?= $is_maintenance_aktif ? 'fa-unlock' : 'fa-lock' ?> me-1"></i>
                            <?= $is_maintenance_aktif ? 'Matikan Maintenance' : 'Aktifkan Maintenance' ?>
                        </button>

                        <button type="button" class="btn btn-success btn-action text-white px-3" data-bs-toggle="modal"
                            data-bs-target="#modalTambahTenant">
                            <i class="fas fa-plus-circle me-1"></i>Tenant Baru
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="border-top-left-radius: 10px;">Nama Outlet</th>
                                <th>Nama Database</th>
                                <th class="text-center">Status Layanan</th>
                                <th>Tanggal Berlangganan</th>
                                <th>Tanggal Jatuh Tempo</th>
                                <th class="text-center" style="border-top-right-radius: 10px; width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($semua_toko)) : ?>
                            <?php foreach ($semua_toko as $toko) : ?>
                            <?php 
                                $hariIni    = date('Y-m-d');
                                $tglDaftar  = $toko['tgl_daftar'];
                                $jatuhTempo = $toko['jatuh_tempo'];
                                $apakahExpired = (!empty($jatuhTempo) && $hariIni > $jatuhTempo);

                                // ==========================================================
                                // PILIHAN A: HITUNG MUNDUR REAL-TIME (Jatuh Tempo - Hari Ini)
                                // ==========================================================
                                $selisihMundur = strtotime($jatuhTempo) - strtotime($hariIni);
                                $sisaHari      = round($selisihMundur / (60 * 60 * 24));

                                // ==========================================================
                                // PILIHAN B: TOTAL DURASI PAKET (Jatuh Tempo - Tanggal Daftar)
                                // ==========================================================
                                $selisihTotal  = strtotime($jatuhTempo) - strtotime($tglDaftar);
                                $totalDurasi   = round($selisihTotal / (60 * 60 * 24));
                            ?>
                            <tr>
                                <td class="fw-bold text-secondary"><?= $toko['nama_toko'] ?></td>
                                <td><code class="px-2 py-1 bg-light text-danger rounded"
                                        style="font-size: 0.9rem;"><?= $toko['nama_database'] ?></code></td>

                                <td class="text-center">
                                    <?php if ($toko['status_aktif'] === 'Y' && !$apakahExpired) : ?>
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-medium"
                                        style="font-size: 0.85rem;">
                                        <i class="fas fa-circle-check me-1"></i> Aktif Beroperasi
                                    </span>
                                    <?php else : ?>
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-medium"
                                        style="font-size: 0.85rem;">
                                        <i class="fas fa-circle-xmark me-1"></i>
                                        <?= $apakahExpired ? 'Masa Aktif Habis' : 'Ditangguhkan' ?>
                                    </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="text-dark fw-bold d-block"><i
                                            class="far fa-calendar-alt text-muted me-2"></i><?= date('d-m-Y', strtotime($tglDaftar)) ?></span>
                                    <small class="text-muted small">Durasi Paket: <strong><?= $totalDurasi ?>
                                            Hari</strong></small>
                                </td>

                                <td>
                                    <span class="text-dark fw-bold d-block mb-1"><i
                                            class="far fa-calendar-alt text-muted me-2"></i><?= date('d-m-Y', strtotime($jatuhTempo)) ?></span>

                                    <?php if (!$apakahExpired) : ?>
                                    <span class="badge bg-primary-subtle text-primary fw-bold"
                                        style="font-size: 0.75rem;">
                                        <i class="fas fa-hourglass-half me-1"></i> Sisa <?= $sisaHari ?> Hari Lagi
                                    </span>
                                    <?php else : ?>
                                    <span class="badge bg-danger-subtle text-danger fw-bold"
                                        style="font-size: 0.75rem;">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Lewat <?= abs($sisaHari) ?>
                                        Hari
                                    </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-action text-dark btn-sm px-3"
                                        data-bs-toggle="modal" data-bs-target="#modalPerpanjang<?= $toko['id_toko'] ?>">
                                        <i class="fas fa-key me-1"></i> Lisensi
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade modal-custom" id="modalPerpanjang<?= $toko['id_toko'] ?>"
                                tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content shadow-lg">
                                        <div class="modal-header border-0 align-items-center">
                                            <h5 class="modal-title fw-bold text-dark"><i
                                                    class="fas fa-sliders text-warning me-2"></i>Konfigurasi Paket
                                                Langganan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="<?= site_url('admin/perpanjang_toko_aksi') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <div class="modal-body p-4">
                                                <div class="bg-light p-3 rounded mb-4">
                                                    <span class="text-muted small d-block">Nama Merchant
                                                        Terpilih:</span>
                                                    <strong class="text-dark fs-5"><?= $toko['nama_toko'] ?></strong>
                                                </div>
                                                <input type="hidden" name="id_toko" value="<?= $toko['id_toko'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold text-secondary">Status Akses
                                                        Aplikasi</label>
                                                    <select name="status_aktif" class="form-select"
                                                        style="border-radius: 8px;">
                                                        <option value="Y"
                                                            <?= $toko['status_aktif'] === 'Y' ? 'selected' : '' ?>>Y -
                                                            Berikan Akses Penuh</option>
                                                        <option value="N"
                                                            <?= $toko['status_aktif'] === 'N' ? 'selected' : '' ?>>N -
                                                            Tangguhkan & Kunci Akun</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label fw-bold text-secondary">Batas Akhir Jatuh
                                                        Tempo</label>
                                                    <input type="date" name="jatuh_tempo" class="form-control"
                                                        style="border-radius: 8px;" value="<?= $toko['jatuh_tempo'] ?>"
                                                        required>
                                                    <div class="form-text mt-2 text-muted"><i
                                                            class="fas fa-info-circle me-1"></i> Setelah melewati
                                                        tanggal ini, sistem otomatis mengunci akses menu operasional
                                                        tenant.</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 bg-light p-3"
                                                style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                                                <button type="button" class="btn btn-secondary btn-action px-4"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success btn-action px-4"><i
                                                        class="fas fa-cloud-arrow-up me-1"></i> Simpan & Sinkronkan
                                                    DB</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-light"></i><br>Belum ada data tenant
                                    yang terdaftar di database master.
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

<div class="modal fade modal-custom" id="modalTambahTenant" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 align-items-center">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-store text-success me-2"></i>Registrasi
                    Tenant Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/tambah_toko_aksi') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Nama Toko / Outlet</label>
                            <input type="text" name="nama_toko" class="form-control" placeholder="Contoh: Kopi Senja 3"
                                style="border-radius: 8px;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Kode Toko (Unique)</label>
                            <input type="text" name="kode_toko" class="form-control" placeholder="Contoh: outlet3"
                                style="border-radius: 8px;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Nama Database Client</label>
                            <input type="text" name="nama_database" class="form-control" placeholder="Contoh: db_kasir3"
                                style="border-radius: 8px;" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Status Awal</label>
                            <select name="status_aktif" class="form-select" style="border-radius: 8px;">
                                <option value="Y">Y - Langsung Aktif</option>
                                <option value="N">N - Non-Aktif (Ditangguhkan)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Tanggal Daftar</label>
                            <input type="date" name="tgl_daftar" class="form-control" style="border-radius: 8px;"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Tanggal Jatuh Tempo Pertama</label>
                            <input type="date" name="jatuh_tempo" class="form-control" style="border-radius: 8px;"
                                value="<?= date('Y-m-d', strtotime('+1 month')) ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3"
                    style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                    <button type="button" class="btn btn-secondary btn-action px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-action px-4"><i class="fas fa-save me-1"></i>
                        Daftarkan Tenant</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade modal-custom" id="modalKonfirmasiMaintenance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header border-0 align-items-center">
                <h5 class="modal-title fw-bold text-dark">
                    <i
                        class="fas fa-triangle-exclamation <?= $is_maintenance_aktif ? 'text-success' : 'text-danger' ?> me-2"></i>
                    Konfirmasi Sistem
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i
                        class="fas <?= $is_maintenance_aktif ? 'fa-dumpster-fire text-success' : 'fa-ban text-danger' ?> fa-4x-stacked fa-3x"></i>
                </div>

                <h5 class="fw-bold text-dark mb-2">
                    Apakah kamu Yakin Ingin <?= $is_maintenance_aktif ? 'Membuka' : 'Mengunci' ?> Aplikasi?
                </h5>

                <p class="text-muted small mb-0">
                    <?= $is_maintenance_aktif 
                        ? 'Tindakan ini akan menghapus gembok sistem. Seluruh tenant/outlet client akan bisa mengakses kembali dashboard operasional secara normal.' 
                        : 'Tindakan ini akan mengunci SELURUH database client secara serentak! Hanya akun Bos yang memegang cookie khusus yang bisa lolos masuk.' 
                    ?>
                </p>
            </div>
            <div class="modal-footer border-0 bg-light p-3"
                style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                <button type="button" class="btn btn-secondary btn-action px-4" data-bs-dismiss="modal">Batal</button>

                <a href="<?= site_url('admin/toggleMaintenance') . ($is_maintenance_aktif ? '' : '?masuk=kasirkita') ?>"
                    class="btn <?= $is_maintenance_aktif ? 'btn-success' : 'btn-danger' ?> btn-action px-4">
                    <i class="fas fa-check-circle me-1"></i> Ya, Eksekusi!
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
window.addEventListener('load', function() {
    // Brutal delete elemen alert bawaan layout jika dia nekat merender ulang
    document.querySelectorAll('.alert, .alert-success, .alert-danger').forEach(function(el) {
        el.remove();
    });

    // 1. Ambil data flashdata secara real-time dari session PHP
    let pesanSukses = "<?= session()->getFlashdata('success') ?>";
    let pesanGagal = "<?= session()->getFlashdata('error') ?>";

    // 2. Eksekusi Jendela Tunggal SweetAlert2 Auto-Close
    if (pesanSukses !== "") {
        Swal.fire({
            title: 'Berhasil, Update!',
            text: pesanSukses,
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            allowOutsideClick: false,
            customClass: {
                popup: 'card-custom'
            }
        });
    } else if (pesanGagal !== "") {
        if (pesanGagal.includes("Sukses") || pesanGagal.includes("BOOM!")) {
            Swal.fire({
                title: 'Berhasil!!',
                text: pesanGagal,
                icon: 'success',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false,
                customClass: {
                    popup: 'card-custom'
                }
            });
        } else {
            Swal.fire({
                title: 'Gagal / Terjadi Kendala!',
                text: pesanGagal,
                icon: 'error',
                confirmButtonText: 'Oke, Saya Cek',
                confirmButtonColor: '#e74a3b',
                customClass: {
                    popup: 'card-custom'
                }
            });
        }
    }
});
</script>

<?= $this->endSection() ?>