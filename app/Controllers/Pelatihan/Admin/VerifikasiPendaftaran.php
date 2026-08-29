<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;

class VerifikasiPendaftaran extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $pendingList = $db->table('peserta_pelatihan')
            ->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama, users_pelatihan.email, users_pelatihan.no_wa, profesi_pelatihan.nama_profesi as profesi_peserta, unit_kerja_pelatihan.nama_unit as unit_peserta, master_pelatihan.nama as judul_pelatihan, master_pelatihan.mekanisme, master_pelatihan.biaya_nominal, master_pelatihan.metode, master_pelatihan.program, master_pelatihan.target_profesi, master_pelatihan.jadwal_mulai, master_pelatihan.jadwal_selesai, master_pelatihan.deskripsi, master_pelatihan.kuota, master_pelatihan.biaya')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->groupStart()
                ->where('peserta_pelatihan.status_pembayaran', 'Pending')
                ->orWhere('peserta_pelatihan.status_akses', 'Pending')
            ->groupEnd()
            ->orderBy('peserta_pelatihan.waktu_daftar', 'DESC')
            ->get()->getResultArray();

        $historyList = $db->table('peserta_pelatihan')
            ->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama, users_pelatihan.email, users_pelatihan.no_wa, profesi_pelatihan.nama_profesi as profesi_peserta, unit_kerja_pelatihan.nama_unit as unit_peserta, master_pelatihan.nama as judul_pelatihan, master_pelatihan.mekanisme, master_pelatihan.biaya_nominal, master_pelatihan.metode, master_pelatihan.program, master_pelatihan.target_profesi, master_pelatihan.jadwal_mulai, master_pelatihan.jadwal_selesai, master_pelatihan.deskripsi, master_pelatihan.kuota, master_pelatihan.biaya')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->groupStart()
                ->where('peserta_pelatihan.status_pembayaran !=', 'Pending')
                ->where('peserta_pelatihan.status_akses !=', 'Pending')
            ->groupEnd()
            ->orderBy('peserta_pelatihan.waktu_daftar', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Verifikasi Pendaftaran',
            'list' => $pendingList,
            'history' => $historyList
        ];

        return view('Pelatihan/admin/verifikasi_pendaftaran/index', $data);
    }

    private function checkCompletion($db, $id, $oldPay = null, $oldAccess = null)
    {
        $record = $db->table('peserta_pelatihan')
            ->select('peserta_pelatihan.*, master_pelatihan.nama as judul_pelatihan, master_pelatihan.jadwal_mulai')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->where('peserta_pelatihan.id', $id)
            ->get()->getRowArray();
            
        if ($record) {
            $isPayApproved = in_array($record['status_pembayaran'], ['Verified', 'Gratis']);
            $isAccessApproved = in_array($record['status_akses'], ['Approved', 'Terbuka']);
            
            $wasPayApproved = in_array($oldPay, ['Verified', 'Gratis']);
            $wasAccessApproved = in_array($oldAccess, ['Approved', 'Terbuka']);
            $wasFullyApproved = $wasPayApproved && $wasAccessApproved;
            
            if ($record['status_pembayaran'] == 'Rejected' || $record['status_akses'] == 'Rejected') {
                $db->table('peserta_pelatihan')->where('id', $id)->update(['status_peserta' => 'Gagal']);
            } elseif ($isPayApproved && $isAccessApproved) {
                $db->table('peserta_pelatihan')->where('id', $id)->update(['status_peserta' => 'Daftar']);
                if (!$wasFullyApproved) {
                    $now = date('Y-m-d H:i:s');
                    $jadwalMulai = $record['jadwal_mulai'] . ' ' . ($record['jam_mulai'] ?? '00:00:00');
                    if ($now >= $jadwalMulai) {
                        $msg = 'Pendaftaran Anda telah lengkap disetujui. Pelatihan sudah bisa diakses, silakan menuju menu Diklat Saya dan klik Mulai Belajar.';
                    } else {
                        $tglMulai = date('d M Y', strtotime($record['jadwal_mulai']));
                        $msg = 'Pendaftaran Anda telah lengkap disetujui. Pelatihan akan dapat diakses mulai tanggal ' . $tglMulai . '.';
                    }

                    $db->table('notifikasi_pelatihan')->insert([
                        'user_id' => $record['user_id'],
                        'title' => 'Pendaftaran Disetujui: ' . $record['judul_pelatihan'],
                        'message' => $msg,
                        'type' => 'success',
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } else {
                $db->table('peserta_pelatihan')->where('id', $id)->update(['status_peserta' => 'Daftar']);
            }
        }
    }

    public function update_status()
    {
        $db = \Config\Database::connect();
        
        $id = $this->request->getPost('id');
        $field = $this->request->getPost('field'); // 'status_akses' or 'status_pembayaran'
        $value = $this->request->getPost('value'); // 'Pending', 'Approved'/'Verified', 'Rejected'
        $reason = $this->request->getPost('reason');

        $record = $db->table('peserta_pelatihan')
            ->select('peserta_pelatihan.*, master_pelatihan.nama as judul_pelatihan')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->where('peserta_pelatihan.id', $id)
            ->get()->getRowArray();

        if (!$record) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pendaftaran tidak ditemukan']);
        }

        if ($field === 'status_akses') {
            if ($value === 'Diterima') $value = 'Approved';
            if ($value === 'Ditolak') $value = 'Rejected';
        } elseif ($field === 'status_pembayaran') {
            if ($value === 'Diterima') $value = 'Verified';
            if ($value === 'Ditolak') $value = 'Rejected';
        }

        $updateData = [$field => $value];
        $db->table('peserta_pelatihan')->where('id', $id)->update($updateData);

        if ($value === 'Rejected') {
            $fieldNameLabel = ($field === 'status_akses') ? 'Akses Kelas' : 'Pembayaran Pelatihan';
            $db->table('notifikasi_pelatihan')->insert([
                'user_id' => $record['user_id'],
                'title' => 'Pendaftaran Ditolak: ' . $record['judul_pelatihan'],
                'message' => 'Pengajuan verifikasi ' . $fieldNameLabel . ' ditolak dengan alasan: ' . ($reason ?: 'Berkas tidak sesuai kriteria.'),
                'type' => 'danger',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } elseif ($value === 'Verified' || $value === 'Approved') {
            $fieldNameLabel = ($field === 'status_akses') ? 'Akses Kelas' : 'Pembayaran Pelatihan';
            $db->table('notifikasi_pelatihan')->insert([
                'user_id' => $record['user_id'],
                'title' => 'Pendaftaran Disetujui: ' . $record['judul_pelatihan'],
                'message' => 'Pengajuan verifikasi ' . $fieldNameLabel . ' Anda telah disetujui. Silakan cek status pendaftaran pelatihan Anda.',
                'type' => 'success',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        $this->checkCompletion($db, $id, $record['status_pembayaran'], $record['status_akses']);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Status berhasil diperbarui']);
    }

    public function reset($id)
    {
        $db = \Config\Database::connect();
        $record = $db->table('peserta_pelatihan')
            ->select('peserta_pelatihan.*, master_pelatihan.biaya, master_pelatihan.mekanisme')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->where('peserta_pelatihan.id', $id)
            ->get()->getRowArray();
            
        if ($record) {
            $status_pembayaran = ($record['biaya'] == 'Berbayar') ? 'Pending' : 'Gratis';
            $status_akses = ($record['mekanisme'] == 'Tertutup') ? 'Pending' : 'Terbuka';
            
            $db->table('peserta_pelatihan')->where('id', $id)->update([
                'status_pembayaran' => $status_pembayaran,
                'status_akses' => $status_akses,
                'status_peserta' => 'Daftar'
            ]);
            return redirect()->to('/pelatihan/admin/verifikasi_pendaftaran')->with('success', 'Tindakan verifikasi berhasil dibatalkan.');
        }
        return redirect()->to('/pelatihan/admin/verifikasi_pendaftaran')->with('error', 'Data tidak ditemukan.');
    }
}
