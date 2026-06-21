<?php
/**
 * @var array $promo
 * 
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Daftar Promo Senja Coffee</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah
            Promo</button>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Promo</th>
                    <th>Potongan</th>
                    <th>Min. Belanja</th>
                    <th>Berlaku</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($promo as $p): ?>
                <tr>
                    <td><span class="badge bg-info text-dark"><?= $p['kode_promo'] ?></span></td>
                    <td><?= $p['nama_promo'] ?></td>
                    <td><?= ($p['tipe_promo'] == 'persen' ? $p['nilai_promo'].'%' : 'Rp '.number_format($p['nilai_promo'])) ?>
                    </td>
                    <td>Rp <?= number_format($p['min_belanja']) ?></td>
                    <td><?= date('d M', strtotime($p['start_date'])) ?> -
                        <?= date('d M Y', strtotime($p['end_date'])) ?></td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm"
                            onclick="editPromo(<?= htmlspecialchars(json_encode($p)) ?>)">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm"
                            onclick="hapusPromo('<?= $p['id_promo'] ?>', '<?= $p['nama_promo'] ?>')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Buat Promo Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/simpan_promo') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Promo</label>
                        <input type="text" name="nama_promo" class="form-control"
                            placeholder="Contoh: Diskon Awal Bulan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Promo (Untuk di Kasir)</label>
                        <input type="text" name="kode_promo" class="form-control" placeholder="Contoh: GAJIAN" required
                            style="text-transform:uppercase">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe</label>
                            <select name="tipe_promo" class="form-select">
                                <option value="nominal">Potongan Harga (Rp)</option>
                                <option value="persen">Persentase (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nilai Potongan</label>
                            <input type="number" name="nilai_promo" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimal Belanja (Rp)</label>
                        <input type="number" name="min_belanja" class="form-control" value="0">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mulai</label>
                            <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Berakhir</label>
                            <input type="date" name="end_date" class="form-control"
                                value="<?= date('Y-m-d', strtotime('+1 month')) ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Promo</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Promo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/update_promo') ?>" method="POST">
                <input type="hidden" name="id_promo" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Promo</label>
                        <input type="text" name="nama_promo" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Promo</label>
                        <input type="text" name="kode_promo" id="edit_kode" class="form-control" required
                            style="text-transform:uppercase">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe</label>
                            <select name="tipe_promo" id="edit_tipe" class="form-select">
                                <option value="nominal">Potongan Harga (Rp)</option>
                                <option value="persen">Persentase (%)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nilai</label>
                            <input type="number" name="nilai_promo" id="edit_nilai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Minimal Belanja</label>
                        <input type="number" name="min_belanja" id="edit_min" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mulai</label>
                            <input type="date" name="start_date" id="edit_start" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Berakhir</label>
                            <input type="date" name="end_date" id="edit_end" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update Promo</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function hapusPromo(id, nama) {
    Swal.fire({
        title: "Hapus Promo?",
        text: "Promo '" + nama + "' akan dihapus permanen dari sistem Senja Coffee!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika diklik Ya, arahkan ke URL hapus
            window.location.href = "<?= base_url('admin/hapus_promo/') ?>" + id;
        }
    });
}

function editPromo(data) {
    $('#edit_id').val(data.id_promo);
    $('#edit_nama').val(data.nama_promo);
    $('#edit_kode').val(data.kode_promo);
    $('#edit_tipe').val(data.tipe_promo);
    $('#edit_nilai').val(data.nilai_promo);
    $('#edit_min').val(data.min_belanja);
    $('#edit_start').val(data.start_date);
    $('#edit_end').val(data.end_date);

    var modal = new bootstrap.Modal(document.getElementById('modalEdit'));
    modal.show();
}
$(document).ready(function() {
    // Cek jika ada flashdata 'success' dari Controller
    <?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        title: "Berhasil!",
        text: "<?= session()->getFlashdata('success') ?>",
        icon: "success",
        timer: 2000,
        showConfirmButton: false
    });
    <?php endif; ?>

    // Cek jika ada flashdata 'error'
    <?php if (session()->getFlashdata('error')) : ?>
    Swal.fire({
        title: "Gagal!",
        text: "<?= session()->getFlashdata('error') ?>",
        icon: "error"
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>