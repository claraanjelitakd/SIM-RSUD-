<?php
namespace App\Controllers\Pelatihan\Peserta;
use App\Controllers\BaseController;

class Training extends BaseController
{
    public function daftar($id)
    {
        $userId = $this->session->get('user_id'); // NIK
        if (!$userId) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        
        $item = $db->table('master_pelatihan')->where('id', $id)->get()->getRowArray();
        if (!$item) return redirect()->to('/pelatihan/peserta/pembelajaran');

        $now = date('Y-m-d H:i:s');
        $regBuka = $item['reg_buka_tgl'] . ' ' . ($item['reg_buka_jam'] ?: '00:00:00');
        $regTutup = $item['reg_tutup_tgl'] . ' ' . ($item['reg_tutup_jam'] ?: '23:59:59');
        if ($now < $regBuka || $now > $regTutup) {
            return redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('error', 'Pendaftaran sedang ditutup.');
        }

        $statusPeserta = 'Daftar';
        $statusPembayaran = 'Gratis';
        $statusAkses = 'Terbuka';

        if ($item['biaya_nominal'] > 0) {
            $statusPembayaran = 'Pending';
        }

        if ($item['mekanisme'] == 'Tertutup') {
            $statusAkses = 'Pending';
        }

        // Check if already registered
        $exists = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();

        if ($exists) {
            $db->table('peserta_pelatihan')->where('id', $exists['id'])->update([
                'status_peserta' => $statusPeserta,
                'status_pembayaran' => $statusPembayaran,
                'status_akses' => $statusAkses,
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => $statusPeserta,
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'status_pembayaran' => $statusPembayaran,
                'status_akses' => $statusAkses,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($statusPembayaran == 'Gratis' && $statusAkses == 'Terbuka') {
                $now = date('Y-m-d H:i:s');
                $jadwalMulai = $item['jadwal_mulai'] . ' ' . ($item['jam_mulai'] ?: '00:00:00');
                if ($now >= $jadwalMulai) {
                    $msg = 'Pendaftaran pelatihan telah berhasil. Pelatihan langsung bisa diakses, silakan menuju menu Diklat Saya dan klik Mulai Belajar.';
                } else {
                    $tglMulai = date('d M Y', strtotime($item['jadwal_mulai']));
                    $msg = 'Pendaftaran pelatihan telah berhasil. Pelatihan akan dapat diakses mulai tanggal ' . $tglMulai . '.';
                }

                $db->table('notifikasi_pelatihan')->insert([
                    'user_id' => $userId,
                    'title' => 'Pendaftaran Berhasil: ' . $item['nama'],
                    'message' => $msg,
                    'type' => 'success',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        if ($statusPembayaran == 'Pending' || $statusAkses == 'Pending') {
            $msg = 'Pendaftaran dikirim. Mohon menunggu verifikasi admin untuk pembayaran/akses.';
        } else {
            $msg = 'Pendaftaran Berhasil! Anda sudah bisa mengakses ruang belajar.';
        }

        $redirect = redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('success', $msg);

        if ($statusPembayaran == 'Pending' && empty($exists['bukti_bayar'])) {
            $redirect = $redirect->with('show_upload_popup', true);
        }

        return $redirect;
    }

    public function upload_bukti_bayar($id)
    {
        helper('upload_security');
        if (!is_safe_upload($this->request->getFile('bukti_bayar'))) {
            return redirect()->back()->with('error', 'Keamanan: File Bukti Pembayaran tidak valid atau mengandung ekstensi berbahaya.');
        }
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $db = \Config\Database::connect();
        
        $file = $this->request->getFile('bukti_bayar');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if (!is_dir(ROOTPATH . 'public/uploads/pelatihan/bukti_bayar')) {
                mkdir(ROOTPATH . 'public/uploads/pelatihan/bukti_bayar', 0777, true);
            }
            
            $user = $db->table('users_pelatihan')->where('nik', $userId)->get()->getRowArray();
            $pelatihan = $db->table('master_pelatihan')->where('id', $id)->get()->getRowArray();
            $namaPelatihan = preg_replace('/[^A-Za-z0-9]/', '_', $pelatihan['nama'] ?? 'Pelatihan');
            $namaUser = preg_replace('/[^A-Za-z0-9]/', '_', $user['nama_lengkap'] ?? 'User');
            
            $newName = "BuktiBayar_{$namaPelatihan}_{$namaUser}_" . date('Ymd_His') . "." . $file->getExtension();
            $file->move(ROOTPATH . 'public/uploads/pelatihan/bukti_bayar', $newName);

            $db->table('peserta_pelatihan')
                ->where('user_id', $userId)
                ->where('pelatihan_id', $id)
                ->update([
                    'bukti_bayar' => 'bukti_bayar/' . $newName,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            return redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('success', 'Bukti pembayaran berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }

    public function belajar($id)
    {
        helper('pelatihan');
        $userId = $this->session->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $item = $db->table('master_pelatihan')->where('id', $id)->get()->getRowArray();
        if (!$item) return redirect()->to('/pelatihan/peserta/pembelajaran');

        $now = date('Y-m-d H:i:s');
        $nowTs = strtotime($now);
        $jadwalMulai = $item['jadwal_mulai'] . ' ' . ($item['jam_mulai'] ?: '00:00:00');
        $jadwalSelesai = $item['jadwal_selesai'] . ' ' . ($item['jam_selesai'] ?: '23:59:59');
        if ($now < $jadwalMulai || $now > $jadwalSelesai) {
            return redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('error', 'Masa pelatihan belum dimulai atau sudah berakhir.');
        }

        $reg = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();

        $isPayApproved = $reg && in_array($reg['status_pembayaran'], ['Verified', 'Gratis']);
        $isAccessApproved = $reg && in_array($reg['status_akses'], ['Approved', 'Terbuka']);

        if (!$reg || !$isPayApproved || !$isAccessApproved) {
            return redirect()->to('/pelatihan/peserta/detail_pelatihan/'.$id)->with('error', 'Akses ditolak. Mohon tunggu verifikasi admin.');
        }

        $pesertaRecord = $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->get()->getRowArray();
        // Auto-create peserta_pelatihan if missing
        if (!$pesertaRecord) {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => 'Aktif',
                'status_pembayaran' => 'Gratis',
                'status_akses' => 'Approved',
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pesertaRecord = $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->get()->getRowArray();
        }
        $completed_steps = $pesertaRecord ? (json_decode($pesertaRecord['completed_steps'] ?? '[]', true) ?? []) : [];
        $pg = $pesertaRecord ? ['progress' => $pesertaRecord['progress'] ?? 0, 'completed_steps' => $completed_steps] : null;
        
        $preTestAttempted = false;
        $postTestAttempts = 0;
        $preTestScore = 0;
        $preTestBenar = 0;
        $preTestSalah = 0;
        $preTestTotal = 0;
        $postTestScore = 0;
        $postTestStatus = 'Tidak Lulus';
        $postTestBenar = 0;
        $postTestSalah = 0;
        $postTestTotal = 0;

        if ($pesertaRecord) {
            $ptAttempt = $db->table('peserta_ujian_pelatihan')->where('peserta_pelat_id', $pesertaRecord['id'])->where('tipe_ujian', 'pre_test')->get()->getRowArray();
            if ($ptAttempt) {
                $preTestAttempted = true;
                $preTestScore = $ptAttempt['score'];
                
                $ptAnswers = $db->table('peserta_jawaban_ujian_pelatihan')->where('peserta_ujian_id', $ptAttempt['id'])->get()->getResultArray();
                $preTestTotal = count($ptAnswers);
                foreach ($ptAnswers as $ans) {
                    if ($ans['is_correct'] == 1) $preTestBenar++;
                }
                $preTestSalah = $preTestTotal - $preTestBenar;
            }
            $postTestAttempts = $db->table('peserta_ujian_pelatihan')->where('peserta_pelat_id', $pesertaRecord['id'])->where('tipe_ujian', 'post_test')->countAllResults();
            
            $ptLastAttempt = $db->table('peserta_ujian_pelatihan')
                ->where('peserta_pelat_id', $pesertaRecord['id'])
                ->where('tipe_ujian', 'post_test')
                ->orderBy('created_at', 'DESC')
                ->get()->getRowArray();
            if ($ptLastAttempt) {
                $postTestScore = $ptLastAttempt['score'];
                $postTestStatus = $ptLastAttempt['status_lulus'];
                
                $ptPostAnswers = $db->table('peserta_jawaban_ujian_pelatihan')->where('peserta_ujian_id', $ptLastAttempt['id'])->get()->getResultArray();
                $postTestTotal = count($ptPostAnswers);
                foreach ($ptPostAnswers as $ans) {
                    if ($ans['is_correct'] == 1) $postTestBenar++;
                }
                $postTestSalah = $postTestTotal - $postTestBenar;
            }
        }

        $konten = [];
        $stepCounter = 1;
        
        $preTestQuestions = [];
        $preTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $id)->where('tipe_evaluasi', 'Pre-test')->get()->getRowArray();
        if ($preTest) {
            $preTestQuestions = $db->table('ujian_soal_pelatihan')->where('ujian_id', $preTest['id'])->get()->getResultArray();
            $konten[] = ['id' => $stepCounter++, 'tipe' => 'pre_test', 'judul' => 'Pre-Test', 'soal' => count($preTestQuestions), 'ujian_id' => $preTest['id']];
        }

        $sesi = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $id)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
            
        $presensiList = [];
        $presensiStatusList = [];
        if ($pesertaRecord) {
            $pData = $db->table('peserta_presensi_pelatihan')->where('peserta_pelat_id', $pesertaRecord['id'])->get()->getResultArray();
            foreach($pData as $pd) {
                $presensiList[$pd['sesi_id']] = $pd['waktu_absen'];
                $presensiStatusList[$pd['sesi_id']] = $pd['status_hadir'];
            }
        }
        
        $statsSesiTotal = count($sesi);
        $statsSesiHadir = 0;
        $materiAlfa = [];
        foreach ($sesi as $s) {
            $status = $presensiStatusList[$s['id']] ?? null;
            if ($status === 'Hadir') {
                $statsSesiHadir++;
            } elseif ($status === 'Alfa') {
                $mats = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->get()->getResultArray();
                foreach ($mats as $m) {
                    $materiAlfa[] = $m['judul'] . ' (Sesi: ' . $s['nama_sesi'] . ')';
                }
            }
        }

        foreach ($sesi as $s) {
            $sessionOpenAt = !empty($s['tanggal']) && !empty($s['waktu']) ? strtotime($s['tanggal'] . ' ' . $s['waktu']) : null;
            $sessionCloseAt = !empty($s['tanggal']) && !empty($s['jam_tutup']) ? strtotime($s['tanggal'] . ' ' . $s['jam_tutup']) : (!empty($s['tanggal']) ? strtotime($s['tanggal'] . ' 23:59:59') : $sessionOpenAt);
            $sessionAvailable = $sessionOpenAt === null || ($nowTs >= $sessionOpenAt && ($sessionCloseAt === null || $nowTs <= $sessionCloseAt));

            $tipeSesiLabel = ucfirst(strtolower($s['tipe_sesi'] ?? 'online'));
            if (strtolower($s['tipe_sesi'] ?? '') == 'offline') {
                $konten[] = [
                    'id' => $stepCounter++,
                    'tipe' => 'presensi',
                    'judul' => 'Sesi ' . $tipeSesiLabel . ': ' . $s['nama_sesi'],
                    'sesi_id' => $s['id'],
                    'waktu' => $s['waktu'] ?? '',
                    'jam_tutup' => $s['jam_tutup'] ?? '',
                    'tanggal' => $s['tanggal'] ?? '',
                    'tempat' => $s['tempat'] ?? '',
                    'alamat' => $s['alamat'] ?? '',
                    'lokasi_ruang' => $s['lokasi_ruang'] ?? '',
                    'maps_url' => $s['maps_url'] ?? '',
                    'available' => $sessionAvailable,
                    'open_at' => $sessionOpenAt ? date('Y-m-d H:i:s', $sessionOpenAt) : null,
                    'close_at' => $sessionCloseAt ? date('Y-m-d H:i:s', $sessionCloseAt) : null,
                    'is_attended' => isset($presensiList[$s['id']]),
                    'attended_at' => isset($presensiList[$s['id']]) ? $presensiList[$s['id']] : null,
                    'status_hadir' => $presensiStatusList[$s['id']] ?? null,
                    'tipe_sesi' => $s['tipe_sesi'] ?? '',
                    'meeting_link' => $s['meeting_link'] ?? '',
                    'meeting_pass' => $s['meeting_pass'] ?? ''
                ];
            } else {
                $konten[] = [
                    'id' => $stepCounter++,
                    'tipe' => 'sesi',
                    'judul' => 'Sesi ' . $tipeSesiLabel . ': ' . $s['nama_sesi'],
                    'sesi_id' => $s['id'],
                    'meeting_link' => $s['meeting_link'] ?? '',
                    'meeting_pass' => $s['meeting_pass'] ?? '',
                    'waktu' => $s['waktu'] ?? '',
                    'jam_tutup' => $s['jam_tutup'] ?? '',
                    'tanggal' => $s['tanggal'] ?? '',
                    'available' => $sessionAvailable,
                    'open_at' => $sessionOpenAt ? date('Y-m-d H:i:s', $sessionOpenAt) : null,
                    'close_at' => $sessionCloseAt ? date('Y-m-d H:i:s', $sessionCloseAt) : null,
                    'tipe_sesi' => $s['tipe_sesi'] ?? '',
                    'is_attended' => isset($presensiList[$s['id']]),
                    'attended_at' => isset($presensiList[$s['id']]) ? $presensiList[$s['id']] : null,
                    'status_hadir' => $presensiStatusList[$s['id']] ?? null
                ];
            }
            
            $sessionOpenAt = !empty($s['tanggal']) && !empty($s['waktu']) ? strtotime($s['tanggal'] . ' ' . $s['waktu']) : null;
            $sessionCloseAt = !empty($s['tanggal']) && !empty($s['jam_tutup']) ? strtotime($s['tanggal'] . ' ' . $s['jam_tutup']) : $sessionOpenAt;
            $sessionAvailable = $sessionOpenAt === null || ($nowTs >= $sessionOpenAt && ($sessionCloseAt === null || $nowTs <= $sessionCloseAt));

            $materi = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->orderBy('segmen', 'ASC')->orderBy('urutan', 'ASC')->get()->getResultArray();
            $groupedMateri = [];
            foreach ($materi as $m) {
                $seg = $m['segmen'] ?: 1;
                $groupedMateri[$seg][] = $m;
            }
            
            // Materi & evaluasi sesi tetap bisa diakses jika Hadir atau Izin, meski sesi sudah tutup
            $sesiPresensiStatus = $presensiStatusList[$s['id']] ?? null;
            $materiAccessible = $sessionAvailable || in_array($sesiPresensiStatus, ['Hadir', 'Izin']);

            foreach ($groupedMateri as $seg => $materiList) {
                $konten[] = [
                    'id' => $stepCounter++, 
                    'tipe' => 'materi_segmen', 
                    'judul' => 'Materi Sesi ' . $s['nama_sesi'] . ' (Segmen ' . $seg . ')',
                    'sesi_id' => $s['id'],
                    'segmen' => $seg,
                    'materi_list' => $materiList,
                    'available' => $materiAccessible,
                    'open_at' => $sessionOpenAt ? date('Y-m-d H:i:s', $sessionOpenAt) : null,
                    'close_at' => $sessionCloseAt ? date('Y-m-d H:i:s', $sessionCloseAt) : null,
                    'tipe_sesi' => $s['tipe_sesi'] ?? ''
                ];
            }

            $konten[] = [
                'id'      => $stepCounter++,
                'tipe'    => 'evaluasi_sesi',
                'judul'   => 'Evaluasi Sesi: ' . $s['nama_sesi'],
                'sesi_id' => $s['id'],
            ];
        }
        $postTestQuestions = [];
        $postTestStepId = null;
        $postTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $id)->where('tipe_evaluasi', 'Post-test')->get()->getRowArray();
        if ($postTest) {
            $allPostSoal = $db->table('ujian_soal_pelatihan')
                ->select('ujian_soal_pelatihan.*, materi_pelatihan.sesi_id as soal_sesi_id')
                ->join('materi_pelatihan', 'materi_pelatihan.id = ujian_soal_pelatihan.materi_id', 'left')
                ->where('ujian_soal_pelatihan.ujian_id', $postTest['id'])
                ->get()->getResultArray();
            // Tampilkan soal jika: materi_id null, atau sesi materi bukan Alfa
            foreach ($allPostSoal as $soal) {
                $soalSesiId = $soal['soal_sesi_id'] ?? null;
                if ($soalSesiId === null) {
                    // Soal tidak terkait materi → selalu tampil
                    $postTestQuestions[] = $soal;
                } else {
                    $soalSesiStatus = $presensiStatusList[$soalSesiId] ?? null;
                    if ($soalSesiStatus !== 'Alfa') {
                        // Hadir, Izin, atau belum presensi → tampil
                        $postTestQuestions[] = $soal;
                    }
                    // Alfa → skip soal ini
                }
            }
            $postTestStepId = $stepCounter;
            $konten[] = ['id' => $stepCounter++, 'tipe' => 'post_test', 'judul' => 'Post-Test', 'soal' => count($postTestQuestions), 'ujian_id' => $postTest['id']];
        }
        
        $evalIndex = $stepCounter++;
        $certIndex = $stepCounter++;
        $konten[] = ['id' => $evalIndex, 'tipe' => 'evaluasi', 'judul' => 'Evaluasi Pelatihan'];
        $konten[] = ['id' => $certIndex, 'tipe' => 'sertifikat', 'judul' => 'Sertifikat Kelulusan'];

        $this->session->set('total_steps_'.$id, $stepCounter - 1);
        $this->session->set('eval_step_'.$id, $evalIndex);
        $this->session->set('cert_step_'.$id, $certIndex);

        $completed_steps = $pg ? ($pg['completed_steps'] ?? []) : [];
        
        if ($preTestAttempted && $pesertaRecord) {
            $preTestStepId = null;
            foreach ($konten as $k) {
                if ($k['tipe'] == 'pre_test') { $preTestStepId = $k['id']; break; }
            }
            if ($preTestStepId && !in_array((int)$preTestStepId, $completed_steps)) {
                $completed_steps[] = (int)$preTestStepId;
                $progressPct = (count($completed_steps) / ($stepCounter - 1)) * 100;
                $db->table('peserta_pelatihan')->where('id', $pesertaRecord['id'])->update([
                    'completed_steps' => json_encode($completed_steps),
                    'progress' => $progressPct
                ]);
            }
        }
        $active_step_id = $this->request->getGet('step') ?? 1;
        
        $filtered_active = array_filter($konten, fn($k) => $k['id'] == $active_step_id);
        
        // Check for closed sessions without presensi & insert Alfa first so status is up-to-date
        if (!empty($filtered_active)) {
            $activeK = reset($filtered_active);
            if (in_array($activeK['tipe'], ['sesi', 'presensi']) && isset($activeK['sesi_id'])) {
                $sesiIdCheck  = $activeK['sesi_id'];
                $statusCheck  = $presensiStatusList[$sesiIdCheck] ?? null;
                $sesiFiltered = array_filter($sesi, fn($s) => $s['id'] == $sesiIdCheck);
                $sesiRowCheck = reset($sesiFiltered);
                $sesiCloseTs  = ($sesiRowCheck && !empty($sesiRowCheck['tanggal']) && !empty($sesiRowCheck['jam_tutup']))
                    ? strtotime($sesiRowCheck['tanggal'] . ' ' . $sesiRowCheck['jam_tutup'])
                    : (($sesiRowCheck && !empty($sesiRowCheck['tanggal'])) ? strtotime($sesiRowCheck['tanggal'] . ' 23:59:59') : null);
                if ($sesiCloseTs !== null && $nowTs > $sesiCloseTs && $statusCheck === null && $pesertaRecord) {
                    $db->table('peserta_presensi_pelatihan')->insert([
                        'peserta_pelat_id' => $pesertaRecord['id'],
                        'sesi_id'          => $sesiIdCheck,
                        'status_hadir'     => 'Alfa',
                        'waktu_absen'      => date('Y-m-d H:i:s'),
                    ]);
                    $presensiStatusList[$sesiIdCheck] = 'Alfa';
                }
            }

            // Auto-skip: jika step aktif adalah materi_segmen atau evaluasi_sesi
            // dari sesi yang Alfa atau sudah tutup tanpa presensi → redirect ke step setelah sesi itu
            $skipTypes = ['materi_segmen', 'evaluasi_sesi'];
            if (in_array($activeK['tipe'], $skipTypes) && isset($activeK['sesi_id'])) {
                $sesiIdCheck = $activeK['sesi_id'];
                $statusCheck = $presensiStatusList[$sesiIdCheck] ?? null;
                // Cek apakah sesi sudah tutup
                $sesiRowCheck = array_filter($sesi, fn($s) => $s['id'] == $sesiIdCheck);
                $sesiRowCheck = reset($sesiRowCheck);
                $sesiCloseCheck = !empty($sesiRowCheck['tanggal']) && !empty($sesiRowCheck['jam_tutup'])
                    ? strtotime($sesiRowCheck['tanggal'] . ' ' . $sesiRowCheck['jam_tutup'])
                    : ($sesiRowCheck && !empty($sesiRowCheck['tanggal']) ? strtotime($sesiRowCheck['tanggal'] . ' 23:59:59') : null);
                $sesiSudahTutup = $sesiCloseCheck !== null && $nowTs > $sesiCloseCheck;
                $isAlfa = ($statusCheck === 'Alfa');
                $belumPresensi = ($statusCheck === null) && $sesiSudahTutup;
                if ($isAlfa || $belumPresensi) {
                    // Cari step pertama yang bukan bagian dari sesi yang sama
                    $nextStep = null;
                    foreach ($konten as $kStep) {
                        if ($kStep['id'] <= $active_step_id) continue;
                        $kSesiId = $kStep['sesi_id'] ?? null;
                        if ($kSesiId !== $sesiIdCheck) {
                            $nextStep = $kStep['id'];
                            break;
                        }
                    }
                    if ($nextStep) {
                        return redirect()->to(base_url('pelatihan/peserta/belajar/' . $id . '?step=' . $nextStep));
                    }
                }
            }
        }
        
        $user = $db->table('users_pelatihan')->where('nik', $userId)->get()->getRowArray();

        $evalQuestionsRaw = $db->table('kuesioner_master_pelatihan')
            ->select('kuesioner_master_pelatihan.*, kategori_evaluasi_pelatihan.nama_kategori as kategori')
            ->join('kategori_evaluasi_pelatihan', 'kategori_evaluasi_pelatihan.id = kuesioner_master_pelatihan.kategori_id', 'left')
            ->where('pelatihan_id', $id)
            ->get()->getResultArray();
        $evalQuestions = [];
        foreach ($evalQuestionsRaw as $eq) {
            $evalQuestions[$eq['kategori']][] = $eq;
        }

        $sesiList = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $id)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $sertifikat = $db->table('sertifikat_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->where('jenis_dokumen', 'rsud')
            ->get()->getRowArray();

        // Fetch materi, narasumber, penyelenggara for global rating questionnaire
        $materiList = $db->table('materi_pelatihan')
            ->where('pelatihan_id', $id)
            ->orderBy('sesi_id', 'ASC')
            ->orderBy('segmen', 'ASC')
            ->orderBy('urutan', 'ASC')
            ->get()->getResultArray();

        $narasumberList = $db->table('narasumber_pelatihan')
            ->select('narasumber_pelatihan.*, pejabat_ttd_pelatihan.nama_pejabat, pejabat_ttd_pelatihan.gelar_depan, pejabat_ttd_pelatihan.gelar_belakang')
            ->join('pejabat_ttd_pelatihan', 'pejabat_ttd_pelatihan.id = narasumber_pelatihan.pejabat_ttd_id', 'left')
            ->where('narasumber_pelatihan.pelatihan_id', $id)
            ->get()->getResultArray();

        $penyelenggaraList = $db->table('penyelenggara_pelatihan')
            ->select('penyelenggara_pelatihan.*, master_penyelenggara.nama')
            ->join('master_penyelenggara', 'master_penyelenggara.id = penyelenggara_pelatihan.penyelenggara_id', 'left')
            ->where('penyelenggara_pelatihan.pelatihan_id', $id)
            ->get()->getResultArray();

        // Check if peserta already submitted global rating
        $ratingAlreadySubmitted = false;
        if ($pesertaRecord) {
            $ratingAlreadySubmitted = $db->table('peserta_kuesioner_saran_pelatihan')
                ->where('peserta_pelat_id', $pesertaRecord['id'])
                ->countAllResults() > 0;
        }

        $submittedSesiEvaluations = [];
        if ($pesertaRecord) {
            $sesiEvals = $db->table('peserta_kuesioner_rating_pelatihan')
                ->select('sesi_id')
                ->distinct()
                ->where('peserta_pelat_id', $pesertaRecord['id'])
                ->where('sesi_id IS NOT NULL', null, false)
                ->get()->getResultArray();
            foreach ($sesiEvals as $se) {
                $submittedSesiEvaluations[] = (int)$se['sesi_id'];
            }
        }

        $active_step_val = !empty($filtered_active) ? reset($filtered_active) : null;
        $active_sesi_id = ($active_step_val && isset($active_step_val['sesi_id'])) ? $active_step_val['sesi_id'] : null;
        $nextSessionStepId = null;
        if ($active_sesi_id !== null) {
            foreach ($konten as $kStep) {
                if ($kStep['id'] <= $active_step_id) continue;
                $kSesiId = $kStep['sesi_id'] ?? null;
                if ($kSesiId !== $active_sesi_id) {
                    $nextSessionStepId = $kStep['id'];
                    break;
                }
            }
        }
        if (!$nextSessionStepId) {
            $nextSessionStepId = $active_step_id + 1;
        }

        $data = [
            'title' => 'Ruang Belajar',
            'p' => $item,
            'konten' => $konten,
            'completed_steps' => $completed_steps,
            'active_step' => reset($filtered_active),
            'active_id' => $active_step_id,
            'nextSessionStepId' => $nextSessionStepId,
            'pg' => $pg,
            'user' => $user,
            'evalIndex' => $evalIndex,
            'certIndex' => $certIndex,
            'postTestIndex' => $postTestStepId,
            'preTestQuestions' => $preTestQuestions,
            'postTestQuestions' => $postTestQuestions,
            'evalQuestions' => $evalQuestions,
            'sesiList' => $sesiList,
            'sertifikat' => $sertifikat,
            'pre_test_attempted' => $preTestAttempted,
            'pre_test_score' => $preTestScore,
            'pre_test_benar' => $preTestBenar,
            'pre_test_salah' => $preTestSalah,
            'pre_test_total' => $preTestTotal,
            'post_test_attempts' => $postTestAttempts,
            'post_test_score' => $postTestScore,
            'post_test_status' => $postTestStatus,
            'post_test_benar' => $postTestBenar,
            'post_test_salah' => $postTestSalah,
            'post_test_total' => $postTestTotal,
            'stats_sesi_total' => $statsSesiTotal,
            'stats_sesi_hadir' => $statsSesiHadir,
            'materi_alfa' => $materiAlfa,
            'max_post_test_attempts' => 3,
            'materiList' => $materiList,
            'narasumberList' => $narasumberList,
            'penyelenggaraList' => $penyelenggaraList,
            'ratingAlreadySubmitted' => $ratingAlreadySubmitted,
            'submittedSesiEvaluations' => $submittedSesiEvaluations,
            'presensiStatusList' => $presensiStatusList,
            'post_test_kkm' => $postTest ? ($postTest['kkm'] ?? 70) : 70,
        ];
        return view('Pelatihan/peserta/pelatihan/belajar', $data);
    }

    private function _countKontenSteps($db, $pelatihanId): int
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
            $stepCounter += count($groupedSegmen); // materi_segmen
            $stepCounter++; // evaluasi_sesi
        }

        $postTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $pelatihanId)->where('tipe_evaluasi', 'Post-test')->get()->getRowArray();
        if ($postTest) $stepCounter++; // post_test
        $stepCounter++; // evaluasi
        $stepCounter++; // sertifikat
        return $stepCounter - 1;
    }

    private function _findPresensiStepId($db, $pelatihanId, $sesiId): ?int
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

    private function _getKontenSteps($db, $pelatihanId): array
    {
        $konten = [];
        $stepCounter = 1;
        $preTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $pelatihanId)->where('tipe_evaluasi', 'Pre-test')->get()->getRowArray();
        if ($preTest) {
            $konten[] = ['id' => $stepCounter++, 'tipe' => 'pre_test'];
        }

        $sesi = $db->table('sesi_interaktif_pelatihan')
            ->where('pelatihan_id', $pelatihanId)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('waktu', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        foreach ($sesi as $s) {
            $tipeSesi = strtolower($s['tipe_sesi'] ?? '') == 'offline' ? 'presensi' : 'sesi';
            $konten[] = ['id' => $stepCounter++, 'tipe' => $tipeSesi, 'sesi_id' => $s['id']];

            $materi = $db->table('materi_pelatihan')->where('sesi_id', $s['id'])->orderBy('segmen', 'ASC')->orderBy('urutan', 'ASC')->get()->getResultArray();
            $groupedSegmen = [];
            foreach ($materi as $m) { $groupedSegmen[$m['segmen'] ?: 1][] = $m; }
            foreach ($groupedSegmen as $seg => $mList) {
                $konten[] = ['id' => $stepCounter++, 'tipe' => 'materi_segmen', 'sesi_id' => $s['id'], 'segmen' => $seg];
            }
            $konten[] = ['id' => $stepCounter++, 'tipe' => 'evaluasi_sesi', 'sesi_id' => $s['id']];
        }

        $postTest = $db->table('ujian_pelatihan')->where('pelatihan_id', $pelatihanId)->where('tipe_evaluasi', 'Post-test')->get()->getRowArray();
        if ($postTest) {
            $konten[] = ['id' => $stepCounter++, 'tipe' => 'post_test'];
        }
        $konten[] = ['id' => $stepCounter++, 'tipe' => 'evaluasi'];
        $konten[] = ['id' => $stepCounter++, 'tipe' => 'sertifikat'];

        return $konten;
    }

    public function tandai_selesai($id, $step_id)
    {
        $userId = $this->session->get('user_id');
        $score = $this->request->getGet('score');

        $is_post_test = $this->request->getGet('is_post_test');
        $db = \Config\Database::connect();

        // Compute totalSteps from DB (don't rely on session which may be fresh)
        $totalSteps = $this->session->get('total_steps_'.$id);
        if (!$totalSteps) {
            $totalSteps = $this->_countKontenSteps($db, $id);
            $this->session->set('total_steps_'.$id, $totalSteps);
        }

        $pesertaRecord = $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->get()->getRowArray();

        // Auto-create peserta_pelatihan if missing
        if (!$pesertaRecord) {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => 'Aktif',
                'status_pembayaran' => 'Gratis',
                'status_akses' => 'Approved',
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pesertaRecord = $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->get()->getRowArray();
        }

        $completed_steps = $pesertaRecord ? (json_decode($pesertaRecord['completed_steps'] ?? '[]', true) ?? []) : [];

        // Logika evaluasi Post-Test
        if ($is_post_test == '1' && $score !== null) {
            $ujian = $db->table('ujian_pelatihan')->where('pelatihan_id', $id)->where('tipe_evaluasi', 'Post-test')->get()->getRowArray();
            $kkm = $ujian ? ($ujian['kkm'] ?? 70) : 70;
            
            $attempts = 0;
            if ($pesertaRecord) {
                $attempts = $db->table('peserta_ujian_pelatihan')
                    ->where('peserta_pelat_id', $pesertaRecord['id'])
                    ->where('tipe_ujian', 'post_test')
                    ->countAllResults();
            }
            
            if ($score < $kkm) {
                if ($attempts >= 3) {
                    if ($pesertaRecord) {
                        $db->table('peserta_pelatihan')
                           ->where('id', $pesertaRecord['id'])
                           ->update(['status_peserta' => 'Tidak Lulus', 'updated_at' => date('Y-m-d H:i:s')]);
                    }
                }
                return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id.'&error=score_low&last_score='.$score.'&attempts='.$attempts);
            }
        }

        // Tandai step selesai
        if (!in_array((int)$step_id, $completed_steps)) {
            $completed_steps[] = (int)$step_id;
        }
        $progressPct = (count($completed_steps) / $totalSteps) * 100;

        if ($pesertaRecord) {
            $db->table('peserta_pelatihan')
               ->where('id', $pesertaRecord['id'])
               ->update([
                   'completed_steps' => json_encode($completed_steps),
                   'progress' => $progressPct,
                   'updated_at' => date('Y-m-d H:i:s')
               ]);
        }

        $sesi_id = $this->request->getGet('sesi_id');
        $do_presensi = $this->request->getGet('do_presensi');
        if ($sesi_id && $pesertaRecord && $do_presensi == '1') {
            $existPresensi = $db->table('peserta_presensi_pelatihan')
                                ->where('peserta_pelat_id', $pesertaRecord['id'])
                                ->where('sesi_id', $sesi_id)
                                ->get()->getRowArray();
            if (!$existPresensi) {
                $db->table('peserta_presensi_pelatihan')->insert([
                    'peserta_pelat_id' => $pesertaRecord['id'],
                    'sesi_id' => $sesi_id,
                    'status_hadir' => 'Hadir',
                    'waktu_absen' => date('Y-m-d H:i:s')
                ]);
            } elseif ($existPresensi['status_hadir'] === 'Alfa') {
                // Peserta presensi mandiri (sesi masih aktif) → ubah Alfa ke Hadir
                $db->table('peserta_presensi_pelatihan')
                   ->where('id', $existPresensi['id'])
                   ->update(['status_hadir' => 'Hadir', 'waktu_absen' => date('Y-m-d H:i:s')]);
            }
        }

        $is_ujian = $this->request->getGet('is_ujian');
        if ($is_ujian == '1') {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id.'&success=1');
        }

        $next_step = $this->request->getGet('next_step');
        if ($next_step) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$next_step);
        }

        if ($pesertaRecord) {
            $kontenSteps = $this->_getKontenSteps($db, $id);
            $currStepObj = array_filter($kontenSteps, fn($k) => $k['id'] == $step_id);
            if (!empty($currStepObj)) {
                $cStep = reset($currStepObj);
                $cSesiId = $cStep['sesi_id'] ?? null;
                if ($cSesiId !== null) {
                    $pRow = $db->table('peserta_presensi_pelatihan')
                        ->where('peserta_pelat_id', $pesertaRecord['id'])
                        ->where('sesi_id', $cSesiId)
                        ->get()->getRowArray();
                    if (($pRow['status_hadir'] ?? null) === 'Alfa') {
                        foreach ($kontenSteps as $kStep) {
                            if ($kStep['id'] <= $step_id) continue;
                            if (($kStep['sesi_id'] ?? null) !== $cSesiId) {
                                return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$kStep['id']);
                            }
                        }
                    }
                }
            }
        }

