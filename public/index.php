<?php

// ========================================================================
// 📡 1. RADAR MULTI-TENANT ANTI-REDIRECT (UNTUK HALAMAN KASIR YG AKTIF)
// ========================================================================
if (isset($_GET['check_status_maintenance'])) {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    $current_dir = basename(dirname(__DIR__)); 
    
    // Cek gembok spesifik tenant atau gembok global umum
    if (file_exists(__DIR__ . '/../maintenance_' . $current_dir . '.flag') || file_exists(__DIR__ . '/../maintenance.flag')) {
        echo json_encode(['maintenance' => true]);
    } else {
        echo json_encode(['maintenance' => false]);
    }
    exit; 
}

use CodeIgniter\Boot;
use Config\Paths;

// ========================================================================
// 🔑 2. CONFIG BYPASS ADMIN (KATA RAHASIA JAGA-JAGA)
// ========================================================================
$kata_rahasia_admin = 'kasirkita';

if (isset($_GET['masuk']) && $_GET['masuk'] === $kata_rahasia_admin) {
    setcookie('admin_bypass_maintenance', $kata_rahasia_admin, time() + (3600 * 12), '/');
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// ========================================================================
// 🚧 3. GEMBOK MAINTENANCE UTAMA & AUTO-UNLOCK
// ========================================================================
$current_dir = basename(dirname(__DIR__));
$is_lock = file_exists(__DIR__ . '/../maintenance_' . $current_dir . '.flag') || file_exists(__DIR__ . '/../maintenance.flag');

if ($is_lock && (!isset($_COOKIE['admin_bypass_maintenance']) || $_COOKIE['admin_bypass_maintenance'] !== $kata_rahasia_admin)) {
    http_response_code(503);

    $base_path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    $url_logo  = $base_path . 'assets/img/icon_kasir.png';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KasirKita - Maintenance</title>
    <style>
    body {
        text-align: center;
        padding: 100px 20px;
        font-family: "Segoe UI", sans-serif;
        background: #faf8f5;
        color: #443e38;
        margin: 0;
    }

    .card {
        text-align: left;
        max-width: 500px;
        margin: 0 auto;
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border-top: 5px solid #198754;
    }

    .logo-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 110px;
        height: 110px;
        overflow: hidden;
        margin: 0 auto;
    }
    </style>
</head>

<body>
    <div class="card">
        <div class="logo-container">
            <img src="<?= $url_logo ?>" alt="Logo Toko" style="width: 100%; height: 100%; object-fit: contain;">
        </div>
        <h1 style="font-size: 32px; color: #198754; text-align: center; font-weight: 700;">Sedang dalam Proses
            Maintenance...</h1>
        <p>Halo Kru & Pelanggan Setia <strong>KasirKita</strong>, saat ini dashboard aplikasi sedang menjalani
            maintenance rutin untuk meningkatkan performa sistem pelayanan.</p>
        <hr style="border: 0; border-top: 1px dashed #e1dbd6; margin: 25px 0;">
        <p style="font-size: 13px; color: #b5aea7; margin: 0; text-align: center;">&mdash; Tim Developer KasirKita
            &mdash;</p>
    </div>

    <script>
    setInterval(function() {
        var URL_Cek = window.location.origin + window.location.pathname + '?check_status_maintenance=1&t=' +
            new Date().getTime();
        fetch(URL_Cek, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.maintenance === false) window.location.href = window.location.origin + window
                    .location.pathname;
            });
    }, 5000);
    </script>
</body>

</html>
<?php
    exit;
}

/* ---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION (START CI4)
 * --------------------------------------------------------------- */
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) chdir(FCPATH);

require FCPATH . '../app/Config/Paths.php';
$paths = new Paths();
require $paths->systemDirectory . '/Boot.php';
exit(Boot::bootWeb($paths));