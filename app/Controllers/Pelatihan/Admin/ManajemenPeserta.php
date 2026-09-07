<?php
namespace App\Controllers\Pelatihan\Admin;
use App\Controllers\BaseController;
use App\Models\Pelatihan\UserPelatihanModel;
use App\Models\Pelatihan\UnitKerjaPelatihanModel;
use App\Models\Pelatihan\ProfesiPelatihanModel;

class ManajemenPeserta extends BaseController
{
    public function index()
    {
        $userModel = new UserPelatihanModel();
        $pesertaPelatihanModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $db = \Config\Database::connect();

        // Auto update status to Selesai if the end date and time have passed
        $db->query("UPDATE master_pelatihan SET status = 'Selesai' WHERE status != 'Selesai' AND jadwal_selesai IS NOT NULL AND jam_selesai IS NOT NULL AND LENGTH(jadwal_selesai) > 5 AND LENGTH(jam_selesai) > 3 AND CONCAT(jadwal_selesai, ' ', jam_selesai) <= NOW()");

        $selectedYear = $this->request->getVar('tahun') ?? date('Y');
        
        $dbUsers = $userModel->select('users_pelatihan.*, profesi_pelatihan.nama_profesi as profesi, profesi_pelatihan.kategori_target as kategori_target, profesi_pelatihan.target_jpl as target_jpl_profesi, unit_kerja_pelatihan.nama_unit as ruangan, users_pelatihan.id_profesi')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('users_pelatihan.role', 'peserta')
            ->findAll();

        $targetKelompok = $this->session->get('target_kelompok') ?? [
            'Named' => 20,
            'Non-Named' => 20
        ];
        
        $stats = [];
        $niks = array_column($dbUsers, 'nik');
        
        $pelatByUser = [];
        $certsByUser = [];
        $fallbackByUser = [];
        $lastPelatByUser = [];
        $lastRegByUser = [];
        $progressByUser = [];

        if (!empty($niks)) {
            // Get all completed trainings at once
            $allCompletedPelat = $pesertaPelatihanModel->select('peserta_pelatihan.user_id, master_pelatihan.jpl, master_pelatihan.nama, master_pelatihan.jadwal_selesai')
                ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                ->whereIn('peserta_pelatihan.user_id', $niks)
                ->where('peserta_pelatihan.status_peserta', 'Lulus')
                ->where('master_pelatihan.cert_published', 1)
                ->findAll();
            foreach ($allCompletedPelat as $cp) {
                $pelatByUser[$cp['user_id']][] = $cp;
            }

            // Get all approved external certificates at once
            $allApprovedCerts = $db->table('sertifikat_pelatihan')
                ->whereIn('user_id', $niks)
                ->where('verifikasi', 'approved')
                ->where('jenis_dokumen !=', 'rsud')
                ->get()->getResultArray();
            foreach ($allApprovedCerts as $ac) {
                $certsByUser[$ac['user_id']][] = $ac;
            }

            // Get all approved RSUD fallback certificates at once
            $rsudFallbackCerts = $db->table('sertifikat_pelatihan sp')
                ->select('sp.user_id, sp.skp, sp.tgl_selesai, sp.judul')
                ->whereIn('sp.user_id', $niks)
                ->where('sp.verifikasi', 'approved')
                ->where('sp.jenis_dokumen', 'rsud')
                ->where("NOT EXISTS (SELECT 1 FROM peserta_pelatihan pp WHERE pp.user_id = sp.user_id AND pp.pelatihan_id = sp.pelatihan_id AND pp.status_peserta = 'Lulus')", null, false)
                ->get()->getResultArray();
            foreach ($rsudFallbackCerts as $rc) {
                $fallbackByUser[$rc['user_id']][] = $rc;
            }

            // Get last training name and reg status
            $allRegistrations = $pesertaPelatihanModel->select('peserta_pelatihan.*, master_pelatihan.nama as nama_pelatihan')
                ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                ->whereIn('peserta_pelatihan.user_id', $niks)
                ->orderBy('peserta_pelatihan.id', 'ASC') // ASC so the last one overwrites previous in loop
                ->findAll();

            $totalSesiByPelatihan = [];
            
            foreach ($allRegistrations as $reg) {
                $lastRegByUser[$reg['user_id']] = $reg;
                $lastPelatByUser[$reg['user_id']] = $reg['nama_pelatihan'];
            }

            $lastRegIds = [];
            $pelatihanIds = [];
            foreach ($lastRegByUser as $reg) {
                $lastRegIds[] = $reg['id'];
                $pelatihanIds[] = $reg['pelatihan_id'];
            }

            if (!empty($pelatihanIds)) {
                $totalSesiRows = $db->table('sesi_interaktif_pelatihan')
                    ->select('pelatihan_id, COUNT(id) as total')
                    ->whereIn('pelatihan_id', array_unique($pelatihanIds))
                    ->groupBy('pelatihan_id')
                    ->get()->getResultArray();
                foreach ($totalSesiRows as $row) {
                    $totalSesiByPelatihan[$row['pelatihan_id']] = $row['total'];
                }
            }

            if (!empty($lastRegIds)) {
                $hadirSesiRows = $db->table('peserta_presensi_pelatihan')
                    ->select('peserta_pelat_id, COUNT(id) as total')
                    ->whereIn('peserta_pelat_id', $lastRegIds)
                    ->where('status_hadir', 'Hadir')
                    ->groupBy('peserta_pelat_id')
                    ->get()->getResultArray();
                $hadirSesiByReg = [];
                foreach ($hadirSesiRows as $row) {
                    $hadirSesiByReg[$row['peserta_pelat_id']] = $row['total'];
                }
                
                foreach ($lastRegByUser as $userId => $reg) {
                    $tSesi = $totalSesiByPelatihan[$reg['pelatihan_id']] ?? 0;
                    $hSesi = $hadirSesiByReg[$reg['id']] ?? 0;
                    $progressByUser[$userId] = $tSesi > 0 ? ($hSesi / $tSesi) * 100 : 0;
                }
            }
        }
        
        $capaianUpdateBatch = []; // For static column sync
        foreach ($dbUsers as $u) {
            $nik = $u['nik'];
            $lastPelatName = $lastPelatByUser[$nik] ?? 'Belum Ada';
            $regStatus = isset($lastRegByUser[$nik]) ? $lastRegByUser[$nik]['status_peserta'] : 'Tidak Ada';
            $progressVal = $progressByUser[$nik] ?? 0;

            $myCompletedPelat = $pelatByUser[$nik] ?? [];
            $myApprovedCerts = $certsByUser[$nik] ?? [];
            $myFallbackCerts = $fallbackByUser[$nik] ?? [];
            
            $completedJpl = 0;
            $history = [];
            foreach ($myCompletedPelat as $cp) {
                $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                if ($yearOfTraining == $selectedYear) {
                    $completedJpl += (int)($cp['jpl'] ?? 0);
                }
                $history[] = [
                    'nama' => '[Internal] ' . $cp['nama'],
                    'jpl' => $cp['jpl'],
                    'tanggal' => !empty($cp['jadwal_selesai']) ? date('d M Y', strtotime($cp['jadwal_selesai'])) : '-'
                ];
            }

            foreach ($myApprovedCerts as $ac) {
                $yearOfTraining = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
                if ($yearOfTraining == $selectedYear) {
                    $completedJpl += (int)($ac['skp'] ?? 0);
                }
                $history[] = [
                    'nama' => '[Eksternal] ' . $ac['judul'],
                    'jpl' => $ac['skp'],
                    'tanggal' => !empty($ac['tgl_selesai']) ? date('d M Y', strtotime($ac['tgl_selesai'])) : '-'
                ];
            }

            foreach ($myFallbackCerts as $rc) {
                $yearOfTraining = !empty($rc['tgl_selesai']) ? date('Y', strtotime($rc['tgl_selesai'])) : date('Y');
                if ($yearOfTraining == $selectedYear) {
                    $completedJpl += (int)($rc['skp'] ?? 0);
                }
                $history[] = [
                    'nama' => '[Internal/Fallback] ' . $rc['judul'],
                    'jpl' => $rc['skp'],
                    'tanggal' => !empty($rc['tgl_selesai']) ? date('d M Y', strtotime($rc['tgl_selesai'])) : '-'
                ];
            }

            // Sync static column only for current year calculations
            if ($selectedYear == date('Y')) {
                $capaianUpdateBatch[] = [
                    'nik' => $nik,
                    'capaian_jpl' => $completedJpl
                ];
            }

            $kategoriTarget = $u['kategori_target'] ?: 'Non-Named';
            $targetJPLKaryawan = $u['target_jpl_profesi'] ?? 20;
            
            $stats[] = [
                'nik' => $nik,
                'nama' => $u['nama_lengkap'],
                'profesi' => $u['profesi'] ?? '-',
                'kategori_target' => $kategoriTarget,
                'divisi' => $u['ruangan'] ?? 'Umum',
                'pelatihan' => $lastPelatName,
                'status_reg' => $regStatus,
                'progress' => $progressVal,
                'jpl' => $completedJpl,
                'target_jpl' => $targetJPLKaryawan,
                'history' => $history
            ];
        }

        if (!empty($capaianUpdateBatch)) {
            $userModel->updateBatch($capaianUpdateBatch, 'nik');
        }

        // Room/Ruangan stats calculation
        $unitKerjaList = $db->table('unit_kerja_pelatihan')->get()->getResultArray();
        
        // Group employees by room
        $employeesByRoom = [];
        foreach ($dbUsers as $u) {
            $employeesByRoom[$u['id_unit_kerja']][] = $u;
        }

        $ruanganStats = [];
        foreach ($unitKerjaList as $ruang) {
            $employeesInRoom = $employeesByRoom[$ruang['id_unit_kerja']] ?? [];
            $totalEmployees = count($employeesInRoom);
            
            $followedList = [];
            $notFollowedList = [];
            foreach ($employeesInRoom as $emp) {
                $nik = $emp['nik'];
                $myCompletedPelat = $pelatByUser[$nik] ?? [];
                $myApprovedCerts = $certsByUser[$nik] ?? [];
                $myFallbackCerts = $fallbackByUser[$nik] ?? [];

                $completedJpl = 0;
                foreach ($myCompletedPelat as $cp) {
                    $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                    if ($yearOfTraining == $selectedYear) {
                        $completedJpl += (int)($cp['jpl'] ?? 0);
                    }
                }
                foreach ($myApprovedCerts as $ac) {
                    $yearOfTraining = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
                    if ($yearOfTraining == $selectedYear) {
                        $completedJpl += (int)($ac['skp'] ?? 0);
                    }
                }
                foreach ($myFallbackCerts as $rc) {
                    $yearOfTraining = !empty($rc['tgl_selesai']) ? date('Y', strtotime($rc['tgl_selesai'])) : date('Y');
                    if ($yearOfTraining == $selectedYear) {
                        $completedJpl += (int)($rc['skp'] ?? 0);
                    }
                }

                $targetJPLKaryawan = $emp['target_jpl_profesi'] ?? 20;
                
                $kurangVal = max(0, $targetJPLKaryawan - $completedJpl);
                $isMet = $completedJpl >= $targetJPLKaryawan;
                
                $empDetails = [
                    'nama' => $emp['nama_lengkap'],
                    'nik' => $emp['nik'],
                    'profesi' => $emp['profesi'] ?? '-',
                    'target' => $targetJPLKaryawan,
                    'jpl' => $completedJpl,
                    'kurang' => $kurangVal,
                    'status' => $isMet ? 'Tercapai' : 'Belum'
                ];

                if ($isMet) {
                    $followedList[] = $empDetails;
                } else {
                    $notFollowedList[] = $empDetails;
                }
            }
            
            $pct = $totalEmployees > 0 ? (count($followedList) / $totalEmployees) * 100 : 0;
            $ruanganStats[] = [
                'nama_unit' => $ruang['nama_unit'],
                'total' => $totalEmployees,
                'persen' => $pct,
                'sudah' => $followedList,
                'belum' => $notFollowedList
            ];
        }

        // Load all professions to let admin classify them
        $profesiList = $db->table('profesi_pelatihan')->get()->getResultArray();

        // Summary data
        $totalKaryawan = count($stats);
        $totalTargetJPL = array_sum(array_column($stats, 'target_jpl'));
        $totalJPLCapaian = array_sum(array_column($stats, 'jpl'));
        $totalKurangJPL = max(0, $totalTargetJPL - $totalJPLCapaian);
        $totalTidakAktif = count(array_filter($stats, fn($s) => $s['pelatihan'] === 'Belum Ada'));
        $rataRataJPL = $totalKaryawan > 0 ? $totalJPLCapaian / $totalKaryawan : 0;

        $data = [
            'title' => 'Monitoring & Matriks JPL',
            'stats' => $stats,
            'selectedYear' => $selectedYear,
            'targetKelompok' => $targetKelompok,
            'profesiList' => $profesiList,
            'ruanganStats' => $ruanganStats,
            'totalKaryawan' => $totalKaryawan,
            'totalTargetJPL' => $totalTargetJPL,
            'totalJPLCapaian' => $totalJPLCapaian,
            'totalKurangJPL' => $totalKurangJPL,
            'totalTidakAktif' => $totalTidakAktif,
            'rataRataJPL' => $rataRataJPL
        ];
        return view('Pelatihan/admin/monitoring/index', $data);
    }

