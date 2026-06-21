<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-md-12">
            <h5 class="fw-bold text-dark">Laporan Rincian Waste (Barang Rusak)</h5>
            <p class="text-muted small">Data penyusutan stok berdasarkan harga modal bahan baku.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-body">

            <div class="d-print-none mb-4">
                <form action="" method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted">Mulai Tanggal</label>
                        <input type="date" name="tgl_mulai" class="form-control form-control-sm shadow-none"
                            value="<?= $tgl_mulai ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" class="form-control form-control-sm shadow-none"
                            value="<?= $tgl_selesai ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark btn-sm w-100 rounded-pill px-3">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" onclick="window.print()"
                            class="btn btn-outline-secondary btn-sm w-100 rounded-pill shadow-none">
                            <i class="fas fa-print me-1"></i> Cetak Laporan
                        </button>
                    </div>
                </form>
            </div>

            <div class="d-none d-print-block text-center mb-4">
                <h4 class="fw-bold mb-1">SENJA COFFEE</h4>
                <h5 class="mb-1">Laporan Waste Barang</h5>
                <p class="small">Periode: <?= date('d/m/Y', strtotime($tgl_mulai)) ?> s/d
                    <?= date('d/m/Y', strtotime($tgl_selesai)) ?></p>
                <hr>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" style="font-size: 13px;">
                    <thead class="bg-light text-muted uppercase">
                        <tr>
                            <th class="ps-3 py-3">No</th>
                            <th>Tanggal & Jam</th>
                            <th>Nama Produk/Bahan</th>
                            <th>Qty</th>
                            <th>Harga Modal</th>
                            <th>Total Rugi</th>
                            <th>Alasan</th>
                            <th class="pe-3 text-end">Input Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 
                        $total_kerugian = 0; 
                        if(empty($waste)): 
                        ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted italic">Tidak ada data waste pada periode
                                ini.</td>
                        </tr>
                        <?php 
                        else:
                            foreach($waste as $w): 
                                $subtotal = $w->qty_waste * $w->harga_beli;
                                $total_kerugian += $subtotal;
                        ?>
                        <tr>
                            <td class="ps-3"><?= $no++ ?></td>
                            <td class="text-muted"><?= date('d/m/Y H:i', strtotime($w->created_at)) ?></td>
                            <td class="fw-bold text-dark"><?= $w->nama_produk ?></td>
                            <td class="text-danger fw-bold">- <?= $w->qty_waste ?></td>
                            <td>Rp <?= number_format($w->harga_beli, 0, ',', '.') ?></td>
                            <td class="fw-bold">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                            <td><span
                                    class="badge bg-light text-dark border fw-normal py-1 px-2"><?= $w->alasan ?></span>
                            </td>
                            <td class="pe-3 text-end fw-semibold"><?= $w->nama_user ?? 'Admin' ?></td>
                        </tr>
                        <?php 
                            endforeach; 
                        endif;
                        ?>
                    </tbody>
                    <?php if(!empty($waste)): ?>
                    <tfoot class="bg-light border-top-0">
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end py-3">TOTAL ESTIMASI KERUGIAN :</td>
                            <td class="text-danger py-3" colspan="3">
                                Rp <?= number_format($total_kerugian, 0, ',', '.') ?>
                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>

            <div class="d-none d-print-block mt-5">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-5">Padang, <?= date('d M Y') ?><br>Manajer Operasional,</p>
                        <br><br>
                        <p class="fw-bold">( ................................ )</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media print {

    .btn,
    form,
    .card-header,
    .navbar,
    .sidebar {
        display: none !important;
    }

    .card {
        border: none !important;
        shadow: none !important;
    }

    body {
        background-color: white !important;
    }
}
</style>

<?= $this->endSection() ?>