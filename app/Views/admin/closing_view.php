<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <div>
            <h4 class="fw-bold mb-0 text-success">Closing Kasir</h4>
            <p class="text-muted mb-0">Rekapitulasi pendapatan harian Senja Coffee</p>
        </div>
        <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 shadow-sm">
            <i class="fas fa-print me-2"></i> Cetak Laporan
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4 d-print-none">
        <div class="card-body p-3">
            <?php $tglFilter = service('request')->getGet('tanggal') ?? date('Y-m-d'); ?>
            <form action="<?= base_url('admin/closing') ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1 text-uppercase">Pilih Tanggal Closing</label>
                    <input type="date" name="tanggal" class="form-control bg-light border-0" 
                        value="<?= service('request')->getGet('tanggal') ?? date('Y-m-d') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100 rounded-3 fw-bold">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="printArea">
        <div class="text-center mb-4 d-none d-print-block">
            <h3 class="fw-bold mb-0 text-uppercase">Senja Coffee & Eatery</h3>
            <p class="mb-1">Laporan Penutupan Kasir (Closing)</p>
            <h6 class="fw-bold">Tanggal: <?= date('d M Y', strtotime($tglFilter)) ?></h6>
            <hr>
        </div>

        <div class="row g-3">
            <div class="col-md-4 fade-in-up">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-success text-white card-hover">
                    <small class="opacity-75 d-block mb-1 text-uppercase fw-bold">Total Pendapatan Bersih</small>
                    <h2 class="fw-bold mb-0">Rp <?= number_format($rekap['total_pendapatan'] ?? 0, 0, ',', '.') ?></h2>
                    <hr class="opacity-25">
                    <small class="small italic opacity-75">Berdasarkan seluruh transaksi 'Lunas'</small>
                </div>
            </div>
            
            <div class="col-md-4 fade-in-up" style="animation-delay: 0.1s;">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white border-start border-danger border-4 card-hover">
                    <div class="d-flex align-items-center h-100">
                        <div class="bg-danger-subtle text-danger p-3 rounded-circle me-3">
                            <i class="fas fa-times-circle fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0 text-danger"><?= $rekap['jumlah_batal'] ?? 0 ?></h4>
                            <small class="text-muted fw-bold">Transaksi Dibatalkan</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="row g-3">
                    <?php 
                    $metode = [
                        ['label' => 'Tunai', 'val' => $rekap['total_tunai'], 'border' => 'border-warning'],
                        ['label' => 'Transfer', 'val' => $rekap['total_transfer'], 'border' => 'border-primary'],
                        ['label' => 'QRIS', 'val' => $rekap['total_qris'], 'border' => 'border-info'],
                        ['label' => 'EDC', 'val' => $rekap['total_edc'], 'border' => 'border-secondary'],
                        ['label' => 'Online (Ojol)', 'val' => $rekap['total_online'], 'border' => 'border-success']
                    ];
                    foreach ($metode as $index => $m): ?>
                        <div class="col-md col-6 fade-in-up" style="animation-delay: <?= 0.2 + ($index * 0.05) ?>s;">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 border-start <?= $m['border'] ?> border-4 card-hover">
                                <small class="text-muted d-block mb-1 fw-bold text-uppercase"><?= $m['label'] ?></small>
                                <h5 class="fw-bold text-dark mb-0">Rp <?= number_format($m['val'] ?? 0, 0, ',', '.') ?></h5>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Animasi Modern */
.fade-in-up { opacity: 0; transform: translateY(20px); animation: fadeInUp 0.6s ease-out forwards; }
@keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
.card-hover { transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); }
.card-hover:hover { transform: translateY(-8px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }

/* Print Settings */
@media print {
    .d-print-none, .btn, form { display: none !important; }
    .card { border: 1px solid #ddd !important; box-shadow: none !important; }
}
</style>

<?= $this->endSection() ?>