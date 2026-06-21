<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>
<?php
/**
 * @var array $semua_kru
 */
?>
<style>
.card-custom {
    border-radius: 15px;
    border: none;
}

.table-custom thead {
    background-color: #198754;
    color: white;
}

.table-custom th {
    padding: 12px 15px;
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
</style>

<div class="container-fluid py-4">
    <!-- Row Grid Utama -->
    <div class="row g-4">

        <!-- ======================================================================== -->
        <!-- COLUMN KIRI: RIWAYAT & CATATAN LEMBUR (COL-MD-9)                         -->
        <!-- ======================================================================== -->
        <div class="col-md-9">
            <div class="card card-custom shadow-sm p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div>
                        <h4 class="fw-bold text-dark mb-1"><i class="fas fa-user-clock text-success me-2"></i>Log Lembur
                            Kru Harian</h4>
                        <p class="text-muted small mb-0">Riwayat lembur operasional outlet Senja Coffee bulanan.</p>
                    </div>
                    <button type="button" class="btn btn-success btn-action text-white px-3" data-bs-toggle="modal"
                        data-bs-target="#modalTambahLembur">
                        <i class="fas fa-plus-circle me-1"></i> Catat Lembur
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="border-top-left-radius: 10px;">Nama Kru</th>
                                <th>Tanggal</th>
                                <th>Durasi Jam</th>
                                <th>Tarif / Jam</th>
                                <th class="text-end">Total Uang</th>
                                <th>Keterangan</th>
                                <th class="text-center" style="border-top-right-radius: 10px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($semua_lembur)) : ?>
                            <?php foreach ($semua_lembur as $l) : ?>
                            <tr>
                                <td class="fw-bold text-dark">
                                    <i class="fas fa-user text-muted me-2"></i><?= esc($l['nama_kru']) ?>
                                </td>
                                <td><span class="badge bg-light text-dark py-2 px-3 fw-bold"><i
                                            class="far fa-calendar-alt text-muted me-2"></i><?= date('d-m-Y', strtotime($l['tanggal_lembur'])) ?></span>
                                </td>
                                <td><span
                                        class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2"><?= $l['jumlah_jam'] ?>
                                        Jam</span></td>
                                <td class="text-secondary">Rp <?= number_format($l['tarif_per_jam'], 0, ',', '.') ?>
                                </td>
                                <td class="text-end fw-bold text-success">Rp
                                    <?= number_format($l['total_uang_lembur'], 0, ',', '.') ?></td>
                                <td class="text-muted italic small"><?= esc($l['keterangan'] ?: '-') ?></td>
                                <td class="text-center">
                                    <a href="<?= site_url('admin/hapus_lembur/' . $l['id_lembur']) ?>"
                                        class="btn btn-sm btn-outline-danger btn-action py-1 px-2"
                                        onclick="return confirm('Hapus riwayat lembur ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else : ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-business-time fa-3x mb-3 text-light"></i><br>Belum ada riwayat
                                    lembur.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ======================================================================== -->
        <!-- COLUMN KANAN: PENGATURAN / MASTER TARIF JABATAN (COL-MD-3)               -->
        <!-- ======================================================================== -->
        <div class="col-md-3">
            <div class="card card-custom shadow-sm p-3 h-100 bg-light">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-cogs text-primary me-2"></i>Master Tarif</h6>
                    <button type="button" class="btn btn-sm btn-primary p-1 px-2 fw-bold shadow-sm"
                        style="font-size: 11px; border-radius: 6px;" data-bs-toggle="modal"
                        data-bs-target="#modalTambahJabatan">
                        <i class="fas fa-plus me-1"></i>Jabatan
                    </button>
                </div>

                <p class="text-muted" style="font-size: 11px; margin-top: -5px;">Klik tombol edit biru atau hapus merah
                    untuk kelola master tarif jabatan.</p>

                <div class="list-group shadow-sm">
                    <?php if (!empty($tarif_lembur)): ?>
                    <?php foreach ($tarif_lembur as $tl): ?>
                    <div class="list-group-item bg-white p-3 border-0 mb-2" style="border-radius: 10px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block text-uppercase fw-bold"
                                    style="font-size: 10px;"><?= esc($tl['jabatan']) ?></small>
                                <span class="fw-bold text-success" style="font-size: 1.1rem;">Rp
                                    <?= number_format($tl['tarif_per_jam'], 0, ',', '.') ?></span>
                            </div>
                            <!-- Kluster Tombol Kendali Master -->
                            <div class="d-flex gap-1">
                                <!-- Tombol Edit -->
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary rounded-circle p-2 d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;" data-bs-toggle="modal"
                                    data-bs-target="#modalEditTarif" data-id="<?= $tl['id_tarif'] ?>"
                                    data-jabatan="<?= $tl['jabatan'] ?>" data-tarif="<?= $tl['tarif_per_jam'] ?>">
                                    <i class="fas fa-pencil-alt" style="font-size: 11px;"></i>
                                </button>
                                <!-- 🎯 TOMBOL BARU: Hapus Jabatan via SweetAlert Target Gate -->
                                <button type="button"
                                    class="btn btn-sm btn-outline-danger rounded-circle p-2 d-flex align-items-center justify-content-center btn-hapus-master-jabatan"
                                    style="width: 32px; height: 32px;"
                                    data-url="<?= site_url('admin/hapus_jabatan/' . $tl['id_tarif']) ?>"
                                    data-jabatan="<?= esc($tl['jabatan']) ?>">
                                    <i class="fas fa-trash-alt" style="font-size: 11px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ======================================================================== -->
<!-- MODAL 1: CATAT LEMBUR KRU                                                -->
<!-- ======================================================================== -->
<div class="modal fade" id="modalTambahLembur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0 bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-clock text-success me-2"></i>Form Input
                    Lembur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/simpan_lembur_aksi') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Pilih Kru / Petugas</label>
                        <select name="id_user" class="form-select" style="border-radius: 8px;" required>
                            <option value="">-- Pilih Anggota Kru --</option>
                            <?php foreach ($semua_kru as $kru): ?>
                            <option value="<?= $kru['id_user'] ?>"><?= esc($kru['nama_user']) ?>
                                (<?= esc($kru['role']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Tanggal Lembur</label>
                        <input type="date" name="tanggal_lembur" class="form-control" style="border-radius: 8px;"
                            value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Jumlah Durasi (Jam)</label>
                        <input type="number" step="0.5" min="0.5" name="jumlah_jam" class="form-control"
                            placeholder="Contoh: 2.5 atau 3" style="border-radius: 8px;" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bold text-secondary">Keterangan / Keperluan</label>
                        <textarea name="keterangan" class="form-control" rows="3"
                            placeholder="Contoh: Lembur Event Ramadhan / Ramai Overload"
                            style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-secondary btn-action px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-action px-4"><i class="fas fa-save me-1"></i>
                        Simpan Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- MODAL 2: EDIT TARIF POSISI JABATAN                                       -->
<!-- ======================================================================== -->
<div class="modal fade" id="modalEditTarif" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0 bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>Ubah Tarif Lembur
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/update_tarif_aksi') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id_tarif" id="edit-id-tarif">
                <div class="modal-body p-3">
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Jabatan Posisi</label>
                        <input type="text" id="edit-jabatan" class="form-control border-0 bg-light fw-bold" readonly>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold text-muted">Tarif Per Jam (Rp)</label>
                        <!-- 🎯 FIXED TOTAL: name="text" sudah dibuang total, anti Rp 0 -->
                        <input type="number" id="edit-tarif" name="tarif_per_jam"
                            class="form-control fw-bold text-success" style="border-radius: 8px;" required>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-2">
                    <button type="submit" class="btn btn-primary btn-action w-100"><i
                            class="fas fa-check-circle me-1"></i> Update Tarif</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================================== -->
<!-- MODAL 3: TAMBAH MASTER JABATAN BARU                                      -->
<!-- ======================================================================== -->
<div class="modal fade" id="modalTambahJabatan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0 bg-light">
                <h6 class="modal-title fw-bold text-dark"><i class="fas fa-plus-circle text-primary me-2"></i>Tambah
                    Jabatan Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/tambah_jabatan_aksi') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Jabatan Baru</label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Supervisor"
                            style="border-radius: 8px;" required>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold text-secondary">Tarif Lembur Per Jam (Rp)</label>
                        <input type="number" name="tarif_per_jam" class="form-control text-success fw-bold"
                            placeholder="Contoh: 20000" style="border-radius: 8px;" value="15000" required>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-2">
                    <button type="submit" class="btn btn-primary btn-action w-100"><i class="fas fa-save me-1"></i>
                        Daftarkan Jabatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.addEventListener('load', function() {
    // 🟢 Feedback Alert Sukses
    let pesanSukses = "<?= session()->getFlashdata('success') ?>";
    if (pesanSukses !== "") {
        Swal.fire({
            title: 'Berhasil!',
            text: pesanSukses,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // 🔴 Feedback Alert Gagal / Validasi Kembar
    let pesanError = "<?= session()->getFlashdata('error') ?>";
    if (pesanError !== "") {
        Swal.fire({
            title: 'Gagal!',
            text: pesanError,
            icon: 'error',
            confirmButtonText: 'Oke, Mengerti'
        });
    }

    // Binder Data Modal Edit Tarif
    const modalEditTarif = document.getElementById('modalEditTarif');
    if (modalEditTarif) {
        modalEditTarif.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const jabatan = button.getAttribute('data-jabatan');
            const tarif = button.getAttribute('data-tarif');

            document.getElementById('edit-id-tarif').value = id;
            document.getElementById('edit-jabatan').value = jabatan;
            document.getElementById('edit-tarif').value = tarif;
        });
    }

    // ========================================================================
    // 🎯 MODAL ALERT KONFIRMASI HAPUS MASTER JABATAN (SWEETALERT2 INTERAKTIF)
    // ========================================================================
    const tombolHapusMaster = document.querySelectorAll('.btn-hapus-master-jabatan');
    tombolHapusMaster.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetUrl = this.getAttribute('data-url');
            const namaPosisi = this.getAttribute('data-jabatan');

            Swal.fire({
                title: 'Yakin dihapus, Bos?',
                text: "Jabatan [" + namaPosisi +
                    "] akan didelete permanen dari sistem master tarif!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Gaskeun lempar kemudi ke URL penghancuran data di controller
                    window.location.href = targetUrl;
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>