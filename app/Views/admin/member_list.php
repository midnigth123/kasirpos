<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<style>
/* Menggunakan font yang lebih modern */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.content-wrapper {
    font-family: 'Inter', sans-serif;
    background-color: #f4f7f6;
    padding-top: 30px;
}

/* Styling Kartu Utama */
.card-member {
    border: none;
    border-radius: 24px;
    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.05);
    background: #ffffff;
    overflow: hidden;
}

.card-header-premium {
    background: #1a1c1e;
    padding: 30px;
    border: none;
}

/* Avatar Inisial Nama */
.avatar-circle {
    width: 45px;
    height: 45px;
    background-color: #3d8bfd;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    font-weight: 700;
    font-size: 1.1rem;
    box-shadow: 0 4px 10px rgba(61, 139, 253, 0.3);
}

/* Tabel Modern */
.table-container {
    padding: 0 20px 20px 20px;
}

.table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #f1f3f5;
    color: #6c757d;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 20px 15px;
}

.table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f8f9fa;
}

.table tbody tr:hover {
    background-color: #fcfdfe;
    transform: scale(1.002);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
}

.table tbody td {
    padding: 20px 15px;
    vertical-align: middle;
    color: #495057;
    font-size: 0.95rem;
}

/* Status Badge */
.badge-point {
    background-color: #fff4e5;
    color: #ff9800;
    padding: 8px 14px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
}

.badge-transaksi {
    background-color: #eef2ff;
    color: #4f46e5;
    padding: 8px 14px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
}

/* Tombol Aksi */
.btn-action {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    border: none;
}

.btn-edit {
    background-color: #f0f7ff;
    color: #007bff;
}

.btn-edit:hover {
    background-color: #007bff;
    color: white;
    transform: translateY(-3px);
}

.btn-delete {
    background-color: #fff5f5;
    color: #e03131;
}

.btn-delete:hover {
    background-color: #e03131;
    color: white;
    transform: translateY(-3px);
}

/* Search Bar */
.search-box {
    background: #f8f9fa;
    border: 1px solid #eee;
    border-radius: 15px;
    padding: 10px 20px;
    max-width: 300px;
}
</style>

<div class="content-wrapper">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Manajemen Member</h3>
                <p class="text-muted small">Kelola database pelanggan setia Senja Coffee Anda</p>
            </div>
            <!-- <button class="btn btn-dark rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Tambah Member
            </button> -->
        </div>

        <div class="card card-member">
            <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                <h5 class="fw-bold mb-0">List Data Member</h5>
                <div class="search-box">
                    <i class="bi bi-search text-muted me-2"></i>
                    <input type="text" id="searchMember" class="border-0 bg-transparent" placeholder="Cari member..."
                        style="outline: none;">
                </div>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="memberTable">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Profil Member</th>
                                <th>No. WhatsApp</th>
                                <th>Poin Pelanggan</th>
                                <th>Total Order</th>
                                <th>Join Date</th>
                                <th class="text-center">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($members)): ?>
                            <?php $no = 1; foreach ($members as $m): ?>
                            <tr>
                                <td class="text-center text-muted small"><?= $no++; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            <?= strtoupper(substr($m['nama_member'], 0, 1)); ?>
                                        </div>
                                        <?php 
                // Hitung level VIP secara otomatis berdasarkan total transaksi
                $totalBelanja = (int)$m['total_transaksi'];
                $level = 'Silver ⚪'; // Default level
                $warna = 'text-secondary'; // Warna teks default

                if ($totalBelanja >= 2000000) {
                    $level = 'Platinum 👑';
                    $warna = 'text-primary'; // Warna teks platinum/biru
                } elseif ($totalBelanja >= 500000) {
                    $level = 'Gold 🥇';
                    $warna = 'text-warning'; // Warna teks emas/kuning
                }
            ?>
                                        <div>
                                            <div class="fw-bold text-dark mb-0"><?= esc($m['nama_member']); ?></div>
                                            <small class="<?= $warna ?> fw-bold"
                                                style="font-size: 0.75rem;"><?= $level; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium">
                                        <i class="bi bi-whatsapp text-success me-2"></i><?= esc($m['no_telepon']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-point"><i class="bi bi-star-fill me-1"></i>
                                        <?= number_format($m['total_poin'], 0, ',', '.'); ?> Pts
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-transaksi text-success fw-bold">
                                        <i class="bi bi-cash-stack me-1"></i> Rp
                                        <?= number_format($m['total_transaksi'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-dark small fw-medium">
                                        <?= date('d M Y', strtotime($m['created_at'])); ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn-action btn-edit"
                                            onclick="editMember('<?= $m['id_member'] ?>', '<?= esc($m['nama_member']) ?>', '<?= $m['no_telepon'] ?>', '<?= $m['total_poin'] ?>', '<?= $m['total_transaksi'] ?>')"
                                            title="Edit Data">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="btn-action btn-delete"
                                            onclick="hapusMember('<?= $m['id_member'] ?>', '<?= esc($m['nama_member']) ?>')"
                                            title="Hapus Member">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80"
                                        class="mb-3 opacity-50">
                                    <p class="text-muted">Waduh, belum ada member yang terdaftar nih bos.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditMember" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-primary text-white border-0 py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Edit Data Member</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditMember">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_id_member">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" id="edit_nama" class="form-control rounded-3 py-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor WhatsApp</label>
                        <input type="number" id="edit_telp" class="form-control rounded-3 py-2" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Total Poin</label>
                            <input type="number" id="edit_poin" class="form-control rounded-3 py-2 bg-light" readonly>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Total Transaksi</label>
                            <input type="number" id="edit_transaksi" class="form-control rounded-3 py-2 bg-light"
                                readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.getElementById('searchMember').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#memberTable tbody tr');

    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
document.getElementById('searchMember').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#memberTable tbody tr');
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// 2. Fungsi Munculkan Modal Edit & Isi Data
function editMember(id, nama, telp, poin, transaksi) {
    $('#edit_id_member').val(id);
    $('#edit_nama').val(nama);
    $('#edit_telp').val(telp);
    $('#edit_poin').val(poin);
    $('#edit_transaksi').val(transaksi);

    $('#modalEditMember').modal('show');
}

// 3. Eksekusi Update Member (AJAX)
$('#formEditMember').on('submit', function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Simpan perubahan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Update!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();

            // Simulasi Kirim Data (Sesuaikan URL Controller bos nanti)
            const data = {
                id: $('#edit_id_member').val(),
                nama: $('#edit_nama').val(),
                telp: $('#edit_telp').val(),
                poin: $('#edit_poin').val(),
                transaksi: $('#edit_transaksi').val()
            };

            $.ajax({
                url: "<?= base_url('admin/update_member') ?>",
                type: "POST",
                data: data,
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data member berhasil diperbaharui.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
});

// 4. Fungsi Hapus Member (SweetAlert2)
function hapusMember(id, nama) {
    Swal.fire({
        title: 'Hapus Member?',
        html: `Yakin ingin menghapus <b>${nama}</b>?<br><small class="text-danger">Data yang dihapus tidak bisa dikembalikan.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();

            $.ajax({
                url: "<?= base_url('admin/hapus_member/') ?>" + id,
                type: "GET",
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Member telah dihapus dari database.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}
</script>

<?= $this->endSection() ?>