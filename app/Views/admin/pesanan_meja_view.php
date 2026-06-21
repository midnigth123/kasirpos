<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<style>
    :root {
        --bg-body: #f8f9fa;
        --card-radius: 16px;
        --primary-green: #10B981;
        --primary-dark: #059669;
    }

    .wrapper-full {
        width: 100%;
        padding: 16px 24px;
        background-color: var(--bg-body);
        min-height: 90vh;
    }

    .modern-card {
        border: none;
        border-radius: var(--card-radius);
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
    }

    .nav-tabs-modern {
        border-bottom: 2px solid #f1f5f9;
        gap: 8px;
    }

    .nav-tabs-modern .nav-link {
        border: none !important;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 12px 20px;
        border-radius: 10px 10px 0 0;
        position: relative;
        transition: all 0.2s ease;
    }

    .nav-tabs-modern .nav-link:hover {
        color: #1e293b;
        background-color: #f1f5f9;
    }

    .nav-tabs-modern .nav-link.active {
        color: var(--primary-green) !important;
        background: transparent !important;
    }

    .nav-tabs-modern .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--primary-green);
        border-radius: 3px;
    }

    .table th {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #f3f4f6;
    }

    .table td {
        padding: 16px 12px;
        color: #1f2937;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .badge-pending {
        background-color: #fef3c7;
        color: #d97706;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        border: 1px solid #fde68a;
    }

    .badge-ditarik {
        background-color: #ecfdf5;
        color: #059669;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        border: 1px solid #a7f3d0;
    }

    .badge-dibatalkan {
        background-color: #fee2e2;
        color: #dc2626;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 20px;
        border: 1px solid #fca5a5;
    }

    .coret-merah {
        text-decoration: line-through red 2px;
    }
</style>

