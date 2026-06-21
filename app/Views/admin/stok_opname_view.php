<?php
/**
 * @var array $produk
 * @var object $cek_opname
 * 
 */
?>

<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="card shadow-sm border-0 rounded-4 p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0">Mode Stok Opname</h6>
            <small class="text-muted">Jika diaktifkan, kasir tidak bisa melakukan transaksi.</small>
        </div>
        <div>
            <?php if ($cek_opname->status == 1) : ?>
            <a href="<?= site_url('admin/toggle_opname/0') ?>" class="btn btn-danger rounded-pill px-4 fw-bold">
                <i class="fas fa-lock me-1"></i> Matikan (Buka Kasir)
            </a>
            <?php else : ?>
            <a href="<?= site_url('admin/toggle_opname/1') ?>" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                <i class="fas fa-lock-open me-1"></i> Aktifkan (Hold Kasir)
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php 
    // Tambahkan ini di bagian paling atas file view
    $db = \Config\Database::connect();
    $cek_opname = $db->table('sistem_kontrol')
                     ->where('nama_fitur', 'stok_opname_hold')
                     ->get()->getRow();
?>

<div class="card shadow-sm border-0 rounded-4 p-3 mb-3">
</div>


<div class="container-fluid py-5 px-4">
    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-plus-circle me-2"></i>Tambah Barang</h5>

                    <div class="mb-3">
                        <label class="small fw-bold text-muted text-uppercase">Cari Produk</label>
                        <select id="produk_id" class="form-select select2-produk">
                            <option value="" disabled selected>Pilih produk...</option>
                            <?php foreach($produk as $p): ?>
                            <option value="<?= $p['produk_id'] ?>" data-nama="<?= $p['nama_produk'] ?>"
                                data-stok="<?= $p['stok'] ?>">
                                <?= $p['nama_produk'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row mb-3 g-2">
                        <div class="col-4">
                            <label class="small fw-bold text-muted text-uppercase">Sistem</label>
                            <input type="text" id="stok_sistem" class="form-control bg-light fw-bold" readonly
                                placeholder="0">
                        </div>
                        <div class="col-4">
                            <label class="small fw-bold text-primary text-uppercase">Fisik</label>
                            <input type="number" id="stok_fisik" class="form-control fw-bold border-primary"
                                placeholder="0">
                        </div>
                        <div class="col-4">
                            <label class="small fw-bold text-muted text-uppercase">Selisih</label>
                            <input type="text" id="tampil_selisih" class="form-control bg-light fw-bold" readonly
                                placeholder="0">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-muted text-uppercase">Keterangan</label>
                        <textarea id="ket_item" class="form-control" rows="2"
                            placeholder="Contoh: Barang rusak / Hilang..."></textarea>
                    </div>

                    <button type="button" id="btn-tambah-list" class="btn btn-primary w-100 py-3 fw-bold shadow-sm"
                        style="border-radius: 12px;">
                        <i class="fas fa-cart-plus me-2"></i> Masukkan ke Daftar
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold m-0 text-dark"><i class="fas fa-list text-info me-2"></i>Draft Opname
                            Borongan</h5>
                        <button type="button" id="btn-simpan-semua" class="btn btn-success px-4 py-2 fw-bold shadow-sm"
                            style="border-radius: 10px; display: none;">
                            <i class="fas fa-save me-2"></i> Update Master Stok
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tabel-list-opname">
                            <thead>
                                <tr class="small text-uppercase fw-bold text-secondary"
                                    style="background-color: #f8fafc;">
                                    <th class="ps-3 py-3">Produk</th>
                                    <th class="text-center py-3">Sistem</th>
                                    <th class="text-center py-3">Fisik</th>
                                    <th class="text-center py-3">Selisih</th>
                                    <th class="py-3">Ket</th>
                                    <th class="text-center py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="empty-row">
                                    <td colspan="6" class="text-center py-5 text-muted small italic">Belum ada barang di
                                        dalam list.</td>
                                </tr>
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
    // Inisialisasi Select2
    $('.select2-produk').select2({
        width: '100%'
    });

    // Fungsi Hitung Selisih Otomatis
    function hitungSelisih() {
        let sistem = parseInt($('#stok_sistem').val()) || 0;
        let fisik = parseInt($('#stok_fisik').val()) || 0;
        let hasil = fisik - sistem;

        $('#tampil_selisih').val(hasil);

        // Coloring logic
        if (hasil < 0) {
            $('#tampil_selisih').addClass('text-danger').removeClass('text-success text-dark');
        } else if (hasil > 0) {
            $('#tampil_selisih').addClass('text-success').removeClass('text-danger text-dark');
        } else {
            $('#tampil_selisih').addClass('text-dark').removeClass('text-danger text-success');
        }
    }

    // Trigger saat pilih produk
    $('#produk_id').on('select2:select', function(e) {
        var data = e.params.data.element;
        $('#stok_sistem').val($(data).data('stok') || 0);
        hitungSelisih();
        $('#stok_fisik').val('').focus();
    });

    // Trigger saat input stok fisik
    $('#stok_fisik').on('input', function() {
        hitungSelisih();
    });

    // Tambah barang ke tabel list
    $('#btn-tambah-list').click(function() {
        let id = $('#produk_id').val();
        let option = $('#produk_id :selected');
        let nama = option.data('nama');
        let sistem = parseInt($('#stok_sistem').val()) || 0;
        let fisik = $('#stok_fisik').val();
        let ket = $('#ket_item').val();

        // Validasi
        if (!id || fisik === "") {
            return Swal.fire('Data Kurang', 'Pilih produk dan isi jumlah fisik dulu Gan!', 'warning');
        }

        fisik = parseInt(fisik);
        let selisih = fisik - sistem;

        // Cek Double
        if ($(`#tr-${id}`).length > 0) {
            return Swal.fire('Double Data', 'Barang ini sudah ada di list.', 'info');
        }

        $('#empty-row').hide();
        $('#btn-simpan-semua').show();

        let row = `
            <tr id="tr-${id}" class="item-opname" data-id="${id}" data-sistem="${sistem}" data-fisik="${fisik}" data-ket="${ket}">
                <td class="ps-3 fw-bold text-dark">${nama}</td>
                <td class="text-center text-secondary">${sistem}</td>
                <td class="text-center fw-bold text-dark">${fisik}</td>
                <td class="text-center">
                    <span class="badge ${selisih < 0 ? 'bg-danger' : (selisih > 0 ? 'bg-primary' : 'bg-success')}">
                        ${selisih > 0 ? '+' : ''}${selisih}
                    </span>
                </td>
                <td class="small text-muted text-truncate" style="max-width: 150px;">${ket || '-'}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm text-danger btn-hapus"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;

        $('#tabel-list-opname tbody').append(row);

        // Reset Form
        $('.select2-produk').val(null).trigger('change');
        $('#stok_sistem').val('0');
        $('#stok_fisik').val('');
        $('#tampil_selisih').val('0').removeClass('text-danger text-success');
        $('#ket_item').val('');
    });

    // Hapus baris
    $(document).on('click', '.btn-hapus', function() {
        $(this).closest('tr').remove();
        if ($('.item-opname').length === 0) {
            $('#empty-row').show();
            $('#btn-simpan-semua').hide();
        }
    });

    // Simpan Ke Master (AJAX)
    $('#btn-simpan-semua').click(function() {
        let dataOpname = [];
        $('.item-opname').each(function() {
            dataOpname.push({
                produk_id: $(this).data('id'),
                stok_sistem: $(this).data('sistem'),
                stok_fisik: $(this).data('fisik'),
                keterangan: $(this).data('ket')
            });
        });

        Swal.fire({
            title: 'Konfirmasi Simpan',
            text: `Update stok master untuk ${dataOpname.length} produk?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            confirmButtonText: 'Ya, Simpan Semua!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url("admin/proses_opname_borongan") ?>',
                    type: 'POST',
                    data: {
                        // Data opname bos
                        data: JSON.stringify(dataOpname),
                        // TAMBAHKAN INI: Token Keamanan CI4
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan!',
                                text: 'Data opname telah berhasil diperbarui.',
                                showConfirmButton: false,
                                timer: 1800,
                                timerProgressBar: true, // Ada loading bar di bawah biar keren
                                background: '#ffffff',
                                iconColor: '#28a745',
                                showClass: {
                                    popup: 'animate__animated animate__fadeInUp animate__faster' // Animasi masuk
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutDown animate__faster' // Animasi keluar
                                },
                                customClass: {
                                    popup: 'border-radius-15 shadow-lg' // Pakai custom CSS jika perlu
                                }
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Ups!',
                                text: res.message,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function(xhr) {
                        // Jika error 403 muncul lagi, akan terlihat di sini
                        console.log(xhr.responseText);
                        alert(
                            'Terjadi kesalahan keamanan (CSRF). Silakan refresh halaman.'
                        );
                    }
                });
            }
        });
    });
});

