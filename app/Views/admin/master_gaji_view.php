<?php
/**
 * @var string $bulan
 * @var array $pegawai_list
 * @var array $gaji_pegawai
 * 
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-money-bill-wave me-2"></i>Pengaturan Gaji
                        Pegawai</h6>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal"
                        data-bs-target="#modalGaji">
                        <i class="fas fa-plus me-1"></i> Atur Gaji Baru
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tableMasterGaji">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Nama Pegawai</th>
                                    <th class="border-0">Gaji / Shift</th>
                                    <th class="border-0">Tunjangan Jabatan</th>
                                    <th class="border-0">Potongan Telat</th>
                                    <th class="border-0 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gaji_pegawai as $g) : ?>
                                <tr>
                                    <td class="fw-bold"><?= $g['nama_user'] ?></td>
                                    <td>Rp <?= number_format($g['nominal_per_shift'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($g['tunjangan_jabatan'], 0, ',', '.') ?></td>
                                    <td class="text-danger">Rp <?= number_format($g['potongan_telat'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-info btn-sm rounded-circle edit-gaji"
                                            data-id="<?= $g['id_master_gaji'] ?>" data-user="<?= $g['id_user'] ?>"
                                            data-nominal="<?= $g['nominal_per_shift'] ?>"
                                            data-tunjangan="<?= $g['tunjangan_jabatan'] ?>"
                                            data-potongan="<?= $g['potongan_telat'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('admin/hapus_gaji/' . $g['id_master_gaji']) ?>"
                                            class="btn btn-outline-danger btn-sm rounded-circle btn-hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGaji" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <form action="<?= base_url('admin/simpan_gaji') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id_master_gaji" id="id_master_gaji">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Form Pengaturan Gaji</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small fw-bold">Pilih Pegawai</label>
                        <select name="id_user" id="id_user" class="form-select shadow-none" required>
                            <option value="">-- Pilih Pegawai --</option>
                            <?php foreach ($pegawai_list as $p) : ?>
                            <option value="<?= $p['id_user'] ?>"><?= $p['nama_user'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Nominal Gaji / Shift</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="nominal_per_shift" id="nominal_per_shift"
                                class="form-control rupiah" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Tunjangan Jabatan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="tunjangan_jabatan" id="tunjangan_jabatan"
                                    class="form-control rupiah" value="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small fw-bold">Denda Telat</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="potongan_telat" id="potongan_telat" class="form-control rupiah"
                                    value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Fungsi Edit: Masukkan data ke modal
    $('.edit-gaji').on('click', function() {
        $('#id_master_gaji').val($(this).data('id'));
        $('#id_user').val($(this).data('user'));
        $('#nominal_per_shift').val($(this).data('nominal'));
        $('#tunjangan_jabatan').val($(this).data('tunjangan'));
        $('#potongan_telat').val($(this).data('potongan'));

        $('.modal-title').text('Edit Pengaturan Gaji');
        $('#modalGaji').modal('show');
    });

    // Reset modal kalau klik tambah baru
    $('#modalGaji').on('hidden.bs.modal', function() {
        $('#id_master_gaji').val('');
        $('.modal-title').text('Form Pengaturan Gaji');
        $('#formGaji')[0].reset();
    });
});

function formatRupiah(angka, prefix) {
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}

// Jalankan fungsi setiap kali user mengetik di input dengan class .rupiah
$('.rupiah').on('keyup', function() {
    $(this).val(formatRupiah($(this).val()));
});
$(document).ready(function() {
    // Tangkap flashdata success dari Controller
    <?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        title: 'Berhasil!',
        text: '<?= session()->getFlashdata('success') ?>',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false,
        border: 'none',
        border_radius: '15px'
    });
    <?php endif; ?>

    // Alert konfirmasi HAPUS (Opsional tapi keren)
    $('.btn-hapus').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data gaji ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.location.href = href;
            }
        });
    });
});
</script>
<?= $this->endSection() ?>