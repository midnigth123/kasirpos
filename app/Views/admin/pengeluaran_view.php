<?php
/**
 * @var string $tanggal_pilihan
 * 
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>
<style>
/* --- 1. CONTAINER UTAMA (GLASS CARDS) --- */
.glass-card {
    background: rgba(15, 15, 15, 0.45);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 25px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    padding: 30px;
    color: #ffffff;
    animation: fadeInScale 0.8s ease-out;
}

/* --- 2. LIGHT MODE OVERRIDE --- */
[data-bs-theme="light"] .glass-card {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #dee2e6;
    color: #222 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

[data-bs-theme="light"] .glass-card h3 {
    color: #046b5d !important;
}

/* --- 3. FORM ELEMENTS (INPUTS) --- */
.form-custom {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    color: #fff !important;
    border-radius: 12px;
    padding: 12px 15px;
    transition: all 0.3s;
}

.form-custom:focus {
    background: rgba(255, 255, 255, 0.15) !important;
    border-color: #12f0d2 !important;
    box-shadow: 0 0 15px rgba(18, 240, 210, 0.2) !important;
}

/* Light Mode Input Fix */
[data-bs-theme="light"] .form-custom {
    background: #f8f9fa !important;
    border: 1px solid #ced4da !important;
    color: #333 !important;
}

/* --- 4. TABEL STYLE (SENJA TABLE) --- */
.table-senja thead th {
    background: rgba(18, 240, 210, 0.1) !important;
    color: #12f0d2;
    border: none;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 1px;
    padding: 15px;
}

[data-bs-theme="light"] .table-senja thead th {
    background: #e9ecef !important;
    color: #046b5d;
}

.table-senja tbody td {
    padding: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

/* --- 5. MODAL GLASSMORPHISM (FIX UNTUK EDIT) --- */
.glass-modal {
    background: rgba(20, 20, 20, 0.8) !important;
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    border-radius: 25px;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

[data-bs-theme="light"] .glass-modal {
    background: #ffffff !important;
    color: #222 !important;
}

/* --- 6. BUTTONS --- */
.btn-simpan {
    background: #12f0d2;
    color: #040a04;
    font-weight: 800;
    border-radius: 12px;
    border: none;
    padding: 12px 20px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-simpan:hover {
    background: #fff;
    color: #040a04;
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(18, 240, 210, 0.4);
}

/* --- 7. ANIMASI --- */
@keyframes fadeInScale {
    0% {
        opacity: 0;
        transform: scale(0.95);
    }

    100% {
        opacity: 1;
        transform: scale(1);
    }
}

/* Scrollbar tipis untuk Table Responsive */
.table-responsive::-webkit-scrollbar {
    height: 5px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: rgba(18, 240, 210, 0.3);
    border-radius: 10px;
}
</style>
<div class="container-fluid mt-4">
    <div class="glass-card">
        <h3 class="fw-bold mb-4">
            <i class="fas fa-cash-register me-2" style="color: #12f0d2;"></i>
            Pengeluaran Harian (Petty Cash)
        </h3>
        <div class="row mb-4 align-items-end">
            <div class="col-md-4">
                <form action="<?= base_url('admin/pengeluaran') ?>" method="GET" id="formFilter">
                    <label class="form-label small text-muted">Filter Berdasarkan Tanggal</label>
                    <div class="input-group">
                        <input type="date" name="filter_tgl" class="form-control form-custom shadow-none"
                            value="<?= $tanggal_pilihan ?>"> <button type="submit" class="btn-simpan py-2 px-3">
                            <i class="fas fa-filter"></i>
                        </button>
                        <?php if($tanggal_pilihan != date('Y-m-d')): ?>
                        <a href="<?= base_url('admin/pengeluaran') ?>" class="btn btn-outline-light ms-2 py-2"
                            style="border-radius: 12px;">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <form action="<?= base_url('admin/simpan_pengeluaran'); ?>" method="POST" id="formSimpan">
            <?= csrf_field(); ?>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small">Keperluan</label>
                    <input type="text" name="keperluan" class="form-control form-custom shadow-none"
                        placeholder="Contoh: Beli Gas LPG / Es Batu" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Qty</label>
                    <input type="number" name="qty" class="form-control form-custom shadow-none" value="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Total Harga (Rp)</label>
                    <input type="text" name="total" class="form-control form-custom shadow-none input-rupiah" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn-simpan w-100">SIMPAN</button>
                </div>
            </div>
        </form>

        <hr class="border-white opacity-25 mb-4">

        <div class="table-responsive">
            <table class="table table-senja align-middle">
                <thead>
                    <tr>
                        <th>Tanggal & Jam</th>
                        <th>Oleh</th>
                        <th>Keperluan</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Total Bayar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($pengeluaran)): ?>
                    <?php foreach($pengeluaran as $p): ?>
                    <tr>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            <?= date('d/m/Y', strtotime($p['created_at'])) ?><br>
                            <i class="far fa-clock me-1 text-info"></i> <?= date('H:i', strtotime($p['created_at'])) ?>
                            WIB
                        </td>

                        <td><span class="badge bg-dark"><?= $p['nama_user'] ?></span></td>

                        <td>
                            <?= $p['nama_keperluan'] ?>
                            <?php if(!empty($p['catatan'])): ?>
                            <br><small class="text-muted font-italic">Note: <?= $p['catatan'] ?></small>
                            <?php endif; ?>
                        </td>

                        <td class="text-center"><?= $p['jumlah'] ?></td>

                        <td class="text-end fw-bold text-info">
                            Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?>
                        </td>

                        <td class="text-center">
                            <button type="button" class="btn btn-sm text-info border-0"
                                onclick="editData('<?= $p['id_pengeluaran'] ?>', '<?= addslashes($p['nama_keperluan']) ?>', '<?= $p['jumlah'] ?>', '<?= $p['total_bayar'] ?>')">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button type="button" class="btn btn-sm text-danger border-0"
                                onclick="hapusData('<?= base_url('admin/hapus_pengeluaran/'.$p['id_pengeluaran']) ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 opacity-50">
                            <i class="fas fa-info-circle me-1"></i> Belum ada pengeluaran hari ini.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-white">Edit Pengeluaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="<?= base_url('admin/update_pengeluaran'); ?>" method="POST" id="formUpdate">
                <?= csrf_field(); ?>
                <input type="hidden" name="id_pengeluaran_harian" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-white">Keperluan</label>
                        <input type="text" name="keterangan" id="edit_keperluan"
                            class="form-control form-custom shadow-none" required>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label small text-white">Qty</label>
                            <input type="number" name="qty_barang" id="edit_jumlah"
                                class="form-control form-custom shadow-none" required>
                        </div>
                        <div class="col-8">
                            <label class="form-label small text-white">Total Harga (Rp)</label>
                            <input type="text" name="jumlah" id="edit_total"
                                class="form-control form-custom shadow-none input-rupiah" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm text-white" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-simpan">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function formatRupiah(angka) {
    if (!angka) return '';
    let number_string = angka.toString().replace(/[^0-9]/g, '');
    let split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return rupiah;
}

document.querySelectorAll('.input-rupiah').forEach(input => {
    input.addEventListener('keyup', function() {
        this.value = formatRupiah(this.value);
    });
});

// JEDOR: Pastikan titik hilang sebelum submit agar tidak error database (Integer)
document.getElementById('formSimpan').addEventListener('submit', function() {
    let inputNominal = this.querySelector('input[name="jumlah"]');
    inputNominal.value = inputNominal.value.replace(/\./g, '');
});

document.getElementById('formUpdate').addEventListener('submit', function() {
    let inputNominal = this.querySelector('input[name="jumlah"]');
    inputNominal.value = inputNominal.value.replace(/\./g, '');
});

function editData(id, keperluan, jumlah, total) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_keperluan').value = keperluan;
    document.getElementById('edit_jumlah').value = jumlah;
    let nominalMurni = Math.round(parseFloat(total));
    document.getElementById('edit_total').value = formatRupiah(nominalMurni);
    var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
    myModal.show();
}

function hapusData(url) {
    Swal.fire({
        title: 'Hapus data ini?',
        text: "Data pengeluaran tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
<?= $this->endSection() ?>