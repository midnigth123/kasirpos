<?php

/**
 * @var array $meja
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<style>
.meja-container {
    perspective: 1000px;
}

.meja-card {
    border: none;
    border-radius: 20px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    background: #fff;
    border: 2px solid #f1f5f9;
}

/* Status: Tersedia */
.meja-available {
    border-top: 6px solid #10b981;
}

.meja-available:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.1);
}

/* Status: Terisi */
.meja-occupied {
    border-top: 6px solid #f43f5e;
    background-color: #fff1f2;
}

.meja-occupied .icon-table {
    color: #f43f5e;
}

.icon-table {
    font-size: 2.5rem;
    margin-bottom: 10px;
    transition: all 0.3s;
}

.badge-status {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}
</style>

<div class="row">
    <?php foreach ($meja as $m): ?>
    <?php
        $status = strtolower($m['status_meja']);

        if ($status === 'terisi') {
            $cardBg = 'background-color: #fff5f5;';
            $borderColor = 'border-danger';
            $badgeColor = 'bg-danger';
            $iconMeja = 'fa-users';
        } elseif ($status === 'reservasi') {
            $cardBg = 'background-color: #fff9c4;';
            $borderColor = 'border-warning';
            $badgeColor = 'bg-warning text-dark';
            $iconMeja = 'fa-calendar-check';
        } else {
            $cardBg = 'background-color: #fff;';
            $borderColor = 'border-success';
            $badgeColor = 'bg-success';
            $iconMeja = 'fa-couch';
        }
        ?>

    <div class="col-md-4 mb-3">
        <div class="card h-100 shadow-sm border-2 <?= $borderColor ?>"
            style="border-top-width: 5px; border-radius: 15px; <?= $cardBg ?>">
            <div class="card-body p-3"> <span class="badge <?= $badgeColor ?> float-end text-uppercase"
                    style="font-size: 0.6rem;"><?= $m['status_meja']; ?></span>

                <div class="my-3 text-center"
                    style="margin-left: 55px; color: <?= ($status === 'terisi') ? '#dc3545' : (($status === 'reservasi') ? '#ffc107' : '#198754') ?>;">
                    <i class="fas <?= $iconMeja ?> fa-2x"></i>
                </div>

                <div class="text-center">
                    <h2 class="fw-bold m-0 text-dark"><?= $m['nomor_meja']; ?></h2>
                    <p class="text-muted small mb-2">Meja Resto</p>
                </div>

                <div class="mt-3">
                    <?php if ($status === 'terisi'): ?>
                    <button type="button" onclick="tampilkanDetailOrder('<?= $m['nomor_meja'] ?>')"
                        class="btn btn-danger btn-sm w-100 rounded-pill mb-2 shadow-sm fw-bold">
                        <i class="fas fa-search me-1"></i> Lihat Order
                    </button>

                    <a href="<?= site_url('admin/kosongkan_meja/' . $m['id_meja']) ?>"
                        class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold"
                        style="border-style: dashed; border-width: 2px; font-size: 0.75rem;"
                        onclick="return confirm('Kosongkan meja <?= $m['nomor_meja'] ?>?')">
                        <i class="fas fa-broom me-1"></i> Kosongkan Meja
                    </a>

                    <?php elseif ($status === 'reservasi'): ?>
                    <button class="btn btn-warning btn-sm w-100 rounded-pill mb-2 shadow-sm fw-bold text-dark" disabled>
                        <i class="fas fa-user-clock me-1"></i> Telah Dibooking
                    </button>

                    <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold"
                        style="border-style: dashed; border-width: 2px; font-size: 0.75rem;"
                        onclick="konfirmasiBatal('<?= $m['id_meja'] ?>', '<?= $m['nomor_meja'] ?>')">
                        <i class="fas fa-window-close me-1"></i> Batalkan / Kosongkan
                    </a>

                    <?php else: ?>
                    <!-- <a href="<?= site_url('admin/buka_meja/' . $m['id_meja']) ?>"
                                class="btn btn-success btn-sm w-100 rounded-pill mb-2 shadow-sm fw-bold">
                                <i class="fas fa-plus me-1"></i> Buka Meja
                            </a> -->

                    <button type="button" onclick="bukaFormReservasi('<?= $m['id_meja'] ?>', '<?= $m['nomor_meja'] ?>')"
                        class="btn btn-warning btn-sm w-100 rounded-pill mb-2 shadow-sm fw-bold text-dark">
                        <i class="fas fa-calendar-alt me-1"></i> Booking Meja
                    </button>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<div class="modal fade" id="modalDetailOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Detail Pesanan Meja <span
                        id="detail_nomor_meja"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="isi_detail_order">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalReservasiAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 15px 30px rgba(0,0,0,0.1);">
            <div class="modal-header bg-warning border-0" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-calendar-check me-2"></i>Form Booking Meja (Admin)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSimpanReservasi">
                <div class="modal-body p-4">
                    <input type="hidden" id="res_id_meja" name="id_meja">

                    <div class="mb-4 text-center">
                        <label class="text-muted small text-uppercase fw-bold" style="letter-spacing: 1px;">Nomor
                            Meja</label>
                        <input type="text" id="res_nomor_meja" name="nomor_meja"
                            class="form-control-plaintext fw-bold text-center text-dark p-0"
                            style="font-size: 3.5rem; line-height: 1;" readonly>
                        <hr class="w-25 mx-auto mt-2" style="border-top: 3px solid #ffc107; opacity: 1;">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small text-muted mb-1">NAMA PELANGGAN</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i
                                    class="fas fa-user text-warning"></i></span>
                            <input type="text" id="res_nama" name="nama_pelanggan"
                                class="form-control bg-light border-0" style="border-radius: 0 10px 10px 0;"
                                placeholder="Nama lengkap..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small text-muted mb-1">NO. TELEPON / WA</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i
                                    class="fas fa-phone text-warning"></i></span>
                            <input type="number" id="res_telepon" name="telpon" class="form-control bg-light border-0"
                                style="border-radius: 0 10px 10px 0;" placeholder="08xxxx" required>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="fw-bold small text-muted mb-1">JADWAL BOOKING</label>
                            <input type="datetime-local" id="res_jam_booking" name="jam_booking"
                                class="form-control bg-light border-0 rounded-3" required>
                        </div>
                        <div class="col-6">
                            <label class="fw-bold small text-muted mb-1">JUMLAH ORANG</label>
                            <input type="number" id="res_jumlah" name="jumlah_orang"
                                class="form-control bg-light border-0 rounded-3" placeholder="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" onclick="SimpanReservasi()"
                        class="btn btn-warning w-100 fw-bold py-3 shadow-sm"
                        style="border-radius: 15px; font-size: 1rem; letter-spacing: 1px;">
                        <i class="fas fa-save me-2"></i>SIMPAN RESERVASI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Logika saat meja diklik
