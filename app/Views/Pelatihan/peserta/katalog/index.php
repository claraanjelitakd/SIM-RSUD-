<?php
$pelatihan = $pelatihan ?? [];
$filters = $filters ?? ['program' => [], 'kategori' => [], 'mekanisme' => [], 'cakupan' => []];
$req = $req ?? [];
?>
<?= $this->extend('Pelatihan/layout/peserta_layout') ?>

<?= $this->section('content') ?>

<div class="glass-wrapper-global">
    <!-- Header Section -->
    <div class="mb-4 animate__animated animate__fadeIn">
        <h3 class="fw-bold mb-3 text-white">Program Diklat & Pelatihan</h3>
        <div class="highlight-bounce mt-2 d-inline-block">
            <span class="badge bg-warning text-dark px-3 py-2 fw-bold shadow-sm" style="font-size: 0.9rem;">
                <i class="fas fa-sparkles me-1 text-danger"></i> Temukan dan ikuti program pelatihan terbaik untuk meningkatkan kompetensi dan profesionalitas Anda.
            </span>
        </div>
    </div>


    <!-- Filter & Search Section -->
    <div class="glass-card-global mb-4">
        <div class="p-3 p-md-4">
            <!-- Mobile Toggle Button -->
            <button class="btn btn-outline-light w-100 d-md-none fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="fas fa-filter me-2"></i> Tampilkan Filter Pencarian
            </button>
            
            <div class="collapse d-md-block mt-3 mt-md-0" id="filterCollapse">
                <form id="filterForm" onsubmit="event.preventDefault();">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Pencarian</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari nama pelatihan..." value="<?= $req['search'] ?? '' ?>" oninput="filterCourses()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Program</label>
                            <select id="programFilter" class="form-select shadow-none" onchange="filterCourses()">
                                <option value="">Semua</option>
                                <?php foreach($filters['program'] as $f): ?>
                                    <option value="<?= $f['program'] ?>" <?= isset($req['program']) && $req['program'] == $f['program'] ? 'selected' : '' ?>><?= $f['program'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Kategori</label>
                            <select id="kategoriFilter" class="form-select shadow-none" onchange="filterCourses()">
                                <option value="">Semua</option>
                                <?php foreach($filters['kategori'] as $f): ?>
                                    <option value="<?= $f['kategori'] ?>" <?= isset($req['kategori']) && $req['kategori'] == $f['kategori'] ? 'selected' : '' ?>><?= $f['kategori'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted">Mekanisme</label>
                            <select id="mekanismeFilter" class="form-select shadow-none" onchange="filterCourses()">
                                <option value="">Semua</option>
                                <?php foreach($filters['mekanisme'] as $f): ?>
                                    <option value="<?= $f['mekanisme'] ?>" <?= isset($req['mekanisme']) && $req['mekanisme'] == $f['mekanisme'] ? 'selected' : '' ?>><?= $f['mekanisme'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-white opacity-75">Target Profesi</label>
                            <div class="dropdown">
                                <button class="btn btn-dark form-select shadow-none text-start text-truncate" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.2);" id="sasaranDropdownBtn">
                                    Pilih Profesi
                                </button>
                                <ul class="dropdown-menu w-100 p-2 shadow-sm" style="max-height: 250px; overflow-y: auto;" id="sasaranDropdownMenu">
                                    <?php
                                    $selectedSasaran = isset($req['sasaran']) && is_array($req['sasaran']) ? $req['sasaran'] : [];
                                    foreach($filters['profesi'] as $prof): 
                                        $isChecked = in_array($prof['nama_profesi'], $selectedSasaran) ? 'checked' : '';
                                    ?>
                                        <li>
                                            <div class="form-check">
                                                <input class="form-check-input sasaran-checkbox" type="checkbox" value="<?= $prof['nama_profesi'] ?>" id="profesi_<?= $prof['id'] ?>" onchange="updateSasaranBtn(); filterCourses();" <?= $isChecked ?>>
                                                <label class="form-check-label" for="profesi_<?= $prof['id'] ?>">
                                                    <?= $prof['nama_profesi'] ?>
                                                </label>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="col-12 text-end mt-3">
                            <a href="<?= base_url('pelatihan/peserta/pembelajaran') ?>" class="btn btn-light text-danger fw-bold rounded-pill px-4 me-2">Reset</a>
                            <!-- Removed submit button -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Grid Layout -->
    <?php if (empty($pelatihan)) : ?>
        <div class="glass-card-global p-5 text-center animate__animated animate__fadeIn">
            <div class="py-5 text-white opacity-75">
                <i class="fas fa-folder-open fa-4x mb-3 opacity-50"></i>
                <h5 class="fw-bold text-white">Belum ada pelatihan tersedia</h5>
                <p class="mb-0">Silakan kembali lagi nanti untuk melihat program pelatihan yang dipublikasikan.</p>
            </div>
        </div>
    <?php else : ?>
        <div class="row g-4">
            <?php foreach ($pelatihan as $p) : ?>
                <?php
                    $gambarPelatihan = !empty($p['gambar_pelatihan'])
                        ? base_url($p['gambar_pelatihan'])
                        : null;
                ?>
                <div class="col-md-6 col-lg-4 course-card-wrapper" 
                     data-title="<?= esc(strtolower($p['nama'])) ?>" 
                     data-program="<?= esc(strtolower($p['program'] ?? '')) ?>" 
                     data-kategori="<?= esc(strtolower($p['kategori'] ?? '')) ?>" 
                     data-mekanisme="<?= esc(strtolower($p['mekanisme'] ?? '')) ?>" 
                     data-sasaran="<?= esc(strtolower(($p['target_profesi'] ?? '') . ' ' . ($p['target_khusus_profesi'] ?? ''))) ?>">
                    <a href="<?= base_url('pelatihan/peserta/detail_pelatihan/'.$p['id']) ?>" class="text-decoration-none">
                        <div class="glass-card-global h-100 p-0 overflow-hidden animate__animated animate__fadeInUp hover-card-premium" style="transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);">
                            <div class="position-relative">
                                <div class="course-img-wrapper position-relative d-flex align-items-center justify-content-center" style="height: 180px; overflow: hidden; background: radial-gradient(circle at 20% 50%, #1f2937 0%, #0f172a 100%);">
                                    <?php if ($gambarPelatihan): ?>
                                        <img src="<?= $gambarPelatihan ?>" alt="<?= esc($p['nama']) ?>" class="w-100 h-100" style="object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="default-course-cover" style="display: none;">
                                            <div class="fw-black text-white text-center px-4" style="line-height: 1.3; font-size: 1.1rem;"><?= esc($p['nama']) ?></div>
                                            <small class="text-white-50 fw-bold mt-2">RSUD KOTA YOGYAKARTA</small>
                                        </div>
                                    <?php else: ?>
                                        <div class="default-course-cover">
                                            <div class="fw-black text-white text-center px-4" style="line-height: 1.3; font-size: 1.1rem;"><?= esc($p['nama']) ?></div>
                                            <small class="text-white-50 fw-bold mt-2">RSUD KOTA YOGYAKARTA</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                                    <span class="badge bg-white text-dark shadow-sm border-0 px-3 py-2 fw-bold" style="font-size: 0.65rem; border-radius: 8px;"><?= strtoupper($p['kategori']) ?></span>
                                </div>
                                <div class="position-absolute top-0 end-0 m-3 d-flex flex-column gap-2">
                                    <span class="badge bg-dark text-white shadow-lg px-3 py-2 fw-extrabold" style="border-radius: 8px; border: 1px solid rgba(255,255,255,0.2);"><?= strtoupper($p['biaya']) ?></span>
                                    <span class="badge bg-danger text-white shadow-lg px-3 py-2 fw-extrabold" style="border-radius: 8px;"><?= strtoupper($p['mekanisme']) ?></span>
                                </div>
                            </div>
                            <div class="p-4 d-flex flex-column h-100">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <?php if (!empty($p['rating'])): ?>
                                    <span class="text-white opacity-75 small fw-bold"><i class="fas fa-star me-1 text-warning"></i> <?= $p['rating'] ?></span>
                                    <span class="text-white opacity-75 small fw-bold">|</span>
                                    <?php endif; ?>
                                    <span class="text-white opacity-75 small fw-bold"><i class="fas fa-clock me-1 text-warning"></i> <?= $p['jpl'] ?> JPL</span>
                                    <?php if (!empty($p['level_pelatihan']) || !empty($p['level'])): ?>
                                    <span class="badge bg-white text-dark fw-bold border-0 px-3 py-1 shadow-sm" style="font-size: 0.65rem; border-radius: 6px;"><?= strtoupper($p['level_pelatihan'] ?? $p['level'] ?? '') ?></span>
                                    <?php endif; ?>
                                </div>
                                <h5 class="fw-bold text-white mb-2" style="font-size: 1.15rem; min-height: 2.8rem; line-height: 1.3;"><?= esc($p['nama']) ?></h5>
                                <?php
                                    $penyArr = array_map('trim', explode(',', $p['penyelenggara'] ?? 'RSUD Kota Yogyakarta'));
                                    $penyUnique = array_unique($penyArr);
                                    $penyStr = implode(', ', $penyUnique);
                                ?>
                                <div class="small opacity-75 fw-bold mb-4 d-flex align-items-center text-white">
                                    <div class="bg-white bg-opacity-10 p-2 rounded-circle me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-hospital text-white small"></i>
                                    </div>
                                    <?= strtoupper(esc($penyStr)) ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3" style="border-color: rgba(255,255,255,0.1) !important;">
                                    <div class="small text-white fw-bold">
                                        <i class="fas fa-users-viewfinder me-1 text-warning"></i> <?= $p['peserta'] ?> / <?= $p['kuota'] ?> <span class="opacity-75 fw-normal ms-1">PESERTA</span>
                                    </div>
                                    <span class="btn btn-action-global btn-sm rounded-pill px-4 fw-bold shadow-sm btn-select" style="background-color: #2563eb; color: white;">Pilih Diklat</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- JS Empty State -->
        <div id="jsEmptyState" class="glass-card-global p-5 text-center animate__animated animate__fadeIn mt-4" style="display: none;">
            <div class="py-5 text-white opacity-75">
                <i class="fas fa-search fa-4x mb-3 opacity-50"></i>
                <h5 class="fw-bold text-white">Pelatihan tidak ditemukan</h5>
                <p class="mb-0">Tidak ada pelatihan yang cocok dengan filter pencarian Anda.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.hover-card-premium:hover { 
    border-color: rgba(255,255,255,0.5) !important; 
    transform: translateY(-10px); 
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important; 
    background: rgba(255,255,255,0.15) !important;
}
.hover-card-premium:hover .btn-select {
    background: #ce2127 !important;
    color: white !important;
    border-color: #ce2127 !important;
}
.hover-card-premium:hover .card-title-hover {
    color: #ce2127 !important;
}
.card-title-hover {
    transition: color 0.3s ease;
}
.default-course-cover {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0a0a0a 0%, #ce2127 100%);
}
@keyframes bounceHighlight {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
.highlight-bounce {
    animation: bounceHighlight 2s infinite ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    filterCourses(); 
});

function updateSasaranBtn() {
    const checkboxes = document.querySelectorAll('.sasaran-checkbox:checked');
    const btn = document.getElementById('sasaranDropdownBtn');
    if (checkboxes.length === 0) {
        btn.innerText = 'Pilih Profesi';
    } else if (checkboxes.length === 1) {
        btn.innerText = checkboxes[0].value;
    } else {
        btn.innerText = checkboxes.length + ' Profesi Dipilih';
    }
}

document.addEventListener('DOMContentLoaded', updateSasaranBtn);

document.getElementById('sasaranDropdownMenu').addEventListener('click', function(e) {
    e.stopPropagation();
});

function filterCourses() {
    let search = document.getElementById('searchInput').value.toLowerCase();
    let program = document.getElementById('programFilter').value.toLowerCase();
    let kategori = document.getElementById('kategoriFilter').value.toLowerCase();
    let mekanisme = document.getElementById('mekanismeFilter').value.toLowerCase();
    
    let sasaranCheckboxes = document.querySelectorAll('.sasaran-checkbox:checked');
    let sasaranValues = Array.from(sasaranCheckboxes).map(cb => cb.value.toLowerCase());

    const cards = document.querySelectorAll('.course-card-wrapper');
    let visibleCount = 0;

    cards.forEach(card => {
        let courseTitle = card.getAttribute('data-title') || '';
        let courseProgram = card.getAttribute('data-program') || '';
        let courseKategori = card.getAttribute('data-kategori') || '';
        let courseMekanisme = card.getAttribute('data-mekanisme') || '';
        let courseSasaran = card.getAttribute('data-sasaran') || '';
        
        let matchSasaran = false;
        if (sasaranValues.length === 0) {
            matchSasaran = true;
        } else {
            for (let s of sasaranValues) {
                if (courseSasaran.includes(s)) {
                    matchSasaran = true;
                    break;
                }
            }
        }

        if (courseTitle.includes(search) &&
            (program === "" || courseProgram === program) &&
            (kategori === "" || courseKategori === kategori) &&
            (mekanisme === "" || courseMekanisme === mekanisme) &&
            matchSasaran) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const emptyState = document.getElementById('emptyState');
    if (emptyState) {
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    let params = new URLSearchParams();
    if (search) params.append('search', search);
    if (program) params.append('program', document.getElementById('programFilter').value);
    if (kategori) params.append('kategori', document.getElementById('kategoriFilter').value);
    if (mekanisme) params.append('mekanisme', document.getElementById('mekanismeFilter').value);
    
    sasaranCheckboxes.forEach(cb => {
        params.append('sasaran[]', cb.value);
    });

    window.history.replaceState({}, '', '?' + params.toString());
}
/*
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Prevent form submission on enter since it auto-submits
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });

        // Set cursor to end if there's a value
        if (searchInput.value.length > 0) {
            searchInput.focus();
            let val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 600); // Wait 600ms after typing stops before submitting
        });
    }
});
*/
</script>

<?= $this->endSection() ?>
