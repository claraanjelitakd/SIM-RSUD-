<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --primary-red: #6e0f19;
        --accent-yellow: #ffb300;
        --teal-kemenkes: #00bfa5;
        --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .profile-card {
        background: linear-gradient(135deg, rgba(110, 15, 25, 0.95) 0%, #4a0a11 100%);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.2);
        overflow: hidden;
    }

    .profile-header {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        height: 100px;
        position: relative;
    }

    .profile-avatar-container {
        position: relative;
        width: 100%;
        display: flex;
        justify-content: center;
        margin-top: -60px;
        margin-bottom: 15px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 30px;
        border: 5px solid white;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        object-fit: cover;
    }

    .target-card {
        background: rgba(0,0,0,0.2);
        border-radius: 20px;
        padding: 20px;
        border: none;
    }

    .progress-custom {
        height: 12px;
        border-radius: 6px;
        background-color: rgba(255,255,255,0.2);
        margin: 15px 0;
    }

    .progress-bar-custom {
        background: linear-gradient(90deg, #ffb300, #ffca28);
        border-radius: 6px;
    }

    .form-control-custom {
        border-radius: 12px;
        padding: 10px 15px;
        border: 1px solid rgba(255,255,255,0.3);
        background-color: rgba(255,255,255,0.1) !important;
        color: white;
        transition: all 0.3s ease;
    }

    .form-control-custom:focus {
        border-color: #ffb300;
        box-shadow: 0 0 0 4px rgba(255, 179, 0, 0.2);
        color: white;
        background-color: rgba(255,255,255,0.2) !important;
    }
    
    .form-control-custom option {
        color: #333;
    }

    .form-control-custom:read-only, .form-control-custom:disabled {
        background-color: rgba(255,255,255,0.05) !important;
        cursor: text;
        color: rgba(255,255,255,0.7);
    }

    .section-title {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.8);
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title::after {
        content: '';
        flex-grow: 1;
        height: 1px;
        background: rgba(255,255,255,0.2);
    }

    .btn-save {
        background-color: #ffb300;
        color: #000;
        border: none;
        padding: 10px 30px;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(255, 179, 0, 0.3);
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background-color: #ffa000;
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(255, 179, 0, 0.4);
    }
</style>

<div class="container-fluid pt-1 pb-4">
    <div class="row g-4">
        <!-- Sidebar Profile -->
        <div class="col-lg-4">
            <div class="profile-card mb-4">
                <div class="profile-header"></div>
                <div class="profile-avatar-container">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['nama_lengkap'] ?? 'Admin') ?>&background=6e0f19&color=fff&size=128" class="profile-avatar">
                </div>
                <div class="text-center px-4 text-white">
                    <h5 class="fw-bold mb-1 text-white"><?= $user['nama_lengkap'] ?? 'Admin' ?></h5>
                    <p class="opacity-75 small text-white-50 mb-0"><?= $user['nama_profesi'] ?? 'Admin Diklat' ?> • <?= $user['nama_unit'] ?? 'RSUD Kota Yogyakarta' ?></p>
                </div>
                
                <div class="p-4 pt-3">
                    <?php if ($user['role'] === 'peserta'): ?>
                    <div class="target-card mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-white"><i class="fas fa-bullseye me-2 text-danger"></i> Target Tahunan</h6>
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3"><?= date('Y') ?></span>
                        </div>
                        
                        <?php 
                            $target = $user['target_jpl'] ?? 20;
                            $current = $user['capaian_jpl'] ?? 0;
                            $percent = $target > 0 ? min(100, ($current / $target) * 100) : 0;
                        ?>

                        <div class="progress" style="height: 12px; border-radius: 6px; margin: 15px 0; background-color: #e2e8f0;">
                            <div class="progress-bar progress-bar-custom" role="progressbar" style="width: <?= $percent ?>%"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between small">
                            <span class="text-white-50">Progres JPL</span>
                            <span class="fw-bold text-white"><?= $current ?> / <?= $target ?> JPL</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="list-group list-group-flush border-0">
                        <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex align-items-center gap-3">
                            <div class="p-2 rounded-lg text-primary"><i class="fas fa-id-card fa-fw"></i></div>
                            <div>
                                <small class="text-white-50 d-block">NIK</small>
                                <span class="fw-bold text-white"><?= $user['nik'] ?? '-' ?></span>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex align-items-center gap-3">
                            <div class="p-2 rounded-lg text-success"><i class="fas fa-envelope fa-fw"></i></div>
                            <div>
                                <small class="text-white-50 d-block">Email</small>
                                <span class="fw-bold d-inline-block text-break text-white" style="max-width: 200px; line-height: 1.2;"><?= $user['email'] ?? '-' ?></span>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex align-items-center gap-3">
                            <div class="p-2 rounded-lg text-warning"><i class="fas fa-shield-alt fa-fw"></i></div>
                            <div>
                                <small class="text-white-50 d-block">Role</small>
                                <span class="fw-bold text-uppercase text-white" style="font-size: 0.75rem;"><?= $user['role'] ?? '-' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="col-lg-8">
            <div class="profile-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Pengaturan Profil</h4>
                        <p class="text-white-50 small mb-0">Perbarui informasi diri dan kontak Anda secara berkala.</p>
                    </div>
                    <i class="fas fa-user-cog fa-3x text-white opacity-25"></i>
                </div>

                <form action="<?= base_url('pelatihan/admin/profil/update') ?>" method="POST" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <!-- Personal Info -->
                    <div class="section-title"><i class="fas fa-user me-1"></i> Data Personal</div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">NAMA LENGKAP</label>
                            <input type="text" name="nama_lengkap" class="form-control form-control-custom" value="<?= $user['nama_lengkap'] ?? '' ?>" required pattern="[A-Za-z\s\.,']+" title="Nama hanya boleh mengandung huruf, spasi, titik, koma, atau tanda kutip tunggal.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">NIK (16 DIGIT)</label>
                            <input type="text" name="nik" class="form-control form-control-custom" value="<?= $user['nik'] ?? '' ?>" readonly title="NIK tidak dapat diubah.">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">EMAIL AKTIF</label>
                            <input type="email" name="email" class="form-control form-control-custom" value="<?= $user['email'] ?? '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">NO. WHATSAPP</label>
                            <input type="tel" name="no_wa" class="form-control form-control-custom" value="<?= $user['no_wa'] ?? '' ?>" required pattern="[0-9]{10,15}" maxlength="15" inputmode="numeric" title="Nomor WhatsApp harus berupa angka murni (10 s.d 15 digit).">
                        </div>
                    </div>

                    <!-- Employment Info -->
                    <div class="section-title"><i class="fas fa-briefcase me-1"></i> Informasi Profesi</div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">UNIT KERJA</label>
                            <select name="id_unit_kerja" class="form-select form-control-custom">
                                <option value="" disabled selected>Pilih Unit Kerja...</option>
                                <?php foreach ($unit_kerja ?? [] as $uk) : ?>
                                    <option value="<?= $uk['id_unit_kerja'] ?>" <?= (($user['id_unit_kerja'] ?? '') == $uk['id_unit_kerja']) ? 'selected' : '' ?>><?= esc($uk['nama_unit']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">PROFESI</label>
                            <select name="id_profesi" class="form-select form-control-custom">
                                <option value="" disabled selected>Pilih Profesi...</option>
                                <?php foreach ($profesi ?? [] as $p) : ?>
                                    <option value="<?= $p['id_profesi'] ?>" <?= (($user['id_profesi'] ?? '') == $p['id_profesi']) ? 'selected' : '' ?>><?= esc($p['nama_profesi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="section-title"><i class="fas fa-shield-alt me-1"></i> Keamanan Akun</div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-12">
                            <div class="mb-2 text-white small fw-bold fst-italic">Kosongkan sandi jika tidak ingin diubah.</div>
                            <label class="form-label small fw-bold text-white mb-1">KATA SANDI BARU</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control form-control-custom" minlength="8" placeholder="Minimal 8 karakter..." pattern="^(?=.*[0-9])(?=.*[a-zA-Z])[a-zA-Z0-9]+$" title="Password harus mengandung kombinasi huruf dan angka (tanpa spasi/simbol).">
                            </div>
                            <div class="form-text text-white" style="font-size: 0.75rem;">Min. 8 karakter, kombinasi angka & huruf.</div>
                        </div>
                    </div>

                    <div class="text-end border-top pt-4">
                        <button type="submit" class="btn btn-save text-white">SIMPAN PERUBAHAN <i class="fas fa-check-circle ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
