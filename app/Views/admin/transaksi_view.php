<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Laporan Transaksi</h4>
            <p class="text-muted mb-0">Riwayat seluruh aktivitas pembayaran</p>
        </div>
    </div>

    <div class="card p-4 mb-4 border-0 shadow-sm rounded-4">
        <h6 class="fw-bold mb-3">Filter Riwayat Transaksi</h6>
        <form method="GET" action="<?= site_url('admin/transaksi') ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Tanggal Mulai</label>
                <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?? '' ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small">Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?? '' ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success me-2 px-4 rounded-pill">Filter Data</button>
                <a href="<?= site_url('admin/transaksi') ?>"
                    class="btn btn-outline-secondary px-4 rounded-pill">Reset</a>
            </div>
        </form>
    </div>

    <div class="card p-4 border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Waktu</th>
                        <th>Item</th>
                        <th>Total</th>
                        <th>Diskon</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($riwayat)): ?>
                    <?php foreach ($riwayat as $row): ?>
                    <tr>
                        <td class="py-3 fw-medium">
                            <a href="javascript:void(0)" class="text-decoration-none fw-bold text-main view-detail"
                                data-bs-toggle="modal" data-bs-target="#detailModal" data-id="<?= $row['id'] ?>"
                                data-invoice="<?= $row['invoice'] ?>">
                                #<?= $row['invoice'] ?>
                            </a>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= $row['subtotal'] ?? 0 ?> Item</td>
                        <td class="fw-bold text-success">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>

                        <td class="text-danger fw-medium">
                            <?= ($row['diskon'] > 0) ? '-Rp ' . number_format($row['diskon'], 0, ',', '.') : '-' ?>
                        </td>

                        <td class="py-3">
                            <?php if ($row['status'] == 'Lunas'): ?>
                            <span class="badge bg-success-subtle text-success rounded-pill px-3">Lunas</span>
                            <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Batal</span>
                            <?php endif; ?>
                        </td>


                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <?php if (isset($transaksi['status']) && $transaksi['status'] == 'Batal'): ?>
                                <div class="mt-4 p-3 rounded-4 bg-danger-subtle border border-danger-subtle">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                                        <h6 class="fw-bold text-danger mb-0 small">Informasi Pembatalan</h6>
                                    </div>
                                    <div class="ms-4 text-danger">
                                        <small class="opacity-75 d-block mb-1">Alasan Pembatalan:</small>
                                        <p class="mb-0 small italic" style="font-style: italic;">
                                            "<?= esc($transaksi['alasan_batal'] ?? 'Tidak ada alasan yang dicatat') ?>"
                                        </p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if ($row['status'] == 'Lunas'): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-batal"
                                    data-id="<?= $row['id'] ?>" data-invoice="<?= $row['invoice'] ?>"
                                    data-bs-toggle="tooltip" title="Batalkan Transaksi">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-sm btn-light text-muted border" disabled
                                    title="Transaksi ini sudah dibatalkan">
                                    <i class="bi bi-slash-circle"></i> Dibatalkan
                                </button>
                                <?php endif; ?>

                        <td>
                            <?php if ($row['alasan_batal']): ?>
                            <span class="text-danger small italic">
                                <i class="bi bi-x-circle me-1"></i>
                                Batal: <?= esc($row['alasan_batal'] ?? 'Tanpa alasan'); ?>
                            </span>
                            <?php else: ?>
                            <span class="text-success small"><i class="bi bi-check2-circle me-1"></i> Terjual</span>
                            <?php endif; ?>
                        </td>
        </div>
        </td>
        </tr>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td colspan="7" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                Belum ada transaksi untuk tanggal ini.
            </td>
        </tr>
        <?php endif; ?>
        </tbody>
        </table>

    </div>