    public function detail_stat($type)
    {
        $userModel = new UserPelatihanModel();
        $pesertaPelatihanModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $db = \Config\Database::connect();

        $selectedYear = $this->request->getVar('tahun') ?? date('Y');

        $dbUsers = $userModel->select('users_pelatihan.*, profesi_pelatihan.nama_profesi as profesi, profesi_pelatihan.kategori_target as kategori_target, profesi_pelatihan.target_jpl as target_jpl_profesi, unit_kerja_pelatihan.nama_unit as ruangan, users_pelatihan.id_profesi')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('users_pelatihan.role', 'peserta')
            ->findAll();

        $niks = array_column($dbUsers, 'nik');
        $pelatByUser = [];
        $certsByUser = [];
        $fallbackByUser = [];
        $lastPelatByUser = [];
        $lastRegByUser = [];

        if (!empty($niks)) {
            $allCompletedPelat = $pesertaPelatihanModel->select('peserta_pelatihan.user_id, master_pelatihan.jpl, master_pelatihan.nama, master_pelatihan.jadwal_selesai')
                ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                ->whereIn('peserta_pelatihan.user_id', $niks)
                ->where('peserta_pelatihan.status_peserta', 'Lulus')
                ->where('master_pelatihan.cert_published', 1)
                ->findAll();
            foreach ($allCompletedPelat as $cp) {
                $pelatByUser[$cp['user_id']][] = $cp;
            }

            $allApprovedCerts = $db->table('sertifikat_pelatihan')
                ->whereIn('user_id', $niks)
                ->where('verifikasi', 'approved')
                ->where('jenis_dokumen !=', 'rsud')
                ->get()->getResultArray();
            foreach ($allApprovedCerts as $ac) {
                $certsByUser[$ac['user_id']][] = $ac;
            }

            $rsudFallbackCerts = $db->table('sertifikat_pelatihan sp')
                ->select('sp.user_id, sp.skp, sp.tgl_selesai, sp.judul')
                ->whereIn('sp.user_id', $niks)
                ->where('sp.verifikasi', 'approved')
                ->where('sp.jenis_dokumen', 'rsud')
                ->where("NOT EXISTS (SELECT 1 FROM peserta_pelatihan pp WHERE pp.user_id = sp.user_id AND pp.pelatihan_id = sp.pelatihan_id AND pp.status_peserta = 'Lulus')", null, false)
                ->get()->getResultArray();
            foreach ($rsudFallbackCerts as $rc) {
                $fallbackByUser[$rc['user_id']][] = $rc;
            }

            $allRegistrations = $pesertaPelatihanModel->select('peserta_pelatihan.*, master_pelatihan.nama as nama_pelatihan')
                ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                ->whereIn('peserta_pelatihan.user_id', $niks)
                ->orderBy('peserta_pelatihan.id', 'ASC')
                ->findAll();
            foreach ($allRegistrations as $reg) {
                $lastRegByUser[$reg['user_id']] = $reg;
                $lastPelatByUser[$reg['user_id']] = $reg['nama_pelatihan'];
            }
        }

        $stats = [];
        foreach ($dbUsers as $u) {
            $nik = $u['nik'];
            $lastPelatName = $lastPelatByUser[$nik] ?? 'Belum Ada';
            $regStatus = isset($lastRegByUser[$nik]) ? $lastRegByUser[$nik]['status_peserta'] : 'Tidak Ada';

            $myCompletedPelat = $pelatByUser[$nik] ?? [];
            $myApprovedCerts = $certsByUser[$nik] ?? [];
            $myFallbackCerts = $fallbackByUser[$nik] ?? [];

            $completedJpl = 0;
            $history = [];
            foreach ($myCompletedPelat as $cp) {
                $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                if ($yearOfTraining == $selectedYear) {
                    $completedJpl += (int)($cp['jpl'] ?? 0);
                }
                $history[] = [
                    'nama' => '[Internal] ' . $cp['nama'],
                    'jpl' => $cp['jpl'],
                    'tanggal' => !empty($cp['jadwal_selesai']) ? date('d M Y', strtotime($cp['jadwal_selesai'])) : '-'
                ];
            }
            foreach ($myApprovedCerts as $ac) {
                $yearOfTraining = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
                if ($yearOfTraining == $selectedYear) {
                    $completedJpl += (int)($ac['skp'] ?? 0);
                }
                $history[] = [
                    'nama' => '[Eksternal] ' . $ac['judul'],
                    'jpl' => $ac['skp'],
                    'tanggal' => !empty($ac['tgl_selesai']) ? date('d M Y', strtotime($ac['tgl_selesai'])) : '-'
                ];
            }
            foreach ($myFallbackCerts as $rc) {
                $yearOfTraining = !empty($rc['tgl_selesai']) ? date('Y', strtotime($rc['tgl_selesai'])) : date('Y');
                if ($yearOfTraining == $selectedYear) {
                    $completedJpl += (int)($rc['skp'] ?? 0);
                }
                $history[] = [
                    'nama' => '[Internal/Fallback] ' . $rc['judul'],
                    'jpl' => $rc['skp'],
                    'tanggal' => !empty($rc['tgl_selesai']) ? date('d M Y', strtotime($rc['tgl_selesai'])) : '-'
                ];
            }

            $targetJPLKaryawan = $u['target_jpl_profesi'] ?? 20;

            $stats[] = [
                'nik' => $nik,
                'nama' => $u['nama_lengkap'],
                'profesi' => $u['profesi'] ?? '-',
                'kategori_target' => $u['kategori_target'] ?: 'Non-Named',
                'divisi' => $u['ruangan'] ?? 'Umum',
                'pelatihan' => $lastPelatName,
                'status_reg' => $regStatus,
                'jpl' => $completedJpl,
                'target_jpl' => $targetJPLKaryawan,
                'history' => $history
            ];
        }

        $filtered = [];
        $title = '';
        switch ($type) {
            case 'all':
                $filtered = $stats;
                $title = 'Semua Karyawan Aktif';
                break;
            case 'kurang':
                $filtered = array_filter($stats, fn($s) => $s['jpl'] < $s['target_jpl']);
                $title = 'Belum Memenuhi Target';
                break;
            case 'cukup':
                $filtered = array_filter($stats, fn($s) => $s['jpl'] >= $s['target_jpl']);
                $title = 'Sudah Memenuhi Target';
                break;
            case 'tidak_aktif':
                $filtered = array_filter($stats, fn($s) => $s['pelatihan'] === 'Belum Ada');
                $title = 'Karyawan Tidak Aktif';
                break;
            case '20jpl':
                $filtered = array_filter($stats, fn($s) => $s['jpl'] >= 20);
                $title = 'Karyawan dengan Capaian ≥ 20 JPL';
                break;
            default:
                return redirect()->to('/pelatihan/admin/monitoring');
        }
        $filtered = array_values($filtered);

        $data = [
            'title' => $title,
            'type' => $type,
            'stats' => $filtered,
            'selectedYear' => $selectedYear,
            'totalCount' => count($filtered)
        ];
        return view('Pelatihan/admin/monitoring/detail_stat', $data);
    }

