<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Transaksi Detail - Senja Coffee</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    body {
        background-color: #ffffff;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #333;
    }

    .header-print {
        border-bottom: 3px solid #198754;
        padding-bottom: 15px;
        margin-bottom: 30px;
    }

    .table th {
        background-color: #f8f9fa !important;
        color: #212529;
    }

    /* Menghilangkan header dan footer default dari browser saat dicetak */
    @media print {
        @page {
            margin: 0;
        }

        body {
            padding: 20px;
        }

        .no-print {
            display: none !important;
        }
    }
    </style>
</head>

<body onload="window.print()">
    <div class="container py-4">

        <div class="header-print d-flex justify-content-between align-items-center">
            <div>
                <h3 class="text-success fw-bold mb-1">Senja Coffee</h3>
                <h5 class="text-secondary m-0">Laporan Transaksi Detail</h5>
                <p class="text-muted small mb-0 mt-1">Rekapitulasi rincian produk terjual beserta harga satuannya</p>
            </div>
            <div class="text-end">
                <p class="mb-1 small"><strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i'); ?></p>
                <p class="mb-0 small"><strong>Status:</strong> <span class="text-success">Valid & Resmi</span></p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Nama Produk</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-center">Total Qty</th>
                        <th class="text-end">Total Pendapatan (Subtotal)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($detail_transaksi)): ?>
                    <?php $no = 1; $grand_total = 0; $grand_qty = 0; ?>
                    <?php foreach ($detail_transaksi as $row): ?>
                    <?php 
                                $grand_qty += $row['total_qty'];
                                $grand_total += $row['total_subtotal'];
                                $harga_satuan = $row['total_subtotal'] / $row['total_qty'];
                            ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="fw-bold text-dark"><?= esc($row['nama_produk']); ?></td>
                        <td class="text-end text-muted">Rp <?= number_format($harga_satuan, 0, ',', '.'); ?></td>
                        <td class="text-center"><?= $row['total_qty']; ?> Terjual</td>
                        <td class="text-end fw-bold text-success">Rp
                            <?= number_format($row['total_subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>

                    <tr class="table-light border-dark border-2">
                        <td colspan="3" class="text-end fw-bold">Total Keseluruhan :</td>
                        <td class="text-center fw-bold text-dark"><?= $grand_qty; ?></td>
                        <td class="text-end fw-bold text-success fs-5">Rp
                            <?= number_format($grand_total, 0, ',', '.'); ?></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada data transaksi.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="no-print mt-5 d-flex justify-content-end gap-2">
            <button class="btn btn-secondary px-4 rounded-pill" onclick="window.close()">
                <i class="fas fa-times me-1"></i> Tutup
            </button>
            <button class="btn btn-success px-4 rounded-pill" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Cetak Laporan
            </button>
        </div>

    </div>
</body>

</html>