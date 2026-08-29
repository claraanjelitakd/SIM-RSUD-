<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --primary-red: #800000;
        --accent-yellow: #ffb300;
        --teal-kemenkes: #00bfa5;
        --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .profile-card {
        /* replaced with glass-card-global inline */
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
        background: #f8fafc;
        border-radius: 20px;
        padding: 20px;
        border: 1px solid #e2e8f0;
    }

    .progress-custom {
        height: 12px;
        border-radius: 6px;
        background-color: #e2e8f0;
        margin: 15px 0;
    }

    .progress-bar-custom {
        background: linear-gradient(90deg, var(--teal-kemenkes), #00e676);
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
        border-color: #f59e0b;
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        background-color: rgba(255,255,255,0.15) !important;
        color: white;
    }
    
    .form-control-custom:read-only, .form-control-custom:disabled {
        background-color: rgba(255,255,255,0.05) !important;
        cursor: text;
        color: rgba(255,255,255,0.7);
    }
    
    .form-control-custom option {
        color: #0f141b;
        background: white;
    }

    .section-title {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.7);
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
        background-color: var(--primary-red);
        border: none;
        padding: 10px 30px;
        border-radius: 12px;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(198, 40, 40, 0.2);
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        background-color: #a51d1d;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(198, 40, 40, 0.3);
    }
</style>

<div class="pt-1 mb-5 glass-wrapper-global">
    <div class="row g-4">
        <!-- Sidebar Profile -->
        <div class="col-lg-4">
            <div class="glass-card-global mb-4 overflow-hidden">
                <div class="profile-header"></div>
                <div class="profile-avatar-container">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['nama_lengkap'] ?? 'User') ?>&background=800000&color=fff&size=128" class="profile-avatar" style="border-color: rgba(255,255,255,0.2);">
                </div>
                <div class="text-center px-4">
                    <h5 class="fw-bold mb-1 text-white"><?= $user['nama_lengkap'] ?? 'User' ?></h5>
                    <p class="opacity-75 small text-white mb-0"><?= $user['profesi'] ?? 'Profesi' ?> • <?= $user['instansi'] ?? 'Instansi' ?></p>
                </div>
                
                <div class="p-4 pt-3">
                    <div class="glass-card-global mb-4" style="background: rgba(0,0,0,0.2); padding: 20px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-white"><i class="fas fa-bullseye me-2 text-warning"></i> Target Tahunan</h6>
                            <span class="badge bg-warning text-dark rounded-pill px-3 shadow-sm"><?= date('Y') ?></span>
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
                            <span class="text-muted">Progres JPL</span>
                            <span class="fw-bold"><?= $current ?> / <?= $target ?> JPL</span>
                        </div>
                    </div>

                    <div class="list-group list-group-flush border-0 bg-transparent">
                        <div class="list-group-item border-0 px-0 py-3 d-flex align-items-center gap-3 bg-transparent">
                            <div class="p-2 rounded-lg text-white" style="background: rgba(255,255,255,0.1);"><i class="fas fa-id-card fa-fw"></i></div>
                            <div>
                                <small class="text-white opacity-75 d-block">NIK</small>
                                <span class="fw-bold text-white"><?= $user['nik'] ?? '-' ?></span>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0 py-3 d-flex align-items-center gap-3 bg-transparent">
                            <div class="p-2 rounded-lg text-white" style="background: rgba(255,255,255,0.1);"><i class="fas fa-envelope fa-fw"></i></div>
                            <div>
                                <small class="text-white opacity-75 d-block">Email</small>
                                <span class="fw-bold d-inline-block text-break text-white" style="max-width: 200px; line-height: 1.2;"><?= $user['email'] ?? '-' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="col-lg-8">
            <div class="glass-card-global p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="fw-bold mb-1 text-white">Pengaturan Profil</h4>
                        <p class="text-white opacity-75 small mb-0">Perbarui informasi diri dan kontak Anda secara berkala.</p>
                    </div>
                    <i class="fas fa-user-cog fa-3x text-white opacity-25"></i>
                </div>

                <form action="<?= base_url('pelatihan/peserta/profil/update') ?>" method="POST" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    <!-- Jenis Peserta -->
                    <div class="section-title text-white"><i class="fas fa-users-cog me-1 text-warning"></i> Jenis Peserta</div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-white mb-1">PILIH JENIS PESERTA <span class="text-white fs-6">*</span></label>
                            <select name="jenis_peserta" class="form-select form-control-custom" required>
                                <option value="" disabled>Pilih Jenis Peserta...</option>
                                <option value="named" <?= ($user['jenis_peserta'] ?? '') == 'named' ? 'selected' : '' ?>>NAMED (PEGAWAI)</option>
                                <option value="non_named" <?= ($user['jenis_peserta'] ?? '') == 'non_named' ? 'selected' : '' ?>>NON-NAMED (UMUM/MAHASISWA)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <div class="section-title text-white"><i class="fas fa-user me-1 text-warning"></i> Data Personal</div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">NAMA LENGKAP <span class="text-white fs-6">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control form-control-custom" value="<?= $user['nama_lengkap'] ?? '' ?>" required pattern="[A-Za-z\s\.,']+" title="Nama hanya boleh mengandung huruf, spasi, titik, koma, atau tanda kutip tunggal.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">NIK (16 DIGIT)</label>
                            <input type="text" name="nik" class="form-control form-control-custom" value="<?= $user['nik'] ?? '' ?>" readonly title="NIK tidak dapat diubah.">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">EMAIL AKTIF <span class="text-white fs-6">*</span></label>
                            <input type="email" name="email" class="form-control form-control-custom" value="<?= $user['email'] ?? '' ?>" required>

                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-white mb-1">NO. WHATSAPP <span class="text-white fs-6">*</span></label>
                            <input type="tel" name="no_wa" class="form-control form-control-custom" value="<?= $user['no_wa'] ?? '' ?>" required pattern="[0-9]{10,15}" maxlength="15" inputmode="numeric" title="Nomor WhatsApp harus berupa angka murni (10 s.d 15 digit).">
                        </div>
                    </div>

                    <!-- Employment Info -->
                    <div class="section-title text-white"><i class="fas fa-briefcase me-1 text-warning"></i> Informasi Profesi</div>
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
                    <div class="section-title text-white"><i class="fas fa-shield-alt me-1 text-warning"></i> Keamanan Akun</div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-12">
                            <div class="mb-2 text-white small fw-bold fst-italic">Kosongkan sandi jika tidak ingin diubah.</div>
                            <label class="form-label small fw-bold text-white mb-1">KATA SANDI BARU</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control form-control-custom" minlength="8" placeholder="Minimal 8 karakter..." pattern="^(?=.*[0-9])(?=.*[a-zA-Z])[a-zA-Z0-9]+$" title="Password harus mengandung kombinasi huruf dan angka (tanpa spasi/simbol).">
                            </div>
                            <div class="form-text text-white fw-bold mt-2" style="font-size: 0.75rem;">Min. 8 karakter, kombinasi angka & huruf.</div>
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