</div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-success text-white rounded-top-4">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="bi bi-receipt me-2"></i> Detail Transaksi <span id="modal-invoice-title">#</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailModalBody">
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>
                    Memuat data...
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                    data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-outline-dark" onclick="prosesCetakUlang()">
                    <i class="bi bi-printer"></i> Cetak Ulang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi Elemen Global
    const htmlElement = document.documentElement;
    const detailModal = document.getElementById('detailModal');

    // === TANGKAP FLASH DATA DARI CONTROLLER SETELAH REDIRECT ===
    // Alert sukses setelah halaman reload otomatis
    <?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        title: 'Berhasil!',
        text: '<?= (string)session()->getFlashdata("success") ?>',
        icon: 'success',
        timer: 3000, // Otomatis menutup sendiri dalam 3 detik (3000 milidetik)
        showConfirmButton: false, // Menghilangkan tombol konfirmasi (Oke/Selesai)
        background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#252525' :
            '#fff',
        color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#fff' : '#000',
    });
    <?php endif; ?>

    // Alert gagal/error setelah halaman reload otomatis
    <?php if (session()->getFlashdata('error')) : ?>
    Swal.fire({
        title: 'Gagal Memproses!',
        // Ditambahkan (string) agar bebas dari deteksi error tipe data campuran
        text: '<?= (string)session()->getFlashdata("error") ?>',
        icon: 'error',
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Tutup',
        background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#252525' :
            '#fff',
        color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#fff' : '#000',
    });
    <?php endif; ?>


    // === FUNGSI DETAIL MODAL (FETCH DATA) ===
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const transaksiId = button.getAttribute('data-id');
            const invoice = button.getAttribute('data-invoice');
            const modalBody = document.getElementById('detailModalBody');
            const modalTitle = document.getElementById('modal-invoice-title');

            // Set judul invoice di header modal
            if (modalTitle) modalTitle.innerText = '#' + invoice;

            // Tampilkan Loading Spinner
            modalBody.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>
                    <span>Memuat data...</span>
                </div>
            `;

            // Ambil data via Fetch API
            fetch('<?= site_url('admin/transaksi/detail/') ?>' + transaksiId)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal memuat data');
                    return response.text();
                })
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    modalBody.innerHTML = `
                        <div class="alert alert-danger text-center m-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> Gagal memuat detail transaksi.
                        </div>
                    `;
                });
        });
    }

    // === FUNGSI PEMBATALAN TRANSAKSI (SWEETALERT) ===
    // Menggunakan event delegation agar tetap jalan meski baris tabel di-refresh
    $(document).on('click', '.btn-batal', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const invoice = $(this).data('invoice');
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

        Swal.fire({
            title: 'Batalkan Transaksi?',
            text: "Berikan alasan untuk pembatalan #" + invoice,
            icon: 'warning',
            input: 'textarea', // Munculkan input box
            inputPlaceholder: 'Contoh: Pelanggan salah pesan / Salah input menu',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali',
            background: isDark ? '#252525' : '#fff',
            color: isDark ? '#fff' : '#000',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan harus diisi agar bisa dibatalkan!'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const alasan = result.value;

                // Menampilkan progress loading sebelum halaman berpindah ke controller
                Swal.fire({
                    title: 'Memproses Pembatalan...',
                    text: 'Mohon tunggu, sistem sedang memperbarui stok dan arus kas.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    background: isDark ? '#252525' : '#fff',
                    color: isDark ? '#fff' : '#000',
                    didOpen: () => {
                        Swal.showLoading(); // Menampilkan animasi spinner loading
                    }
                });

                // Alihkan ke controller setelah jeda singkat untuk transisi visual yang halus
                setTimeout(() => {
                    window.location.href = "<?= site_url('admin/transaksi_batal/') ?>" +
                        id + "?alasan=" + encodeURIComponent(alasan);
                }, 800);
            }
        });
    });
});

// === FUNGSI CETAK ULANG (DI LUAR DOMCONTENTLOADED AGAR BISA DIPANGGIL ONCLICK) ===
function prosesCetakUlang() {
    // 1. Ambil Nomor Invoice dari Judul Modal
    const titleEl = document.getElementById('modal-invoice-title');
    let invoice = titleEl ? titleEl.innerText : 'Unknown';

    // 2. Ambil List Produk dari Body Modal
    let rows = document.querySelectorAll('#detailModalBody table tbody tr');
    let items = [];

    rows.forEach(row => {
        let cols = row.querySelectorAll('td');
        if (cols.length >= 3) {
            let namaProduk = cols[0].innerText.trim().split('\n')[0];
            let qty = cols[1].innerText.trim();
            let subtotal = cols[2].innerText.trim();

            items.push({
                nama: namaProduk,
                qty: qty,
                subtotal: subtotal
            });
        }
    });

    if (items.length === 0) {
        Swal.fire('Error', 'Data produk tidak ditemukan atau modal belum dimuat!', 'error');
        return;
    }

    // 3. Susun HTML untuk Struk (Thermal 80mm)
    let htmlStruk = `
    <html>
    <head>
        <style>
            @page { size: 80mm auto; margin: 0; }
            body { 
                font-family: 'Courier New', Courier, monospace; 
                width: 70mm; font-size: 11px; padding: 5mm; margin: 0 auto; color: #000;
            }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .line { border-top: 1px dashed #000; margin: 5px 0; }
            table { width: 100%; border-collapse: collapse; }
            h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        </style>
    </head>
    <body>
        <div class="text-center">
            <div style="border: 1px solid #000; padding: 2px; font-size: 9px; margin-bottom: 5px; font-weight:bold;">*** COPY STRUK ***</div>
            <h2>Senja Coffee & Eatery</h2>
            <small>Perum PT Sumbar Mas Blok B2</small><br>
            <div class="line"></div>
            <div style="text-align: left;">
                No: ${invoice}<br>
                Tgl: ${new Date().toLocaleDateString('id-ID')} ${new Date().toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})}
            </div>
        </div>
        <div class="line"></div>
        <table>
            ${items.map(item => `
                <tr><td colspan="2" style="padding-top: 5px;">${item.nama}</td></tr>
                <tr>
                    <td>${item.qty} x</td>
                    <td class="text-right">${item.subtotal}</td>
                </tr>
            `).join('')}
        </table>
        <div class="line"></div>
        <div class="text-center" style="margin-top: 10px;">
            <strong>Terima Kasih</strong><br>
            <small>ig : @senjacoffee</small>
        </div>
    </body>
    </html>`;

    // 4. Proses Cetak via Iframe Tersembunyi
    let iframe = document.getElementById('print-frame-admin');
    if (!iframe) {
        iframe = document.createElement('iframe');
        iframe.id = 'print-frame-admin';
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
    }

    let doc = iframe.contentWindow.document;
    doc.open();
    doc.write(htmlStruk);
    doc.close();

    setTimeout(() => {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }, 500);
}
</script>

<?= $this->endSection() ?>