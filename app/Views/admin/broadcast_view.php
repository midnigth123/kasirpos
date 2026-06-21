<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<style>
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
</style>

<div class="container-fluid py-4">
    <div class="card card-custom shadow-sm p-4">
        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
            <div>
                <h4 class="fw-bold text-dark mb-1"><i class="fas fa-bullhorn text-danger me-2"></i>Centralized Broadcast
                    System</h4>
                <p class="text-muted small mb-0">Kirim pengumuman, info maintenance server, atau promo massal langsung
                    ke dashboard seluruh kasir tenant.</p>
            </div>
            <button type="button" class="btn btn-danger btn-action text-white px-3" data-bs-toggle="modal"
                data-bs-target="#modalTambahBroadcast">
                <i class="fas fa-plus-circle me-1"></i> Terbitkan Broadcast
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-custom table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="border-top-left-radius: 10px; width: 20%;">Judul Siaran</th>
                        <th>Isi Pesan Broadcast</th>
                        <th style="width: 12%;">Mulai Tayang</th>
                        <th style="width: 12%;">Selesai Tayang</th>
                        <th class="text-center" style="width: 15%;">Status</th>
                        <th class="text-center" style="border-top-right-radius: 10px; width: 12%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($semua_pengumuman)) : ?>
                    <?php foreach ($semua_pengumuman as $p) : ?>
                    <tr>
                        <td class="fw-bold text-dark"><?= $p['judul'] ?></td>
                        <td class="text-secondary"><?= $p['isi_pesan'] ?></td>
                        <td><span class="badge bg-light text-dark py-2 px-3 fw-bold"><i
                                    class="far fa-calendar-alt text-muted me-2"></i><?= date('d-m-Y', strtotime($p['tgl_mulai'])) ?></span>
                        </td>
                        <td><span class="badge bg-light text-dark py-2 px-3 fw-bold"><i
                                    class="far fa-calendar-alt text-muted me-2"></i><?= date('d-m-Y', strtotime($p['tgl_selesai'])) ?></span>
                        </td>
                        <td class="text-center">
                            <span
                                class="badge bg-<?= $p['status_aktif'] === 'Y' ? 'success' : 'secondary' ?> px-3 py-2 rounded-pill">
                                <?= $p['status_aktif'] === 'Y' ? 'Aktif Menyiarkan' : 'Mati' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">

                                <?php if ($p['status_aktif'] === 'Y') : ?>
                                <a href="<?= site_url('admin/status_broadcast/' . $p['id_pengumuman'] . '/N') ?>"
                                    class="btn btn-sm btn-outline-danger py-1 px-2" title="Matikan">
                                    <i class="fas fa-power-off"></i>
                                </a>
                                <?php else : ?>
                                <a href="<?= site_url('admin/status_broadcast/' . $p['id_pengumuman'] . '/Y') ?>"
                                    class="btn btn-sm btn-outline-success py-1 px-2" title="Hidupkan">
                                    <i class="fas fa-broadcast-tower"></i>
                                </a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-outline-warning py-1 px-2"
                                    onclick="editBroadcast(<?= htmlspecialchars(json_encode($p)) ?>)" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <a href="<?= site_url('admin/hapus_broadcast/' . $p['id_pengumuman']) ?>"
                                    class="btn btn-sm btn-outline-danger py-1 px-2"
                                    onclick="return confirm('Yakin ingin menghapus siaran ini?')" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-volume-mute fa-3x mb-3 text-light"></i><br>Belum ada riwayat siaran
                            broadcast dari pusat.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahBroadcast" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content style-custom" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0 bg-light"
                style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-paper-plane text-danger me-2"></i>Buat Siaran
                    Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/simpan_pengumuman_aksi') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Judul Pengumuman</label>
                        <input type="text" name="judul" class="form-control"
                            placeholder="Contoh: INFO MAINTENANCE SERVER SAKTI" style="border-radius: 8px;" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Isi Pesan Siaran</label>
                        <textarea name="isi_pesan" class="form-control" rows="4"
                            placeholder="Ketik pesan informasi penting Bos di sini..." style="border-radius: 8px;"
                            required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Tanggal Mulai Tayang</label>
                            <input type="date" name="tgl_mulai" class="form-control" style="border-radius: 8px;"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Tanggal Selesai Tayang</label>
                            <input type="date" name="tgl_selesai" class="form-control" style="border-radius: 8px;"
                                value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label fw-bold text-secondary">Status Langsung Aktif?</label>
                        <select name="status_aktif" class="form-select" style="border-radius: 8px;">
                            <option value="Y">Y - Langsung Siarkan Sekarang</option>
                            <option value="N">N - Simpan Sebagai Draft Saja</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light p-3"
                    style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                    <button type="button" class="btn btn-secondary btn-action px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-action px-4"><i class="fas fa-bullhorn me-1"></i>
                        Sebar Siaran!</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function editBroadcast(data) {
    const modal = new bootstrap.Modal(document.getElementById('modalTambahBroadcast'));
    const form = document.querySelector('#modalTambahBroadcast form');

    // Ubah Action Form ke mode edit
    form.action = "<?= site_url('admin/update_broadcast') ?>/" + data.id_pengumuman;

    // Isi Input
    form.querySelector('[name="judul"]').value = data.judul;
    form.querySelector('[name="isi_pesan"]').value = data.isi_pesan;
    form.querySelector('[name="tgl_mulai"]').value = data.tgl_mulai;
    form.querySelector('[name="tgl_selesai"]').value = data.tgl_selesai;
    form.querySelector('[name="status_aktif"]').value = data.status_aktif;

    modal.show();
}

function resetForm() {
    const form = document.querySelector('#modalTambahBroadcast form');
    form.action = "<?= site_url('admin/simpan_pengumuman_aksi') ?>";
    form.reset();
}
window.addEventListener('load', function() {
    let pesanSukses = "<?= session()->getFlashdata('success') ?>";
    if (pesanSukses !== "") {
        Swal.fire({
            title: 'BroadCast Berhasil!',
            text: pesanSukses,
            icon: 'success',
            timer: 2500,
            showConfirmButton: false,
            timerProgressBar: true
        });
    }
});
</script>
<?= $this->endSection() ?>