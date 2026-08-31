<?php 
$title = $title ?? 'SIM-DIKLAT'; 
// Auto-update training status if end time has passed
try {
    $db = \Config\Database::connect();
    $db->query("UPDATE master_pelatihan SET status = 'Selesai' WHERE CONCAT(jadwal_selesai, ' ', COALESCE(jam_selesai, '23:59:59')) < NOW() AND status IN ('Publish', 'Aktif', 'Sedang Berjalan')");
} catch (\Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?> - SIM-DIKLAT RSUD KOTA YOGYAKARTA</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= get_system_favicon() ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-red: #c62828;
            --primary-black: #0f141b;
            --primary-dark: #0f172a;
            --bg-light: #ffffff;
            --surface: #ffffff;
            --surface-soft: #ffffff;
            --border-muted: #ffffff;
        }

        html {
            overflow-x: hidden;
            height: 100%;
            overscroll-behavior-y: none;
        }
        
        body {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: var(--primary-black);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-top: 100px;
        }
        
        .glass-wrapper-global {
            background: linear-gradient(135deg, rgba(80, 10, 15, 0.98) 0%, rgba(45, 5, 10, 0.98) 100%);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255,255,255,0.1);
            color: white;
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .glass-card-global {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border-radius: 16px;
            color: white;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .glass-breadcrumb {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(16px) !important;
            -webkit-backdrop-filter: blur(16px) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05) !important;
            position: sticky;
            top: 90px;
            z-index: 1010;
        }
        
        .glass-card-global:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.4);
            border-color: rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.15);
        }
        
        .btn-action-global {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        }
        
        .btn-action-global:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 25px rgba(0,0,0,0.2) !important;
            filter: brightness(1.15);
        }

        /* Visibility Fixes */
        .glass-wrapper-global .text-muted,
        .glass-card-global .text-muted,
        .glass-wrapper .text-muted,
        .glass-card .text-muted,
        .detail-header .text-muted {
            color: #ffffff !important;
            opacity: 0.85 !important;
        }

        .text-dark {
            color: var(--primary-black) !important;
        }

        .notification-popup {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 10800;
            width: min(360px, calc(100% - 3rem));
            background: rgba(15, 23, 42, 0.98);
            border: 1px solid rgba(206, 33, 39, 0.35);
            border-radius: 1.5rem;
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.25);
            color: white;
            overflow: hidden;
            backdrop-filter: blur(12px);
            transition: transform 0.25s ease, opacity 0.25s ease;
        }

        .notification-popup.d-none {
            display: none !important;
        }

        .notification-popup .popup-inner {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem 1rem 1rem 1.2rem;
        }

        .notification-popup .popup-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: rgba(206, 33, 39, 0.18);
            display: grid;
            place-items: center;
            color: #fffbfb;
            font-size: 1.2rem;
        }

        .notification-popup .popup-title {
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.2px;
            margin-bottom: 0.15rem;
            color: #ffffff !important;
        }

        .notification-popup .popup-message {
            font-size: 0.85rem;
            color: #ffffff !important;
            line-height: 1.5;
        }

        @keyframes bounceHighlight {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .highlight-bounce {
            animation: bounceHighlight 2s infinite ease-in-out;
        }

        .notification-popup .popup-close {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.75);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .notification-popup .popup-close:hover {
            color: #ffffff;
            transform: scale(1.05);
        }

        .navbar-custom {
            background: rgba(0, 0, 0, 0.95);
            padding: 0.85rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand img {
            height: 42px;
            width: auto;
            background: #ffffff;
            padding: 6px;
            border-radius: 12px;
            display: block;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.05) rotate(-2deg);
        }

        .logo-placeholder {
            height: 45px;
            width: 45px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-red);
            font-size: 1.5rem;
            box-shadow: inset 0 0 0 2px var(--primary-red);
        }

        .nav-link {
            font-weight: 700;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.65) !important;
            padding: 0.5rem 1.2rem !important;
            margin: 0 0.3rem;
            border-radius: 50rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.3px;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .btn-profile {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            border-radius: 12px;
            padding: 0.6rem 1.5rem !important;
            border: 2px solid rgba(255, 255, 255, 0.3);
            font-weight: 800;
            transition: all 0.3s;
        }

        .btn-profile:hover {
            background: var(--primary-red);
            border-color: var(--primary-red);
            transform: translateY(-2px);
        }

        .card {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            border-radius: 1.5rem;
        }

        .footer-custom {
            background: var(--primary-dark);
            color: white;
            padding: 2.5rem 0 1.5rem;
            margin-top: 1rem;
            border-top: 5px solid var(--primary-red);
        }

        /* Custom breadcrumb styling removed for text-only display */

        .breadcrumb-item a {
            color: #64748b;
            font-weight: 600;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: #cbd5e1;
        }

        /* Alert & Toast Styling (Capsule Style) */
        .alert-capsule {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 1rem 1.5rem;
            color: var(--primary-dark);
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            margin-bottom: 2rem;
            border-left: 6px solid var(--primary-red);
            animation: slideInUp 0.5s ease-out;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert-icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        /* Button Styling */
        .btn-primary-custom {
            background: var(--primary-red) !important;
            color: white !important;
            border: none !important;
            transition: all 0.3s ease;
            font-weight: 800;
            letter-spacing: 0.5px;
            border-radius: 50rem;
        }

        .btn-primary-custom:hover {
            background: #a51a1f !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(206, 33, 39, 0.4);
        }

        .btn-dark {
            background: var(--primary-black) !important;
            color: white !important;
            border: none !important;
            font-weight: 800;
            transition: all 0.3s ease;
            border-radius: 50rem;
        }

        .btn-dark:hover {
            background: #1e293b !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.3);
        }

        .btn-outline-custom {
            border: 2px solid var(--primary-red);
            color: var(--primary-red);
            font-weight: 800;
            transition: all 0.3s;
            border-radius: 50rem;
        }

        .btn-outline-custom:hover {
            background: var(--primary-red);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(206, 33, 39, 0.4);
        }

        .hover-text-danger {
            transition: all 0.3s ease;
            display: inline-block;
        }

        .hover-text-danger:hover {
            color: var(--primary-red) !important;
            transform: translateX(5px);
        }

        .footer-icon {
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .footer-icon:hover {
            background: var(--primary-red);
            color: white !important;
            transform: translateY(-3px) scale(1.1);
        }
        
        /* Mobile Responsive Enhancements */
        @media (max-width: 991.98px) {
            .navbar-nav .nav-item {
                margin-bottom: 0.5rem;
                width: 100%;
            }
            .navbar-nav .nav-link {
                padding: 0.8rem 1rem !important;
                text-align: left;
                width: 100%;
            }
            .btn-profile {
                width: 100%;
                display: block;
                margin-top: 0.5rem;
                text-align: left;
            }
            .navbar-brand img {
                height: 32px !important;
            }
            .navbar-brand span {
                font-size: 1rem !important;
            }
            body {
                padding-top: 90px;
            }
            .glass-wrapper-global {
                padding: 1.5rem 1rem !important;
                border-radius: 16px !important;
                margin-top: 0.5rem !important;
                margin-bottom: 1rem !important;
            }
            .glass-breadcrumb {
                top: 70px !important;
                font-size: 0.8rem !important;
                padding: 0.5rem 0.8rem !important;
            }
            /* Reset Belajar Sidebar Fixed position on Mobile */
            #sidebar-container {
                position: relative !important;
                height: auto !important;
                margin-bottom: 1.5rem !important;
                top: 0 !important;
                max-height: none !important;
            }
            #content-container {
                height: auto !important;
                overflow-y: visible !important;
            }
            .mobile-offcanvas {
                --bs-offcanvas-bg: transparent;
                background: linear-gradient(135deg, rgba(160, 20, 30, 0.90) 0%, rgba(90, 10, 15, 0.95) 100%) !important;
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
                border-left: 1px solid rgba(255,255,255,0.15) !important;
                box-shadow: -10px 0 30px rgba(0,0,0,0.6) !important;
            }
        }
        @media (max-width: 575.98px) {
            .course-img-wrapper {
                height: 160px !important;
            }
            h2, h3, h4 {
                font-size: 1.25rem !important;
            }
            h5 {
                font-size: 1.1rem !important;
            }
            .glass-wrapper-global {
                padding: 1.25rem 0.85rem !important;
                border-radius: 12px !important;
            }
            .btn {
                padding: 0.5rem 1rem !important;
                font-size: 0.85rem !important;
            }
            .badge {
                font-size: 0.7rem !important;
                padding: 0.35rem 0.65rem !important;
            }
            .table-responsive {
                border-radius: 12px;
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= site_url('pelatihan/peserta/beranda') ?>">
                <img src="<?= get_system_logo() ?>" alt="Logo" class="me-3" style="background: white; padding: 4px; border-radius: 12px; height: 40px;">
                <div>
                    <span class="fw-bold text-white mb-0 d-block" style="font-size: clamp(0.9rem, 3vw, 1.2rem); line-height: 1; letter-spacing: -0.5px;">SIM DIKLAT <span style="color: var(--primary-red);">RSUD KOTA YOGYAKARTA</span></span>
                </div>
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <i class="fas fa-bars"></i>
            </button>
            <!-- Offcanvas Navigation (Mobile) / Normal Navbar (Desktop) -->
            <div class="offcanvas-lg offcanvas-end mobile-offcanvas" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header border-bottom border-secondary border-opacity-50 d-lg-none" style="z-index: 1055; position: relative;">
                    <h5 class="offcanvas-title text-white fw-bold" id="offcanvasNavbarLabel">Menu Navigasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#offcanvasNavbar" onclick="try{ bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasNavbar')).hide(); }catch(e){}" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <?php
                    $uri = service('request')->getUri()->getPath();
                    $segments = explode('/', trim($uri, '/'));
                    
                    $isHomeActive = in_array('beranda', $segments) || in_array('dashboard', $segments) || (count($segments) > 0 && end($segments) == 'peserta');
                    $isKatalogActive = in_array('pembelajaran', $segments) && !in_array('pembelajaran_saya', $segments);
                    $isDiklatSayaActive = in_array('pembelajaran_saya', $segments) || in_array('belajar', $segments) || in_array('learn', $segments);
                    $isSertifikatActive = in_array('sertifikat_saya', $segments) || in_array('sertifikat', $segments) || in_array('unduh_sertifikat', $segments) || in_array('upload_sertifikat', $segments) || in_array('edit_sertifikat', $segments);
                    ?>
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item"><a class="nav-link <?= $isHomeActive ? 'active' : '' ?>" href="<?= site_url('pelatihan/peserta/beranda') ?>">Beranda</a></li>
                        <li class="nav-item"><a class="nav-link <?= $isKatalogActive ? 'active' : '' ?>" href="<?= site_url('pelatihan/peserta/pembelajaran') ?>">Katalog</a></li>
                        <li class="nav-item"><a class="nav-link <?= $isDiklatSayaActive ? 'active' : '' ?>" href="<?= site_url('pelatihan/peserta/pembelajaran_saya') ?>">Diklat Saya</a></li>
                        <li class="nav-item"><a class="nav-link <?= $isSertifikatActive ? 'active' : '' ?>" href="<?= site_url('pelatihan/peserta/sertifikat_saya') ?>">Sertifikat</a></li>
                        <li class="nav-item dropdown ms-lg-3 mt-3 mt-lg-0 border-top border-lg-0 pt-3 pt-lg-0" style="border-color: rgba(255,255,255,0.1) !important;">
                            <?php
                            $notifCount = 0;
                            if (session()->get('user_id')) {
                                $db = \Config\Database::connect();
                                $notifCount = $db->table('notifikasi_pelatihan')->where('user_id', session()->get('user_id'))->where('is_read', 0)->countAllResults();
                            }
                            ?>
                            <a class="nav-link dropdown-toggle btn-profile px-4 shadow-sm position-relative d-inline-block" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2 text-warning"></i> <?= session()->get('nama') ?>
                                <?php if ($notifCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">New alerts</span>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow mt-2 p-2 rounded-3" style="min-width: 200px;">
                                <li><a class="dropdown-item py-2 px-3 rounded-2 fw-semibold small mb-1 text-secondary" href="<?= site_url('pelatihan/peserta/profil') ?>"><i class="far fa-user me-2" style="width: 16px; text-align: center;"></i> Profil Saya</a></li>
                                <li>
                                    <a class="dropdown-item py-2 px-3 rounded-2 fw-semibold small mb-1 d-flex justify-content-between align-items-center text-secondary" href="<?= site_url('pelatihan/peserta/notifikasi') ?>">
                                        <span><i class="far fa-bell me-2" style="width: 16px; text-align: center;"></i> Notifikasi</span>
                                        <?php if ($notifCount > 0): ?>
                                            <span class="badge bg-danger rounded-pill"><?= $notifCount ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider my-1 border-secondary border-opacity-25">
                                </li>
                                <li><a class="dropdown-item py-2 px-3 rounded-2 fw-semibold small text-danger" href="<?= site_url('pelatihan/logout') ?>"><i class="fas fa-sign-out-alt me-2" style="width: 16px; text-align: center;"></i> Keluar</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="notification-popup d-none" id="globalNotificationPopup" role="alert" aria-live="polite" aria-atomic="true">
        <div class="popup-inner">
            <div class="popup-icon"><i class="fas fa-bell"></i></div>
            <div>
                <div class="popup-title" id="popupTitle">Notifikasi</div>
                <div class="popup-message" id="popupMessage">Pesan notifikasi akan muncul di sini.</div>
            </div>
            <button type="button" class="popup-close" onclick="hidePopupNotification()" aria-label="Tutup notifikasi">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <?php 
        $isKatalogDetail = in_array('detail_pelatihan', $segments);
        $isKatalogIndex = in_array('pembelajaran', $segments) && !in_array('pembelajaran_saya', $segments);
        $isDiklatSayaDetail = in_array('belajar', $segments) || in_array('learn', $segments) || in_array('materi', $segments) || in_array('kuis', $segments) || in_array('evaluasi', $segments);
        $isSertifikatDetail = in_array('upload_sertifikat', $segments) || in_array('edit_sertifikat', $segments) || in_array('unduh_sertifikat', $segments);
        
        $isFullWidth = true;
        
        $isMainTab = $isHomeActive || ($isKatalogActive && !$isKatalogDetail) || ($isDiklatSayaActive && !$isDiklatSayaDetail) || ($isSertifikatActive && !$isSertifikatDetail) || in_array('profil', $segments) || in_array('notifikasi', $segments);
        
        // Hide breadcrumb in layout for Katalog Detail so we can render it custom in the view
        $hideBreadcrumb = $isMainTab || $isKatalogDetail || $isDiklatSayaDetail;
    ?>
    <div class="<?= $isFullWidth ? 'container-fluid px-0' : 'container' ?> pt-3 pb-0" style="flex: 1 0 auto;">
        <div class="row m-0">
            <div class="col-12 <?= $isFullWidth ? 'px-4 px-lg-5' : '' ?>">
                <?php if (!$hideBreadcrumb): ?>
                <nav aria-label="breadcrumb" class="shadow-sm px-4 py-2 mb-4 d-inline-block" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 50rem;">
                    <ol class="breadcrumb mb-0 align-items-center">
                            <?php if ($isKatalogDetail): ?>
                                <li class="breadcrumb-item"><a href="<?= site_url('pelatihan/peserta/pembelajaran') ?>" class="text-decoration-none text-muted fw-bold hover-text-danger" style="font-size: 0.85rem;"><i class="fas fa-home me-1"></i> KATALOG</a></li>
                            <?php elseif ($isDiklatSayaDetail): ?>
                                <li class="breadcrumb-item"><a href="<?= site_url('pelatihan/peserta/pembelajaran_saya') ?>" class="text-decoration-none text-muted fw-bold hover-text-danger" style="font-size: 0.85rem;"><i class="fas fa-book-open me-1"></i> DIKLAT SAYA</a></li>
                            <?php elseif ($isSertifikatDetail): ?>
                                <li class="breadcrumb-item"><a href="<?= site_url('pelatihan/peserta/sertifikat_saya') ?>" class="text-decoration-none text-muted fw-bold hover-text-danger" style="font-size: 0.85rem;"><i class="fas fa-certificate me-1"></i> SERTIFIKAT SAYA</a></li>
                            <?php endif; ?>

                            <?php if (in_array('detail_pelatihan', $segments)): ?>
                                <li class="breadcrumb-item active fw-bolder text-dark" aria-current="page" style="font-size: 0.85rem;">DETAIL PELATIHAN</li>
                            <?php elseif (in_array('belajar', $segments) || in_array('learn', $segments)): ?>
                                <li class="breadcrumb-item active fw-bolder text-dark" aria-current="page" style="font-size: 0.85rem;">PROSES BELAJAR</li>
                            <?php elseif (in_array('upload_sertifikat', $segments)): ?>
                                <li class="breadcrumb-item active fw-bolder text-dark" aria-current="page" style="font-size: 0.85rem;">UPLOAD SERTIFIKAT</li>
                            <?php elseif (in_array('edit_sertifikat', $segments)): ?>
                                <li class="breadcrumb-item active fw-bolder text-dark" aria-current="page" style="font-size: 0.85rem;">EDIT SERTIFIKAT</li>
                            <?php else: ?>
                                <li class="breadcrumb-item active fw-bolder text-dark" aria-current="page" style="font-size: 0.85rem;"><?= strtoupper($title) ?></li>
                            <?php endif; ?>
                    </ol>
                </nav>
                <?php endif; ?>
            </div>
        </div>

        <?= $this->renderSection('content') ?>
    </div>

    <footer class="text-white pt-4 pb-3 mt-auto" style="background-color: #000000; z-index: 1020; flex-shrink: 0;">
        <div class="container">
            <div class="row g-4 mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-4">
                        <img src="<?= get_system_logo() ?>" alt="Logo" height="60" class="bg-white p-2 rounded-lg me-3">
                        <div>
                            <h4 class="fw-bold mb-0 text-white">SIM DIKLAT <span style="color: var(--primary-red);">RSUD KOTA YOGYAKARTA</span></h4>
                        </div>
                    </div>
                    <p class="small opacity-75 pe-md-5 lh-lg">Sistem Informasi Manajemen Pelatihan Terpadu untuk pengembangan kompetensi berkelanjutan bagi seluruh tenaga medis dan non-medis RSUD Kota Yogyakarta.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-4 text-uppercase small letter-spacing-1">Tautan Cepat</h6>
                    <ul class="list-unstyled small opacity-75">
                        <li class="mb-3"><a href="<?= site_url('pelatihan/peserta/beranda') ?>" class="text-white text-decoration-none hover-text-danger">Beranda</a></li>
                        <li class="mb-3"><a href="<?= site_url('pelatihan/peserta/pembelajaran') ?>" class="text-white text-decoration-none hover-text-danger">Katalog</a></li>
                        <li class="mb-3"><a href="<?= site_url('pelatihan/peserta/pembelajaran_saya') ?>" class="text-white text-decoration-none hover-text-danger">Diklat Saya</a></li>
                        <li class="mb-3"><a href="<?= site_url('pelatihan/peserta/sertifikat_saya') ?>" class="text-white text-decoration-none hover-text-danger">Sertifikat</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-4 text-uppercase small letter-spacing-1">Hubungi Kami</h6>
                    <div class="d-flex gap-3 mb-3 align-items-center">
                        <div class="footer-icon"><i class="fas fa-map-marker-alt text-danger"></i></div>
                        <p class="small opacity-75 mb-0" style="transition: all 0.3s ease;">Jl. Wirosaban No.1, Sorosutan, Kota Yogyakarta</p>
                    </div>
                    <div class="d-flex gap-3 mb-3 align-items-center">
                        <div class="footer-icon"><i class="fas fa-phone text-danger"></i></div>
                        <p class="small opacity-75 mb-0" style="transition: all 0.3s ease;">(0274) 371150</p>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="footer-icon"><i class="fas fa-envelope text-danger"></i></div>
                        <p class="small opacity-75 mb-0" style="transition: all 0.3s ease;">diklat@rsud.jogjakota.go.id</p>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-white opacity-10">
            <div class="d-flex justify-content-between align-items-center small opacity-50">
                <div>&copy; 2026 RSUD Kota Yogyakarta.</div>
                <div class="d-flex gap-4">
                    <a href="#" class="text-white text-decoration-none hover-text-danger">Privacy Policy</a>
                    <a href="#" class="text-white text-decoration-none hover-text-danger">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Auto refresh every 60 seconds to keep data valid and updated
        setTimeout(() => {
            window.location.reload();
        }, 60000);

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
                padding: '2.5rem',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'rounded-pill px-4 py-2 fw-bold',
                    cancelButton: 'rounded-pill px-4 py-2 fw-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) callback();
            });
        };

        window.showPopupNotification = function(title, text, icon = 'info') {
            const popup = document.getElementById('globalNotificationPopup');
            const popupTitle = document.getElementById('popupTitle');
            const popupMessage = document.getElementById('popupMessage');

            popupTitle.innerHTML = title;
            popupMessage.innerHTML = text;
            popup.classList.remove('d-none');
            popup.style.transform = 'translateY(0)';
            popup.style.opacity = '1';

            if (window._popupTimeout) {
                clearTimeout(window._popupTimeout);
            }
            window._popupTimeout = setTimeout(() => {
                hidePopupNotification();
            }, 4500);
        };

        window.hidePopupNotification = function() {
            const popup = document.getElementById('globalNotificationPopup');
            if (!popup) return;
            popup.style.transform = 'translateY(-20px)';
            popup.style.opacity = '0';
            setTimeout(() => popup.classList.add('d-none'), 250);
        };

        // Handle Session Flashdata
        <?php if (session()->getFlashdata('success')) : ?>
            window.showPopupNotification('Berhasil!', '<?= session()->getFlashdata('success') ?>', 'success');
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            window.showPopupNotification('Gagal!', '<?= session()->getFlashdata('error') ?>', 'error');
        <?php endif; ?>

        <?php if (session()->getFlashdata('warning')) : ?>
            window.showPopupNotification('Peringatan!', '<?= session()->getFlashdata('warning') ?>', 'warning');
        <?php endif; ?>

        // Prevent default for dummy links
        $(document).on('click', 'a[href="#"]', function(e) {
            e.preventDefault();
        });
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