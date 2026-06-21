<?php

/**
 * @var string $bulan
 * @var string $tahun
 * @var array $cashflow
 */
// Header agar browser mendownload file sebagai Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Cashflow_" . $bulan . "_" . $tahun . ".xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table border="1">
    <thead>
        <tr>
            <th colspan="6" style="font-weight:bold; font-size: 14pt; text-align:center;">
                <?= strtoupper($pengaturan['nama_toko'] ?? 'SENJA COFFEE') ?>
            </th>
        </tr>
        <tr>
            <th colspan="6" style="text-align:center; font-weight:bold;">
                LAPORAN ARUS KAS - Periode: <?= $bulan ?> / <?= $tahun ?>
            </th>
        </tr>
        <tr style="background-color: #cccccc;">
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Keterangan</th>
            <th style="text-align:right;">Masuk (Rp)</th>
            <th style="text-align:right;">Keluar (Rp)</th>
            <th style="text-align:right;">Saldo Akhir (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $t_masuk = 0; $t_keluar = 0;
        foreach($cashflow as $cf): 
            $t_masuk += $cf['masuk'];
            $t_keluar += $cf['keluar'];
        ?>
        <tr>
            <td><?= date('d/m/Y', strtotime($cf['tanggal'])) ?></td>
            <td><?= $cf['kategori'] ?></td>
            <td><?= $cf['keterangan'] ?></td>
            <td align="right"><?= $cf['masuk'] ?></td>
            <td align="right"><?= $cf['keluar'] ?></td>
            <td align="right"><?= $cf['saldo_akhir'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr style="font-weight:bold; background-color: #eeeeee;">
            <td colspan="3" align="center">TOTAL PERIODE INI</td>
            <td align="right"><?= $t_masuk ?></td>
            <td align="right"><?= $t_keluar ?></td>
            <td align="right"><?= $t_masuk - $t_keluar ?></td>
        </tr>
    </tfoot>
</table>