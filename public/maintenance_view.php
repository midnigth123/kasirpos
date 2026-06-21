<?php

/**
 * @var string $url_logo
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
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
        max-width: 500px;
        margin: 0 auto;
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border-top: 5px solid #198754;
    }

    .logo-container {
        width: 110px;
        height: 110px;
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
        fetch(window.location.href + '?check_status_maintenance=1', {
                cache: 'no-store'
            })
            .then(r => r.json()).then(d => {
                if (!d.maintenance) window.location.reload();
            });
    }, 5000);
    </script>
</body>

</html>