function handleMejaClick(id, status, nomor) {
    if (status === 'Tersedia') {
        // Arahkan ke menu transaksi dengan membawa ID Meja
        window.location.href = "<?= site_url('kasir/transaksi?meja=') ?>" + nomor;
    } else {
        // Jika terisi, mungkin buka modal rincian apa saja yang dimakan
        Swal.fire({
            title: 'Meja ' + nomor,
            text: "Meja ini sedang digunakan. Buka rincian pesanan?",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Lihat Detail',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                // Logika ke detail pesanan
            }
        });
    }
}

function bukaFormReservasi(id, nomor) {
    // A. Reset isi form dulu agar bekas ketikan sebelumnya bersih
    const form = $('#formSimpanReservasi');
    if (form.length > 0) {
        form[0].reset();
    }

    // B. BARU SUNTIK NILAINYA (Kunci biar gak ke-reset)
    $('#res_id_meja').val(id);
    $('#res_nomor_meja').val(nomor); // JEDOR! Mengisi nilai ke input id="res_nomor_meja"

    // C. Tampilkan modalnya
    $('#modalReservasiAdmin').modal('show');
}

// 2. FUNGSI UNTUK SIMPAN DATA (DIKLIk DARI TOMBOL SIMPAN RESERVASI)
function SimpanReservasi() {
    // Validasi manual form input text yang required
    if ($('#res_nama').val() == '' || $('#res_telepon').val() == '') {
        Swal.fire('Peringatan!', 'Nama Pelanggan dan No. Telepon wajib diisi!', 'warning');
        return false;
    }

    Swal.fire({
        title: 'Sedang menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    // 1. Ambil nama token dan nilai token CSRF aktif dari browser
    let csrfName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    // 2. Gabungkan data form dengan token CSRF
    let formData = $('#formSimpanReservasi').serializeArray();
    formData.push({
        name: csrfName,
        value: csrfHash
    }); // JEDOR! Menyusupkan token keamanan

    $.ajax({
        url: "<?= site_url('admin/simpan_reservasi') ?>",
        method: "POST",
        data: formData, // Kirim data yang sudah legal dan aman
        dataType: "json",
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Gagal!', res.message, 'error');
            }
        },
        error: function(xhr) {
            // Jika gagal, kita bisa intip errornya di sini
            Swal.fire('Error!', 'Terjadi kesalahan: ' + xhr.statusText, 'error');
        }
    });
}

