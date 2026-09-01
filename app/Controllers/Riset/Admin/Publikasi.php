<?php

namespace App\Controllers\Riset\Admin;

use App\Controllers\BaseController;
use App\Models\PublikasiRisetModel;
use App\Models\DokumenRisetModel;

class Publikasi extends BaseController
{
    protected PublikasiRisetModel $publikasiModel;
    protected DokumenRisetModel $dokumenModel;

    public function __construct()
    {
        $this->publikasiModel = new PublikasiRisetModel();
        $this->dokumenModel = new DokumenRisetModel();
    }

    public function index()
    {
        $daftar = $this->publikasiModel
            ->where('status !=', 'ditolak')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('Riset/admin/publikasi/index', [
            'title'       => 'Review Publikasi',
            'active_menu' => 'publikasi',
            'daftar'      => $daftar
        ]);
    }

    public function detail($id = null)
    {
        $publikasi = $this->publikasiModel->find($id);

        if (!$publikasi) {
            return redirect()->to(base_url('riset/admin/publikasi'))->with('error', 'Data publikasi tidak ditemukan.');
        }

        // Ambil dokumen terkait
        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)
                                      ->whereIn('jenis_dokumen', ['publikasi', 'permohonan_izin', 'salinan_izin_penelitian', 'draft_artikel', 'pernyataan_anonimitas', 'Surat Izin Publikasi Resmi'])
                                      ->findAll();
        $publikasi['dokumen'] = $dokumen;
        
        // Fetch user data for contact info
        $userModel = new \App\Models\UserRisetModel();
        $user = $userModel->find($publikasi['user_riset_id']);
        $publikasi['email'] = $user['email'] ?? 'Tidak ada';
        $publikasi['no_telp'] = $user['no_telp'] ?? 'Tidak ada';
        $publikasi['tanggal'] = date('d/m/Y', strtotime($publikasi['created_at'] ?? 'now'));

        // Generate nomor surat berdasarkan angka tertinggi di tahun berjalan
        $currentYear = date('Y');
        $existingNumbers = $this->publikasiModel->like('no_surat_izin', '/' . $currentYear, 'before')->findColumn('no_surat_izin');
        
        $maxNumber = 0;
        if ($existingNumbers) {
            foreach ($existingNumbers as $numStr) {
                $parts = explode('/', $numStr);
                if (isset($parts[0]) && is_numeric($parts[0])) {
                    $val = (int)$parts[0];
                    if ($val > $maxNumber) {
                        $maxNumber = $val;
                    }
                }
            }
        }
        
