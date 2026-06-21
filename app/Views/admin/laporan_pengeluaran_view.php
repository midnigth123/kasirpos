<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="glass-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0">
                <i class="fas fa-file-invoice-dollar me-2" style="color: #12f0d2;"></i>
                Laporan Pengeluaran Harian
            </h3>
            <button onclick="window.print()" class="btn btn-outline-light border-0 shadow-sm btn-cetak">
                <i class="fas fa-print me-2"></i> Cetak Laporan
            </button>
        </div>

        <form action="<?= site_url('admin/laporan_pengeluaran'); ?>" method="GET" class="mb-5 no-print">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Dari Tanggal</label>
                    <input type="date" name="tgl_awal" class="form-control form-custom shadow-none"
                        value="<?= isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Sampai Tanggal</label>
                    <input type="date" name="tgl_akhir" class="form-control form-custom shadow-none"
                        value="<?= isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn-simpan w-100">
                        <i class="fas fa-search me-2"></i> FILTER DATA
                    </button>
                </div>
            </div>
        </form>

        <hr class="border-white opacity-25 mb-4 no-print">

        <div class="table-responsive">
            <table class="table table-senja align-middle">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Tanggal & Jam</th>
                        <th width="15%">Oleh</th>
                        <th>Keperluan</th>
                        <th class="text-center" width="10%">Qty</th>
                        <th class="text-end" width="20%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    $grand_total = 0;
                    if(!empty($laporan)): 
                        foreach($laporan as $l): 
                            $grand_total += $l['total_bayar'];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold"><?= date('d/m/Y', strtotime($l['tanggal'])) ?></div>
                            <div class="opacity-50 small"><?= date('H:i', strtotime($l['created_at'])) ?> WIB</div>
                        </td>
                        <td><span class="badge bg-dark border border-secondary"><?= $l['nama_user'] ?></span></td>
                        <td><?= $l['nama_keperluan'] ?></td>
                        <td class="text-center"><?= $l['jumlah'] ?></td>
                        <td class="text-end fw-bold text-info">
                            Rp <?= number_format($l['total_bayar'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 opacity-50">
                            <i class="fas fa-search mb-2 d-block fa-2x"></i>
                            Data tidak ditemukan untuk periode ini.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <?php if(!empty($laporan)): ?>
                <tfoot class="border-top border-white">
                    <tr class="fw-bold" style="color: #12f0d2;">
                        <td colspan="5" class="text-end py-3 px-4">TOTAL PENGELUARAN :</td>
                        <td class="text-end py-3 fs-5">
                            Rp <?= number_format($grand_total, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<style>
/* 1. VARIABEL WARNA (Kunci agar Teks Terang/Gelap Otomatis) */
:root {
    --glass-bg: rgba(255, 255, 255, 0.1);
    --glass-text: #ffffff;
    --glass-border: rgba(255, 255, 255, 0.2);
    --input-bg: rgba(0, 0, 0, 0.3);
    --input-text: #ffffff;
    --input-border: rgba(255, 255, 255, 0.3);
    --table-row-text: #ffffff;
}

/* Override Variabel saat Light Mode Aktif */
[data-bs-theme="light"] {
    --glass-bg: rgba(255, 255, 255, 0.9);
    /* Putih Pekat */
    --glass-text: #222222;
    /* Hitam Tajam */
    --glass-border: #dddddd;
    --input-bg: #ffffff;
    --input-text: #222222;
    --input-border: #cccccc;
    --table-row-text: #333333;
}

/* 2. PENERAPAN STYLE */
.glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    border: 1px solid var(--glass-border);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
    padding: 30px;
    color: var(--glass-text);
    /* Teks Judul & Label mengikuti mode */
    transition: all 0.3s ease;
}

.form-label {
    color: var(--glass-text) !important;
    font-weight: 600;
}

.form-custom {
    background: var(--input-bg) !important;
    border: 1px solid var(--input-border) !important;
    color: var(--input-text) !important;
    border-radius: 12px;
    padding: 12px;
}

.form-custom::placeholder {
    color: var(--input-text);
    opacity: 0.5;
}

.table-senja thead th {
    background: rgba(7, 138, 214, 0.15);
    /* Toska Transparan */
    color: #1216f0;
    /* Warna Identitas Senja Coffee tetap */
    border: none;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 1px;
}

.table-senja tbody tr {
    color: var(--table-row-text) !important;
    border-bottom: 1px solid rgba(128, 128, 128, 0.1);
}

.btn-simpan {
    background: #048b79;
    color: #040a04;
    font-weight: 700;
    border-radius: 12px;
    border: none;
    padding: 12px 25px;
    transition: 0.3s;
}

.btn-simpan:hover {
    background: #000;
    color: #fff;
    transform: translateY(-2px);
}

/* Badge agar terlihat di Light Mode */
[data-bs-theme="light"] .badge.bg-dark {
    background-color: #222 !important;
    color: #fff !important;
}

/* 3. CSS KHUSUS PRINT (Tetap Formal) */
@media print {
    @page {
        size: portrait;
        margin: 1cm;
    }

    .no-print,
    .btn-cetak,
    .sidebar,
    footer,
    header {
        display: none !important;
    }

    body {
        background: white !important;
        color: black !important;
    }

    .glass-card {
        background: white !important;
        color: black !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }

    .table-senja {
        color: black !important;
        width: 100% !important;
        border: 1px solid #ddd !important;
    }

    .table-senja thead th {
        background: #f0f0f0 !important;
        color: black !important;
        border: 1px solid #ddd !important;
    }

    .table-senja tbody td {
        border: 1px solid #ddd !important;
        color: black !important;
    }

    .text-info {
        color: black !important;
    }
}
</style>

<?= $this->endSection() ?>