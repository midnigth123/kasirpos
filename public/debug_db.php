<?php
$conn = mysqli_connect("localhost", "root", "", "db_kasir");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$kode_toko = "kasirpos"; // Ganti ini sesuai isi kolom kode_toko Bos di DB
$result = mysqli_query($conn, "SELECT * FROM master_toko WHERE kode_toko = '$kode_toko'");

if (mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    echo "Data ditemukan: <pre>" . print_r($data, true) . "</pre>";
} else {
    echo "Data TIDAK DITEMUKAN untuk kode: " . $kode_toko;
}
?>