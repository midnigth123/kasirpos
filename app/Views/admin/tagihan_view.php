<?php
/**
 * @var string $pesan
 */
// Kunci utama: Jangan pakai $this->extend() di sini agar tidak masuk ke dalam layout dashboard!
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masa Langganan Habis</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('icon_kasir.ico') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.css">
    <style>
    body {
        background-color: #f8f9fa;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        font-family: 'Segoe UI', -apple-system, sans-serif;
    }

    .card-tagihan {
        max-width: 550px;
        width: 100%;
        border-radius: 15px;
        border: none;
    }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center">
        <div class="card card-tagihan shadow p-5 text-center bg-white">
            <div class="text-danger mb-4">
                <i class="fas fa-wallet fa-5x animate__animated animate__bounceIn"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">Layanan Ditangguhkan Sementara</h3>

            <p class="text-muted mb-1">Masa aktif langganan toko Anda telah berakhir.</p>
            <p class="text-danger fw-bold mb-4" style="font-size: 1.1rem;">
                <i class="fas fa-calendar-alt me-1"></i> Tanggal Jatuh Tempo:
                <?= date('d-m-Y', strtotime($tgl_selesai ?? '09-06-2026')) ?>
            </p>

            <div class="alert alert-warning text-start" role="alert">
                <i class="fas fa-info-circle me-2"></i> Untuk melanjutkan penggunaan aplikasi
                <strong>KasirKita</strong>, silakan lakukan perpanjangan paket atau hubungi Admin Penjualan.
            </div>

            <div class="d-grid gap-2 mt-4">
                <?php 
                    $namaToko = session()->get('nama_toko') ?? 'Toko Klien';
                    
                    $pesanWa = "Halo Admin KasirKita, saya mau perpanjang langganan untuk:\n\n"
                            . "• Nama Outlet : " . $namaToko . "\n\n"
                            . "Mohon info prosedur pembayarannya. Terima kasih.";
                            
                    $urlWa = "https://wa.me/628126639311?text=" . urlencode($pesanWa);
                ?>

                <a href="<?= $urlWa ?>" target="_blank" class="btn btn-success btn-lg py-2 fw-semibold">
                    <i class="fab fa-whatsapp me-2"></i>Hubungi Admin (WhatsApp)
                </a>

                <!-- <a href="<?= site_url('logout') ?>" class="btn btn-light btn-sm text-muted mt-2">
                    <i class="fas fa-sign-out-alt me-1"></i> Keluar ke Halaman Login
                </a> -->
            </div>
        </div>
    </div>

    <script>
    var isRedirecting = false;

    setInterval(function() {
        if (isRedirecting) return;

        // Menembak rute cek lisensi dengan anti-cache timestamp
        var URL_Cek = "<?= site_url('cek_lisensi_toko') ?>?t=" + new Date().getTime();

        fetch(URL_Cek, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                // 🎯 JIKA BOS SUDAH PERPANJANG (Status aktif Y dan Expired False)
                if (data.status_aktif === 'Y' && data.apakah_expired === false) {
                    isRedirecting = true; // Kunci radar

                    // 🔥 BYPASS MUTLAK: Lempar ke clear_session untuk dihancurkan session-nya 
                    // tanpa melewati gerbang log close-kasir / absen pulang!
                    window.location.replace("<?= site_url('clear_session') ?>");
                }
            })
            .catch(function(error) {
                console.log("Menunggu perpanjangan lisensi dari pusat...");
            });
    }, 5000); // Cek berkala setiap 5 detik
    </script>
</body>

</html>