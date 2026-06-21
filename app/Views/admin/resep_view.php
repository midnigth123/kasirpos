<?php
/**
 * @var array $resep
 * @var array $menu
 * @var array $bahan
 * 
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid py-4" style="background: #f8f9fc; min-height: 100vh;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="font-weight-bold text-dark mb-1">Manajemen Resep</h4>
            <p class="text-muted small mb-0">Atur komposisi bahan baku untuk menu racikan Anda.</p>
        </div>
        <div class="badge badge-primary-soft px-3 py-2 text-primary font-weight-bold shadow-sm">
            <i class="fas fa-layer-group mr-2"></i><?= count($resep) ?> Menu Terdaftar
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 radius-15 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-shape bg-primary-light text-primary mr-3">
                            <i class="fas fa-blender"></i>
                        </div>
                        <h6 class="mb-0 font-weight-bold">Racik Komposisi</h6>
                    </div>

                    <form action="<?= base_url('admin/simpan_resep') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-group mb-3">
                            <label class="text-xs font-weight-bold text-uppercase tracking-wider text-muted">Produk Jual
                                (Menu)</label>
                            <select name="id_produk_jual" class="form-control custom-select-lg shadow-none" required>
                                <option value="">Pilih Menu...</option>
                                <?php foreach($menu as $m): ?>
                                <option value="<?= $m['produk_id'] ?>"><?= $m['nama_produk'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-xs font-weight-bold text-uppercase tracking-wider text-muted">Bahan
                                Baku</label>
                            <select name="id_bahan_baku" class="form-control custom-select-lg shadow-none" required>
                                <option value="">Pilih Bahan...</option>
                                <?php foreach($bahan as $b): ?>
                                <option value="<?= $b['produk_id'] ?>"><?= $b['nama_produk'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-xs font-weight-bold text-uppercase tracking-wider text-muted">Takaran
                                Kebutuhan</label>
                            <div class="input-group input-group-lg">
                                <input type="number" step="0.01" name="jumlah_kebutuhan"
                                    class="form-control border-right-0 shadow-none" placeholder="0.00" required>
                                <div class="input-group-append">
                                    <span
                                        class="input-group-text bg-white text-muted border-left-0 small font-weight-bold">Unit</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-primary hover-lift">
                            <i class="fas fa-plus-circle mr-2"></i>Simpan Bahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 radius-15 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold text-dark">Daftar Komposisi</h6>
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0"><i
                                    class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" id="searchInput" class="form-control border-left-0 bg-light shadow-none"
                            placeholder="Cari nama menu...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-align-middle mb-0" id="resepTable">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-muted border-0">Menu
                                    Jual</th>
                                <th class="px-4 py-3 text-xs font-weight-bold text-uppercase text-muted border-0">Detail
                                    Komposisi</th>
                                <th
                                    class="px-4 py-3 text-xs font-weight-bold text-uppercase text-muted border-0 text-center">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($resep)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/gray/coffee-break.svg"
                                        style="width: 140px;" class="mb-3 opacity-5">
                                    <p class="text-muted font-italic">Belum ada resep yang dikonfigurasi.</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach($resep as $r): ?>
                            <tr class="hover-bg-light transition-3s">
                                <td class="px-4 py-4 align-top">
                                    <div class="d-flex align-items-center mt-2">
                                        <div
                                            class="avatar-md bg-primary-soft text-primary mr-3 font-weight-bold shadow-xs">
                                            <?= strtoupper(substr($r['nama_menu'], 0, 1)) ?>
                                        </div>
                                        <span
                                            class="h6 mb-0 font-weight-bold text-dark menu-name"><?= esc($r['nama_menu']) ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="recipe-container p-3 bg-white shadow-xs radius-15">
                                        <div class="row align-items-center no-gutters">
                                            <div class="col-7">
                                                <div class="list-bahan-text text-dark font-weight-500">
                                                    <?= $r['list_bahan'] ?>
                                                </div>
                                            </div>
                                            <div class="col-5 text-right">
                                                <?php 
                                                    // 1. Bersihkan data dari tag <br> yang mungkin ikut dari database
                                                    $clean_qty = str_replace(['<br>', '<br />', '<br/>'], ',', $r['list_qty']);
                                                    
                                                    // 2. Pecah berdasarkan koma
                                                    $qtys = explode(",", $clean_qty); 
                                                    
                                                    foreach($qtys as $q):
                                                        $q = trim($q); // Hilangkan spasi liar
                                                        if($q === "") continue; 
                                                ?>
                                                <div class="mb-1">
                                                    <span class="badge-qty shadow-sm">
                                                        <span class="qty-label">QTY</span> <?= $q ?>
                                                    </span>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center align-top">
                                    <div class="mt-2">
                                        <a href="<?= base_url('admin/hapus_resep/' . $r['id_produk_jual']) ?>"
                                            class="btn btn-icon btn-outline-danger shadow-sm hover-lift rounded-circle"
                                            onclick="konfirmasiHapus(event, this.href)" data-toggle="tooltip"
                                            title="Hapus Seluruh Resep <?= esc($r['nama_menu']) ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
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
</div>

<style>
/* Utility Classes */
.radius-15 {
    border-radius: 15px !important;
}

