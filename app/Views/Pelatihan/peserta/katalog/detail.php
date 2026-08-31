<?php
$p = $p ?? [];
$reg = $reg ?? [];
$reg_status = $reg_status ?? 'belum_daftar';
$is_reg_open = $is_reg_open ?? false;
$is_learning_open = $is_learning_open ?? false;
$is_learning_finished = $is_learning_finished ?? false;
$konten = $konten ?? [];
$reg_buka = $reg_buka ?? null;
$reg_tutup = $reg_tutup ?? null;
$jadwal_mulai = $jadwal_mulai ?? null;
$jadwal_selesai = $jadwal_selesai ?? null;
$has_progress = $has_progress ?? false;
?>

<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --primary-red: #ce2127;
        --primary-dark: #111111;
        --primary-black: #000000;
        --soft-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    }

    .detail-header {
        background: linear-gradient(135deg, rgba(80, 10, 15, 0.98) 0%, rgba(45, 5, 10, 0.98) 100%);
        color: white;
        padding: 40px 0 80px;
        border-radius: 0 0 40px 40px;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
    }

    .detail-header::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, transparent 60%);
        pointer-events: none;
    }

    .info-card {
        background: linear-gradient(135deg, rgba(80, 10, 15, 0.98) 0%, rgba(45, 5, 10, 0.98) 100%);
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.15);
        position: sticky;
        top: 20px;
        color: white;
    }

    .section-title {
        position: relative;
        padding-left: 18px;
        margin-bottom: 25px;
        font-weight: 800;
        color: var(--primary-dark);
        font-size: 1.25rem;
    }

    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 80%;
        width: 5px;
        background: var(--primary-red);
        border-radius: 10px;
    }

    .badge-premium {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(5px);
        padding: 8px 16px;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    @media (max-width: 768px) {
        .feature-grid {
            grid-template-columns: 1fr;
        }
    }

    .feature-item {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: flex-start;
        gap: 15px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .feature-item:hover {
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: #cbd5e1;
    }

    .feature-icon {
        background: white;
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-red);
        font-size: 1.25rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        flex-shrink: 0;
    }

    .registration-alert {
        background: #6c757d; /* Abu-abu untuk belum dibuka */
        color: white;
        border-radius: 16px;
        padding: 20px;
        border-left: 5px solid #495057;
    }
    
    .target-profesi-box {
        /* max-height removed to show all */
    }
    
    /* Scrollbar styling for target profesi removed */

    .glass-wrapper {
        background: linear-gradient(135deg, rgba(80, 10, 15, 0.98) 0%, rgba(45, 5, 10, 0.98) 100%);
        box-shadow: 0 15px 35px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.15) !important;
        color: white;
    }

    .glass-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        color: white;
    }
    
    .glass-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.4);
        border-color: rgba(255,255,255,0.5);
        background: rgba(255,255,255,0.15);
    }
    
    .glass-card .keahlian-badge {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,0.1);
    }

    .section-title-glass {
        position: relative;
        padding-left: 18px;
        margin-bottom: 25px;
        font-weight: 800;
        color: white;
        font-size: 1.25rem;
    }

    .section-title-glass::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 80%;
        width: 5px;
        background: white;
        border-radius: 10px;
    }

    .btn-action {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    }
    
    .btn-action:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 15px 25px rgba(0,0,0,0.2) !important;
        filter: brightness(1.15);
    }

    @keyframes bounceAction {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .btn-mantul {
        animation: bounceAction 2s infinite ease-in-out;
    }
    .btn-mantul:hover {
        animation-play-state: paused;
    }
</style>

<?php
    $gambarPelatihan = !empty($p['gambar_pelatihan']) ? base_url($p['gambar_pelatihan']) : null;
?>

<div class="detail-header" <?= $gambarPelatihan ? 'style="background-image: linear-gradient(135deg, rgba(80, 10, 15, 0.95) 0%, rgba(45, 5, 10, 0.98) 100%), url(' . esc($gambarPelatihan) . '); background-size: cover; background-position: center;"' : '' ?>>
    <div class="container-fluid px-4 px-lg-5 position-relative" style="z-index: 2;">
        
        <!-- Integrated Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= site_url('pelatihan/peserta/pembelajaran') ?>" class="text-decoration-none text-white opacity-75 fw-bold">KATALOG</a></li>
                <li class="breadcrumb-item active fw-bold text-white" aria-current="page">DETAIL PELATIHAN</li>
            </ol>
        </nav>

        <div class="row align-items-center">
            <div class="col-lg-10 animate__animated animate__fadeInUp">
                <div class="d-flex gap-2 mb-4 flex-wrap">
                    <span class="badge badge-premium rounded-pill text-white"><?= strtoupper($p['metode'] ?? 'ONLINE') ?></span>
                    <span class="badge badge-premium rounded-pill text-white"><?= strtoupper($p['mekanisme'] ?? 'TERBUKA') ?></span>
                    <span class="badge badge-premium rounded-pill text-white"><?= strtoupper($p['biaya'] ?? 'GRATIS') ?></span>
                    <span class="badge badge-premium rounded-pill text-white"><i class="fas fa-tag me-1"></i> <?= strtoupper($p['kategori'] ?? 'UMUM') ?></span>
                </div>
                <h1 class="display-5 fw-extrabold mb-4 lh-sm text-white" style="letter-spacing: -0.5px;"><?= $p['nama'] ?? 'Pelatihan Kesehatan' ?></h1>
                <div class="d-flex align-items-center gap-3 bg-white bg-opacity-10 p-3 rounded-4 d-inline-flex border border-white border-opacity-10">
                    <?php 
                        $penyList = explode(',', $p['penyelenggara'] ?? 'Dinas Kesehatan');
                        $firstPeny = trim($penyList[0]);
                    ?>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($firstPeny) ?>&background=ce2127&color=fff" class="rounded-circle shadow-sm" width="45">
                    <div>
                        <div class="fw-bold text-white mb-1"><?= strtoupper($p['penyelenggara'] ?? 'Dinas Kesehatan') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4 px-lg-5 mb-5" style="margin-top: -60px; position: relative; z-index: 10;">
    <div class="row g-4 justify-content-center">
        
        <!-- Sidebar Info -->
        <div class="col-lg-3 col-xl-3 order-2 order-lg-2 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="info-card">
                <h5 class="fw-bold mb-4 text-white d-flex align-items-center gap-2">
                    <i class="fas fa-clipboard-check text-white"></i> Registrasi
                </h5>
                
                <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded-4 p-3 mb-4">
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <span class="text-white fw-bold small">KUOTA</span>
                        <span class="h6 fw-bold mb-0 text-white"><?= $p['kuota'] ?? 0 ?> <small class="text-white opacity-75">Slot</small></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 align-items-center">
                        <span class="text-white fw-bold small">TERISI</span>
                        <span class="h6 fw-bold mb-0 text-white"><?= $p['peserta'] ?? 0 ?> <small class="text-white opacity-75">Peserta</small></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-white border-opacity-25">
                        <span class="text-white fw-bold small">TOTAL BOBOT</span>
                        <span class="h5 fw-bold mb-0 text-white"><?= $p['jpl'] ?? 0 ?> <small class="text-white opacity-75 fs-6">JPL</small></span>
                    </div>
                </div>

                <?php if (($p['biaya'] ?? '') == 'Berbayar'): ?>
                <div class="mb-4 border border-white border-opacity-25 p-3 rounded-4 bg-white bg-opacity-10">
                    <h6 class="fw-bold mb-3 text-white"><i class="fas fa-wallet me-2"></i> Biaya Pendaftaran</h6>
                    <div class="fs-4 fw-bold text-white mb-3">Rp <?= number_format($p['biaya_nominal'] ?? 0, 0, ',', '.') ?></div>
                    <div class="small text-white opacity-75 mb-1">Transfer Rekening:</div>
                    <div class="bg-white bg-opacity-10 p-2 rounded border border-white border-opacity-10 fw-bold text-white mb-1"><?= $p['nama_bank'] ?? '-' ?></div>
                    <div class="bg-white bg-opacity-10 p-2 rounded border border-white border-opacity-10 font-monospace text-white fw-bold mb-1"><?= $p['no_rekening'] ?? '-' ?></div>
                    <div class="bg-white bg-opacity-10 p-2 rounded border border-white border-opacity-10 small text-white opacity-75">a.n <?= $p['atas_nama'] ?? '-' ?></div>
                </div>
                <?php endif; ?>

                <div class="mb-0">
                    <?php
                        $nowTs = time();
                        $regBukaTs = strtotime($reg_buka ?? '');
                        $regTutupTs = strtotime($reg_tutup ?? '');
                        $regBukaText = $regBukaTs ? date('d M Y H:i', $regBukaTs) . ' WIB' : '-';
                        $regTutupText = $regTutupTs ? date('d M Y H:i', $regTutupTs) . ' WIB' : '-';
                    ?>
                    <?php if ($reg_status == 'pending') : ?>
                        <?php if (($p['biaya'] ?? '') == 'Berbayar') : ?>
                            <?php if (!empty($reg['bukti_bayar'])): ?>
                                <div class="alert alert-warning border-0 text-center py-3 mb-0 rounded-3 shadow-sm btn-mantul" style="background: rgba(245, 158, 11, 0.1); color: #fcd34d;">
                                    <i class="fas fa-hourglass-half mb-2 fs-4"></i><br>
                                    <span class="fw-bold d-block">MENUNGGU VERIFIKASI</span>
                                    <small class="opacity-75">Bukti bayar sedang ditinjau.</small>
                                </div>
                            <?php else: ?>
                                <button type="button" onclick="showUploadForm()" class="btn w-100 py-3 fs-6 rounded-pill text-white fw-bold shadow btn-action btn-mantul" style="background-color: #d97706;">
                                    UNGGAH BUKTI BAYAR <i class="fas fa-upload ms-2"></i>
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning border-0 text-center py-3 mb-0 rounded-3 shadow-sm btn-mantul" style="background: rgba(245, 158, 11, 0.1); color: #fcd34d;">
                                <i class="fas fa-hourglass-half mb-2 fs-4"></i><br>
                                <span class="fw-bold d-block">MENUNGGU VERIFIKASI</span>
                                <small class="opacity-75">Admin sedang meninjau profil Anda.</small>
                            </div>
                        <?php endif; ?>
                    <?php elseif ($reg_status == 'disetujui') : ?>
                        <?php if ($is_learning_finished): ?>
                            <div class="alert alert-secondary border-0 text-center py-3 mb-0 rounded-3 shadow-sm fw-bold btn-mantul" style="background: rgba(255,255,255,0.1); color: white;">
                                PELATIHAN BERAKHIR
                            </div>
                        <?php elseif ($is_learning_open): ?>
                            <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id']) ?>" class="btn w-100 py-3 rounded-pill fw-bold text-white shadow-lg btn-action d-flex justify-content-center align-items-center gap-2" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); letter-spacing: 1px; border: 1px solid rgba(255,255,255,0.2);">
                                <span><?= !empty($has_progress) ? 'LANJUTKAN' : 'MULAI' ?></span>
                            </a>
                        <?php else: ?>
                            <div class="alert alert-success border-0 text-center py-3 mb-0 rounded-3 shadow-sm btn-mantul" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                                <i class="fas fa-check-circle mb-2 fs-4"></i><br>
                                <span class="fw-bold d-block">TERDAFTAR</span>
                                <small class="opacity-75">Tunggu jadwal dimulai.</small>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if (!$is_reg_open): ?>
                            <div class="registration-alert text-center mb-0 btn-mantul" style="background: rgba(255,255,255,0.1); border-left: 5px solid rgba(255,255,255,0.2);">
                                <?php if ($regBukaTs && $nowTs < $regBukaTs): ?>
                                    <i class="fas fa-lock mb-2 fs-4 text-white-50"></i>
                                    <strong class="d-block mb-1">PENDAFTARAN BELUM DIBUKA</strong>
                                    <small class="d-block text-white opacity-75">Buka: <?= esc($regBukaText) ?></small>
                                <?php else: ?>
                                    <i class="fas fa-lock mb-2 fs-4 text-white-50"></i>
                                    <strong class="d-block mb-1">PENDAFTARAN DITUTUP</strong>
                                    <small class="d-block text-white opacity-75">Tutup: <?= esc($regTutupText) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <form action="<?= base_url('pelatihan/peserta/daftar/'.$p['id']) ?>" method="POST">
                                <button type="submit" class="btn w-100 py-3 rounded-pill text-white fw-bold shadow-lg btn-action btn-mantul" style="background-color: var(--primary-red);">
                                    DAFTAR SEKARANG <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </form>
                            <div class="text-center mt-3 small text-white opacity-75">
                                Ditutup pada: <?= esc($regTutupText) ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>

        <!-- Main Content (Beside Sidebar) -->
        <div class="col-lg-8 col-xl-9 order-1 order-lg-1 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            
            <div class="glass-wrapper p-4 p-md-5 rounded-4 mb-4">
                
                <!-- Pengumuman -->
                <?php if (!empty($p['pengumuman'])): ?>
                <div class="alert glass-card d-flex gap-3 align-items-center mb-5 rounded-3 border-0">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; color: white;">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 text-white">Pengumuman</h6>
                        <p class="mb-0 small text-white opacity-75"><?= $p['pengumuman'] ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tentang -->
                <div class="mb-5">
                    <h4 class="section-title-glass">Tentang Pelatihan</h4>
                    <p class="text-white opacity-75" style="line-height: 1.8;">
                        <?= $p['deskripsi'] ?? 'Deskripsi pelatihan tidak tersedia.' ?>
                    </p>
                </div>

                <!-- Informasi Grid -->
                <div class="mb-5">
                    <h4 class="section-title-glass">Informasi Lengkap</h4>
                    <div class="feature-grid">
                        <div class="glass-card rounded-4 p-3 d-flex align-items-start gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:45px; height:45px;"><i class="fas fa-shapes fs-5"></i></div>
                            <div>
                                <div class="small text-white opacity-75 fw-bold mb-1">Tema / Program</div>
                                <div class="text-white fw-bold small"><?= $p['tema'] ?? '-' ?></div>
                                <div class="text-white opacity-50 small fw-semibold"><?= $p['program'] ?? '-' ?></div>
                            </div>
                        </div>
                        <div class="glass-card rounded-4 p-3 d-flex align-items-start gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:45px; height:45px;"><i class="fas fa-layer-group fs-5"></i></div>
                            <div>
                                <div class="small text-white opacity-75 fw-bold mb-1">Level & Cakupan</div>
                                <div class="text-white fw-bold small"><?= $p['level'] ?? '-' ?></div>
                                <div class="text-white opacity-50 small fw-semibold"><?= $p['cakupan'] ?? '-' ?></div>
                            </div>
                        </div>
                        <div class="glass-card rounded-4 p-3 d-flex align-items-start gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:45px; height:45px;"><i class="fas fa-user-md fs-5"></i></div>
                            <div class="w-100">
                                <div class="small text-white opacity-75 fw-bold mb-1">Target Profesi</div>
                                <div class="text-white fw-bold small">
                                    <?php 
                                        $profesiList = explode(',', $p['target_profesi'] ?? 'Semua Profesi');
                                        foreach($profesiList as $prof) {
                                            echo '<span class="d-inline-block bg-white bg-opacity-25 rounded px-2 py-1 mb-1 me-1" style="font-size:0.7rem;">'.trim($prof).'</span>';
                                        }
                                    ?>
                                </div>
                                <?php if(!empty($p['target_khusus_profesi'])): ?>
                                    <div class="text-white opacity-75 small fw-semibold mt-1 border-top border-white border-opacity-10 pt-1">Khusus: <?= $p['target_khusus_profesi'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="glass-card rounded-4 p-3 d-flex align-items-start gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:45px; height:45px;"><i class="fas fa-calendar-alt fs-5"></i></div>
                            <div>
                                <div class="small text-white opacity-75 fw-bold mb-1">Jadwal Pelaksanaan</div>
                                <div class="text-white fw-bold small"><?= tanggal_indo($p['jadwal_mulai'] ?? 'now') ?> - <?= tanggal_indo($p['jadwal_selesai'] ?? 'now') ?></div>
                                <div class="text-white opacity-50 small fw-semibold"><?= $p['jam_mulai'] ?? '08:00' ?> s.d <?= $p['jam_selesai'] ?? '16:00' ?> WIB</div>
                            </div>
                        </div>
                        <div class="glass-card rounded-4 p-3 d-flex align-items-start gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:45px; height:45px;"><i class="fas fa-map-marker-alt fs-5"></i></div>
                            <div>
                                <div class="small text-white opacity-75 fw-bold mb-1">Metode & Lokasi</div>
                                <div class="text-white fw-bold small"><?= $p['metode'] ?? 'Online' ?></div>
                                <?php
                                    $metodeText = $p['metode'] ?? 'Online';
                                    $lokasiText = 'Zoom / LMS';
                                    if ($metodeText == 'Offline' || $metodeText == 'Offline / Clasical') {
                                        $lokasiText = empty($p['lokasi']) ? 'LOKASI OFFLINE menyesuaikan sesi.' : $p['lokasi'];
                                    } elseif (strpos(strtolower($metodeText), 'blended') !== false || strpos(strtolower($metodeText), 'hybrid') !== false) {
                                        $lokasiText = 'Zoom / LMS & ' . (empty($p['lokasi']) ? 'LOKASI OFFLINE menyesuaikan sesi.' : $p['lokasi']);
                                    }
                                ?>
                                <div class="text-white opacity-50 small fw-semibold"><?= $lokasiText ?></div>
                            </div>
                        </div>
                        <div class="glass-card rounded-4 p-3 d-flex align-items-start gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:45px; height:45px;"><i class="fas fa-check-double fs-5"></i></div>
                            <div>
                                <div class="small text-white opacity-75 fw-bold mb-1">Nilai KKM Kelulusan</div>
                                <div class="text-white fw-bold small"><?= $p['kkm'] ?? '70' ?></div>
                                <div class="text-white opacity-50 small fw-semibold">Batas Minimal Lulus</div>
                            </div>
                        </div>
                        <div class="glass-card rounded-4 p-3 d-flex align-items-start gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:45px; height:45px;"><i class="fas fa-award fs-5"></i></div>
                            <div>
                                <div class="small text-white opacity-75 fw-bold mb-1">SKP & Bobot JPL</div>
                                <div class="text-white fw-bold small"><?= $p['skp'] ?? '0' ?> SKP / <?= $p['jpl'] ?? '0' ?> JPL</div>
                                <div class="text-white opacity-50 small fw-semibold">Sertifikasi</div>
                            </div>
                        </div>
                        <div class="glass-card rounded-4 p-3 d-flex align-items-start gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:45px; height:45px;"><i class="fas fa-hospital-user fs-5"></i></div>
                            <div>
                                <div class="small text-white opacity-75 fw-bold mb-1">Target Unit Khusus</div>
                                <div class="text-white fw-bold small"><?= $p['target_khusus_unit'] ?? 'Semua Unit / Tidak Ada' ?></div>
                                <div class="text-white opacity-50 small fw-semibold">Prioritas Peserta</div>
                            </div>
                        </div>
                        <div class="glass-card rounded-4 p-3 d-flex align-items-start gap-3">
                            <div class="bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:45px; height:45px;"><i class="fas fa-headset fs-5"></i></div>
                            <div>
                                <div class="small text-white opacity-75 fw-bold mb-1">Kontak Penyelenggara</div>
                                <div class="text-white fw-bold small"><?= $p['kontak'] ?? '-' ?></div>
                                <div class="text-white opacity-50 small fw-semibold">Bantuan / Info</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Narasumber Section Inside Main Box -->
                <div class="mb-5">
                    <h4 class="section-title-glass">Instruktur & Narasumber Ahli</h4>
                    <div class="row g-4">
                        <?php if (empty($p['narasumber_data'])): ?>
                            <div class="col-12">
                                <div class="bg-white bg-opacity-10 rounded-3 p-4 text-center">
                                    <p class="text-white opacity-75 mb-0">Belum ada instruktur/narasumber yang ditentukan.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach($p['narasumber_data'] as $n): ?>
                            <div class="col-12 col-xl-6">
                                <div class="p-4 rounded-4 glass-card h-100 d-flex flex-column">
                                    <div class="d-flex flex-column flex-sm-row gap-4 mb-4">
                                        <?php
                                            $fotoN = !empty($n['foto']) ? base_url('uploads/narasumber/'.$n['foto']) : 'https://ui-avatars.com/api/?name='.urlencode($n['nama_lengkap']).'&background=fff&color=ce2127';
                                        ?>
                                        <div class="position-relative flex-shrink-0 text-center text-sm-start">
                                            <img src="<?= $fotoN ?>" class="rounded-circle object-fit-cover shadow-lg" width="100" height="100" style="border: 3px solid rgba(255,255,255,0.9);">
                                            <div class="position-absolute bg-success rounded-circle border border-3 border-white" style="width: 20px; height: 20px; bottom: 5px; right: 5px;"></div>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-white mb-2" style="letter-spacing: -0.5px;"><?= esc($n['nama_lengkap']) ?></h5>
                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                <span class="keahlian-badge text-white px-3 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                                    <i class="fas fa-star text-warning me-1"></i> <?= esc($n['keahlian'] ?? 'Instruktur') ?>
                                                </span>
                                                <?php if(!empty($n['jabatan'])): ?>
                                                    <span class="bg-black bg-opacity-25 text-white px-3 py-1 rounded-pill fw-normal" style="font-size: 0.7rem;">
                                                        <i class="fas fa-briefcase me-1 opacity-50"></i> <?= esc($n['jabatan']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-white opacity-75 small mb-1">
                                                <i class="fas fa-graduation-cap me-2 opacity-50"></i><?= esc($n['pendidikan'] ?? 'Pendidikan tidak disebutkan') ?>
                                            </div>
                                            <div class="d-flex flex-wrap gap-3 text-white opacity-75 small">
                                                <?php if(!empty($n['email'])): ?>
                                                    <div><i class="fas fa-envelope me-2 opacity-50"></i><?= esc($n['email']) ?></div>
                                                <?php endif; ?>
                                                <?php if(!empty($n['kontak'])): ?>
                                                    <div><i class="fas fa-phone-alt me-2 opacity-50"></i><?= esc($n['kontak']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if(!empty($n['riwayat'])): ?>
                                        <div class="bg-black bg-opacity-25 p-3 rounded-3 mt-auto border border-white border-opacity-10">
                                            <h6 class="text-white fw-bold mb-2 fs-6"><i class="fas fa-file-alt me-2 text-white-50"></i> Profil & Riwayat</h6>
                                            <p class="text-white opacity-75 small mb-0 lh-base" style="text-align: justify;">
                                                <?= nl2br(esc($n['riwayat'])) ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tujuan & Kompetensi -->
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="glass-card p-4 rounded-4 h-100">
                            <h6 class="fw-bold mb-3 text-white d-flex align-items-center gap-2"><i class="fas fa-bullseye text-warning"></i> Tujuan</h6>
                            <p class="text-white opacity-75 small mb-0 lh-lg"><?= $p['tujuan'] ?? 'Belum ada informasi tujuan.' ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4 rounded-4 h-100">
                            <h6 class="fw-bold mb-3 text-white d-flex align-items-center gap-2"><i class="fas fa-star text-warning"></i> Kompetensi</h6>
                            <p class="text-white opacity-75 small mb-0 lh-lg"><?= $p['kompetensi'] ?? 'Belum ada informasi kompetensi.' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Kurikulum -->
                <div class="mb-2">
                    <h4 class="section-title-glass">Kurikulum Materi</h4>
                    <?php if(empty($konten)): ?>
                        <div class="text-center py-5 text-white opacity-75 glass-card rounded-4 border-0">
                            <i class="fas fa-folder-open mb-3 fs-3 opacity-50"></i>
                            <div class="small fw-bold">Belum ada materi terdaftar</div>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush rounded-4 overflow-hidden" style="border: 1px solid rgba(255,255,255,0.2);">
                            <?php foreach($konten as $index => $k): ?>
                                <div class="list-group-item p-3 border-bottom border-white border-opacity-10" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(5px);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white bg-opacity-25 rounded-circle shadow-sm d-flex align-items-center justify-content-center fw-bold text-white border border-white border-opacity-25" style="width: 35px; height: 35px; flex-shrink: 0; font-size: 0.8rem;">
                                            <?= $index + 1 ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-1 text-white" style="font-size: 0.9rem;"><?= $k['judul'] ?></h6>
                                                    <?php if(!empty($k['deskripsi'])): ?>
                                                        <p class="text-white opacity-75 mb-0" style="font-size: 0.75rem;"><?= $k['deskripsi'] ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="badge rounded-pill bg-white text-danger px-2 py-1 fw-bold shadow-sm" style="font-size: 0.65rem;">
                                                    <i class="fas fa-<?= $k['tipe'] == 'video' ? 'play-circle' : ($k['tipe'] == 'materi' ? 'file-alt' : 'tasks') ?> me-1"></i>
                                                    <?= strtoupper($k['tipe']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function showUploadForm() {
        Swal.fire({
            title: 'Unggah Bukti Pembayaran',
            html: `
                <div class="text-start mb-3">
                    <p class="text-muted small">Format yang didukung: JPG, PNG, PDF (Maks. 8MB)</p>
                </div>
                <form id="uploadForm" action="<?= base_url('pelatihan/peserta/upload_bukti_bayar/'.$p['id']) ?>" method="POST" enctype="multipart/form-data">
                    <input type="file" name="bukti_bayar" id="bukti_bayar" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal',
            confirmButtonColor: 'var(--primary-red)',
            cancelButtonColor: '#64748b',
            preConfirm: () => {
                const fileInput = document.getElementById('bukti_bayar');
                if (!fileInput.files.length) {
                    Swal.showValidationMessage('File bukti bayar harus dipilih');
                    return false;
                }
                document.getElementById('uploadForm').submit();
            }
        });
    }

    <?php if (session()->getFlashdata('show_upload_popup')): ?>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            showUploadForm();
        }, 500);
    });
    <?php endif; ?>
</script>
<?= $this->endSection() ?>
