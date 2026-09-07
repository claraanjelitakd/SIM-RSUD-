<?= $this->extend('Pelatihan/layout/admin_layout') ?>
<?php 
/** 
 * @var array $p 
 * @var float $avg 
 * @var array $feedbacks 
 */ 
?>

<?= $this->section('content') ?>
<div class="mb-4">
    <a href="<?= base_url('pelatihan/admin/monitoring_peserta') ?>" class="btn btn-white btn-sm rounded-pill px-3 fw-bold border shadow-sm mb-3">
        <i class="fas fa-arrow-left me-2 text-danger"></i> KEMBALI
    </a>
    <div class="row g-3 align-items-center">
        <div class="col-md-8">
            <div class="bg-white p-4 rounded-lg shadow-sm border-start border-warning border-5">
                <h4 class="fw-bold mb-1 text-uppercase"><?= $p['nama'] ?></h4>
                <div class="text-muted small">DETAIL FEEDBACK DAN SARAN PESERTA</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-dark text-white p-4 rounded-lg shadow-sm text-center">
                <div class="small fw-bold text-warning text-uppercase mb-1" style="letter-spacing: 1px;">Skor Kepuasan</div>
                <div class="display-6 fw-bold mb-0"><?= $avg ?> <small class="fs-6 text-muted">/ 5.0</small></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <?php if(!empty($questionStats)): ?>
        <?php foreach ($questionStats as $kat => $qs): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-lg p-4 bg-white h-100">
                <h6 class="fw-bold mb-4 text-primary border-bottom pb-2 text-uppercase"><i class="fas fa-list-ul me-2"></i> KATEGORI: <?= esc($kat) ?></h6>
                <?php foreach($qs as $q): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold text-dark"><?= esc($q['pertanyaan']) ?></span>
                        <span class="small fw-bold text-warning"><i class="fas fa-star me-1"></i> <?= $q['avg_rating'] ?> <span class="text-muted fw-normal">(<?= $q['total_votes'] ?> vote)</span></span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                        <div class="progress-bar bg-warning" style="width: <?= ($q['avg_rating'] / 5) * 100 ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-light border-0 shadow-sm text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-50"></i>
                <h6 class="fw-bold text-muted mb-0">Belum ada data detail rating kuesioner.</h6>
            </div>
        </div>
    <?php endif; ?>
