<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Pengaturan Shift Kasir</h4>
            <p class="text-muted mb-0">Kelola jadwal shift operasional kasir</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahShiftModal">
            <i class="fas fa-plus me-1"></i> Tambah Shift
        </button>
    </div>

    <?php if (session()->getFlashdata('pesan_sukses')) : ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('pesan_sukses'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Shift</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($shift)): ?>
                        <?php foreach ($shift as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td class="fw-bold"><?= $row['nama_shift'] ?></td>
                            <td><i class="fas fa-clock text-success me-1"></i> <?= $row['jam_mulai'] ?></td>
                            <td><i class="fas fa-clock text-danger me-1"></i> <?= $row['jam_selesai'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                    data-bs-target="#hapusShiftModal<?= $row['shift_id'] ?>">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="hapusShiftModal<?= $row['shift_id'] ?>" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-sm" style="border-radius: 16px;">
                                    <div class="modal-header bg-danger text-white border-0 p-4">
                                        <h5 class="modal-title fw-bold">Konfirmasi Hapus</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-4 px-4 text-center text-dark">
                                        <p class="mb-0">Apakah Anda yakin ingin menghapus
                                            <strong><?= $row['nama_shift'] ?></strong>?
                                        </p>
                                    </div>
                                    <div class="modal-footer border-0 justify-content-center gap-2 p-4">
                                        <button type="button" class="btn btn-outline-secondary px-4"
                                            data-bs-dismiss="modal">Batal</button>
                                        <a href="<?= site_url('admin/shift-kasir/hapus/'.$row['shift_id']) ?>"
                                            class="btn btn-danger px-4">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada data shift yang ditambahkan.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white border-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Tambah Shift Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/shift-kasir/simpan') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Shift</label>
                        <input type="text" class="form-control" name="nama_shift" placeholder="Contoh: Shift Pagi"
                            required style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jam Mulai</label>
                        <input type="time" class="form-control" name="jam_mulai" required style="border-radius: 10px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jam Selesai</label>
                        <input type="time" class="form-control" name="jam_selesai" required
                            style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal"
                        style="border-radius: 10px;">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 text-white"
                        style="background-color: #0d6efd; border-radius: 10px;">Simpan Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>