// Proses Simpan lewat AJAX
$('#formSimpanReservasi').on('submit', function(e) {
    e.preventDefault();

    // Tampilkan loading sebentar
    Swal.fire({
        title: 'Sedang menyimpan...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        url: "<?= site_url('admin/simpan_reservasi') ?>",
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // Refresh halaman biar meja jadi kuning (Reservasi)
                });
            } else {
                Swal.fire('Gagal!', res.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Terjadi kesalahan sistem saat menyimpan.', 'error');
        }
    });
});


$('#formSimpanReservasi').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: "<?= site_url('admin/simpan_reservasi') ?>", // Arahkan ke method simpan di Admin
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire('Berhasil!', res.message, 'success').then(() => {
                    location.reload(); // Refresh biar meja jadi Kuning
                });
            } else {
                Swal.fire('Gagal!', res.message, 'error');
            }
        }
    });
});

function tampilkanDetailOrder(nomor_meja) {
    $('#detail_nomor_meja').text(nomor_meja);
    $('#modalDetailOrder').modal('show');
    $('#isi_detail_order').html(
        '<div class="text-center p-3"><i class="fas fa-spinner fa-spin fa-2x text-danger"></i></div>');

    $.ajax({
        url: "<?= site_url('kasir/get_order_by_meja/') ?>" + nomor_meja,
        method: "GET",
        dataType: "json",
        success: function(res) {
            if (res && res.length > 0) {
                let html = `<table class="table table-sm table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Menu</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>`;
                let total = 0;
                $.each(res, function(i, item) {
                    html += `<tr>
                        <td>${item.nama_produk}</td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-end">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
                    </tr>`;
                    total += parseInt(item.subtotal);
                });
                html += `</tbody>
                    <tfoot class="fw-bold bg-light">
                        <tr>
                            <td colspan="2" class="text-center">TOTAL</td>
                            <td class="text-end text-danger">Rp ${total.toLocaleString('id-ID')}</td>
                        </tr>
                    </tfoot>
                </table>`;
                $('#isi_detail_order').html(html);
            } else {
                $('#isi_detail_order').html(
                    '<div class="alert alert-warning text-center">Data pesanan tidak ditemukan.</div>');
            }
        }
    });
}

function confirmKosongkanMeja(id, nomor) {
    Swal.fire({
        title: 'Kosongkan Meja ' + nomor + '?',
        text: "Pastikan pelanggan sudah bayar dan meja sudah bersih.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kosongkan!',
        cancelButtonText: 'Batal',
        borderRadius: '15px'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "<?= site_url('admin/kosongkan_meja/') ?>" + id;
        }
    });
}

function konfirmasiBatal(id, nomor) {
    Swal.fire({
        title: 'Batalkan Reservasi?',
        text: "Meja " + nomor + " akan dikosongkan dan statusnya kembali 'Tersedia'.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33', // Warna merah untuk aksi hapus/batal
        cancelButtonColor: '#6e7881',
        confirmButtonText: 'Ya, Kosongkan!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        background: '#ffffff',
        borderRadius: '20px',
        customClass: {
            popup: 'glass-modal-style' // Jika bos mau tambah custom CSS
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // JEDOR! Arahkan ke rute controller untuk update status meja
            window.location.href = "<?= base_url('admin/batal_reservasi/') ?>/" + id;
        }
    });
}

function handleMejaClick(id, status, nomor) {
    if (status === 'Tersedia') {
        window.location.href = "<?= site_url('kasir/transaksi?meja=') ?>" + nomor;
    } else {
        // Logika lihat order bisa diarahkan ke list transaksi meja tersebut
        Swal.fire('Info', 'Meja ' + nomor + ' sedang digunakan.', 'info');
    }
}
// Auto Refresh setiap 10 detik agar status meja update otomatis
setInterval(function() {
    $('#container-monitoring-meja').load(window.location.href + ' #container-monitoring-meja > *');
}, 5000);
</script>

<?= $this->endSection() ?>