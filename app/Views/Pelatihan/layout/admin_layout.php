<?php

/** @var string $title */ ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - SIM-DIKLAT RSUD KOTA YOGYAKARTA</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= get_system_favicon() ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        html, body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f7fe;
            color: #0f172a;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 50rem;
            height: 38px;
            border: 1px solid #dee2e6;
            padding-top: 4px;
        }

        .swal2-popup {
            border-radius: 1.5rem !important;
        }

        /* Color Palette */
        :root {
            --primary-red: #c62828;
            --primary-dark: #1e293b;
            --primary-black: #0f172a;
            --primary-yellow: #ffc107;
            --sidebar-bg: #ffffff;
            --bg-light: #f8fafc;
        }

        .list-group-item.active {
            background-color: var(--primary-red) !important;
            border-color: var(--primary-red) !important;
            color: white !important;
        }

        .list-group-item.active .text-dark,
        .list-group-item.active .text-muted {
            color: white !important;
        }

        .text-muted {
            color: #64748b !important;
            opacity: 1 !important;
        }

        .text-dark {
            color: var(--primary-black) !important;
        }

        .bg-primary-custom {
            background: var(--primary-black) !important;
        }

        .text-primary-custom {
            color: var(--primary-black) !important;
        }

        .btn-primary-custom {
            background: var(--primary-red) !important;
            color: white !important;
            transition: all 0.3s ease;
            border: none;
            font-weight: 800;
        }

        .btn-primary-custom:hover {
            background: #a51a1f !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(206, 33, 39, 0.3);
        }

        .rounded-lg {
            border-radius: 1.25rem !important;
        }

        .notification-popup {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 10800;
            width: min(380px, calc(100% - 3rem));
            background: rgba(15, 23, 42, 0.97);
            border: 1px solid rgba(206, 33, 39, 0.35);
            border-radius: 1.5rem;
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.24);
            color: white;
            overflow: hidden;
            backdrop-filter: blur(10px);
            transform: translateY(-15px);
            opacity: 0;
            transition: transform 0.25s ease, opacity 0.25s ease;
        }

        .notification-popup.show {
            transform: translateY(0);
            opacity: 1;
        }

        .notification-popup .popup-inner {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem 1rem 1rem 1.25rem;
        }

        .notification-popup .popup-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(206, 33, 39, 0.18);
            display: grid;
            place-items: center;
            color: white;
            font-size: 1.2rem;
        }

        .notification-popup .popup-title {
            font-size: 0.95rem;
            font-weight: 800;
            margin-bottom: 0.1rem;
        }

        .notification-popup .popup-message {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.5;
        }

        .notification-popup .popup-close {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.72);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .notification-popup .popup-close:hover {
            color: white;
            transform: scale(1.05);
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: rgba(110, 15, 25, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            z-index: 1000;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
            padding-bottom: 20px;
        }

        .sidebar-content::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-content::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .main-content {
            margin-left: 250px;
            padding: 1.5rem;
            transition: all 0.3s;
            min-height: 100vh;
            background-color: #f4f7fe;
            width: calc(100% - 250px);
            overflow-x: hidden;
        }

        .nav-link-custom {
            padding: 0.6rem 1.25rem;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            border-radius: 50rem;
            margin: 0.15rem 0.8rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 700;
            font-size: 0.8rem;
        }

        .nav-link-custom i {
            width: 18px;
            margin-right: 10px;
            font-size: 0.9rem;
            transition: all 0.2s;
            opacity: 0.7;
        }

        .nav-link-custom:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .nav-link-custom.active {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        .nav-link-custom.active i, .nav-link-custom:hover i {
            color: #ffffff;
            opacity: 1;
        }
        
        /* Premium text colors for dark sidebar */
        .sidebar .text-dark { color: #ffffff !important; }
        .sidebar .text-muted { color: rgba(255, 255, 255, 0.4) !important; }
        .sidebar .bg-white { background: transparent !important; }
        .sidebar .border-top { border-top: 1px solid rgba(255, 255, 255, 0.08) !important; }
        
        .sidebar .btn-outline-dark {
            color: rgba(255, 255, 255, 0.8);
            border-color: rgba(255, 255, 255, 0.2);
        }
        .sidebar .btn-outline-dark:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.4);
            color: #ffffff;
        }
        .sidebar .btn-dark {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        .sidebar .btn-dark:hover {
            background: rgba(255, 255, 255, 0.2) !important;
        }

        .card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), inset 0 2px 0 rgba(255, 255, 255, 0.8);
            border-radius: 1.5rem;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08), inset 0 2px 0 rgba(255, 255, 255, 1);
            transform: translateY(-2px);
        }

        .logo-box {
            background: #fff;
            border-radius: 14px;
            padding: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .btn-sidebar-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            color: var(--primary-black);
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
                box-shadow: 0 0 30px rgba(0,0,0,0.1) !important;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(15, 23, 42, 0.5);
                backdrop-filter: blur(2px);
                z-index: 999;
                opacity: 0;
                transition: opacity 0.3s;
            }
            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
            .header-wrapper {
                flex-wrap: wrap !important;
                align-items: center !important;
                gap: 10px !important;
                justify-content: space-between !important;
            }
            .header-controls {
                display: flex !important;
                flex-wrap: wrap;
                margin-left: auto;
                align-items: center;
                gap: 10px !important;
                justify-content: flex-end;
            }
            .header-title-container {
                display: flex;
                align-items: center;
                flex-shrink: 1;
                max-width: 100%;
            }
            .header-controls > .dropdown {
                flex-shrink: 0;
                position: relative;
            }
            .header-controls > .input-group,
            .header-controls > .btn-light {
                flex-shrink: 1;
                width: auto !important;
                min-width: 150px;
                max-width: 100%;
                margin-top: 0 !important;
            }
            .btn-sidebar-toggle {
                display: flex !important;
            }
        }

        /* Global Compact Table & Button */
        .table td,
        .table th {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.85rem;
        }

        .btn {
            padding: 0.3rem 0.8rem !important;
            font-size: 0.8rem !important;
        }

        .badge {
            padding: 0.35em 0.65em !important;
        }

        /* Global Modal Scrollable & Fits Screen */
        .modal-dialog {
            max-height: 95vh;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .modal-content {
            max-height: 95vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header,
        .modal-footer {
            flex-shrink: 0;
        }

        .modal-body {
            overflow-y: auto;
            padding: 1.25rem !important;
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* Fix: <form> inside .modal-content breaks flex scrolling */
        .modal-content > form {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
        }

        .modal-content > form > .modal-body {
            flex: 1 1 auto;
            min-height: 0;
        }

        .modal-content > form > .modal-footer {
            flex-shrink: 0;
        }
    </style>
</head>

<body>

    <div class="sidebar shadow-sm">
        <div class="p-4 mb-2">
            <a href="<?= site_url('pelatihan/admin/dashboard') ?>" class="text-decoration-none">
                <div class="d-flex align-items-center mb-0">
                    <div class="logo-box me-3">
                        <img src="<?= get_system_logo() ?>" alt="Logo" style="height: 40px; width: auto; object-fit: contain;">
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-white" style="letter-spacing: -0.5px; font-size: 1.1rem; line-height: 1;">SIM DIKLAT <span class="text-white">RSUD KOTA YOGYAKARTA</span></h6>
                    </div>
                </div>
            </a>
        </div>

        <div class="sidebar-content">
            <nav class="mt-2">
                <?php $cur = current_url(); ?>
                <a href="<?= site_url('pelatihan/admin/dashboard') ?>" class="nav-link-custom <?= strpos($cur, 'dashboard') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
                <div class="text-muted small fw-bold px-4 py-3 mt-2 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.2px; opacity: 0.5;">Manajemen Utama</div>
                <a href="<?= site_url('pelatihan/admin/akun_peserta') ?>" class="nav-link-custom <?= strpos($cur, 'akun_peserta') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-cog"></i> Manajemen Akun
                </a>
                <a href="<?= site_url('pelatihan/admin/pelatihan') ?>" class="nav-link-custom <?= strpos($cur, 'admin/pelatihan') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chalkboard-teacher"></i> Program Diklat
                </a>
                <a href="<?= site_url('pelatihan/admin/verifikasi_pendaftaran') ?>" class="nav-link-custom <?= strpos($cur, 'verifikasi_pendaftaran') !== false ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-check"></i> Verifikasi Pendaftaran
                </a>
                <a href="<?= site_url('pelatihan/admin/monitoring_peserta') ?>" class="nav-link-custom <?= (strpos($cur, 'monitoring_peserta') !== false || strpos($cur, 'presensi') !== false || strpos($cur, 'grading') !== false || strpos($cur, 'feedback') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-user-graduate"></i> Monitoring Progress
                </a>

                <div class="text-muted small fw-bold px-4 py-3 mt-2 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.2px; opacity: 0.5;">Output & Analitik</div>
                <a href="<?= site_url('pelatihan/admin/sertifikat') ?>" class="nav-link-custom <?= strpos($cur, 'admin/sertifikat') !== false ? 'active' : '' ?>">
                    <i class="fas fa-award"></i> Sertifikat
                </a>
                <a href="<?= site_url('pelatihan/admin/monitoring') ?>" class="nav-link-custom <?= strpos($cur, 'admin/monitoring') !== false && strpos($cur, 'monitoring_peserta') === false ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Analitik Diklat
                </a>

                <div class="text-muted small fw-bold px-4 py-3 mt-2 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.2px; opacity: 0.5;">Pengaturan Sistem</div>
                <a href="<?= site_url('pelatihan/admin/master/profesi') ?>" class="nav-link-custom <?= strpos($cur, 'master/profesi') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-md"></i> Daftar Profesi
                </a>
                <a href="<?= site_url('pelatihan/admin/master/ruangan') ?>" class="nav-link-custom <?= strpos($cur, 'master/ruangan') !== false ? 'active' : '' ?>">
                    <i class="fas fa-hospital-user"></i> Daftar Ruangan
                </a>

                <a href="<?= site_url('pelatihan/admin/master/kategori_evaluasi') ?>" class="nav-link-custom <?= strpos($cur, 'master/kategori_evaluasi') !== false ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> Kategori Evaluasi
                </a>
                <a href="<?= site_url('pelatihan/admin/master/kategori_skp') ?>" class="nav-link-custom <?= strpos($cur, 'master/kategori_skp') !== false ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> Ranah & Kategori SKP
                </a>
                <a href="<?= site_url('pelatihan/admin/master/narasumber') ?>" class="nav-link-custom <?= strpos($cur, 'master/narasumber') !== false ? 'active' : '' ?>">
                    <i class="fas fa-chalkboard-teacher"></i> Daftar Narasumber
                </a>
                <a href="<?= site_url('pelatihan/admin/master/penyelenggara') ?>" class="nav-link-custom <?= strpos($cur, 'master/penyelenggara') !== false ? 'active' : '' ?>">
                    <i class="fas fa-building"></i> Daftar Penyelenggara
                </a>
                <a href="<?= site_url('pelatihan/admin/pengaturan_logo') ?>" class="nav-link-custom <?= strpos($cur, 'pengaturan_logo') !== false ? 'active' : '' ?>">
                    <i class="fas fa-image"></i> Pengaturan Logo
                </a>

                <div class="text-muted small fw-bold px-4 py-3 mt-2 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.2px; opacity: 0.5;">Akun</div>
                <a href="<?= site_url('pelatihan/admin/profil') ?>" class="nav-link-custom <?= strpos($cur, 'profil') !== false && strpos($cur, 'admin/profil') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-circle"></i> Profil Saya
                </a>
            </nav>
        </div>

        <div class="p-3 border-top bg-white">
            <div class="d-flex align-items-center mb-3">
                <a href="<?= site_url('pelatihan/admin/profil') ?>" class="text-decoration-none d-flex align-items-center overflow-hidden" style="width: 100%;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('nama') ?? 'Admin') ?>&background=0f172a&color=fff" class="rounded-circle me-2 shadow-sm flex-shrink-0" width="36">
                    <div class="overflow-hidden" style="min-width: 0; max-width: calc(100% - 44px);">
                        <div class="fw-bold small text-white text-truncate" style="max-width: 100%;"><?= session()->get('nama') ?></div>
                        <div class="text-white opacity-75 fw-bold" style="font-size: 0.6rem; letter-spacing: 0.5px;">ADMIN DIKLAT</div>
                    </div>
                </a>
            </div>
            <a href="<?= site_url('pelatihan/admin/profil') ?>" class="btn btn-outline-dark btn-sm w-100 rounded-pill fw-bold mb-2" style="font-size: 0.75rem;">
                <i class="fas fa-user me-1"></i> PROFIL
            </a>
            <a href="<?= site_url('pelatihan/logout') ?>" class="btn btn-dark btn-sm w-100 rounded-pill fw-bold border-0" style="background: var(--primary-black) !important;">
                <i class="fas fa-sign-out-alt me-1 text-warning"></i> LOGOUT
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <div class="d-flex justify-content-between align-items-center mb-2 header-wrapper">
            <div class="header-title-container">
                <button class="btn-sidebar-toggle me-3" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div>
                    <h4 class="fw-bold mb-0 text-dark"><?= $title ?></h4>
                    <?php if (!isset($hide_default_breadcrumb) || !$hide_default_breadcrumb): ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                            <li class="breadcrumb-item"><a href="<?= site_url('pelatihan/admin/dashboard') ?>" class="text-decoration-none text-muted">Admin</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
                        </ol>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
            <div class="header-controls d-flex gap-3 align-items-center">
                <?php if (isset($title) && strpos(strtolower($title), 'dashboard') === false && strpos(strtolower($title), 'pengaturan logo') === false && strpos(strtolower($title), 'profil') === false): ?>
                    <?php
                    $placeholderText = 'Cari data...';
                    if (isset($title)) {
                        $tLower = strtolower($title);
                        if (strpos($tLower, 'akun') !== false || strpos($tLower, 'peserta') !== false) {
                            $placeholderText = 'Cari nama, NIK, atau instansi';
                        } elseif (strpos($tLower, 'pelatihan') !== false) {
                            $placeholderText = 'Cari nama pelatihan';
                        } elseif (strpos($tLower, 'sertifikat') !== false) {
                            $placeholderText = 'Cari sertifikat';
                        } elseif (strpos($tLower, 'profesi') !== false || strpos($tLower, 'ruangan') !== false || strpos($tLower, 'kategori') !== false) {
                            $placeholderText = 'Cari data master';
                        } else {
                            $placeholderText = 'Cari data ' . strtolower($title) . '';
                        }
                    }
                    ?>
                    <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden bg-white border" style="width: 250px;">
                        <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted small"></i></span>
                        <input type="text" id="globalSearchData" class="form-control border-0" placeholder="<?= esc($placeholderText) ?>">
                    </div>
                <?php endif; ?>

                <?php
                // Ambil notifikasi secara dinamis (sinkron dengan Dashboard)
                $db = \Config\Database::connect();
                $notifs = [];

                $p_pending = $db->table('peserta_pelatihan')->groupStart()->where('status_akses', 'Pending')->orWhere('status_pembayaran', 'Pending')->groupEnd()->countAllResults();
                if ($p_pending > 0) {
                    $notifs[] = ['title' => 'Registrasi baru menunggu verifikasi', 'message' => "$p_pending pendaftaran perlu dicek.", 'url' => base_url('pelatihan/admin/verifikasi_pendaftaran'), 'type' => 'danger', 'created_at' => date('Y-m-d H:i')];
                }

                $v_baru = $db->table('peserta_pelatihan')->groupStart()->where('status_akses', 'Pending')->orWhere('status_pembayaran', 'Pending')->groupEnd()->where('DATE(waktu_daftar) = ' . $db->escape(date('Y-m-d')), null, false)->countAllResults();
                if ($v_baru > 0) {
                    $notifs[] = ['title' => 'Verifikasi baru hari ini', 'message' => "$v_baru peserta masuk antrean hari ini.", 'url' => base_url('pelatihan/admin/verifikasi_pendaftaran'), 'type' => 'warning', 'created_at' => date('Y-m-d H:i')];
                }

                $s_masuk = $db->table('sertifikat_pelatihan')->where('verifikasi', 'pending')->countAllResults();
                if ($s_masuk > 0) {
                    $notifs[] = ['title' => 'Sertifikat masuk', 'message' => "$s_masuk sertifikat eksternal menunggu keputusan admin.", 'url' => base_url('pelatihan/admin/sertifikat'), 'type' => 'primary', 'created_at' => date('Y-m-d H:i')];
                }

                $sp_masuk = $db->table('sertifikat_pelatihan')->where('verifikasi', 'pending')->groupStart()->where('ranah', 'Pengabdian')->orWhere('jenis_dokumen', 'Pengabdian')->groupEnd()->countAllResults();
                if ($sp_masuk > 0) {
                    $notifs[] = ['title' => 'Sertifikat pengabdian masuk', 'message' => "$sp_masuk pengajuan pengabdian menunggu admin pengabdian.", 'url' => base_url('pelatihan/admin_pengabdian/sertifikat'), 'type' => 'success', 'created_at' => date('Y-m-d H:i')];
                }

                $activeTrainingsNotif = $db->table('master_pelatihan')->select('id, nama, jadwal_mulai, reg_tutup_tgl, kuota')->where('status', 'Aktif')->get()->getResultArray();
                foreach ($activeTrainingsNotif as $trn) {
                    $h_reg = !empty($trn['reg_tutup_tgl']) ? (int) floor((strtotime($trn['reg_tutup_tgl']) - strtotime(date('Y-m-d'))) / 86400) : null;
                    $h_mulai = !empty($trn['jadwal_mulai']) ? (int) floor((strtotime($trn['jadwal_mulai']) - strtotime(date('Y-m-d'))) / 86400) : null;
                    if ($h_reg !== null && $h_reg >= 0 && $h_reg <= 3) {
                        $notifs[] = ['title' => $h_reg === 0 ? 'Registrasi ditutup hari ini' : "$h_reg hari lagi registrasi tutup", 'message' => $trn['nama'], 'url' => base_url('pelatihan/admin/pelatihan/kelola/' . $trn['id']), 'type' => 'dark', 'created_at' => date('Y-m-d H:i')];
                    }
                    if ($h_mulai !== null && $h_mulai >= 0 && $h_mulai <= 3) {
                        $notifs[] = ['title' => $h_mulai === 0 ? 'Pelatihan dimulai hari ini' : "$h_mulai hari lagi pelatihan aktif", 'message' => $trn['nama'], 'url' => base_url('pelatihan/admin/pelatihan/kelola/' . $trn['id']), 'type' => 'danger', 'created_at' => date('Y-m-d H:i')];
                    }
                }

                $dbNotifs = $db->table('notifikasi_pelatihan')
                    ->where('user_id', 'admin')
                    ->orderBy('created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray();
                
                $unreadDb = 0;
                foreach($dbNotifs as $dn) {
                    if($dn['is_read'] == 0) $unreadDb++;
                    $notifs[] = [
                        'title' => $dn['title'],
                        'message' => $dn['message'],
                        'url' => base_url('pelatihan/admin/verifikasi_pendaftaran'), // Default url
                        'type' => $dn['type'] ?? 'primary',
                        'created_at' => date('Y-m-d H:i', strtotime($dn['created_at']))
                    ];
                }

                // Sort all notifications by created_at DESC
                usort($notifs, function($a, $b) {
                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                });

                $notifs = array_slice($notifs, 0, 8); // Limit to top 8 in dropdown
                $unread = $unreadDb > 0 ? $unreadDb : count($notifs);
                ?>
                <div class="position-relative dropdown">
                    <button class="btn btn-white btn-sm rounded-circle shadow-sm border p-2" type="button" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px;">
                        <i class="fas fa-bell text-muted"></i>
                    </button>
                    <?php if ($unread > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.5rem;"><?= $unread ?></span>
                    <?php endif; ?>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2" aria-labelledby="notifDropdown" style="width: 320px; max-height: 400px; overflow-y: auto;">
                        <li class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                            <strong class="text-dark mb-0">Notifikasi Terbaru</strong>
                            <?php if ($unread > 0): ?><span class="badge bg-danger rounded-pill"><?= $unread ?> Baru</span><?php endif; ?>
                        </li>
                        <?php if (empty($notifs)): ?>
                            <li class="p-4 text-center text-muted small">Belum ada notifikasi penting</li>
                        <?php else: ?>
                            <?php foreach ($notifs as $n): ?>
                                <li><a href="<?= esc($n['url']) ?>" class="dropdown-item border-bottom p-3 text-wrap">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-<?= $n['type'] ?? 'info' ?> rounded-circle p-2 text-white me-3 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; flex-shrink: 0;">
                                                <i class="fas fa-bell small"></i>
                                            </div>
                                            <div>
                                                <div class="small fw-bold text-dark mb-1"><?= esc($n['title']) ?></div>
                                                <div class="small text-muted" style="font-size: 0.75rem;"><?= esc($n['message']) ?></div>
                                                <div class="text-muted mt-2" style="font-size: 0.65rem;"><i class="far fa-clock me-1"></i> <?= esc($n['created_at']) ?></div>
                                            </div>
                                        </div>
                                    </a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li><a class="dropdown-item text-center small text-primary fw-bold py-3 bg-light" href="<?= base_url('pelatihan/admin/dashboard') ?>">Lihat Semua Notifikasi</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        window.showAdminPopupNotification = function(title, text, icon = 'info') {
            const popup = document.getElementById('adminNotificationPopup');
            const popupTitle = document.getElementById('adminPopupTitle');
            const popupMessage = document.getElementById('adminPopupMessage');

            popupTitle.innerHTML = title;
            popupMessage.innerHTML = text;
            popup.classList.add('show');
            clearTimeout(window.adminPopupTimer);
            window.adminPopupTimer = setTimeout(() => {
                popup.classList.remove('show');
            }, 5000);
        };

        window.hideAdminPopupNotification = function() {
            const popup = document.getElementById('adminNotificationPopup');
            popup.classList.remove('show');
        };

        window.notify = function(title, text, icon = 'success') {
            Swal.fire({
                title: `<span class="fw-bold fs-3 mt-3 d-block">${title === 'success' ? 'Berhasil' : title}</span>`,
                html: `<p class="text-muted fw-bold px-3">${text}</p>`,
                icon: icon,
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#c62828',
                padding: '2.5rem',
                buttonsStyling: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase'
                }
            });
        };

        window.confirmAction = function(title, text, callback) {
            Swal.fire({
                title: `<span class="fw-bold fs-3 mt-3 d-block">${title}</span>`,
                html: `<p class="text-muted fw-bold px-3">${text}</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#c62828',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase',
                    cancelButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase ms-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        };

        window.showToast = function(text, icon = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({
                icon: icon,
                title: text
            });
        };

        // Handle Session Flashdata
        <?php if (session()->getFlashdata('success')) : ?>
            window.notify('success', '<?= session()->getFlashdata('success') ?>');
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            window.notify('Gagal', '<?= session()->getFlashdata('error') ?>', 'error');
        <?php endif; ?>

        <?php if (session()->getFlashdata('warning')) : ?>
            window.notify('Peringatan', '<?= session()->getFlashdata('warning') ?>', 'warning');
        <?php endif; ?>

        // Global Simulation Alert removed

        // Auto-scroll sidebar to active menu
        $(document).ready(function() {
            var activeMenu = $('.sidebar-content .nav-link-custom.active');
            if (activeMenu.length) {
                var menuContainer = $('.sidebar-content');
                var scrollTo = activeMenu.offset().top - menuContainer.offset().top + menuContainer.scrollTop() - (menuContainer.height() / 2) + (activeMenu.outerHeight() / 2);
                // using animate for smooth scrolling
                menuContainer.animate({
                    scrollTop: scrollTo
                }, 300);
            }

            // Global Search Functionality
            $('#globalSearchData').on('keyup', function() {
                var value = $(this).val();

                // Cek apakah ada DataTable yang aktif di halaman ini
                var hasDataTable = false;
                if ($.fn.DataTable) {
                    $('table').each(function() {
                        if ($.fn.DataTable.isDataTable(this)) {
                            $(this).DataTable().search(value).draw();
                            hasDataTable = true;
                        }
                    });
                }

                // Jika bukan DataTable, gunakan fallback filter DOM manual
                if (!hasDataTable) {
                    var valueLower = value.toLowerCase();
                    var $tables = $('table tbody tr');

                    if ($tables.length > 0) {
                        $tables.filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(valueLower) > -1);
                        });
                    } else {
                        var $cards = $('.card, .card-item-searchable');
                        if ($cards.length > 0) {
                            $cards.filter(function() {
                                $(this).toggle($(this).text().toLowerCase().indexOf(valueLower) > -1);
                            });
                        }
                    }
                }
            });
        });

        function toggleSidebar() {
            $('.sidebar').toggleClass('active');
            $('.sidebar-overlay').toggleClass('active');
            if ($('.sidebar').hasClass('active')) {
                $('body').css('overflow', 'hidden');
            } else {
                $('body').css('overflow', '');
            }
        }
    </script>
    <?= $this->renderSection('scripts') ?>
    <!-- Global File Size Validation -->
    <script>
        document.addEventListener('submit', function(e) {
            let form = e.target;
            let fileInputs = form.querySelectorAll('input[type="file"]');
            let maxSize = 8 * 1024 * 1024; // 8MB
            let tooLarge = false;
            let fileName = '';

            for (let i = 0; i < fileInputs.length; i++) {
                let input = fileInputs[i];
                if (input.files && input.files.length > 0) {
                    for (let j = 0; j < input.files.length; j++) {
                        if (input.files[j].size > maxSize) {
                            tooLarge = true;
                            fileName = input.files[j].name;
                            break;
                        }
                    }
                }
                if (tooLarge) break;
            }

            if (tooLarge) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'File "' + fileName + '" melebihi batas maksimal 8MB. Silakan kompres atau pilih file yang lebih kecil.',
                        confirmButtonColor: '#ce2127',
                        customClass: { popup: 'rounded-4 shadow-lg border-0', confirmButton: 'rounded-pill px-5 py-2 fw-bold text-uppercase' }
                    });
                } else {
                    alert('File "' + fileName + '" terlalu besar! Maksimal ukuran file adalah 8MB.');
                }
            }
        });
    </script>
</body>

</html>