    public function export_jpl_excel()
    {
        $userModel = new UserPelatihanModel();
        $pesertaPelatihanModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $db = \Config\Database::connect();

        $selectedYear = $this->request->getVar('tahun') ?? date('Y');

        $dbUsers = $userModel->select('users_pelatihan.*, profesi_pelatihan.nama_profesi as profesi, profesi_pelatihan.target_jpl as target_jpl_profesi, unit_kerja_pelatihan.nama_unit as ruangan')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('users_pelatihan.role', 'peserta')
            ->findAll();

        $niks = array_column($dbUsers, 'nik');
        $pelatByUser = [];
        $certsByUser = [];
        $fallbackByUser = [];

        if (!empty($niks)) {
            $allCompletedPelat = $pesertaPelatihanModel->select('peserta_pelatihan.user_id, master_pelatihan.nama as judul, master_pelatihan.jpl, master_pelatihan.jadwal_mulai as tgl_mulai, master_pelatihan.jadwal_selesai as tgl_selesai')
                ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
                ->whereIn('peserta_pelatihan.user_id', $niks)
                ->where('peserta_pelatihan.status_peserta', 'Lulus')
                ->where('master_pelatihan.cert_published', 1)
                ->findAll();
            foreach ($allCompletedPelat as $cp) {
                $pelatByUser[$cp['user_id']][] = $cp;
            }

            $allApprovedCerts = $db->table('sertifikat_pelatihan')
                ->whereIn('user_id', $niks)
                ->where('verifikasi', 'approved')
                ->where('jenis_dokumen !=', 'rsud')
                ->get()->getResultArray();
            foreach ($allApprovedCerts as $ac) {
                $certsByUser[$ac['user_id']][] = $ac;
            }

            $rsudFallbackCerts = $db->table('sertifikat_pelatihan sp')
                ->select('sp.user_id, sp.skp, sp.tgl_mulai, sp.tgl_selesai, sp.judul, sp.penerbit, sp.jenis_dokumen, sp.created_at')
                ->whereIn('sp.user_id', $niks)
                ->where('sp.verifikasi', 'approved')
                ->where('sp.jenis_dokumen', 'rsud')
                ->where("NOT EXISTS (SELECT 1 FROM peserta_pelatihan pp WHERE pp.user_id = sp.user_id AND pp.pelatihan_id = sp.pelatihan_id AND pp.status_peserta = 'Lulus')", null, false)
                ->get()->getResultArray();
            foreach ($rsudFallbackCerts as $rc) {
                $fallbackByUser[$rc['user_id']][] = $rc;
            }
        }

        $rows = [];
        $totalJPL = 0;
        $totalTarget = 0;

        foreach ($dbUsers as $u) {
            $nik = $u['nik'];
            $target = (int)($u['target_jpl_profesi'] ?? 20);
            $completedJpl = 0;

            foreach (($pelatByUser[$nik] ?? []) as $cp) {
                $y = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                if ($y == $selectedYear) $completedJpl += (int)($cp['jpl'] ?? 0);
            }
            foreach (($certsByUser[$nik] ?? []) as $ac) {
                $y = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
                if ($y == $selectedYear) $completedJpl += (int)($ac['skp'] ?? 0);
            }
            foreach (($fallbackByUser[$nik] ?? []) as $rc) {
                $y = !empty($rc['tgl_selesai']) ? date('Y', strtotime($rc['tgl_selesai'])) : date('Y');
                if ($y == $selectedYear) $completedJpl += (int)($rc['skp'] ?? 0);
            }

            $totalJPL += $completedJpl;
            $totalTarget += $target;

            $rows[] = [
                'nama'    => $u['nama_lengkap'],
                'nik'     => $nik,
                'profesi' => $u['profesi'] ?? '-',
                'ruangan' => $u['ruangan'] ?? '-',
                'jpl'     => $completedJpl,
                'target'  => $target,
            ];
        }

        usort($rows, fn($a, $b) => strcmp($a['nama'], $b['nama']));

        $count = count($rows);
        $rataRata = $count > 0 ? round($totalJPL / $count, 1) : 0;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat JPL Peserta');

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center']
        ];
        $subtitleStyle = [
            'font' => ['size' => 11],
            'alignment' => ['horizontal' => 'center']
        ];
        $summaryStyle = [
            'font' => ['bold' => true, 'size' => 11]
        ];
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '212529']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'DEE2E6']]]
        ];
        $cellCenter = [
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'DEE2E6']]]
        ];
        $cellLeft = [
            'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'DEE2E6']]]
        ];
        $greenFill = [
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D4EDDA']]
        ];
        $yellowFill = [
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FFF3CD']]
        ];
        $redFill = [
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F8D7DA']]
        ];

        // Title
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'LAPORAN RIWAYAT JPL PESERTA');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']]);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Tahun Evaluasi: ' . $selectedYear);
        $sheet->getStyle('A2')->applyFromArray($subtitleStyle);

        // Summary
        $sheet->setCellValue('A4', 'Banyak Karyawan');
        $sheet->setCellValue('B4', $count . ' orang');
        $sheet->getStyle('A4')->applyFromArray($summaryStyle);

        $sheet->setCellValue('A5', 'Total Capaian JPL');
        $sheet->setCellValue('B5', $totalJPL . ' JPL');
        $sheet->getStyle('A5')->applyFromArray($summaryStyle);

        $sheet->setCellValue('A6', 'Total Target JPL');
        $sheet->setCellValue('B6', $totalTarget . ' JPL');
        $sheet->getStyle('A6')->applyFromArray($summaryStyle);

        $sheet->setCellValue('A7', 'Rata-rata JPL');
        $sheet->setCellValue('B7', $rataRata . ' JPL');
        $sheet->getStyle('A7')->applyFromArray($summaryStyle);
        $sheet->getStyle('B7')->applyFromArray(['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0D6EFD']]]);

        $pegawai20 = 0;
        foreach ($rows as $r) {
            if ($r['jpl'] >= 20) $pegawai20++;
        }
        $persen20 = $count > 0 ? round(($pegawai20 / $count) * 100, 1) : 0;

        $sheet->setCellValue('A8', 'Persentase ≥ 20 JPL');
        $sheet->setCellValue('B8', $persen20 . '% (' . $pegawai20 . ' pegawai)');
        $sheet->getStyle('A8')->applyFromArray($summaryStyle);
        $sheet->getStyle('B8')->applyFromArray(['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '198754']]]);

        // Helper: convert column index to letter
        $colLetter = function($n) {
            $s = '';
            while ($n > 0) { $n--; $s = chr(65 + ($n % 26)) . $s; $n = intdiv($n, 26); }
            return $s;
        };

        // Header row
        $headers = ['No', 'Nama', 'NIK', 'Profesi', 'Ruangan', 'Capaian JPL', 'Target JPL'];
        $headerRow = 10;
        foreach ($headers as $col => $val) {
            $sheet->setCellValue($colLetter($col + 1) . $headerRow, $val);
        }
        $sheet->getStyle('A10:G10')->applyFromArray($headerStyle);

        // Data rows
        foreach ($rows as $i => $r) {
            $row = $headerRow + 1 + $i;
            $pct = $r['target'] > 0 ? round(($r['jpl'] / $r['target']) * 100) : 0;

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $r['nama']);
            $sheet->setCellValue('C' . $row, $r['nik']);
            $sheet->setCellValue('D' . $row, $r['profesi']);
            $sheet->setCellValue('E' . $row, $r['ruangan']);
            $sheet->setCellValue('F' . $row, $r['jpl']);
            $sheet->setCellValue('G' . $row, $r['target']);

            // Apply borders to entire row
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($cellLeft);

            // Center columns: No, Capaian JPL, Target JPL
            $sheet->getStyle('A' . $row)->applyFromArray($cellCenter);
            $sheet->getStyle('F' . $row)->applyFromArray($cellCenter);
            $sheet->getStyle('G' . $row)->applyFromArray($cellCenter);

            // Color the JPL cell
            $fill = $r['jpl'] >= $r['target'] ? $greenFill : ($pct >= 50 ? $yellowFill : $redFill);
            $sheet->getStyle('F' . $row)->applyFromArray($fill);
        }

        // Auto width
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(22);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);

        // === SHEET 2: DETAIL RIWAYAT ===
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Detail Riwayat');

        $sheet2->mergeCells('A1:K1');
        $sheet2->setCellValue('A1', 'Monitoring JPL Pegawai');
        $sheet2->mergeCells('A2:K2');
        $sheet2->setCellValue('A2', 'RSUD Kota Yogyakarta');
        
        $currentMonthName = strtoupper(date('F'));
        $sheet2->mergeCells('A3:K3');
        $sheet2->setCellValue('A3', 'BULAN ' . $currentMonthName);
        $sheet2->mergeCells('A4:K4');
        $sheet2->setCellValue('A4', 'Tahun ' . $selectedYear);

        $sheet2->getStyle('A1:K2')->applyFromArray(['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => 'center']]);
        $sheet2->getStyle('A3:K3')->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF0000']], 'alignment' => ['horizontal' => 'center']]);
        $sheet2->getStyle('A4:K4')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']]);

        $headers2 = ['NO', 'NIK', 'NAMA', 'TANGGAL', 'TEMPAT', 'JENIS', 'ACARA', 'PENYELENGGARA', 'TANGGAL MULAI', 'TANGGAL SELESAI', 'JPL'];
        $headerRow2 = 6;
        foreach ($headers2 as $col => $val) {
            $sheet2->setCellValue($colLetter($col + 1) . $headerRow2, $val);
        }
        $headerStyle2 = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']]]
        ];
        $sheet2->getStyle('A6:K6')->applyFromArray($headerStyle2);

        $row2 = 7;
        $no2 = 1;

        foreach ($dbUsers as $u) {
            $nik = $u['nik'];
            $nama = $u['nama_lengkap'];

            $userHistories = [];
            foreach (($pelatByUser[$nik] ?? []) as $cp) {
                $y = !empty($cp['tgl_selesai']) ? date('Y', strtotime($cp['tgl_selesai'])) : date('Y');
                if ($y == $selectedYear) {
                    $userHistories[] = [
                        'tanggal' => !empty($cp['tgl_selesai']) ? date('d/m/Y', strtotime($cp['tgl_selesai'])) : '-',
                        'tempat' => $cp['tempat'] ?? 'RSUD Kota Yogyakarta',
                        'jenis' => 'Internal',
                        'acara' => $cp['judul'],
                        'penyelenggara' => 'RSUD Kota Yogyakarta',
                        'tgl_mulai' => !empty($cp['tgl_mulai']) ? date('d/m/Y', strtotime($cp['tgl_mulai'])) : '-',
                        'tgl_selesai' => !empty($cp['tgl_selesai']) ? date('d/m/Y', strtotime($cp['tgl_selesai'])) : '-',
                        'jpl' => (float)($cp['jpl'] ?? 0),
                    ];
                }
            }
            foreach (($certsByUser[$nik] ?? []) as $ac) {
                $y = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
                if ($y == $selectedYear) {
                    $tgl = !empty($ac['created_at']) ? date('d/m/Y', strtotime($ac['created_at'])) : (!empty($ac['tgl_selesai']) ? date('d/m/Y', strtotime($ac['tgl_selesai'])) : '-');
                    $userHistories[] = [
                        'tanggal' => $tgl,
                        'tempat' => $ac['tempat'] ?? '-',
                        'jenis' => ucfirst($ac['jenis_dokumen']),
                        'acara' => $ac['judul'],
                        'penyelenggara' => $ac['penerbit'] ?? '-',
                        'tgl_mulai' => !empty($ac['tgl_mulai']) ? date('d/m/Y', strtotime($ac['tgl_mulai'])) : '-',
                        'tgl_selesai' => !empty($ac['tgl_selesai']) ? date('d/m/Y', strtotime($ac['tgl_selesai'])) : '-',
                        'jpl' => (float)($ac['skp'] ?? 0), 
                    ];
                }
            }
            foreach (($fallbackByUser[$nik] ?? []) as $rc) {
                $y = !empty($rc['tgl_selesai']) ? date('Y', strtotime($rc['tgl_selesai'])) : date('Y');
                if ($y == $selectedYear) {
                    $tgl = !empty($rc['created_at']) ? date('d/m/Y', strtotime($rc['created_at'])) : (!empty($rc['tgl_selesai']) ? date('d/m/Y', strtotime($rc['tgl_selesai'])) : '-');
                    $userHistories[] = [
                        'tanggal' => $tgl,
                        'tempat' => $rc['tempat'] ?? 'RSUD Kota Yogyakarta',
                        'jenis' => 'Internal/Fallback',
                        'acara' => $rc['judul'],
                        'penyelenggara' => $rc['penerbit'] ?? 'RSUD Kota Yogyakarta',
                        'tgl_mulai' => !empty($rc['tgl_mulai']) ? date('d/m/Y', strtotime($rc['tgl_mulai'])) : '-',
                        'tgl_selesai' => !empty($rc['tgl_selesai']) ? date('d/m/Y', strtotime($rc['tgl_selesai'])) : '-',
                        'jpl' => (float)($rc['skp'] ?? 0),
                    ];
                }
            }

            if (!empty($userHistories)) {
                $first = true;
                foreach ($userHistories as $uh) {
                    $sheet2->setCellValue('A' . $row2, $first ? $no2++ : '');
                    $sheet2->setCellValueExplicit('B' . $row2, $first ? $nik : '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet2->setCellValue('C' . $row2, $first ? $nama : '');
                    
                    $sheet2->setCellValue('D' . $row2, $uh['tanggal']);
                    $sheet2->setCellValue('E' . $row2, $uh['tempat']);
                    $sheet2->setCellValue('F' . $row2, $uh['jenis']);
                    $sheet2->setCellValue('G' . $row2, $uh['acara']);
                    $sheet2->setCellValue('H' . $row2, $uh['penyelenggara']);
                    $sheet2->setCellValue('I' . $row2, $uh['tgl_mulai']);
                    $sheet2->setCellValue('J' . $row2, $uh['tgl_selesai']);
                    $sheet2->setCellValue('K' . $row2, $uh['jpl'] > 0 ? $uh['jpl'] : '');
                    
                    $sheet2->getStyle('A' . $row2 . ':K' . $row2)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']]]
                    ]);
                    
                    $row2++;
                    $first = false;
                }
            }
        }
        
        foreach(range('A','K') as $columnID) {
            $sheet2->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet2->getColumnDimension('G')->setWidth(40);
        $sheet2->getColumnDimension('H')->setWidth(30);

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'Riwayat_JPL_Peserta_' . $selectedYear . '.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    public function set_target($userId)
    {
        // individual target override (if needed)
        return redirect()->to('/pelatihan/admin/monitoring')->with('success', 'Konfigurasi target berhasil diperbarui.');
    }

    public function jpl_history(string $userId)
    {
        $db = \Config\Database::connect();
        $selectedYear = $this->request->getGet('tahun') ?? date('Y');

        // 1. Internal completed trainings
        $myCompletedPelat = $db->table('peserta_pelatihan')
            ->select('master_pelatihan.nama as judul, master_pelatihan.jpl as skp, master_pelatihan.jadwal_selesai as tgl_selesai, "rsud" as jenis')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->where('peserta_pelatihan.user_id', $userId)
            ->where('peserta_pelatihan.status_peserta', 'Lulus')
            ->where('master_pelatihan.cert_published', 1)
            ->get()->getResultArray();

        // 2. External / Mandiri
        $myApprovedCerts = $db->table('sertifikat_pelatihan')
            ->select('judul, skp, tgl_selesai, jenis_dokumen as jenis')
            ->where('user_id', $userId)
            ->where('verifikasi', 'approved')
            ->where('jenis_dokumen !=', 'rsud')
            ->get()->getResultArray();

        // 3. Fallback RSUD certificates
        $rsudFallbackCerts = $db->table('sertifikat_pelatihan sp')
            ->select('sp.judul, sp.skp, sp.tgl_selesai, "rsud" as jenis')
            ->where('sp.user_id', $userId)
            ->where('sp.verifikasi', 'approved')
            ->where('sp.jenis_dokumen', 'rsud')
            ->where("NOT EXISTS (SELECT 1 FROM peserta_pelatihan pp WHERE pp.user_id = sp.user_id AND pp.pelatihan_id = sp.pelatihan_id AND pp.status_peserta = 'Lulus')", null, false)
            ->get()->getResultArray();

        $allHistory = array_merge($myCompletedPelat, $myApprovedCerts, $rsudFallbackCerts);
        
        $aktif = [];
        $kadaluarsa = [];

        foreach ($allHistory as $h) {
            $year = !empty($h['tgl_selesai']) ? date('Y', strtotime($h['tgl_selesai'])) : date('Y');
            if ($year == $selectedYear) {
                $aktif[] = $h;
            } else {
                $kadaluarsa[] = $h;
            }
        }

        return $this->response->setJSON([
            'aktif' => $aktif,
            'kadaluarsa' => $kadaluarsa
        ]);
    }

    public function remind_individual()
    {
        $json = $this->request->getJSON();
        $nik = $json->nik ?? '';
        $message = $json->message ?? 'Pengingat capaian JPL tahunan Anda.';
        
        if (empty($nik)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'NIK tidak valid.']);
        }
        
        $notifModel = new \App\Models\Pelatihan\NotifikasiPelatihanModel();
        $notifModel->insert([
            'user_id' => $nik,
            'title' => 'Peringatan Capaian JPL',
            'message' => $message,
            'type' => 'warning',
            'is_read' => 0
        ]);
        
        $userModel = new UserPelatihanModel();
        $user = $userModel->where('nik', $nik)->first();
        
        return $this->response->setJSON(['status' => 'success']);
    }

    public function broadcast_room()
    {
        $json = $this->request->getJSON();
        $niks = $json->niks ?? [];
        $message = $json->message ?? 'Pengingat capaian JPL tahunan Anda.';
        
        if (empty($niks)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada peserta dipilih.']);
        }
        
        $userModel = new UserPelatihanModel();
        $pesertaPelatihanModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $notifModel = new \App\Models\Pelatihan\NotifikasiPelatihanModel();
        $db = \Config\Database::connect();
        
        $targetKelompok = $this->session->get('target_kelompok') ?? [
            'Named' => 20,
            'Non-Named' => 20
        ];
        
        $sentCount = 0;
        
        // Optimize: Fetch all users at once
        $users = $userModel->select('users_pelatihan.*, profesi_pelatihan.kategori_target, profesi_pelatihan.target_jpl as target_jpl_profesi')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->whereIn('users_pelatihan.nik', $niks)
            ->findAll();
            
        if (empty($users)) {
            return $this->response->setJSON(['status' => 'success', 'count' => 0]);
        }

        // Optimize: Fetch all completed trainings for these users at once
        $allCompletedPelat = $pesertaPelatihanModel->select('peserta_pelatihan.user_id, master_pelatihan.jpl, master_pelatihan.jadwal_selesai')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->whereIn('peserta_pelatihan.user_id', $niks)
            ->where('peserta_pelatihan.status_peserta', 'Lulus')
            ->where('master_pelatihan.cert_published', 1)
            ->findAll();
            
        $pelatByUser = [];
        foreach ($allCompletedPelat as $cp) {
            $pelatByUser[$cp['user_id']][] = $cp;
        }

        // Optimize: Fetch all approved external certs at once
        $allApprovedCerts = $db->table('sertifikat_pelatihan')
            ->whereIn('user_id', $niks)
            ->where('verifikasi', 'approved')
            ->where('jenis_dokumen !=', 'rsud')
            ->get()->getResultArray();
            
        $certsByUser = [];
        foreach ($allApprovedCerts as $ac) {
            $certsByUser[$ac['user_id']][] = $ac;
        }
        
        $notifData = [];
        $currentYear = date('Y');

        foreach ($users as $user) {
            $nik = $user['nik'];
            $myCompletedPelat = $pelatByUser[$nik] ?? [];
            $myApprovedCerts = $certsByUser[$nik] ?? [];
            
            $completedJpl = 0;
            foreach ($myCompletedPelat as $cp) {
                $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                if ($yearOfTraining == $currentYear) $completedJpl += (int)($cp['jpl'] ?? 0);
            }
            foreach ($myApprovedCerts as $ac) {
                $yearOfTraining = !empty($ac['tgl_selesai']) ? date('Y', strtotime($ac['tgl_selesai'])) : date('Y');
                if ($yearOfTraining == $currentYear) $completedJpl += (int)($ac['skp'] ?? 0);
            }
            
            $targetJPL = $user['target_jpl_profesi'] ?? 20;
            $kurangVal = max(0, $targetJPL - $completedJpl);
            
            $personalized = str_replace(
                ['{nama}', '{jpl}', '{target}', '{kurang}'],
                [$user['nama_lengkap'], $completedJpl, $targetJPL, $kurangVal],
                $message
            );
            
            $notifData[] = [
                'user_id' => $nik,
                'title' => 'Peringatan Capaian JPL - Room Broadcast',
                'message' => $personalized,
                'type' => 'warning',
                'is_read' => 0
            ];
            
            $sentCount++;
        }
        
        if (!empty($notifData)) {
            $notifModel->insertBatch($notifData);
        }
        
        return $this->response->setJSON(['status' => 'success', 'count' => $sentCount]);
    }

    public function save_mapping_kelompok()
    {
        $profesiTarget = $this->request->getPost('profesi_target'); // e.g. [1 => 20, 2 => 20]
        $db = \Config\Database::connect();
        
        if (!empty($profesiTarget)) {
            foreach ($profesiTarget as $idProf => $target) {
                $db->table('profesi_pelatihan')
                    ->where('id_profesi', $idProf)
                    ->update(['target_jpl' => $target]);
            }
        }
        
        return redirect()->to('/pelatihan/admin/monitoring')->with('success', 'Target JPL profesi berhasil disimpan.')->with('active_tab', 'matriks');
    }

    public function broadcast_notifikasi()
    {
        $divisi = $this->request->getPost('divisi');
        $statusJpl = $this->request->getPost('status_jpl');
        $message = $this->request->getPost('message') ?: 'Pemberitahuan penting mengenai capaian program diklat Anda.';
        
        $userModel = new UserPelatihanModel();
        $pesertaPelatihanModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $notifModel = new \App\Models\Pelatihan\NotifikasiPelatihanModel();

        // Query users based on filters
        $query = $userModel->select('users_pelatihan.*, profesi_pelatihan.nama_profesi as profesi, profesi_pelatihan.kategori_target as kategori_target, profesi_pelatihan.target_jpl as target_jpl_profesi, unit_kerja_pelatihan.nama_unit as ruangan')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('users_pelatihan.role', 'peserta');
            
        if (!empty($divisi)) {
            $query->where('unit_kerja_pelatihan.nama_unit', $divisi);
        }
        
        $usersList = $query->findAll();
        
        $targetKelompok = $this->session->get('target_kelompok') ?? [
            'Named' => 20,
            'Non-Named' => 20
        ];

        $sentCount = 0;
        
        $niks = array_column($usersList, 'nik');
        if (empty($niks)) {
            return redirect()->to('/pelatihan/admin/monitoring')->with('success', 'Tidak ada peserta yang sesuai kriteria.');
        }

        // Optimize: Fetch all completed trainings for these users at once
        $allCompletedPelat = $pesertaPelatihanModel->select('peserta_pelatihan.user_id, master_pelatihan.jpl, master_pelatihan.jadwal_selesai')
            ->join('master_pelatihan', 'master_pelatihan.id = peserta_pelatihan.pelatihan_id')
            ->whereIn('peserta_pelatihan.user_id', $niks)
            ->where('peserta_pelatihan.status_peserta', 'Lulus')
            ->where('master_pelatihan.cert_published', 1)
            ->findAll();
            
        $pelatByUser = [];
        foreach ($allCompletedPelat as $cp) {
            $pelatByUser[$cp['user_id']][] = $cp;
        }

        $notifData = [];
        $currentYear = date('Y');

        foreach ($usersList as $u) {
            $myCompletedPelat = $pelatByUser[$u['nik']] ?? [];
            
            $completedJpl = 0;
            foreach ($myCompletedPelat as $cp) {
                $yearOfTraining = !empty($cp['jadwal_selesai']) ? date('Y', strtotime($cp['jadwal_selesai'])) : date('Y');
                if ($yearOfTraining == $currentYear) {
                    $completedJpl += (int)($cp['jpl'] ?? 0);
                }
            }

            $targetJPLKaryawan = $u['target_jpl_profesi'] ?? 20;
            $isMet = $completedJpl >= $targetJPLKaryawan;
            
            if ($statusJpl === 'Belum Memenuhi' && $isMet) continue;
            if ($statusJpl === 'Terpenuhi' && !$isMet) continue;

            $kurangVal = max(0, $targetJPLKaryawan - $completedJpl);
            
            $personalized = str_replace(
                ['{nama}', '{jpl}', '{target}', '{kurang}'],
                [$u['nama_lengkap'], $completedJpl, $targetJPLKaryawan, $kurangVal],
                $message
            );
            
            $notifData[] = [
                'user_id' => $u['nik'],
                'title' => 'Broadcast Capaian JPL',
                'message' => $personalized,
                'type' => 'info',
                'is_read' => 0
            ];
            
            $sentCount++;
        }
        
        if (!empty($notifData)) {
            $notifModel->insertBatch($notifData);
        }
        
        return redirect()->to('/pelatihan/admin/monitoring')->with('success', 'Broadcast berhasil dikirim ke ' . $sentCount . ' peserta.');
    }

    public function reminder(string $userId)
    {
        $notifModel = new \App\Models\Pelatihan\NotifikasiPelatihanModel();
        $message = 'Capaian JPL tahunan Anda masih di bawah target minimal. Harap segera ikuti program diklat yang tersedia.';
        $notifModel->insert([
            'user_id' => $userId,
            'title' => 'Peringatan Capaian JPL',
            'message' => $message,
            'type' => 'warning',
            'is_read' => 0
        ]);
        
        $userModel = new UserPelatihanModel();
        $user = $userModel->where('nik', $userId)->first();
        
        return redirect()->back()->with('success', 'Pengingat telah dikirim ke notifikasi peserta.');
    }

    public function akun_peserta()
    {
        $userModel = new UserPelatihanModel();
        $unitKerjaModel = new UnitKerjaPelatihanModel();
        $profesiModel = new ProfesiPelatihanModel();

        $allUsers = $userModel->getUserWithRelations();

        $named = [];
        $non_named = [];

        foreach ($allUsers as $u) {
            if (in_array($u['role'], ['admin', 'admin_pengabdian'])) {
                continue;
            }

            $mapped = [
                'id'      => $u['nik'], // Use NIK as identifier
                'nama'    => $u['nama_lengkap'],
                'email'   => $u['email'],
                'nik'     => $u['nik'],
                'wa'      => $u['no_wa'],
                'profesi' => $u['nama_profesi'] ?? '-',
                'ruangan' => $u['nama_unit'] ?? 'Umum',
                'instansi'=> $u['nama_unit'] ?? 'RSUD Kota Yogyakarta',
                'status'  => $u['status'],
                'id_unit_kerja' => $u['id_unit_kerja'],
                'id_profesi'    => $u['id_profesi'],
                'role'          => $u['role'],
                'jenis_peserta' => $u['jenis_peserta'],
                'target_jpl'    => $u['profesi_target_jpl'] ?? 20,
                'capaian_jpl'   => $u['capaian_jpl'] ?? 0
            ];

            if ($u['jenis_peserta'] === 'named') {
                $named[] = $mapped;
            } else {
                $non_named[] = $mapped;
            }
        }

        $data = [
            'title'           => 'Manajemen Akun Peserta',
            'users_named'     => $named,
            'users_non_named' => $non_named,
            'unit_kerja'      => $unitKerjaModel->findAll(),
            'profesi'         => $profesiModel->findAll()
        ];

        return view('Pelatihan/admin/manajemen_peserta/index', $data);
    }

    public function tambah_akun()
    {
        $roleInput = $this->request->getPost('role'); // admin, admin_pengabdian, named, nonnamed
        
        $rules = [
            'nik'      => 'required|numeric|exact_length[16]|is_unique[users_pelatihan.nik]',
            'nama'     => 'required|min_length[1]|max_length[150]|regex_match[/^[a-zA-Z\s.,\']+$/]',
            'email'    => 'required|valid_email|is_unique[users_pelatihan.email]',
            'wa'       => 'required|numeric|min_length[10]|max_length[15]',
        ];

        $customErrors = [
            'nik' => [
                'numeric'      => 'NIK harus berupa angka murni.',
                'exact_length' => 'NIK harus tepat 16 digit.',
                'is_unique'    => 'NIK sudah terdaftar.'
            ],
            'nama' => [
                'regex_match'  => 'Nama hanya boleh mengandung huruf, spasi, titik, koma, atau tanda kutip.'
            ],
            'email' => [
                'valid_email'  => 'Format email tidak valid.',
                'is_unique'    => 'Email sudah terdaftar.'
            ],
            'wa' => [
                'numeric'      => 'No. WhatsApp harus berupa angka murni.',
                'min_length'   => 'No. WhatsApp minimal 10 digit.',
                'max_length'   => 'No. WhatsApp maksimal 15 digit.'
            ]
        ];

        if (!$this->validate($rules, $customErrors)) {
            $errors = $this->validator->getErrors();
            $errorString = implode('<br>', $errors);
            return redirect()->back()->withInput()->with('error', $errorString);
        }

        // Map role and jenis_peserta based on select choice
        $dbRole = 'peserta';
        $jenisPeserta = 'named';

        if ($roleInput === 'admin') {
            $dbRole = 'admin';
        } elseif ($roleInput === 'admin_pengabdian') {
            $dbRole = 'admin_pengabdian';
        } elseif ($roleInput === 'nonnamed') {
            $jenisPeserta = 'non_named';
        }

        $idUnitKerja = $this->request->getPost('id_unit_kerja');
        $idProfesi = $this->request->getPost('id_profesi');

        if ($jenisPeserta === 'non_named') {
             $idUnitKerja = $this->request->getPost('id_unit_kerja');
             $idProfesi = $this->request->getPost('id_profesi');
        }

        // Generate default password
        $randomPassword = 'RSUDKotaYogyakarta2026';

        $userData = [
            'nik'           => $this->request->getPost('nik'),
            'nama_lengkap'  => $this->request->getPost('nama'),
            'email'         => $this->request->getPost('email'),
            'no_wa'         => $this->request->getPost('wa'),
            'jenis_peserta' => $jenisPeserta,
            'role'          => $dbRole,
            'id_unit_kerja' => $idUnitKerja ?: null,
            'id_profesi'    => $idProfesi ?: null,
            'password'      => password_hash($randomPassword, PASSWORD_BCRYPT),
            'status'        => 'aktif'
        ];

        $userModel = new UserPelatihanModel();
        if ($userModel->insert($userData)) {
            return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Akun berhasil ditambahkan. Password default peserta adalah RSUDKotaYogyakarta2026');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan akun. Silakan coba lagi.');
        }
    }

    public function edit_akun()
    {
        $id = $this->request->getPost('id'); // original NIK
        $newNik = $this->request->getPost('nik');
        $newEmail = $this->request->getPost('email');

        $userModel = new UserPelatihanModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'nama'     => 'required|min_length[1]|max_length[150]|regex_match[/^[a-zA-Z\s.,\']+$/]',
            'wa'       => 'required|numeric|min_length[10]|max_length[15]',
        ];

        // Add conditional rules if NIK or Email has changed
        if ($newNik !== $id) {
            $rules['nik'] = 'required|numeric|exact_length[16]|is_unique[users_pelatihan.nik]';
        } else {
            $rules['nik'] = 'required|numeric|exact_length[16]';
        }

        if ($newEmail !== $user['email']) {
            $rules['email'] = 'required|valid_email|is_unique[users_pelatihan.email]';
        } else {
            $rules['email'] = 'required|valid_email';
        }


        $customErrors = [
            'nik' => [
                'numeric'      => 'NIK harus berupa angka murni.',
                'exact_length' => 'NIK harus tepat 16 digit.',
                'is_unique'    => 'NIK sudah terdaftar.'
            ],
            'nama' => [
                'regex_match'  => 'Nama hanya boleh mengandung huruf, spasi, titik, koma, atau tanda kutip.'
            ],
            'email' => [
                'valid_email'  => 'Format email tidak valid.',
                'is_unique'    => 'Email sudah terdaftar.'
            ],
            'wa' => [
                'numeric'      => 'No. WhatsApp harus berupa angka murni.',
                'min_length'   => 'No. WhatsApp minimal 10 digit.',
                'max_length'   => 'No. WhatsApp maksimal 15 digit.'
            ]
        ];

        if (!$this->validate($rules, $customErrors)) {
            $errors = $this->validator->getErrors();
            $errorString = implode('<br>', $errors);
            return redirect()->back()->withInput()->with('error', $errorString);
        }

        $idUnitKerja = $this->request->getPost('id_unit_kerja');
        $idProfesi = $this->request->getPost('id_profesi');
        $jenisPeserta = $this->request->getPost('jenis_peserta');
        $roleInput = $this->request->getPost('role'); // admin, admin_pengabdian, peserta

        $updateData = [
            'nama_lengkap'  => $this->request->getPost('nama'),
            'email'         => $this->request->getPost('email'),
            'no_wa'         => $this->request->getPost('wa'),
            'id_unit_kerja' => $idUnitKerja ?: null,
            'id_profesi'    => $idProfesi ?: null
        ];

        if (!empty($jenisPeserta)) {
            $updateData['jenis_peserta'] = $jenisPeserta;
        }

        if (!empty($roleInput)) {
            // Normalize role value
            $validRoles = ['admin', 'admin_pengabdian', 'peserta'];
            if (in_array($roleInput, $validRoles)) {
                $updateData['role'] = $roleInput;
            }
        }


        // If NIK is updated, CodeIgniter's manual primary key update is safer to handle by transaction
        if ($newNik !== $id) {
            $updateData['nik'] = $newNik;
            $db = \Config\Database::connect();
            $db->transBegin();
            
            // Insert updated user
            $newUser = array_merge($user, $updateData);
            $userModel->insert($newUser);
            
            // Delete old user
            $userModel->delete($id);

            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Gagal memperbarui NIK.');
            } else {
                $db->transCommit();
                return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Akun berhasil diperbarui.');
            }
        }

        if ($userModel->update($id, $updateData)) {
            return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Akun berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui akun.');
        }
    }

    public function toggle_status(string $nik)
{
    $userModel = new UserPelatihanModel();
    $user = $userModel->find($nik);

    if (!$user) {
        return redirect()->back()->with('error', 'Akun tidak ditemukan.');
    }

    $newStatus = ($user['status'] === 'aktif') ? 'nonaktif' : 'aktif';

    if ($userModel->update($nik, ['status' => $newStatus])) {
        // HANYA kirim satu pesan sukses
        return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Status akun berhasil diubah.');
    } else {
        return redirect()->back()->with('error', 'Gagal mengubah status akun.');
    }
}

    public function delete_akun(string $nik)
    {
        $userModel = new UserPelatihanModel();
        if ($userModel->delete($nik)) {
            return redirect()->to('/pelatihan/admin/akun_peserta')->with('success', 'Akun berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus akun.');
        }
    }

    public function monitoring_peserta()
    {
        $masterPelatihanModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();

        $pelatihan = $masterPelatihanModel->orderBy('nama', 'ASC')->findAll();
        
        $list = [];
        foreach ($pelatihan as $p) {
            $count = $pesertaModel->where('pelatihan_id', $p['id'])->countAllResults();
            $list[] = array_merge($p, ['total_peserta' => $count]);
        }

        return view('Pelatihan/admin/monitoring/kelas', ['title' => 'Monitoring Progress Diklat', 'list' => $list]);
    }

    public function presensi(string $pelatihanId)
    {
        $masterPelatihanModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $sesiModel = new \App\Models\Pelatihan\SesiInteraktifPelatihanModel();
        $db = \Config\Database::connect();

        $pelatihan = $masterPelatihanModel->find($pelatihanId);
        if (!$pelatihan) {
            return redirect()->to('/pelatihan/admin/monitoring_peserta');
        }

        // Fetch sessions
        $sesi_list = $sesiModel->where('pelatihan_id', $pelatihanId)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->findAll();

        // Fetch participants (joined with users_pelatihan, profesi_pelatihan, unit_kerja_pelatihan)
        $peserta = $pesertaModel->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama, users_pelatihan.email, profesi_pelatihan.nama_profesi as profesi, unit_kerja_pelatihan.nama_unit as ruangan')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('peserta_pelatihan.pelatihan_id', $pelatihanId)
            ->findAll();

        // Map presence status
        foreach ($peserta as &$p) {
            $p['kehadiran'] = [];
            $p['hadir_count'] = 0;
            
            foreach ($sesi_list as $s) {
                $presensi = $db->table('peserta_presensi_pelatihan')
                    ->where('peserta_pelat_id', $p['id'])
                    ->where('sesi_id', $s['id'])
                    ->get()->getRowArray();
                
                $status = $presensi ? $presensi['status_hadir'] : 'Alfa';
                $p['kehadiran'][$s['id']] = $status;
                if ($status == 'Hadir' || $status == 'Izin') {
                    $p['hadir_count']++;
                }
            }

            $p['progress'] = $p['progress'] ?? 0;
        }

        $data = [
            'title' => 'Monitoring Presensi',
            'hide_default_breadcrumb' => true,
            'pelatihan' => $pelatihan,
            'sesi_list' => $sesi_list,
            'peserta' => $peserta
        ];

        return view('Pelatihan/admin/monitoring/presensi', $data);
    }

    public function toggle_presensi()
    {
        $userId = $this->request->getPost('user_id'); // NIK
        $pelatihanId = $this->request->getPost('pelatihan_id');
        $sesiId = $this->request->getPost('sesi'); // sesi_id
        $rawStatus = $this->request->getPost('status');
        $statusMap = ['hadir' => 'Hadir', 'izin' => 'Izin', 'alfa' => 'Alfa'];
        $status = $statusMap[$rawStatus] ?? 'Alfa';

        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $peserta = $pesertaModel->where('user_id', $userId)
            ->where('pelatihan_id', $pelatihanId)
            ->first();

        if (!$peserta) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Peserta tidak ditemukan']);
        }

        $db = \Config\Database::connect();
        $exist = $db->table('peserta_presensi_pelatihan')
            ->where('peserta_pelat_id', $peserta['id'])
            ->where('sesi_id', $sesiId)
            ->get()->getRowArray();

        if ($exist) {
            $db->table('peserta_presensi_pelatihan')
                ->where('id', $exist['id'])
                ->update(['status_hadir' => $status, 'waktu_absen' => date('Y-m-d H:i:s')]);
        } else {
            $db->table('peserta_presensi_pelatihan')->insert([
                'peserta_pelat_id' => $peserta['id'],
                'sesi_id' => $sesiId,
                'status_hadir' => $status,
                'waktu_absen' => date('Y-m-d H:i:s')
            ]);
        }

        $this->_updatePresensiProgress($db, $peserta, $pelatihanId, $sesiId, $status);

        return $this->response->setJSON(['status' => 'success']);
    }

    private function _findPresensiStepId(\CodeIgniter\Database\BaseConnection $db, string $pelatihanId, string $sesiId): ?int
    {
        $stepCounter = 1;
        $preTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $pelatihanId)->where('tipe_evaluasi', 'Pre-test')->get()->getRowArray();
        if ($preTest) $stepCounter++;

        $sesi = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $pelatihanId)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        foreach ($sesi as $s) {
            if ((int)$s['id'] === (int)$sesiId) {
                return $stepCounter;
            }
            $stepCounter++; // presensi or sesi
            $materi = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->get()->getResultArray();
            $groupedSegmen = [];
            foreach ($materi as $m) { $groupedSegmen[$m['segmen'] ?: 1][] = $m; }
            $stepCounter += count($groupedSegmen);
            $stepCounter++; // evaluasi_sesi
        }
        return null;
    }

    private function _countTotalSteps(\CodeIgniter\Database\BaseConnection $db, string $pelatihanId): int
    {
        $stepCounter = 1;
        $preTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $pelatihanId)->where('tipe_evaluasi', 'Pre-test')->get()->getRowArray();
        if ($preTest) $stepCounter++;

        $sesi = $db->table('sesi_interaktif_pelatihan')->where('pelatihan_id', $pelatihanId)->get()->getResultArray();
        foreach ($sesi as $s) {
            $stepCounter++; // presensi or sesi
            $materi = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->get()->getResultArray();
            $groupedSegmen = [];
            foreach ($materi as $m) { $groupedSegmen[$m['segmen'] ?: 1][] = $m; }
            $stepCounter += count($groupedSegmen);
            $stepCounter++; // evaluasi_sesi
        }

        $postTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $pelatihanId)->where('tipe_evaluasi', 'Post-test')->get()->getRowArray();
        if ($postTest) $stepCounter++;
        $stepCounter++; // evaluasi
        $stepCounter++; // sertifikat
        return $stepCounter - 1;
    }

    private function _updatePresensiProgress(\CodeIgniter\Database\BaseConnection $db, array $peserta, string $pelatihanId, string $sesiId, string $status)
    {
        $stepId = $this->_findPresensiStepId($db, $pelatihanId, $sesiId);
        if (!$stepId) return;

        $completedSteps = json_decode($peserta['completed_steps'] ?? '[]', true) ?? [];
        $totalSteps = $this->_countTotalSteps($db, $pelatihanId);

        if (in_array($status, ['Hadir', 'Izin'])) {
            if (!in_array((int)$stepId, $completedSteps)) {
                $completedSteps[] = (int)$stepId;
            }
        } else {
            $completedSteps = array_values(array_filter($completedSteps, fn($s) => (int)$s !== (int)$stepId));
        }

        $progressPct = $totalSteps > 0 ? (count($completedSteps) / $totalSteps) * 100 : 0;

        $db->table('peserta_pelatihan')
            ->where('id', $peserta['id'])
            ->update([
                'completed_steps' => json_encode($completedSteps),
                'progress' => $progressPct,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    } 
    public function hapus_pendaftaran(string $userId, string $pelatihanId)
    {
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $pesertaModel->where('user_id', $userId)
            ->where('pelatihan_id', $pelatihanId)
            ->delete();

        return redirect()->back()->with('success', 'Peserta berhasil dihapus dari pelatihan.');
    }

    public function add_peserta($pelatihanId = null)
    {
        $masterPelatihanModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $userPelatihanModel = new UserPelatihanModel();
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        
        $pelatihan = $masterPelatihanModel->orderBy('nama', 'ASC')->findAll();
        
        // Fetch users who are NOT admins, with their relations
        $users = $userPelatihanModel->select('users_pelatihan.*, profesi_pelatihan.nama_profesi as profesi, unit_kerja_pelatihan.nama_unit as ruangan')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('users_pelatihan.role', 'peserta')
            ->findAll();
        
        // Fetch registered NIKs in this training to disable check
        $registeredNiks = [];
        if ($pelatihanId) {
            $registered = $pesertaModel->where('pelatihan_id', $pelatihanId)->findAll();
            $registeredNiks = array_column($registered, 'user_id');
        }
        
        $data = [
            'title' => 'Tambah Peserta Ke Pelatihan',
            'pelatihan' => $pelatihan,
            'users' => $users,
            'selectedId' => $pelatihanId,
            'registeredNiks' => $registeredNiks
        ];
        return view('Pelatihan/admin/monitoring/add_peserta', $data);
    }

    public function save_peserta()
    {
        $pelatihanId = $this->request->getPost('pelatihan_id');
        $userIds = $this->request->getPost('user_ids') ?? [];
        
        if (empty($pelatihanId) || empty($userIds)) {
            return redirect()->back()->with('error', 'Silakan pilih pelatihan dan peserta.');
        }
 
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        
        foreach ($userIds as $nik) {
            $exists = $pesertaModel->where('user_id', $nik)
                ->where('pelatihan_id', $pelatihanId)
                ->first();
            
            if (!$exists) {
                $pesertaModel->insert([
                    'user_id' => $nik,
                    'pelatihan_id' => $pelatihanId,
                    'status_peserta' => 'Daftar',
                    'waktu_daftar' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
        
        return redirect()->to('/pelatihan/admin/presensi/'.$pelatihanId)->with('success', count($userIds) . ' Peserta berhasil ditambahkan.');
    }

    public function export_excel(string $pelatihanId)
    {
        $masterPelatihanModel = new \App\Models\Pelatihan\MasterPelatihanModel();
        $pesertaModel = new \App\Models\Pelatihan\PesertaPelatihanModel();
        $db = \Config\Database::connect();

        $pelatihan = $masterPelatihanModel->find($pelatihanId);
        if (!$pelatihan) {
            return redirect()->to('/pelatihan/admin/monitoring_peserta');
        }

        $countPeserta = $pesertaModel->where('pelatihan_id', $pelatihanId)->countAllResults();

        // Spreadsheet setup
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // === SHEET 1: INFO PELATIHAN ===
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Info Pelatihan');
        
        $sheet1->setCellValue('A1', 'INFORMASI PELATIHAN: ' . $pelatihan['nama']);
        $sheet1->mergeCells('A1:B1');
        $sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $sheet1->setCellValue('A3', 'Nama Pelatihan');
        $sheet1->setCellValue('B3', $pelatihan['nama']);
        
        $sheet1->setCellValue('A4', 'Metode & Lokasi');
        $sheet1->setCellValue('B4', ($pelatihan['metode'] ?? '-') . ' - ' . ($pelatihan['lokasi'] ?? '-'));
        
        $sheet1->setCellValue('A5', 'Kuota');
        $sheet1->setCellValue('B5', $pelatihan['kuota']);
        
        $sheet1->setCellValue('A6', 'Jumlah Peserta');
        $sheet1->setCellValue('B6', $countPeserta);
        
        $sheet1->setCellValue('A7', 'Tema / Program');
        $sheet1->setCellValue('B7', $pelatihan['program'] ?? '-');
        
        $sheet1->setCellValue('A8', 'Level & Cakupan');
        $sheet1->setCellValue('B8', ($pelatihan['level_pelatihan'] ?? '-') . ' - ' . ($pelatihan['cakupan'] ?? '-'));
        
        $sheet1->setCellValue('A9', 'Nilai SKP');
        $sheet1->setCellValue('B9', $pelatihan['skp'] ?? '-');
        
        $sheet1->setCellValue('A10', 'Total JPL');
        $sheet1->setCellValue('B10', $pelatihan['jpl'] ?? '-');
        
        $sheet1->setCellValue('A11', 'Jadwal Pelaksanaan');
        $jadwal = ($pelatihan['jadwal_mulai'] ?? '') . ' s.d ' . ($pelatihan['jadwal_selesai'] ?? '');
        $sheet1->setCellValue('B11', $jadwal);
        
        $sheet1->setCellValue('A12', 'Tujuan');
        $sheet1->setCellValue('B12', strip_tags($pelatihan['tujuan'] ?? '-'));
        
        $sheet1->setCellValue('A13', 'Kompetensi');
        $sheet1->setCellValue('B13', strip_tags($pelatihan['kompetensi'] ?? '-'));

        $narasumberList = $db->table('narasumber_pelatihan')
            ->join('pejabat_ttd_pelatihan', 'pejabat_ttd_pelatihan.id = narasumber_pelatihan.pejabat_ttd_id')
            ->where('narasumber_pelatihan.pelatihan_id', $pelatihanId)
            ->get()->getResultArray();
        $narasumberText = [];
        foreach($narasumberList as $n) { $narasumberText[] = $n['nama_pejabat']; }
        $sheet1->setCellValue('A14', 'Narasumber');
        $sheet1->setCellValue('B14', implode(', ', $narasumberText) ?: '-');

        // Kurikulum Materi
        $sheet1->setCellValue('A16', 'Kurikulum Materi');
        $sheet1->getStyle('A16')->getFont()->setBold(true);
        $rowK = 17;
        $noK = 1;

        // Pre Test
        $sheet1->setCellValue('A'.$rowK, $noK++);
        $sheet1->setCellValue('B'.$rowK, "Pre Test");
        $rowK++;

        // Sesi
        $sesi = $db->table('sesi_interaktif_pelatihan')->where('pelatihan_id', $pelatihanId)->orderBy('tanggal', 'ASC')->get()->getResultArray();
        foreach ($sesi as $s) {
            $sheet1->setCellValue('A'.$rowK, $noK++);
            $sheet1->setCellValue('B'.$rowK, "Sesi " . ucfirst($s['tipe_sesi']) . ": " . $s['nama_sesi'] . " (" . $s['tanggal'] . ")");
            $rowK++;
        }

        // Materi
        $materi = $db->table('materi_pelatihan')->where('pelatihan_id', $pelatihanId)->orderBy('urutan', 'ASC')->get()->getResultArray();
        foreach ($materi as $m) {
            $sheet1->setCellValue('A'.$rowK, $noK++);
            $sheet1->setCellValue('B'.$rowK, "Materi: " . $m['judul']);
            $rowK++;
        }

        // Post Test
        $sheet1->setCellValue('A'.$rowK, $noK++);
        $sheet1->setCellValue('B'.$rowK, "Post Test");
        $rowK++;

        // Kuesioner
        $kuesionerCount = $db->table('kuesioner_master_pelatihan')->where('pelatihan_id', $pelatihanId)->countAllResults();
        if ($kuesionerCount > 0) {
            $sheet1->setCellValue('A'.$rowK, $noK++);
            $sheet1->setCellValue('B'.$rowK, "Evaluasi Pelatihan (Kuesioner)");
            $rowK++;
        }

        $sheet1->setCellValue('A'.$rowK, $noK++);
        $sheet1->setCellValue('B'.$rowK, "Sertifikat Kelulusan");

        $sheet1->getColumnDimension('A')->setWidth(25);
        $sheet1->getColumnDimension('B')->setWidth(80);
        $sheet1->getStyle('A3:A14')->getFont()->setBold(true);

        // === SHEET 2: PESERTA ===
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Data Peserta');

        $headers = [
            'No', 'Role Sistem', 'Status Aktivasi', 'Nama Lengkap', 'NIK', 'Email', 
            'No WhatsApp', 'Unit Kerja', 'Profesi', 'Target JPL', 'Capaian JPL', 
            'Nilai Pre Test', 'Nilai Post Test', 'Status Kelulusan'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet2->setCellValue($col . '1', $header);
            $sheet2->getStyle($col . '1')->getFont()->setBold(true);
            $sheet2->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        $peserta = $pesertaModel->select('peserta_pelatihan.*, users_pelatihan.nama_lengkap as nama, users_pelatihan.email, users_pelatihan.no_wa, users_pelatihan.role, users_pelatihan.status, users_pelatihan.capaian_jpl, profesi_pelatihan.nama_profesi as profesi, profesi_pelatihan.target_jpl, unit_kerja_pelatihan.nama_unit as ruangan')
            ->join('users_pelatihan', 'users_pelatihan.nik = peserta_pelatihan.user_id')
            ->join('profesi_pelatihan', 'profesi_pelatihan.id_profesi = users_pelatihan.id_profesi', 'left')
            ->join('unit_kerja_pelatihan', 'unit_kerja_pelatihan.id_unit_kerja = users_pelatihan.id_unit_kerja', 'left')
            ->where('peserta_pelatihan.pelatihan_id', $pelatihanId)
            ->findAll();

        $row = 2;
        $no = 1;
        foreach ($peserta as $p) {
            // Pre-test
            $pre = $db->table('peserta_ujian_pelatihan')
                      ->where('peserta_pelat_id', $p['id'])
                      ->where('tipe_ujian', 'pre_test')
                      ->get()->getRowArray();
            $nilaiPre = $pre ? $pre['score'] : '-';

            // Post-test
            $post = $db->table('peserta_ujian_pelatihan')
                      ->where('peserta_pelat_id', $p['id'])
                      ->where('tipe_ujian', 'post_test')
                      ->get()->getRowArray();
            $nilaiPost = $post ? $post['score'] : '-';

            $kkm = $pelatihan['kkm'] ?? 70;
            $statusKelulusan = ($nilaiPost !== '-' && $nilaiPost >= $kkm) ? 'LULUS' : 'TIDAK LULUS';
            if ($nilaiPost === '-') $statusKelulusan = 'BELUM UJIAN';

            $sheet2->setCellValue('A'.$row, $no++);
            $sheet2->setCellValue('B'.$row, $p['role'] ?? 'Peserta');
            $sheet2->setCellValue('C'.$row, $p['status'] ?? 'Aktif');
            $sheet2->setCellValue('D'.$row, $p['nama']);
            $sheet2->setCellValueExplicit('E'.$row, $p['user_id'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet2->setCellValue('F'.$row, $p['email']);
            $sheet2->setCellValueExplicit('G'.$row, $p['no_wa'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet2->setCellValue('H'.$row, $p['ruangan'] ?? '-');
            $sheet2->setCellValue('I'.$row, $p['profesi'] ?? '-');
            $sheet2->setCellValue('J'.$row, $p['target_jpl'] ?? 0);
            $sheet2->setCellValue('K'.$row, $p['capaian_jpl'] ?? 0);
            $sheet2->setCellValue('L'.$row, $nilaiPre);
            $sheet2->setCellValue('M'.$row, $nilaiPost);
            $sheet2->setCellValue('N'.$row, $statusKelulusan);
            
            $row++;
        }

        // === SHEET 3: DATA FEEDBACK ===
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Data Feedback');

        // Fetch all distinct columns first
        $distinctCols = $db->table('peserta_kuesioner_rating_pelatihan pkr')
            ->select('pkr.kuesioner_id, pkr.sesi_id, k.pertanyaan, s.nama_sesi, cat.nama_kategori as kategori, AVG(pkr.nilai_rating) as avg_rating')
            ->join('kuesioner_master_pelatihan k', 'k.id = pkr.kuesioner_id', 'left')
            ->join('kategori_evaluasi_pelatihan cat', 'cat.id = k.kategori_id', 'left')
            ->join('sesi_interaktif_pelatihan s', 's.id = pkr.sesi_id', 'left')
            ->join('peserta_pelatihan pp', 'pp.id = pkr.peserta_pelat_id')
            ->where('pp.pelatihan_id', $pelatihanId)
            ->groupBy('pkr.kuesioner_id, pkr.sesi_id')
            ->get()->getResultArray();

        // Fetch data
        $totalRating = 0;
        $countFb = 0;
        $feedbacks = [];
        foreach ($peserta as $pl) {
            $saran = $db->table('peserta_kuesioner_saran_pelatihan')
                        ->where('peserta_pelat_id', $pl['id'])
                        ->get()->getRowArray();
            if ($saran) {
                $rating = $saran['rating_umum'];
                $komentar = $saran['saran_masukan'];
                
                $jawaban = $db->table('peserta_kuesioner_rating_pelatihan')
                              ->select('kuesioner_id, sesi_id, nilai_rating')
                              ->where('peserta_pelat_id', $pl['id'])
                              ->get()->getResultArray();
                $jawabanMap = [];
                foreach ($jawaban as $j) {
                    $key = $j['kuesioner_id'] . '_' . $j['sesi_id'];
                    $jawabanMap[$key] = $j['nilai_rating'];
                }

                $feedbacks[] = [
                    'nama' => $pl['nama'],
                    'rating' => $rating,
                    'komentar' => $komentar,
                    'jawaban_map' => $jawabanMap
                ];
                $totalRating += $rating;
                $countFb++;
            }
        }
        $avgFb = $countFb > 0 ? round($totalRating / $countFb, 1) : 0;

        // 1. Overall Summary
        $sheet3->setCellValue('A1', 'SUMMARY FEEDBACK PELATIHAN');
        $sheet3->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        $sheet3->setCellValue('A3', 'Total Responden');
        $sheet3->setCellValue('B3', $countFb . ' Orang');
        
        $sheet3->setCellValue('A4', 'Rata-rata Rating Keseluruhan');
        $sheet3->setCellValue('B4', $avgFb . ' / 5.0');
        $sheet3->getStyle('A3:A4')->getFont()->setBold(true);

        // 2. Average per Category / Question
        $rowF = 6;
        $sheet3->setCellValue('A'.$rowF, 'Rata-rata per Pertanyaan');
        $sheet3->getStyle('A'.$rowF)->getFont()->setBold(true);
        $rowF++;
        
        $sheet3->setCellValue('A'.$rowF, 'Kategori');
        $sheet3->setCellValue('B'.$rowF, 'Sesi');
        $sheet3->setCellValue('C'.$rowF, 'Pertanyaan');
        $sheet3->setCellValue('D'.$rowF, 'Rata-rata Rating');
        $sheet3->getStyle('A'.$rowF.':D'.$rowF)->getFont()->setBold(true);
        $rowF++;

        $qOrder = []; // Store column keys to map detail columns later
        foreach ($distinctCols as $c) {
            $key = $c['kuesioner_id'] . '_' . $c['sesi_id'];
            $qOrder[] = [
                'key' => $key,
                'header' => ($c['nama_sesi'] ? '['.$c['nama_sesi'].'] ' : '') . $c['pertanyaan']
            ];
            
            $qAvg = $c['avg_rating'] ? round($c['avg_rating'], 1) : 0;

            $sheet3->setCellValue('A'.$rowF, $c['kategori'] ?? 'Lainnya');
            $sheet3->setCellValue('B'.$rowF, $c['nama_sesi'] ?: '-');
            $sheet3->setCellValue('C'.$rowF, $c['pertanyaan']);
            $sheet3->setCellValue('D'.$rowF, $qAvg);
            $rowF++;
        }
        
        $sheet3->getColumnDimension('C')->setWidth(50);
        $sheet3->getColumnDimension('D')->setWidth(20);

        // 3. Detail Per Peserta
        $rowF += 2;
        $sheet3->setCellValue('A'.$rowF, 'DETAIL FEEDBACK PER PESERTA');
        $sheet3->getStyle('A'.$rowF)->getFont()->setBold(true)->setSize(12);
        $rowF++;

        // Headers for detail table
        $sheet3->setCellValue('A'.$rowF, 'No');
        $sheet3->setCellValue('B'.$rowF, 'Nama Peserta');
        $sheet3->setCellValue('C'.$rowF, 'Rating Umum');
        $sheet3->setCellValue('D'.$rowF, 'Saran & Masukan');
        
        $col = 'E';
        foreach ($qOrder as $q) {
            $sheet3->setCellValue($col.$rowF, $q['header']);
            $sheet3->getColumnDimension($col)->setWidth(30);
            $col++;
        }
        
        $sheet3->getStyle('A'.$rowF.':'.$col.$rowF)->getFont()->setBold(true);
        
        $rowF++;
        $noF = 1;
        foreach ($feedbacks as $fb) {
            $sheet3->setCellValue('A'.$rowF, $noF++);
            $sheet3->setCellValue('B'.$rowF, $fb['nama']);
            $sheet3->setCellValue('C'.$rowF, $fb['rating']);
            $sheet3->setCellValue('D'.$rowF, $fb['komentar']);
            
            $cCol = 'E';
            foreach ($qOrder as $q) {
                $val = $fb['jawaban_map'][$q['key']] ?? '-';
                $sheet3->setCellValue($cCol.$rowF, $val);
                $cCol++;
            }
            $rowF++;
        }
        
        $sheet3->getColumnDimension('A')->setAutoSize(true);
        $sheet3->getColumnDimension('B')->setAutoSize(true);
        $sheet3->getColumnDimension('D')->setWidth(40);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Data_Monitoring_Pelatihan_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }
}
