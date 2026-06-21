<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4"><i class="fas fa-dolly-flatbed me-2"></i> Penerimaan Barang Masuk</h4>

    <?php if (session()->getFlashdata('pesan')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="auto-close-alert">
        <?= session()->getFlashdata('pesan') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card p-4 shadow-sm border-0" style="border-radius: 16px;">
                <h5 class="fw-bold mb-3 text-success"><i class="fas fa-plus-circle me-2"></i> Form Input Penerimaan</h5>
                <form action="<?= site_url('admin/penerimaan/simpan') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Masuk</label>
                        <input type="date" class="form-control border-0 bg-light py-2 px-3 shadow-none"
                            name="tanggal_masuk" value="<?= old('tanggal_masuk', date('Y-m-d')) ?>" required
                            style="border-radius: 10px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-danger">
                            <i class="fas fa-calendar-times me-1"></i> Tanggal Expire
                        </label>
                        <input type="date" class="form-control border-0 bg-light py-2 px-3 shadow-none"
                            name="tgl_expired[]" value="<?= old('tgl_expired') ?>" required
                            style="border-radius: 10px;">
                        <div class="form-text small">Sesuaikan dengan tanggal pada kemasan produk.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Supplier</label>
                        <select class="form-select border-0 bg-light py-2 px-3 shadow-none" name="supplier" required
                            style="border-radius: 10px;">
                            <option value="" disabled selected>Pilih Supplier / Toko...</option>

                            <?php if (isset($supplier) && !empty($supplier)): ?>
                            <?php foreach ($supplier as $s): ?>
                            <option value="<?= $s['nama_supplier'] ?>"
                                <?= old('supplier') == $s['nama_supplier'] ? 'selected' : '' ?>>
                                <?= $s['nama_supplier'] ?>
                            </option>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <option value="" disabled>Data supplier masih kosong</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <hr class="my-3">

                    <div id="dynamic-items">
                        <div class="row mb-3 item-row border-bottom pb-3">
                            <div class="col-12 mb-2">
                                <label class="form-label fw-semibold">Pilih Produk</label>
                                <select class="form-select border-0 bg-light py-2 px-3 shadow-none" name="produk_id[]"
                                    required style="border-radius: 10px;">
                                    <option value="">-- Pilih Produk --</option>
                                    <?php if (isset($produk) && !empty($produk)): ?>
                                    <?php foreach ($produk as $p): ?>
                                    <option value="<?= $p['produk_id'] ?>"><?= $p['nama_produk'] ?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-semibold">Jumlah Masuk</label>
                                <input type="number" class="form-control border-0 bg-light py-2 px-3 shadow-none"
                                    name="jumlah_masuk[]" placeholder="Contoh: 10" min="1" required
                                    style="border-radius: 10px;">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label fw-semibold">Harga Beli Baru</label>
                                <input type="number" class="form-control border-0 bg-light py-2 px-3 shadow-none"
                                    name="harga_beli_baru[]" placeholder="Harga Satuan" min="0"
                                    style="border-radius: 10px;">
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="add-more">
                        <i class="fas fa-plus fa-sm me-1"></i> Tambah Baris Produk
                    </button>

                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-success py-2 shadow-sm"
                            style="background: #059669; border: none; border-radius: 12px;">
                            <i class="fas fa-save me-1"></i> Simpan Penerimaan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card p-4 shadow-sm border-0" style="border-radius: 16px;">
                <h5 class="fw-bold mb-3 text-primary"><i class="fas fa-list me-2"></i> Data Penerimaan Belum Disetujui
                </h5>

                <?php if (!empty($penerimaan_pending)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kode / Supplier</th>
                                <th>Tanggal</th>
                                <th>Item Barang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($penerimaan_pending as $row): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark"><?= $row['kode_penerimaan'] ?></span><br>
                                    <small class="text-muted"><i class="fas fa-truck me-1"></i>
                                        <?= $row['supplier'] ?></small>
                                </td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal_masuk'])) ?></td>
                                <td>
                                    <ul class="ps-3 mb-0" style="font-size: 0.85rem;">
                                        <?php 
                                        if (isset($penerimaan_detail) && !empty($penerimaan_detail)):
                                            foreach ($penerimaan_detail as $detail): 
                                                if ($detail['penerimaan_id'] == $row['penerimaan_id']):
                                        ?>
                                        <li>
                                            <?= $detail['nama_produk'] ?>
                                            <span class="badge bg-secondary"><?= $detail['jumlah_masuk'] ?> pcs</span>
                                        </li>
                                        <?php 
                                                endif;
                                            endforeach; 
                                        endif;
                                        ?>
                                    </ul>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success px-3 py-2 shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#konfirmasiModal"
                                        data-action="<?= site_url('admin/penerimaan/konfirmasi/' . $row['penerimaan_id']) ?>"
                                        style="border-radius: 8px;">
                                        <i class="fas fa-check"></i> Konfirmasi
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3 text-secondary"></i>
                    <p>Tidak ada barang yang sedang menunggu konfirmasi.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header bg-success text-white border-0 p-4">
                <h5 class="modal-title fw-bold" id="konfirmasiModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Penerimaan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 px-4 text-dark text-center">
                <p class="mb-0">Apakah Anda yakin ingin mengonfirmasi penerimaan ini? Stok akan otomatis bertambah ke
                    master barang.</p>
            </div>
            <div class="modal-footer border-0 bg-white p-4 justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal"
                    style="border-radius: 10px;">Batal</button>
                <form id="formKonfirmasi" action="" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success px-4 py-2 text-white shadow-sm"
                        style="background-color: #059669; border-radius: 10px;">
                        <i class="fas fa-check me-1"></i> Ya, Konfirmasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                </div>
                <h4 class="fw-bold text-dark">Berhasil!</h4>
                <p class="text-muted mb-0">Penerimaan barang telah dikonfirmasi dan stok diperbarui.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addMoreBtn = document.getElementById('add-more');
    const dynamicItems = document.getElementById('dynamic-items');

    addMoreBtn.addEventListener('click', function() {
        const itemRow = document.createElement('div');
        itemRow.classList.add('row', 'mb-3', 'item-row', 'border-bottom', 'pb-3');

        itemRow.innerHTML = `
            <div class="col-12 mb-2">
                <label class="form-label fw-semibold">Pilih Produk</label>
                <select class="form-select border-0 bg-light py-2 px-3 shadow-none" name="produk_id[]" required style="border-radius: 10px;">
                    <option value="">-- Pilih Produk --</option>
                    <?php if (isset($produk) && !empty($produk)): ?>
                        <?php foreach ($produk as $p): ?>
                            <option value="<?= $p['produk_id'] ?>"><?= $p['nama_produk'] ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label fw-semibold">Jumlah Masuk</label>
                <input type="number" class="form-control border-0 bg-light py-2 px-3 shadow-none" name="jumlah_masuk[]" placeholder="Contoh: 10" min="1" required style="border-radius: 10px;">
            </div>
            <div class="col-md-6 mb-2">
                <label class="form-label fw-semibold">Harga Beli Baru</label>
                <input type="number" class="form-control border-0 bg-light py-2 px-3 shadow-none" name="harga_beli_baru[]" placeholder="Harga Satuan" min="0" style="border-radius: 10px;">
            </div>
            <div class="col-12 text-end mt-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-row" style="border-radius: 8px;">
                    <i class="fas fa-trash fa-xs"></i> Hapus Baris
                </button>
            </div>
        `;

        itemRow.querySelector('.remove-row').addEventListener('click', function() {
            itemRow.remove();
        });

        dynamicItems.appendChild(itemRow);
    });

    // Otomatis menutup alert setelah 4 detik
    const alertElement = document.getElementById('auto-close-alert');
    if (alertElement) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }, 4000);
    }

    // Modal Konfirmasi Script
    var konfirmasiModal = document.getElementById('konfirmasiModal');
    konfirmasiModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var actionUrl = button.getAttribute('data-action');

        var form = document.getElementById('formKonfirmasi');
        form.action = actionUrl;
    });
    document.getElementById('formKonfirmasi').addEventListener('submit', function(e) {
        e.preventDefault(); // Tahan dulu biar modal suksesnya kelihatan wkwk

        // 1. Tutup modal konfirmasi Bootstrap yang sedang terbuka
        const modalElement = document.getElementById('konfirmasiModal');
        const modalBootstrap = bootstrap.Modal.getInstance(modalElement);
        if (modalBootstrap) {
            modalBootstrap.hide();
        }

        // 2. Munculkan SweetAlert Berhasil dengan style modern
        Swal.fire({
            title: 'Berhasil!',
            text: 'Penerimaan barang telah dikonfirmasi dan stok otomatis diperbarui.',
            icon: 'success',
            iconColor: '#059669',
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
            backdrop: `rgba(0,0,0,0.4)`,
            customClass: {
                popup: 'rounded-20',
            },
            willClose: () => {
                this.submit();
            }
        });
    });
});
</script>

<?= $this->endSection() ?>