</div>

    <div class="col-12">
        
        <?php // ─── Sesi Stats ──────────────────────────────────────────────── ?>
        <?php if (!empty($sesiStats)): ?>
        <div class="mb-4">
            <h5 class="fw-bold mb-3 text-uppercase"><i class="fas fa-clock me-2 text-info"></i> Rata-rata Penilaian per Sesi</h5>
            <div class="row g-3">
                <?php foreach ($sesiStats as $ss): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-lg p-4 bg-white h-100 border-top border-info border-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-info text-uppercase"><i class="fas fa-users me-2"></i><?= esc($ss['nama']) ?></h6>
                            <span class="badge rounded-pill px-3 py-2 fw-bold bg-info text-white">
                                <i class="fas fa-star me-1"></i><?= $ss['avg_overall'] ?> / 5.0
                            </span>
                        </div>
                        <?php foreach ($ss['pertanyaan'] as $pq): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold text-dark"><?= esc($pq['pertanyaan']) ?></span>
                                <span class="small fw-bold text-warning"><i class="fas fa-star me-1"></i><?= $pq['avg_rating'] ?> <span class="text-muted fw-normal">(<?= $pq['total_votes'] ?> vote)</span></span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                                <div class="progress-bar bg-info" style="width: <?= ($pq['avg_rating'] / 5) * 100 ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <hr class="my-4">
        <?php endif; ?>

        <?php // ─── Materi Stats ─────────────────────────────────────────────── ?>
        <?php if (!empty($materiStats)): ?>
        <div class="mb-4">
            <h5 class="fw-bold mb-3 text-uppercase"><i class="fas fa-book me-2 text-primary"></i> Rata-rata Penilaian Materi</h5>
            <div class="row g-3">
                <?php foreach ($materiStats as $ms): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-lg p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-primary text-uppercase"><i class="fas fa-book-open me-2"></i><?= esc($ms['judul']) ?></h6>
                            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: #1e3a5f; color: white;">
                                <i class="fas fa-star me-1"></i><?= $ms['avg_overall'] ?> / 5.0
                            </span>
                        </div>
                        <?php foreach ($ms['pertanyaan'] as $pq): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold text-dark"><?= esc($pq['pertanyaan']) ?></span>
                                <span class="small fw-bold text-warning"><i class="fas fa-star me-1"></i><?= $pq['avg_rating'] ?> <span class="text-muted fw-normal">(<?= $pq['total_votes'] ?> vote)</span></span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                                <div class="progress-bar bg-primary" style="width: <?= ($pq['avg_rating'] / 5) * 100 ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <hr class="my-4">
        <?php endif; ?>

        <?php // ─── Narasumber Stats ─────────────────────────────────────────── ?>
        <?php if (!empty($narasumberStats)): ?>
        <div class="mb-4">
            <h5 class="fw-bold mb-3 text-uppercase"><i class="fas fa-chalkboard-teacher me-2 text-success"></i> Rata-rata Penilaian Narasumber</h5>
            <div class="row g-3">
                <?php foreach ($narasumberStats as $ns): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-lg p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-success text-uppercase"><i class="fas fa-user-tie me-2"></i><?= esc($ns['nama']) ?></h6>
                            <span class="badge rounded-pill px-3 py-2 fw-bold bg-success text-white">
                                <i class="fas fa-star me-1"></i><?= $ns['avg_overall'] ?> / 5.0
                            </span>
                        </div>
                        <?php foreach ($ns['pertanyaan'] as $pq): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold text-dark"><?= esc($pq['pertanyaan']) ?></span>
                                <span class="small fw-bold text-warning"><i class="fas fa-star me-1"></i><?= $pq['avg_rating'] ?> <span class="text-muted fw-normal">(<?= $pq['total_votes'] ?> vote)</span></span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                                <div class="progress-bar bg-success" style="width: <?= ($pq['avg_rating'] / 5) * 100 ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <hr class="my-4">
        <?php endif; ?>

        <?php // ─── Penyelenggara Stats ──────────────────────────────────────── ?>
        <?php if (!empty($penyelenggaraStats)): ?>
        <div class="mb-4">
            <h5 class="fw-bold mb-3 text-uppercase"><i class="fas fa-users-cog me-2 text-warning"></i> Rata-rata Penilaian Penyelenggara</h5>
            <div class="row g-3">
                <?php foreach ($penyelenggaraStats as $ps): ?>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-lg p-4 bg-white h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-warning text-uppercase"><i class="fas fa-building me-2"></i><?= esc($ps['nama']) ?></h6>
                            <span class="badge rounded-pill px-3 py-2 fw-bold bg-warning text-dark">
                                <i class="fas fa-star me-1"></i><?= $ps['avg_overall'] ?> / 5.0
                            </span>
                        </div>
                        <?php foreach ($ps['pertanyaan'] as $pq): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold text-dark"><?= esc($pq['pertanyaan']) ?></span>
                                <span class="small fw-bold text-warning"><i class="fas fa-star me-1"></i><?= $pq['avg_rating'] ?> <span class="text-muted fw-normal">(<?= $pq['total_votes'] ?> vote)</span></span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                                <div class="progress-bar bg-warning" style="width: <?= ($pq['avg_rating'] / 5) * 100 ?>%"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <hr class="my-4">
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0">Rincian Feedback per Peserta</h5>
            <span class="badge bg-dark text-white rounded-pill px-3 py-2"><?= count($feedbacks) ?> Peserta</span>
        </div>
        <div class="accordion shadow-sm rounded-lg" id="accordionFeedbacks">
            <?php foreach ($feedbacks as $index => $fb) : ?>
            <div class="accordion-item border-0 border-bottom">
                <h2 class="accordion-header" id="heading<?= $index ?>">
                    <button class="accordion-button collapsed py-4 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
                        <div class="d-flex w-100 justify-content-between align-items-center pe-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($fb['nama']) ?>&background=random" class="rounded-circle shadow-sm" width="48" height="48">
                                <div>
                                    <div class="fw-bold text-dark text-uppercase"><?= $fb['nama'] ?></div>
                                    <div class="small mt-1">
                                        <span class="badge <?= ($fb['skor_post_test'] !== null && $fb['skor_post_test'] >= ($p['kkm'] ?? 70)) ? 'bg-success' : 'bg-danger' ?> rounded-pill px-2">
                                            Post-Test: <?= $fb['skor_post_test'] ?? 'N/A' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="text-warning mb-1" style="font-size: 0.9rem;">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="<?= $i <= $fb['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                    <?php endfor; ?>
                                </div>
                                <div class="small text-muted fw-bold"><?= $fb['rating'] ?> / 5.0 (Rating Umum)</div>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapse<?= $index ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#accordionFeedbacks">
                    <div class="accordion-body bg-light p-4">
                        <div class="bg-white p-4 rounded-4 shadow-sm mb-4 border-start border-warning border-4">
                            <h6 class="fw-bold text-muted small text-uppercase mb-2">SARAN DAN MASUKAN:</h6>
                            <p class="fst-italic text-dark mb-0">"<?= esc($fb['komentar']) ?>"</p>
                        </div>
                        
                        <?php if (!empty($fb['jawaban_detail'])): ?>
                            <?php foreach ($fb['jawaban_detail'] as $sesi => $kategoriList): ?>
                                <h6 class="fw-bold mt-4 mb-3 text-danger border-bottom pb-2"><i class="fas fa-clock me-2"></i>Sesi: <?= esc($sesi) ?></h6>
                                <div class="row g-4 mb-4">
                                    <?php foreach ($kategoriList as $kat => $jawabans): ?>
                                        <div class="col-md-6">
                                            <div class="card h-100 border-0 shadow-sm rounded-4">
                                                <div class="card-header bg-white border-bottom fw-bold text-primary py-3 text-uppercase">
                                                    <i class="fas fa-list-ul me-2"></i> <?= esc($kat) ?>
                                                </div>
                                                <div class="card-body p-0">
                                                    <ul class="list-group list-group-flush">
                                                        <?php foreach ($jawabans as $j): ?>
                                                        <li class="list-group-item p-3 border-bottom-0 border-light">
                                                            <div class="small text-dark mb-2"><?= esc($j['pertanyaan']) ?></div>
                                                            <div class="text-warning small fw-bold">
                                                                <?php for($i=1; $i<=5; $i++): ?>
                                                                    <i class="<?= $i <= $j['nilai_rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                                                <?php endfor; ?> 
                                                                <span class="text-muted ms-1">(<?= $j['nilai_rating'] ?>)</span>
                                                            </div>
                                                        </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-secondary text-center">Detail jawaban kuesioner tidak tersedia.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?= $this->endSection() ?>
