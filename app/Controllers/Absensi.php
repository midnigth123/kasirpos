<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Absensi extends BaseController
{
    public function absen_pakai_pin()
{
    // JEDOR! Pastikan Timezone sinkron
    date_default_timezone_set('Asia/Jakarta');

    $db = $this->db;
    $pin = $this->request->getPost('pin_pegawai');
    $img = $this->request->getPost('image_tag'); 
    $tgl_hari_ini = date('Y-m-d'); // Variabel tanggal hari ini

    // 1. Cari data Pegawai berdasarkan PIN
    $pegawai = $db->table('user')->where('pin_user', $pin)->get()->getRow();
    if (!$pegawai) return redirect()->back()->with('error', 'PIN Salah!');

    // 2. JEDOR! CEK APAKAH SUDAH ABSEN HARI INI
    // Ini logika pembatas agar tidak bisa absen masuk berkali-kali
    $cek_absen = $db->table('absensi')
        ->where('id_user', $pegawai->id_user)
        ->where('tanggal', $tgl_hari_ini)
        ->get()
        ->getRow();

    if ($cek_absen) {
        // Jika sudah ada data, kirim notif peringatan (Warna Kuning)
        // Pastikan di View ada penangkap session 'peringatan_absen'
        return redirect()->back()->with('peringatan_absen', [
            'nama'  => $pegawai->nama_user,
            'jam'   => date('H:i', strtotime($cek_absen->jam_masuk)),
            'pesan' => 'Anda sudah melakukan absen masuk hari ini!'
        ]);
    }

    // 3. --- PROSES FOTO ---
    $nama_file = null;
    if ($img) {
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $nama_file = 'selfie_' . $pegawai->id_user . '_' . time() . '.jpg';
        file_put_contents(FCPATH . 'uploads/absensi/' . $nama_file, $data);
    }

    $jam_masuk = date('H:i:s');
    $shift_input = $this->request->getPost('shift');

    // 4. --- INSERT DATABASE (Hanya jalan jika poin nomor 2 lolos) ---
    $db->table('absensi')->insert([
        'id_user'    => $pegawai->id_user,
        'tanggal'    => $tgl_hari_ini,
        'jam_masuk'  => $jam_masuk,
        'nama_shift' => $shift_input,
        'foto'       => $nama_file,
    ]);

    // 5. --- NOTIF BERHASIL (Warna Hijau) ---
    return redirect()->back()->with('absen_sukses', [
        'nama'  => $pegawai->nama_user,
        'jam'   => date('H:i', strtotime($jam_masuk)),
        'shift' => $shift_input,
        'label' => 'Berhasil Absen Masuk'
    ]);
}

    public function absen_pulang_pin()
{
    date_default_timezone_set('Asia/Jakarta');
    $db = $this->db;
    $pin_input = $this->request->getPost('pin_pegawai');
    $tgl_hari_ini = date('Y-m-d');

    // 1. Cek Pegawai
    $pegawai = $db->table('user')->where('pin_user', $pin_input)->get()->getRow();
    if (!$pegawai) return redirect()->back()->with('error_absen', 'PIN Salah!');

    // 2. Cari data absen MASUK (Cek apakah query ini menemukan barisnya)
    $absen = $db->table('absensi')
        ->where('id_user', $pegawai->id_user)
        ->where('tanggal', $tgl_hari_ini)
        ->get()
        ->getRow();

    if (!$absen) {
        return redirect()->back()->with('error_absen', 'Gagal: Record masuk tidak ditemukan untuk ID ' . $pegawai->id_user);
    }

    // 3. Proses Update dengan Validasi Ketat
    $jam_sekarang = date('H:i:s');
    
    // JEDOR! Kita coba eksekusi dan tangkap statusnya
    $update = $db->table('absensi')
        ->where('id_absensi', $absen->id_absensi) 
        ->update([
            'jam_pulang' => $jam_sekarang
        ]);

    // DEBUG: Jika baris yang berubah 0, berarti ada yang salah dengan ID atau Database
    if ($db->affectedRows() > 0) {
        return redirect()->back()->with('absen_sukses', [
            'nama'  => $pegawai->nama_user,
            'jam'   => date('H:i', strtotime($jam_sekarang)),
            'shift' => $absen->nama_shift,
            'label' => 'Berhasil Pulang'
        ]);
    } else {
        // JEDOR! Kalau gagal masuk ke sini, kita matikan program dan intip error-nya
        $error = $db->error();
        die("Gagal Simpan ke DB! Error Code: " . $error['code'] . " | Pesan: " . $error['message'] . " | ID yang ditembak: " . $absen->id_absensi);
    }
}
}