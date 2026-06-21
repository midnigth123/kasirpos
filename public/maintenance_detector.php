<?php
$conn = mysqli_connect("localhost", "root", "", "db_kasir");
$kode_toko_aktif = 'outlet2'; // Sesuaikan dengan DB Bos

if (isset($_GET['check_status_maintenance'])) {
    header('Content-Type: application/json');
    $is_lock = false;
    if ($conn) {
        $result = mysqli_query($conn, "SELECT is_maintenance FROM master_toko WHERE kode_toko = '$kode_toko_aktif' LIMIT 1");
        $toko = mysqli_fetch_assoc($result);
        if ($toko && $toko['is_maintenance'] === 'Y') $is_lock = true;
    }
    echo json_encode(['maintenance' => $is_lock]);
    exit;
}

if ($conn) {
    $result = mysqli_query($conn, "SELECT is_maintenance FROM master_toko WHERE kode_toko = '$kode_toko_aktif' LIMIT 1");
    $toko = mysqli_fetch_assoc($result);
    
    if ($toko && $toko['is_maintenance'] === 'Y' && (!isset($_COOKIE['admin_bypass_maintenance']) || $_COOKIE['admin_bypass_maintenance'] !== 'kasirkita')) {
        http_response_code(503);
        $base_path = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $url_logo  = $base_path . 'assets/img/icon_kasir.png';
        include 'C:/xampp/htdocs/kasirpos/public/maintenance_view.php';
        exit;
    }
    mysqli_close($conn);
}