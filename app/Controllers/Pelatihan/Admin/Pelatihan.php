<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;
use App\Models\Pelatihan\MasterPelatihanModel;
use App\Models\Pelatihan\MateriPelatihanModel;
use App\Models\Pelatihan\SesiInteraktifPelatihanModel;
use App\Models\Pelatihan\UjianPelatihanModel;
use App\Models\Pelatihan\UjianSoalPelatihanModel;
use App\Models\Pelatihan\PesertaPelatihanModel;
use App\Models\Pelatihan\KuesionerMasterPelatihanModel;
use App\Models\Pelatihan\NarasumberPelatihanModel;
use App\Models\Pelatihan\PenyelenggaraPelatihanModel;
use App\Models\Pelatihan\PejabatTtdPelatihanModel;
use App\Models\Pelatihan\MasterPenyelenggaraModel;

class Pelatihan extends BaseController
{
    protected MasterPelatihanModel $masterPelatihanModel;
    protected MateriPelatihanModel $materiPelatihanModel;
    protected SesiInteraktifPelatihanModel $sesiModel;
    protected PesertaPelatihanModel $pesertaModel;
    protected UjianPelatihanModel $evaluasiModel;
    protected UjianSoalPelatihanModel $evaluasiSoalModel;
    protected KuesionerMasterPelatihanModel $kuesionerModel;
    protected NarasumberPelatihanModel $narasumberPelModel;
    protected PenyelenggaraPelatihanModel $penyelenggaraPelModel;
    protected PejabatTtdPelatihanModel $pejabatModel;
    protected MasterPenyelenggaraModel $masterPenyelenggaraModel;
    
    public function __construct()
    {
        $this->masterPelatihanModel = new MasterPelatihanModel();
        $this->materiPelatihanModel = new MateriPelatihanModel();
        $this->sesiModel = new SesiInteraktifPelatihanModel();
        $this->evaluasiModel = new UjianPelatihanModel();
        $this->evaluasiSoalModel = new UjianSoalPelatihanModel();
        $this->pesertaModel = new PesertaPelatihanModel();
        $this->kuesionerModel = new KuesionerMasterPelatihanModel();
        $this->narasumberPelModel = new NarasumberPelatihanModel();
        $this->penyelenggaraPelModel = new PenyelenggaraPelatihanModel();
        $this->pejabatModel = new PejabatTtdPelatihanModel();
        $this->masterPenyelenggaraModel = new MasterPenyelenggaraModel();
    }

