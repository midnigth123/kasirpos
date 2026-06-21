<?php
/**
 * @var array $pengajuan
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark"><i class="fas fa-calendar-alt text-success me-2"></i>Pengajuan Izin & Cuti
            </h4>
            <p class="text-muted small">Kelola permohonan izin, sakit, dan cuti karyawan.</p>
        </div>
    </div>

    <div class="card card-custom shadow-sm p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Pegawai</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Bukti</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pengajuan as $p) : ?>
                    <tr>
                        <td class="fw-bold"><?= esc($p['nama_user']) ?></td>
                        <td>
                            <span class="badge bg-info text-white"><?= $p['jenis_izin'] ?></span>
                        </td>
                        <td><?= date('d M', strtotime($p['tgl_mulai'])) ?> -
                            <?= date('d M', strtotime($p['tgl_selesai'])) ?></td>
                        <td style="max-width: 200px;" class="text-truncate"><?= esc($p['alasan']) ?></td>
                        <td>
                            <?php if ($p['file_pendukung']) : ?>
                            <a href="<?= base_url('uploads/surat/' . $p['file_pendukung']) ?>" target="_blank"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-file-pdf"></i> Lihat
                            </a>
                            <?php else : ?>
                            <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php 
                                $statusClass = ($p['status'] == 'Disetujui') ? 'bg-success' : (($p['status'] == 'Ditolak') ? 'bg-danger' : 'bg-warning');
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= $p['status'] ?></span>
                        </td>
                        <td class="text-center">
                            <?php if ($p['status'] == 'Pending') : ?>
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="<?= site_url('admin/proses_izin/' . $p['id_izin'] . '/Disetujui') ?>"
                                    class="btn btn-sm btn-success" title="Setujui"><i class="fas fa-check"></i></a>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#modalTolak<?= $p['id_izin'] ?>" title="Tolak">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php else : ?>
                            <small class="text-muted">Telah diproses</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php foreach ($pengajuan as $p) : ?>
<?php if ($p['status'] == 'Pending') : ?>
<div class="modal fade" id="modalTolak<?= $p['id_izin'] ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= site_url('admin/proses_izin/' . $p['id_izin'] . '/Ditolak') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Alasan Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="alasan_tolak" class="form-control" rows="3" required
                        placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endforeach; ?>

<script>
<?php if (session()->getFlashdata('success')) : ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: '<?= session()->getFlashdata('success') ?>',
    timer: 2000,
    showConfirmButton: false
});
<?php endif; ?>
</script>

<?= $this->endSection() ?>