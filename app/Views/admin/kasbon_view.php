<?php

/**
 * @var string $title
 * @var array $kasbon
 * @var array $pegawai
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Approval Kasbon Pegawai</h4>
            <p class="text-muted small">Kelola dan setujui pengajuan pinjaman kru Senja Coffee</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 border-0 py-3 text-secondary small fw-bold">PEGAWAI</th>
                            <th class="border-0 py-3 text-secondary small fw-bold">TANGGAL & NOMINAL</th>
                            <th class="border-0 py-3 text-secondary small fw-bold">ALASAN</th>
                            <th class="border-0 py-3 text-secondary small fw-bold text-center">STATUS</th>
                            <th class="border-0 py-3 pe-4 text-secondary small fw-bold text-center">AKSI APPROVAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($kasbon as $row): ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <?= strtoupper(substr($row['nama_user'], 0, 1)) ?>
                                    </div>
                                    <div class="fw-bold text-dark"><?= $row['nama_user'] ?></div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="text-muted small"><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?>
                                </div>
                                <div class="fw-bold text-danger">Rp <?= number_format($row['nominal'], 0, ',', '.') ?>
                                </div>
                            </td>
                            <td class="py-3">
                                <small class="text-muted italic">"<?= $row['keterangan'] ?>"</small>
                            </td>
                            <td class="py-3 text-center">
                                <?php if($row['status'] == 'Pending'): ?>
                                <span
                                    class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill small">Menunggu</span>
                                <?php elseif($row['status'] == 'Disetujui'): ?>
                                <span
                                    class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small">Disetujui</span>
                                <?php else: ?>
                                <span
                                    class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill small">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-center">
                                <?php if($row['status'] == 'Pending'): ?>
                                <div class="d-flex justify-content-center gap-2">
                                    <button
                                        onclick="konfirmasiApprove('<?= base_url('admin/kasbon/approve/'.$row['id_kasbon']) ?>', '<?= $row['nama_user'] ?>')"
                                        class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-check me-1"></i> Setuju
                                    </button>

                                    <button
                                        onclick="konfirmasiReject('<?= base_url('admin/kasbon/reject/'.$row['id_kasbon']) ?>', '<?= $row['nama_user'] ?>')"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-times me-1"></i> Tolak
                                    </button>
                                </div>
                                <?php else: ?>
                                <span class="text-muted small">Selesai Diproses</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.bg-light {
    background-color: #f8fafc !important;
}

.table tbody tr td {
    border-bottom: 1px solid #f1f5f9;
}

.table tbody tr:last-child td {
    border: 0;
}
</style>
<script>
function konfirmasiApprove(url, nama) {
    Swal.fire({
        title: 'Setujui Kasbon?',
        text: "Anda akan menyetujui pengajuan kasbon dari " + nama,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754', // Warna Hijau Success
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        border: 'none',
        customClass: {
            popup: 'rounded-4 shadow-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
}

function konfirmasiReject(url, nama) {
    Swal.fire({
        title: 'Tolak Kasbon?',
        text: "Apakah Anda yakin ingin menolak pengajuan kasbon dari " + nama + "?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545', // Warna Merah Danger
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-4 shadow-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
}
<?php if(session()->getFlashdata('message')): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= session()->getFlashdata('message') ?>',
    timer: 3000,
    showConfirmButton: false,
    customClass: {
        popup: 'rounded-4'
    }
});
<?php endif; ?>
<?php if(session()->getFlashdata('message')): ?>
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000, // JEDOR! 3 detik otomatis hilang
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

Toast.fire({
    icon: 'success',
    title: '<?= session()->getFlashdata('message') ?>'
});
<?php endif; ?>
</script>

<?= $this->endSection() ?>