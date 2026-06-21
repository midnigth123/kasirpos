<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<div class="container-fluid py-4">
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-filter me-2 text-primary"></i>Filter Rentang Tanggal
            </h6>
            <form method="get" action="<?= current_url() ?>" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-medium">Tanggal Mulai</label>
                    <input type="date" name="tgl_mulai" class="form-control rounded-3"
                        value="<?= request()->getGet('tgl_mulai') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-medium">Tanggal Selesai</label>
                    <input type="date" name="tgl_selesai" class="form-control rounded-3"
                        value="<?= request()->getGet('tgl_selesai') ?>">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-3 w-100 fw-medium">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="<?= site_url('admin/retur') ?>"
                        class="btn btn-outline-secondary rounded-3 w-100 fw-medium">
                        <i class="fas fa-sync-alt me-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Riwayat Retur & Pembatalan</h5>
            <div id="wrapper-tombol-export"></div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelReturFinal" class="table table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>Tanggal</th>
                            <th>Kode Retur</th>
                            <th>Invoice Asal</th>
                            <th>Total Refund</th>
                            <th>Kasir</th>
                            <th style="width: 10%" class="no-export">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($all_retur)) : ?>
                        <?php $no = 1; foreach ($all_retur as $row) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td data-order="<?= strtotime($row['created_at']) ?>">
                                <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                            </td>
                            <td><span class="badge bg-secondary text-white"><?= esc($row['kode_retur']) ?></span></td>
                            <td><?= esc($row['invoice_asal']) ?></td>
                            <td class="fw-semibold text-danger" data-order="<?= $row['total_refund'] ?>">
                                Rp <?= number_format($row['total_refund'], 0, ',', '.') ?>
                            </td>
                            <td><?= esc($row['nama_kasir'] ?? 'System') ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 btn-detail-retur"
                                    data-bs-toggle="modal" data-bs-target="#returModal"
                                    data-id="<?= $row['id_retur'] ?>" data-kode="<?= $row['kode_retur'] ?>">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="returModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 py-3">
                <h5 class="modal-title fw-bold">Detail Retur <span id="modal-retur-title" class="text-muted"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="returModalBody">
            </div>
            <div class="modal-footer border-0 py-3">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi DataTables beserta Konfigurasi Tombol Excel & PDF
    var table = $('#tabelReturFinal').DataTable({
        "order": [
            [0, "asc"]
        ], // Urutan default berdasarkan kolom nomor
        "pageLength": 10,
        "language": {
            "search": "Cari Cepat:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        },
        "buttons": [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-2"></i>Excel',
                className: 'btn btn-success btn-sm rounded-3 px-3 fw-medium',
                title: 'Laporan Riwayat Retur dan Pembatalan',
                exportOptions: {
                    columns: ':not(.no-export)' // Kolom dengan class 'no-export' (Aksi) tidak ikut diunduh
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf me-2"></i>PDF',
                className: 'btn btn-danger btn-sm rounded-3 px-3 fw-medium',
                title: 'Laporan Riwayat Retur dan Pembatalan',
                exportOptions: {
                    columns: ':not(.no-export)'
                },
                customize: function(doc) {
                    doc.content[1].table.widths = ['5%', '20%', '25%', '20%', '15%',
                    '15%']; // Atur proporsi lebar kolom PDF
                    doc.styles.tableHeader.alignment = 'left';
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-2"></i>Cetak',
                className: 'btn btn-dark btn-sm rounded-3 px-3 fw-medium',
                title: 'Laporan Riwayat Retur dan Pembatalan',
                exportOptions: {
                    columns: ':not(.no-export)'
                }
            }
        ]
    });

    // Pindahkan posisi tombol ekspor dari bawaan DataTables ke container kustom di bagian header card
    table.buttons().container().appendTo('#wrapper-tombol-export');

    // 2. SweetAlert2 Flashdata Notification (Sukses / Error)
    <?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        title: 'Berhasil!',
        text: '<?= (string)session()->getFlashdata("success") ?>',
        icon: 'success',
        timer: 3000,
        showConfirmButton: false,
        background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#252525' :
            '#fff',
        color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#fff' : '#000',
    });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
    Swal.fire({
        title: 'Gagal Memproses!',
        text: '<?= (string)session()->getFlashdata("error") ?>',
        icon: 'error',
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Tutup',
        background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#252525' :
            '#fff',
        color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#fff' : '#000',
    });
    <?php endif; ?>

    // 3. Sistem Fetch Data ke dalam Modal Detail
    const returModal = document.getElementById('returModal');
    if (returModal) {
        returModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const returId = button.getAttribute('data-id');
            const kodeRetur = button.getAttribute('data-kode');
            const modalBody = document.getElementById('returModalBody');
            const modalTitle = document.getElementById('modal-retur-title');

            if (modalTitle) modalTitle.innerText = '#' + kodeRetur;

            modalBody.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>
                    <span>Memuat data detail retur...</span>
                </div>
            `;

            fetch('<?= site_url('admin/retur/detail/') ?>' + returId)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal memuat data');
                    return response.text();
                })
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    modalBody.innerHTML = `
                        <div class="alert alert-danger text-center m-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> Gagal memuat rincian detail retur.
                        </div>
                    `;
                });
        });
    }
});
</script>

<?= $this->endSection() ?>