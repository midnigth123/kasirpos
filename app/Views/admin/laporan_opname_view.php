<?php
/**
 * 
 * @var array $opname
 */
?>


?>


<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="container-fluid py-5 px-4 opname-container">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h3 class="fw-bold section-title mb-1">Riwayat Stok Opname</h3>
            <p class="text-muted small mb-0">Terdiri dari <span class="fw-bold text-info"><?= count($opname) ?></span>
                grup audit</p>
        </div>
        <!-- <div class="col-auto">
            <a href="<?= base_url('admin/stokopname') ?>" class="btn btn-info px-4 py-2 shadow-sm hover-up text-white"
                style="border-radius: 12px; font-weight: 600;">
                <i class="fas fa-plus-circle me-2"></i> Tambah Opname
            </a>
        </div> -->
    </div>

    <div class="card border-0 shadow-sm mb-4 p-3" style="border-radius: 15px;">
        <form action="" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="small fw-bold text-muted">DARI TANGGAL</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= @$_GET['tgl_awal'] ?>">
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted">SAMPAI TANGGAL</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= @$_GET['tgl_akhir'] ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="border-radius: 10px;">
                    <i class="fas fa-filter me-2"></i> Filter
                </button>
            </div>
            <?php if(isset($_GET['tgl_awal'])): ?>
            <div class="col-md-1">
                <a href="<?= base_url('admin/laporan_opname') ?>" class="btn btn-light w-100 shadow-sm"
                    style="border-radius: 10px;">Reset</a>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="card border-0 shadow-sm custom-card" style="border-radius: 20px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3">Waktu Audit</th>
                            <th class="py-3">Kode Opname</th>
                            <th class="py-3 text-center">Total Item</th>
                            <th class="py-3">Petugas</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($opname as $o): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex flex-column">
                                    <span
                                        class="fw-semibold small date-text"><?= date('d M Y', strtotime($o['created_at'])) ?></span>
                                    <span class="text-muted"
                                        style="font-size: 0.7rem;"><?= date('H:i', strtotime($o['created_at'])) ?>
                                        WIB</span>
                                </div>
                            </td>
                            <td>
                                <code class="fw-bold text-primary"><?= $o['kode_opname'] ?: 'TANPA-KODE' ?></code>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-light text-dark border px-3"><?= $o['total_item'] ?>
                                    Produk</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($o['nama_user'] ?? 'User') ?>&background=0D6EFD&color=fff"
                                        class="rounded-circle me-2" width="24" height="24">
                                    <span
                                        class="small text-muted-custom text-capitalize"><?= $o['nama_user'] ?? 'Admin' ?></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-info px-3 btn-detail"
                                    data-kode="<?= $o['kode_opname'] ?>" data-id="<?= $o['opname_id'] ?>"
                                    style="border-radius: 8px;">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($opname)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="fw-bold">Rincian Barang Audit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Kode Transaksi: </small><span id="detail-kode"
                            class="fw-bold text-primary"></span>
                    </div>
                    <button type="button" id="btn-cetak-modal" class="btn btn-sm btn-primary px-3 shadow-sm"
                        style="border-radius: 8px;">
                        <i class="fas fa-print me-1"></i> Cetak Laporan
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-2">Nama Produk</th>
                                <th class="text-center">Sistem</th>
                                <th class="text-center">Fisik</th>
                                <th class="text-center">Selisih</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="isi-detail">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Tambahkan Iframe secara dinamis ke dalam body jika belum ada
    if ($('#printFrame').length === 0) {
        $('body').append('<iframe id="printFrame" style="display:none;"></iframe>');
    }

    $('.btn-detail').click(function() {
        let kode = $(this).data('kode');
        let id = $(this).data('id');

        $('#detail-kode').text(kode);

        // Update URL data pada tombol cetak (kita gunakan attribute data agar lebih aman)
        $('#btn-cetak-modal').data('url', "<?= base_url('admin/cetak_opname') ?>/" + id);

        $('#isi-detail').html('<tr><td colspan="5" class="text-center py-3">Memuat data...</td></tr>');
        $('#modalDetail').modal('show');

        $.ajax({
            url: "<?= base_url('admin/detail_opname') ?>/" + kode,
            method: "GET",
            dataType: "json",
            success: function(data) {
                let html = '';
                if (data.length > 0) {
                    data.forEach(function(v) {
                        let badgeClass = v.selisih < 0 ? 'badge-minus' : (v
                            .selisih > 0 ? 'badge-plus' : 'badge-zero');
                        let selisihText = v.selisih > 0 ? '+' + v.selisih : v
                            .selisih;

                        html += `<tr>
                            <td class="ps-3 fw-semibold">${v.nama_produk}</td>
                            <td class="text-center">${v.stok_sistem}</td>
                            <td class="text-center fw-bold">${v.stok_fisik}</td>
                            <td class="text-center">
                                <span class="badge-opname ${badgeClass}">${selisihText}</span>
                            </td>
                            <td class="small text-muted">${v.keterangan || '-'}</td>
                        </tr>`;
                    });
                } else {
                    html =
                        '<tr><td colspan="5" class="text-center py-3 text-muted">Tidak ada rincian data.</td></tr>';
                }
                $('#isi-detail').html(html);
            },
            error: function() {
                $('#isi-detail').html(
                    '<tr><td colspan="5" class="text-center py-3 text-danger">Gagal memuat data.</td></tr>'
                );
            }
        });
    });

    // Fungsi klik tombol cetak di dalam modal
    $('#btn-cetak-modal').click(function(e) {
        e.preventDefault();
        let url = $(this).data('url');

        if (!url) return;

        const iframe = document.getElementById('printFrame');
        iframe.src = url;

        // Munculkan loading tipis pada tombol agar user tahu proses sedang jalan
        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Menyiapkan...').addClass('disabled');

        iframe.onload = function() {
            // Kembalikan tombol ke semula
            btn.html(originalText).removeClass('disabled');

            // Eksekusi Print
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };
    });
});
</script>

<style>
.opname-container {
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
}

.custom-card {
    background-color: white !important;
    border: none !important;
}

.custom-table thead th {
    color: #888;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 700;
    border: none;
}

.badge-opname {
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 700;
    display: inline-block;
    min-width: 45px;
    text-align: center;
}

.badge-minus {
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.badge-plus {
    background-color: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}

.badge-zero {
    background-color: rgba(128, 128, 128, 0.1);
    color: grey;
}

.hover-up:hover {
    transform: translateY(-2px);
    transition: 0.3s;
}

.text-muted-custom {
    color: #6c757d;
    font-weight: 500;
}

code {
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}
</style>
<iframe id="printFrame" style="display:none;"></iframe>
<?= $this->endSection() ?>