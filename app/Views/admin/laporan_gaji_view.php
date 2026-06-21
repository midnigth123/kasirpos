<?php
/**
 * @var string $bulan_pilih
 * @var string $tahun_pilih
 * @var array $rekap
 */
?>
<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="card shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-wallet me-2"></i>Rekap Gaji Bulanan - Senja Coffee
            </h6>
            <div class="badge bg-light text-dark border">
                Periode: <?= date('F', mktime(0, 0, 0, $bulan_pilih, 1)) ?> <?= $tahun_pilih ?>
            </div>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="bulan" class="form-select border-0 bg-light">
                        <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?= sprintf('%02d', $m) ?>"
                            <?= $bulan_pilih == sprintf('%02d', $m) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="tahun" class="form-control border-0 bg-light"
                        value="<?= $tahun_pilih ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Kru</th>
                            <th>Hadir</th>
                            <th>Gaji Pokok</th>
                            <th>Tunjangan</th>
                            <th class="text-success">Uang Lembur</th>
                            <th>Pot. Telat</th>
                            <th class="text-danger text-center">Kasbon</th>
                            <th>Total Diterima</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rekap as $r) : ?>
                        <?php 
                            // Proteksi nilai null agar tetap dianggap 0 (JEDOR!)
                            $nom_shift = $r['nominal_per_shift'] ?? 0;
                            $tunjangan = $r['tunjangan_jabatan'] ?? 0;
                            $pot_telat = $r['potongan_telat'] ?? 0;
                            
                            // 🚀 TAMBAHAN 2: Tangkap data lembur dari subquery controller pusat
                            $uang_lembur = $r['total_lembur'] ?? 0;
                            
                            $gaji_pokok = $r['total_hadir'] * $nom_shift;
                            $total_potongan_telat = $r['total_telat'] * $pot_telat;
                            $total_kasbon = (float)($r['total_kasbon'] ?? 0); 
                            
                            // 🚀 TAMBAHAN 3: Update Rumus Akhir (Gaji Pokok + Tunjangan + Uang Lembur) - (Potongan Telat + Kasbon)
                            $total_akhir = ($gaji_pokok + $tunjangan + $uang_lembur) - ($total_potongan_telat + $total_kasbon);
                        ?>
                        <tr>
                            <td class="fw-bold text-dark">
                                <?= $r['nama_user'] ?>
                                <br><small class="text-muted fw-normal"><?= $r['jabatan'] ?></small>
                            </td>
                            <td><span class="badge bg-info text-dark rounded-pill"><?= $r['total_hadir'] ?> Shift</span>
                            </td>
                            <td>Rp <?= number_format($gaji_pokok, 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($tunjangan, 0, ',', '.') ?></td>

                            <td class="fw-semibold text-success">
                                Rp <?= number_format($uang_lembur, 0, ',', '.') ?>
                            </td>

                            <td class="text-danger">
                                Rp <?= number_format($total_potongan_telat, 0, ',', '.') ?>
                                <br><small class="text-muted">(<?= $r['total_telat'] ?>x)</small>
                            </td>

                            <td class="text-center">
                                <?php if($total_kasbon > 0): ?>
                                <span class="text-danger fw-bold">Rp
                                    <?= number_format($total_kasbon, 0, ',', '.') ?></span>
                                <br><small class="badge bg-danger-subtle text-danger p-0 px-2"
                                    style="font-size: 10px; border-radius: 5px;">Potong Gaji</small>
                                <?php else: ?>
                                <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>

                            <td class="fw-bold text-success" style="font-size: 1.1rem;">
                                Rp <?= number_format($total_akhir, 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <button
                                    onclick="printSlip('<?= $r['id_user'] ?>', '<?= $bulan_pilih ?>', '<?= $tahun_pilih ?>')"
                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-print"></i> Slip
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<iframe id="print_frame" name="print_frame" style="display:none;"></iframe>

<script>
function printSlip(id, bulan, tahun) {
    var printUrl = "<?= base_url('admin/cetak_slip') ?>/" + id + "/" + bulan + "/" + tahun;
    var frame = document.getElementById('print_frame');
    frame.src = printUrl;

    frame.onload = function() {
        frame.contentWindow.focus();
        frame.contentWindow.print();
    };
}
</script>
<?= $this->endSection() ?>