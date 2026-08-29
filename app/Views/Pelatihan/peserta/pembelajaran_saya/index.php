<?php echo $this->extend('Pelatihan/layout/peserta_layout'); ?>
<?php echo $this->section('content'); ?>
<?php
/**
 * @var array $minta_akses
 * @var array $belum_dimulai
 * @var array $berjalan
 * @var array $selesai
 * @var array $dibatalkan
 */
?>

<style>
    :root {
        --primary-red: #ce2127;
        --primary-dark: #0f172a;
        --primary-navy: #1e3a8a;
        --primary-gold: #f59e0b;
        --primary-yellow: #ffc107;
        --soft-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .nav-pills .nav-link {
        border-radius: 12px;
        font-weight: 800;
        color: #fff !important;
        background: rgba(16, 185, 129, 0.15) !important; /* Green glass */
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 12px 25px;
        transition: all 0.3s;
        border: 1px solid rgba(16, 185, 129, 0.3);
        margin-right: 10px;
        opacity: 0.8;
    }
    .nav-pills .nav-link:hover {
        background: rgba(16, 185, 129, 0.3) !important;
        opacity: 1;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
    }
    .nav-pills .nav-link.active {
        background: rgba(16, 185, 129, 0.8) !important;
        color: #fff !important;
        opacity: 1;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        border-color: rgba(16, 185, 129, 1);
    }
    .card {
        border: 2px solid #f1f5f9;
        border-radius: 20px;
        box-shadow: var(--soft-shadow);
        transition: all 0.3s;
    }
    .card:hover {
        transform: translateY(-8px);
        border-color: var(--primary-red);
    }
    .badge-status {
        font-size: 0.75rem;
        padding: 8px 15px;
        border-radius: 100px;
    }
    .btn-action-custom {
        background: linear-gradient(135deg, var(--primary-red) 0%, #7f0000 100%);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }
    .btn-action-custom:hover {
        background: linear-gradient(135deg, #e53935 0%, var(--primary-red) 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(206, 33, 39, 0.4);
    }
    .status-card {
        background: rgba(15, 23, 42, 0.4); /* subtle navy background */
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }
</style>

<div class="pt-1 mb-5 glass-wrapper-global">
    <!-- Header Section -->
    <div class="mb-4 animate__animated animate__fadeIn">
        <h3 class="fw-bold mb-3 text-white">Pembelajaran Saya</h3>
        <div class="highlight-bounce mt-2 d-inline-block">
            <span class="badge bg-warning text-dark px-3 py-2 fw-bold shadow-sm" style="font-size: 0.9rem;">
                Berikut daftar program pembelajaran yang Anda ambil beserta status pelaksanaannya.
            </span>
        </div>
    </div>
    <ul class="nav nav-pills mb-5 p-2 rounded-lg d-inline-flex border border-light" style="background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.1) !important;" id="ps-tab" role="tablist">
        <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#minta-akses" type="button" role="tab">MINTA AKSES</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#belum-dimulai" type="button" role="tab">BELUM DIMULAI</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#sedang-berjalan" type="button" role="tab">SEDANG BERJALAN</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#selesai" type="button" role="tab">SELESAI</button></li>
        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#dibatalkan" type="button" role="tab">DIBATALKAN</button></li>
    </ul>
    <div class="tab-content">
        <!-- Minta Akses -->
        <div class="tab-pane fade show active" id="minta-akses" role="tabpanel">
            <div class="row g-4">
                <?php foreach ($minta_akses as $p): ?>
                <div class="col-md-4">
                    <div class="glass-card-global h-100 status-card" style="border-top: 3px solid rgba(255,255,255,0.7);">
                        <div class="p-4 d-flex flex-column h-100">
                            <h5 class="fw-bold text-white text-uppercase"><?= $p['nama'] ?></h5>
                            <p class="small opacity-75 text-white fw-bold mb-3"><i class="fas fa-hospital me-1 text-warning"></i> <?= strtoupper($p['penyelenggara']) ?></p>
                            <span class="badge bg-warning text-dark badge-status fw-bold shadow-sm mb-4">MENUNGGU VERIFIKASI</span>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="<?= base_url('pelatihan/peserta/detail_pelatihan/'.$p['id']) ?>" class="btn btn-light flex-grow-1 rounded-pill fw-bold shadow-sm">DETAIL</a>
                                <a href="javascript:void(0)" class="btn btn-outline-light flex-grow-1 rounded-pill fw-bold shadow-sm" onclick="confirmBatalkan('<?= base_url('pelatihan/peserta/batalkan_pelatihan/'.$p['pelatihan_id']) ?>', '<?= esc($p['nama'], 'js') ?>')">BATALKAN</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Belum Dimulai -->
        <div class="tab-pane fade" id="belum-dimulai" role="tabpanel">
            <div class="row g-4">
                <?php foreach ($belum_dimulai as $p): ?>
                <div class="col-md-4">
                    <div class="glass-card-global h-100 status-card" style="border-top: 3px solid rgba(255,255,255,0.7);">
                        <div class="p-4 d-flex flex-column h-100">
                            <h5 class="fw-bold text-white text-uppercase"><?= $p['nama'] ?></h5>
                            <p class="small opacity-75 text-white fw-bold mb-3"><i class="fas fa-hospital me-1 text-warning"></i> <?= strtoupper($p['penyelenggara']) ?></p>
                            <span class="badge bg-success text-white badge-status fw-bold shadow-sm mb-4">DISETUJUI</span>
                            <div class="d-flex flex-column gap-2 mt-auto">
                                <?php 
                                    $now = date('Y-m-d H:i:s');
                                    $mulai = $p['jadwal_mulai'] . ' ' . ($p['jam_mulai'] ?: '00:00:00');
                                    $waktu_selesai = $p['jadwal_selesai'] . ' ' . ($p['jam_selesai'] ?: '23:59:59');
                                    if ($now >= $mulai && $now <= $waktu_selesai):
                                ?>
                                    <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id']) ?>" class="btn btn-action-custom w-100 rounded-pill fw-bold shadow-sm">MULAI BELAJAR</a>
                                <?php elseif ($now < $mulai): ?>
                                    <button class="btn btn-secondary w-100 rounded-pill fw-bold shadow-sm" disabled style="background: rgba(255,255,255,0.2); border: none;">BELUM MULAI</button>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 rounded-pill fw-bold shadow-sm" disabled style="background: rgba(255,255,255,0.2); border: none;">WAKTU HABIS</button>
                                <?php endif; ?>
                                <a href="javascript:void(0)" class="btn btn-outline-light w-100 rounded-pill fw-bold shadow-sm" onclick="confirmBatalkan('<?= base_url('pelatihan/peserta/batalkan_pelatihan/'.$p['pelatihan_id']) ?>', '<?= esc($p['nama'], 'js') ?>')">BATALKAN</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Sedang Berjalan -->
        <div class="tab-pane fade" id="sedang-berjalan" role="tabpanel">
            <div class="row g-4">
                <?php foreach ($berjalan as $p): ?>
                <div class="col-md-4">
                    <div class="glass-card-global h-100 status-card" style="border-top: 3px solid rgba(255,255,255,0.7);">
                        <div class="p-4 d-flex flex-column h-100">
                            <h5 class="fw-bold text-white text-uppercase"><?= $p['nama'] ?></h5>
                            <p class="small opacity-75 text-white fw-bold mb-3"><i class="fas fa-hospital me-1 text-warning"></i> <?= strtoupper($p['penyelenggara']) ?></p>
                            <div class="progress mb-2" style="height:12px; border-radius: 10px; background: rgba(255,255,255,0.2);"><div class="progress-bar shadow-sm" style="background-color: #10b981; width: <?= $p['progress'] ?>%;"></div></div>
                            <div class="d-flex justify-content-between mb-3">
                                <small class="text-white fw-bold opacity-75">PROGRES BELAJAR</small>
                                <small class="text-warning fw-bold"><?= round($p['progress']) ?>%</small>
                            </div>
                            <?php 
                                $now = date('Y-m-d H:i:s');
                                $mulai = $p['jadwal_mulai'] . ' ' . ($p['jam_mulai'] ?: '00:00:00');
                                $waktu_selesai = $p['jadwal_selesai'] . ' ' . ($p['jam_selesai'] ?: '23:59:59');
                                if ($now >= $mulai && $now <= $waktu_selesai):
                            ?>
                                <a href="<?= base_url('pelatihan/peserta/belajar/'.$p['id']) ?>" class="btn btn-action-custom w-100 mt-auto rounded-pill fw-bold shadow-sm">LANJUTKAN</a>
                            <?php else: ?>
                                <button class="btn btn-secondary w-100 mt-auto rounded-pill fw-bold shadow-sm" disabled style="background: rgba(255,255,255,0.2); border: none;">MASA BERAKHIR</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Selesai -->
        <div class="tab-pane fade" id="selesai" role="tabpanel">
            <div class="row g-4">
                <?php foreach ($selesai as $p): ?>
                <div class="col-md-4">
                    <div class="glass-card-global h-100 status-card" style="border-top: 3px solid rgba(255,255,255,0.7);">
                        <div class="p-4 text-center">
                            <div class="p-3 rounded-circle d-inline-block mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.15);">
                                <i class="fas fa-medal text-warning fs-4"></i>
                            </div>
                            <h5 class="fw-bold text-white text-uppercase"><?= $p['nama'] ?></h5>
                            <p class="small opacity-75 text-white fw-bold mb-3"><?= strtoupper($p['penyelenggara']) ?></p>
                            <span class="badge bg-warning text-dark badge-status fw-bold px-4 py-2 rounded-pill shadow-sm">DIKLAT SELESAI</span>
                            <a href="<?= base_url('pelatihan/peserta/sertifikat_saya') ?>" class="btn btn-action-custom w-100 mt-4 rounded-pill fw-bold shadow-sm">LIHAT SERTIFIKAT</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- Dibatalkan -->
        <div class="tab-pane fade" id="dibatalkan" role="tabpanel">
            <div class="row g-4">
                <?php foreach ($dibatalkan as $p): ?>
                <div class="col-md-4">
                    <div class="glass-card-global h-100 status-card" style="border-top: 3px dashed rgba(255,255,255,0.7);">
                        <div class="p-4 text-center opacity-75">
                            <i class="fas fa-times-circle text-danger fs-1 mb-3 opacity-75"></i>
                            <h5 class="fw-bold text-white text-uppercase"><?= $p['nama'] ?></h5>
                            <p class="small text-white fw-bold opacity-75"><?= strtoupper($p['penyelenggara']) ?></p>
                            <span class="badge bg-danger text-white badge-status fw-bold shadow-sm">DIBATALKAN</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php echo $this->endSection(); ?>

<?php echo $this->section('scripts'); ?>
<script>
    function confirmBatalkan(url, nama) {
        Swal.fire({
            title: '<span class="fw-bold fs-5 d-block mt-2">Batalkan Pelatihan?</span>',
            html: `<p class="text-muted fw-bold px-2">Apakah Anda yakin ingin membatalkan pendaftaran pelatihan <b>${nama}</b>?</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-times me-1"></i> Ya, Batalkan!',
            cancelButtonText: 'Kembali',
            customClass: { popup: 'rounded-4 shadow-lg border-0', confirmButton: 'rounded-pill px-4 py-2 fw-bold', cancelButton: 'rounded-pill px-4 py-2 fw-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>
<?php echo $this->endSection(); ?>
