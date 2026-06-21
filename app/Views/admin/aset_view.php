<?php
/**
 * @var array $aset
 */
?>
<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Manajemen Aset</h3>
            <p class="text-muted small mb-0">Kelola dan pantau penyusutan inventaris.</p>
        </div>
        <button class="btn btn-primary rounded-3 shadow-sm px-4 fw-bold" data-bs-toggle="modal"
            data-bs-target="#modalTambahAset">
            <i class="fas fa-plus me-2"></i> Tambah Aset
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchAset" class="form-control bg-light border-0"
                            placeholder="Cari berdasarkan nama aset atau kode aset...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="filterKategori" class="form-select bg-light border-0 text-muted">
                        <option value="">Semua Kategori</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Mesin">Mesin</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Kendaraan">Kendaraan</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted fw-bold d-block mb-1 text-uppercase">Total Aset</small>
                <h4 class="fw-bold mb-0 text-primary"><?= count($aset); ?> <small
                        class="fs-6 fw-normal text-muted">Item</small></h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted fw-bold d-block mb-1 text-uppercase">Total Nilai Buku</small>
                <h4 class="fw-bold mb-0 text-success">
                    <?php 
                        $total_nb = array_sum(array_column($aset, 'nilai_buku'));
                        echo "Rp " . number_format($total_nb, 0, ',', '.');
                    ?>
                </h4>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0 small fw-bold text-uppercase">Info Aset</th>
                            <th class="py-3 border-0 small fw-bold text-uppercase">Kategori</th>
                            <th class="py-3 border-0 small fw-bold text-uppercase">Tgl Beli</th>
                            <th class="py-3 border-0 small fw-bold text-end text-uppercase">Harga Beli</th>
                            <th class="py-3 border-0 small fw-bold text-end text-uppercase">Nilai Buku</th>
                            <th class="py-3 border-0 small fw-bold text-center text-uppercase">Kondisi</th>
                            <th class="pe-4 py-3 border-0 small fw-bold text-center text-uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="bodyTabelAset">
                        <?php foreach ($aset as $row) : 
                            $persen_sisa = ($row['harga_beli'] > 0) ? ($row['nilai_buku'] / $row['harga_beli']) * 100 : 0;
                            $warna_nb = ($persen_sisa < 30) ? 'text-danger fw-bold' : 'text-dark fw-bold';
                        ?>
                        <tr class="baris-aset">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center me-3 border"
                                        style="width: 45px; height: 45px; overflow: hidden;">
                                        <?php if($row['foto_aset']): ?>
                                        <img src="<?= base_url('assets/uploads/aset/'.$row['foto_aset']); ?>"
                                            style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                        <i class="fas fa-box text-muted opacity-50"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold mb-0 nama-aset"><?= esc($row['nama_aset']); ?></div>
                                        <small class="text-muted kode-aset"><?= esc($row['kode_aset']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="kat-aset"><span
                                    class="badge bg-light text-dark border fw-normal"><?= $row['kategori']; ?></span>
                            </td>
                            <td class="small"><?= date('d/m/Y', strtotime($row['tgl_beli'])); ?></td>
                            <td class="text-end fw-medium">Rp <?= number_format($row['harga_beli'], 0, ',', '.'); ?>
                            </td>
                            <td class="text-end <?= $warna_nb; ?>">
                                Rp <?= number_format($row['nilai_buku'], 0, ',', '.'); ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                    $bg = 'bg-success';
                                    if($row['kondisi'] == 'Rusak Berat') $bg = 'bg-danger';
                                    if(in_array($row['kondisi'], ['Diservis', 'Rusak Ringan'])) $bg = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?= $bg; ?> rounded-pill px-3" style="font-size: 10px;">
                                    <?= strtoupper($row['kondisi']); ?>
                                </span>
                            </td>
                            <td class="pe-4 text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" class="btn btn-sm btn-light border btn-view-aset"
                                        data-aset='<?= json_encode($row); ?>' title="Detail">
                                        <i class="fas fa-eye text-primary"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light border btn-edit-aset"
                                        data-aset='<?= json_encode($row) ?>' title="Edit">
                                        <i class="fas fa-edit text-warning"></i>
                                    </button>
                                    <a href="<?= base_url('admin/hapus_aset/'.$row['id_aset']); ?>"
                                        class="btn btn-sm btn-light border btn-hapus" title="Hapus">
                                        <i class="fas fa-trash text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <tr id="noData" style="display: none;">
                            <td colspan="7" class="text-center py-5 text-muted">Aset tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="fw-bold mb-0 text-primary">Daftarkan Aset Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/simpan_aset'); ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">KODE ASET</label>
                            <input type="text" name="kode_aset" class="form-control bg-light border-0"
                                placeholder="AST-2026-xxx" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NAMA ASET</label>
                            <input type="text" name="nama_aset" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">KATEGORI</label>
                            <select name="kategori" class="form-select bg-light border-0">
                                <option value="Elektronik">Elektronik</option>
                                <option value="Mesin">Mesin</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Kendaraan">Kendaraan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">TANGGAL BELI</label>
                            <input type="date" name="tgl_beli" class="form-control bg-light border-0"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">LOKASI</label>
                            <input type="text" name="lokasi" class="form-control bg-light border-0"
                                value="KasiKita Main">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">HARGA BELI (RP)</label>
                            <input type="text" class="form-control bg-light border-0 format-rupiah" placeholder="0"
                                required>
                            <input type="hidden" name="harga_beli">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">UMUR (BULAN)</label>
                            <input type="number" name="umur_ekonomis" class="form-control bg-light border-0" value="60">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">NILAI SISA (RP)</label>
                            <input type="text" class="form-control bg-light border-0 format-rupiah" placeholder="0">
                            <input type="hidden" name="nilai_sisa">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">FOTO ASET</label>
                            <input type="file" name="foto_aset" class="form-control bg-light border-0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4 fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold">Simpan Aset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalViewAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="fw-bold mb-0 text-primary">Detail Aset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div id="v-foto-container"
                    class="mb-3 mx-auto rounded-4 shadow-sm border overflow-hidden d-flex align-items-center justify-content-center"
                    style="width: 150px; height: 150px; background: #f8f9fa;">
                    <img id="v-foto" src="" class="d-none" style="width: 100%; height: 100%; object-fit: cover;">
                    <i id="v-icon" class="fas fa-box fa-3x text-muted opacity-25"></i>
                </div>
                <h4 class="fw-bold mb-1" id="v-nama"></h4>
                <span class="badge bg-light text-dark border mb-4" id="v-kode"></span>
                <div class="row g-3 text-start">
                    <div class="col-6"><small class="text-muted d-block small">KATEGORI</small><span class="fw-bold"
                            id="v-kategori"></span></div>
                    <div class="col-6"><small class="text-muted d-block small">TANGGAL BELI</small><span class="fw-bold"
                            id="v-tgl"></span></div>
                    <div class="col-6 border-top pt-2"><small class="text-muted d-block small">HARGA BELI</small><span
                            class="fw-bold text-success" id="v-harga"></span></div>
                    <div class="col-6 border-top pt-2"><small class="text-muted d-block small">NILAI BUKU</small><span
                            class="fw-bold text-primary" id="v-nb"></span></div>
                    <div class="col-12 border-top pt-2"><small class="text-muted d-block small">LOKASI:</small><span
                            class="fw-bold" id="v-lokasi"></span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="fw-bold mb-0 text-warning">Edit Data Aset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/update_aset'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_aset" id="edit-id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label small fw-bold">KODE ASET</label><input
                                type="text" name="kode_aset" id="edit-kode" class="form-control bg-light border-0"
                                readonly></div>
                        <div class="col-md-6"><label class="form-label small fw-bold">NAMA ASET</label><input
                                type="text" name="nama_aset" id="edit-nama" class="form-control bg-light border-0"
                                required></div>
                        <div class="col-md-4"><label class="form-label small fw-bold">KATEGORI</label><select
                                name="kategori" id="edit-kategori" class="form-select bg-light border-0">
                                <option value="Elektronik">Elektronik</option>
                                <option value="Mesin">Mesin</option>
                                <option value="Furniture">Furniture</option>
                                <option value="Kendaraan">Kendaraan</option>
                            </select></div>
                        <div class="col-md-4"><label class="form-label small fw-bold">TANGGAL BELI</label><input
                                type="date" name="tgl_beli" id="edit-tgl" class="form-control bg-light border-0"
                                required></div>
                        <div class="col-md-4"><label class="form-label small fw-bold">LOKASI</label><input type="text"
                                name="lokasi" id="edit-lokasi" class="form-control bg-light border-0"></div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">HARGA BELI (RP)</label>
                            <input type="text" id="edit-input-harga"
                                class="form-control bg-light border-0 format-rupiah" required>
                            <input type="hidden" name="harga_beli" id="edit-harga-asli">
                        </div>
                        <div class="col-md-3"><label class="form-label small fw-bold">UMUR</label><input type="number"
                                name="umur_ekonomis" id="edit-umur" class="form-control bg-light border-0"></div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">NILAI SISA</label>
                            <input type="text" id="edit-input-sisa"
                                class="form-control bg-light border-0 format-rupiah">
                            <input type="hidden" name="nilai_sisa" id="edit-sisa-asli">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">KONDISI</label>
                            <select name="kondisi" id="edit-kondisi" class="form-select bg-light border-0">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                                <option value="Diservis">Diservis</option>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label small fw-bold">FOTO ASET</label><input
                                type="file" name="foto_aset" class="form-control bg-light border-0"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-4 fw-bold"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-3 px-4 fw-bold">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 1. LIVE SEARCH
    function filterTable() {
        let keyword = $('#searchAset').val().toLowerCase();
        let kategori = $('#filterKategori').val().toLowerCase();
        let visibleCount = 0;
        $('.baris-aset').each(function() {
            let nama = $(this).find('.nama-aset').text().toLowerCase();
            let kode = $(this).find('.kode-aset').text().toLowerCase();
            let kat = $(this).find('.kat-aset').text().toLowerCase();
            if ((nama.includes(keyword) || kode.includes(keyword)) && (kategori === "" || kat.includes(
                    kategori))) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        $('#noData').toggle(visibleCount === 0);
    }
    $('#searchAset, #filterKategori').on('keyup change', filterTable);

    // 2. FORMAT RUPIAH
    $(document).on('keyup', '.format-rupiah', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        $(this).next('input[type="hidden"]').val(val);
        $(this).val(val ? new Intl.NumberFormat('id-ID').format(val) : '');
    });

    // 3. MODAL VIEW
    $(document).on('click', '.btn-view-aset', function() {
        const data = $(this).data('aset');
        const rp = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        });
        $('#v-nama').text(data.nama_aset);
        $('#v-kode').text(data.kode_aset);
        $('#v-kategori').text(data.kategori);
        $('#v-lokasi').text(data.lokasi || '-');
        $('#v-tgl').text(new Date(data.tgl_beli).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }));
        $('#v-harga').text(rp.format(data.harga_beli));
        $('#v-nb').text(rp.format(data.nilai_buku));
        if (data.foto_aset) {
            $('#v-foto').attr('src', '<?= base_url('assets/uploads/aset/'); ?>/' + data.foto_aset)
                .removeClass('d-none');
            $('#v-icon').addClass('d-none');
        } else {
            $('#v-foto').addClass('d-none');
            $('#v-icon').removeClass('d-none');
        }
        $('#modalViewAset').modal('show');
    });

    // 4. MODAL EDIT
    $(document).on('click', '.btn-edit-aset', function() {
        const data = $(this).data('aset');
        $('#edit-id').val(data.id_aset);
        $('#edit-kode').val(data.kode_aset);
        $('#edit-nama').val(data.nama_aset);
        $('#edit-kategori').val(data.kategori);
        $('#edit-tgl').val(data.tgl_beli);
        $('#edit-lokasi').val(data.lokasi);
        $('#edit-umur').val(data.umur_ekonomis);
        $('#edit-kondisi').val(data.kondisi);
        $('#edit-harga-asli').val(data.harga_beli);
        $('#edit-sisa-asli').val(data.nilai_sisa);
        $('#edit-input-harga').val(new Intl.NumberFormat('id-ID').format(data.harga_beli));
        $('#edit-input-sisa').val(new Intl.NumberFormat('id-ID').format(data.nilai_sisa));
        $('#modalEditAset').modal('show');
    });

    // 5. DELETE ALERT
    $(document).on('click', '.btn-hapus', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: "Hapus Aset?",
            text: "Data hilang permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus!"
        }).then((res) => {
            if (res.isConfirmed) window.location.href = href;
        });
    });

    // 6. FLASH DATA
    <?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= session()->getFlashdata('success'); ?>',
        showConfirmButton: false,
        timer: 2000
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>