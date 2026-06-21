<?php
/**
 * @var array $absensi
 * @var string $tgl_mulai
 * @var string $tgl_selesai
 */
?>

<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white border-0 py-4 px-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-1 text-dark">
                                <i class="fas fa-user-check me-2 text-success"></i> Monitoring Absensi
                            </h5>
                            <p class="text-muted small mb-0">Kelola dan pantau kehadiran staf Senja Coffee secara
                                real-time.</p>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            <a href="<?= site_url('admin/absensi/export_excel_native?tgl_mulai=' . $tgl_mulai . '&tgl_selesai=' . $tgl_selesai) ?>"
                                class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                                <i class="fas fa-file-excel me-2"></i> Export Ke Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 pb-4">
                    <div class="row g-3 mb-4 p-3 bg-light rounded-4 align-items-end">
                        <div class="col-md-6">
                            <form action="<?= site_url('admin/absensi') ?>" method="GET" id="formFilter">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark">Rentang Awal</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="far fa-calendar-alt text-muted"></i></span>
                                            <input type="date" name="tgl_mulai"
                                                class="form-control border-start-0 shadow-none"
                                                value="<?= $tgl_mulai ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-dark">Rentang Akhir</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="far fa-calendar-alt text-muted"></i></span>
                                            <input type="date" name="tgl_selesai"
                                                class="form-control border-start-0 shadow-none"
                                                value="<?= $tgl_selesai ?>">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="submit" form="formFilter"
                                    class="btn btn-dark btn-sm rounded-pill fw-bold px-4 shadow-sm"
                                    style="height: 45px;">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>

                                <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden border flex-grow-1"
                                    style="height: 38px;">
                                    <span class="input-group-text bg-white border-0 ps-3">
                                        <i class="fas fa-search text-success"></i>
                                    </span>
                                    <input type="text" id="autoSearchAbsensi"
                                        class="form-control border-0 bg-white ps-2"
                                        placeholder="Ketik nama atau shift..." style="height: 35px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tableAbsensi">
                            <thead class="bg-transparent">
                                <tr class="text-muted small uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">
                                    <th class="ps-3 border-0">PEGAWAI</th>
                                    <th class="border-0">WAKTU ABSEN</th>
                                    <th class="border-0">SHIFT</th>
                                    <th class="border-0 text-center">MASUK / PULANG</th>
                                    <th class="border-0">FOTO</th>
                                    <th class="border-0">STATUS</th>
                                    <th class="border-0 text-center">DETAIL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($absensi as $row): ?>
                                <tr style="transition: all 0.3s;">
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center py-1">
                                            <div class="avatar-sm me-3 bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                                style="width: 38px; height: 38px; font-size: 14px;">
                                                <?= strtoupper(substr($row['username'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <span
                                                    class="fw-bold text-dark d-block"><?= esc($row['username']) ?></span>
                                                <small class="text-muted" style="font-size: 11px;">ID:
                                                    #ABS-<?= $row['id_absensi'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="text-dark fw-medium"><?= date('d M Y', strtotime($row['tanggal'])) ?></span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-light text-dark border px-2 py-1 fw-bold"><?= esc($row['nama_shift']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle px-2"><?= $row['jam_masuk'] ?></span>
                                            <span
                                                class="badge <?= $row['jam_pulang'] ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-light text-muted border' ?> px-2">
                                                <?= $row['jam_pulang'] ?: '--:--' ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['foto'])): ?>
                                        <div class="position-relative d-inline-block">
                                            <?php 
                                                // 1. Cek apakah filenya benar-benar ada di folder uploads/absensi/
                                                $file_path = FCPATH . 'uploads/absensi/' . $row['foto'];
                                                
                                                if (file_exists($file_path)) {
                                                    // Jika ada, gunakan base_url langsung ke file
                                                    $url_foto = base_url('uploads/absensi/' . $row['foto']);
                                                } else {
                                                    // Jika tidak ada di folder, cek apakah isinya string Base64 (data:image...)
                                                    $url_foto = (strpos($row['foto'], 'data:image') === 0) ? $row['foto'] : 'https://placehold.co/45x45?text=File+Hilang';
                                                }
                                            ?>

                                            <img src="<?= $url_foto ?>" class="rounded-3 shadow-sm border p-1 bg-white"
                                                style="width: 45px; height: 45px; object-fit: cover; cursor: pointer;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalLihatFoto<?= $row['id_absensi'] ?>"
                                                onerror="this.src='https://placehold.co/45x45?text=Error';">
                                        </div>

                                        <div class="modal fade" id="modalLihatFoto<?= $row['id_absensi'] ?>"
                                            tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                                <div class="modal-content border-0 shadow-lg"
                                                    style="border-radius: 20px;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h6 class="modal-title fw-bold"><?= $row['username'] ?></h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-3">
                                                        <div class="rounded-4 overflow-hidden border mb-2">
                                                            <img src="<?= $url_foto ?>" class="img-fluid w-100"
                                                                style="min-height: 200px; object-fit: cover;">
                                                        </div>
                                                        <div class="badge bg-light text-dark border w-100 py-2"
                                                            style="border-radius: 10px;">
                                                            <i class="far fa-clock me-1 text-primary"></i>
                                                            <?= $row['jam_masuk'] ?> (Shift <?= $row['nama_shift'] ?>)
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-image-slash"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $jam_masuk = $row['jam_masuk'];
                                            $shift = $row['nama_shift'];
                                            $status = "Tepat Waktu";
                                            $badge_class = "text-primary bg-primary-subtle border-primary-subtle";

                                            if (($shift == 'Pagi' && $jam_masuk > '08:00:59') || 
                                                ($shift == 'Sore' && $jam_masuk > '14:01:59') || 
                                                ($shift == 'Malam' && $jam_masuk > '21:01:59')) {
                                                $status = "Terlambat";
                                                $badge_class = "text-warning bg-warning-subtle border-warning-subtle";
                                            }
                                        ?>
                                        <span class="badge border <?= $badge_class ?> rounded-pill px-3">
                                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i> <?= $status ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-success btn-sm border-0 rounded-circle"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalLihatFoto<?= $row['id_absensi'] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalLihatFoto<?= $row['id_absensi'] ?>" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                            <div class="modal-body p-3 text-center">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-bold m-0"><?= esc($row['username']) ?></h6>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <img src="<?= $row['foto'] ?>"
                                                    class="img-fluid rounded-4 shadow-sm mb-3"
                                                    onerror="this.src='https://placehold.co/300x400?text=Foto+Tidak+Ada';">
                                                <div class="bg-light p-2 rounded-3 small text-muted fw-bold">
                                                    <i class="far fa-clock me-1"></i> <?= $row['jam_masuk'] ?> (Shift
                                                    <?= esc($row['nama_shift']) ?>)
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#autoSearchAbsensi").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#tableAbsensi tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
$(document).ready(function() {
    $('#tableAbsensi').DataTable({
        "paging": true, // Mengaktifkan Pager
        "lengthChange": true, // Pilihan jumlah baris per halaman
        "searching": true, // Mengaktifkan search
        "ordering": true, // Mengaktifkan sorting
        "info": true, // Menampilkan informasi (Showing 1 to X of Y)
        "language": {
            "paginate": {
                "previous": "Prev",
                "next": "Next"
            },
            "search": "Cari:",
            "lengthMenu": "Tampil _MENU_ data"
        },
        "columnDefs": [{
                "orderable": false,
                "targets": [4, 5, 6]
            } // Nonaktifkan sorting di kolom foto, status, detail
        ]
    });
});
</script>

<style>
/* Styling Hover & Warna Modern */
#tableAbsensi tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.002);
}

.bg-success-subtle {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.bg-danger-subtle {
    background-color: #ffebee;
    color: #c62828;
}

.bg-primary-subtle {
    background-color: #e3f2fd;
    color: #1565c0;
}

.bg-warning-subtle {
    background-color: #fff8e1;
    color: #f9a825;
}

.btn-outline-success:hover {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.input-group-text {
    border: 1px solid #dee2e6;
}

.dataTables_wrapper .dataTables_paginate .page-item .page-link {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 40px !important;
    /* Memberi lebar agar angka tidak terhimpit */
    height: 40px !important;
    /* Memberi tinggi agar terlihat proporsional */
    padding: 0 10px !important;
    border-radius: 8px !important;
    color: #198754;
    font-weight: 500;
}

/* Memperbaiki tombol aktif */
.dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
    background-color: #198754 !important;
    border-color: #198754 !important;
    color: #ffffff !important;
}

/* Memperbaiki tombol Previous/Next agar tidak tertutup */
.dataTables_wrapper .dataTables_paginate .page-item:first-child .page-link,
.dataTables_wrapper .dataTables_paginate .page-item:last-child .page-link {
    padding: 0 15px !important;
    min-width: auto !important;
}
</style>

<?= $this->endSection() ?>