<div class="wrapper-full">
    <?php if (session()->getFlashdata('pesan_sukses')) : ?>
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" id="autoDismissAlert"
            role="alert" style="border-radius: 12px; background-color: #ecfdf5; color: #065f46;">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('pesan_sukses'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row align-items-center g-3 mb-4">
        <div class="col-12 col-lg-4">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex justify-content-center align-items-center text-white"
                    style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #10B981, #059669); flex-shrink: 0;">
                    <i class="fas fa-receipt fa-lg"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">Manajemen Pesanan</h4>
                    <p class="text-muted small mb-0">Pantau antrean meja secara real-time.</p>
                </div>
            </div>
        </div>
    </div>

    <div id="container-pesanan">
        <ul class="nav nav-tabs nav-tabs-modern mb-3" id="pesananTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#tab-pending"
                    type="button" role="tab">
                    <i class="fas fa-clock me-2 text-warning"></i>Pending
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="dibatalkan-tab" data-bs-toggle="tab" data-bs-target="#tab-dibatalkan"
                    type="button" role="tab">
                    <i class="fas fa-times-circle me-2 text-danger"></i>Dibatalkan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ditarik-tab" data-bs-toggle="tab" data-bs-target="#tab-ditarik" type="button"
                    role="tab">
                    <i class="fas fa-check-double me-2 text-success"></i>Selesai
                </button>
            </li>
        </ul>

        <div class="card modern-card border-0 p-3 shadow-sm">
            <div class="tab-content" id="pesananTabContent">

                <div class="tab-pane fade show active" id="tab-pending" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-secondary">
                                    <th class="ps-3">No. Meja</th>
                                    <th>Pemesan</th>
                                    <th>Total Harga</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <!-- <th class="text-center">Aksi</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php $hasPending = false;
                                if (!empty($produk)): foreach ($produk as $p): ?>
                                        <?php if (($p['status_order'] ?? 'Pending') === 'Pending'): $hasPending = true; ?>
                                            <tr>
                                                <td class="ps-3 fw-bold text-dark">Meja <?= esc($p['nomor_meja'] ?? $p['id_me_ja'] ?? '-') ?></td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?= esc($p['nama_pemesan'] ?? 'Pelanggan') ?></div>
                                                </td>
                                                <td class="fw-bold text-success">Rp <?= number_format($p['total_harga'] ?? 0, 0, ',', '.') ?></td>
                                                <td><span class="text-secondary small"><?= esc($p['metode_pembayaran'] ?? 'Tunai') ?></span></td>
                                                <td><span class="badge-modern badge-pending">Pending</span></td>
                                                <td class="text-muted small">
                                                    <div class="fw-bold"><?= date('H:i', strtotime($p['created_at'])) ?></div>
                                                    <div style="font-size: 10px;"><?= date('d/m/Y', strtotime($p['created_at'])) ?></div>
                                                </td>
                                                <!-- <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button class="btn btn-sm btn-success text-white"><i class="fas fa-arrow-alt-circle-right me-1"></i> Tarik</button>
                                                        <button class="btn btn-sm btn-light text-danger" data-bs-toggle="modal" data-bs-target="#modalHapusProduk<?= $p['id_temp'] ?>"><i class="fas fa-ban"></i> Cancel</button>
                                                    </div>
                                                </td> -->
                                            </tr>
                                <?php endif;
                                    endforeach;
                                endif; ?>
                                <?php if (!$hasPending): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Tidak ada antrean pesanan pending.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-ditarik" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-secondary">
                                    <th class="ps-3">No. Meja</th>
                                    <th>Pemesan</th>
                                    <th>Total Harga</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                    <th class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $hasDitarik = false;
                                if (!empty($produk)): foreach ($produk as $p): ?>
                                        <?php if (($p['status_order'] ?? '') === 'Ditarik' || ($p['status_order'] ?? '') === 'Success'): $hasDitarik = true; ?>
                                            <tr>
                                                <td class="ps-3 fw-bold text-dark">Meja <?= esc($p['nomor_meja'] ?? $p['id_meja'] ?? '-') ?></td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?= esc($p['nama_pemesan'] ?? 'Pelanggan') ?></div>
                                                </td>
                                                <td class="fw-bold text-success">Rp <?= number_format($p['total_harga'] ?? 0, 0, ',', '.') ?></td>
                                                <td><span class="text-secondary small"><?= esc($p['metode_pembayaran'] ?? 'Tunai') ?></span></td>
                                                <td><span class="badge-modern badge-ditarik">Selesai</span></td>
                                                <td class="text-muted small"><?= date('H:i', strtotime($p['created_at'])) ?></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-light text-primary btn-lihat-menu"
                                                        data-meja="<?= esc($p['nomor_meja'] ?? $p['id_meja'] ?? '-') ?>"
                                                        data-nama="<?= esc($p['nama_pemesan'] ?? 'Pelanggan') ?>"
                                                        data-items='<?= $p['item_json'] ?>'>
                                                        <i class="fas fa-eye"></i> Lihat Menu
                                                    </button>
                                                </td>
                                            </tr>
                                <?php endif;
                                    endforeach;
                                endif; ?>
                                <?php if (!$hasDitarik): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Belum ada pesanan yang ditarik.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-dibatalkan" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-secondary">
                                    <th class="ps-3">No. Meja</th>
                                    <th>Pemesan</th>
                                    <th>Total Harga</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $hasBatal = false;
                                if (!empty($produk)): foreach ($produk as $p): ?>
                                        <?php if (($p['status_order'] ?? '') === 'Dibatalkan' || ($p['status_order'] ?? '') === 'Cancel'): $hasBatal = true; ?>
                                            <tr>
                                                <td class="ps-3 fw-bold text-dark">Meja <?= esc($p['nomor_me_ja'] ?? $p['id_meja'] ?? '-') ?></td>
                                                <td>
                                                    <div class="text-muted coret-merah"><?= esc($p['nama_pemesan'] ?? 'Pelanggan') ?></div>
                                                </td>
                                                <td class="text-muted">Rp <?= number_format($p['total_harga'] ?? 0, 0, ',', '.') ?></td>
                                                <td><span class="text-secondary small"><?= esc($p['metode_pembayaran'] ?? 'Tunai') ?></span></td>
                                                <td><span class="badge-modern badge-dibatalkan">Dibatalkan</span></td>
                                                <td class="text-danger small"><?= date('H:i', strtotime($p['updated_at'] ?? $p['created_at'])) ?></td>
                                            </tr>
                                <?php endif;
                                    endforeach;
                                endif; ?>
                                <?php if (!$hasBatal): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Tidak ada riwayat pesanan batal.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLihatMenu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="fw-bold mb-0">Menu Pesanan - Meja <span id="txtMeja"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="small text-muted mb-3">Pemesan: <b id="txtNama" class="text-dark"></b></div>
                <div id="listMenuPesanan"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light w-100 fw-bold" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- 1. Logika Auto Dismiss Alert ---
    function setupAlertDismiss() {
        setTimeout(function() {
            var alertNode = document.getElementById('autoDismissAlert');
            if (alertNode) {
                var alert = new bootstrap.Alert(alertNode);
                alert.close();
            }
        }, 3000);
    }

    // --- 2. Logika Auto Reload 5 Detik ---
    function reloadPesanan() {
        // Hanya reload jika tidak ada modal yang sedang terbuka
        if ($('.modal.show').length === 0) {
            $.ajax({
                url: window.location.href,
                type: 'GET',
                success: function(response) {
                    var newContent = $(response).find('#container-pesanan').html();
                    $('#container-pesanan').html(newContent);
                    // Penting: Kembalikan tab yang aktif setelah konten di-update
                    var activeTabId = $('.nav-link.active').attr('id');
                    $('#' + activeTabId).tab('show');
                }
            });
        }
    }

    $(document).ready(function() {
        setupAlertDismiss();
        setInterval(reloadPesanan, 5000);
    });

    // --- 3. Lihat Detail Menu (Gunakan Delegation agar tetap jalan setelah reload) ---
    $(document).on('click', '.btn-lihat-menu', function() {
        const meja = $(this).data('meja');
        const nama = $(this).data('nama');
        const itemsRaw = $(this).attr('data-items');

        $('#txtMeja').text(meja);
        $('#txtNama').text(nama);
        $('#listMenuPesanan').empty();

        try {
            const items = JSON.parse(itemsRaw);
            let html = '';
            items.forEach(item => {
                html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded-3 border-start border-primary border-3">
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark small">${item.nama}</span>
                        <span class="text-muted" style="font-size: 10px;">Harga: Rp ${parseInt(item.harga).toLocaleString('id-ID')}</span>
                    </div>
                    <div class="badge bg-primary rounded-pill">${item.qty}x</div>
                </div>`;
            });
            $('#listMenuPesanan').html(html || '<div class="text-center text-muted">Tidak ada item</div>');
        } catch (e) {
            $('#listMenuPesanan').html('<div class="alert alert-danger small">Gagal memuat menu</div>');
        }

        var myModal = new bootstrap.Modal(document.getElementById('modalLihatMenu'));
        myModal.show();
    });
</script>

<?= $this->endSection() ?>