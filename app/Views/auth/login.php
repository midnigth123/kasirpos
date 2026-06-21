<!DOCTYPE html>
<html lang="id">

<head>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('icon_kasir.ico') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Login - KasirKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    body {
        background-color: #f8f9fa;
        /* Ganti ke Poppins agar konsisten dan modern */
        font-family: 'Poppins', sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
    }

    .login-wrapper {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        width: 90%;
        /* UKURAN DIKECILKAN: dari 900px ke 750px atau 800px */
        max-width: 800px;
        display: flex;
        min-height: 500px;
        /* Tinggi yang pas, tidak terlalu menjulang */
    }

    .form-side {
        /* PADDING DIKECILKAN: agar tidak terlalu lowong */
        padding: 40px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .image-side {
        flex: 1;
        background: url('https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&q=80&w=800') center/cover no-repeat;
        position: relative;
        /* Sembunyikan gambar di layar HP agar tetap rapi */
    }

    /* Responsivitas untuk Tablet & HP */
    @media (max-width: 768px) {
        .login-wrapper {
            max-width: 400px;
            /* Sangat compact di HP */
        }

        .image-side {
            display: none;
            /* Hilangkan sisi gambar jika layar sempit */
        }
    }

    .logo-circle {
        width: 50px;
        /* Perkecil sedikit */
        height: 50px;
        background-color: var(--hover-bg, #e9ecef);
        color: #198754;
        /* Gunakan hijau brand Senja Coffee */
        border-radius: 12px;
        /* Ganti ke kotak tumpul agar lebih modern */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }

    h3 {
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 5px;
    }

    .text-muted {
        font-size: 0.85rem;
        margin-bottom: 25px;
    }

    /* Input Field yang lebih compact */
    .form-control,
    .input-group-text {
        background-color: #f8f9fa !important;
        border: 1px solid #eee !important;
        padding: 10px 15px !important;
        /* Perkecil padding input */
        font-size: 0.9rem;
    }

    .btn-primary {
        background-color: #198754;
        /* Hijau Senja Coffee */
        border: none;
        padding: 12px;
        font-weight: 700;
        border-radius: 10px;
        margin-top: 10px;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #146c43;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
    }
    </style>
</head>

<body>

    <div class="container px-4">
        <div class="login-wrapper row mx-auto">

            <div class="col-md-6 form-side">
                <div class="d-flex align-items-center justify-content-center"
                    style="width: 55px; height: 55px; overflow: hidden;">
                    <img src="<?= base_url('assets/img/icon_kasir.png') ?>" alt="Logo Toko"
                        style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <h3 class="fw-bold mb-1">KasirKita Pos</h3>
                <p class="text-muted small mb-4">Dashboard Admin atau Kasir Anda.</p>

                <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger d-flex align-items-center py-2 text-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/proses_login') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold small">KODE TOKO</label>
                        <div class="input-group">
                            <span class="input-group-text no-border-right rounded-start">
                                <i class="fas fa-key"></i>
                            </span>
                            <input type="text" name="kode_toko" class="form-control no-border-left rounded-end"
                                placeholder="Contoh: SENJA01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold small">USERNAME</label>
                        <div class="input-group">
                            <span class="input-group-text no-border-right rounded-start"><i
                                    class="fas fa-user"></i></span>
                            <input type="text" name="username" class="form-control no-border-left rounded-end"
                                placeholder="Masukkan username" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted fw-semibold small">PASSWORD</label>
                        <div class="input-group">
                            <span class="input-group-text no-border-right rounded-start"><i
                                    class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" class="form-control no-border-left"
                                placeholder="••••••••" required>
                            <span class="input-group-text rounded-end bg-transparent border-start-0"
                                style="cursor: pointer;" onclick="togglePassword()">
                                <i class="fas fa-eye text-muted" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3 shadow-sm">
                        LOGIN <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="text-center mt-4">
                    <span class="text-muted small">Hak Cipta &copy; 2026 DnDev</span>
                </div>
            </div>

            <div class="col-md-6 image-side d-none d-md-block">
            </div>

        </div>
    </div>

    <script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // 2. TAMBAHKAN SCRIPT ALERT ABSEN DI SINI
    <?php if (session()->getFlashdata('pesan')) : ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= session()->getFlashdata('pesan') ?>',
        timer: 4000,
        showConfirmButton: false,
        timerProgressBar: true,
        background: '#ffffff',
        iconColor: '#198754', // Hijau Senja Coffee
    });
    <?php endif; ?>

    // Alert untuk Error (Opsional tapi bagus untuk konsistensi)
    <?php if (session()->getFlashdata('error')) : ?>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonColor: '#198754'
    });
    <?php endif; ?>

    //Auto Reload Maintenance
    document.addEventListener("DOMContentLoaded", function() {
        setInterval(function() {
            // Membaca root path aplikasi murni (index.php) agar bebas dari interceptor auth
            var rootPath = window.location.pathname.split('index.php')[0] + 'index.php';
            var URL_Final = window.location.origin + rootPath + '?check_status_maintenance=1&t=' +
                new Date().getTime();

            fetch(URL_Final, {
                    cache: 'no-store'
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    // 🎯 KETANGKAP BASAH: Jika data JSON dari index.php murni mendeteksi maintenance = true
                    if (data.maintenance === true) {
                        // Paksa form login ini reload seketika demi menutup gerbang masuk!
                        window.location.reload();
                    }
                })
                .catch(function(error) {
                    console.log("Memantau radar gembok KasirKita...");
                });
        }, 4000); // ⏱️ Scan status gembok server setiap 4 detik sekali
    });
    </script>

</body>

</html>