        return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.($step_id + 1));
    }
    public function submit_kuis($id)
    {
        $userId = $this->session->get('user_id');
        $step_id = $this->request->getPost('step_id');
        $tipe_ujian = $this->request->getPost('tipe_ujian'); // 'pre_test' or 'post_test'
        $answersJson = $this->request->getPost('answers');
        
        $answers = json_decode($answersJson, true) ?? [];
        $totalQuestions = count($answers);
        $correctCount = 0;

        $db = \Config\Database::connect();
        
        // Cek data peserta
        $pesertaRecord = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();

        // Auto-create if missing
        if (!$pesertaRecord) {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => 'Aktif',
                'status_pembayaran' => 'Gratis',
                'status_akses' => 'Approved',
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pesertaRecord = $db->table('peserta_pelatihan')
                ->where('user_id', $userId)
                ->where('pelatihan_id', $id)
                ->get()->getRowArray();
        }
            
        if (!$pesertaRecord) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id)->with('error', 'Data peserta tidak ditemukan.');
        }

        $attempts = $db->table('peserta_ujian_pelatihan')
            ->where('peserta_pelat_id', $pesertaRecord['id'])
            ->where('tipe_ujian', $tipe_ujian)
            ->countAllResults();

        if ($tipe_ujian == 'pre_test' && $attempts >= 1) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id)->with('error', 'Pre-Test hanya dapat dikerjakan 1 kali.');
        }

        if ($tipe_ujian == 'post_test' && $attempts >= 3) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$step_id)->with('error', 'Post-Test hanya dapat dikerjakan 3 kali.');
        }

        // Ambil soal untuk mencocokkan jawaban
        $soalList = [];
        $db_tipe_evaluasi = ($tipe_ujian == 'pre_test') ? 'Pre-test' : 'Post-test';
        $ujian = $db->table('ujian_pelatihan')
            ->where('pelatihan_id', $id)
            ->where('tipe_evaluasi', $db_tipe_evaluasi)
            ->get()->getRowArray();
            
        if ($ujian) {
            $soals = $db->table('ujian_soal_pelatihan')->where('ujian_id', $ujian['id'])->get()->getResultArray();
            foreach ($soals as $s) {
                $soalList[$s['id']] = strtolower(trim($s['jawaban_benar']));
            }
        }

        // Untuk post-test: denominator = total semua soal (termasuk yang di-skip Alfa)
        // Soal yang tidak dijawab (di-skip) dihitung salah
        $totalAllQuestions = ($tipe_ujian == 'post_test') ? count($soalList) : $totalQuestions;

        $logJawaban = [];
        foreach ($answers as $ans) {
            $sId = $ans['soal_id'];
            $j = strtolower(trim($ans['jawaban'] ?? ''));
            $isCorrect = (isset($soalList[$sId]) && $soalList[$sId] === $j) ? 1 : 0;
            if ($isCorrect) $correctCount++;
            
            $logJawaban[] = [
                'soal_id' => $sId,
                'jawaban_peserta' => strtoupper($j),
                'is_correct' => $isCorrect
            ];
        }

        // Soal yang di-skip (tidak ada di answers) → log sebagai tidak dijawab
        if ($tipe_ujian == 'post_test') {
            $answeredIds = array_column($answers, 'soal_id');
            foreach ($soalList as $soalId => $jawaban) {
                if (!in_array($soalId, $answeredIds)) {
                    $logJawaban[] = [
                        'soal_id' => $soalId,
                        'jawaban_peserta' => '-',
                        'is_correct' => 0
                    ];
                }
            }
        }

        $score = $totalAllQuestions > 0 ? round(($correctCount / $totalAllQuestions) * 100, 2) : 0;
        
        // Simpan Hasil Ujian
        $ujianIdInserted = null;
        $kkm = $ujian ? ($ujian['kkm'] ?? 70) : 70;
        
        $statusLulus = ($score >= $kkm) ? 'Lulus' : 'Tidak Lulus';

        $db->table('peserta_ujian_pelatihan')->insert([
            'peserta_pelat_id' => $pesertaRecord['id'],
            'tipe_ujian' => $tipe_ujian,
            'score' => $score,
            'status_lulus' => $statusLulus,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $ujianIdInserted = $db->insertID();

        // Simpan Log Jawaban
        if ($ujianIdInserted && !empty($logJawaban)) {
            foreach ($logJawaban as &$lj) {
                $lj['peserta_ujian_id'] = $ujianIdInserted;
            }
            $db->table('peserta_jawaban_ujian_pelatihan')->insertBatch($logJawaban);
        }

        // Lanjut ke tandai_selesai (menggunakan querystring untuk update status progres dll)
        $isPostTestNum = ($tipe_ujian == 'post_test') ? 1 : 0;
        return redirect()->to('/pelatihan/peserta/tandai_selesai/'.$id.'/'.$step_id.'?score='.$score.'&is_post_test='.$isPostTestNum.'&is_ujian=1');
    }


    public function submit_evaluasi_sesi($id)
    {
        $userId  = $this->session->get('user_id');
        $stepId  = $this->request->getPost('step_id');
        $sesiId  = $this->request->getPost('sesi_id');

        if (!$sesiId) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id)->with('error', 'Sesi tidak valid.');
        }

        $db = \Config\Database::connect();
        $pesertaRecord = $db->table('peserta_pelatihan')
            ->where('user_id', $userId)
            ->where('pelatihan_id', $id)
            ->get()->getRowArray();

        // Auto-create if missing
        if (!$pesertaRecord) {
            $db->table('peserta_pelatihan')->insert([
                'user_id' => $userId,
                'pelatihan_id' => $id,
                'status_peserta' => 'Aktif',
                'status_pembayaran' => 'Gratis',
                'status_akses' => 'Approved',
                'waktu_daftar' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $pesertaRecord = $db->table('peserta_pelatihan')
                ->where('user_id', $userId)
                ->where('pelatihan_id', $id)
                ->get()->getRowArray();
        }

        if (!$pesertaRecord) {
            return redirect()->to('/pelatihan/peserta/belajar/'.$id)->with('error', 'Data peserta tidak ditemukan.');
        }

        $pesertaPelatId = $pesertaRecord['id'];
        $batchData = [];

        $materiRows = $db->table('materi_pelatihan')->where('sesi_id', $sesiId)->select('id')->get()->getResultArray();
        $materiIds = array_column($materiRows, 'id');

        $ratingsMateri = $this->request->getPost('rating_materi');
        if ($ratingsMateri && is_array($ratingsMateri)) {
            foreach ($ratingsMateri as $materiId => $kuesionerRatings) {
                if (!in_array((int)$materiId, $materiIds)) continue;
                foreach ($kuesionerRatings as $kuesionerId => $nilai) {
                    $batchData[] = [
                        'peserta_pelat_id' => $pesertaPelatId,
                        'kuesioner_id'     => $kuesionerId,
                        'sesi_id'          => $sesiId,
                        'materi_id'        => $materiId,
                        'narasumber_id'    => null,
                        'penyelenggara_id' => null,
                        'nilai_rating'     => (int)$nilai,
                    ];
                }
            }
        }

        $ratingsNarasumber = $this->request->getPost('rating_narasumber');
        if ($ratingsNarasumber && is_array($ratingsNarasumber)) {
            foreach ($ratingsNarasumber as $narasumberId => $kuesionerRatings) {
                foreach ($kuesionerRatings as $kuesionerId => $nilai) {
                    $batchData[] = [
                        'peserta_pelat_id' => $pesertaPelatId,
                        'kuesioner_id'     => $kuesionerId,
                        'sesi_id'          => $sesiId,
                        'materi_id'        => null,
                        'narasumber_id'    => $narasumberId,
                        'penyelenggara_id' => null,
                        'nilai_rating'     => (int)$nilai,
                    ];
                }
            }
        }

        $ratingsPenyelenggara = $this->request->getPost('rating_penyelenggara');
        if ($ratingsPenyelenggara && is_array($ratingsPenyelenggara)) {
            foreach ($ratingsPenyelenggara as $penyelenggaraId => $kuesionerRatings) {
                foreach ($kuesionerRatings as $kuesionerId => $nilai) {
                    $batchData[] = [
                        'peserta_pelat_id' => $pesertaPelatId,
                        'kuesioner_id'     => $kuesionerId,
                        'sesi_id'          => $sesiId,
                        'materi_id'        => null,
                        'narasumber_id'    => null,
                        'penyelenggara_id' => $penyelenggaraId,
                        'nilai_rating'     => (int)$nilai,
                    ];
                }
            }
        }

        $ratingsFasil = $this->request->getPost('rating_fasilitator');
        if ($ratingsFasil && is_array($ratingsFasil) && isset($ratingsFasil[$sesiId])) {
            foreach ($ratingsFasil[$sesiId] as $kuesionerId => $nilai) {
                $batchData[] = [
                    'peserta_pelat_id' => $pesertaPelatId,
                    'kuesioner_id'     => $kuesionerId,
                    'sesi_id'          => $sesiId,
                    'materi_id'        => null,
                    'narasumber_id'    => null,
                    'penyelenggara_id' => null,
                    'nilai_rating'     => $nilai,
                ];
            }
        }

        $ratings = $this->request->getPost('rating');
        if ($ratings && is_array($ratings)) {
            foreach ($ratings as $kuesionerId => $nilai) {
                $batchData[] = [
                    'peserta_pelat_id' => $pesertaPelatId,
                    'kuesioner_id'     => $kuesionerId,
                    'sesi_id'          => $sesiId,
                    'materi_id'        => null,
                    'narasumber_id'    => null,
                    'penyelenggara_id' => null,
                    'nilai_rating'     => $nilai,
                ];
            }
        }

        if (!empty($batchData)) {
            $db->table('peserta_kuesioner_rating_pelatihan')->insertBatch($batchData);
        }

        if ($stepId && $pesertaRecord) {
            $completedSteps = json_decode($pesertaRecord['completed_steps'] ?? '[]', true) ?? [];
            if (!in_array((int)$stepId, $completedSteps)) {
                $completedSteps[] = (int)$stepId;
            }
            $totalSteps = $this->session->get('total_steps_'.$id);
            if (!$totalSteps) {
                $totalSteps = $this->_countKontenSteps($db, $id);
                $this->session->set('total_steps_'.$id, $totalSteps);
            }
            $db->table('peserta_pelatihan')
               ->where('id', $pesertaPelatId)
               ->update([
                   'completed_steps' => json_encode($completedSteps),
                   'progress' => (count($completedSteps) / $totalSteps) * 100,
                   'updated_at' => date('Y-m-d H:i:s')
               ]);
        }

        return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.($stepId + 1))
            ->with('success', 'Evaluasi sesi berhasil dikirim!');
    }

    public function submit_evaluasi($id)
    {
        $userId    = $this->session->get('user_id');
        $evalIndex = $this->session->get('eval_step_'.$id) ?? 6;
        $certIndex = $this->session->get('cert_step_'.$id) ?? 7;

        $db = \Config\Database::connect();
        $pesertaRecord = $db->table('peserta_pelatihan')
           ->where('user_id', $userId)
           ->where('pelatihan_id', $id)
           ->get()->getRowArray();

        if ($pesertaRecord) {
            $pesertaPelatId = $pesertaRecord['id'];
            $completedSteps = json_decode($pesertaRecord['completed_steps'] ?? '[]', true) ?? [];
            if (!in_array($evalIndex, $completedSteps)) $completedSteps[] = $evalIndex;
            if (!in_array($certIndex, $completedSteps)) $completedSteps[] = $certIndex;

            $db->table('peserta_pelatihan')
               ->where('id', $pesertaPelatId)
               ->update([
                   'completed_steps' => json_encode($completedSteps),
                   'progress' => 100,
                   'updated_at' => date('Y-m-d H:i:s')
               ]);

            $ratingUmum = $this->request->getPost('rating_umum') ?? 5;
            $saran      = $this->request->getPost('saran') ?? '';

            $db->table('peserta_kuesioner_saran_pelatihan')->insert([
                'peserta_pelat_id' => $pesertaPelatId,
                'rating_umum'      => $ratingUmum,
                'saran_masukan'    => $saran,
                'waktu_submit'     => date('Y-m-d H:i:s')
            ]);

            $ujianPost = $db->table('peserta_ujian_pelatihan')
                ->where('peserta_pelat_id', $pesertaPelatId)
                ->where('tipe_ujian', 'post_test')
                ->orderBy('created_at', 'DESC')
                ->get()->getRowArray();

            if ($ujianPost && $ujianPost['status_lulus'] == 'Lulus') {
                $db->table('peserta_pelatihan')
                   ->where('id', $pesertaPelatId)
                   ->update([
                       'status_peserta' => 'Lulus',
                       'updated_at'     => date('Y-m-d H:i:s')
                   ]);
                $msg = 'Pelatihan Selesai! Anda telah resmi Lulus pelatihan ini. Terimakasih atas evaluasi Anda.';
            } else {
                $msg = 'Pelatihan Selesai! Terimakasih atas evaluasi Anda.';
            }
        }

        return redirect()->to('/pelatihan/peserta/belajar/'.$id.'?step='.$certIndex)->with('success', $msg ?? 'Evaluasi berhasil disimpan.');
    }

    public function approve_and_start($id)
    {
        $userId = $this->session->get('user_id');
        if (!$userId) return redirect()->to('/login');

        $db = \Config\Database::connect();
        $db->table('peserta_pelatihan')
           ->where('user_id', $userId)
           ->where('pelatihan_id', $id)
           ->update([
               'status_pembayaran' => 'Verified',
               'status_akses' => 'Approved'
           ]);

        return redirect()->to('/pelatihan/peserta/belajar/'.$id)->with('success', 'Akses berhasil disetujui.');
    }

    public function reset_simulasi($id = null)
    {
        $userId = $this->session->get('user_id');
        $db = \Config\Database::connect();
        
        if ($id) {
            $db->table('peserta_pelatihan')->where('user_id', $userId)->where('pelatihan_id', $id)->delete();
        } else {
            $db->table('peserta_pelatihan')->where('user_id', $userId)->delete();
        }
        return redirect()->back()->with('success', 'Reset berhasil.');
    }
}
