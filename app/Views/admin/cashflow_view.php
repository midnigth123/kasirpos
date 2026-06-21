<?php

/**
 * @var string $saldo_saat_ini
 * @var string $bulan_pilih
 * @var string $tahun_pilih
 * @var array $cashflow
 * @var array $pengaturan
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<style>
    /* STYLE UNTUK TAMPILAN LAYAR (BROWSER) */
    .print-only {
        display: none;
    }

    @media print {
        @page {
            margin: 1cm;
            size: A4;
        }

        .no-print,
        .sidebar,
        .navbar,
        .btn,
        .modal,
        form,
        .badge {
            display: none !important;
        }

        .container-fluid {
            padding: 0 !important;
        }

        .card {
            border: none !important;
            shadow: none !important;
        }

        .card-header {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        .table {
            width: 100% !important;
            border: 1px solid #000 !important;
        }

        th,
        td {
            border: 1px solid #000 !important;
            padding: 8px !important;
            color: #000 !important;
        }

        .text-success,
        .text-danger,
        .text-primary {
            color: #000 !important;
            font-weight: bold;
        }

        body {
            background: white !important;
            font-size: 12px;
        }
    }
</style>

<div class="container-fluid py-4">

    <div class="print-only text-center mb-4">
        <h2 style="margin:0; font-weight: bold;">
            <?= strtoupper($pengaturan['nama_toko'] ?? 'SENJA COFFEE') ?>
        </h2>
        <p style="margin:5px 0; font-size: 14px;">
            <?= $pengaturan['alamat'] ?? 'Alamat Belum Diatur' ?>
        </p>

        <hr style="border: 1px solid #000; margin: 10px 0;">

        <h3 style="text-decoration: underline; margin-bottom: 5px;">LAPORAN ARUS KAS (CASH FLOW)</h3>
        <p style="margin: 0;">
            Periode: <?= date('F', mktime(0, 0, 0, (int)$bulan_pilih, 1)) ?> <?= $tahun_pilih ?>
        </p>
    </div>

    <div class="row mb-4 no-print">
        <div class="col-md-4">
            <div class="card <?= ($saldo_saat_ini < 0) ? 'bg-danger' : 'bg-success'; ?> text-white shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h6 class="small text-uppercase opacity-75">Saldo Kas Saat Ini</h6>
                    <h2 class="fw-bold mb-0">
                        <?php
                        if ($saldo_saat_ini < 0) {
                            // Jika minus, tanda negatif dibuang dengan abs() lalu dibungkus kurung
                            echo 'Rp (' . number_format(abs($saldo_saat_ini), 0, ',', '.') . ')';
                        } else {
                            echo 'Rp ' . number_format($saldo_saat_ini, 0, ',', '.');
                        }
                        ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center no-print">
            <h6 class="m-0 fw-bold text-primary">Data Aliran Kas</h6>
            <div class="btn-group">
                <a href="<?= base_url('admin/export_cashflow?bulan=' . $bulan_pilih . '&tahun=' . $tahun_pilih) ?>"
                    class="btn btn-success btn-sm rounded-pill px-3 me-2">
                    <i class="fas fa-file-excel me-1"></i> Excel
                </a>
                <button onclick="window.print()" class="btn btn-secondary btn-sm rounded-pill px-3 me-2">
                    <i class="fas fa-print me-1"></i> Cetak
                </button>
                <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal"
                    data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-1"></i> Catat Transaksi
                </button>
            </div>
        </div>

        <div class="card-body">
            <form action="" method="GET" class="row g-2 mb-4 no-print">
                <div class="col-md-2">
                    <select name="bulan" class="form-select border-0 bg-light">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= sprintf('%02d', $m) ?>"
                                <?= $bulan_pilih == sprintf('%02d', $m) ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="tahun" class="form-control border-0 bg-light"
                        value="<?= $tahun_pilih ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm"><i class="fas fa-search"></i></button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">Tanggal</th>
                            <th>Keterangan</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-end">Masuk</th>
                            <th class="text-end">Keluar</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_m = 0;
                        $total_k = 0;
                        foreach ($cashflow as $cf):
                            $total_m += $cf['masuk'];
                            $total_k += $cf['keluar'];
                        ?>
                            <tr>
                                <td class="text-center small"><?= date('d/m/Y', strtotime($cf['tanggal'])) ?></td>
                                <td><?= $cf['keterangan'] ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border"><?= $cf['kategori'] ?></span>
                                </td>
                                <td class="text-success text-end fw-bold">
                                    <?= $cf['masuk'] > 0 ? number_format($cf['masuk'], 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-danger text-end fw-bold">
                                    <?= $cf['keluar'] > 0 ? number_format($cf['keluar'], 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-end fw-bold text-primary">Rp
                                    <?= number_format($cf['saldo_akhir'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="3" class="text-center">TOTAL PERIODE INI</td>
                            <td class="text-end text-success"><?= number_format($total_m, 0, ',', '.') ?></td>
                            <td class="text-end text-danger"><?= number_format($total_k, 0, ',', '.') ?></td>
                            <td class="text-end bg-yellow-100">Sisa: <?= number_format($total_m - $total_k, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="print-only mt-5">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
                        <br><br><br>
                        <p class="fw-bold">( ___________ )</p>
                        <p>Manager Operasional</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?= base_url('admin/simpan_cashflow') ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Input Transaksi Kas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Jenis</label>
                        <select name="jenis" class="form-select">
                            <option value="masuk">Masuk (+)</option>
                            <option value="keluar">Keluar (-)</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Kategori</label>
                        <select name="kategori" class="form-select">
                            <option value="Operasional">Operasional</option>
                            <option value="Investasi">Investasi</option>
                            <option value="Pendanaan">Pendanaan</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nominal</label>
                    <input type="number" name="nominal" class="form-control" placeholder="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>