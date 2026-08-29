<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    :root {
        --primary-red: #ce2127;
        --primary-dark: #111111;
        --primary-light: #ffffff;
        --primary-gray: #f8f9fa;
        --soft-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        --hover-shadow: 0 15px 35px rgba(206, 33, 39, 0.15);
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8f9fa;
    }

    /* Search Form */
    .search-wrapper input {
        border-radius: 50px 0 0 50px;
        padding-left: 25px;
        height: 50px;
        border: 2px solid #eee;
        border-right: none;
    }
    .search-wrapper input:focus { border-color: var(--primary-red); box-shadow: none; }
    .search-wrapper button {
        border-radius: 0 50px 50px 0;
        height: 50px;
        padding: 0 25px;
        background-color: var(--primary-red);
        color: white;
        border: 2px solid var(--primary-red);
    }

    /* Cards */
    .dashboard-card {
        background: #fff;
        border-radius: 20px;
        border: none;
        box-shadow: var(--soft-shadow);
        transition: 0.3s;
        margin-bottom: 24px;
    }

    .stat-box {
        padding: 20px;
        border-radius: 16px;
        background: #fff;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: 0.3s;
    }
    .stat-box:hover { transform: translateY(-5px); border-color: var(--primary-red); box-shadow: var(--hover-shadow); }

    .stat-icon {
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }

    /* Progress JPL */
    .progress-glass { height: 10px; background: #f1f5f9; border-radius: 50px; overflow: hidden; }
    .progress-fill-jpl { background: linear-gradient(90deg, #c62828, #f44336); border-radius: 50px; }

    /* Calendar Mini */
    .fc { font-family: 'Plus Jakarta Sans', sans-serif; }
    .fc .fc-toolbar-title { font-size: 1.1rem !important; font-weight: 800; color: var(--primary-dark); }
    .fc .fc-button-primary { background: var(--primary-dark) !important; border: none !important; border-radius: 8px !important; text-transform: capitalize; padding: 4px 10px; }
    .fc .fc-daygrid-day-number { font-size: 0.8rem; font-weight: 600; padding: 4px; }
    .fc .fc-col-header-cell-cushion { font-size: 0.75rem; font-weight: 700; color: #64748b; }
    .fc .fc-day-today { background-color: rgba(206,33,39,0.05) !important; }
    .fc-event { border-radius: 4px !important; font-size: 0.65rem !important; font-weight: 700; cursor: pointer; border: none !important; margin: 1px !important; padding: 2px 4px !important;}

    /* Animations */
    .animate-fade { animation: fadeIn 0.6s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    @keyframes bounce-welcome {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .animate-bounce-welcome {
        display: inline-block;
        animation: bounce-welcome 2s ease-in-out infinite;
    }
    @keyframes wave-hand {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(15deg); }
        50% { transform: rotate(-10deg); }
        75% { transform: rotate(15deg); }
    }
    .animate-wave {
        display: inline-block;
        animation: wave-hand 2s ease-in-out infinite;
        transform-origin: 70% 70%;
    }
</style>

<div class="glass-wrapper-global">
    <div class="row g-4">
        <!-- LEFT COLUMN (70%) -->
        <div class="col-lg-8 animate-fade">
            
            <!-- Header -->
            <div class="mb-4">
                <h3 class="fw-bold mb-1 text-white animate-bounce-welcome">Halo, <span class="text-warning"><?= $user['nama'] ?? 'Peserta' ?></span> <span class="animate-wave">👋</span></h3>
                <p class="text-white opacity-75 mb-0 fw-medium animate-bounce-welcome" style="animation-delay: 0.1s;"><?= $user['profesi'] ?? 'Umum' ?> | <?= $user['instansi'] ?? 'Instansi' ?></p>
            </div>

            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="glass-card-global p-4 h-100 mb-0">
                        <div class="d-flex justify-content-between align-items-end mb-3">
                            <div>
                                <div class="text-white opacity-75 small fw-bold text-uppercase mb-1">Capaian JPL Aktif</div>
                                <h2 class="fw-bold mb-0 text-white"><?= $total_jpl ?> <span class="fs-6 opacity-75 fw-medium">/ <?= $target_jpl ?></span></h2>
                            </div>
                            <div class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-bold shadow-sm">
                                <?= $target_jpl > 0 ? round(($total_jpl / $target_jpl) * 100) : 0 ?>%
                            </div>
                        </div>
                        <div class="progress-glass" style="background: rgba(255,255,255,0.2);">
                            <div class="progress-fill-jpl h-100 shadow-sm" style="background: #10b981; width: <?= $target_jpl > 0 ? ($total_jpl / $target_jpl) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column gap-3 h-100">
                        <div class="glass-card-global p-3 d-flex align-items-center gap-3 flex-grow-1 mb-0">
                            <div class="stat-icon bg-white text-danger shadow-sm"><i class="fas fa-book-open"></i></div>
                            <div>
                                <h4 class="fw-bold mb-0 text-white"><?= $total_belajar ?></h4>
                                <small class="opacity-75 fw-bold text-white">Total Pelatihan Diikuti</small>
                            </div>
                        </div>
                        <div class="glass-card-global p-3 d-flex align-items-center gap-3 flex-grow-1 mb-0">
                            <div class="stat-icon bg-warning text-dark shadow-sm"><i class="fas fa-award"></i></div>
                            <div>
                                <h4 class="fw-bold mb-0 text-white"><?= $selesai ?></h4>
                                <small class="opacity-75 fw-bold text-white">Sertifikat Diperoleh</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diklat Aktif (Sedang Berjalan) -->
            <?php if(!empty($diklat_aktif)): ?>
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-white"><i class="fas fa-play-circle text-warning me-2"></i>Sedang Berjalan</h5>
                    <a href="<?= base_url('pelatihan/peserta/pembelajaran_saya') ?>" class="small text-warning fw-bold text-decoration-none">Lihat Semua</a>
                </div>
                <?php foreach($diklat_aktif as $da): ?>
                <div class="glass-card-global p-3 mb-3 hover-scale">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-white text-dark border-0 mb-2 shadow-sm"><?= $da['mekanisme'] ?></span>
                            <h6 class="fw-bold mb-1 text-white"><?= $da['nama'] ?></h6>
                            <small class="text-white opacity-75 fw-bold"><i class="fas fa-calendar-alt me-1"></i> Selesai: <?= tanggal_indo($da['jadwal_selesai'] ?? $da['jadwal_mulai']) ?></small>
                        </div>
                        <a href="<?= base_url('pelatihan/peserta/belajar/'.$da['id']) ?>" class="btn btn-action-global text-dark rounded-pill fw-bold px-4" style="background-color: #f8f9fa;">Lanjut Belajar</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Pembelajaran Populer -->
            <div class="mb-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-white"><i class="fas fa-fire text-warning me-2"></i>Diklat Populer</h5>
                </div>
                <div class="d-flex overflow-auto pb-4 gap-3 hide-scroll" style="flex-wrap: nowrap; scrollbar-width: none;">
                    <style>.hide-scroll::-webkit-scrollbar { display: none; }</style>
                    <?php foreach ($pelatihan_populer as $p) : ?>
                    <div style="min-width: 260px; max-width: 260px;">
                        <a href="<?= base_url('pelatihan/peserta/detail_pelatihan/'.$p['id']) ?>" class="text-decoration-none text-white">
                            <div class="glass-card-global h-100 p-0 overflow-hidden hover-card-premium" style="transition: 0.3s; border-radius: 12px;">
                                <?php if(!empty($p['gambar_pelatihan'])): ?>
                                <div class="position-relative d-flex align-items-center justify-content-center" style="height: 100px;">
                                    <img src="<?= base_url($p['gambar_pelatihan']) ?>" alt="<?= esc($p['nama']) ?>" class="w-100 h-100" style="object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-100 h-100 p-2" style="display: none; background: linear-gradient(135deg, #0f172a 0%, #c62828 100%); flex-direction: column; align-items: center; justify-content: center; border-bottom: 2px solid #c62828;">
                                        <div class="fw-bold text-white text-center" style="font-size: 0.75rem; line-height: 1.2;"><?= esc($p['nama']) ?></div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="position-relative d-flex align-items-center justify-content-center p-2" style="height: 100px; background: linear-gradient(135deg, #0f172a 0%, #c62828 100%); border-bottom: 2px solid #c62828;">
                                    <div class="fw-bold text-white text-center" style="font-size: 0.75rem; line-height: 1.2;"><?= esc($p['nama']) ?></div>
                                </div>
                                <?php endif; ?>
                                <div class="p-3">
                                    <span class="badge bg-white text-dark fw-bold mb-2 shadow-sm" style="font-size: 0.65rem;"><?= strtoupper($p['kategori']) ?></span>
                                    <h6 class="fw-bold mb-2 lh-base text-white" style="font-size: 0.9rem; min-height: 2.7rem;"><?= $p['nama'] ?></h6>
                                    <div class="d-flex justify-content-between align-items-center border-top border-light pt-2" style="border-color: rgba(255,255,255,0.1) !important;">
                                        <small class="opacity-75 fw-bold text-white" style="font-size: 0.7rem;"><i class="fas fa-users me-1"></i> <?= $p['peserta'] ?> Peserta</small>
                                        <small class="text-white fw-bold text-uppercase" style="font-size: 0.7rem;"><?= $p['biaya'] ?></small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <div class="col-lg-4 animate-fade" style="animation-delay: 0.2s;">
            <div class="card p-3 sticky-top border-0 shadow-sm" style="top: 100px; background: #ffffff; border-radius: 16px;">
                <h6 class="fw-bold mb-3 px-2 border-start border-3 border-danger text-dark"><i class="far fa-calendar-alt me-2 text-danger"></i> Agenda Diklat</h6>
                <div class="d-flex flex-wrap gap-2 mb-3 px-2">
                    <span class="badge bg-danger" style="font-size: 0.65rem;">Lanjutkan</span>
                    <span class="badge bg-dark text-white" style="font-size: 0.65rem;">Terdaftar</span>
                    <span class="badge bg-secondary" style="font-size: 0.65rem;">Selesai</span>
                    <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">Belum Daftar</span>
                </div>
                <div id='calendar' style="height: 400px; font-size: 0.85rem;" class="text-dark"></div>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var scheduleData = <?= json_encode($jadwal) ?>;
        
        var formattedEvents = scheduleData.map(function(item) {
            let color = '#334155'; // default grey for belum daftar
            if (item.status_enrollment === 'lanjutkan') {
                color = '#c62828'; // red for Lanjutkan
            } else if (item.status_enrollment === 'terdaftar') {
                color = '#111111'; // black for Terdaftar
            } else if (item.status_enrollment === 'selesai') {
                color = '#6c757d'; // gray for Selesai
            } else {
                color = item.tipe === 'pelatihan' ? '#6c757d' : '#f8f9fa';
            }

            return {
                title: item.event,
                start: item.tanggal,
                end: item.end ? new Date(new Date(item.end).getTime() + 86400000).toISOString().split('T')[0] : null,
                backgroundColor: color,
                extendedProps: {
                    tipe: item.tipe,
                    reg_buka: item.reg_buka || null,
                    reg_tutup: item.reg_tutup || null,
                    jam: item.jam || null,
                    status_enrollment: item.status_enrollment || 'belum_daftar'
                }
            };
        });

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: { left: 'prev', center: 'title', right: 'next' },
            events: formattedEvents,
            contentHeight: 'auto',
            eventClick: function(info) {
                let p = info.event.extendedProps;
                let htmlContent = `<div class="text-start mt-3">`;
                
                if (p.tipe === 'pelatihan') {
                    htmlContent += `
                        <div class="alert alert-danger bg-opacity-10 border-0 text-danger fw-bold small p-3 rounded-3 mb-3">
                            <i class="fas fa-info-circle me-1"></i> Jadwal Pelaksanaan Pelatihan
                        </div>
                        <div class="mb-2"><strong class="text-dark">Mulai:</strong> ${info.event.start.toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</div>
                    `;
                    if (info.event.end) {
                        let endDate = new Date(info.event.end.getTime() - 86400000); // revert the exclusive end date
                        htmlContent += `<div class="mb-3"><strong class="text-dark">Selesai:</strong> ${endDate.toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</div>`;
                    }
                    if (p.reg_buka && p.reg_tutup) {
                        htmlContent += `
                            <hr>
                            <h6 class="fw-bold text-dark mb-2">Periode Pendaftaran</h6>
                            <div class="small bg-light p-2 rounded border">
                                ${new Date(p.reg_buka).toLocaleDateString('id-ID')} s.d ${new Date(p.reg_tutup).toLocaleDateString('id-ID')}
                            </div>
                        `;
                    }
                } else if (p.tipe === 'sesi') {
                    htmlContent += `
                        <div class="alert alert-dark border-0 text-dark fw-bold small p-3 rounded-3 mb-3">
                            <i class="fas fa-video me-1"></i> Sesi Interaktif (Live/Tatap Muka)
                        </div>
                        <div class="mb-2"><strong class="text-dark">Tanggal:</strong> ${info.event.start.toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</div>
                        <div class="mb-3"><strong class="text-dark">Pukul:</strong> ${p.jam ? p.jam + ' WIB' : 'Menyesuaikan'}</div>
                    `;
                }

                // Add Status Badge
                let statusBadge = '';
                if (p.status_enrollment === 'lanjutkan') {
                    statusBadge = '<span class="badge bg-danger w-100 p-2">STATUS: SEDANG BERJALAN (LANJUTKAN)</span>';
                } else if (p.status_enrollment === 'terdaftar') {
                    statusBadge = '<span class="badge bg-dark text-white w-100 p-2">STATUS: TERDAFTAR</span>';
                } else if (p.status_enrollment === 'selesai') {
                    statusBadge = '<span class="badge bg-secondary w-100 p-2">STATUS: SELESAI</span>';
                } else {
                    statusBadge = '<span class="badge bg-light text-dark border w-100 p-2">STATUS: BELUM MENDAFTAR</span>';
                }
                
                htmlContent += `<div class="mt-4">${statusBadge}</div></div>`;

                Swal.fire({
                    title: `<span class="fs-5 fw-bold text-dark">${info.event.title}</span>`,
                    html: htmlContent,
                    showConfirmButton: true,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#111',
                    customClass: { popup: 'rounded-4 border-0 shadow-lg' }
                });
            }
        });
        calendar.render();
    });
</script>

<?= $this->endSection() ?>