        $increment = str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);
        $romans = ['01'=>'I', '02'=>'II', '03'=>'III', '04'=>'IV', '05'=>'V', '06'=>'VI', '07'=>'VII', '08'=>'VIII', '09'=>'IX', '10'=>'X', '11'=>'XI', '12'=>'XII'];
        $romanMonth = $romans[date('m')];
        $default_nomor_surat = "{$increment}/SIP-PUB/{$romanMonth}/" . $currentYear;

        return view('Riset/admin/publikasi/detail', [
            'title'               => 'Detail Draft Publikasi',
            'active_menu'         => 'publikasi',
            'id'                  => $id,
            'data'                => $publikasi,
            'default_nomor_surat' => $default_nomor_surat
        ]);
    }

    public function approve()
    {
        $id       = $this->request->getPost('id');
        $status   = $this->request->getPost('status_validasi');
        $catatan  = $this->request->getPost('catatan');
        $nominal  = $this->request->getPost('nominal_bayar');
        $nomor_surat = $this->request->getPost('nomor_surat');

        $updateData = [];

        if ($status === 'konfirmasi_dokumen') {
            $updateData['status'] = 'menunggu_pembayaran';
            $updateData['catatan_revisi'] = null;
            if ($nominal) {
                $updateData['nominal_bayar'] = $nominal;
            }
            $message = 'Dokumen disetujui. Menunggu pembayaran dari peneliti.';
        } elseif ($status === 'konfirmasi_bayar_izin') {
            $updateData['status'] = 'selesai';
            $updateData['catatan_revisi'] = null;
            // Generate nomor surat izin
            if ($nomor_surat) {
                $exists = $this->publikasiModel->where('no_surat_izin', $nomor_surat)->where('id !=', $id)->first();
                if ($exists) {
                    return redirect()->back()->with('error', "Gagal memproses: Nomor surat {$nomor_surat} sudah digunakan di publikasi lain.");
                }
                $updateData['no_surat_izin'] = $nomor_surat;
            } else {
                $currentYear = date('Y');
                $existingNumbers = $this->publikasiModel->like('no_surat_izin', '/' . $currentYear, 'before')->findColumn('no_surat_izin');
                
                $maxNumber = 0;
                if ($existingNumbers) {
                    foreach ($existingNumbers as $numStr) {
                        $parts = explode('/', $numStr);
                        if (isset($parts[0]) && is_numeric($parts[0])) {
                            $val = (int)$parts[0];
                            if ($val > $maxNumber) {
                                $maxNumber = $val;
                            }
                        }
                    }
                }
                $increment = str_pad($maxNumber + 1, 3, '0', STR_PAD_LEFT);
                
                $romans = ['01'=>'I', '02'=>'II', '03'=>'III', '04'=>'IV', '05'=>'V', '06'=>'VI', '07'=>'VII', '08'=>'VIII', '09'=>'IX', '10'=>'X', '11'=>'XI', '12'=>'XII'];
                $romanMonth = $romans[date('m')];
                $updateData['no_surat_izin'] = "{$increment}/SIP-PUB/{$romanMonth}/" . $currentYear;
            }
            $message = 'Pembayaran divalidasi. Surat Izin Publikasi diterbitkan dan diarsipkan otomatis!';
        } elseif ($status === 'konfirmasi_bayar_terima') {
            $updateData['status'] = 'selesai';
            $updateData['catatan_revisi'] = null;
            $message = 'Pembayaran divalidasi. Laporan hasil riset diarsipkan tanpa penerbitan surat izin.';
        } elseif ($status === 'revisi') {
            $updateData['status'] = 'direvisi';
            $updateData['catatan_revisi'] = $catatan;
            $message = 'Berkas dikembalikan untuk revisi.';
        } elseif ($status === 'revisi_bayar') {
            $updateData['status'] = 'menunggu_pembayaran';
            $updateData['catatan_revisi'] = $catatan;
            $message = 'Bukti pembayaran dikembalikan untuk direvisi.';
        } elseif ($status === 'tolak') {
            $updateData['status'] = 'ditolak';
            $updateData['catatan_revisi'] = $catatan;
            $message = 'Berkas ditolak. Catatan: ' . ($catatan ?: 'Mohon perbaiki draft sesuai standar.');
        } else {
            return redirect()->to(base_url('riset/admin/publikasi'))
                ->with('warning', 'Status validasi tidak dikenali.');
        }

        $this->publikasiModel->update($id, $updateData);

        return redirect()->to(base_url('riset/admin/publikasi'))
            ->with('success', $message);
    }

    public function buka_arsip($id)
    {
        $publikasi = $this->publikasiModel->find($id);
        if (!$publikasi) {
            return redirect()->back()->with('error', 'Data publikasi tidak ditemukan.');
        }

        // Cari dokumen artikel/laporan (selalu disimpan sebagai 'draft_artikel' pada form submission)
        $dokumen = $this->dokumenModel->where('pengajuan_riset_id', $id)
                                      ->groupStart()
                                        ->where('jenis_dokumen', 'draft_artikel')
                                        ->orWhere('jenis_dokumen', 'publikasi')
                                      ->groupEnd()
                                      ->first();

        if ($dokumen && !empty($dokumen['file_path'])) {
            return redirect()->to(base_url($dokumen['file_path']));
        }

        return redirect()->back()->with('error', 'File PDF dokumen tidak ditemukan di dalam sistem.');
    }

    public function print($id = null)
    {
        $publikasi = $this->publikasiModel->find($id);

        if (!$publikasi) {
            return redirect()->to(base_url('riset/admin/publikasi'))->with('error', 'Data publikasi tidak ditemukan.');
        }

        $pengajuanModel = new \App\Models\PengajuanRisetModel();
        $pengajuan = $pengajuanModel->find($publikasi['pengajuan_riset_id']);
        
        $publikasi['nama'] = $pengajuan['nama'] ?? '-';
        $publikasi['identitas'] = $pengajuan['identitas'] ?? '-';
        $publikasi['institusi'] = $pengajuan['institusi'] ?? '-';

        // Format waktu penelitian
        $bulanIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                      7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        
        $waktu_penelitian = '-';
        if (!empty($publikasi['waktu_mulai']) && !empty($publikasi['waktu_selesai'])) {
            $mulai = date_create($publikasi['waktu_mulai']);
            $selesai = date_create($publikasi['waktu_selesai']);
            $waktu_penelitian = date_format($mulai, 'd') . ' ' . $bulanIndo[(int)date_format($mulai, 'm')] . ' ' . date_format($mulai, 'Y')
                              . ' s/d ' 
                              . date_format($selesai, 'd') . ' ' . $bulanIndo[(int)date_format($selesai, 'm')] . ' ' . date_format($selesai, 'Y');
        }

        $pengaturanModel = new \App\Models\PengaturanSuratRisetModel();
        $pengaturan = $pengaturanModel->first();

        return view('Riset/peneliti/publikasi/surat_izin_publikasi_template', [
            'title'              => 'Cetak Surat Izin Publikasi',
            'active_menu'        => 'publikasi',
            'nama_peneliti'      => $publikasi['nama'] ?? '-',
            'nim'                => $publikasi['identitas'] ?? '-',
            'institusi'          => $publikasi['institusi'] ?? '-',
            'judul_riset'        => $publikasi['judul'] ?? '-',
            'nama_publikasi'     => $publikasi['nama_publikasi'] ?? '-',
            'jenis_jurnal'       => $publikasi['jenis_jurnal'] ?? '-',
            'kategori'           => $publikasi['kategori_jurnal'] ?? '-',
            'issn'               => $publikasi['issn'] ?? '-',
            'scope'              => $publikasi['scope'] ?? '-',
            'alamat_web'         => $publikasi['alamat_web'] ?? '-',
            'waktu_penelitian'   => $waktu_penelitian,
            'nomor_surat'        => $publikasi['no_surat_izin'] ?? null,
            'pengaturan'         => $pengaturan
        ]);
    }

    public function uploadSuratIzin()
    {
        $id = $this->request->getPost('id');
        $file = $this->request->getFile('surat_izin');

        if ($file && $file->isValid()) {
            $newName = str_replace(' ', '_', $file->getClientName());
            $file->move(FCPATH . 'uploads/riset/dokumen', $newName);

            $this->dokumenModel->where('pengajuan_riset_id', $id)
                               ->where('jenis_dokumen', 'Surat Izin Publikasi Resmi')
                               ->delete();
                               
            $this->dokumenModel->insert([
                'pengajuan_riset_id' => $id,
                'jenis_dokumen'      => 'Surat Izin Publikasi Resmi',
                'file_path'          => 'uploads/riset/dokumen/' . $newName,
                'status_dokumen'     => 'valid'
            ]);

            return redirect()->to(base_url("riset/admin/publikasi/detail/{$id}"))->with('success', 'Surat Izin Publikasi Resmi berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah surat izin publikasi.');
    }
}