function konfirmasiStatus(status) {
    let judul = status == 1 ? 'Mulai Stok Opname?' : 'Selesai Stok Opname?';
    let teks = status == 1 ?
        'Transaksi di kasir akan DI-HOLD sementara sampai proses selesai!' :
        'Kasir akan dibuka kembali dan transaksi bisa berjalan normal.';
    let icon = status == 1 ? 'warning' : 'success';
    let warnaTombol = status == 1 ? '#d33' : '#28a745';

    Swal.fire({
        title: judul,
        text: teks,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: warnaTombol,
        cancelButtonColor: '#6e7881',
        confirmButtonText: status == 1 ? 'Ya, Aktifkan!' : 'Ya, Selesai!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Arahkan ke link toggle yang kita buat tadi
            window.location.href = "<?= site_url('admin/toggle_opname/') ?>" + status;
        }
    })
}

// Munculkan alert sukses setelah halaman refresh
<?php if (session()->getFlashdata('message')) : ?>
Swal.fire({
    title: 'Berhasil!',
    text: '<?= session()->getFlashdata("message") ?>',
    icon: 'success',
    timer: 2000,
    showConfirmButton: false
});
<?php endif; ?>
</script>

<style>
.select2-container--default .select2-selection--single {
    height: 40px !important;
    padding: 5px !important;
    border-radius: 8px !important;
    border: 1px solid #dee2e6 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
}

.table thead th {
    border: none !important;
}
</style>

<?= $this->endSection() ?>