    private function getNarasumberForPelatihan(int $pelatihanId): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('narasumber_pelatihan np')
            ->select('pt.id as id, pt.nama_pejabat, pt.gelar_depan, pt.gelar_belakang, pt.keahlian')
            ->join('pejabat_ttd_pelatihan pt', 'pt.id = np.pejabat_ttd_id')
            ->where('np.pelatihan_id', $pelatihanId)
            ->groupBy('pt.id, pt.nama_pejabat, pt.gelar_depan, pt.gelar_belakang, pt.keahlian')
            ->get()->getResultArray();
        return $rows;
    }

    private function getPenyelenggaraForPelatihan(int $pelatihanId): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('penyelenggara_pelatihan pp')
            ->select('mp.id as id, mp.nama, mp.fokus_bidang')
            ->join('master_penyelenggara mp', 'mp.id = pp.penyelenggara_id')
            ->where('pp.pelatihan_id', $pelatihanId)
            ->groupBy('mp.id, mp.nama, mp.fokus_bidang')
            ->get()->getResultArray();
        return $rows;
    }

    private function getNarasumberForSesi(int $sesiId): array
    {
        $db = \Config\Database::connect();
        return $db->table('narasumber_pelatihan np')
            ->select('np.id as pivot_id, pt.id as id, pt.nama_pejabat, pt.gelar_depan, pt.gelar_belakang')
            ->join('pejabat_ttd_pelatihan pt', 'pt.id = np.pejabat_ttd_id')
            ->where('np.sesi_id', $sesiId)
            ->get()->getResultArray();
    }

    private function getPenyelenggaraForSesi(int $sesiId): array
    {
        $db = \Config\Database::connect();
        return $db->table('penyelenggara_pelatihan pp')
            ->select('pp.id as pivot_id, mp.id as id, mp.nama')
            ->join('master_penyelenggara mp', 'mp.id = pp.penyelenggara_id')
            ->where('pp.sesi_id', $sesiId)
            ->get()->getResultArray();
    }

    private function formatNamaLengkap(array $row): string
    {
        $nama = '';
        if (!empty($row['gelar_depan'])) $nama .= $row['gelar_depan'] . ' ';
        $nama .= $row['nama_pejabat'];
        if (!empty($row['gelar_belakang'])) $nama .= ', ' . $row['gelar_belakang'];
        return $nama;
    }

    public function index()
    {
        $db = \Config\Database::connect();
        
        $pelatihan = $this->masterPelatihanModel
            ->select('master_pelatihan.*, master_kategori_skp_pelatihan.nama_kategori as kategori_kegiatan, master_kategori_skp_pelatihan.ranah as ranah_skp')
            ->join('master_kategori_skp_pelatihan', 'master_kategori_skp_pelatihan.id = master_pelatihan.kategori_skp_id', 'left')
            ->orderBy('master_pelatihan.id', 'DESC')
            ->findAll();
        
        $pelatihanIds = array_column($pelatihan, 'id');
        
        $pesertaCounts = [];
        $narasumberGrouped = [];
        $penyelenggaraGrouped = [];

        if (!empty($pelatihanIds)) {
            // Pre-fetch peserta count
            $pesertaRows = $db->table('peserta_pelatihan')
                ->select('pelatihan_id, COUNT(id) as total')
                ->whereIn('pelatihan_id', $pelatihanIds)
                ->groupBy('pelatihan_id')
                ->get()->getResultArray();
            foreach ($pesertaRows as $row) {
                $pesertaCounts[$row['pelatihan_id']] = $row['total'];
            }

            // Pre-fetch narasumber
            $narasumberRows = $db->table('narasumber_pelatihan np')
                ->select('np.pelatihan_id, pt.id, pt.nama_pejabat, pt.gelar_depan, pt.gelar_belakang, pt.keahlian')
                ->join('pejabat_ttd_pelatihan pt', 'pt.id = np.pejabat_ttd_id')
                ->whereIn('np.pelatihan_id', $pelatihanIds)
                ->groupBy('np.pelatihan_id, pt.id, pt.nama_pejabat, pt.gelar_depan, pt.gelar_belakang, pt.keahlian')
                ->get()->getResultArray();
            foreach ($narasumberRows as $row) {
                $narasumberGrouped[$row['pelatihan_id']][] = $row;
            }

            // Pre-fetch penyelenggara
            $penyelenggaraRows = $db->table('penyelenggara_pelatihan pp')
                ->select('pp.pelatihan_id, mp.id, mp.nama, mp.fokus_bidang')
                ->join('master_penyelenggara mp', 'mp.id = pp.penyelenggara_id')
                ->whereIn('pp.pelatihan_id', $pelatihanIds)
                ->groupBy('pp.pelatihan_id, mp.id, mp.nama, mp.fokus_bidang')
                ->get()->getResultArray();
            foreach ($penyelenggaraRows as $row) {
                $penyelenggaraGrouped[$row['pelatihan_id']][] = $row;
            }
        }

        foreach ($pelatihan as &$p) {
            $p['peserta'] = $pesertaCounts[$p['id']] ?? 0;
            
            $narasumberList = $narasumberGrouped[$p['id']] ?? [];
            $p['narasumber'] = implode(', ', array_map(fn($n) => $this->formatNamaLengkap($n), $narasumberList));
            $p['narasumber_list'] = array_map(fn($n) => $this->formatNamaLengkap($n), $narasumberList);
            $p['narasumber_ids'] = array_column($narasumberList, 'id');
            
            $penyelenggaraList = $penyelenggaraGrouped[$p['id']] ?? [];
            $p['penyelenggara'] = implode(', ', array_column($penyelenggaraList, 'nama'));
            $p['penyelenggara_list'] = array_column($penyelenggaraList, 'nama');
            $p['penyelenggara_ids'] = array_column($penyelenggaraList, 'id');
        }

        $profesi = $db->table('profesi_pelatihan')->get()->getResultArray();
        $unit_kerja = $db->table('unit_kerja_pelatihan')->get()->getResultArray();

        $kategoriSkpAll = $db->table('master_kategori_skp_pelatihan')->orderBy('ranah')->orderBy('nama_kategori')->get()->getResultArray();
        $kategori_skp_grouped = [];
        $ranah_skp_list = [];
        foreach ($kategoriSkpAll as $k) {
            $kategori_skp_grouped[$k['ranah']][] = $k['nama_kategori'];
            if (!in_array($k['ranah'], $ranah_skp_list)) {
                $ranah_skp_list[] = $k['ranah'];
            }
        }

        $masterNarasumberList = $db->table('pejabat_ttd_pelatihan')
            ->where('status', 'Narasumber')
            ->orderBy('nama_pejabat', 'ASC')
            ->get()->getResultArray();
        $masterPenyelenggaraList = $db->table('master_penyelenggara')
            ->where('status', 'Aktif')
            ->orderBy('nama', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title'               => 'Kelola Pelatihan',
            'pelatihan'           => $pelatihan,
            'profesi'             => $profesi,
            'unit_kerja'          => $unit_kerja,
            'kategori_skp_grouped' => $kategori_skp_grouped,
            'ranah_skp_list'      => $ranah_skp_list,
            'master_narasumber_list' => $masterNarasumberList,
            'master_penyelenggara_list' => $masterPenyelenggaraList,
        ];
        return view('Pelatihan/admin/pelatihan/index', $data);
    }

    public function simpan()
    {
        helper('upload_security');
        if (!is_safe_upload($this->request->getFile('gambar_pelatihan'))) {
            return redirect()->back()->withInput()->with('error', 'Keamanan: File Gambar Pelatihan tidak valid atau mengandung ekstensi berbahaya.');
        }
        $data = $this->request->getPost();
        $gambarPelatihan = $this->uploadGambarPelatihan($data['nama'] ?? 'Pelatihan');
        
        $db = \Config\Database::connect();
        $kategoriSkp = $db->table('master_kategori_skp_pelatihan')
            ->where('nama_kategori', $data['kategori_kegiatan'] ?? '')
            ->get()->getRowArray();
        $kategoriSkpId = $kategoriSkp ? $kategoriSkp['id'] : null;

        $targetKhususProfesi = isset($data['target_khusus_profesi']) ? (is_array($data['target_khusus_profesi']) ? implode(',', $data['target_khusus_profesi']) : $data['target_khusus_profesi']) : '';
        $targetKhususUnit = isset($data['target_khusus_unit']) ? (is_array($data['target_khusus_unit']) ? implode(',', $data['target_khusus_unit']) : $data['target_khusus_unit']) : '';

        $insertData = [
            'tema' => $data['tema'] ?? null,
            'nama' => $data['nama'],
            'program' => $data['program'],
            'kategori' => $data['kategori'],
            'kategori_skp_id' => $kategoriSkpId,
            'biaya' => $data['biaya'] ?? 'Gratis',
            'nama_bank' => $data['nama_bank'] ?? null,
            'no_rekening' => $data['no_rekening'] ?? '',
            'atas_nama' => $data['atas_nama'] ?? null,
            'biaya_nominal' => $data['biaya_nominal'] ?? 0,
            'level' => $data['level'],
            'cakupan' => $data['cakupan'],
            'jpl' => (int)$data['jpl'],
            'skp' => $data['skp'] ?? 0,
            'mekanisme' => $data['mekanisme'],
            'target_khusus_profesi' => $targetKhususProfesi,
            'target_khusus_unit' => $targetKhususUnit,
            'metode' => $data['metode'],
            'kontak' => $data['kontak'],
            'reg_buka_tgl' => $data['reg_buka_tgl'] ?? null,
            'reg_buka_jam' => $data['reg_buka_jam'] ?? null,
            'reg_tutup_tgl' => $data['reg_tutup_tgl'] ?? null,
            'reg_tutup_jam' => $data['reg_tutup_jam'] ?? null,
            'jadwal_mulai' => $data['jadwal_mulai'],
            'jam_mulai' => $data['jam_mulai'],
            'jadwal_selesai' => $data['jadwal_selesai'],
            'jam_selesai' => $data['jam_selesai'],
            'kuota' => (int)$data['kuota'],
            'target_profesi' => isset($data['target_profesi']) ? (is_array($data['target_profesi']) ? implode(',', $data['target_profesi']) : $data['target_profesi']) : '',
            'pengumuman' => $data['pengumuman'],
            'tujuan' => $data['tujuan'],
            'deskripsi' => $data['deskripsi'],
            'kompetensi' => $data['kompetensi'],
            'status' => 'Draft',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($gambarPelatihan) {
            $insertData['gambar_pelatihan'] = $gambarPelatihan;
        }

        $this->masterPelatihanModel->insert($insertData);
        $insertedId = $this->masterPelatihanModel->getInsertID();

        // Handle Narasumber (pivot: narasumber_pelatihan)
        $narasumbers = $data['narasumber'] ?? [];
        if (!is_array($narasumbers)) $narasumbers = explode(',', $narasumbers);
        foreach ($narasumbers as $nid) {
            $nid = trim($nid);
            if (!empty($nid) && is_numeric($nid)) {
                $this->narasumberPelModel->insert([
                    'pelatihan_id' => $insertedId,
                    'pejabat_ttd_id' => (int)$nid,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Handle Penyelenggara (pivot: penyelenggara_pelatihan)
        $penyelenggaras = $data['penyelenggara'] ?? [];
        if (!is_array($penyelenggaras)) $penyelenggaras = explode(',', $penyelenggaras);
        foreach ($penyelenggaras as $pid) {
            $pid = trim($pid);
            if (!empty($pid) && is_numeric($pid)) {
                $this->penyelenggaraPelModel->insert([
                    'pelatihan_id' => $insertedId,
                    'penyelenggara_id' => (int)$pid,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        return redirect()->to('/pelatihan/admin/pelatihan')->with('success', 'Pelatihan berhasil ditambahkan sebagai Draft. Silakan publish jika sudah siap.');
    }

    public function publish(string $id)
    {
        $this->masterPelatihanModel->update($id, ['status' => 'Aktif', 'updated_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', 'Pelatihan berhasil di-publish.');
    }

    public function selesai(string $id)
    {
        $this->masterPelatihanModel->update($id, ['status' => 'Selesai', 'updated_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', 'Pelatihan telah diselesaikan.');
    }

    public function draft(string $id)
    {
        $this->masterPelatihanModel->update($id, ['status' => 'Draft', 'updated_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', 'Pelatihan dikembalikan ke Draft.');
    }

    public function batal(string $id)
    {
        $this->masterPelatihanModel->update($id, ['status' => 'Batal', 'updated_at' => date('Y-m-d H:i:s')]);
        return redirect()->back()->with('success', 'Pelatihan telah dibatalkan.');
    }

    public function update()
    {
        $data = $this->request->getPost();
        $id = $data['id'];
        $existing = $this->masterPelatihanModel->find($id);

        $db = \Config\Database::connect();
        $kategoriSkp = $db->table('master_kategori_skp_pelatihan')
            ->where('nama_kategori', $data['kategori_kegiatan'] ?? '')
            ->get()->getRowArray();
        $kategoriSkpId = $kategoriSkp ? $kategoriSkp['id'] : null;

        $targetKhususProfesi = isset($data['target_khusus_profesi']) ? (is_array($data['target_khusus_profesi']) ? implode(',', $data['target_khusus_profesi']) : $data['target_khusus_profesi']) : '';
        $targetKhususUnit = isset($data['target_khusus_unit']) ? (is_array($data['target_khusus_unit']) ? implode(',', $data['target_khusus_unit']) : $data['target_khusus_unit']) : '';

        $updateData = [
            'tema' => $data['tema'] ?? null,
            'nama' => $data['nama'],
            'program' => $data['program'],
            'kategori' => $data['kategori'],
            'kategori_skp_id' => $kategoriSkpId,
            'biaya' => $data['biaya'] ?? 'Gratis',
            'nama_bank' => $data['nama_bank'] ?? null,
            'no_rekening' => $data['no_rekening'] ?? '',
            'atas_nama' => $data['atas_nama'] ?? null,
            'biaya_nominal' => $data['biaya_nominal'] ?? 0,
            'level' => $data['level'],
            'cakupan' => $data['cakupan'],
            'jpl' => (int)$data['jpl'],
            'skp' => $data['skp'] ?? 0,
            'mekanisme' => $data['mekanisme'],
            'target_khusus_profesi' => $targetKhususProfesi,
            'target_khusus_unit' => $targetKhususUnit,
            'metode' => $data['metode'],
            'kontak' => $data['kontak'],
            'reg_buka_tgl' => $data['reg_buka_tgl'] ?? null,
            'reg_buka_jam' => $data['reg_buka_jam'] ?? null,
            'reg_tutup_tgl' => $data['reg_tutup_tgl'] ?? null,
            'reg_tutup_jam' => $data['reg_tutup_jam'] ?? null,
            'jadwal_mulai' => $data['jadwal_mulai'],
            'jam_mulai' => $data['jam_mulai'],
            'jadwal_selesai' => $data['jadwal_selesai'],
            'jam_selesai' => $data['jam_selesai'],
            'kuota' => (int)$data['kuota'],
            'target_profesi' => isset($data['target_profesi']) ? (is_array($data['target_profesi']) ? implode(',', $data['target_profesi']) : $data['target_profesi']) : '',
            'pengumuman' => $data['pengumuman'],
            'tujuan' => $data['tujuan'],
            'deskripsi' => $data['deskripsi'],
            'kompetensi' => $data['kompetensi'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $gambarPelatihan = $this->uploadGambarPelatihan($data['nama'] ?? 'Pelatihan');
        if ($gambarPelatihan) {
            if (!empty($existing['gambar_pelatihan']) && file_exists(FCPATH . $existing['gambar_pelatihan'])) {
                unlink(FCPATH . $existing['gambar_pelatihan']);
            }
            $updateData['gambar_pelatihan'] = $gambarPelatihan;
        }

        $this->masterPelatihanModel->update($id, $updateData);

        // Update Narasumber (pivot)
        $narasumbers = $data['narasumber'] ?? [];
        if (!is_array($narasumbers)) $narasumbers = explode(',', $narasumbers);
        $narasumbers = array_filter(array_map('trim', $narasumbers));

        $existingNarasumber = $this->narasumberPelModel->where('pelatihan_id', $id)->findAll();
        $existingPejabatIds = array_unique(array_column($existingNarasumber, 'pejabat_ttd_id'));

        foreach ($existingNarasumber as $en) {
            if (!in_array($en['pejabat_ttd_id'], $narasumbers)) {
                $this->narasumberPelModel->delete($en['id']);
            }
        }

        foreach ($narasumbers as $nid) {
            if (is_numeric($nid) && !in_array($nid, $existingPejabatIds)) {
                $this->narasumberPelModel->insert([
                    'pelatihan_id' => $id,
                    'pejabat_ttd_id' => (int)$nid,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Update Penyelenggara (pivot)
        $penyelenggaras = $data['penyelenggara'] ?? [];
        if (!is_array($penyelenggaras)) $penyelenggaras = explode(',', $penyelenggaras);
        $penyelenggaras = array_filter(array_map('trim', $penyelenggaras));

        $existingPenyelenggara = $this->penyelenggaraPelModel->where('pelatihan_id', $id)->findAll();
        $existingPenyelenggaraIds = array_unique(array_column($existingPenyelenggara, 'penyelenggara_id'));

        foreach ($existingPenyelenggara as $ep) {
            if (!in_array($ep['penyelenggara_id'], $penyelenggaras)) {
                $this->penyelenggaraPelModel->delete($ep['id']);
            }
        }

        foreach ($penyelenggaras as $pid) {
            if (is_numeric($pid) && !in_array($pid, $existingPenyelenggaraIds)) {
                $this->penyelenggaraPelModel->insert([
                    'pelatihan_id' => $id,
                    'penyelenggara_id' => (int)$pid,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        return redirect()->to('/pelatihan/admin/pelatihan')->with('success', 'Pelatihan berhasil diperbarui.');
    }

    private function uploadGambarPelatihan(string $namaPelatihan): ?string
    {
        $file = $this->request->getFile('gambar_pelatihan');
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower($file->getExtension());
        if (!in_array($extension, $allowedExtensions, true) || $file->getSize() > 2 * 1024 * 1024) {
            return null;
        }

        $targetDir = FCPATH . 'uploads/pelatihan/gambar_pelatihan';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $safeName = preg_replace('/[^A-Za-z0-9]/', '_', $namaPelatihan ?: 'Pelatihan');
        $newName = 'Gambar_' . $safeName . '_' . date('Ymd_His') . '.' . $extension;
        $file->move($targetDir, $newName);

        return 'uploads/pelatihan/gambar_pelatihan/' . $newName;
    }

    public function hapus(string $id)
    {
        $pelatihan = $this->masterPelatihanModel->find($id);
        if (!empty($pelatihan['gambar_pelatihan']) && file_exists(FCPATH . $pelatihan['gambar_pelatihan'])) {
            unlink(FCPATH . $pelatihan['gambar_pelatihan']);
        }
        $this->narasumberPelModel->where('pelatihan_id', $id)->delete();
        $this->penyelenggaraPelModel->where('pelatihan_id', $id)->delete();
        $this->masterPelatihanModel->delete($id);
        return redirect()->to('/pelatihan/admin/pelatihan')->with('success', 'Pelatihan berhasil dihapus.');
    }


    public function kelola(string $id)
    {
        $pelatihan = $this->masterPelatihanModel->find($id);
        
        if (empty($pelatihan)) return redirect()->to('/pelatihan/admin/pelatihan');

        $kuesionerDb = $this->kuesionerModel
            ->select('kuesioner_master_pelatihan.*, kategori_evaluasi_pelatihan.nama_kategori as kategori')
            ->join('kategori_evaluasi_pelatihan', 'kategori_evaluasi_pelatihan.id = kuesioner_master_pelatihan.kategori_id', 'left')
            ->where('pelatihan_id', $id)
            ->findAll();
        $kuesionerGrouped = [];
        foreach($kuesionerDb as $k) {
            $kuesionerGrouped[$k['kategori']][] = $k;
        }

        $masterNarasumberAll = $this->getNarasumberForPelatihan($id);
        $masterPenyelenggaraAll = $this->getPenyelenggaraForPelatihan($id);

        $sesiList = $this->sesiModel->where('pelatihan_id', $id)->orderBy('tanggal', 'ASC')->orderBy('waktu', 'ASC')->findAll();
        foreach ($sesiList as &$s) {
            $ns = $this->getNarasumberForSesi($s['id']);
            $s['narasumber'] = implode(', ', array_map(fn($n) => $this->formatNamaLengkap($n), $ns));
            $s['narasumber_arr'] = array_map(fn($n) => $this->formatNamaLengkap($n), $ns);
            $s['narasumber_ids'] = array_column($ns, 'id');
            
            $py = $this->getPenyelenggaraForSesi($s['id']);
            $s['penyelenggara'] = implode(', ', array_column($py, 'nama'));
            $s['penyelenggara_arr'] = array_column($py, 'nama');
            $s['penyelenggara_ids'] = array_column($py, 'id');
        }

        $data = [
            'title' => 'Manajemen Konten',
            'p' => $pelatihan,
            'materi' => $this->materiPelatihanModel->where('pelatihan_id', $id)->orderBy('urutan', 'ASC')->findAll(),
            'sesi_online' => $this->sesiModel->where('pelatihan_id', $id)->where('tipe_sesi', 'online')->findAll(),
            'sesi_offline' => $this->sesiModel->where('pelatihan_id', $id)->where('tipe_sesi', 'offline')->findAll(),
            'sesiList' => $sesiList,
            'master_narasumber' => $masterNarasumberAll,
            'master_penyelenggara' => $masterPenyelenggaraAll,
            'kuis' => [], 
            'presensi' => [],
            'kuesioner' => $kuesionerGrouped
        ];
        return view('Pelatihan/admin/pelatihan/kelola', $data);
    }

    public function simpan_materi()
    {
        $id = $this->request->getPost('pelatihan_id');
        $file = $this->request->getFile('file_materi');
        $filePath = '';

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedExtensions = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','ppt','pptx','txt','csv','mp4','webm','ogg','mp3','wav','m4a','mov','avi','mkv','wmv'];
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, $allowedExtensions, true)) {
                return redirect()->back()->with('error', 'Format file tidak didukung. Silakan gunakan gambar, PDF, Word, Excel, PowerPoint, video, atau teks.');
            }
            if (!is_dir(FCPATH . 'uploads/pelatihan/materi')) {
                mkdir(FCPATH . 'uploads/pelatihan/materi', 0777, true);
            }
            $pelatihan = $this->masterPelatihanModel->find($id);
            $namaPelatihan = preg_replace('/[^A-Za-z0-9]/', '_', $pelatihan['nama'] ?? 'Pelatihan');
            $judulMateri = preg_replace('/[^A-Za-z0-9]/', '_', $this->request->getPost('judul') ?? 'Materi');
            $newName = "Materi_{$namaPelatihan}_{$judulMateri}_" . date('Ymd_His') . "." . $file->getExtension();
            
            $file->move(FCPATH . 'uploads/pelatihan/materi', $newName);
            $filePath = 'uploads/pelatihan/materi/' . $newName;
        } elseif ($this->request->getPost('tipe') === 'link') {
            $filePath = $this->request->getPost('link_materi') ?? '';
        }

        $sesiId = $this->request->getPost('sesi_id') ?: null;
        $inputSegmen = $this->request->getPost('segmen');
        
        if ($inputSegmen === '' || $inputSegmen === null) {
            $db = \Config\Database::connect();
            $builder = $db->table('materi_pelatihan')->where('pelatihan_id', $id);
            if ($sesiId) {
                $builder->where('sesi_id', $sesiId);
            } else {
                $builder->where('sesi_id', null);
            }
            $maxRow = $builder->selectMax('segmen')->get()->getRowArray();
            $nextSegmen = ($maxRow && isset($maxRow['segmen'])) ? (int)$maxRow['segmen'] + 1 : 1;
        } else {
            $nextSegmen = (int)$inputSegmen;
        }

        $insertData = [
            'pelatihan_id' => $id,
            'sesi_id'      => $sesiId,
            'segmen'       => $nextSegmen,
            'urutan'       => $this->request->getPost('urutan') ?? 1.0,
            'judul'        => $this->request->getPost('judul'),
            'tipe'         => $this->request->getPost('tipe'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'file_path'    => $filePath,
            'created_at'   => date('Y-m-d H:i:s')
        ];

        $this->materiPelatihanModel->insert($insertData);
        return redirect()->back()->with('success', 'Materi berhasil ditambahkan');
    }

    public function update_materi()
    {
        $id = $this->request->getPost('id_materi');
        $materi = $this->materiPelatihanModel->find($id);
        if (!$materi) {
            return redirect()->back()->with('error', 'Materi tidak ditemukan');
        }

        $file = $this->request->getFile('file_materi');
        $filePath = $materi['file_path'];

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedExtensions = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','ppt','pptx','txt','csv','mp4','webm','ogg','mp3','wav','m4a','mov','avi','mkv','wmv'];
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, $allowedExtensions, true)) {
                return redirect()->back()->with('error', 'Format file tidak didukung. Silakan gunakan gambar, PDF, Word, Excel, PowerPoint, video, atau teks.');
            }
            // Delete old file if exists
            if (!empty($materi['file_path']) && file_exists(FCPATH . $materi['file_path'])) {
                unlink(FCPATH . $materi['file_path']);
            }
            if (!is_dir(FCPATH . 'uploads/pelatihan/materi')) {
                mkdir(FCPATH . 'uploads/pelatihan/materi', 0777, true);
            }
            $pelatihan = $this->masterPelatihanModel->find($materi['pelatihan_id']);
            $namaPelatihan = preg_replace('/[^A-Za-z0-9]/', '_', $pelatihan['nama'] ?? 'Pelatihan');
            $judulMateri = preg_replace('/[^A-Za-z0-9]/', '_', $this->request->getPost('judul') ?? 'Materi');
            $newName = "Materi_{$namaPelatihan}_{$judulMateri}_" . date('Ymd_His') . "." . $file->getExtension();
            
            $file->move(FCPATH . 'uploads/pelatihan/materi', $newName);
            $filePath = 'uploads/pelatihan/materi/' . $newName;
        } elseif ($this->request->getPost('tipe') === 'link' && !empty($this->request->getPost('link_materi'))) {
            $filePath = $this->request->getPost('link_materi');
        }

        $updateData = [
            'sesi_id'      => $this->request->getPost('sesi_id') ?: null,
            'segmen'       => (int)($this->request->getPost('segmen') ?? 1),
            'urutan'       => $this->request->getPost('urutan') ?? 1.0,
            'judul'        => $this->request->getPost('judul'),
            'tipe'         => $this->request->getPost('tipe'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'file_path'    => $filePath,
        ];

        $this->materiPelatihanModel->update($id, $updateData);
        return redirect()->back()->with('success', 'Materi berhasil diupdate');
    }

    public function hapus_materi(string $id)
    {
        $materi = $this->materiPelatihanModel->find($id);
        if ($materi) {
            if (!empty($materi['file_path']) && file_exists($materi['file_path'])) {
                unlink($materi['file_path']);
            }
            $this->materiPelatihanModel->delete($id);
            return redirect()->back()->with('success', 'Materi berhasil dihapus');
        }
        return redirect()->back()->with('error', 'Materi tidak ditemukan');
    }

    public function undangan(string $id)
    {
        $data = [
            'title' => 'Kelola Undangan',
            'id' => $id
        ];
        return view('pelatihan/admin/pelatihan/undangan', $data);
    }

    public function tambah_sesi_presensi()
    {
        $insertData = [
            'pelatihan_id' => $this->request->getPost('pelatihan_id'),
            'tipe_sesi' => 'offline',
            'nama_sesi' => $this->request->getPost('nama'),
            'waktu_mulai' => $this->request->getPost('waktu_mulai') ?? date('Y-m-d H:i:s'),
            'lokasi' => $this->request->getPost('lokasi'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->sesiModel->insert($insertData);
        return redirect()->back()->with('success', 'Sesi presensi berhasil ditambahkan');
    }

    public function simpan_online_setup()
    {
        $insertData = [
            'pelatihan_id' => $this->request->getPost('pelatihan_id'),
            'tipe_sesi' => 'online',
            'nama_sesi' => 'Online Meeting',
            'meeting_link' => $this->request->getPost('meeting_link'),
            'meeting_pass' => $this->request->getPost('meeting_pass'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->sesiModel->insert($insertData);
        return redirect()->back()->with('success', 'Setup Online berhasil disimpan');
    }

    public function simpan_offline_setup() {
        $insertData = [
            'pelatihan_id' => $this->request->getPost('pelatihan_id'),
            'tipe_sesi' => 'offline',
            'nama_sesi' => 'Offline Setup',
            'lokasi' => $this->request->getPost('tempat') . ' - ' . $this->request->getPost('alamat'),
            'maps_url' => $this->request->getPost('maps_url'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->sesiModel->insert($insertData);
        return redirect()->back()->with('success', 'Setup Offline berhasil disimpan');
    }

    public function simpan_sesi()
    {
        $idSesi = $this->request->getPost('id_sesi');
        $data = [
            'pelatihan_id' => $this->request->getPost('pelatihan_id'),
            'tipe_sesi'    => $this->request->getPost('tipe_sesi'),
            'nama_sesi'    => $this->request->getPost('nama_sesi'),
            'tanggal'      => $this->request->getPost('tanggal'),
            'waktu'        => $this->request->getPost('waktu'),
            'jam_tutup'    => $this->request->getPost('jam_tutup') ?: null,
            'lokasi_ruang' => $this->request->getPost('lokasi_ruang'),
            'tempat'       => $this->request->getPost('tempat'),
            'alamat'       => $this->request->getPost('alamat'),
            'maps_url'     => $this->request->getPost('maps_url'),
            'meeting_link' => $this->request->getPost('meeting_link'),
            'meeting_pass' => $this->request->getPost('meeting_pass'),
            'updated_at'   => date('Y-m-d H:i:s')
        ];

        if (empty($idSesi)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->sesiModel->insert($data);
            $idSesi = $this->sesiModel->getInsertID();
            $msg = 'Sesi berhasil ditambahkan';
        } else {
            $this->sesiModel->update($idSesi, $data);
            $msg = 'Sesi berhasil diupdate';
        }

        // Handle Narasumber for sesi (unified: narasumber_pelatihan with sesi_id)
        $narasumberIds = $this->request->getPost('narasumber') ?? [];
        if (!is_array($narasumberIds)) $narasumberIds = explode(',', $narasumberIds);
        $narasumberIds = array_filter(array_map('intval', $narasumberIds), fn($n) => $n > 0);

        // Deduplicate: remove existing duplicate rows for same pelatihan+pejabat+sesi
        $db = \Config\Database::connect();
        $db->query("DELETE np1 FROM narasumber_pelatihan np1
            INNER JOIN narasumber_pelatihan np2
            WHERE np1.id > np2.id
            AND np1.pejabat_ttd_id = np2.pejabat_ttd_id
            AND np1.pelatihan_id = np2.pelatihan_id
            AND np1.sesi_id = np2.sesi_id
            AND np1.pelatihan_id = ?", [$data['pelatihan_id']]);

        // Release narasumber that are no longer selected for this sesi
        $existingNarasumber = $this->narasumberPelModel
            ->where('pelatihan_id', $data['pelatihan_id'])
            ->where('sesi_id', $idSesi)
            ->findAll();
        foreach ($existingNarasumber as $en) {
            if (!in_array((int)$en['pejabat_ttd_id'], $narasumberIds)) {
                $this->narasumberPelModel->update($en['id'], ['sesi_id' => null]);
            }
        }

        foreach ($narasumberIds as $nid) {
            // Check exact combo: pelatihan + narasumber + sesi ini
            $alreadyInSesi = $this->narasumberPelModel
                ->where('pelatihan_id', $data['pelatihan_id'])
                ->where('pejabat_ttd_id', $nid)
                ->where('sesi_id', $idSesi)
                ->first();

            if ($alreadyInSesi) {
                // Sudah terisi di sesi ini → skip
                continue;
            }

            // Cari row dengan sesi_id kosong untuk override
            $unassigned = $this->narasumberPelModel
                ->where('pelatihan_id', $data['pelatihan_id'])
                ->where('pejabat_ttd_id', $nid)
                ->where('sesi_id', null)
                ->first();

            if ($unassigned) {
                $this->narasumberPelModel->update($unassigned['id'], ['sesi_id' => $idSesi]);
            } else {
                $this->narasumberPelModel->insert([
                    'pejabat_ttd_id' => $nid,
                    'pelatihan_id'   => $data['pelatihan_id'],
                    'sesi_id'        => $idSesi,
                    'created_at'     => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Handle Penyelenggara for sesi (unified: penyelenggara_pelatihan with sesi_id)
        $penyelenggaraIds = $this->request->getPost('penyelenggara') ?? [];
        if (!is_array($penyelenggaraIds)) $penyelenggaraIds = explode(',', $penyelenggaraIds);
        $penyelenggaraIds = array_filter(array_map('intval', $penyelenggaraIds), fn($p) => $p > 0);

        // Deduplicate existing duplicate rows
        $db->query("DELETE pp1 FROM penyelenggara_pelatihan pp1
            INNER JOIN penyelenggara_pelatihan pp2
            WHERE pp1.id > pp2.id
            AND pp1.penyelenggara_id = pp2.penyelenggara_id
            AND pp1.pelatihan_id = pp2.pelatihan_id
            AND pp1.sesi_id = pp2.sesi_id
            AND pp1.pelatihan_id = ?", [$data['pelatihan_id']]);

        // Release penyelenggara that are no longer selected for this sesi
        $existingPenyelenggara = $this->penyelenggaraPelModel
            ->where('pelatihan_id', $data['pelatihan_id'])
            ->where('sesi_id', $idSesi)
            ->findAll();
        foreach ($existingPenyelenggara as $ep) {
            if (!in_array((int)$ep['penyelenggara_id'], $penyelenggaraIds)) {
                $this->penyelenggaraPelModel->update($ep['id'], ['sesi_id' => null]);
            }
        }

        foreach ($penyelenggaraIds as $pid) {
            $alreadyInSesi = $this->penyelenggaraPelModel
                ->where('pelatihan_id', $data['pelatihan_id'])
                ->where('penyelenggara_id', $pid)
                ->where('sesi_id', $idSesi)
                ->first();

            if ($alreadyInSesi) {
                continue;
            }

            $unassigned = $this->penyelenggaraPelModel
                ->where('pelatihan_id', $data['pelatihan_id'])
                ->where('penyelenggara_id', $pid)
                ->where('sesi_id', null)
                ->first();

            if ($unassigned) {
                $this->penyelenggaraPelModel->update($unassigned['id'], ['sesi_id' => $idSesi]);
            } else {
                $this->penyelenggaraPelModel->insert([
                    'penyelenggara_id' => $pid,
                    'pelatihan_id'     => $data['pelatihan_id'],
                    'sesi_id'          => $idSesi,
                    'created_at'       => date('Y-m-d H:i:s')
                ]);
            }
        }

        return redirect()->back()->with('success', $msg);
    }

    public function hapus_sesi(string $id)
    {
        $sesi = $this->sesiModel->find($id);
        if ($sesi) {
            $this->narasumberPelModel->where('sesi_id', $id)->delete();
            $this->penyelenggaraPelModel->where('sesi_id', $id)->delete();
            $this->sesiModel->delete($id);
            return redirect()->back()->with('success', 'Sesi berhasil dihapus');
        }
        return redirect()->back()->with('error', 'Sesi tidak ditemukan');
    }

    public function get_evaluasi_soal(string $pelatihan_id, string $tipe)
    {
        $evaluasi = $this->evaluasiModel->where('pelatihan_id', $pelatihan_id)->where('tipe_evaluasi', $tipe)->first();
        if (!$evaluasi) {
            // Auto create evaluasi entry if not exists
            $this->evaluasiModel->insert([
                'pelatihan_id' => $pelatihan_id,
                'tipe_evaluasi' => $tipe,
                'kkm' => 70,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $evaluasi = $this->evaluasiModel->where('pelatihan_id', $pelatihan_id)->where('tipe_evaluasi', $tipe)->first();
        }

        $soal = $this->evaluasiSoalModel->where('ujian_id', $evaluasi['id'])->findAll();

        return $this->response->setJSON([
            'evaluasi' => $evaluasi,
            'soal' => $soal
        ]);
    }

    public function simpan_kkm()
    {
        $ujian_id = $this->request->getPost('ujian_id');
        $kkm = $this->request->getPost('kkm');
        
        $this->evaluasiModel->update($ujian_id, ['kkm' => $kkm, 'updated_at' => date('Y-m-d H:i:s')]);
        
        return $this->response->setJSON(['status' => 'success', 'message' => 'KKM berhasil disimpan']);
    }

    public function simpan_evaluasi_soal()
    {
        $id = $this->request->getPost('id_soal');
        $ujian_id = $this->request->getPost('ujian_id');
        
        $file = $this->request->getFile('file_soal');
        $filePath = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedExtensions = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','ppt','pptx','txt','csv','mp4','webm','ogg'];
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, $allowedExtensions, true)) {
                return redirect()->back()->with('error', 'Format lampiran soal tidak didukung. Silakan gunakan gambar, PDF, atau video.');
            }
            if (!is_dir(FCPATH . 'uploads/pelatihan/soal')) {
                mkdir(FCPATH . 'uploads/pelatihan/soal', 0777, true);
            }
            $ujian = $this->evaluasiModel->find($ujian_id);
            $pelatihan = $ujian ? $this->masterPelatihanModel->find($ujian['pelatihan_id']) : null;
            $namaPelatihan = preg_replace('/[^A-Za-z0-9]/', '_', $pelatihan['nama'] ?? 'Pelatihan');
            $newName = "Soal_{$namaPelatihan}_" . date('Ymd_His') . "." . $file->getExtension();
            
            $file->move(FCPATH . 'uploads/pelatihan/soal', $newName);
            $filePath = 'uploads/pelatihan/soal/' . $newName;
        }

        $data = [
            'ujian_id'      => $ujian_id,
            'materi_id'     => $this->request->getPost('materi_id') ?: null,
            'tipe_soal'     => 'Pilihan Ganda',
            'pertanyaan'    => $this->request->getPost('pertanyaan'),
            'opsi_a'        => $this->request->getPost('opsi_a'),
            'opsi_b'        => $this->request->getPost('opsi_b'),
            'opsi_c'        => $this->request->getPost('opsi_c'),
            'opsi_d'        => $this->request->getPost('opsi_d'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
        ];

        if ($filePath) {
            $data['file_path'] = $filePath;
        }

        if ($id) {
            if ($filePath) {
                // Delete old file
                $oldSoal = $this->evaluasiSoalModel->find($id);
                if ($oldSoal && !empty($oldSoal['file_path']) && file_exists(FCPATH . $oldSoal['file_path'])) {
                    unlink(FCPATH . $oldSoal['file_path']);
                }
            }
            $this->evaluasiSoalModel->update($id, $data);
            $message = 'Soal berhasil diupdate';
        } else {
            $this->evaluasiSoalModel->insert($data);
            $message = 'Soal berhasil ditambahkan';
        }

        return redirect()->back()->with('success', $message);
    }

    public function hapus_evaluasi_soal(string $id)
    {
        $soal = $this->evaluasiSoalModel->find($id);
        if ($soal) {
            if (!empty($soal['file_path']) && file_exists($soal['file_path'])) {
                unlink($soal['file_path']);
            }
            $this->evaluasiSoalModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Soal berhasil dihapus']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Soal tidak ditemukan']);
    }

    public function hapus_file_soal(string $id)
    {
        $soal = $this->evaluasiSoalModel->find($id);
        if ($soal && !empty($soal['file_path']) && file_exists($soal['file_path'])) {
            unlink($soal['file_path']);
            $this->evaluasiSoalModel->update($id, ['file_path' => null]);
            return $this->response->setJSON(['status' => 'success']);
        }
        return $this->response->setJSON(['status' => 'error']);
    }

    public function get_kuesioner(string $pelatihan_id)
    {
        $kuesioner = $this->kuesionerModel
            ->select('kuesioner_master_pelatihan.*, kategori_evaluasi_pelatihan.nama_kategori as kategori')
            ->join('kategori_evaluasi_pelatihan', 'kategori_evaluasi_pelatihan.id = kuesioner_master_pelatihan.kategori_id', 'left')
            ->where('pelatihan_id', $pelatihan_id)
            ->findAll();
        
        $kategoriList = []; // Kategori that currently have questions
        $dataGrouped = [];
        
        foreach ($kuesioner as $k) {
            if (!in_array($k['kategori'], $kategoriList)) {
                $kategoriList[] = $k['kategori'];
                $dataGrouped[$k['kategori']] = [];
            }
            $dataGrouped[$k['kategori']][] = $k;
        }

        // Ambil semua master kategori dari KategoriEvaluasiPelatihanModel
        $kategoriMasterModel = new \App\Models\Pelatihan\KategoriEvaluasiPelatihanModel();
        $allKategoriDb = $kategoriMasterModel->findAll();
        $allKategori = array_column($allKategoriDb, 'nama_kategori');

        return $this->response->setJSON([
            'kategori' => $kategoriList,       // existing used categories
            'allKategori' => $allKategori,     // all available master categories
            'kuesioner' => $dataGrouped
        ]);
    }

    public function simpan_kuesioner()
    {
        $pelatihan_id = $this->request->getPost('pelatihan_id');
        $kategori = $this->request->getPost('kategori');
        $kategori_baru = $this->request->getPost('kategori_baru');
        $pertanyaan = $this->request->getPost('pertanyaan');
        
        $kategoriMasterModel = new \App\Models\Pelatihan\KategoriEvaluasiPelatihanModel();
        
        if (!empty($kategori_baru)) {
            $kategori = $kategori_baru;
        }

        if (empty($kategori) || empty($pertanyaan)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kategori dan Pertanyaan tidak boleh kosong.']);
        }

        $katRecord = $kategoriMasterModel->where('nama_kategori', $kategori)->first();
        if (!$katRecord) {
            $kategoriMasterModel->insert(['nama_kategori' => $kategori]);
            $kategoriId = $kategoriMasterModel->getInsertID();
        } else {
            $kategoriId = $katRecord['id'];
        }

        $this->kuesionerModel->insert([
            'pelatihan_id' => $pelatihan_id,
            'kategori_id' => $kategoriId,
            'pertanyaan' => $pertanyaan
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Kuesioner berhasil ditambahkan.']);
    }

    public function hapus_kuesioner(string $id)
    {
        $this->kuesionerModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Pertanyaan kuesioner berhasil dihapus.']);
    }

    public function update_kuesioner()
    {
        $id = $this->request->getPost('id');
        $pertanyaan = $this->request->getPost('pertanyaan');
        $this->kuesionerModel->update($id, ['pertanyaan' => $pertanyaan]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function generate_template_kuesioner(string $pelatihan_id)
    {
        $template = [
            ['kategori' => 'Fasilitator', 'pertanyaan' => 'Penguasaan materi oleh narasumber/fasilitator'],
            ['kategori' => 'Fasilitator', 'pertanyaan' => 'Kemampuan interaksi dan penyampaian fasilitator'],
            ['kategori' => 'Fasilitator', 'pertanyaan' => 'Kerapihan dan penampilan fasilitator'],
            ['kategori' => 'Materi', 'pertanyaan' => 'Kesesuaian materi dengan kebutuhan pekerjaan'],
            ['kategori' => 'Materi', 'pertanyaan' => 'Kelengkapan dan relevansi materi pelatihan'],
            ['kategori' => 'Modul', 'pertanyaan' => 'Kemudahan memahami bahasa dan sistematika modul'],
            ['kategori' => 'Modul', 'pertanyaan' => 'Kualitas visual slide presentasi dan materi ajar'],
            ['kategori' => 'Narasumber', 'pertanyaan' => 'Penguasaan materi oleh narasumber'],
            ['kategori' => 'Narasumber', 'pertanyaan' => 'Kemampuan interaksi dan penyampaian narasumber'],
            ['kategori' => 'Narasumber', 'pertanyaan' => 'Ketepatan waktu penyampaian materi oleh narasumber'],
            ['kategori' => 'Penyelenggara', 'pertanyaan' => 'Kinerja penyelenggara dalam pelaksanaan pelatihan'],
            ['kategori' => 'Penyelenggara', 'pertanyaan' => 'Kepuasan terhadap layanan penyelenggara'],
            ['kategori' => 'Penyelenggara', 'pertanyaan' => 'Ketepatan informasi dan koordinasi penyelenggara'],
        ];

        // Hapus yang lama jika ada (agar tidak double jika dipanggil ulang)
        $this->kuesionerModel->where('pelatihan_id', $pelatihan_id)->delete();

        $kategoriMasterModel = new \App\Models\Pelatihan\KategoriEvaluasiPelatihanModel();

        foreach ($template as $item) {
            $katRecord = $kategoriMasterModel->where('nama_kategori', $item['kategori'])->first();
            if (!$katRecord) {
                $kategoriMasterModel->insert(['nama_kategori' => $item['kategori']]);
                $kategoriId = $kategoriMasterModel->getInsertID();
            } else {
                $kategoriId = $katRecord['id'];
            }

            $this->kuesionerModel->insert([
                'pelatihan_id' => $pelatihan_id,
                'kategori_id' => $kategoriId,
                'pertanyaan' => $item['pertanyaan']
            ]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Template kuesioner berhasil dimuat.']);
    }

    public function pengaturan_logo()
    {
        $logoModel = new \App\Models\Pelatihan\PengaturanLogoPelatihanModel();
        $logoSetup = $logoModel->where('id', 1)->first();
        if (!$logoSetup) {
            $logoModel->insert([
                'id' => 1,
                'logo_path' => 'assets/img/logo_rs.jpg',
                'favicon_path' => 'assets/img/logo_rs.jpg',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $logoSetup = $logoModel->where('id', 1)->first();
        }

        $data = [
            'title' => 'Pengaturan Logo Sistem',
            'logoSetup' => $logoSetup
        ];
        return view('Pelatihan/admin/pengaturan_logo/index', $data);
    }

    public function update_logo()
    {
        $logoModel = new \App\Models\Pelatihan\PengaturanLogoPelatihanModel();
        $logoSetup = $logoModel->where('id', 1)->first();

        $updateData = ['updated_at' => date('Y-m-d H:i:s')];

        // Handle logo file upload
        $logoFile = $this->request->getFile('logo_sistem');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            if (!is_safe_upload($logoFile)) {
                return redirect()->back()->with('error', 'File logo sistem tidak aman atau format tidak didukung.');
            }
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/system')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/system', 0777, true);
            }
            $newName = "system_logo_" . date('Ymd_His') . "." . $logoFile->getExtension();
            $logoFile->move(ROOTPATH . 'public/uploads/pelatihan/system', $newName);
            $updateData['logo_path'] = 'system/' . $newName;
        }

        // Handle favicon file upload
        $faviconFile = $this->request->getFile('favicon_sistem');
        if ($faviconFile && $faviconFile->isValid() && !$faviconFile->hasMoved()) {
            if (!is_safe_upload($faviconFile)) {
                return redirect()->back()->with('error', 'File favicon sistem tidak aman atau format tidak didukung.');
            }
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/system')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/system', 0777, true);
            }
            $newName = "system_favicon_" . date('Ymd_His') . "." . $faviconFile->getExtension();
            $faviconFile->move(ROOTPATH . 'public/uploads/pelatihan/system', $newName);
            $updateData['favicon_path'] = 'system/' . $newName;
        }

        if ($logoSetup) {
            $logoModel->update(1, $updateData);
        } else {
            $updateData['id'] = 1;
            $logoModel->insert($updateData);
        }

        return redirect()->back()->with('success', 'Logo sistem berhasil diperbarui secara menyeluruh.');
    }
}
