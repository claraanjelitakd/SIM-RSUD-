<?php

/**
 * @var array $unit_kerja
 * @var array $profesi
 */
?>
<?php helper('pelatihan'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - SIM-DIKLAT RSUD KOTA YOGYAKARTA</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= get_system_favicon() ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-red: #c62828;
            --primary-dark: #1e293b;
            --primary-yellow: #ffc107;
            --bg-light: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
            color: var(--primary-dark);
        }

        .register-container {
            display: flex;
            width: 100vw;
            height: 100vh;
            background: white;
            overflow: hidden;
        }

        .register-side-info {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #334155 100%);
            width: 400px;
            padding: 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            height: 100%;
            flex-shrink: 0;
        }

        .register-side-info::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, var(--primary-red) 0%, transparent 70%);
            opacity: 0.1;
            filter: blur(40px);
        }

        .register-form-side {
            flex: 1;
            padding: 2.5rem 4rem;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
            overflow-y: auto;
        }

        .logo-img {
            height: 80px;
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }

        .brand-title {
            font-weight: 800;
            font-size: 1.8rem;
            line-height: 1.2;
            margin-top: 2rem;
            letter-spacing: -0.5px;
        }

        .tagline {
            font-size: 0.85rem;
            opacity: 0.8;
            border-left: 3px solid var(--primary-yellow);
            padding-left: 1rem;
            margin-top: 1rem;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 10px 16px;
            border: 2px solid #f1f5f9;
            background-color: #ffffff;
            font-weight: 600;
            transition: all 0.3s;
            color: var(--primary-dark);
            font-size: 0.85rem;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #fff;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 4px rgba(206, 33, 39, 0.1);
            color: var(--primary-dark);
        }

        .section-title {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--primary-red);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f1f5f9;
            margin-left: 10px;
        }

        .btn-register-submit {
            background: var(--primary-red);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 800;
            color: white;
            transition: all 0.3s;
            box-shadow: 0 8px 16px rgba(206, 33, 39, 0.2);
            font-size: 0.9rem;
        }

        .btn-register-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(206, 33, 39, 0.3);
            background: #a51a1f;
        }

        .btn-check:checked+.btn-outline-red {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
            color: white;
            box-shadow: 0 6px 12px rgba(206, 33, 39, 0.15);
        }

        .btn-outline-red {
            color: var(--primary-red);
            border: 2px solid var(--primary-red);
            border-radius: 12px;
            padding: 8px 15px;
            font-weight: 700;
            transition: all 0.3s;
            font-size: 0.8rem;
        }

        .btn-outline-red:hover {
            background-color: #fff5f5;
            color: var(--primary-red);
            transform: translateY(-1px);
        }

        @media (max-width: 992px) {
            body {
                height: auto;
                min-height: 100vh;
                display: block;
                overflow-x: hidden;
                overflow-y: auto;
            }

            .register-container {
                display: block;
                width: 100%;
                height: auto;
                min-height: 100vh;
                overflow: visible;
            }

            .register-side-info {
                width: 100%;
                padding: 1.5rem;
                height: auto;
            }

            .register-form-side {
                padding: 1.5rem;
                height: auto;
                display: block;
            }
        }

        /* Figure-Ground Custom Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .modal-card {
            background: white;
            border-radius: 24px;
            padding: 2.5rem;
            width: 480px;
            max-width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            transform: scale(0.9);
            animation: modalIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        @keyframes modalIn {
            to {
                transform: scale(1);
            }
        }

        .animate-bounce {
            animation: bounce 1s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(-5%);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }

            50% {
                transform: none;
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }
    </style>
</head>

<body>

    <div class="register-container">
        <div class="register-side-info">
            <div>
                <img src="<?= get_system_logo() ?>" alt="Logo RSUD" class="logo-img" style="border-radius: 12px; height: 60px; width: auto; object-fit: contain;">
                <h1 class="brand-title">SIM DIKLAT<br><span style="color: var(--primary-yellow)">RSUD KOTA YOGYAKARTA</span></h1>
                <p class="tagline">Sistem Informasi Manajemen Pelatihan dan Pengembangan Kompetensi Pegawai.</p>
            </div>
            <div class="small opacity-50">
                &copy; 2026 RSUD Kota Yogyakarta.
            </div>
        </div>

        <div class="register-form-side">
            <!-- Figure-Ground Modal Error Alert -->
            <?php if (session()->getFlashdata('error')) : ?>
                <div id="errorModalOverlay" class="modal-overlay">
                    <div class="modal-card">
                        <div class="mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger animate-bounce"></i>
                        </div>
                        <h5 class="fw-extrabold text-dark mb-2">Registrasi Gagal</h5>
                        <p class="text-muted small mb-4"><?= session()->getFlashdata('error') ?></p>
                        <button type="button" class="btn btn-register-submit w-100" onclick="closeErrorModal()">
                            Mengerti
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <div id="registerHeader" class="mb-3 d-flex justify-content-between align-items-start">
                <div>
                    <h3 class="fw-bold text-dark">Registrasi Akun Peserta</h3>
                    <p class="text-muted small mb-0">Silakan lengkapi formulir di bawah ini untuk membuat akun portal diklat.</p>
                </div>

            </div>

            <form action="<?= base_url('pelatihan/auth/register') ?>" method="POST" id="registerForm">
                <div class="section-title">Jenis Peserta</div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="role" id="role_named" value="named" <?= old('role', 'named') === 'named' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-red w-100 d-flex align-items-center justify-content-center gap-2" for="role_named">
                            <i class="fas fa-user-md"></i>
                            <span>Tenaga Kesehatan (Named)</span>
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="role" id="role_nonnamed" value="nonnamed" <?= old('role') === 'nonnamed' ? 'checked' : '' ?>>
                        <label class="btn btn-outline-red w-100 d-flex align-items-center justify-content-center gap-2" for="role_nonnamed">
                            <i class="fas fa-users"></i>
                            <span>Umum (Non-Named)</span>
                        </label>
                    </div>
                </div>

                <div class="section-title">Informasi Dasar</div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">NAMA LENGKAP</label>
                        <input type="text" name="nama" class="form-control" placeholder="Sesuai KTP" value="<?= old('nama') ?>" required pattern="[A-Za-z\s.,']+" title="Nama hanya boleh berisi huruf, spasi, titik, koma, atau tanda kutip.">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">NIK (16 DIGIT)</label>
                        <input type="text" name="nik" class="form-control" placeholder="16 digit NIK" value="<?= old('nik') ?>" required pattern="[0-9]{16}" maxlength="16" inputmode="numeric" title="NIK harus berupa 16 digit angka murni.">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">EMAIL AKTIF</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="<?= old('email') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">NO. WHATSAPP</label>
                        <input type="tel" name="phone" class="form-control" placeholder="08123456789" value="<?= old('phone') ?>" required pattern="[0-9]{10,15}" maxlength="15" inputmode="numeric" title="Nomor WhatsApp harus berupa angka murni (10 s.d 15 digit).">
                    </div>
                </div>

                <div class="section-title" id="profesiSectionTitle">Informasi Profesi</div>
                <div class="row g-3 mb-3" id="profesiSectionFields">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">UNIT KERJA</label>
                        <select name="id_unit_kerja" id="id_unit_kerja" class="form-select" required>
                            <option value="" disabled selected>Pilih Unit Kerja</option>
                            <?php foreach ($unit_kerja as $uk): ?>
                                <option value="<?= $uk['id_unit_kerja'] ?>" <?= old('id_unit_kerja') == $uk['id_unit_kerja'] ? 'selected' : '' ?>><?= esc($uk['nama_unit']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">PROFESI</label>
                        <select name="id_profesi" id="id_profesi" class="form-select" required>
                            <option value="" disabled selected>Pilih Profesi</option>
                            <?php foreach ($profesi as $p): ?>
                                <option value="<?= $p['id_profesi'] ?>" <?= old('id_profesi') == $p['id_profesi'] ? 'selected' : '' ?>><?= esc($p['nama_profesi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="section-title">Keamanan Akun</div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">KATA SANDI</label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control" placeholder="Kombinasi angka & huruf saja" required pattern="^(?=.*[0-9])(?=.*[a-zA-Z])[a-zA-Z0-9]{8,}$" title="Password harus minimal 8 karakter berupa kombinasi huruf dan angka saja (tanpa simbol)." style="padding-right:46px;">
                            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent text-muted" onclick="togglePassword(this)" tabindex="-1"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold mb-1">KONFIRMASI KATA SANDI</label>
                        <div class="position-relative">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi kata sandi" required pattern="^(?=.*[0-9])(?=.*[a-zA-Z])[a-zA-Z0-9]{8,}$" title="Password harus minimal 8 karakter berupa kombinasi huruf dan angka saja (tanpa simbol)." style="padding-right:46px;">
                            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent text-muted" onclick="togglePassword(this)" tabindex="-1"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-register-submit w-100">
                        BUAT AKUN SEKARANG <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                    <div class="mt-2">
                        <p class="text-dark fw-bold small mb-0">Sudah memiliki akun? <a href="<?= base_url('pelatihan/login') ?>" class="text-danger text-decoration-none">Masuk di sini</a></p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const roleNamed = document.getElementById('role_named');
        const roleNonNamed = document.getElementById('role_nonnamed');
        const profesiSectionTitle = document.getElementById('profesiSectionTitle');
        const profesiSectionFields = document.getElementById('profesiSectionFields');
        const inputUnitKerja = document.getElementById('id_unit_kerja');
        const inputProfesi = document.getElementById('id_profesi');
        const registerForm = document.getElementById('registerForm');
        // Run on load
        document.addEventListener("DOMContentLoaded", function() {

            const nikInput = document.getElementsByName('nik')[0];
            const phoneInput = document.getElementsByName('phone')[0];
            const passwordInput = document.getElementsByName('password')[0];
            const confirmPasswordInput = document.getElementsByName('confirm_password')[0];

            // Real-time character block to reject non-digits on NIK
            if (nikInput) {
                nikInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            // Real-time character block to reject non-digits on No WA
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            // Real-time character block to reject non-alphanumeric (combination numbers/letters only) on password
            [passwordInput, confirmPasswordInput].forEach(input => {
                if (input) {
                    input.addEventListener('input', function(e) {
                        this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
                    });
                }
            });
        });

        // Simple frontend validation for password matching
        registerForm.addEventListener('submit', function(e) {
            const password = document.getElementsByName('password')[0].value;
            const confirmPassword = document.getElementsByName('confirm_password')[0].value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Kata sandi dan konfirmasi kata sandi tidak cocok!');
            }
        });

        function closeErrorModal() {
            const overlay = document.getElementById('errorModalOverlay');
            if (overlay) {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 300);
            }
        }

        function togglePassword(btn) {
            const input = btn.parentElement.querySelector('input');
            const icon  = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }


    </script>
</body>

</html>