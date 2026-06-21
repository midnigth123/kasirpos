<?php
/**
 * @var array  $row
 * @var string $bulan
 * @var string $tahun
 */

// 1. Definisikan semua angka (casting ke float agar aman untuk matematika)
$gaji_pokok = (float)($row['total_hadir'] * $row['nominal_per_shift']);
$tunjangan  = (float)($row['tunjangan_jabatan'] ?? 0);
$kasbon     = (float)($row['total_kasbon'] ?? 0); // JEDOR! Ini nominal kasbonnya
$pot_telat  = (float)($row['total_telat'] * $row['potongan_telat']);

// 🚀 TAMBAHAN 1: Ambil data nominal lembur dari database pusat (JEDOR!)
$uang_lembur = (float)($row['total_lembur'] ?? 0);

// 2. RUMUS YANG DIPERBARUI: (Gaji + Tunjangan + Uang Lembur) - (Kasbon + Telat)
$total_akhir = ($gaji_pokok + $tunjangan + $uang_lembur) - ($kasbon + $pot_telat);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Slip Gaji - <?= $row['nama_user'] ?></title>
    <style>
    body {
        font-family: 'Courier New', Courier, monospace;
        width: 100%;
        max-width: 300px;
        margin: auto;
        padding: 10px;
        color: #000;
    }

    .text-center {
        text-align: center;
    }

    .header {
        margin-bottom: 15px;
        border-bottom: 1px dashed #000;
        padding-bottom: 10px;
    }

    .row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .footer {
        margin-top: 20px;
        text-align: center;
        font-size: 11px;
    }

    .total {
        border-top: 1px solid #000;
        margin-top: 10px;
        padding-top: 5px;
        font-weight: bold;
        font-size: 14px;
    }

    @media print {
        .no-print {
            display: none;
        }

        body {
            margin: 0;
            padding: 0;
        }
    }
    </style>
</head>

<body onload="window.print()">
    <div class="header text-center">
        <h3 style="margin:0"><?= strtoupper($pengaturan['nama_toko'] ??'') ?></h3>
        <small><?= $pengaturan['alamat'] ?? 'Premium Coffee & Space' ?></small>

        <div style="margin-top: 5px;">
            <small>SLIP GAJI: <?= $bulan ?>/<?= $tahun ?></small>
        </div>
    </div>

    <div class="row">
        <span>Nama:</span>
        <span style="font-weight: bold;"><?= $row['nama_user'] ?></span>
    </div>
    <div class="row">
        <span>Jabatan:</span>
        <span><?= ucwords($row['role'] ?? $row['jabatan']) ?></span>
    </div>
    <div class="row">
        <span>Total Hadir:</span>
        <span><?= $row['total_hadir'] ?> Shift</span>
    </div>

    <hr style="border: 0.5px dashed #000">

    <div class="row">
        <span>Gaji Pokok:</span>
        <span><?= number_format($gaji_pokok, 0, ',', '.') ?></span>
    </div>
    <div class="row">
        <span>Tunjangan:</span>
        <span><?= number_format($tunjangan, 0, ',', '.') ?></span>
    </div>

    <div class="row">
        <span>Uang Lembur:</span>
        <span><?= number_format($uang_lembur, 0, ',', '.') ?></span>
    </div>

    <?php if($kasbon > 0): ?>
    <div class="row">
        <span>Kasbon:</span>
        <span style="color:red">-<?= number_format($kasbon, 0, ',', '.') ?></span>
    </div>
    <?php endif; ?>

    <div class="row">
        <span>Denda Telat (<?= $row['total_telat'] ?>x):</span>
        <span style="color:red">-<?= number_format($pot_telat, 0, ',', '.') ?></span>
    </div>

    <div class="row total">
        <span>TOTAL DITERIMA:</span>
        <span>Rp <?= number_format($total_akhir, 0, ',', '.') ?></span>
    </div>

    <div class="footer">
        <p>Terima kasih atas dedikasimu!<br>Barista & Crew <?= strtoupper($pengaturan['nama_toko'] ??'') ?></p>
        <button class="no-print" onclick="window.print()" style="margin-top:10px; cursor:pointer;">Cetak Ulang</button>
    </div>
</body>

</html>