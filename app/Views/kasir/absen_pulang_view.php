<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Absen Pulang - Senja Coffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #1a0a0a 0%, #f01212 100%);
        height: 100vh !important;
        /* Paksa tetap 100vh */
        display: flex !important;
        /* Paksa tetap flex agar di tengah */
        align-items: center;
        justify-content: center;
        margin: 0;
        overflow: hidden;
    }

    /* Mencegah pergeseran layout saat SweetAlert muncul */
    body.swal2-shown {
        overflow: hidden !important;
        padding-right: 0 !important;
        display: flex !important;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 25px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        padding: 40px;
        text-align: center;
        color: white;
        max-width: 450px;
        width: 90%;
        animation: fadeInScale 0.8s ease-out;
    }

    .btn-pulang {
        background: #fff;
        color: #f01212;
        border: none;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 18px;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        width: 100%;
        /* Tombol full width biar rapi */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .btn-pulang:hover {
        background: #1a0a0a;
        color: white;
        transform: translateY(-5px);
    }

    .time-now {
        font-size: 14px;
        opacity: 0.8;
        margin-bottom: 20px;
        display: block;
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
        <div class="mb-4"><i class="fas fa-fingerprint fa-4x"></i></div>
        <h2 class="fw-bold mb-1">Halo, <?= session()->get('nama_user'); ?>!</h2>
        <span class="time-now">
            <i class="far fa-calendar-alt me-1"></i> <?= date('d M Y'); ?>
            <i class="far fa-clock ms-2 me-1"></i> <span id="clock">--:--</span>
        </span>
        <br>
        <p>Terima kasih kerja kerasnya hari ini, <br><strong><?= session()->get('nama_user'); ?></strong></p>
        <p>Klik tombol di bawah untuk Absen Pulang</p>
        <br>
        <form action="<?= site_url('kasir/simpan_absen_pulang'); ?>" method="POST" id="formPulang">
            <?= csrf_field(); ?>
            <button type="submit" class="btn-pulang">
                <i class="fas fa-fingerprint me-2"></i> Absen Pulang
            </button>
        </form>
    </div>

    <script>
    // Update Jam
    function updateClock() {
        const clockEl = document.getElementById('clock');
        if (clockEl) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            clockEl.textContent = hours + ':' + minutes;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // SWEETALERT OTOMATIS
    // SWEETALERT OTOMATIS
    <?php if (session()->getFlashdata('sukses_absen')): ?>
    Swal.fire({
        title: 'Berhasil Pulang!',
        text: 'Absen Pulang Berhasil... Terima Kasih!!!',
        icon: 'success',
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
        allowOutsideClick: false,
        heightAuto: false,
        background: '#fff',
        iconColor: '#f01212',
        backdrop: `rgba(26, 10, 10, 0.8)`
    }).then((result) => {
        // Paksa pindah ke logout untuk destroy session
        window.location.href = "<?= base_url('logout'); ?>";
    });
    <?php endif; ?>

    // Animasi Loading saat diklik
    document.getElementById('formPulang').addEventListener('submit', function() {
        const btn = document.querySelector('.btn-pulang');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.8';
    });
    </script>
</body>

</html>