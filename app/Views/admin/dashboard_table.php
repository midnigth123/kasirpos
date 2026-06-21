<?php

/**
 * @var array $meja
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 fw-bold text-dark">Dashboard Analitik Meja</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Statistik Meja</li>
    </ol>

    <!-- 1. AREA KARTU STATISTIK (DASHBOARD DISPLAY) -->
    <div class="row mb-4">
        <!-- Total Meja -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100 py-2 border-0" style="border-radius: 20px; transition: all 0.3s;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Meja Resto
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= count($meja) ?> Meja</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-box bg-light-primary"><i class="fas fa-th-large fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
            $tersedia = array_filter($meja, function($m) { return strtolower($m['status_meja']) == 'tersedia'; });
            $terisi = array_filter($meja, function($m) { return strtolower($m['status_meja']) == 'terisi'; });
            $reservasi = array_filter($meja, function($m) { return strtolower($m['status_meja']) == 'reservasi'; });
        ?>

        <!-- Tersedia -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100 py-2 border-0" style="border-radius: 20px;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Meja Kosong
                                (Tersedia)</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= count($tersedia) ?> Meja</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-box bg-light-success"><i
                                    class="fas fa-check-circle fa-2x text-success"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terisi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100 py-2 border-0" style="border-radius: 20px;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Meja Terisi (Makan)
                            </div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= count($terisi) ?> Meja</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-box bg-light-danger"><i class="fas fa-users fa-2x text-danger"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reservasi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100 py-2 border-0" style="border-radius: 20px;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Meja Terbooking</div>
                            <div class="h4 mb-0 fw-bold text-gray-800"><?= count($reservasi) ?> Meja</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-box bg-light-warning"><i
                                    class="fas fa-calendar-alt fa-2x text-warning"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. DIAGRAM PERSENTASE OKUPANSI (VISUAL TAMBAHAN) -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white fw-bold text-dark border-0 pt-3">
                    <i class="fas fa-chart-pie me-1 text-secondary"></i> Persentase Hunian Meja
                </div>
                <div class="card-body">
                    <?php 
                        $total = count($meja) > 0 ? count($meja) : 1;
                        $p_tersedia = (count($tersedia) / $total) * 100;
                        $p_terisi = (count($terisi) / $total) * 100;
                        $p_reservasi = (count($reservasi) / $total) * 100;
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Tersedia (Hijau)</span>
                            <strong><?= round($p_tersedia) ?>%</strong>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $p_tersedia ?>%">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Sedang Terisi (Merah)</span>
                            <strong><?= round($p_terisi) ?>%</strong>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $p_terisi ?>%">
                            </div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Reservasi (Kuning)</span>
                            <strong><?= round($p_reservasi) ?>%</strong>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $p_reservasi ?>%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-body d-flex flex-column justify-content-center text-center p-4">
                    <h5 class="fw-bold text-muted">Layar Monitor Besar</h5>
                    <p class="small text-muted">Buka halaman display full screen tanpa sidebar khusus untuk dipajang di
                        TV monitor restoran.</p>
                    <a href="<?= site_url('admin/live_display') ?>" target="_blank"
                        class="btn btn-danger rounded-pill shadow-sm px-4 align-self-center fw-bold">
                        <i class="fas fa-tv me-1"></i> Buka Live Display Meja
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-box {
    width: 55px;
    height: 55px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-light-primary {
    background-color: #e3f2fd;
}

.bg-light-success {
    background-color: #e8f5e9;
}

.bg-light-danger {
    background-color: #ffebee;
}

.bg-light-warning {
    background-color: #fff8e1;
}
</style>
<?= $this->endSection() ?>