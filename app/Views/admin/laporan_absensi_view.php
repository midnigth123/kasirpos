<?php

/**
 * @var string $tgl_awal
 * @var string $tgl_akhir
 * @var array $rekap
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fas fa-clipboard-list text-primary me-2"></i>Laporan Absensi Pegawai</h4>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
        <form method="GET" class="row g-3">
            <div class="col-auto"><input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" class="form-control">
            </div>
            <div class="col-auto"><input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="form-control">
            </div>
            <div class="col-auto"><button type="submit" class="btn btn-primary px-4">Filter</button></div>
        </form>
    </div>

    <div class="card shadow-sm border-0 rounded-4 p-4">
        <div class="row align-items-center mb-3">
            <div class="col-6">
                <div id="exportWrapper"></div>
            </div>
            <div class="col-6 text-end">
                <div id="searchWrapper"></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="tabelLaporan">
                <thead class="table-light">
                    <tr>
                        <th>Nama Pegawai</th>
                        <th>Total Hadir</th>
                        <th>Total Terlambat</th>
                        <th>Total Izin/Cuti</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rekap as $r) : ?>
                    <tr>
                        <td class="fw-bold"><?= $r['nama_user'] ?></td>
                        <td><span class="badge bg-success rounded-pill px-3"><?= $r['total_hadir'] ?> hari</span></td>
                        <td><span class="badge bg-danger rounded-pill px-3"><?= $r['total_terlambat'] ?> kali</span>
                        </td>
                        <td><span class="badge bg-info rounded-pill px-3"><?= $r['total_izin_cuti'] ?> kali</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Styling tombol & search agar presisi */
.dt-button.buttons-excel {
    background: #198754 !important;
    color: #fff !important;
    border-radius: 8px !important;
    border: none !important;
    padding: 8px 16px !important;
}

.dataTables_filter input {
    padding: 6px 12px !important;
    border: 1px solid #ced4da !important;
    border-radius: 8px !important;
    margin-left: 10px !important;
}

/* Pagination rapi dan sejajar */
.dataTables_wrapper .dataTables_paginate {
    display: flex !important;
    justify-content: flex-end !important;
    align-items: center !important;
    margin-top: 1rem !important;
    gap: 5px;
}

/* Tombol Individual (Previous, 1, 2, Next) */
.dataTables_wrapper .dataTables_paginate .paginate_button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 40px !important;
    height: 40px !important;
    padding: 0 15px !important;
    /* Tambahkan padding samping agar angka tidak mepet */
    border: 1px solid #dee2e6 !important;
    border-radius: 8px !important;
    background: #ffffff !important;
    color: #333 !important;
    /* Warna teks dasar */
    cursor: pointer;
    transition: all 0.2s ease;
}

/* Tombol yang Sedang Aktif (Current Page) */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #0d6efd !important;
    /* Biru Bootstrap */
    color: #ffffff !important;
    /* Teks putih */
    border-color: #0d6efd !important;
}

/* Tombol saat Hover */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
    background: #e9ecef !important;
    border-color: #ced4da !important;
}

/* Tombol Disabled (Prev/Next di awal/akhir) */
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    color: #adb5bd !important;
    background: #f8f9fa !important;
    cursor: not-allowed;
}
</style>

<script>
$(document).ready(function() {
    var table = $('#tabelLaporan').DataTable({
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fas fa-file-excel me-1"></i> Export Excel',
            title: 'Laporan_Absensi_<?= date('d-m-Y') ?>'
        }],
        language: {
            search: "Cari data:"
        }
    });

    // Pindahkan elemen DataTables ke wrapper yang baru kita buat
    table.buttons().container().appendTo('#exportWrapper');
    $('.dataTables_filter').appendTo('#searchWrapper');
});
</script>
<?= $this->endSection() ?>