.transition-3s {
    transition: all 0.3s ease;
}

.text-xs {
    font-size: 0.75rem;
}

.tracking-wider {
    letter-spacing: 0.05em;
}

.font-weight-500 {
    font-weight: 500;
}

/* Backgrounds & Soft Tones */
.bg-primary-light {
    background-color: rgba(78, 115, 223, 0.1);
}

.bg-primary-soft {
    background-color: #e8efff;
    color: #4e73df;
}

.badge-primary-soft {
    background-color: #e8efff;
    border: 1px solid #d1dfff;
}

/* Box Styling */
.recipe-container {
    border: 1px solid #e3e6f0;
    border-left: 5px solid #4e73df !important;
    background: #ffffff;
}

/* List Text Enlarged */
.list-bahan-text {
    font-size: 0.95rem;
    line-height: 2;
    color: #2e3b4e;
}

/* Badge Qty Styling */
.badge-qty {
    background-color: #f8f9fc;
    color: #333;
    border: 1px solid #d1dfff;
    padding: 0.4rem 0.6rem;
    font-size: 0.85rem;
    font-weight: 700;
    border-radius: 8px;
    display: inline-block;
    min-width: 75px;
    text-align: center;
}

.qty-label {
    font-size: 0.6rem;
    color: #4e73df;
    margin-right: 4px;
    letter-spacing: 1px;
}

/* Avatar & Icon Shape */
.icon-shape {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.avatar-md {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    font-size: 1.1rem;
}

/* Form Styling */
.custom-select-lg {
    height: calc(1.5em + 1.2rem + 2px);
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    border-radius: 12px;
    border: 1px solid #e3e6f0;
}

.form-control:focus {
    border-color: #bac8f3;
    box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.05) !important;
}

/* Buttons */
.btn-lg {
    border-radius: 12px;
    padding: 0.8rem;
    font-size: 1rem;
    font-weight: 600;
}

.shadow-primary {
    box-shadow: 0 4px 15px rgba(78, 115, 223, 0.25);
}

.hover-lift:hover {
    transform: translateY(-3px);
    transition: 0.2s;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.btn-icon {
    width: 45px;
    height: 45px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

/* Tables */
.hover-bg-light:hover {
    background-color: #fbfcfe;
}

.shadow-xs {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
}

.align-top {
    vertical-align: top !important;
}
</style>

<script>
// Pindahkan fungsi konfirmasi ke luar agar bisa diakses oleh onclick HTML
function konfirmasiHapus(event, url) {
    event.preventDefault();
    Swal.fire({
        title: 'Hapus Resep?',
        text: "Seluruh bahan penyusun menu ini akan dihapus.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6e7881',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        borderRadius: '1.25rem'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

$(document).ready(function() {
    // Aktifkan Tooltip
    $('[data-toggle="tooltip"]').tooltip();

    // LIVE SEARCH OPTIMIZED
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase().trim(); // Tambahkan trim()

        $("#resepTable tbody tr").each(function() {
            var row = $(this);
            var menuName = row.find(".menu-name").text().toLowerCase();

            // JEDOR! Cek apakah baris bukan 'noResults' sebelum toggle
            if (row.attr('id') !== 'noResults') {
                row.toggle(menuName.indexOf(value) > -1);
            }
        });

        // Toggle No Results Message
        var visibleRows = $("#resepTable tbody tr:visible").not('#noResults').length;
        if (visibleRows === 0) {
            if ($("#noResults").length === 0) {
                $("#resepTable tbody").append(
                    '<tr id="noResults"><td colspan="3" class="text-center py-5 text-muted">' +
                    '<i class="fas fa-search fa-2x mb-3 d-block opacity-25"></i>' +
                    'Menu "' + $(this).val() + '" tidak ditemukan...</td></tr>'
                );
            }
        } else {
            $("#noResults").remove();
        }
    });
});
</script>
<?= $this->endSection() ?>