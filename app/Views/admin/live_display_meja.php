<?php

/**
 * @var array $meja
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('icon_kasir.ico') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live RealTime Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Share+Tech+Mono&display=swap"
        rel="stylesheet">

    <style>
    :root {
        --bg-dark: #090a0f;
        --solid-success: #198754;
        --solid-danger: #dc3545;
        --solid-warning: #ffc107;
    }

    body {
        background-color: var(--bg-dark);
        color: #ffffff;
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow-x: hidden;
    }

    /* ANIMASI KEDIP (BLINKING) */
    @keyframes blink-green {

        0%,
        100% {
            box-shadow: 0 10px 25px rgba(25, 135, 84, 0.4);
            filter: brightness(1);
        }

        50% {
            box-shadow: 0 10px 40px rgba(25, 135, 84, 0.8);
            filter: brightness(1.2);
        }
    }

    @keyframes blink-red {

        0%,
        100% {
            box-shadow: 0 10px 25px rgba(220, 53, 69, 0.4);
            opacity: 1;
        }

        50% {
            box-shadow: 0 10px 40px rgba(220, 53, 69, 0.7);
            opacity: 0.9;
        }
    }

    @keyframes blink-yellow {

        0%,
        100% {
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
            filter: saturate(1);
        }

        50% {
            box-shadow: 0 10px 40px rgba(255, 193, 7, 0.8);
            filter: saturate(1.5);
        }
    }

    .live-header {
        background: #11121a;
        border-bottom: 2px solid #1f2130;
    }

    .pulse-indicator {
        width: 14px;
        height: 14px;
        background-color: #ff3838;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 14px #ff3838;
        animation: pulse 1.5s infinite;
    }

    /* Grid Layout */
    .grid-meja {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 25px;
        padding: 20px;
    }

    .card-meja-futuristic {
        border: none;
        border-radius: 24px;
        position: relative;
        transition: all 0.3s ease;
    }

    /* MEJA TERSEDIA + KEDIP */
    .status-tersedia {
        background: linear-gradient(145deg, #198754, #115f3a);
        animation: blink-green 3s infinite ease-in-out;
    }

    .status-tersedia .nomor-meja-display {
        color: #ffffff;
        text-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .status-tersedia .badge-status {
        background: #ffffff;
        color: #198754;
    }

    .status-tersedia .icon-glow {
        color: rgba(255, 255, 255, 0.9);
    }

    .status-tersedia .text-label {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    /* MEJA TERISI + KEDIP */
    .status-terisi {
        background: linear-gradient(145deg, #dc3545, #9b1c26);
        animation: blink-red 2s infinite ease-in-out;
    }

    .status-terisi .nomor-meja-display {
        color: #ffffff;
        text-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    }

    .status-terisi .badge-status {
        background: #ffffff;
        color: #dc3545;
    }

    .status-terisi .icon-glow {
        color: rgba(255, 255, 255, 0.9);
    }

    .status-terisi .text-label {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    /* MEJA RESERVASI + KEDIP */
    .status-reservasi {
        background: linear-gradient(145deg, #ffc107, #c79400);
        animation: blink-yellow 4s infinite ease-in-out;
    }

    .status-reservasi .nomor-meja-display {
        color: #111116;
        text-shadow: 0 2px 5px rgba(255, 255, 255, 0.2);
    }

    .status-reservasi .badge-status {
        background: #111116;
        color: #ffc107;
    }

    .status-reservasi .icon-glow {
        color: #111116;
    }

    .status-reservasi .text-label {
        color: #2b2b36 !important;
    }

    .nomor-meja-display {
        font-size: 5.5rem;
        font-weight: 800;
        line-height: 1;
        margin: 10px 0;
        letter-spacing: -2px;
    }

    .badge-status {
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    #live-clock {
        font-family: 'Share Tech Mono', monospace;
        font-size: 2.5rem;
        color: #ffffff;
        text-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
        letter-spacing: 2px;
        line-height: 1;
        margin-bottom: 5px;
    }

    .header-date {
        color: #ffffff !important;
        opacity: 0.8;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        font-size: 0.9rem;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    .summary-bar {
        background: #11121a;
        border-top: 1px solid #1f2130;
    }
    </style>

    <meta http-equiv="refresh" content="10">
</head>

<body>

    <div class="live-header py-3 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <span class="pulse-indicator me-3"></span>
            <div>
                <h4 class="fw-bold m-0 text-uppercase text-white" style="letter-spacing: 2px;">Informasi Ketersediaan
                    Meja</h4>
                <small class="text-white-50" style="font-size: 0.8rem;">
                    Silakan pilih meja kosong yang berwarna <b class="text-white">HIJAU</b>
                </small>
            </div>
        </div>
        <div class="text-end">
            <div id="live-clock"><?= date('H:i:s') ?></div>
            <div class="header-date"><?= date('l, d F Y') ?></div>
        </div>
    </div>

    <div class="container-fluid py-4 mb-5">
        <div class="grid-meja">
            <?php foreach ($meja as $m): ?>
            <?php 
                $status = strtolower($m['status_meja']); 
                if ($status === 'terisi') {
                    $classStatus = 'status-terisi';
                    $icon = 'fa-users';
                } elseif ($status === 'reservasi') {
                    $classStatus = 'status-reservasi';
                    $icon = 'fa-user-clock';
                } else {
                    $classStatus = 'status-tersedia';
                    $icon = 'fa-couch';
                }
            ?>

            <div class="card-meja-futuristic <?= $classStatus ?> p-4 text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <i class="fas <?= $icon ?> fa-2xl icon-glow"></i>
                    <span class="badge shadow-sm badge-status">
                        <?= $m['status_meja']; ?>
                    </span>
                </div>

                <h1 class="nomor-meja-display"><?= $m['nomor_meja']; ?></h1>

                <div class="text-label small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 2px;">
                    Meja Restoran
                </div>
            </div>

            <?php endforeach; ?>
        </div>
    </div>

    <?php 
        $t_tersedia = count(array_filter($meja, function($m) { return strtolower($m['status_meja']) == 'tersedia'; }));
        $t_terisi = count(array_filter($meja, function($m) { return strtolower($m['status_meja']) == 'terisi'; }));
        $t_reservasi = count(array_filter($meja, function($m) { return strtolower($m['status_meja']) == 'reservasi'; }));
    ?>
    <div class="summary-bar fixed-bottom py-3 px-4 d-flex justify-content-center gap-5 small fw-bold">
        <span class="d-flex align-items-center gap-2">
            <span
                style="display:inline-block; width:15px; height:15px; background:#198754; border-radius:4px; box-shadow: 0 0 10px #198754;"></span>
            KOSONG / TERSEDIA: <?= $t_tersedia ?>
        </span>
        <span class="d-flex align-items-center gap-2">
            <span
                style="display:inline-block; width:15px; height:15px; background:#dc3545; border-radius:4px; box-shadow: 0 0 10px #dc3545;"></span>
            SEDANG TERISI: <?= $t_terisi ?>
        </span>
        <span class="d-flex align-items-center gap-2">
            <span
                style="display:inline-block; width:15px; height:15px; background:#ffc107; border-radius:4px; box-shadow: 0 0 10px #ffc107;"></span>
            RESERVASI: <?= $t_reservasi ?>
        </span>
    </div>

    <script>
    setInterval(() => {
        const now = new Date();
        document.getElementById('live-clock').innerText = now.toTimeString().split(' ')[0];
    }, 1000);
    </script>
</body>

</html>