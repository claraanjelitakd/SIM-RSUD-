<?= $this->extend('Pelatihan/layout/peserta_layout') ?>
<?= $this->section('content') ?>
<?php
/**
 * @var int $capaian_jpl
 * @var int $target_jpl
 * @var array $sertifikat
 */
?>

<style>
    :root {
        --primary-red: #ce2127;
        --primary-dark: #0f172a;
        --primary-yellow: #ffc107;
        --bg-light: #ffffff;
        --text-dark: #0f172a;
        --text-muted: #64748b;
    }

    .search-input-group {
        background: white;
        border-radius: 12px;
        border: 2px solid #f1f5f9;
        padding: 10px 20px;
        display: flex;
        align-items: center;
        flex-grow: 1;
        transition: all 0.3s;
    }

    .search-input-group:focus-within {
        border-color: var(--primary-red);
        box-shadow: 0 0 0 4px rgba(206, 33, 39, 0.1);
    }

    .search-input-group i {
        color: var(--primary-red);
    }

    .search-input-group input {
        border: none;
        outline: none;
        padding-left: 10px;
        width: 100%;
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-dark);
    }

    .search-input-group input::placeholder {
        color: rgba(255, 255, 255, 0.75);
    }

    .nav-pills-kemenkes .nav-link {
        color: #fff !important;
        background: #475569 !important;
        font-weight: 800;
        padding: 12px 35px;
        border-radius: 50rem;
        transition: all 0.3s;
        border: 1px solid #334155;
        margin-right: 10px;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        opacity: 0.7;
    }
    .nav-pills-kemenkes .nav-link:hover {
        background: #1e293b !important;
        opacity: 1;
    }

    .nav-pills-kemenkes .nav-link.active {
        background-color: var(--primary-dark) !important;
        color: white !important;
        opacity: 1;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.2);
        border-color: var(--primary-dark);
    }

    .cert-card-horizontal {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: 24px;
        padding: 30px;
        border: 1px solid rgba(255,255,255,0.2);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 30px;
        transition: all 0.3s;
    }

    .cert-card-horizontal:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        border-color: rgba(255,255,255,0.5);
    }

    .cert-doc-icon {
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .cert-badge-cat {
        display: inline-block;
        padding: 6px 16px;
        background: var(--primary-dark);
        color: #ffffff;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 800;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }



    .jpl-skp-info {
        display: flex;
        align-items: center;
        gap: 15px;
        color: var(--text-dark);
        font-size: 0.85rem;
        margin-top: 15px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .cert-card-horizontal {
            flex-direction: column;
            text-align: center;
            padding: 20px;
            gap: 15px;
        }
        .cert-doc-icon {
            width: 70px;
            height: 70px;
        }
        .jpl-skp-info {
            justify-content: center;
            flex-wrap: wrap;
        }
        .cert-card-horizontal .flex-grow-1 {
            width: 100%;
        }
        .cert-card-horizontal .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>

<div class="pt-1 mb-5 glass-wrapper-global">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 animate__animated animate__fadeIn">
        <div>
            <h3 class="fw-bold mb-3 text-white">Sertifikat & Portofolio</h3>
            <div class="highlight-bounce mt-2 d-inline-block">
                <span class="badge bg-warning text-dark px-3 py-2 fw-bold shadow-sm" style="font-size: 0.9rem;">
                    Kelola dokumen sertifikat, capaian JPL, dan surat tugas Anda.
                </span>
            </div>
        </div>
        <div class="d-flex gap-3 align-items-center flex-wrap">
            <div class="text-md-end border-end pe-3 border-2 border-light" style="border-color: rgba(255,255,255,0.2) !important;">
                <div class="small fw-bold text-white opacity-75 mb-1">CAPAIAN TAHUNAN</div>
                <span class="fw-bold text-warning fs-5"><?= $capaian_jpl ?></span> <span class="text-white fw-bold small opacity-75">/ <?= $target_jpl ?> JPL</span>
            </div>
            <a href="<?= base_url('pelatihan/peserta/upload_sertifikat') ?>" class="btn btn-action-global rounded-pill fw-bold shadow-sm px-4 py-2 d-flex align-items-center gap-2" style="background-color: #2563eb; color: white;">
                <i class="fas fa-cloud-upload-alt"></i> UPLOAD SERTIFIKAT
            </a>
        </div>
    </div>

    <?php 
    // Extract unique years for filter
    $currentYear = (int)date('Y');
    $years = [];
    // Selalu tampilkan minimal 5 tahun ke belakang
    for ($i = $currentYear; $i >= $currentYear - 4; $i--) {
        $years[] = (string)$i;
    }
    foreach ($sertifikat as $s) {
        $y = date('Y', strtotime($s['created_at']));
        if (!in_array($y, $years)) $years[] = $y;
    }
    rsort($years);
    ?>

    <!-- Tab & Filter Bar -->
    <div class="mb-4">
        <ul class="nav nav-pills nav-pills-kemenkes mb-3 d-flex flex-wrap gap-2" id="sertifikatTab" role="tablist">
            <li class="nav-item"><button class="nav-link active m-0" data-filter="all" type="button">Semua</button></li>
            <li class="nav-item"><button class="nav-link m-0" data-filter="rsud" type="button">Pelatihan Internal</button></li>
            <li class="nav-item"><button class="nav-link m-0" data-filter="mandiri" type="button">Mandiri</button></li>
            <li class="nav-item"><button class="nav-link m-0" data-filter="surat_tugas" type="button">Surat Tugas</button></li>
            <li class="nav-item"><button class="nav-link m-0" data-filter="pengabdian" type="button">Pengabdian</button></li>
        </ul>

        <div class="row g-2">
            <div class="col-md-6 col-lg-5">
                <div class="search-input-group h-100" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-search text-warning"></i>
                    <input type="text" class="form-control border-0 shadow-none fw-bold text-white" id="searchInput" placeholder="Cari sertifikat..." style="background: transparent;">
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <div class="search-input-group h-100" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-calendar-alt text-warning"></i>
                    <select class="form-select border-0 shadow-none fw-bold text-white" id="monthFilter" style="background: transparent;">
                        <option value="all" class="text-dark">Bulan</option>
                        <option value="01" class="text-dark">Januari</option>
                        <option value="02" class="text-dark">Februari</option>
                        <option value="03" class="text-dark">Maret</option>
                        <option value="04" class="text-dark">April</option>
                        <option value="05" class="text-dark">Mei</option>
                        <option value="06" class="text-dark">Juni</option>
                        <option value="07" class="text-dark">Juli</option>
                        <option value="08" class="text-dark">Agustus</option>
                        <option value="09" class="text-dark">September</option>
                        <option value="10" class="text-dark">Oktober</option>
                        <option value="11" class="text-dark">November</option>
                        <option value="12" class="text-dark">Desember</option>
                    </select>
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <div class="search-input-group h-100" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-filter text-warning"></i>
                    <select class="form-select border-0 shadow-none fw-bold text-white" id="yearFilter" style="background: transparent;">
                        <option value="all" class="text-dark">Tahun</option>
                        <?php foreach($years as $y): ?>
                            <option value="<?= $y ?>" class="text-dark"><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="p-4" id="certContainer">
        <?php if (empty($sertifikat)) : ?>
            <div class="text-center py-5">
                <i class="fas fa-folder-open text-white fa-3x mb-3 opacity-25"></i>
                <p class="text-white opacity-75 fw-bold mb-0">Belum ada data sertifikat yang tercatat.</p>
            </div>
        <?php else : ?>
            <?php foreach ($sertifikat as $s) : ?>
                <?php 
                    $typeClass = strtolower($s['jenis_dokumen'] ?? '');
                    if ($typeClass === 'surat tugas') $typeClass = 'surat_tugas';
                ?>
                <div class="cert-card-horizontal cert-item" data-type="<?= $typeClass ?>" data-year="<?= date('Y', strtotime($s['created_at'])) ?>" data-month="<?= date('m', strtotime($s['created_at'])) ?>" data-title="<?= strtolower(esc($s['judul'])) ?>" data-penerbit="<?= strtolower(esc($s['penerbit'] ?? '')) ?>">
                    <!-- Icon -->
                    <div class="cert-doc-icon">
                        <i class="fas fa-file-contract fa-3x text-white opacity-75"></i>
                    </div>

                    <!-- Info -->
                    <div class="flex-grow-1">
                        <div class="text-white opacity-75 small mb-1">
                            No. Sertifikat: <span class="fw-bold text-white"><?= esc($s['no_sertifikat'] ?? '-') ?></span>
                        </div>
                        <h5 class="fw-bold text-white mb-1 lh-sm"><?= esc($s['judul']) ?></h5>
                        <p class="text-white opacity-75 small mb-2">Penerbit: <?= esc($s['penerbit'] ?? '-') ?></p>

                        <!-- Type & Status Badges -->
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <span class="badge bg-dark rounded-pill text-uppercase px-3 py-1.5 fw-bold" style="font-size:0.6rem;">
                                <?php 
                                    if($s['jenis_dokumen'] == 'rsud') echo 'Diterbitkan RSUD';
                                    elseif($s['jenis_dokumen'] == 'mandiri') echo 'Mandiri / External';
                                    elseif($s['jenis_dokumen'] == 'surat_tugas') echo 'Surat Tugas';
                                    elseif($s['jenis_dokumen'] == 'pengabdian') echo 'Pengabdian';
                                    else echo esc($s['jenis_dokumen']);
                                ?>
                            </span>

                            <?php if ($s['verifikasi'] == 'approved') : ?>
                                <span class="badge bg-success rounded-pill px-3 py-1.5 fw-bold"><i class="fas fa-check-circle me-1"></i> Disetujui (<?= number_format($s['skp'], 0) ?> JPL)</span>
                            <?php elseif ($s['verifikasi'] == 'rejected') : ?>
                                <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-bold cursor-pointer" onclick="showRejectionReason('<?= esc($s['alasan_penolakan']) ?>')" title="Klik untuk lihat alasan penolakan"><i class="fas fa-exclamation-circle me-1"></i> Ditolak (Klik Detail)</span>
                            <?php else : ?>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-bold"><i class="fas fa-clock me-1"></i> Menunggu Verifikasi</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-2 text-white opacity-75 small fw-bold">
                            <i class="far fa-calendar-alt me-1 text-warning"></i> 
                            Pada <?= tanggal_indo($s['tgl_mulai'] ?? 'now') ?> s/d <?= tanggal_indo($s['tgl_selesai'] ?? 'now') ?>
                        </div>
                        
                        <div class="jpl-skp-info text-white">
                            <span><i class="fas fa-user-circle me-1 text-warning"></i> Ranah: <?= esc($s['ranah'] ?? 'Pembelajaran') ?></span>
                            <span><i class="far fa-file-alt me-1 text-warning"></i> Jumlah <?= number_format($s['skp'], 0) ?> Jam Pelajaran (JPL)</span>
                        </div>
                    </div>

                    <!-- Action -->
                    <div class="d-flex flex-column gap-2 text-end" style="min-width: 160px;">
                        <button class="btn btn-sm rounded-lg fw-bold py-2 shadow-sm border text-nowrap" style="background: #ce2127; color: white;" onclick="showCertDetail(<?= htmlspecialchars(json_encode($s)) ?>)">
                            <i class="fas fa-info-circle me-1"></i> Lihat Detail
                        </button>
                        
                        <?php if($s['jenis_dokumen'] == 'rsud'): ?>
                            <a href="<?= base_url('pelatihan/peserta/unduh_sertifikat/' . $s['id']) ?>?auto=1" target="_blank" class="btn btn-sm rounded-lg fw-bold py-2 shadow-sm border text-nowrap" style="background: #ce2127; color: white;">
                                <i class="fas fa-file-pdf me-1"></i> Download PDF
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('pelatihan/peserta/edit_sertifikat/' . $s['id']) ?>" class="btn btn-sm btn-warning shadow-sm fw-bold py-2 rounded-lg text-dark border text-nowrap">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        <?php endif; ?>

                        <?php if(!empty($s['file_path'])): ?>
                            <a href="<?= base_url($s['file_path']) ?>" target="_blank" class="btn btn-sm rounded-lg fw-bold py-2 shadow-sm border text-nowrap" style="background: #f1f5f9; color: #0f172a;">
                                <i class="fas fa-eye me-1 text-danger"></i> Lihat Sertifikat
                            </a>
                        <?php endif; ?>

                        <?php if(!empty($s['surat_tugas_path'])): ?>
                            <a href="<?= base_url($s['surat_tugas_path']) ?>" target="_blank" class="btn btn-sm rounded-lg fw-bold py-2 shadow-sm border text-nowrap" style="background: #e2e8f0; color: #0f172a;">
                                <i class="fas fa-file-alt me-1 text-danger"></i> Surat Tugas
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="certDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Detail Sertifikat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">

                    <h5 class="fw-bold text-dark" id="detailJudul">Judul Pelatihan</h5>
                    <span class="badge bg-dark text-white rounded-pill px-4 py-2 fw-bold" id="detailKategori">Webinar</span>
                </div>

                <div class="card bg-light border-0" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-6">
                                <label class="text-muted small fw-bold d-block mb-1">NOMOR SERTIFIKAT</label>
                                <span class="fw-bold text-dark" id="detailNomor">-</span>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold d-block mb-1">PENERBIT</label>
                                <span class="fw-bold text-dark" id="detailPenerbit">-</span>
                            </div>
                            <div class="col-12"><hr class="my-0 opacity-10"></div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold d-block mb-1">RANAH / KATEGORI</label>
                                <span class="fw-bold text-dark" id="detailRanah">-</span>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold d-block mb-1">TOTAL JPL / SKP</label>
                                <span class="fw-bold text-dark" id="detailJpl">-</span>
                            </div>
                            <div class="col-12"><hr class="my-0 opacity-10"></div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold d-block mb-1">WAKTU PELAKSANAAN</label>
                                <span class="fw-bold text-dark" id="detailWaktu">-</span>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small fw-bold d-block mb-1">TIPE PENGAJUAN</label>
                                <span class="fw-bold text-dark text-uppercase" id="detailTipe">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="detail_berkas_links" class="mt-4 text-center">
                    <!-- Preview will be injected here -->
                </div>
            </div> <!-- End of modal-body -->
            <div class="modal-footer border-0 p-4 pt-0 mt-3">
                <button type="button" class="btn btn-dark w-100 py-3 rounded-pill fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function getPreviewHtml(path, title) {
        if (!path) return '';
        let ext = path.split('.').pop().toLowerCase();
        let baseUrl = "<?= base_url() ?>";
        if (!baseUrl.endsWith('/')) baseUrl += '/';
        let cleanPath = path.startsWith('/') ? path.substring(1) : path;
        let url = baseUrl + cleanPath;
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            return `<div class="mb-4 w-100 text-start"><div class="fw-bold mb-2 text-dark"><i class="fas fa-image text-danger me-1"></i> ${title}:</div><div class="text-center"><img src="${url}" class="img-fluid rounded shadow-sm border" style="max-height: 500px;"></div></div>`;
        } else if (ext === 'pdf') {
            return `<div class="mb-4 w-100 text-start"><div class="fw-bold mb-2 text-dark"><i class="fas fa-file-pdf text-danger me-1"></i> ${title}:</div><iframe src="${url}" class="w-100 rounded border shadow-sm" style="height: 500px;"></iframe></div>`;
        }
        return `<div class="mb-4 w-100 text-start"><div class="fw-bold mb-2 text-dark"><i class="fas fa-file text-muted me-1"></i> ${title}:</div><a href="${url}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3"><i class="fas fa-download me-1"></i> Unduh / Lihat File</a></div>`;
    }

    function showCertDetail(cert) {
        document.getElementById('detailJudul').innerText = cert.judul;
        document.getElementById('detailKategori').innerText = cert.kategori_kegiatan || 'Sertifikat Kegiatan';
        document.getElementById('detailNomor').innerText = cert.no_sertifikat || '-';
        document.getElementById('detailPenerbit').innerText = cert.penerbit || '-';
        document.getElementById('detailRanah').innerText = (cert.ranah || '-') + ' / ' + (cert.kategori_kegiatan || '-');
        document.getElementById('detailJpl').innerText = parseFloat(cert.skp) + ' JPL';
        document.getElementById('detailWaktu').innerText = (cert.tgl_mulai || '') + ' s/d ' + (cert.tgl_selesai || '');
        document.getElementById('detailTipe').innerText = cert.jenis_dokumen || '-';

        let baseUrl = "<?= base_url('/') ?>";
        if (!baseUrl.endsWith('/')) baseUrl += '/';

        const berkasLinks = document.getElementById('detail_berkas_links');
        berkasLinks.innerHTML = '';
        
        if (cert.jenis_dokumen === 'rsud') {
            berkasLinks.innerHTML += `<div class="mb-4 w-100 text-start"><div class="fw-bold mb-2 text-dark"><i class="fas fa-certificate text-danger me-1"></i> Sertifikat (Otomatis RSUD):</div><a href="${baseUrl}pelatihan/peserta/unduh_sertifikat/${cert.id}?auto=1" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3 w-100 py-2"><i class="fas fa-download me-1"></i> Unduh Sertifikat RSUD</a></div>`;
        } else if (cert.file_path) {
            berkasLinks.innerHTML += getPreviewHtml(cert.file_path, 'Dokumen Sertifikat/Bukti');
        }

        const typeLower = (cert.jenis_dokumen || '').toLowerCase();
        if ((typeLower === 'surat tugas' || typeLower === 'surat_tugas') && cert.surat_tugas_path) {
            berkasLinks.innerHTML += getPreviewHtml(cert.surat_tugas_path, 'Surat Tugas');
        }

        if (!cert.file_path && !cert.surat_tugas_path && cert.jenis_dokumen !== 'rsud') {
            berkasLinks.innerHTML = '<span class="text-muted small">Tidak ada lampiran berkas</span>';
        }

        new bootstrap.Modal(document.getElementById('certDetailModal')).show();
    }

    function showRejectionReason(reason) {
        Swal.fire({
            title: '<span class="fw-bold fs-5 text-dark"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Alasan Penolakan</span>',
            text: reason || 'Berkas tidak sesuai ketentuan.',
            icon: 'error',
            confirmButtonColor: '#ce2127',
            confirmButtonText: 'Tutup'
        });
    }

    // Filter Logic
    document.addEventListener('DOMContentLoaded', () => {
        const tabBtns = document.querySelectorAll('.nav-pills-kemenkes .nav-link');
        const yearFilter = document.getElementById('yearFilter');
        const monthFilter = document.getElementById('monthFilter');
        const searchInput = document.getElementById('searchInput');
        const items = document.querySelectorAll('.cert-item');

        let activeType = 'all';
        let activeYear = 'all';
        let activeMonth = 'all';
        let searchQuery = '';

        function filterItems() {
            items.forEach(item => {
                const itemType = item.getAttribute('data-type');
                const itemYear = item.getAttribute('data-year');
                const itemMonth = item.getAttribute('data-month');
                const itemTitle = item.getAttribute('data-title');
                const itemPenerbit = item.getAttribute('data-penerbit');
                
                const typeMatch = (activeType === 'all' || itemType === activeType);
                const yearMatch = (activeYear === 'all' || itemYear === activeYear);
                const monthMatch = (activeMonth === 'all' || itemMonth === activeMonth);
                const searchMatch = (searchQuery === '' || itemTitle.includes(searchQuery) || itemPenerbit.includes(searchQuery));

                if (typeMatch && yearMatch && monthMatch && searchMatch) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        tabBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                tabBtns.forEach(b => b.classList.remove('active'));
                e.target.classList.add('active');
                activeType = e.target.getAttribute('data-filter');
                filterItems();
            });
        });

        yearFilter.addEventListener('change', (e) => {
            activeYear = e.target.value;
            filterItems();
        });

        monthFilter.addEventListener('change', (e) => {
            activeMonth = e.target.value;
            filterItems();
        });

        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase();
            filterItems();
        });
    });
</script>

<?= $this->endSection() ?>
