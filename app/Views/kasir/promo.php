<div class="input-group mb-3">
    <input type="text" id="kode_promo" class="form-control" placeholder="Masukkan Kode Promo">
    <button class="btn btn-outline-secondary" type="button" onclick="terapkanPromo()">Cek</button>
</div>
<div id="info_promo" class="small text-success mb-2"></div>

<input type="hidden" name="id_promo" id="id_promo">
<input type="hidden" name="potongan_diskon" id="potongan_diskon" value="0">
<script>
function terapkanPromo() {
    let kode = $('#kode_promo').val();
    let total = totalBelanjaSebelumDiskon; // Ambil variabel total belanja bos

    $.post('<?= base_url('admin/cek_promo') ?>', { kode_promo: kode, total_belanja: total }, function(res) {
        if (res.status == 'success') {
            $('#id_promo').val(res.id_promo);
            $('#potongan_diskon').val(res.potongan);
            $('#info_promo').text('Promo Berhasil: ' + res.nama_promo + ' (-Rp ' + res.potongan + ')');
            
            // Update Grand Total di layar kasir
            updateGrandTotal(res.potongan);
        } else {
            alert(res.msg);
        }
    });
}
</script>