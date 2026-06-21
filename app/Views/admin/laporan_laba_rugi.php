<?php
/**
 * @var string $tglAkhir
 * @var string $tglAwal
 * @var array $laporan
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Laporan Laba Rugi</h5>
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="<?= base_url('admin/laporan_laba_rugi') ?>" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Tanggal Mulai</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i
                                        class="fas fa-calendar text-primary"></i></span>
                                <input type="date" name="tglAwal" class="form-control" value="<?= $tglAwal ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Tanggal Selesai</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i
                                        class="fas fa-calendar text-danger"></i></span>
                                <input type="date" name="tglAkhir" class="form-control" value="<?= $tglAkhir ?>">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                                <i class="fas fa-search me-2"></i>Tampilkan Laporan
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th width="15%">Tanggal</th>
                                    <th>Omset Bersih</th>
                                    <th>Total Diskon</th>
                                    <th>Modal (HPP)</th>
                                    <th>Laba Kotor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalOmzet = 0; 
                                $totalDiskon = 0;
                                $totalHpp = 0; 
                                $totalLaba = 0;
                                
                                if(empty($laporan)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                                        Belum ada data transaksi pada periode ini.
                                    </td>
                                </tr>
                                <?php else: 
                                    foreach ($laporan as $row): 
                                        // Hitung Laba Kotor per baris: Omset Bersih - Total Modal
                                        $laba_per_baris = $row['total_omzet_bersih'] - $row['total_modal'];
                                        
                                        // Akumulasi Total Bawah
                                        $totalOmzet  += $row['total_omzet_bersih'];
                                        $totalDiskon += $row['total_diskon_diberikan'];
                                        $totalHpp    += $row['total_modal'];
                                        $totalLaba   += $laba_per_baris;
                                ?>
                                <tr>
                                    <td class="text-center fw-bold">
                                        <?= date('d/m/Y', strtotime($row['tanggal'])) ?>
                                    </td>
                                    <td class="text-end px-3">
                                        Rp <?= number_format($row['total_omzet_bersih'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end px-3 text-warning">
                                        Rp <?= number_format($row['total_diskon_diberikan'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end px-3 text-danger">
                                        Rp <?= number_format($row['total_modal'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end px-3 fw-bold text-success">
                                        Rp <?= number_format($laba_per_baris, 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>

                            <?php if(!empty($laporan)): ?>
                            <tfoot class="table-dark">
                                <tr class="py-3">
                                    <td class="text-center fw-bold">TOTAL PERIODE INI</td>
                                    <td class="text-end px-3 fw-bold">
                                        Rp <?= number_format($totalOmzet, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end px-3 fw-bold text-warning">
                                        Rp <?= number_format($totalDiskon, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end px-3 fw-bold">
                                        Rp <?= number_format($totalHpp, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end px-3 fw-bold bg-success">
                                        Rp <?= number_format($totalLaba, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>

                    <?php if(!empty($laporan)): ?>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="border-start border-4 border-primary p-3 bg-white shadow-sm">
                                <small class="text-muted d-block fw-bold mb-1">TOTAL OMZET</small>
                                <h4 class="mb-0 text-primary">Rp <?= number_format($totalOmzet, 0, ',', '.') ?></h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-start border-4 border-danger p-3 bg-white shadow-sm">
                                <small class="text-muted d-block fw-bold mb-1">TOTAL MODAL (HPP)</small>
                                <h4 class="mb-0 text-danger">Rp <?= number_format($totalHpp, 0, ',', '.') ?></h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border-start border-4 border-success p-3 bg-white shadow-sm">
                                <small class="text-muted d-block fw-bold mb-1">ESTIMASI LABA KOTOR</small>
                                <h4 class="mb-0 text-success">Rp <?= number_format($totalLaba, 0, ',', '.') ?></h4>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>