<?php
/**
 * @var array $riwayat_izin
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fas fa-file-signature text-primary me-2"></i>Pengajuan Izin/Cuti/Sakit</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIzin">
            <i class="fas fa-plus me-1"></i> Ajukan Izin
        </button>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama Pengaju</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayat_izin as $r) : ?>
                    <tr>
                        <td><strong><?= esc(session()->get('nama_user')) ?></strong></td>
                        <td><span class="badge bg-secondary"><?= $r['jenis_izin'] ?></span></td>
                        <td><?= date('d M Y', strtotime($r['tgl_mulai'])) ?> s/d
                            <?= date('d M Y', strtotime($r['tgl_selesai'])) ?></td>
                        <td><?= esc($r['alasan']) ?></td>
                        <td>
                            <?php 
                                $statusClass = ($r['status'] == 'Disetujui') ? 'bg-success' : (($r['status'] == 'Ditolak') ? 'bg-danger' : 'bg-warning');
                            ?>
                            <span class="badge <?= $statusClass ?> rounded-pill"><?= $r['status'] ?></span>
                            <?php if ($r['status'] == 'Ditolak' && !empty($r['alasan_tolak'])): ?>
                            <br><small class="text-danger">Ket: <?= esc($r['alasan_tolak']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($r['status'] == 'Pending') : ?>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-primary" title="Edit"
                                    onclick="editIzin(<?= htmlspecialchars(json_encode($r)) ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="konfirmasiHapus('<?= site_url('admin/hapus_izin/' . $r['id_izin']) ?>')"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <?php else : ?>
                            <span class="text-muted small">Sudah di Proses</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalIzin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="<?= site_url('admin/simpan_izin') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Ajukan Izin Baru</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Izin</label>
                        <select name="jenis_izin" class="form-select" required>
                            <option value="Izin">Izin</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><label>Mulai</label><input type="date" name="tgl_mulai"
                                class="form-control" required></div>
                        <div class="col-6 mb-3"><label>Selesai</label><input type="date" name="tgl_selesai"
                                class="form-control" required></div>
                    </div>
                    <div class="mb-3"><label>Alasan</label><textarea name="alasan" class="form-control" rows="3"
                            required></textarea></div>
                    <div class="mb-3"><label>Upload Bukti</label><input type="file" name="file_pendukung"
                            class="form-control"></div>
                </div>
                <div class="modal-footer border-0"><button type="submit" class="btn btn-primary w-100">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="<?= site_url('admin/update_izin') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id_izin" id="edit_id">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalEditLabel">Edit Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Izin</label>
                        <select name="jenis_izin" id="edit_jenis" class="form-select" required>
                            <option value="Izin">Izin</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Mulai</label>
                            <input type="date" name="tgl_mulai" id="edit_mulai" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Selesai</label>
                            <input type="date" name="tgl_selesai" id="edit_selesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <textarea name="alasan" id="edit_alasan" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-warning w-100 text-white fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
/* Styling agar Search & Export rapi */
.dt-buttons {
    margin-bottom: 1rem;
    float: left;
}

.dataTables_filter {
    margin-bottom: 1rem;
    float: right;
}

.dt-button.buttons-excel {
    background: #198754 !important;
    color: #fff !important;
    border-radius: 8px !important;
    border: none !important;
    padding: 6px 12px !important;
}

.dataTables_filter input {
    padding: 5px 10px !important;
    border: 1px solid #ced4da !important;
    border-radius: 8px !important;
}
</style>
<script>
function editIzin(data) {
    document.getElementById('edit_id').value = data.id_izin;
    document.getElementById('edit_jenis').value = data.jenis_izin;
    document.getElementById('edit_mulai').value = data.tgl_mulai;
    document.getElementById('edit_selesai').value = data.tgl_selesai;
    document.getElementById('edit_alasan').value = data.alasan;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

<?php if (session()->getFlashdata('success')) : ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= session()->getFlashdata('success') ?>',
    timer: 2000,
    showConfirmButton: false
});
<?php endif; ?>

function konfirmasiHapus(url) {
    Swal.fire({
        title: 'Yakin ingin membatalkan?',
        text: "Data ini akan dihapus dan tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Arahkan ke URL hapus
            window.location.href = url;
        }
    })
}
</script>

<?= $this->endSection() ?>