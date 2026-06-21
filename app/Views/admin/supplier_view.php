<?php
/**
 * @var array $supplier
 */
?>

<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Daftar Supplier</h3>
            <p class="text-muted small mb-0">Kelola data mitra dan vendor penyuplai bahan baku.</p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm px-4" data-bs-toggle="modal"
            data-bs-target="#modalTambahSupplier">
            <i class="fas fa-plus me-2"></i> Tambah Supplier
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 small fw-bold">NAMA SUPPLIER</th>
                            <th class="py-3 border-0 small fw-bold">KONTAK / PIC</th>
                            <th class="py-3 border-0 small fw-bold">ALAMAT</th>
                            <th class="py-3 border-0 small fw-bold">KATEGORI</th>
                            <th class="pe-4 py-3 border-0 small fw-bold text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($supplier as $row) : ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold mb-0"><?= $row['nama_supplier']; ?></div>
                                <small class="text-muted"><?= $row['kode_supplier']; ?></small>
                            </td>
                            <td>
                                <div class="small"><i class="fab fa-whatsapp text-success me-1"></i>
                                    <?= $row['no_telp']; ?></div>
                                <div class="small text-muted"><?= $row['email']; ?></div>
                            </td>
                            <td class="small text-truncate" style="max-width: 200px;"><?= $row['alamat']; ?></td>
                            <td><span
                                    class="badge bg-soft-primary text-primary rounded-pill px-3"><?= $row['kategori_supply']; ?></span>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light border btn-edit-supplier"
                                        data-supplier='<?= json_encode($row); ?>'>
                                        <i class="fas fa-edit text-warning"></i>
                                    </button>
                                    <a href="<?= base_url('admin/hapus_supplier/'.$row['id_supplier']); ?>"
                                        class="btn btn-sm btn-light border btn-hapus">
                                        <i class="fas fa-trash text-danger"></i>
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
</div>
<div class="modal fade" id="modalTambahSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Tambah Mitra Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/simpan_supplier'); ?>" method="POST">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">KODE SUPPLIER</label>
                            <input type="text" name="kode_supplier" class="form-control bg-light border-0"
                                placeholder="SUP-XXX" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">NAMA PERUSAHAAN / TOKO</label>
                            <input type="text" name="nama_supplier" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NAMA PIC (PERSON IN CHARGE)</label>
                            <input type="text" name="nama_pic" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NOMOR WHATSAPP</label>
                            <input type="number" name="no_telp" class="form-control bg-light border-0"
                                placeholder="08xxxx" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">EMAIL</label>
                            <input type="email" name="email" class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">KATEGORI SUPPLY</label>
                            <select name="kategori_supply" class="form-select bg-light border-0">
                                <option value="Bahan Baku (Kopi/Susu)">Bahan Baku (Kopi/Susu)</option>
                                <option value="Packaging">Packaging</option>
                                <option value="Peralatan">Peralatan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">ALAMAT LENGKAP</label>
                            <textarea name="alamat" class="form-control bg-light border-0" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditSupplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold mb-0">Edit Mitra Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/update_supplier'); ?>" method="POST">
                <input type="hidden" name="id_supplier" id="edit-id-supp">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">KODE SUPPLIER</label>
                            <input type="text" id="edit-kode-supp" class="form-control bg-light border-0" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">NAMA PERUSAHAAN / TOKO</label>
                            <input type="text" name="nama_supplier" id="edit-nama-supp"
                                class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NAMA PIC</label>
                            <input type="text" name="nama_pic" id="edit-pic-supp"
                                class="form-control bg-light border-0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NOMOR WHATSAPP</label>
                            <input type="number" name="no_telp" id="edit-telp-supp"
                                class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">KATEGORI SUPPLY</label>
                            <select name="kategori_supply" id="edit-kategori-supp"
                                class="form-select bg-light border-0">
                                <option value="Bahan Baku (Kopi/Susu)">Bahan Baku (Kopi/Susu)</option>
                                <option value="Packaging">Packaging</option>
                                <option value="Peralatan">Peralatan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">ALAMAT LENGKAP</label>
                            <textarea name="alamat" id="edit-alamat-supp" class="form-control bg-light border-0"
                                rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="submit" class="btn btn-warning rounded-3 px-4 fw-bold">Update Mitra</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // 1. LOGIK MAPPING DATA KE MODAL EDIT
    $('.btn-edit-supplier').on('click', function() {
        const data = $(this).data('supplier');

        $('#edit-id-supp').val(data.id_supplier);
        $('#edit-kode-supp').val(data.kode_supplier);
        $('#edit-nama-supp').val(data.nama_supplier);
        $('#edit-pic-supp').val(data.nama_pic);
        $('#edit-telp-supp').val(data.no_telp);
        $('#edit-kategori-supp').val(data.kategori_supply);
        $('#edit-alamat-supp').val(data.alamat);

        $('#modalEditSupplier').modal('show');
    });

    // 2. ALERT KONFIRMASI HAPUS (Berdiri Sendiri)
    $('.btn-hapus').on('click', function(e) {
        e.preventDefault();
        const link = $(this).attr('href');

        Swal.fire({
            title: 'Yakin mau hapus?',
            text: "Data supplier ini bakal hilang selamanya",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = link;
            }
        });
    });

    // 3. NOTIFIKASI BERHASIL (Flashdata)
    <?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        icon: 'success',
        title: 'Supplier Berhasil di Simpan!',
        text: "<?= session()->getFlashdata('success'); ?>",
        showConfirmButton: false,
        timer: 2500,
        borderRadius: '15px'
    });
    <?php endif; ?>
});
</script>
<style>
.bg-soft-primary {
    background-color: #e7f1ff;
    color: #0d6efd;
}

.table-responsive {
    min-height: 300px;
}
</style>
<?= $this->endSection() ?>