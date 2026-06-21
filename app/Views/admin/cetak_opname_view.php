<?php

/**
 * 
 * @var array $items
 * @var array $header
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cetak Opname - <?= $header['kode_opname'] ?></title>
    <style>
        /* Gaya kertas thermal / struk bersih */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            /* Sedikit dikecilkan agar muat di kertas thermal */
            color: #000;
            margin: 0;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        /* KUNCI AGAR SEJAJAR: table-layout fixed */
        table.items {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.items th {
            border-bottom: 1px solid #000;
            padding: 5px 0;
            text-align: left;
        }

        table.items td {
            padding: 8px 0;
            vertical-align: top;
            border-bottom: 0.5px solid #eee;
        }

        /* Mengunci lebar kolom (Total 100%) */
        .col-nama {
            width: 40%;
        }

        .col-sistem {
            width: 20%;
            text-align: center;
        }

        .col-fisik {
            width: 20%;
            text-align: center;
        }

        .col-selisih {
            width: 20%;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin: 0; text-transform: uppercase;">Laporan Stok Opname</h2>
        <p style="margin: 5px 0;">Senja Coffee & Eatery</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="100">Kode Transaksi</td>
            <td width="10">:</td>
            <td><strong><?= $header['kode_opname'] ?></strong></td>
        </tr>
        <tr>
            <td>Waktu Audit</td>
            <td>:</td>
            <td><?= date('d/m/Y H:i', strtotime($header['created_at'])) ?> WIB</td>
        </tr>
        <tr>
            <td>Petugas</td>
            <td>:</td>
            <td><?= $header['nama_user'] ?></td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th class="text-center">Sistem</th>
                <th class="text-center">Fisik</th>
                <th class="text-center">Selisih</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?= $item['nama_produk'] ?><br>
                        <small style="color: #666;">Ket: <?= $item['keterangan'] ?: '-' ?></small>
                    </td>
                    <td class="text-center"><?= $item['stok_sistem'] ?></td>
                    <td class="text-center"><strong><?= $item['stok_fisik'] ?></strong></td>
                    <td class="text-center">
                        <?= ($item['selisih'] > 0 ? '+' : '') . $item['selisih'] ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh sistem pada: <?= date('d/m/Y H:i:s') ?>
    </div>

    <div class="no-print" style="margin-top: 50px; text-align: center; border-top: 1px solid #ccc; padding-top: 20px;">
    </div>