<?php
$p = $p ?? [];
$konten = $konten ?? [];
$completed_steps = $completed_steps ?? [];
$active_step = $active_step ?? [];
$active_id = $active_id ?? 1;
$pg = $pg ?? [];
$user = $user ?? [];
$evalQuestions = $evalQuestions ?? [];
$sesiList = $sesiList ?? [];
$sertifikat = $sertifikat ?? [];
$preTestQuestions = $preTestQuestions ?? [];
$postTestQuestions = $postTestQuestions ?? [];
$evalIndex = $evalIndex ?? null;
$certIndex = $certIndex ?? null;
$postTestIndex = $postTestIndex ?? null;
$materiList = $materiList ?? [];
$narasumberList = $narasumberList ?? [];
$penyelenggaraList = $penyelenggaraList ?? [];
$ratingAlreadySubmitted = $ratingAlreadySubmitted ?? false;
$submittedSesiEvaluations = $submittedSesiEvaluations ?? [];
$nowTs = time();


?>

<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --primary-red: #ce2127;
        --accent-yellow: #ffc107;
        --primary-dark: #0f172a;
        --bg-light: #f8fafc;
        --active-item: #f1f5f9;
    }

    .learning-layout {
        display: flex;
        min-height: calc(100vh - 100px);
        margin: -1rem 0; /* Remove negative horizontal margins */
        background: transparent;
        overflow: hidden;
    }
    @media (min-width: 992px) {
        .learning-layout {
            margin: -1rem 0; 
        }
    }

    .learning-sidebar {
        width: 320px;
        background: white;
        border-right: 1px solid #e2e8f0;
        overflow-y: auto;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
    }

    .learning-content {
        flex-grow: 1;
        padding: 40px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        padding: 25px;
        border-bottom: 2px solid var(--primary-red);
        background: var(--primary-dark);
        color: white;
    }

    .sidebar-nav {
        flex-grow: 1;
        padding: 20px 0;
    }

    .nav-step {
        padding: 15px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
        color: #475569;
        transition: all 0.2s;
        border-left: 5px solid transparent;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .nav-step:hover {
        background: #f1f5f9;
        color: var(--primary-red);
    }

    .nav-step.active {
        background: #fff1f2;
        border-left-color: var(--primary-red);
        color: var(--primary-red);
        font-weight: 800;
    }

    .nav-step.completed i {
        color: #10b981;
    }

    .nav-step.locked {
        color: #94a3b8;
    }

    .nav-step i {
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
    }

    .content-card {
        background: linear-gradient(135deg, rgba(80, 10, 15, 0.98) 0%, rgba(45, 5, 10, 0.98) 100%);
        border-radius: 0; /* Full screen card */
        box-shadow: none;
        padding: 40px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        border: none;
        color: white;
    }

    .material-viewer {
        flex-grow: 1;
        background: var(--primary-dark);
        border-radius: 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        border: 8px solid #e2e8f0;
    }

    .inline-document-preview {
        width: 100%;
        min-height: 620px;
        border: 0;
        border-radius: 14px;
        background: #ffffff;
    }

    .document-preview-shell {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.15);
    }

    .document-preview-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: #0f172a;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 800;
        font-size: .82rem;
    }

    .document-preview-head a {
        color: var(--primary-red);
        text-decoration: none;
        white-space: nowrap;
    }

    .viewer-placeholder {
        text-align: center;
        color: #fff;
    }

    /* Multi-step Evaluation Stepper */
    .evaluasi-stepper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 50px;
        position: relative;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .evaluasi-stepper::before {
        content: '';
        position: absolute;
        top: 25px;
        left: 50px;
        right: 50px;
        height: 2px;
        background: #edf2f7;
        z-index: 1;
    }

    .stepper-progress {
        position: absolute;
        top: 25px;
        left: 50px;
        height: 2px;
        background: var(--primary-red);
        z-index: 2;
        transition: width 0.3s ease;
    }

    .step-item {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        width: 100px;
    }

    .step-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: white;
        border: 2px solid #edf2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #718096;
        transition: all 0.3s;
    }

    .step-item.active .step-circle {
        border-color: var(--primary-red);
        background: var(--primary-red);
        color: white;
        box-shadow: 0 4px 12px rgba(206, 33, 39, 0.3);
    }

    .step-item.completed .step-circle {
        border-color: #10b981;
        background: #10b981;
        color: white;
    }

    .step-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #a0aec0;
    }

    .step-item.active .step-label {
        color: var(--primary-dark);
    }

    /* Rating Scale 1-5 Custom */
    .rating-row {
        margin-bottom: 30px;
    }

    .rating-row > label {
        color: #ffffff !important;
        font-size: 1.1rem !important;
        font-weight: 800 !important;
    }

    .rating-options {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .rating-btn {
        flex: 1;
        text-align: center;
    }

    .rating-btn input {
        display: none;
    }

    .rating-btn label {
        display: block;
        padding: 12px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid rgba(255, 255, 255, 0.15);
        font-weight: 800;
        color: white !important;
    }

    .rating-btn input:checked + label {
        background: #ffffff;
        border-color: #ffffff;
        color: #ce2127 !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    /* Quiz Interactivity */
    .quiz-option {
        padding: 20px;
        background: #ffffff;
        border: 2px solid #ffffff;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
        color: #1a202c !important;
        font-weight: 600;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .quiz-option:hover {
        border-color: #ce2127 !important;
        background: #f8fafc;
        transform: translateY(-2px);
    }

    .quiz-option input {
        display: none;
    }

    .quiz-option.selected {
        border-color: #ce2127 !important;
        background: #fff1f2;
        box-shadow: 0 4px 12px rgba(206, 33, 39, 0.15);
        color: #ce2127 !important;
    }

    .quiz-option-circle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .quiz-option.selected .quiz-option-circle {
        border-color: #ce2127;
        background: #ce2127;
    }

    .quiz-option.selected .quiz-option-circle::after {
        content: '';
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
    }

    .btn-selanjutnya {
        background-color: var(--primary-red);
        color: white;
        padding: 15px 40px;
        border-radius: 12px;
        font-weight: 800;
        border: none;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-selanjutnya:hover {
        background-color: #a51a1f;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(206, 33, 39, 0.2);
    }

    .btn-selanjutnya:disabled {
        background-color: #cbd5e0;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    @media (max-width: 992px) {
        .learning-layout { flex-direction: column; }
        .learning-content { padding: 16px; }
        .content-card { padding: 20px; }
        .inline-document-preview { min-height: 350px; }
    }
    @media (max-width: 575.98px) {
        .learning-layout { margin: -10px 0; border-radius: 0; border-left: none; border-right: none; }
        .content-card { padding: 16px; border-radius: 0; }
        .content-card h2 { font-size: 1.3rem !important; }
    }

    @keyframes bounceSmall {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

</style>

<div class="learning-layout">
    
    <!-- Sidebar Offcanvas -->
    <div class="offcanvas offcanvas-start border-0 shadow-lg" tabindex="-1" id="sidebarBelajar" aria-labelledby="sidebarBelajarLabel" style="width: 320px;">
        <div class="learning-sidebar w-100 h-100 border-0">
            <div class="sidebar-header">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="small text-white text-uppercase fw-bold letter-spacing-1">Progress Belajar</div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebarBelajar" aria-label="Close"></button>
                </div>
            <h6 class="fw-bold mb-3"><?= $p['nama'] ?></h6>
            <div class="progress" style="height: 8px; background: #e2e8f0; border-radius: 10px;">
                <div class="progress-bar bg-danger shadow-sm" style="width: <?= (count($completed_steps) / count($konten)) * 100 ?>%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-light fw-bold opacity-75"><?= count($completed_steps) ?>/<?= count($konten) ?> SELESAI</small>
                <small class="text-light fw-bold"><?= round((count($completed_steps) / count($konten)) * 100) ?>%</small>
            </div>
        </div>

        <div class="sidebar-nav">
            <?php 
                $max_completed = !empty($completed_steps) ? max($completed_steps) : 0;
                foreach ($konten as $index => $k) : 
                    $is_completed = in_array($k['id'], $completed_steps);
                    $is_active = $active_id == $k['id'];
                    $is_locked = $k['id'] > ($max_completed + 1);
                    $is_materi = $k['tipe'] == 'materi_segmen';
                    $is_evaluasi_sesi = $k['tipe'] == 'evaluasi_sesi';
                    $is_sub_item = $is_materi || $is_evaluasi_sesi;
                    $sesiStatus = isset($k['sesi_id']) ? ($presensiStatusList[$k['sesi_id']] ?? null) : null;
                    // Hanya lock materi_segmen & evaluasi_sesi jika alfa, BUKAN step presensi/sesi itu sendiri
                    $isAlfaBlocked = $is_sub_item && $sesiStatus === 'Alfa';
                    if ($isAlfaBlocked) $is_locked = true;
            ?>
                <a href="<?= $is_locked ? 'javascript:void(0)' : base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$k['id']) ?>" 
                   class="nav-step <?= $is_active ? 'active' : '' ?> <?= $is_completed ? 'completed' : '' ?> <?= $is_locked ? 'locked opacity-50' : '' ?> <?= $is_sub_item ? 'ms-4' : '' ?>"
                   <?= $is_locked ? 'onclick="Swal.fire({icon:\'lock\', title:\'Materi Terkunci\', text:\''.($isAlfaBlocked ? 'Anda tidak dapat mengakses materi ini karena status kehadiran tercatat ALFA.' : 'Selesaikan materi sebelumnya untuk membuka bagian ini.').'\', confirmButtonColor:\'#1a202c\'})"' : '' ?>>
                    <?php if ($is_locked) : ?>
                        <i class="fas fa-lock"></i>
                    <?php elseif ($is_completed) : ?>
                        <i class="fas fa-check-circle"></i>
                    <?php elseif ($is_active) : ?>
                        <i class="fas fa-play-circle"></i>
                    <?php elseif ($is_evaluasi_sesi) : ?>
                        <i class="fas fa-clipboard-check"></i>
                    <?php else : ?>
                        <i class="fas fa-circle-notch"></i>
                    <?php endif; ?>
                    <span class="fw-bold"><?= strtoupper($k['judul']) ?></span>
                    <?php if ($k['tipe'] == 'pre_test' || $k['tipe'] == 'post_test') : ?>
                        <span class="badge bg-dark text-white shadow-sm px-3 py-2 fw-bold border border-white"><?= strtoupper($p['mekanisme']) ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

    <!-- Content Area -->
    <div class="learning-content p-0">
        
        <!-- Floating Navigasi Materi Button -->
        <button class="btn rounded-pill text-dark d-inline-flex align-items-center fw-extrabold shadow-lg" 
                style="position: fixed; bottom: 30px; right: 30px; z-index: 1030; background: white; border: 2px solid #ce2127; animation: bounceSmall 2s infinite ease-in-out; padding: 12px 24px; font-size: 1rem; gap: 10px;" 
                onmouseover="this.style.animationPlayState='paused';" onmouseout="this.style.animationPlayState='running';"
                type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarBelajar" aria-controls="sidebarBelajar" title="Tampilkan Menu Progress Belajar">
            <i class="fas fa-bars text-danger"></i> Navigasi Materi
        </button>

        <div class="content-card">
            <div class="d-flex flex-column justify-content-center align-items-center mb-4 border-bottom border-white border-opacity-20 pb-4 text-center">
                <div class="badge bg-white text-dark px-4 py-2 rounded-pill fw-bold mb-3" style="font-size: 0.8rem; letter-spacing: 1px;"><?= strtoupper(str_replace('_', ' ', $active_step['tipe'])) ?></div>
                <div>
                    <h2 class="fw-bold mb-0 text-white fs-2" style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);"><?= strtoupper($active_step['judul']) ?></h2>
                    <?php if ($active_step['tipe'] == 'presensi') : ?>
                        <p class="text-white-50 small mb-0 mt-2 fw-bold"><i class="fas fa-map-marker-alt me-1 text-warning"></i> LOKASI: RSUD KOTA YOGYAKARTA</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($active_step['tipe'] == 'presensi' || $active_step['tipe'] == 'sesi') : ?>
                <?php
                    $sessionOpen = isset($active_step['available']) ? (bool)$active_step['available'] : true;
                    $currSesiId = $active_step['sesi_id'] ?? 0;
                    $statusHadirSesi = $active_step['status_hadir'] ?? ($presensiStatusList[$currSesiId] ?? null);
                    $isAlfaSesi = ($statusHadirSesi === 'Alfa');
                    $isHadirSesi = ($statusHadirSesi === 'Hadir');
                    $isIzinSesi = ($statusHadirSesi === 'Izin');
                    
                    $tipeSesiVal = strtolower($active_step['tipe_sesi'] ?? ($active_step['tipe'] == 'presensi' ? 'offline' : 'online'));
                    if ($tipeSesiVal === 'offline') {
                        $badgeTipeText = 'SESI OFFLINE (TATAP MUKA)';
                        $badgeTipeIcon = 'fa-users';
                        $badgeTipeClass = 'bg-danger text-white';
                    } elseif ($tipeSesiVal === 'hybrid') {
                        $badgeTipeText = 'SESI HYBRID';
                        $badgeTipeIcon = 'fa-headset';
                        $badgeTipeClass = 'bg-warning text-dark';
                    } else {
                        $badgeTipeText = 'SESI ONLINE';
                        $badgeTipeIcon = 'fa-video';
                        $badgeTipeClass = 'bg-info text-white';
                    }

                    $materiForSesi = array_values(array_filter($materiList ?? [], fn($m) => (int)($m['sesi_id'] ?? 0) === (int)$currSesiId));
                    $narasumberForSesi = array_values(array_filter($narasumberList ?? [], fn($n) => (int)($n['sesi_id'] ?? 0) === (int)$currSesiId));
                    $penyelenggaraForSesi = array_values(array_filter($penyelenggaraList ?? [], fn($p) => (int)($p['sesi_id'] ?? 0) === (int)$currSesiId));
                ?>

                <div class="py-2 animate__animated animate__fadeIn">
                    <!-- Sesi Header Badges -->
                    <div class="d-flex align-items-center justify-content-center gap-3 mb-4 flex-wrap">
                        <span class="badge <?= $badgeTipeClass ?> fs-6 px-4 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="fas <?= $badgeTipeIcon ?> me-2"></i> <?= $badgeTipeText ?>
                        </span>
                        <span class="badge <?= $sessionOpen ? 'bg-success text-white' : 'bg-secondary text-white' ?> fs-6 px-4 py-2 rounded-pill fw-bold shadow-sm">
                            <i class="fas <?= $sessionOpen ? 'fa-door-open' : 'fa-lock' ?> me-2"></i> <?= $sessionOpen ? 'SESI DIBUKA' : 'SESI DITUTUP' ?>
                        </span>
                    </div>

                    <!-- Clean Structured Info Box (2 Columns) -->
                    <div class="row g-4 mb-4">
                        
                        <!-- Left Column -->
                        <div class="col-lg-6">
                            
                            <!-- 1. JADWAL PELAKSANAAN -->
                            <div class="p-4 rounded-4 shadow-sm h-100 mb-4" style="background: #ffffff; border: 2px solid #e2e8f0 !important;">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                                        <i class="fas fa-calendar-alt fa-2x text-danger"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-0 letter-spacing-1">JADWAL PELAKSANAAN</h5>
                                </div>
                                <div class="row g-3 fs-6 ps-2">
                                    <div class="col-5 text-secondary fw-semibold">Tanggal</div>
                                    <div class="col-7 text-dark fw-bold">: <?= !empty($active_step['tanggal']) ? tanggal_indo($active_step['tanggal']) : '-' ?></div>
                                    
                                    <div class="col-5 text-secondary fw-semibold">Jam Sesi</div>
                                    <div class="col-7 text-dark fw-bold">: <?= (!empty($active_step['waktu']) ? date('H:i', strtotime($active_step['waktu'])) : '00:00') ?> s.d <?= (!empty($active_step['jam_tutup']) ? date('H:i', strtotime($active_step['jam_tutup'])) : 'Selesai') ?> WIB</div>
                                    
                                    <?php if (!empty($active_step['tempat'])): ?>
                                        <div class="col-5 text-secondary fw-semibold">Tempat</div>
                                        <div class="col-7 text-dark fw-bold">: <?= esc($active_step['tempat']) ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($active_step['lokasi_ruang'])): ?>
                                        <div class="col-5 text-secondary fw-semibold">Ruang</div>
                                        <div class="col-7 text-dark fw-bold">: <?= esc($active_step['lokasi_ruang']) ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($active_step['alamat'])): ?>
                                        <div class="col-5 text-secondary fw-semibold">Alamat</div>
                                        <div class="col-7 text-secondary">: <?= esc($active_step['alamat']) ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($active_step['maps_url'])): ?>
                                        <div class="col-5 text-secondary fw-semibold">Maps</div>
                                        <div class="col-7"><a href="<?= $active_step['maps_url'] ?>" target="_blank" class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill px-3"><i class="fas fa-map-marked-alt me-1"></i> Buka Google Maps</a></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-lg-6 d-flex flex-column gap-4">
                            
                            <!-- 2. LINK MEETING & PASSWORD -->
                            <?php if ($tipeSesiVal !== 'offline' || !empty($active_step['meeting_link']) || !empty($active_step['meeting_pass'])): ?>
                            <div class="p-4 rounded-4 shadow-sm" style="background: #f0fdf4; border: 2px solid #bbf7d0 !important;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                                        <i class="fas fa-video fa-lg text-success"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 letter-spacing-1">LINK MEETING &amp; PASSCODE</h6>
                                </div>
                                <div class="ps-2">
                                    <?php if (!empty($active_step['meeting_link'])): ?>
                                        <div class="mb-3">
                                            <a href="<?= esc($active_step['meeting_link']) ?>" target="_blank" class="btn btn-success text-white fw-bold fs-6 rounded-pill px-4 py-2 shadow-sm w-100 text-start">
                                                <i class="fas fa-external-link-alt me-2"></i> Buka Link Meeting
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="text-dark fw-bold mt-2 d-flex justify-content-between align-items-center">
                                        <span class="text-secondary fw-semibold fs-6">Passcode:</span>
                                        <?php if (!empty($active_step['meeting_pass'])): ?>
                                            <span class="badge bg-warning text-dark fs-6 fw-extrabold px-3 py-1 rounded-pill border border-warning shadow-sm">
                                                <?= esc($active_step['meeting_pass']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-secondary fst-italic">Tidak ada</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- 3. NARASUMBER & PENYELENGGARA -->
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-4 shadow-sm h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="small fw-bold text-primary mb-2"><i class="fas fa-user-tie me-2"></i>NARASUMBER</div>
                                        <div class="fs-6 text-dark fw-bold">
                                            <?php if (!empty($narasumberForSesi)): ?>
                                                <?php foreach ($narasumberForSesi as $nSesi): ?>
                                                    <div class="mb-1 text-truncate" title="<?= esc(($nSesi['gelar_depan'] ? $nSesi['gelar_depan'].' ' : '').$nSesi['nama_pejabat'].($nSesi['gelar_belakang'] ? ', '.$nSesi['gelar_belakang'] : '')) ?>">• <?= esc(($nSesi['gelar_depan'] ? $nSesi['gelar_depan'].' ' : '').$nSesi['nama_pejabat'].($nSesi['gelar_belakang'] ? ', '.$nSesi['gelar_belakang'] : '')) ?></div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-secondary fw-normal fst-italic">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-4 shadow-sm h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="small fw-bold text-primary mb-2"><i class="fas fa-building me-2"></i>PENYELENGGARA</div>
                                        <div class="fs-6 text-dark fw-bold">
                                            <?php if (!empty($penyelenggaraForSesi)): ?>
                                                <?php foreach ($penyelenggaraForSesi as $pSesi): ?>
                                                    <div class="mb-1 text-truncate" title="<?= esc($pSesi['nama']) ?>">• <?= esc($pSesi['nama']) ?></div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-secondary fw-normal fst-italic">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. MATERI PEMBELAJARAN -->
                            <div class="p-4 rounded-4 shadow-sm" style="background: #fffbeb; border: 2px solid #fde68a !important;">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
                                        <i class="fas fa-book fa-lg text-warning"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 letter-spacing-1">MATERI PEMBELAJARAN</h6>
                                </div>
                                <div class="ps-2">
                                    <?php if (!empty($materiForSesi)): ?>
                                        <?php foreach ($materiForSesi as $mIndex => $mSesi): ?>
                                            <div class="<?= ($mIndex < count($materiForSesi) - 1) ? 'mb-2 pb-2 border-bottom border-warning border-opacity-25' : '' ?>">
                                                <div class="fw-bold text-dark fs-6"><i class="fas fa-file-alt text-warning me-2"></i><?= esc($mSesi['judul']) ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-secondary fs-6 fst-italic">Belum ada materi terdaftar.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                        </div>

                    </div>

                    <!-- Presensi & Actions Footer -->
                    <div class="w-100 text-center">
                        <?php if ($isAlfaSesi && !$sessionOpen): ?>
                            <!-- SESI TERLEWAT & STATUS ALFA -->
                            <div class="alert bg-danger bg-opacity-30 rounded-4 shadow-sm p-4 text-center border border-danger text-white mb-3">
                                <div class="mb-2"><span class="badge bg-danger px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-times-circle me-1"></i> STATUS KEHADIRAN: ALFA</span></div>
                                <h5 class="fw-bold text-white mb-1">Sesi ini sudah terlewat</h5>
                                <p class="text-white-50 fs-6 mb-0">Status kehadiran Anda tercatat <strong class="text-warning">ALFA</strong>. Isi materi dan evaluasi pada sesi ini tidak dapat diakses.</p>
                            </div>
                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.'?next_step='.$nextSessionStepId.(isset($active_step['sesi_id']) ? '&sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn w-100 py-3 rounded-pill fw-extrabold shadow-lg hover-scale fs-5 border-0 animate__animated animate__bounce animate__infinite" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: white !important; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4) !important;">
                                LANJUT KE SESI BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php elseif ($isHadirSesi): ?>
                            <!-- STATUS HADIR -->
                            <div class="alert bg-success bg-opacity-30 rounded-4 shadow-sm p-4 text-center border border-success text-white mb-3">
                                <div class="mb-2"><span class="badge bg-success px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-check-circle me-1"></i> STATUS KEHADIRAN: HADIR</span></div>
                                <h5 class="fw-bold text-white mb-2">Presensi Anda telah tercatat</h5>
                                <?php if (!empty($active_step['attended_at'])): ?>
                                    <p class="text-white-50 fs-6 mb-0">Pada: <strong class="text-white"><?= date('d M Y H:i', strtotime($active_step['attended_at'])) ?> WIB</strong></p>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.'?next_step='.($active_id + 1).(isset($active_step['sesi_id']) ? '&sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn w-100 py-3 rounded-pill fw-extrabold shadow-lg hover-scale fs-5 border-0 animate__animated animate__bounce animate__infinite" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: white !important; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4) !important;">
                                SELESAI &amp; LANJUT KE MATERI <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php elseif ($isIzinSesi): ?>
                            <!-- STATUS IZIN -->
                            <div class="alert bg-warning bg-opacity-30 rounded-4 shadow-sm p-4 text-center border border-warning text-white mb-3">
                                <div class="mb-2"><span class="badge bg-warning text-dark px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-exclamation-circle me-1"></i> STATUS KEHADIRAN: IZIN</span></div>
                                <h5 class="fw-bold text-white mb-2">Presensi Anda tercatat sebagai Izin</h5>
                                <?php if (!empty($active_step['attended_at'])): ?>
                                    <p class="text-white-50 fs-6 mb-0">Pada: <strong class="text-white"><?= date('d M Y H:i', strtotime($active_step['attended_at'])) ?> WIB</strong></p>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.'?next_step='.($active_id + 1).(isset($active_step['sesi_id']) ? '&sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn w-100 py-3 rounded-pill fw-extrabold shadow-lg hover-scale fs-5 border-0 animate__animated animate__bounce animate__infinite" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: white !important; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4) !important;">
                                SELESAI &amp; LANJUT KE MATERI <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php elseif ($sessionOpen): ?>
                            <!-- SESI MASIH DIBUKA -->
                            <?php if ($isAlfaSesi): ?>
                                <div class="mb-2"><span class="badge bg-danger px-4 py-2 rounded-pill fs-6 fw-bold"><i class="fas fa-times-circle me-1"></i> STATUS: ALFA</span></div>
                                <p class="text-warning fs-6 fw-bold mb-3">Status Anda saat ini ALFA. Sesi masih buka — presensi sekarang untuk mengubah status.</p>
                            <?php endif; ?>

                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.'?do_presensi=1'.(isset($active_step['sesi_id']) ? '&sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn w-100 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 border-0 animate__animated animate__bounce animate__infinite" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: white !important; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4) !important;">
                                PRESENSI SEKARANG <i class="fas fa-user-check ms-2"></i>
                            </a>
                        <?php else: ?>
                            <!-- SESI TERLEWAT TANPA STATUS PRESENSI (FALLBACK) -->
                            <div class="alert bg-secondary bg-opacity-30 rounded-4 shadow-sm p-4 text-center border border-secondary text-white mb-3">
                                <h5 class="fw-bold text-white-50 mb-0">Sesi ini sudah terlewat</h5>
                            </div>
                            <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.'?next_step='.$nextSessionStepId.(isset($active_step['sesi_id']) ? '&sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn w-100 py-3 rounded-pill fw-extrabold shadow-lg hover-scale fs-5 border-0 animate__animated animate__bounce animate__infinite" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: white !important; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4) !important;">
                                LANJUT KE SESI BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($active_step['tipe'] == 'materi' || $active_step['tipe'] == 'materi_segmen') : ?>
                <?php
                    $sesiPresensiStatus = $presensiStatusList[$active_step['sesi_id']] ?? null;
                    $isAlfaLocked = ($sesiPresensiStatus === 'Alfa');
                ?>
                <?php if ($isAlfaLocked): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-lock fa-5x text-white opacity-25 mb-4"></i>
                        <h3 class="fw-bold text-white mb-3">MATERI DITUTUP</h3>
                        <p class="text-white-50 fs-5 mb-0" style="max-width: 500px; margin: 0 auto;">Anda tidak dapat mengakses materi ini karena status kehadiran Anda pada sesi terkait tercatat <strong class="text-danger">ALFA</strong>. Silakan hubungi admin untuk informasi lebih lanjut.</p>
                    </div>
                <?php else: ?>
                    <div class="text-start mb-4" id="materiAccordion">
                        <?php foreach($active_step['materi_list'] as $index => $m): ?>
                            <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden animate__animated animate__fadeInUp" style="animation-delay: <?= $index * 0.1 ?>s;">
                                <!-- JUDUL MODUL (Header Accordion) -->
                                <button class="w-100 text-start border-0 p-4 d-flex justify-content-between align-items-center" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#materiCollapse<?= $index ?>" 
                                        aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" 
                                        aria-controls="materiCollapse<?= $index ?>"
                                        style="background: linear-gradient(to right, #f8fafc, #e2e8f0);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                            <i class="fas fa-book text-primary fs-4"></i>
                                        </div>
                                        <h4 class="fw-bold text-dark mb-0 fs-5"><?= esc($m['judul']) ?></h4>
                                    </div>
                                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center transition-transform materi-chevron shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-chevron-down text-primary"></i>
                                    </div>
                                </button>

                                <div id="materiCollapse<?= $index ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#materiAccordion">
                                    <div class="p-4 bg-white">
                                        <!-- DESKRIPSI MODUL -->
                                        <?php if (!empty($m['deskripsi'])): ?>
                                            <div class="mb-4 p-4 rounded-4" style="background-color: #fffbeb; border-left: 5px solid #fbbf24;">
                                                <h6 class="fw-bold text-warning-emphasis mb-2"><i class="fas fa-info-circle me-2"></i>Deskripsi Modul</h6>
                                                <div class="text-secondary lh-lg fs-6">
                                                    <?= $m['deskripsi'] ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- DOKUMEN MODUL -->
                                        <?php if(!empty($m['file_path'])): ?>
                                            <div class="rounded-4 overflow-hidden border border-secondary border-opacity-10 shadow-sm" style="background: #f1f5f9;">
                                                <div class="p-2">
                                                    <?php
                                                        $fileUrl = base_url($m['file_path']);
                                                        echo renderPelatihanFilePreview($m['file_path'], 'Materi :', $fileUrl);
                                                    ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <style>
                        .materi-chevron {
                            transition: transform 0.3s ease;
                        }
                        button[aria-expanded="true"] .materi-chevron {
                            transform: rotate(180deg);
                        }
                        button:focus {
                            outline: none;
                        }
                    </style>
                <?php endif; ?>

                <?php
                    $sessionOpen = isset($active_step['available']) ? (bool)$active_step['available'] : true;
                    $currentSesiPresensiStatus = isset($active_step['sesi_id']) ? ($presensiStatusList[$active_step['sesi_id']] ?? null) : null;
                    $isCurrentAlfaLocked = ($currentSesiPresensiStatus === 'Alfa');
                    $targetNextStep = $isCurrentAlfaLocked ? $nextSessionStepId : ($active_id + 1);
                ?>
                <div class="mt-2 text-center w-100">
                    <?php if ($isCurrentAlfaLocked || $sessionOpen): ?>
                        <a href="<?= base_url('pelatihan/peserta/tandai_selesai/'.$p['id'].'/'.$active_id.'?next_step='.$targetNextStep.(isset($active_step['sesi_id']) ? '&sesi_id='.$active_step['sesi_id'] : '')) ?>" class="btn w-100 py-3 rounded-pill fw-extrabold shadow-lg hover-scale fs-5 border-0 animate__animated animate__bounce animate__infinite" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: white !important; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4) !important;">
                            <?= $isCurrentAlfaLocked ? 'LANJUT KE SESI BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>' : 'SELESAI &amp; LANJUT <i class="fas fa-arrow-right ms-2"></i>' ?>
                        </a>
                    <?php else: ?>
                        <button class="btn w-100 py-3 rounded-pill fw-bold shadow-sm fs-5" style="background: #e2e8f0 !important; color: #94a3b8 !important; cursor: not-allowed; border: none;" disabled>
                            SELESAI &amp; LANJUT <i class="fas fa-lock ms-2"></i>
                        </button>
                    <?php endif; ?>
                </div>

            <?php elseif ($active_step['tipe'] == 'evaluasi_sesi') : ?>
                <?php
                    $sesiId = $active_step['sesi_id'];
                    $isSesiEvalSubmitted = in_array($sesiId, $submittedSesiEvaluations);
                    $materiForSesi = array_values(array_filter($materiList ?? [], fn($m) => (int)($m['sesi_id'] ?? 0) === (int)$sesiId));
                    $narasumberForSesi = array_values(array_filter($narasumberList ?? [], fn($n) => (int)($n['sesi_id'] ?? 0) === (int)$sesiId));
                    $penyelenggaraForSesi = array_values(array_filter($penyelenggaraList ?? [], fn($p) => (int)($p['sesi_id'] ?? 0) === (int)$sesiId));
                    $globalQuestions = $evalQuestions ?? [];
                ?>

                <div class="evaluasi-area py-2">
                    <?php if ($isSesiEvalSubmitted) : ?>
                        <div class="alert rounded-4 p-5 text-center shadow-lg mx-auto animate__animated animate__fadeInUp" style="max-width: 800px; background: #ffffff; border: 2px solid #e2e8f0 !important;">
                            <div class="bg-success bg-opacity-10 d-inline-block p-4 rounded-circle mb-4 animate__animated animate__bounceIn">
                                <i class="fas fa-clipboard-check fa-4x text-success"></i>
                            </div>
                            <h4 class="fw-bold mb-3 text-dark letter-spacing-1">Evaluasi Sesi Ini Sudah Dikirim</h4>
                            <p class="mb-5 text-muted fs-6">Anda telah menyelesaikan seluruh evaluasi untuk sesi ini. Terima kasih atas penilaian dan masukan Anda yang sangat berharga untuk kami.</p>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$nextSessionStepId) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 border-0 text-white animate__animated animate__pulse animate__infinite" style="background: #ce2127;">
                                LANJUT KE SESI BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    <?php else : ?>
                        <form id="evalSesiForm" action="<?= base_url('pelatihan/peserta/submit_evaluasi_sesi/'.$p['id']) ?>" method="POST" onsubmit="Swal.fire({title: 'Menyimpan Evaluasi...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});">
                            <input type="hidden" name="sesi_id" value="<?= $sesiId ?>">
                            <input type="hidden" name="step_id" value="<?= $active_id ?>">
                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-4 mb-4 p-4 rounded-4 border-start border-primary border-5 text-white" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15) !important; border-left-width: 5px !important;">
                                    <div class="bg-primary p-3 rounded-circle text-white shadow-sm">
                                        <i class="fas fa-book fa-2x"></i>
                                    </div>
                                    <div>
                                        <div class="text-white-50 small fw-bold text-uppercase">Evaluasi Materi & Modul Sesi Ini</div>
                                        <h4 class="fw-bold mb-0 text-white fs-4">Penilaian Materi & Modul Pembelajaran</h4>
                                    </div>
                                </div>

                                <?php
                                    $materiQs = [];
                                    foreach ($globalQuestions as $kat => $qs) {
                                        if (in_array(strtolower($kat), ['materi', 'modul'])) {
                                            $materiQs = array_merge($materiQs, $qs);
                                        }
                                    }
                                ?>
                                <?php if (empty($materiForSesi)): ?>
                                    <div class="alert alert-info">Belum ada materi yang terdaftar di sesi ini.</div>
                                <?php elseif (empty($materiQs)): ?>
                                    <div class="alert alert-warning">Belum ada pertanyaan evaluasi untuk kategori Materi/Modul.</div>
                                <?php else: ?>
                                    <?php foreach ($materiForSesi as $materi): ?>
                                        <div class="mb-4 p-4 rounded-4 shadow-sm text-white" style="background: rgba(0, 0, 0, 0.35); border: 2px solid rgba(255, 255, 255, 0.15) !important;">
                                            <h5 class="fw-bold text-white mb-3"><i class="fas fa-file-alt me-2 text-warning"></i> <?= esc($materi['judul']) ?></h5>
                                            <?php foreach ($materiQs as $q): ?>
                                                <div class="rating-row mb-4">
                                                    <label class="fw-bold mb-3 d-block text-white fs-5"><?= esc($q['pertanyaan']) ?></label>
                                                    <div class="rating-options">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <div class="rating-btn">
                                                                <input type="radio" name="rating_materi[<?= $materi['id'] ?>][<?= $q['id'] ?>]" id="rmateri_<?= $materi['id'] ?>_<?= $q['id'] ?>_<?= $i ?>" value="<?= $i ?>" required>
                                                                <label for="rmateri_<?= $materi['id'] ?>_<?= $q['id'] ?>_<?= $i ?>">
                                                                    <div class="fw-bold"><?= $i ?></div>
                                                                    <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                                    <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-4 mb-4 p-4 rounded-4 border-start border-success border-5 text-white" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15) !important; border-left-width: 5px !important;">
                                    <div class="bg-success p-3 rounded-circle text-white shadow-sm">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                    <div>
                                        <div class="text-white-50 small fw-bold text-uppercase">Evaluasi Narasumber</div>
                                        <h4 class="fw-bold mb-0 text-white fs-4">Penilaian Narasumber</h4>
                                    </div>
                                </div>
                                <?php
                                    $narasumberQs = [];
                                    foreach ($globalQuestions as $kat => $qs) {
                                        if (strtolower($kat) === 'narasumber') { $narasumberQs = $qs; break; }
                                    }
                                ?>
                                <?php if (empty($narasumberForSesi)): ?>
                                    <div class="alert alert-info">Belum ada narasumber yang terdaftar di sesi ini.</div>
                                <?php elseif (empty($narasumberQs)): ?>
                                    <div class="alert alert-warning">Belum ada pertanyaan evaluasi untuk kategori Narasumber.</div>
                                <?php else: ?>
                                    <?php foreach ($narasumberForSesi as $narasumber): ?>
                                        <div class="mb-4 p-4 rounded-4 shadow-sm text-white" style="background: rgba(0, 0, 0, 0.35); border: 2px solid rgba(255, 255, 255, 0.15) !important;">
                                            <h5 class="fw-bold text-white mb-3"><i class="fas fa-user me-2 text-success"></i> <?= esc(($narasumber['gelar_depan'] ? $narasumber['gelar_depan'].' ' : '').$narasumber['nama_pejabat'].($narasumber['gelar_belakang'] ? ', '.$narasumber['gelar_belakang'] : '')) ?></h5>
                                            <?php foreach ($narasumberQs as $q): ?>
                                                <div class="rating-row mb-4">
                                                    <label class="fw-bold mb-3 d-block text-white fs-5"><?= esc($q['pertanyaan']) ?></label>
                                                    <div class="rating-options">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <div class="rating-btn">
                                                                <input type="radio" name="rating_narasumber[<?= $narasumber['id'] ?>][<?= $q['id'] ?>]" id="rnar_sesi_<?= $narasumber['id'] ?>_<?= $q['id'] ?>_<?= $i ?>" value="<?= $i ?>" required>
                                                                <label for="rnar_sesi_<?= $narasumber['id'] ?>_<?= $q['id'] ?>_<?= $i ?>">
                                                                    <div class="fw-bold"><?= $i ?></div>
                                                                    <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                                    <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-4 mb-4 p-4 rounded-4 border-start border-warning border-5 text-white" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15) !important; border-left-width: 5px !important;">
                                    <div class="bg-warning p-3 rounded-circle text-dark shadow-sm">
                                        <i class="fas fa-users-cog fa-2x"></i>
                                    </div>
                                    <div>
                                        <div class="text-white-50 small fw-bold text-uppercase">Evaluasi Penyelenggara</div>
                                        <h4 class="fw-bold mb-0 text-white fs-4">Penilaian Penyelenggara</h4>
                                    </div>
                                </div>
                                <?php
                                    $penyelenggaraQs = [];
                                    foreach ($globalQuestions as $kat => $qs) {
                                        if (strtolower($kat) === 'penyelenggara') { $penyelenggaraQs = $qs; break; }
                                    }
                                ?>
                                <?php if (empty($penyelenggaraForSesi)): ?>
                                    <div class="alert alert-info">Belum ada penyelenggara yang terdaftar di sesi ini.</div>
                                <?php elseif (empty($penyelenggaraQs)): ?>
                                    <div class="alert alert-warning">Belum ada pertanyaan evaluasi untuk kategori Penyelenggara.</div>
                                <?php else: ?>
                                    <?php foreach ($penyelenggaraForSesi as $penyelenggara): ?>
                                        <div class="mb-4 p-4 rounded-4 shadow-sm text-white" style="background: rgba(0, 0, 0, 0.35); border: 2px solid rgba(255, 255, 255, 0.15) !important;">
                                            <h5 class="fw-bold text-white mb-3"><i class="fas fa-building me-2 text-warning"></i> <?= esc($penyelenggara['nama'] ?? '') ?></h5>
                                            <?php foreach ($penyelenggaraQs as $q): ?>
                                                <div class="rating-row mb-4">
                                                    <label class="fw-bold mb-3 d-block text-white fs-5"><?= esc($q['pertanyaan']) ?></label>
                                                    <div class="rating-options">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <div class="rating-btn">
                                                                <input type="radio" name="rating_penyelenggara[<?= $penyelenggara['id'] ?>][<?= $q['id'] ?>]" id="rpen_sesi_<?= $penyelenggara['id'] ?>_<?= $q['id'] ?>_<?= $i ?>" value="<?= $i ?>" required>
                                                                <label for="rpen_sesi_<?= $penyelenggara['id'] ?>_<?= $q['id'] ?>_<?= $i ?>">
                                                                    <div class="fw-bold"><?= $i ?></div>
                                                                    <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                                    <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <?php
                                $fasilQs = [];
                                foreach ($globalQuestions as $kat => $qs) {
                                    if (strtolower($kat) === 'fasilitator') { $fasilQs = $qs; break; }
                                }
                            ?>
                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-4 mb-4 p-4 rounded-4 border-start border-dark border-5 text-white" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15) !important; border-left-width: 5px !important;">
                                    <div class="bg-dark p-3 rounded-circle text-white shadow-sm">
                                        <i class="fas fa-user-tie fa-2x"></i>
                                    </div>
                                    <div>
                                        <div class="text-white-50 small fw-bold text-uppercase">Evaluasi Fasilitator</div>
                                        <h4 class="fw-bold mb-0 text-white fs-4">Fasilitator Sesi: <?= esc($active_step['judul']) ?></h4>
                                    </div>
                                </div>
                                <?php if (empty($fasilQs)): ?>
                                    <div class="alert alert-warning">Belum ada pertanyaan evaluasi untuk kategori Fasilitator.</div>
                                <?php else: ?>
                                    <div class="mb-4 p-4 rounded-4 shadow-sm text-white" style="background: rgba(0, 0, 0, 0.35); border: 2px solid rgba(255, 255, 255, 0.15) !important;">
                                        <?php foreach ($fasilQs as $q): ?>
                                            <div class="rating-row mb-4">
                                                <label class="fw-bold mb-3 d-block text-white fs-5"><?= esc($q['pertanyaan']) ?></label>
                                                <div class="rating-options">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <div class="rating-btn">
                                                            <input type="radio" name="rating_fasilitator[<?= $sesiId ?>][<?= $q['id'] ?>]" id="q_fasil_sesi_<?= $sesiId ?>_<?= $q['id'] ?>_<?= $i ?>" value="<?= $i ?>" required>
                                                            <label for="q_fasil_sesi_<?= $sesiId ?>_<?= $q['id'] ?>_<?= $i ?>">
                                                                <div class="fw-bold"><?= $i ?></div>
                                                                <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                                <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                                            </label>
                                                        </div>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="w-100 mt-4">
                                <button type="submit" class="btn w-100 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-4 border-0" style="background: #ce2127 !important; color: white !important;">KIRIM EVALUASI SESI</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

            <?php elseif ($active_step['tipe'] == 'pre_test' || $active_step['tipe'] == 'post_test') : ?>
                <?php if ($active_step['tipe'] == 'pre_test' && !empty($pre_test_attempted)) : ?>
                    <div class="alert rounded-4 p-5 text-center shadow-lg mx-auto" style="max-width: 800px; background: #ffffff; border: 2px solid #e2e8f0 !important;">
                        <div class="bg-success bg-opacity-10 d-inline-block p-4 rounded-circle mb-3 animate__animated animate__bounceIn">
                            <i class="fas fa-check-circle fa-4x text-success"></i>
                        </div>
                        <h4 class="fw-bold mb-4 text-dark letter-spacing-1">Pre-Test Berhasil Diselesaikan</h4>
                        <p class="mb-4 text-muted fs-6">Terima kasih, Anda telah mengerjakan Pre-Test. Pre-Test hanya dapat dikerjakan satu kali untuk mengukur kemampuan awal Anda sebelum mengikuti pelatihan.</p>
                        
                        <div class="row g-3 justify-content-center mb-5">
                            <div class="col-sm-4">
                                <div class="p-3 rounded-4 shadow-sm h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="text-muted small fw-bold mb-2 text-uppercase">Jawaban Benar</div>
                                    <div class="fs-2 fw-extrabold text-success"><i class="fas fa-check text-success opacity-50 me-2"></i><?= $pre_test_benar ?? 0 ?></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 rounded-4 shadow-sm h-100" style="background: #fff1f2; border: 1px solid #fecdd3;">
                                    <div class="text-danger small fw-bold mb-2 text-uppercase">Jawaban Salah</div>
                                    <div class="fs-2 fw-extrabold text-danger"><i class="fas fa-times text-danger opacity-50 me-2"></i><?= $pre_test_salah ?? 0 ?></div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 rounded-4 shadow-sm h-100 position-relative overflow-hidden" style="background: var(--primary-red); border: 1px solid var(--primary-red);">
                                    <i class="fas fa-star position-absolute text-white opacity-10" style="font-size: 5rem; right: -15px; bottom: -15px;"></i>
                                    <div class="text-white-50 small fw-bold mb-2 text-uppercase position-relative z-1">Skor Akhir</div>
                                    <div class="fs-2 fw-extrabold text-white position-relative z-1"><?= number_format($pre_test_score ?? 0, 2) ?></div>
                                </div>
                            </div>
                        </div>

                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 animate__animated animate__pulse animate__infinite border-0 text-white" style="background: #ce2127;">
                            LANJUT BELAJAR <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                <?php elseif ($active_step['tipe'] == 'post_test' && $post_test_status == 'Lulus' && !isset($_GET['retake'])) : ?>
                    <div class="alert rounded-4 p-5 text-center shadow-lg mx-auto" style="max-width: 900px; background: #ffffff; border: 2px solid #e2e8f0 !important;">
                        <h4 class="fw-bold mb-4 text-dark letter-spacing-1">Post-Test Berhasil Diselesaikan</h4>
                        
                        <div class="row g-3 justify-content-center mb-4">
                            <div class="col-sm-6">
                                <div class="p-4 rounded-4 shadow-sm h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="text-muted small fw-bold mb-2 text-uppercase">Skor Pre-Test</div>
                                    <div class="fs-1 fw-extrabold text-secondary"><?= number_format($pre_test_score ?? 0, 2) ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-4 rounded-4 shadow-sm h-100" style="background: var(--primary-red); border: 1px solid var(--primary-red);">
                                    <div class="text-white-50 small fw-bold mb-2 text-uppercase">Skor Post-Test</div>
                                    <div class="fs-1 fw-extrabold text-white"><?= number_format($post_test_score ?? 0, 2) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 justify-content-center mb-4">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 shadow-sm h-100" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                    <div class="text-success small fw-bold mb-2 text-uppercase">Jawaban Benar</div>
                                    <div class="fs-3 fw-extrabold text-success"><?= $post_test_benar ?? 0 ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 shadow-sm h-100" style="background: #fff1f2; border: 1px solid #fecdd3;">
                                    <div class="text-danger small fw-bold mb-2 text-uppercase">Jawaban Salah</div>
                                    <div class="fs-3 fw-extrabold text-danger"><?= $post_test_salah ?? 0 ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-4 shadow-sm mb-5 text-start" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <h6 class="fw-bold text-dark mb-3">Ringkasan Kehadiran Sesi</h6>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-secondary fw-semibold">Sesi Diikuti</span>
                                <span class="text-dark fw-bold"><?= $stats_sesi_hadir ?? 0 ?> / <?= $stats_sesi_total ?? 0 ?> Sesi</span>
                            </div>
                            <?php if (!empty($materi_alfa)): ?>
                                <div class="text-danger small fw-bold mb-2 text-uppercase">Materi Sesi Yang Tidak Diikuti (Alfa)</div>
                                <ul class="text-secondary small mb-0 ps-3">
                                    <?php foreach ($materi_alfa as $ma): ?>
                                        <li><?= esc($ma) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-success small fw-bold mb-0">Luar biasa! Anda mengikuti seluruh sesi pelatihan.</div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-3 flex-wrap justify-content-center">
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 border-0 text-white animate__animated animate__pulse animate__infinite" style="background: #ce2127;">
                                SELESAI & LANJUT KE EVALUASI
                            </a>
                            <?php $sisa = 3 - ($post_test_attempts ?? 0); if ($sisa > 0): ?>
                                <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$active_id.'&retake=1') ?>" class="btn btn-outline-success px-4 py-3 rounded-pill fw-bold border-2">
                                    KERJAKAN ULANG (SISA <?= $sisa ?>x)
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($active_step['tipe'] == 'post_test' && !empty($post_test_attempts) && $post_test_attempts >= 3) : ?>
                    <div class="alert rounded-4 p-5 text-center shadow-lg mx-auto" style="max-width: 900px; background: #ffffff; border: 2px solid #e2e8f0 !important;">
                        <h4 class="fw-bold mb-3 text-danger letter-spacing-1">Batas Post-Test Telah Tercapai</h4>
                        <p class="mb-5 text-dark fs-6">Anda telah mencoba Post-Test sebanyak 3 kali. Kesempatan pengerjaan ulang Anda telah habis dan Anda tidak dapat mengulanginya lagi.</p>
                        
                        <div class="row g-3 justify-content-center mb-4">
                            <div class="col-sm-6">
                                <div class="p-4 rounded-4 shadow-sm h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="text-muted small fw-bold mb-2 text-uppercase">Skor Pre-Test</div>
                                    <div class="fs-1 fw-extrabold text-secondary"><?= number_format($pre_test_score ?? 0, 2) ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-4 rounded-4 shadow-sm h-100" style="background: var(--primary-red); border: 1px solid var(--primary-red);">
                                    <div class="text-white-50 small fw-bold mb-2 text-uppercase">Skor Terakhir Post-Test</div>
                                    <div class="fs-1 fw-extrabold text-white"><?= number_format($post_test_score ?? 0, 2) ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 justify-content-center mb-4">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 shadow-sm h-100" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                    <div class="text-success small fw-bold mb-2 text-uppercase">Jawaban Benar</div>
                                    <div class="fs-3 fw-extrabold text-success"><?= $post_test_benar ?? 0 ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-4 shadow-sm h-100" style="background: #fff1f2; border: 1px solid #fecdd3;">
                                    <div class="text-danger small fw-bold mb-2 text-uppercase">Jawaban Salah</div>
                                    <div class="fs-3 fw-extrabold text-danger"><?= $post_test_salah ?? 0 ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-4 shadow-sm mb-5 text-start" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <h6 class="fw-bold text-dark mb-3">Ringkasan Kehadiran Sesi</h6>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-secondary fw-semibold">Sesi Diikuti</span>
                                <span class="text-dark fw-bold"><?= $stats_sesi_hadir ?? 0 ?> / <?= $stats_sesi_total ?? 0 ?> Sesi</span>
                            </div>
                            <?php if (!empty($materi_alfa)): ?>
                                <div class="text-danger small fw-bold mb-2 text-uppercase">Materi Sesi Yang Tidak Diikuti (Alfa)</div>
                                <ul class="text-secondary small mb-0 ps-3">
                                    <?php foreach ($materi_alfa as $ma): ?>
                                        <li><?= esc($ma) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-success small fw-bold mb-0">Luar biasa! Anda mengikuti seluruh sesi pelatihan.</div>
                            <?php endif; ?>
                        </div>

                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 border-0 text-white animate__animated animate__pulse animate__infinite" style="background: #ce2127;">
                            LANJUT KE EVALUASI
                        </a>
                    </div>
                <?php elseif (isset($_GET['error']) && $_GET['error'] == 'score_low') :
                    $lastScore = $_GET['last_score'] ?? 0;
                    $attempts = $_GET['attempts'] ?? 1;
                    $sisa_percobaan = 3 - $attempts;
                ?>
                    <div class="alert text-center p-5 rounded-4 mb-4 mx-auto shadow-lg" style="max-width: 800px; margin-top: 30px; background: #ffffff; border: 2px solid #e2e8f0 !important;">
                        <h3 class="fw-extrabold text-danger mb-4"><i class="fas fa-exclamation-triangle me-2 text-warning animate__animated animate__flash animate__infinite"></i> Nilai Belum Memenuhi KKM</h3>
                        
                        <div class="row g-3 justify-content-center mb-4">
                            <div class="col-sm-5">
                                <div class="p-3 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="text-muted small fw-bold mb-1">SKOR PRE-TEST</div>
                                    <div class="fs-2 fw-extrabold text-primary"><?= number_format($pre_test_score ?? 0, 2) ?></div>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="p-3 rounded-4" style="background: #fff1f2; border: 1px solid #fecdd3;">
                                    <div class="text-danger small fw-bold mb-1">SKOR POST-TEST SAAT INI</div>
                                    <div class="fs-2 fw-extrabold text-danger animate__animated animate__shakeX"><?= number_format($lastScore, 2) ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <p class="mb-4 text-dark fs-5">Skor minimal kelulusan adalah <strong class="text-danger"><?= $post_test_kkm ?? 70 ?></strong>.</p>
                        
                        <?php if ($sisa_percobaan > 0): ?>
                            <p class="mb-2 fw-bold text-dark fs-6">Apakah Anda ingin mengerjakan ulang Post-Test?</p>
                            <p class="mb-4 text-muted">Sisa kuota pengerjaan Anda: <strong class="text-danger"><?= $sisa_percobaan ?></strong> kali</p>
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$active_id.'&retake=1') ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 border-0 text-white" style="background: #10b981;">
                                    <i class="fas fa-redo-alt me-2"></i> KERJAKAN ULANG SEKARANG
                                </a>
                                <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn btn-outline-danger px-5 py-3 rounded-pill fw-bold hover-scale fs-5" style="border-width: 2px;">
                                    LANJUT KE EVALUASI <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <p class="mb-4 text-danger fw-bold fs-5"><i class="fas fa-lock me-2"></i> Kesempatan pengerjaan ulang Anda telah habis.</p>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.($active_id + 1)) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 border-0 text-white" style="background: #ce2127;">
                                LANJUT KE EVALUASI <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                <div class="test-area py-4" id="quizContainer">
                    <div class="row g-4">
                        <div class="col-lg-9">
                            <div class="d-flex justify-content-between align-items-center mb-5 p-3 rounded-pill" style="background: #ffffff; border: 1px solid #e2e8f0 !important; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                <div class="flex-grow-1 mx-4">
                                    <div class="progress" style="height: 12px; border-radius: 20px; background: #f1f5f9;">
                                        <div class="progress-bar shadow-sm" id="quizProgress" style="width: 20%; background-color: #ce2127;"></div>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark me-3" id="quizCounter">SOAL 1/5</span>
                            </div>
                            
                            <div id="questionArea">
                                <!-- Questions will be injected here via JS -->
                            </div>
                            
                            <div class="d-flex justify-content-between mt-5">
                                <button onclick="prevQuestion()" id="btnPrev" class="btn btn-outline-light px-5 py-3 rounded-pill fw-bold invisible" style="min-width: 220px; border-width: 2px;">
                                    <i class="fas fa-arrow-left me-2"></i> SEBELUMNYA
                                </button>
                                <button onclick="nextQuestionQuiz()" id="btnNext" class="btn shadow-lg px-5 py-3 rounded-pill fw-bold border-0" style="background-color: #ffffff; color: #ce2127; min-width: 220px; transition: all 0.3s ease;">
                                    BERIKUTNYA <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card border-0 shadow-lg rounded-4 sticky-top" style="top: 100px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-4 text-center text-dark">Navigasi Soal</h6>
                                    <div class="d-flex flex-wrap gap-2 justify-content-center" id="quizNavGrid">
                                        <!-- Boxes injected via JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    <?php
                        $quizDataJson = "[]";
                        if ($active_step['tipe'] == 'pre_test') {
                            $mapped = array_map(function($q) {
                                $kunci = strtolower(trim($q['jawaban_benar']));
                                return [
                                    'id' => $q['id'],
                                    'q' => $q['pertanyaan'],
                                    'a' => [$q['opsi_a'], $q['opsi_b'], $q['opsi_c'], $q['opsi_d']],
                                    'c' => $kunci == 'a' ? 0 : ($kunci == 'b' ? 1 : ($kunci == 'c' ? 2 : 3)),
                                    'f' => $q['file_path'] ?? null
                                ];
                            }, $preTestQuestions ?? []);
                            $quizDataJson = json_encode($mapped);
                        } elseif ($active_step['tipe'] == 'post_test') {
                            $mapped = array_map(function($q) {
                                $kunci = strtolower(trim($q['jawaban_benar']));
                                return [
                                    'id' => $q['id'],
                                    'q' => $q['pertanyaan'],
                                    'a' => [$q['opsi_a'], $q['opsi_b'], $q['opsi_c'], $q['opsi_d']],
                                    'c' => $kunci == 'a' ? 0 : ($kunci == 'b' ? 1 : ($kunci == 'c' ? 2 : 3)),
                                    'f' => $q['file_path'] ?? null
                                ];
                            }, $postTestQuestions ?? []);
                            $quizDataJson = json_encode($mapped);
                        }
                    ?>
                    const quizData = <?= $quizDataJson ?>;
                    const quizStorageKey = 'quiz_<?= $p['id'] ?>_<?= $active_step['tipe'] ?? '' ?>';

                    let currentQ = 0;
                    let savedIdx = sessionStorage.getItem(quizStorageKey + '_idx');
                    if (savedIdx) {
                        let parsedIdx = parseInt(savedIdx);
                        if (!isNaN(parsedIdx) && parsedIdx >= 0 && parsedIdx < quizData.length) {
                            currentQ = parsedIdx;
                        }
                    }

                    let answers = new Array(quizData.length).fill(null);
                    
                    let savedQuiz = sessionStorage.getItem(quizStorageKey);
                    if (savedQuiz) {
                        let parsedQuiz = JSON.parse(savedQuiz);
                        if (parsedQuiz.length === answers.length) {
                            answers = parsedQuiz;
                        }
                    }

                    function buildAttachmentPreview(filePath, title) {
                        const ext = (filePath || '').split('.').pop().toLowerCase();
                        const url = '<?= base_url() ?>' + filePath;
                        const fileName = (filePath || '').split('/').pop();
                        const displayTitle = title || fileName || 'Lampiran Soal';

                        if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                            return `<div class="mb-4 text-center"><img src="${url}" class="img-fluid rounded shadow" style="max-height: 400px;" alt="${displayTitle}"></div>`;
                        }

                        if (['mp4','webm','ogg'].includes(ext)) {
                            return `<div class="mb-4 text-center"><video controls class="w-100 rounded shadow" style="max-height: 400px;"><source src="${url}" type="video/${ext}">Browser Anda tidak mendukung pemutaran video.</video></div>`;
                        }

                        if (ext === 'pdf') {
                            return `
                                <div class="mb-4 document-preview-shell shadow-sm position-relative">
                                    <button type="button" class="btn btn-dark btn-sm position-absolute" style="top: 12px; right: 15px; z-index: 10;" onclick="toggleFullscreen(this.parentElement)">
                                        <i class="fas fa-expand"></i> Full View
                                    </button>
                                    <div class="document-preview-head">
                                        <span>${displayTitle}</span>
                                    </div>
                                    <iframe src="${url}" class="inline-document-preview" style="min-height: 500px;" title="${displayTitle}"></iframe>
                                </div>`;
                        }

                        if (['doc','docx','xls','xlsx','ppt','pptx'].includes(ext)) {
                            const label = ['xls','xlsx'].includes(ext) ? 'Excel' : (['ppt','pptx'].includes(ext) ? 'PowerPoint' : 'Word');
                            return `
                                <div class="mb-4 document-preview-shell shadow-sm position-relative">
                                    <button type="button" class="btn btn-dark btn-sm position-absolute" style="top: 12px; right: 15px; z-index: 10;" onclick="toggleFullscreen(this.parentElement)">
                                        <i class="fas fa-expand"></i> Full View
                                    </button>
                                    <div class="document-preview-head">
                                        <span>${displayTitle}</span>
                                        <a href="${url}" target="_blank" rel="noopener">Unduh file</a>
                                    </div>
                                    <div class="p-4 text-center bg-light" style="min-height: 220px;">
                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                        <h6 class="fw-bold text-dark">Preview ${label} tidak tersedia di halaman ini</h6>
                                        <p class="text-muted small mb-0">File materi ini tidak dapat dibuka langsung dari ruang belajar.</p>
                                    </div>
                                </div>`;
                        }

                        if (['txt','csv'].includes(ext)) {
                            return `
                                <div class="mb-4 document-preview-shell shadow-sm position-relative">
                                    <button type="button" class="btn btn-dark btn-sm position-absolute" style="top: 12px; right: 15px; z-index: 10;" onclick="toggleFullscreen(this.parentElement)">
                                        <i class="fas fa-expand"></i> Full View
                                    </button>
                                    <div class="document-preview-head">
                                        <span>${displayTitle}</span>
                                        <a href="${url}" target="_blank" rel="noopener">Buka tab baru</a>
                                    </div>
                                    <iframe src="${url}" class="inline-document-preview" style="min-height: 500px;" title="${displayTitle}"></iframe>
                                </div>`;
                        }

                        return `<div class="mb-4 text-center p-3 bg-light rounded fw-bold">File pendukung: <a href="${url}" target="_blank" class="text-danger text-decoration-none">${fileName}</a></div>`;
                    }

                    function renderNavGrid() {
                        const navGrid = document.getElementById('quizNavGrid');
                        if (navGrid) {
                            navGrid.innerHTML = quizData.map((_, i) => `
                                <button onclick="gotoQuestion(${i})" class="btn fw-bold p-0 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px; border-width: 2px; ${currentQ === i ? 'background: #ce2127; border-color: #ce2127; color: white !important;' : (answers[i] !== null ? 'background: #10b981; border-color: #10b981; color: white !important;' : 'background: #ffffff; border-color: #cbd5e1; color: #1e293b !important;')}" >
                                    ${i + 1}
                                </button>
                            `).join('');
                        }
                    }

                    function gotoQuestion(idx) {
                        currentQ = idx;
                        renderQuestion();
                    }

                    function renderQuestion() {
                        const q = quizData[currentQ];
                        const area = document.getElementById('questionArea');
                        sessionStorage.setItem(quizStorageKey + '_idx', currentQ);
                        
                        let mediaHtml = '';
                        if (q.f) {
                            mediaHtml = buildAttachmentPreview(q.f, q.q || 'Lampiran Soal');
                        }

                        area.innerHTML = `
                            <div class="question-card">
                                ${mediaHtml}
                                <h5 class="fw-bold mb-4" style="color: #ffffff !important; font-size: 1.3rem !important;">${q.q}</h5>
                                <div class="d-flex flex-column gap-3">
                                    ${q.a.map((opt, i) => `
                                        <div class="quiz-option ${answers[currentQ] === i ? 'selected' : ''}" onclick="selectOption(${i})">
                                            <div class="quiz-option-circle"></div>
                                            <span class="fw-medium">${opt}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;

                        document.getElementById('quizProgress').style.width = ((currentQ + 1) / quizData.length * 100) + '%';
                        document.getElementById('quizCounter').innerText = `Soal ${currentQ + 1}/${quizData.length}`;
                        document.getElementById('btnPrev').classList.toggle('invisible', currentQ === 0);
                        document.getElementById('btnNext').innerHTML = currentQ === quizData.length - 1 ? 
                            'Selesai & Kirim' : 
                            'Berikutnya <i class="fas fa-arrow-right ms-2"></i>';
                        
                        // Apply brand red style if not finished, green if finished
                        if (currentQ < quizData.length - 1) {
                            document.getElementById('btnNext').style.backgroundColor = '#ffffff';
                            document.getElementById('btnNext').style.color = '#ce2127';
                        } else {
                            document.getElementById('btnNext').style.backgroundColor = '#10b981';
                            document.getElementById('btnNext').style.color = '#ffffff';
                        }
                        renderNavGrid();
                    }

                    function selectOption(idx) {
                        answers[currentQ] = idx;
                        sessionStorage.setItem(quizStorageKey, JSON.stringify(answers));
                        const options = document.querySelectorAll('#questionArea .quiz-option');
                        options.forEach((opt, i) => {
                            if (i === idx) {
                                opt.classList.add('selected');
                            } else {
                                opt.classList.remove('selected');
                            }
                        });
                        renderNavGrid();
                    }

                    function prevQuestion() {
                        if (currentQ > 0) {
                            currentQ--;
                            renderQuestion();
                        }
                    }

                    function nextQuestionQuiz() {
                        if (answers[currentQ] === null) {
                            Swal.fire({ icon: 'warning', title: 'Belum Menjawab', text: 'Silakan pilih salah satu jawaban terlebih dahulu!', confirmButtonColor: '#1a202c' });
                            return;
                        }

                        if (currentQ < quizData.length - 1) {
                            currentQ++;
                            renderQuestion();
                        } else {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '<?= base_url('pelatihan/peserta/submit_kuis/' . $p['id']) ?>';
                            
                            const stepInput = document.createElement('input');
                            stepInput.type = 'hidden';
                            stepInput.name = 'step_id';
                            stepInput.value = '<?= $active_id ?>';
                            form.appendChild(stepInput);
                            
                            const tipeInput = document.createElement('input');
                            tipeInput.type = 'hidden';
                            tipeInput.name = 'tipe_ujian';
                            tipeInput.value = '<?= $active_step['tipe'] ?>';
                            form.appendChild(tipeInput);
                            
                            const letters = ['A', 'B', 'C', 'D'];
                            const answerData = answers.map((ansIdx, i) => {
                                return {
                                    soal_id: quizData[i].id,
                                    jawaban: letters[ansIdx]
                                };
                            });
                            
                            const answersInput = document.createElement('input');
                            answersInput.type = 'hidden';
                            answersInput.name = 'answers';
                            answersInput.value = JSON.stringify(answerData);
                            form.appendChild(answersInput);
                            
                            sessionStorage.removeItem(quizStorageKey);
                            sessionStorage.removeItem(quizStorageKey + '_idx');
                            
                            document.body.appendChild(form);
                            Swal.fire({
                                title: 'Memproses Jawaban...',
                                text: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            form.submit();
                        }
                    }

                    renderQuestion();
                </script>
                <?php endif; ?>
            <?php elseif ($active_step['tipe'] == 'evaluasi') : ?>
                <?php 
                    $post_test_completed = $postTestIndex ? (in_array($postTestIndex, $completed_steps) || (isset($post_test_attempts) && $post_test_attempts > 0)) : true;
                ?>

                <div class="evaluasi-area py-2">
                    <?php if (!$post_test_completed) : ?>
                        <div class="text-center py-5">
                            <i class="fas fa-lock fa-4x text-muted mb-3"></i>
                            <h5 class="fw-bold">Evaluasi Belum Terbuka</h5>
                            <p class="text-muted">Anda harus menyelesaikan <strong>Post-Test</strong> terlebih dahulu untuk dapat mengisi evaluasi ini.</p>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$postTestIndex) ?>" class="btn btn-selanjutnya mt-3">Pergi ke Post-Test</a>
                        </div>
                    <?php elseif ($ratingAlreadySubmitted) : ?>
                        <div class="alert rounded-4 p-5 text-center shadow-lg mx-auto animate__animated animate__fadeInUp" style="max-width: 800px; background: #ffffff; border: 2px solid #e2e8f0 !important;">
                            <h4 class="fw-bold mb-3 text-dark letter-spacing-1">Evaluasi Akhir Sudah Dikirim</h4>
                            <p class="mb-5 text-muted fs-6">Anda telah menyelesaikan seluruh rangkaian evaluasi pelatihan ini. Terima kasih atas penilaian dan saran Anda, masukan Anda sangat berharga untuk meningkatkan kualitas pelatihan kami ke depannya.</p>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id'].'?step='.$certIndex) ?>" class="btn px-5 py-3 rounded-pill fw-bold shadow-lg hover-scale fs-5 border-0 text-white animate__animated animate__pulse animate__infinite" style="background: #ce2127;">
                                LIHAT SERTIFIKAT SEKARANG
                            </a>
                        </div>
                    <?php else : ?>
                        <form id="evaluationForm" action="<?= base_url('pelatihan/peserta/submit_evaluasi/'.$p['id']) ?>" method="POST" onsubmit="Swal.fire({title: 'Menyimpan Evaluasi...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});">

                            <div class="d-flex align-items-center gap-4 mb-5 p-4 bg-light rounded-4 border-start border-danger border-5">
                                <div class="p-3 rounded-circle text-white shadow-sm" style="background: var(--primary-red);">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-1 text-uppercase text-dark">Rating &amp; Saran Keseluruhan</h4>
                                    <p class="text-muted small mb-0 fw-bold">Berikan penilaian akhir dan masukan Anda</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold mb-3 d-block">Rating Keseluruhan Pelatihan (1-5):</label>
                                <div class="rating-options" style="justify-content: flex-start;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <div class="rating-btn">
                                            <input type="radio" name="rating_umum" id="rumum_<?= $i ?>" value="<?= $i ?>" required>
                                            <label for="rumum_<?= $i ?>">
                                                <div class="fw-bold"><?= $i ?></div>
                                                <?php if ($i == 1): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Kurang</div><?php endif; ?>
                                                <?php if ($i == 5): ?><div style="font-size:0.65rem;margin-top:4px;line-height:1;">Sangat Baik</div><?php endif; ?>
                                            </label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="fw-bold mb-2">Saran &amp; Masukan Tambahan:</label>
                                <textarea name="saran" class="form-control border-0 bg-light p-3 rounded-4" rows="5" placeholder="Ketik saran atau kritik konstruktif Anda di sini..."></textarea>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <button type="submit" class="btn py-3 flex-grow-2 rounded-pill fw-bold shadow-lg w-100 fs-5" style="background: #ce2127 !important; color: white !important;">KIRIM &amp; SELESAIKAN</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

            <?php elseif ($active_step['tipe'] == 'sertifikat') : ?>
                <div class="text-center py-4">
                    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
                    <?php if (empty($sertifikat)) : ?>
                        <div class="success-portal-card p-5 mb-5 mx-auto animate__animated animate__fadeInUp" style="max-width: 800px; background: white; border-radius: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); position: relative; overflow: hidden; border: 1px solid #f1f5f9;">
                            <!-- Decorative Background Elements -->
                            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255, 193, 7, 0.05); border-radius: 50%; z-index: 0;"></div>
                            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(15, 23, 42, 0.05); border-radius: 50%; z-index: 0;"></div>

                            <div class="position-relative" style="z-index: 1;">
                                <div class="mb-5 text-center">
                                    <?php if (isset($post_test_status) && $post_test_status == 'Lulus'): ?>
                                        <div class="bg-warning bg-opacity-10 d-inline-block p-4 rounded-circle mb-4 animate__animated animate__bounceIn">
                                            <i class="fas fa-hourglass-half fa-4x text-warning"></i>
                                        </div>
                                        <h2 class="display-6 fw-bold text-dark mb-2 letter-spacing-1">SERTIFIKAT DALAM PROSES</h2>
                                        <div class="badge bg-warning bg-opacity-25 text-dark rounded-pill px-4 py-2 mt-2 fw-bold border border-warning border-opacity-50" style="font-size: 0.9rem;"><i class="fas fa-spinner fa-spin me-2"></i> Sedang Menerbitkan...</div>
                                    <?php else: ?>
                                        <div class="bg-danger bg-opacity-10 d-inline-block p-4 rounded-circle mb-4 animate__animated animate__bounceIn">
                                            <i class="fas fa-times-circle fa-4x text-danger"></i>
                                        </div>
                                        <h2 class="display-6 fw-bold text-dark mb-2 letter-spacing-1">PELATIHAN SELESAI</h2>
                                        <div class="badge bg-danger bg-opacity-25 text-dark rounded-pill px-4 py-2 mt-2 fw-bold border border-danger border-opacity-50" style="font-size: 0.9rem;"><i class="fas fa-info-circle me-2"></i> Sertifikat Tidak Tersedia</div>
                                    <?php endif; ?>
                                </div>

                                <div class="p-4 mb-4 rounded-4 text-start shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0; position: relative;">
                                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, var(--primary-red), #f59e0b); border-radius: 4px 4px 0 0;"></div>
                                    <h5 class="fw-bold text-dark mb-3">Detail Penyelesaian Pelatihan</h5>
                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3" style="background: #fff; border: 1px solid #e2e8f0;">
                                                <div class="small text-muted fw-bold text-uppercase mb-1">Status Kelulusan</div>
                                                <?php if (isset($post_test_status) && $post_test_status == 'Lulus'): ?>
                                                    <div class="fw-bold text-success fs-5"><i class="fas fa-check-circle me-2"></i> LULUS</div>
                                                <?php else: ?>
                                                    <div class="fw-bold text-danger fs-5"><i class="fas fa-times-circle me-2"></i> TIDAK LULUS</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-3" style="background: #fff; border: 1px solid #e2e8f0;">
                                                <div class="small text-muted fw-bold text-uppercase mb-1">Nilai Akhir (Post-Test)</div>
                                                <div class="fw-bold text-dark fs-5"><i class="fas fa-star text-warning me-2"></i> <?= number_format($post_test_score ?? 0, 2) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 rounded-3 mb-3" style="background: #fff; border: 1px solid #e2e8f0;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="small text-muted fw-bold text-uppercase">Progress Keseluruhan</div>
                                            <div class="small fw-bold text-dark"><?= count($completed_steps) ?>/<?= count($konten) ?> Selesai (<?= round((count($completed_steps) / count($konten)) * 100) ?>%)</div>
                                        </div>
                                        <div class="progress" style="height: 8px; border-radius: 4px; background: #e2e8f0;">
                                            <div class="progress-bar" style="width: <?= (count($completed_steps) / count($konten)) * 100 ?>%; background: var(--primary-red);"></div>
                                        </div>
                                    </div>
                                    
                                    <?php
                                        $missed_sesi = [];
                                        $missed_materi = [];
                                        foreach ($konten as $k) {
                                            // The active step 'sertifikat' is currently not marked completed yet, but we shouldn't show it as missed material/sesi. 
                                            // Since we filter by 'presensi', 'sesi', 'materi', 'materi_segmen', it's safe.
                                            if (!in_array($k['id'], $completed_steps)) {
                                                if (in_array($k['tipe'], ['presensi', 'sesi'])) {
                                                    $missed_sesi[] = $k['judul'];
                                                } elseif (in_array($k['tipe'], ['materi', 'materi_segmen'])) {
                                                    $missed_materi[] = $k['judul'];
                                                }
                                            }
                                        }
                                    ?>
                                    
                                    <?php if (!empty($missed_sesi) || !empty($missed_materi)): ?>
                                        <div class="p-3 rounded-3" style="background: #fff; border: 1px dashed #cbd5e1;">
                                            <h6 class="fw-bold text-danger mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Bagian yang Terlewat</h6>
                                            <?php if (!empty($missed_sesi)): ?>
                                                <div class="mb-2">
                                                    <div class="small fw-bold text-muted mb-1">Sesi:</div>
                                                    <ul class="mb-0 ps-3 text-dark small">
                                                        <?php foreach ($missed_sesi as $ms): ?>
                                                            <li><?= esc($ms) ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($missed_materi)): ?>
                                                <div>
                                                    <div class="small fw-bold text-muted mb-1">Materi:</div>
                                                    <ul class="mb-0 ps-3 text-dark small">
                                                        <?php foreach ($missed_materi as $mm): ?>
                                                            <li><?= esc($mm) ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="p-3 rounded-3 text-center" style="background: rgba(16, 185, 129, 0.1); border: 1px dashed rgba(16, 185, 129, 0.5);">
                                            <div class="fw-bold text-success small"><i class="fas fa-check-double me-2"></i> Luar biasa! Anda tidak melewatkan sesi atau materi apa pun.</div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="p-4 mb-5 rounded-4 text-center shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <?php if (isset($post_test_status) && $post_test_status == 'Lulus'): ?>
                                        <i class="fas fa-info-circle fa-2x text-muted opacity-25 mb-3"></i>
                                        <p class="text-dark fw-bold mb-2">Selamat! Anda telah berhasil menyelesaikan pelatihan <br><span class="text-danger"><?= $p['nama'] ?></span></p>
                                        <p class="text-muted mb-0 small">Sertifikat Anda sedang dalam tahap verifikasi akhir dan proses penandatanganan oleh penyelenggara. Anda akan menerima notifikasi segera setelah sertifikat digital resmi Anda siap untuk diunduh.</p>
                                    <?php else: ?>
                                        <i class="fas fa-info-circle fa-2x text-danger opacity-75 mb-3"></i>
                                        <p class="text-dark fw-bold mb-2">Anda telah menyelesaikan pelatihan <br><span class="text-danger"><?= $p['nama'] ?></span></p>
                                        <p class="text-muted mb-0 small text-danger">Maaf, Sertifikat tidak diterbitkan karena nilai akhir Post-Test Anda belum memenuhi KKM.</p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex justify-content-center">
                                    <a href="<?= base_url('pelatihan/peserta/beranda') ?>" class="btn px-5 py-3 fw-bold rounded-pill fs-5 d-flex align-items-center justify-content-center gap-3 shadow-sm hover-scale" style="border: 2px solid #0f172a; color: #0f172a; background: transparent; transition: all 0.3s ease;">
                                        <i class="fas fa-arrow-left fs-5"></i> KEMBALI KE BERANDA
                                    </a>
                                </div>
                            </div>
                    <?php else: ?>
                    <script>
                        window.onload = function() {
                            <?php if ($post_test_status == 'Lulus'): ?>
                            // Trigger Confetti
                            var count = 200;
                            var defaults = { origin: { y: 0.7 } };

                            function fire(particleRatio, opts) {
                                confetti(Object.assign({}, defaults, opts, {
                                    particleCount: Math.floor(count * particleRatio)
                                }));
                            }

                            fire(0.25, { spread: 26, startVelocity: 55 });
                            fire(0.2, { spread: 60 });
                            fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
                            fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
                            fire(0.1, { spread: 120, startVelocity: 45 });

                            // Trigger SweetAlert2
                            Swal.fire({
                                title: '<span class="text-success fw-bold">PELATIHAN SELESAI!</span>',
                                html: 'Selamat! Anda telah menyelesaikan <b><?= $p['nama'] ?></b> dengan hasil yang memuaskan.',
                                icon: 'success',
                                iconColor: '#10b981',
                                confirmButtonText: 'LIHAT HASIL AKHIR',
                                confirmButtonColor: '#1e293b',
                                background: '#fff',
                                backdrop: `rgba(15, 23, 42, 0.4)`,
                                showClass: { popup: 'animate__animated animate__zoomIn' },
                                hideClass: { popup: 'animate__animated animate__fadeOut' }
                            });
                            <?php else: ?>
                            Swal.fire({
                                title: '<span class="text-warning fw-bold">PELATIHAN SELESAI</span>',
                                html: 'Anda telah menyelesaikan seluruh modul pelatihan <b><?= $p['nama'] ?></b>.',
                                icon: 'info',
                                iconColor: '#f59e0b',
                                confirmButtonText: 'LIHAT HASIL AKHIR',
                                confirmButtonColor: '#1e293b',
                                background: '#fff',
                                backdrop: `rgba(15, 23, 42, 0.4)`,
                                showClass: { popup: 'animate__animated animate__zoomIn' },
                                hideClass: { popup: 'animate__animated animate__fadeOut' }
                            });
                            <?php endif; ?>
                        };
                    </script>

                    <div class="success-portal-card p-5 mb-5 mx-auto animate__animated animate__fadeInUp" style="max-width: 800px; background: white; border-radius: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); position: relative; overflow: hidden; border: 1px solid #f1f5f9;">
                        <!-- Decorative Background Elements -->
                        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(16, 185, 129, 0.05); border-radius: 50%; z-index: 0;"></div>
                        <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(206, 33, 39, 0.05); border-radius: 50%; z-index: 0;"></div>

                        <div class="position-relative" style="z-index: 1;">
                            <div class="mb-4">
                                <?php if ($post_test_status == 'Lulus'): ?>
                                    <div class="bg-success bg-opacity-10 d-inline-block p-4 rounded-circle mb-4 animate__animated animate__bounceIn animate__delay-1s">
                                        <i class="fas fa-award fa-5x text-success"></i>
                                    </div>
                                    <h1 class="display-5 fw-bold text-dark mb-2">SELAMAT! ANDA LULUS</h1>
                                    <p class="text-muted fw-bold fs-5 mb-5 opacity-75">Sertifikat Digital Anda telah diterbitkan dan siap diunduh.</p>
                                <?php else: ?>
                                    <div class="bg-danger bg-opacity-10 d-inline-block p-4 rounded-circle mb-4 animate__animated animate__bounceIn animate__delay-1s">
                                        <i class="fas fa-times-circle fa-5x text-danger"></i>
                                    </div>
                                    <h1 class="display-5 fw-bold text-dark mb-2">PELATIHAN SELESAI</h1>
                                    <p class="text-danger fw-bold fs-5 mb-5 opacity-75">Sertifikat tidak diterbitkan karena nilai akhir Post-Test belum memenuhi KKM.</p>
                                <?php endif; ?>
                            </div>

                            <div class="row g-4 mb-5">
                                <div class="col-md-4">
                                    <div class="p-4 rounded-4 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <small class="text-muted fw-bold d-block mb-2 text-uppercase letter-spacing-1">HASIL AKHIR</small>
                                        <div class="display-6 fw-bold text-dark"><?= number_format($post_test_score ?? 0, 2) ?></div>
                                        <?php if ($post_test_status == 'Lulus'): ?>
                                            <div class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 mt-2 fw-bold">KOMPETEN</div>
                                        <?php else: ?>
                                            <div class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 mt-2 fw-bold">TIDAK LULUS</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 rounded-4 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <small class="text-muted fw-bold d-block mb-2 text-uppercase letter-spacing-1">TOTAL DURASI</small>
                                        <div class="h3 fw-bold text-dark mb-0"><?= esc($p['jpl'] ?? 0) ?> JPL</div>
                                        <div class="small text-muted mt-2 fw-bold">Jam Pelajaran</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 rounded-4 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <small class="text-muted fw-bold d-block mb-2 text-uppercase letter-spacing-1">PENYELENGGARA</small>
                                        <div class="h6 fw-bold text-dark mb-0 text-truncate" title="<?= esc($p['penyelenggara'] ?? '') ?>"><?= strtoupper(esc($p['penyelenggara'] ?? 'TIDAK DIKETAHUI')) ?></div>
                                        <div class="small text-muted mt-2 fw-bold">Institusi Terdaftar</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 mb-5 rounded-4 d-flex align-items-center gap-4 text-start" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white;">
                                <div class="bg-white bg-opacity-10 p-3 rounded-3">
                                    <i class="fas fa-id-badge fa-2x"></i>
                                </div>
                                <div>
                                    <div class="opacity-75 small fw-bold">NAMA PESERTA</div>
                                    <h4 class="fw-bold mb-0"><?= strtoupper($user['nama_lengkap'] ?? 'Peserta') ?></h4>
                                </div>
                                <div class="ms-auto text-end">
                                    <div class="opacity-50 small fw-bold">TANGGAL SELESAI</div>
                                    <div class="fw-bold"><?= date('d F Y') ?></div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                                <?php if ($post_test_status == 'Lulus'): ?>
                                    <a href="<?= base_url('pelatihan/peserta/sertifikat_saya') ?>" class="btn px-5 py-3 shadow-lg fw-bold rounded-pill fs-5 d-flex align-items-center justify-content-center gap-2" style="background: var(--primary-red) !important; color: white !important;">
                                        <i class="fas fa-certificate fs-4"></i> LIHAT SERTIFIKAT SAYA
                                    </a>
                                <?php endif; ?>
                                <a href="<?= base_url('pelatihan/peserta/beranda') ?>" class="btn px-5 py-3 fw-bold rounded-pill fs-5 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="border: 2px solid #0f172a; color: #0f172a; background: transparent;">
                                    <i class="fas fa-home fs-4"></i> KEMBALI KE BERANDA
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form').forEach(form => {
        if (form.getAttribute('onsubmit') && form.getAttribute('onsubmit').includes('Swal.fire')) {
            form.removeAttribute('onsubmit');
            // Disable native validation so our submit event ALWAYS fires
            form.setAttribute('novalidate', 'true');
            
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ada yang terlewat!',
                        text: 'Mohon pastikan Anda sudah mengisi/memilih seluruh pertanyaan wajib sebelum mengirim.',
                        confirmButtonColor: '#ce2127'
                    });
                } else {
                    Swal.fire({
                        title: 'Memproses...', 
                        text: 'Mohon tunggu sebentar', 
                        allowOutsideClick: false, 
                        didOpen: () => { Swal.showLoading(); }
                    });
                }
            });
        }
    });
});

function toggleFullscreen(elem) {
    if (!document.fullscreenElement) {
        elem.requestFullscreen().catch(err => {
            console.error(`Error attempting to enable fullscreen: ${err.message}`);
        });
    } else {
        document.exitFullscreen();
    }
}
</script>

<?= $this->endSection() ?>
