<div class="p-4">
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle m-0">
            <thead class="table-light">
                <tr>
                    <th>Barcode</th>
                    <th>Nama Produk</th>
                    <th class="text-center">Qty Retur</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal Refund</th>
                    <th>Alasan</th>
                    <th class="text-center">Kembali Ke Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($detail)) : ?>
                <?php foreach ($detail as $item) : ?>
                <tr>
                    <td><code><?= esc($item['barcode'] ?? '-') ?></code></td>
                    <td class="fw-medium"><?= esc($item['nama_produk']) ?></td>
                    <td class="text-center fw-bold"><?= (float)$item['qty_retur'] ?></td>
                    <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                    <td class="text-danger fw-semibold">Rp <?= number_format($item['subtotal_refund'], 0, ',', '.') ?>
                    </td>
                    <td><small class="text-muted"><?= esc($item['alasan']) ?></small></td>
                    <td class="text-center">
                        <?php if ($item['kembali_ke_stok'] == 'Ya') : ?>
                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded">Ya</span>
                        <?php else : ?>
                        <span class="badge bg-danger-subtle text-danger px-2 py-1 rounded">Tidak (Waste)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else : ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Detail item retur tidak ditemukan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>