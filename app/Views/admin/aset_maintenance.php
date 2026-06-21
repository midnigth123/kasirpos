<?php

/**
 * @var array $maintenance
 * @var array $daftar_aset
 */
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold text-success mb-1">
            <i class="fas fa-wrench me-2"></i> Maintenance Aset
        </h4>
        <p class="text-muted small mb-0">Kelola riwayat perbaikan dan perawatan aset </p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal"
        data-bs-target="#modalTambahMaintenance">
        <i class="fas fa-plus me-1"></i> Catat Servis
    </button>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1 text-uppercase">Cari Aset/Teknisi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" id="searchInput" class="form-control bg-light border-0"
                        placeholder="Ketik nama..." value="<?= isset($_GET['search']) ? esc($_GET['search']) : '' ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1 text-uppercase">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control bg-light border-0"
                    value="<?= isset($_GET['start_date']) ? esc($_GET['start_date']) : '' ?>">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1 text-uppercase">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control bg-light border-0"
                    value="<?= isset($_GET['end_date']) ? esc($_GET['end_date']) : '' ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-success rounded-3 w-100 fw-bold">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="<?= site_url('admin/aset_maintenance') ?>"
                    class="btn btn-light rounded-3 w-100 fw-bold border">
                    <i class="fas fa-sync-alt me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableMaintenance">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4 py-3 small fw-bold">TANGGAL</th>
                        <th class="border-0 py-3 small fw-bold">NAMA ASET</th>
                        <th class="border-0 py-3 small fw-bold text-end">BIAYA</th>
                        <th class="border-0 py-3 small fw-bold">TEKNISI</th>
                        <th class="border-0 py-3 small fw-bold text-center">JENIS</th>
                        <th class="border-0 py-3 small fw-bold text-center text-uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="maintenanceTableBody">
                    <?php if (empty($maintenance)) : ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Data tidak ditemukan atau belum ada riwayat
                                servis.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($maintenance as $m) : ?>
                        <tr>
                            <td class="px-4 small fw-medium"><?= date('d/m/Y', strtotime($m['tgl_maintenance'])) ?></td>
                            <td>
                                <div class="fw-bold mb-0 text-dark"><?= esc($m['nama_aset']) ?></div>
                                <small class="text-muted text-uppercase"
                                    style="font-size: 0.65rem;"><?= esc($m['kode_aset']) ?></small>
                            </td>
                            <td class="fw-bold text-success text-end">Rp <?= number_format($m['biaya'], 0, ',', '.') ?></td>
                            <td class="small"><?= esc($m['teknisi'] ?? '-') ?></td>
                            <td class="text-center">
                                <span
                                    class="badge rounded-pill <?= $m['jenis_tindakan'] == 'Rutin' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' ?> px-3"
                                    style="font-size: 10px;">
                                    <?= strtoupper($m['jenis_tindakan']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button"
                                        class="btn btn-light btn-sm rounded-pill text-primary px-3 btn-edit-maintenance"
                                        data-mainten='<?= json_encode($m) ?>' title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= site_url('admin/hapus_maintenance/' . $m['id_maintenance']) ?>"
                                        class="btn btn-light btn-sm rounded-pill text-danger px-3 btn-hapus"
                                        title="Hapus Data">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahMaintenance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 p-4 pb-0 text-success">
                <h5 class="fw-bold"><i class="fas fa-plus-circle me-2"></i>Catat Riwayat Servis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/simpan_maintenance') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Pilih Aset</label>
                        <select name="id_aset" class="form-select bg-light border-0 py-2" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($daftar_aset as $a): ?>
                                <option value="<?= $a['id_aset'] ?>"><?= $a['nama_aset'] ?> (<?= $a['kode_aset'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted mb-2 text-uppercase">Tanggal Servis</label>
                            <input type="date" name="tgl_maintenance" class="form-control bg-light border-0 py-2"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted mb-2 text-uppercase">Nama Teknisi</label>
                            <input type="text" name="teknisi" class="form-control bg-light border-0 py-2"
                                placeholder="Nama Teknisi" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Biaya Servis (RP)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted">Rp</span>
                            <input type="text" class="form-control bg-light border-0 py-2 format-rupiah" placeholder="0"
                                required>
                        </div>
                        <input type="hidden" name="biaya" class="raw-biaya">
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Jenis Tindakan</label>
                        <select name="jenis_tindakan" class="form-select bg-light border-0 py-2" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Rutin">Rutin (Cleaning)</option>
                            <option value="Perbaikan">Perbaikan (Repair)</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Keterangan Perbaikan</label>
                        <textarea name="keterangan" class="form-control bg-light border-0" rows="3"
                            placeholder="Detail perbaikan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">Simpan
                        Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditMaintenance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 p-4 pb-0 text-primary">
                <h5 class="fw-bold"><i class="fas fa-edit me-2"></i>Update Data Servis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= site_url('admin/update_maintenance') ?>" method="POST">
                <input type="hidden" name="id_maintenance" id="edit-id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Aset</label>
                        <select name="id_aset" id="edit-aset" class="form-select bg-light border-0 py-2" required>
                            <?php foreach ($daftar_aset as $a): ?>
                                <option value="<?= $a['id_aset'] ?>"><?= $a['nama_aset'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted mb-2 text-uppercase">Tanggal</label>
                            <input type="date" name="tgl_maintenance" id="edit-tgl"
                                class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold text-muted mb-2 text-uppercase">Teknisi</label>
                            <input type="text" name="teknisi" id="edit-teknisi"
                                class="form-control bg-light border-0 py-2" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Jenis Tindakan</label>
                        <select name="jenis_tindakan" id="edit-jenis" class="form-select bg-light border-0 py-2"
                            required>
                            <option value="Rutin">Rutin (Cleaning)</option>
                            <option value="Perbaikan">Perbaikan (Repair)</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Biaya (RP)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-muted">Rp</span>
                            <input type="text" class="form-control bg-light border-0 py-2 format-rupiah"
                                id="edit-biaya-display" required>
                        </div>
                        <input type="hidden" name="biaya" class="raw-biaya" id="edit-biaya-raw">
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold text-muted mb-2 text-uppercase">Keterangan</label>
                        <textarea name="keterangan" id="edit-keterangan" class="form-control bg-light border-0"
                            rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Update
                        Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.15);
    }

    .bg-soft-warning {
        background-color: rgba(255, 193, 7, 0.2);
    }
</style>

<script>
    $(document).ready(function() {
        // 1. Live Search Tabel
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#maintenanceTableBody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // 2. Format Rupiah Universal
        $(document).on('keyup', '.format-rupiah', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            $(this).closest('div').parent().find('.raw-biaya').val(val);
            if (val !== "") {
                $(this).val(new Intl.NumberFormat('id-ID').format(val));
            } else {
                $(this).val('');
            }
        });

        // 3. Mapping Data ke Modal Edit
        $(document).on('click', '.btn-edit-maintenance', function() {
            const data = $(this).data('mainten');
            $('#edit-id').val(data.id_maintenance);
            $('#edit-aset').val(data.id_aset);
            $('#edit-tgl').val(data.tgl_maintenance);
            $('#edit-teknisi').val(data.teknisi);
            $('#edit-jenis').val(data.jenis_tindakan);
            $('#edit-keterangan').val(data.keterangan);

            let biaya = data.biaya.toString();
            $('#edit-biaya-raw').val(biaya);
            $('#edit-biaya-display').val(new Intl.NumberFormat('id-ID').format(biaya));

            $('#modalEditMaintenance').modal('show');
        });

        // 4. Konfirmasi Hapus
        $(document).on('click', '.btn-hapus', function(e) {
            e.preventDefault();
            const link = $(this).attr('href');
            Swal.fire({
                title: 'Hapus riwayat ini?',
                text: "Data servis ini akan hilang permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                borderRadius: '15px'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = link;
            });
        });

        // 5. Alert Flashdata
        <?php if (session()->getFlashdata('success')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "<?= session()->getFlashdata('success'); ?>",
                timer: 2000,
                showConfirmButton: false,
                borderRadius: '15px'
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>