<?php

// ========================================================================
// 📡 1. RADAR MULTI-TENANT ANTI-REDIRECT (UNTUK HALAMAN KASIR YG AKTIF)
// ========================================================================
// Menjawab request fetch dari halaman kasir secara instan agar tidak dicegat Filter Login CI4
if (isset($_GET['check_status_maintenance'])) {
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Mengambil nama folder tempat project ditaruh di hosting (Dinamis untuk Tenant)
    $current_dir = basename(dirname(__DIR__)); 
    
    // Cek gembok spesifik tenant (misal: maintenance_toko1.flag) atau gembok global umum
    if (file_exists(__DIR__ . '/../maintenance_' . $current_dir . '.flag') || file_exists(__DIR__ . '/../maintenance.flag')) {
        echo json_encode(['maintenance' => true]);
    } else {
        echo json_encode(['maintenance' => false]);
    }
    exit; // Langsung potong di sini, jangan biarkan masuk ke framework!
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
        font-family: "Segoe UI", -apple-system, sans-serif;
        background: #faf8f5;
        color: #443e38;
        margin: 0;
    }

    h1 {
        font-size: 32px;
        color: #198754;
        margin-top: 20px;
        margin-bottom: 15px;
        font-weight: 700;
    }

    p {
        font-size: 16px;
        color: #6c757d;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .card {
        display: block;
        text-align: left;
        max-width: 500px;
        margin: 0 auto;
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border-top: 5px solid #198754;
    }

    .text-center {
        text-align: center;
    }

    /* 🎯 FIX VISUAL: Diubah ke 110px x 110px bulat simetris tanpa distorsi */
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

        <h1 class="text-center">Sedang dalam Proses Maintenance...</h1>
        <p>Halo Kru & Pelanggan Setia <strong>KasirKita</strong>, saat ini dashboard aplikasi sedang menjalani
            maintenance rutin untuk meningkatkan performa sistem pelayanan.</p>
        <p>Aplikasi tidak dapat diakses sementara waktu. Kami akan segera kembali dalam beberapa saat dengan performa
            yang lebih baik!</p>
        <hr style="border: 0; border-top: 1px dashed #e1dbd6; margin: 25px 0;">
        <p style="font-size: 13px; color: #b5aea7; margin: 0; text-align: center;">&mdash; Tim Developer KasirKita
            &mdash;</p>
    </div>

    <script>
    setInterval(function() {
        // 🎯 FIX RADAR URL: Mengarah langsung ke switch radar di atas secara instan
        var URL_Cek = window.location.origin + window.location.pathname + '?check_status_maintenance=1&t=' +
            new Date().getTime();

        fetch(URL_Cek, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                // Jika gembok dilepas oleh pusat, otomatis muat ulang halaman login kasir
                if (data.maintenance === false) {
                    window.location.href = window.location.origin + window.location.pathname;
                }
            })
            .catch(function(error) {
                console.log("Mengecek status gembok...");
            });
    }, 5000);
    </script>
</body>

</html>
<?php
    exit;
}

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */
$minPhpVersion = '8.1';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf('Your PHP version must be %s or higher to run CodeIgniter. Current version: %s', $minPhpVersion, PHP_VERSION);
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;
    exit(1);
}

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION (START CI4)
 *---------------------------------------------------------------
 */
require FCPATH . '../app/Config/Paths.php';
$paths = new Paths();
require $paths->systemDirectory . '/Boot.php';
exit(Boot::bootWeb($paths));