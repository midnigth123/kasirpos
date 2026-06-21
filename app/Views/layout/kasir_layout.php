<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - <?= esc(session()->get('nama_toko') ?? 'Aplikasi Kasir') ?></title>

    <!-- 🎯 FIX UTAMA: Mengganti logo orange di tab browser (Favicon) -->
    <link rel="shortcut icon" href="<?= base_url('assets/img/icon_kasir.png') ?>" type="image/x-icon">
    <link rel="icon" href="<?= base_url('assets/img/icon_kasir.png') ?>" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <!-- NAVBAR UTAMA -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3 mb-4">
        <div class="container-fluid">

            <!-- BRANDING LOGO & NAMA OUTLET LOGGED IN -->
            <a class="navbar-brand fw-bold text-success d-flex align-items-center gap-3" href="#">

                <!-- LOGO KASIR -->
                <div class="d-flex align-items-center justify-content-center"
                    style="width: 55px; height: 55px; overflow: hidden; flex-shrink: 0;">
                    <img src="<?= base_url('assets/img/icon_kasir.png') ?>" alt="Logo Toko"
                        style="width: 100%; height: 100%; object-fit: contain;">
                </div>

                <!-- TEKS OUTLET -->
                <span>
                    <?= esc(session()->get('nama_toko') ?? 'Senja Coffee & Eatery') ?> - Kasir
                </span>

            </a>

            <!-- INFORMASI PETUGAS & TOMBOL AKSI -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small me-2">
                    <i class="fas fa-user me-1"></i> Kasir:
                    <strong class="text-dark">
                        <?= esc(session()->get('nama_user') ?? 'Guest') ?>
                    </strong>
                </span>

                <a href="<?= site_url('admin/dashboard') ?>"
                    class="btn btn-outline-success btn-sm fw-semibold d-flex align-items-center gap-1.5 shadow-sm"
                    style="border-radius: 8px; transition: all 0.2s; padding: 0.35rem 0.75rem;">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>

                <a href="<?= site_url('kasir/close-kasir') ?>"
                    class="btn btn-outline-danger btn-sm fw-semibold d-flex align-items-center gap-1.5 shadow-sm"
                    style="border-radius: 8px; transition: all 0.2s; padding: 0.35rem 0.75rem;">
                    <i class="fas fa-lock"></i>
                    <span>Tutup Kasir</span>
                </a>
            </div>

        </div>
    </nav>

    <!-- KONTEN HALAMAN -->
    <div class="container-fluid px-4">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // 🚀 SUNTIK LANGSUNG: Mencetak URL gerbang utama murni dari CodeIgniter 
        // Ini menjamin alamat check_status 100% akurat di localhost maupun Ngrok!
        var URL_Gembok = "<?= site_url('?check_status_maintenance=1') ?>";

        setInterval(function() {
            // Tambahkan timestamp acak (?t=) biar browser tidak malas mengambil dari cache
            var URL_Final = URL_Gembok + "&t=" + new Date().getTime();

            fetch(URL_Final, {
                    cache: 'no-store'
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    // Jika server berbisik TRUE (Maintenance diaktifkan oleh Bos)
                    if (data.maintenance === true) {
                        // Paksa layar kasir reload total biar langsung terkunci ke halaman gembok!
                        window.location.reload();
                    }
                })
                .catch(function(error) {
                    console.log("Memantau sinyal gembok KasirKita...");
                });
        }, 4000); // ⏱️ Cek berkala setiap 4 Saja
    });
    </script>
</body>

</html>