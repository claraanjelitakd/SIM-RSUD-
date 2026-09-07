<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;

class Feedback extends BaseController
{
    public function index()
    {
        return redirect()->to('/pelatihan/admin/monitoring_peserta');
    }

    public function detail($id)
    {
        $masterModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $p = $masterModel->find($id);
        
        if (empty($p)) return redirect()->to('/pelatihan/admin/monitoring_peserta');

        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $pesertaList = $pesertaModel->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->where('peserta_pelatihan.pelatihan_id', $id)
            ->findAll();

        $feedbacks = [];
        $totalRating = 0;
        $count = 0;
        
        $db = \Config\Database::connect();
        foreach ($pesertaList as $pl) {
            $saran = $db->table('peserta_kuesioner_saran_pelatihan')
                        ->where('peserta_pelat_id', $pl['id'])
                        ->get()->getRowArray();
                        
            if ($saran) {
                $rating = $saran['rating_umum'];
                $komentar = $saran['saran_masukan'];
                
                // Fetch post-test score
                $postTest = $db->table('peserta_ujian_pelatihan')
                               ->select('score as nilai_score')
                               ->where('peserta_pelat_id', $pl['id'])
                               ->where('tipe_ujian', 'post_test')
                               ->orderBy('created_at', 'DESC')
                               ->get()->getRowArray();
                $skorPostTest = $postTest ? $postTest['nilai_score'] : null;

                // Fetch detailed answers
                $jawaban = $db->table('peserta_kuesioner_rating_pelatihan')
                              ->select('kuesioner_master_pelatihan.pertanyaan, kategori_evaluasi_pelatihan.nama_kategori as kategori, peserta_kuesioner_rating_pelatihan.nilai_rating, sesi_interaktif_pelatihan.nama_sesi')
                              ->join('kuesioner_master_pelatihan', 'kuesioner_master_pelatihan.id = peserta_kuesioner_rating_pelatihan.kuesioner_id')
                              ->join('kategori_evaluasi_pelatihan', 'kategori_evaluasi_pelatihan.id = kuesioner_master_pelatihan.kategori_id', 'left')
                              ->join('sesi_interaktif_pelatihan', 'sesi_interaktif_pelatihan.id = peserta_kuesioner_rating_pelatihan.sesi_id', 'left')
                              ->where('peserta_kuesioner_rating_pelatihan.peserta_pelat_id', $pl['id'])
                              ->get()->getResultArray();

                // Group jawaban by Sesi, then Category
                $jawabanDetail = [];
                foreach ($jawaban as $j) {
                    $sesi = !empty($j['nama_sesi']) ? $j['nama_sesi'] : 'Keseluruhan';
                    $kat = $j['kategori'];
                    if (!isset($jawabanDetail[$sesi])) $jawabanDetail[$sesi] = [];
                    if (!isset($jawabanDetail[$sesi][$kat])) $jawabanDetail[$sesi][$kat] = [];
                    $jawabanDetail[$sesi][$kat][] = $j;
                }

                $feedbacks[] = [
                    'nama' => $pl['nama'],
                    'rating' => $rating,
                    'komentar' => $komentar,
                    'skor_post_test' => $skorPostTest,
                    'jawaban_detail' => $jawabanDetail
                ];
                $totalRating += $rating;
                $count++;
            }
        }

        if ($count == 0) {
            $avg = 0;
        } else {
            $avg = round($totalRating / $count, 1);
        }

        // Fetch detailed question ratings
        $questionStats = [];
        $questions = $db->table('kuesioner_master_pelatihan')
            ->select('kuesioner_master_pelatihan.*, kategori_evaluasi_pelatihan.nama_kategori as kategori')
            ->join('kategori_evaluasi_pelatihan', 'kategori_evaluasi_pelatihan.id = kuesioner_master_pelatihan.kategori_id', 'left')
            ->where('pelatihan_id', $id)
            ->get()->getResultArray();
        
        if (!empty($questions)) {
            foreach ($questions as $q) {
                // Calculate average rating for this question based on peserta of this pelatihan
                $ratingStat = $db->table('peserta_kuesioner_rating_pelatihan')
                    ->selectAvg('nilai_rating')
                    ->selectCount('id', 'total_votes')
                    ->where('kuesioner_id', $q['id'])
                    ->get()->getRowArray();
                
                $q['avg_rating'] = $ratingStat['nilai_rating'] ? round($ratingStat['nilai_rating'], 1) : 0;
                $q['total_votes'] = $ratingStat['total_votes'] ?: 0;
                
                $questionStats[$q['kategori']][] = $q;
            }
        }

        $data = [
            'title' => 'Detail Feedback: ' . $p['nama'],
            'p' => $p,
            'avg' => $avg,
            'feedbacks' => $feedbacks,
            'questionStats' => $questionStats
        ];

        // ─── Aggregate ratings by Sesi ───────────────────────────────────────
        $sesiStats = [];
        $sesiList = $db->table('sesi_interaktif_pelatihan')->where('pelatihan_id', $id)->get()->getResultArray();
        foreach ($sesiList as $sesi) {
            $ratingsForSesi = $db->table('peserta_kuesioner_rating_pelatihan')
                ->select('peserta_kuesioner_rating_pelatihan.kuesioner_id, kuesioner_master_pelatihan.pertanyaan, AVG(peserta_kuesioner_rating_pelatihan.nilai_rating) as avg_rating, COUNT(peserta_kuesioner_rating_pelatihan.id) as total_votes')
                ->join('kuesioner_master_pelatihan', 'kuesioner_master_pelatihan.id = peserta_kuesioner_rating_pelatihan.kuesioner_id', 'left')
                ->where('peserta_kuesioner_rating_pelatihan.sesi_id', $sesi['id'])
                ->groupBy('peserta_kuesioner_rating_pelatihan.kuesioner_id')
                ->get()->getResultArray();
            if (!empty($ratingsForSesi)) {
                $sesiStats[] = [
                    'id'   => $sesi['id'],
                    'nama' => $sesi['nama_sesi'],
                    'pertanyaan' => array_map(function($r) {
                        return ['pertanyaan' => $r['pertanyaan'], 'avg_rating' => round((float)$r['avg_rating'], 1), 'total_votes' => (int)$r['total_votes']];
                    }, $ratingsForSesi),
                    'avg_overall' => count($ratingsForSesi) > 0 ? round(array_sum(array_column($ratingsForSesi, 'avg_rating')) / count($ratingsForSesi), 1) : 0,
                ];
            }
        }

        // ─── Aggregate ratings by Materi ─────────────────────────────────────
        $materiStats = [];
        $materiList = $db->table('materi_pelatihan')->where('pelatihan_id', $id)->orderBy('urutan', 'ASC')->get()->getResultArray();
        foreach ($materiList as $materi) {
            $ratingsForMateri = $db->table('peserta_kuesioner_rating_pelatihan')
                ->select('peserta_kuesioner_rating_pelatihan.kuesioner_id, kuesioner_master_pelatihan.pertanyaan, AVG(peserta_kuesioner_rating_pelatihan.nilai_rating) as avg_rating, COUNT(peserta_kuesioner_rating_pelatihan.id) as total_votes')
                ->join('kuesioner_master_pelatihan', 'kuesioner_master_pelatihan.id = peserta_kuesioner_rating_pelatihan.kuesioner_id', 'left')
                ->where('peserta_kuesioner_rating_pelatihan.materi_id', $materi['id'])
                ->groupBy('peserta_kuesioner_rating_pelatihan.kuesioner_id')
                ->get()->getResultArray();
            if (!empty($ratingsForMateri)) {
                $materiStats[] = [
                    'id'    => $materi['id'],
                    'judul' => $materi['judul'],
                    'pertanyaan' => array_map(function($r) {
                        return ['pertanyaan' => $r['pertanyaan'], 'avg_rating' => round((float)$r['avg_rating'], 1), 'total_votes' => (int)$r['total_votes']];
                    }, $ratingsForMateri),
                    'avg_overall' => count($ratingsForMateri) > 0 ? round(array_sum(array_column($ratingsForMateri, 'avg_rating')) / count($ratingsForMateri), 1) : 0,
                ];
            }
        }

        // ─── Aggregate ratings by Narasumber ──────────────────────────────────
        $narasumberStats = [];
        $narasumberList = $db->table('narasumber_pelatihan')
            ->select('narasumber_pelatihan.id, pejabat_ttd_pelatihan.nama_pejabat, pejabat_ttd_pelatihan.gelar_depan, pejabat_ttd_pelatihan.gelar_belakang')
            ->join('pejabat_ttd_pelatihan', 'pejabat_ttd_pelatihan.id = narasumber_pelatihan.pejabat_ttd_id', 'left')
            ->where('narasumber_pelatihan.pelatihan_id', $id)
            ->get()->getResultArray();
        foreach ($narasumberList as $narasumber) {
            $ratingsForNar = $db->table('peserta_kuesioner_rating_pelatihan')
                ->select('peserta_kuesioner_rating_pelatihan.kuesioner_id, kuesioner_master_pelatihan.pertanyaan, AVG(peserta_kuesioner_rating_pelatihan.nilai_rating) as avg_rating, COUNT(peserta_kuesioner_rating_pelatihan.id) as total_votes')
                ->join('kuesioner_master_pelatihan', 'kuesioner_master_pelatihan.id = peserta_kuesioner_rating_pelatihan.kuesioner_id', 'left')
                ->where('peserta_kuesioner_rating_pelatihan.narasumber_id', $narasumber['id'])
                ->groupBy('peserta_kuesioner_rating_pelatihan.kuesioner_id')
                ->get()->getResultArray();
            if (!empty($ratingsForNar)) {
                $narasumberStats[] = [
                    'id'   => $narasumber['id'],
                    'nama' => ($narasumber['gelar_depan'] ? $narasumber['gelar_depan'].' ' : '').$narasumber['nama_pejabat'].($narasumber['gelar_belakang'] ? ', '.$narasumber['gelar_belakang'] : ''),
                    'pertanyaan' => array_map(function($r) {
                        return ['pertanyaan' => $r['pertanyaan'], 'avg_rating' => round((float)$r['avg_rating'], 1), 'total_votes' => (int)$r['total_votes']];
                    }, $ratingsForNar),
                    'avg_overall' => count($ratingsForNar) > 0 ? round(array_sum(array_column($ratingsForNar, 'avg_rating')) / count($ratingsForNar), 1) : 0,
                ];
            }
        }

        // ─── Aggregate ratings by Penyelenggara ───────────────────────────────
        $penyelenggaraStats = [];
        $penyelenggaraList = $db->table('penyelenggara_pelatihan')
            ->select('penyelenggara_pelatihan.id, master_penyelenggara.nama')
            ->join('master_penyelenggara', 'master_penyelenggara.id = penyelenggara_pelatihan.penyelenggara_id', 'left')
            ->where('penyelenggara_pelatihan.pelatihan_id', $id)
            ->get()->getResultArray();
        foreach ($penyelenggaraList as $penyelenggara) {
            $ratingsForPen = $db->table('peserta_kuesioner_rating_pelatihan')
                ->select('peserta_kuesioner_rating_pelatihan.kuesioner_id, kuesioner_master_pelatihan.pertanyaan, AVG(peserta_kuesioner_rating_pelatihan.nilai_rating) as avg_rating, COUNT(peserta_kuesioner_rating_pelatihan.id) as total_votes')
                ->join('kuesioner_master_pelatihan', 'kuesioner_master_pelatihan.id = peserta_kuesioner_rating_pelatihan.kuesioner_id', 'left')
                ->where('peserta_kuesioner_rating_pelatihan.penyelenggara_id', $penyelenggara['id'])
                ->groupBy('peserta_kuesioner_rating_pelatihan.kuesioner_id')
                ->get()->getResultArray();
            if (!empty($ratingsForPen)) {
                $penyelenggaraStats[] = [
                    'id'   => $penyelenggara['id'],
                    'nama' => $penyelenggara['nama'] ?? $penyelenggara['penyelenggara_id'],
                    'pertanyaan' => array_map(function($r) {
                        return ['pertanyaan' => $r['pertanyaan'], 'avg_rating' => round((float)$r['avg_rating'], 1), 'total_votes' => (int)$r['total_votes']];
                    }, $ratingsForPen),
                    'avg_overall' => count($ratingsForPen) > 0 ? round(array_sum(array_column($ratingsForPen, 'avg_rating')) / count($ratingsForPen), 1) : 0,
                ];
            }
        }

        $data['materiStats']       = $materiStats;
        $data['narasumberStats']   = $narasumberStats;
        $data['penyelenggaraStats'] = $penyelenggaraStats;
        $data['sesiStats']         = $sesiStats;

        return view('Pelatihan/admin/feedback/detail', $data);
    }
}
