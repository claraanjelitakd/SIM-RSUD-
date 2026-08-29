<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Kelola Sertifikat Pengabdian' ?> - SIM-DIKLAT RSUD KOTA YOGYAKARTA</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= get_system_favicon() ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
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
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.65);
            text-decoration: none;
            border-radius: 50rem;
            margin: 0.2rem 1rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 700;
            font-size: 0.85rem;
        }

        .nav-link-custom i {
            width: 22px;
            margin-right: 12px;
            font-size: 1rem;
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
            .header-controls > .dropdown,
            .header-controls > .btn-light:first-child,
            .header-controls > a.btn {
                flex-shrink: 0;
                position: relative !important;
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
    </style>
</head>

<body>

    <div class="sidebar shadow-sm">
        <div class="p-4 mb-2">
            <div class="d-flex align-items-center mb-0">
                <div class="logo-box me-3">
                    <img src="<?= get_system_logo() ?>" alt="Logo" style="height: 40px; width: auto; object-fit: contain;">
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-white" style="letter-spacing: -0.5px; font-size: 1.1rem; line-height: 1;">SIM DIKLAT <span class="text-white">RSUD KOTA YOGYAKARTA</span></h5>
                </div>
            </div>
        </div>

        <div class="sidebar-content">
            <nav class="mt-2">
                <!-- Dashboard (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-chart-pie"></i> Dashboard
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>

                <div class="text-muted small fw-bold px-4 py-3 mt-2 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.2px; opacity: 0.5;">Manajemen Utama</div>

                <!-- Program Diklat (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-chalkboard-teacher"></i> Program Diklat
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>
                <!-- Tambah Peserta (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-user-plus"></i> Tambah Peserta
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>
                <!-- Monitoring Progress (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-user-graduate"></i> Monitoring Progress
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>
                <!-- Manajemen Akun (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-user-cog"></i> Manajemen Akun
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>

                <div class="text-muted small fw-bold px-4 py-3 mt-2 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.2px; opacity: 0.5;">Keuangan & Output</div>

                <!-- Verifikasi Biaya (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-wallet"></i> Verifikasi Biaya
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>
                <!-- Sertifikat & SKP (Active) -->
                <a href="<?= base_url('pelatihan/admin_pengabdian/sertifikat') ?>" class="nav-link-custom active">
                    <i class="fas fa-award"></i> Sertifikat Pengabdian
                    <?php if (($pending_pengabdian ?? 0) > 0): ?>
                        <span class="badge bg-danger rounded-pill ms-auto"><?= number_format($pending_pengabdian ?? 0) ?></span>
                    <?php endif; ?>
                </a>
                <!-- Analitik Diklat (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-chart-line"></i> Analitik Diklat
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>
                <!-- Persetujuan Akses (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-user-check"></i> Persetujuan Akses
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>

                <div class="text-muted small fw-bold px-4 py-3 mt-2 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.2px; opacity: 0.5;">Evaluasi & Laporan</div>

                <!-- Nilai Post-test (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-poll"></i> Nilai Post-test
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>
                <!-- Feedback Pelatihan (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-comment-dots"></i> Feedback Pelatihan
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>

                <div class="text-muted small fw-bold px-4 py-3 mt-2 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.2px; opacity: 0.5;">Pengaturan Sistem</div>

                <!-- Daftar Profesi (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-user-md"></i> Daftar Profesi
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>
                <!-- Daftar Ruangan (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-hospital-user"></i> Daftar Ruangan
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>
                <!-- Daftar Instansi (Locked) -->
                <a href="#" onclick="event.preventDefault(); return false;" class="nav-link-custom disabled opacity-50 text-muted" style="pointer-events: none;">
                    <i class="fas fa-building"></i> Daftar Instansi
                    <i class="fas fa-lock ms-auto text-danger small" style="font-size: 0.65rem;"></i>
                </a>

                <div class="text-muted small fw-bold px-4 py-3 mt-2 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1.2px; opacity: 0.5;">Akun</div>
                <?php $cur = current_url(); ?>
                <a href="<?= base_url('pelatihan/admin_pengabdian/profil') ?>" class="nav-link-custom <?= strpos($cur, 'profil') !== false ? 'active' : '' ?>">
                    <i class="fas fa-user-circle"></i> Profil Saya
                </a>
            </nav>
        </div>

        <div class="p-3 border-top bg-white">
            <div class="d-flex align-items-center mb-3">
                <a href="<?= base_url('pelatihan/admin_pengabdian/profil') ?>" class="text-decoration-none d-flex align-items-center overflow-hidden" style="width: 100%;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('nama') ?? 'Admin Pengabdian') ?>&background=c62828&color=fff" class="rounded-circle me-2 shadow-sm flex-shrink-0" width="36">
                    <div class="overflow-hidden" style="min-width: 0; max-width: calc(100% - 44px);">
                        <div class="fw-bold small text-white text-truncate" style="max-width: 100%;"><?= session()->get('nama') ?? 'Admin Pengabdian' ?></div>
                        <div class="text-white opacity-75 fw-bold" style="font-size: 0.6rem; letter-spacing: 0.5px;">ADMIN PENGABDIAN</div>
                    </div>
                </a>
            </div>
            <a href="<?= base_url('pelatihan/admin_pengabdian/profil') ?>" class="btn btn-outline-dark btn-sm w-100 rounded-pill fw-bold mb-2" style="font-size: 0.75rem;">
                <i class="fas fa-user me-1"></i> PROFIL
            </a>
            <a href="<?= base_url('pelatihan/logout') ?>" class="btn btn-dark btn-sm w-100 rounded-pill fw-bold border-0" style="background: var(--primary-black) !important;">
                <i class="fas fa-sign-out-alt me-1 text-warning"></i> LOGOUT
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
        <div class="d-flex justify-content-between align-items-center mb-3 header-wrapper">
            <div class="header-title-container">
                <button class="btn-sidebar-toggle me-3" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div>
                    <h4 class="fw-bold mb-0 text-dark"><?= $title ?? 'Kelola Sertifikat Pengabdian' ?></h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">Admin Pengabdian</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $title ?? 'Kelola Sertifikat Pengabdian' ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="header-controls d-flex gap-3 align-items-center">
                <?php if (!isset($title) || strpos(strtolower($title), 'profil') === false): ?>
                <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden bg-white border" style="width: 250px;">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted small"></i></span>
                    <input type="text" id="globalSearchData" class="form-control border-0" placeholder="Cari nama peserta, judul, status...">
                </div>
                <?php endif; ?>
                <a href="<?= base_url('pelatihan/admin_pengabdian/sertifikat') ?>" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-dark">
                    Sertifikat pending
                    <span class="badge bg-danger ms-1"><?= number_format($pending_pengabdian ?? 0) ?></span>
                </a>
            </div>
        </div>

        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
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
            }).then((result) => { if (result.isConfirmed) callback(); });
        };

        <?php if (session()->getFlashdata('success')): ?>
            window.notify('success', '<?= session()->getFlashdata('success') ?>');
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            window.notify('Gagal', '<?= session()->getFlashdata('error') ?>', 'error');
        <?php endif; ?>
        <?php if (session()->getFlashdata('warning')): ?>
            window.notify('Peringatan', '<?= session()->getFlashdata('warning') ?>', 'warning');
        <?php endif; ?>

        $(document).ready(function() {
            var activeMenu = $('.sidebar-content .nav-link-custom.active');
            if (activeMenu.length) {
                var menuContainer = $('.sidebar-content');
                var scrollTo = activeMenu.offset().top - menuContainer.offset().top + menuContainer.scrollTop() - (menuContainer.height() / 2) + (activeMenu.outerHeight() / 2);
                menuContainer.animate({ scrollTop: scrollTo }, 300);
            }

            $('#globalSearchData').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                if ($('table tbody tr').length > 0) {
                    $('table tbody tr').filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
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
