<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Absensi Kasir - Senja Coffee</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

    <style>
    /* 1. BASE STYLE & CENTERING (Tetap sama agar kartu di tengah) */
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #040a04 0%, #12f0d2 100%);
        background-attachment: fixed;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 10px 0;
        /* Beri ruang tipis di atas bawah */
        overflow-y: auto;
    }

    /* 2. GLASSMORPHISM CARD (Tinggi otomatis, tidak kaku) */
    .glass-card {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 25px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        text-align: center;
        color: white;
        max-width: 450px;
        width: 90%;
        margin: auto;
        animation: fadeInScale 0.8s ease-out;

        /* --- KUNCI UTAMA PERKECIL UKURAN DI TABLET LANDSCAPE --- */
        /* Menggunakan Media Query untuk skala global di layar pendek */
        padding: 30px;
        /* Default untuk portrait/mobile */
    }

    /* 3. MEDIA QUERY KHUSUS UNTUK PERKECIL SKALA GLOBAL (Tablet Landscape/Layar Pendek) */
    @media (orientation: landscape) and (max-height: 750px) {

        /* Perkecil padding kartu utama */
        .glass-card {
            padding: 15px 25px;
            border-radius: 20px;
        }

        /* Perkecil Ukuran Ikon */
        .icon-box {
            width: 60px !important;
            height: 60px !important;
            font-size: 25px !important;
            margin-bottom: 10px !important;
        }

        /* Perkecil Teks Judul & Jam */
        h2 {
            font-size: 1.25rem !important;
        }

        .time-now {
            margin-bottom: 10px !important;
            font-size: 13px !important;
        }

        p.mb-4 {
            margin-bottom: 10px !important;
            font-size: 13px !important;
        }

        /* Perkecil Kotak Kamera (Paling Penting untuk Hemat Ruang) */
        #my_camera {
            max-width: 240px !important;
            height: 180px !important;
            /* Tinggi dikurangi */
            border-width: 2px !important;
            margin-bottom: 10px !important;
        }

        /* Perkecil Elemen Form */
        .form-select-custom {
            padding: 8px !important;
            margin-bottom: 10px !important;
            font-size: 14px !important;
        }

        /* Perkecil Tombol Absen */
        .btn-absen {
            padding: 10px !important;
            font-size: 14px !important;
            margin-top: 5px !important;
        }

        /* Perkecil Jarak Tombol Kembali */
        .mt-4.pt-2 {
            margin-top: 10px !important;
            padding-top: 0 !important;
        }
    }

    /* 4. DEFAULT STYLING UNTUK ELEMEN (Di luar media query agar tidak hilang) */
    .icon-box {
        width: 90px;
        height: 90px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: #12f0d2;
        font-size: 40px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .time-now {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 20px;
        display: block;
        font-weight: 300;
    }

    .form-select-custom {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50px;
        padding: 12px 15px;
        font-weight: 600;
        text-align: center;
        color: #333;
        margin-bottom: 20px;
        cursor: pointer;
    }

    #my_camera {
        width: 100% !important;
        max-width: 320px;
        height: 240px !important;
        margin: 0 auto;
        border-radius: 20px;
        overflow: hidden;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }

    #my_camera video {
        object-fit: cover !important;
        width: 100% !important;
        height: 100% !important;
    }

    .btn-absen {
        background: #fff;
        color: #040a04;
        border: none;
        padding: 15px;
        border-radius: 50px;
        font-weight: 800;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-back {
        display: inline-flex;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 13px;
    }

    @keyframes fadeInScale {
        0% {
            opacity: 0;
            transform: scale(0.9);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    </style>
</head>

<body>

    <div class="glass-card">
        <div class="icon-box">
            <i class="fas fa-fingerprint"></i>
        </div>

        <h2 class="fw-bold mb-1">Halo, <?= session()->get('nama_user'); ?>!</h2>
        <span class="time-now">
            <i class="far fa-calendar-alt me-1"></i> <?= date('d M Y'); ?>
            <i class="far fa-clock ms-2 me-1"></i> <span id="clock">--:--</span>
        </span>

        <p class="mb-4" style="font-size: 15px; opacity: 0.9;">
            Semangat kerja! <br> Silakan pilih shift dan absen masuk.
        </p>

        <form action="<?= base_url('kasir/simpan_absen'); ?>" method="POST" id="formAbsen">
            <input type="hidden" name="image_tag" id="image_tag">

            <select name="shift" class="form-select form-select-custom shadow-none mb-3" required>
                <option value="" disabled selected>-- PILIH SHIFT --</option>
                <option value="Pagi">SHIFT PAGI (08:00)</option>
                <option value="Sore">SHIFT SORE (14:01)</option>
                <option value="Malam">SHIFT MALAM (21:01)</option>
            </select>

            <div class="mb-3 text-center">
                <small class="text-white-50 d-block mb-2" style="font-size: 10px; letter-spacing: 1px;">VERIFIKASI WAJAH
                    (LIVE)</small>
                <div id="my_camera"></div>
            </div>

            <button type="submit" class="btn-absen">
                <i class="fas fa-fingerprint me-2"></i> ABSEN MASUK
            </button>
        </form>

        <div class="mt-4 pt-2">
            <div class="mt-4 pt-2">
                <a href="<?= base_url('login'); ?>" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i> KEMBALI KE LOGIN
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // 1. Fungsi Jam Realtime
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.getHours().toString().padStart(2, '0') + ':' +
            now.getMinutes().toString().padStart(2, '0') + ':' +
            now.getSeconds().toString().padStart(2, '0');
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 2. Konfigurasi Kamera
    Webcam.set({
        width: 635,
        height: 530,
        dest_width: 640,
        dest_height: 480,
        image_format: 'jpeg',
        jpeg_quality: 90,
        facingMode: "user"
    });
    Webcam.attach('#my_camera');

    // 3. Gabungkan Logika Absen (HANYA SATU LISTENER)
    document.getElementById('formAbsen').addEventListener('submit', function(e) {
        e.preventDefault(); // Stop pengiriman ganda

        const form = this;
        const btn = form.querySelector('.btn-absen');
        const shift = form.querySelector('select[name="shift"]').value;

        if (!shift) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Silakan pilih shift kerja dulu!'
            });
            return;
        }

        // Matikan tombol agar tidak diklik berkali-kali
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> MEMPROSES...';

        // Ambil Foto
        Webcam.snap(function(data_uri) {
            // Masukkan foto ke input hidden
            document.getElementById('image_tag').value = data_uri;

            // Kirim data menggunakan Fetch (AJAX)
            const formData = new FormData(form);
            fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    // Jika sukses, tampilkan SweetAlert
                    Swal.fire({
                        title: 'Berhasil Absen!',
                        text: 'Selamat bekerja!',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        heightAuto: false,
                        scrollbarPadding: false
                    }).then(() => {
                        // Redirect setelah alert selesai
                        window.location.href = "<?= site_url('admin/dashboard') ?>";
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan absen.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-fingerprint me-2"></i> ABSEN MASUK';
                });
        });
    });
    </script>
</body>

</html>