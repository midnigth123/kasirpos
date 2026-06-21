<?php

/**
 * @var array $meja
 */
?>

<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<style>
    .card-meja {
        border: none;
        border-radius: 16px;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }

    .card-meja:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .status-badge {
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .bg-available {
        background: #ecfdf5;
        color: #10b981;
    }

    .bg-occupied {
        background: #fff7ed;
        color: #f97316;
    }
</style>

<div class="wrapper-full">
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex justify-content-center align-items-center text-white"
                    style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #3B82F6, #2563EB);">
                    <i class="fas fa-border-all fa-lg"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">Pengaturan Meja</h4>
                    <p class="text-muted small mb-0">Kelola denah dan ketersediaan meja restoran.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 12px;" data-bs-toggle="modal" data-bs-target="#modalTambahMeja">
                <i class="fas fa-plus me-2"></i> Tambah Meja
            </button>
        </div>
    </div>
    <?php if (session()->getFlashdata('pesan_sukses')) : ?>
        <div class="alert alert-modern alert-dismissible fade show mb-4 p-3" id="autoCloseAlert" role="alert"
            style="background-color: #ecfdf5; color: #065f46; border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                <div>
                    <h6 class="fw-bold mb-0">Berhasil!</h6>
                    <small><?= session()->getFlashdata('pesan_sukses'); ?></small>
                </div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="modern-card p-3 d-flex align-items-center gap-3">
                <div class="bg-light p-3 rounded-3 text-primary"><i class="fas fa-chair fa-lg"></i></div>
                <div>
                    <h5 class="fw-bold mb-0"><?= count($meja) ?></h5>
                    <small class="text-muted">Total Meja</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($meja as $m) : ?>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card card-meja modern-card p-3 text-center">
                    <div class="dropdown text-end">
                        <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm" style="border-radius: 12px;">
                            <li><a class="dropdown-item small" href="#" onclick="editMeja('<?= $m['id_meja'] ?>', '<?= $m['nomor_meja'] ?>')"><i class="fas fa-edit me-2"></i> Edit</a></li>
                            <li>
                                <a class="dropdown-item small text-danger btn-hapus-meja"
                                    href="javascript:void(0)"
                                    data-id="<?= $m['id_meja'] ?>"
                                    data-nomor="<?= esc($m['nomor_meja']) ?>">
                                    <i class="fas fa-trash me-2"></i> Hapus
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="my-3">
                        <i class="fas fa-couch fa-3x <?= $m['status_meja'] == 'Tersedia' ? 'text-success' : 'text-warning' ?>" style="opacity: 0.2;"></i>
                        <h3 class="fw-bold mt-2 mb-1"><?= esc($m['nomor_meja']) ?></h3>
                        <span class="status-badge <?= $m['status_meja'] == 'Tersedia' ? 'bg-available' : 'bg-occupied' ?>">
                            <?= esc($m['status_meja']) ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="modal fade" id="modalTambahMeja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form action="<?= site_url('admin/meja/simpan') ?>" method="post">
                <div class="modal-header border-0">
                    <h6 class="fw-bold mb-0">Tambah Meja Baru</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-0">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nomor/Nama Meja</label>
                        <input type="text" name="nomor_meja" class="form-control" placeholder="Contoh: 01 atau A1" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="border-radius: 10px;">Simpan Meja</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditMeja" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <form id="formEditMeja" method="post">
                <div class="modal-header border-0">
                    <h6 class="fw-bold mb-0">Ubah Data Meja</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-0">
                    <div class="mb-3">
                        <label class="small fw-bold mb-1">Nomor/Nama Meja Baru</label>
                        <input type="text" name="nomor_meja" id="edit_nomor_meja" class="form-control" placeholder="Contoh: 02" required style="border-radius: 10px;">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="border-radius: 10px; background: #3B82F6; border: none;">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    setTimeout(function() {
        var alertNode = document.getElementById('autoCloseAlert');
        if (alertNode) {
            var bsAlert = new bootstrap.Alert(alertNode);
            bsAlert.close();
        }
    }, 4000);

    function editMeja(id, nomor) {
        $('#formEditMeja').attr('action', '<?= site_url('admin/meja/update/') ?>' + id);
        $('#edit_nomor_meja').val(nomor);
        var modalEdit = new bootstrap.Modal(document.getElementById('modalEditMeja'));
        modalEdit.show();
    }
    $(document).on('click', '.btn-hapus-meja', function(e) {
        e.preventDefault();

        const idMeja = $(this).data('id');
        const nomorMeja = $(this).data('nomor');

        Swal.fire({
            title: 'Hapus Meja ' + nomorMeja + '?',
            text: "Data meja ini akan dihapus permanen dari sistem!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626', // Warna Merah
            cancelButtonColor: '#6B7280', // Warna Abu-abu
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            border: 'none',
            borderRadius: '20px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading sebentar biar makin pro
                Swal.fire({
                    title: 'Sedang Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Arahkan ke URL hapus di controller
                window.location.href = "<?= site_url('admin/meja/hapus/') ?>" + idMeja;
            }
        });
    });
</script>
<?= $this->endSection() ?>