<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?php
/**
 * @var int|string $selectedYear
 * @var array $stats
 * @var array $profesiList
 * @var array $ruanganStats
 * @var int $totalTidakAktif
 * @var int $totalKaryawan
 * @var int $totalTargetJPL
 * @var int $totalJPLCapaian
 * @var int $totalKurangJPL
 * @var float|int $rataRataJPL
 */
?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.1/css/rowGroup.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .nav-pills .nav-link { color: #6c757d; }
    .nav-pills .nav-link.active { color: #fff !important; }
</style>

<div class="container-fluid px-0">


    <?php
    $kurang = count(array_filter($stats, fn($p) => $p['jpl'] < $p['target_jpl']));
    $cukup = count(array_filter($stats, fn($p) => $p['jpl'] >= $p['target_jpl']));
    ?>
    <div class="row mb-4 g-3">
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-custom bg-white h-100" style="cursor:pointer;" onclick="window.location.href='<?= site_url("pelatihan/admin/monitoring/detail_stat/all?tahun=$selectedYear") ?>'">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1 fw-bold" style="font-size: 0.65rem;">TOTAL KARYAWAN AKTIF</h6>
                        <h3 class="mb-0 fw-bold text-dark"><?= count($stats) ?> <span class="fs-6 text-muted fw-normal">Orang</span></h3>
                    </div>
                    <div class="bg-dark text-white p-3 rounded-circle d-flex align-items-center justify-content-center" style="border: 2px solid #ce2127; width:48px; height:48px;"><i class="fas fa-users text-danger"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-custom bg-white h-100 border-bottom border-danger border-3" style="cursor:pointer;" onclick="window.location.href='<?= site_url("pelatihan/admin/monitoring/detail_stat/kurang?tahun=$selectedYear") ?>'">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1 fw-bold" style="font-size: 0.65rem;">BELUM MEMENUHI TARGET</h6>
                        <h3 class="mb-0 fw-bold text-danger"><?= $kurang ?> <span class="fs-6 text-muted fw-normal">Orang</span></h3>
                    </div>
                    <div class="bg-light text-dark p-3 rounded-circle d-flex align-items-center justify-content-center" style="border: 1px solid #dee2e6; width:48px; height:48px;"><i class="fas fa-exclamation-triangle text-danger"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-custom bg-white h-100 border-bottom border-dark border-3" style="cursor:pointer;" onclick="window.location.href='<?= site_url("pelatihan/admin/monitoring/detail_stat/cukup?tahun=$selectedYear") ?>'">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1 fw-bold" style="font-size: 0.65rem;">SUDAH MEMENUHI TARGET</h6>
                        <h3 class="mb-0 fw-bold text-dark"><?= $cukup ?> <span class="fs-6 text-muted fw-normal">Orang</span></h3>
                    </div>
                    <div class="bg-dark text-white p-3 rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px;"><i class="fas fa-check-circle text-white"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-custom bg-white h-100 border-bottom border-light border-3" style="cursor:pointer;" onclick="window.location.href='<?= site_url("pelatihan/admin/monitoring/detail_stat/tidak_aktif?tahun=$selectedYear") ?>'">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1 fw-bold" style="font-size: 0.65rem;">KARYAWAN TIDAK AKTIF</h6>
                        <h3 class="mb-0 fw-bold text-secondary"><?= $totalTidakAktif ?> <span class="fs-6 text-muted fw-normal">Orang</span></h3>
                    </div>
                    <div class="bg-light text-secondary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px;"><i class="fas fa-user-slash"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl">
            <div class="card border-0 shadow-sm rounded-custom bg-white h-100 border-bottom border-info border-3" style="cursor:pointer;" onclick="window.location.href='<?= site_url("pelatihan/admin/monitoring/export_jpl_excel?tahun=$selectedYear") ?>'">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted mb-1 fw-bold" style="font-size: 0.65rem;">RATA RATA JPL</h6>
                        <h3 class="mb-0 fw-bold text-info"><?= number_format($rataRataJPL, 1) ?> <span class="fs-6 text-muted fw-normal">JPL</span></h3>
                    </div>
                    <div class="bg-light text-info p-3 rounded-circle d-flex align-items-center justify-content-center" style="border: 1px solid #dee2e6; width:48px; height:48px;"><i class="fas fa-file-excel text-info"></i></div>
                </div>
            </div>
        </div>
    </div>

    <?php $activeTab = session()->getFlashdata('active_tab') ?: 'ruangan'; ?>
    <!-- Main Navigation Tabs & Filter -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <ul class="nav nav-pills bg-white p-2 rounded-custom shadow-sm border m-0" id="diklatTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'ruangan' ? 'active' : '' ?> fw-bold px-4 rounded-pill py-2.5" id="ruangan-tab" data-bs-toggle="tab" data-bs-target="#tab-ruangan" type="button" role="tab">
                    <i class="fas fa-hospital me-2"></i> Analisis Per Ruangan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'matriks' ? 'active' : '' ?> fw-bold px-4 rounded-pill py-2.5" id="matriks-tab" data-bs-toggle="tab" data-bs-target="#tab-matriks" type="button" role="tab">
                    <i class="fas fa-sitemap me-2"></i> Matriks Target Kelompok
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= $activeTab === 'history' ? 'active' : '' ?> fw-bold px-4 rounded-pill py-2.5" id="history-tab" data-bs-toggle="tab" data-bs-target="#tab-history" type="button" role="tab">
                    <i class="fas fa-history me-2"></i> Riwayat JPL Peserta
                </button>
            </li>
        </ul>

        <div class="d-flex gap-2 align-items-center bg-white p-2 px-4 rounded-custom shadow-sm border">
            <label class="fw-bold small text-muted text-uppercase mb-0 me-2"><i class="fas fa-calendar-alt me-1 text-danger"></i> Tahun Evaluasi:</label>
            <select id="selectTahun" class="form-select form-select-sm border shadow-sm fw-bold text-danger" style="width: 110px; border-radius: 20px;" onchange="changeYear(this.value)">
                <?php
                $curYear = date('Y');
                for ($y = $curYear + 1; $y >= $curYear - 2; $y--):
                ?>
                    <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>

    <div class="tab-content" id="diklatTabContent">

        <div class="tab-pane fade <?= $activeTab === 'matriks' ? 'show active' : '' ?>" id="tab-matriks" role="tabpanel">
            <form action="<?= base_url('pelatihan/admin/monitoring/save_mapping_kelompok') ?>" method="POST" id="formMatriks">
                <div class="card border-0 shadow-sm rounded-custom overflow-hidden bg-white mb-5 border-bottom border-dark border-3">
                    <div class="card-header bg-white p-4 border-bottom">
                        <h5 class="fw-bold mb-1 text-dark">Matriks Target JPL per Profesi</h5>
                        <p class="text-muted small mb-0">Tentukan target JPL tahunan minimal untuk masing-masing profesi.</p>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="tableProfesiMapping" class="table table-hover align-middle mb-0 w-100">
                                <thead class="bg-light text-muted fw-bold uppercase-tracking">
                                    <tr>
                                        <th class="ps-4 py-3">NAMA PROFESI / JABATAN</th>
                                        <th class="pe-4 text-center" style="width: 300px;">TARGET JPL TAHUNAN MINIMAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($profesiList as $p): ?>
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <i class="fas fa-id-card text-muted me-2"></i>
                                                <a href="javascript:void(0)" onclick="showProfesiPeserta(<?= $p['id_profesi'] ?>, '<?= esc($p['nama_profesi']) ?>')" class="fw-bold text-dark text-decoration-none text-hover-red"><?= esc($p['nama_profesi']) ?></a>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <div class="input-group input-group-sm mx-auto" style="max-width: 150px;">
                                                    <input type="number" name="profesi_target[<?= $p['id_profesi'] ?>]" class="form-control text-center fw-bold border-dark" value="<?= esc($p['target_jpl']) ?>" min="0" required>
                                                    <span class="input-group-text bg-dark text-white small border-dark">JPL / Thn</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white p-4 text-end border-top">
                        <button type="submit" class="btn btn-danger rounded-pill px-5 py-2 fw-bold shadow">
                            <i class="fas fa-save me-2"></i> SIMPAN TARGET PROFESI
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane fade <?= $activeTab === 'ruangan' ? 'show active' : '' ?>" id="tab-ruangan" role="tabpanel">
            <div class="card-body p-0">
                <div class="table-responsive p-4 pt-2">
                    <table id="tableRuangan" class="table table-hover align-middle mb-0 w-100">
                        <thead class="bg-light text-muted small fw-bold">
                            <tr>
                                <th>NAMA RUANGAN</th>
                                <th class="text-center">TOTAL KARYAWAN</th>
                                <th class="text-center">TOTAL TARGET</th>
                                <th class="text-center">TOTAL CAPAIAN</th>
                                <th class="text-center">KEKURANGAN</th>
                                <th class="text-center">PEGAWAI AMAN</th>
                                <th class="text-center">PEGAWAI KURANG</th>
                                <th class="text-center">% TERCAPAI</th>
                                <th class="text-center" style="width:100px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ruanganStats as $r):
                                $totalSudah = count($r['sudah']);
                                $totalBelum = count($r['belum']);
                                $total = $r['total'];

                                $totalTargetRuangan = 0;
                                $totalCapaianRuangan = 0;
                                $totalKurangRuangan = 0;
                                foreach (array_merge($r['sudah'], $r['belum']) as $s) {
                                    $totalTargetRuangan += $s['target'];
                                    // Cap individual jpl to target so overachievers don't skew the room's total percentage more than 100% per person
                                    $jplCapped = $s['jpl'] > $s['target'] ? $s['target'] : $s['jpl'];
                                    $totalCapaianRuangan += $jplCapped;
                                    $totalKurangRuangan += $s['kurang'];
                                }
                                
                                $pctTercapai = $totalTargetRuangan > 0 ? ($totalCapaianRuangan / $totalTargetRuangan) * 100 : 0;
                            ?>
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <i class="fas fa-hospital-user text-danger me-2"></i> <?= esc($r['nama_unit']) ?>
                                    </td>
                                    <td class="text-center fw-bold text-dark"><?= $total ?></td>
                                    <td class="text-center text-muted"><?= $totalTargetRuangan ?></td>
                                    <td class="text-center text-dark fw-bold">
                                        <?php
                                            $actualTotalCapaian = 0;
                                            foreach (array_merge($r['sudah'], $r['belum']) as $s) {
                                                $actualTotalCapaian += $s['jpl'];
                                            }
                                            echo $actualTotalCapaian;
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold <?= $totalKurangRuangan > 0 ? 'text-danger' : 'text-dark' ?>"><?= $totalKurangRuangan ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-dark text-white rounded-pill fw-bold"><?= $totalSudah ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-danger border border-danger rounded-pill fw-bold"><?= $totalBelum ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center gap-2 justify-content-center">
                                            <div class="progress shadow-sm flex-grow-1" style="height: 6px; border-radius: 3px; max-width: 60px; background-color:#f1f5f9;">
                                                <div class="progress-bar bg-danger" style="width: <?= $pctTercapai ?>%"></div>
                                            </div>
                                            <span class="fw-bold small"><?= number_format($pctTercapai, 1) ?>%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <?php if (($totalSudah + $totalBelum) > 0): ?>
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 fw-bold" style="font-size:0.65rem;" title="Ingatkan seluruh pegawai ruangan" onclick='remindRoom(<?= json_encode($r['nama_unit']) ?>, <?= json_encode(array_merge($r['sudah'], $r['belum'])) ?>)'>
                                                    <i class="fas fa-bell"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-2" style="font-size:0.65rem;" title="Detail Sudah Target" onclick='showRoomDetails("Sudah Target - <?= esc($r['nama_unit']) ?>", <?= json_encode($r['sudah']) ?>)'>
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" style="font-size:0.65rem;" title="Detail Belum Target" onclick='showRoomDetails("Belum Target - <?= esc($r['nama_unit']) ?>", <?= json_encode($r['belum']) ?>)'>
                                                <i class="fas fa-exclamation"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade <?= $activeTab === 'history' ? 'show active' : '' ?>" id="tab-history" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-custom overflow-hidden bg-white mb-5 border-bottom border-dark border-3">
            <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Daftar Riwayat JPL Peserta</h5>
                    <p class="text-muted small mb-0">Lihat histori capaian JPL masing-masing peserta.</p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-4 pt-2">
                    <table id="tableHistory" class="table table-hover align-middle mb-0 w-100">
                        <thead class="bg-light text-muted small fw-bold">
                            <tr>
                                <th>NAMA / NIK</th>
                                <th>PROFESI / JABATAN</th>
                                <th>RUANGAN</th>
                                <th class="text-center">CAPAIAN JPL</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats as $s): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-uppercase text-dark"><?= esc($s['nama']) ?></div>
                                        <div class="text-muted font-monospace" style="font-size:0.65rem;"><?= esc($s['nik']) ?></div>
                                    </td>
                                    <td class="text-muted small"><?= esc($s['profesi']) ?></td>
                                    <td class="text-muted small"><?= esc($s['divisi']) ?></td>
                                    <td class="text-center fw-bold">
                                        <span class="<?= $s['jpl'] >= $s['target_jpl'] ? 'text-dark' : 'text-danger' ?>"><?= $s['jpl'] ?></span> / <?= $s['target_jpl'] ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick='openHistoryModal(<?= json_encode($s) ?>)'>
                                                <i class="fas fa-history me-1"></i> Detail
                                            </button>
                                            <?php if ($s['jpl'] < $s['target_jpl']): ?>
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold" onclick='openRemindModal(<?= json_encode(["nik" => $s["nik"], "nama" => $s["nama"], "jpl" => $s["jpl"], "target" => $s["target_jpl"], "kurang" => max(0, $s["target_jpl"] - $s["jpl"])]) ?>)'>
                                                <i class="fas fa-bell me-1"></i> Ingatkan
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<div class="modal fade" id="modalHistory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-custom">
            <div class="modal-header bg-light border-0 px-4 py-3">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-history text-danger me-2"></i> Riwayat JPL Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-custom border">
                    <div class="avatar bg-dark text-white rounded-circle p-2.5 me-3 text-center d-flex align-items-center justify-content-center fw-bold" style="width: 42px; height: 42px; border: 2px solid #ce2127;">
                        <span id="histAvatar">?</span>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark id-text" id="histNama">-</h6>
                        <span class="text-muted small font-monospace" id="histNik">-</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark small text-uppercase mb-0">Pelatihan Yang Diselesaikan:</h6>
                    <select id="histYearFilter" class="form-select form-select-sm border-dark w-auto fw-bold" onchange="fetchHistory()">
                        <?php
                        $curYear = date('Y');
                        for ($y = $curYear; $y >= $curYear - 3; $y--):
                        ?>
                            <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div id="historyListContainer" class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer bg-light border-0 px-4 py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-warning rounded-pill px-4 fw-bold small" onclick="let emp=statsGlobal.find(s=>s.nik===currentHistoryNik);if(emp)openRemindModal({nik:emp.nik,nama:emp.nama,jpl:emp.jpl,target:emp.target_jpl,kurang:Math.max(0,emp.target_jpl-emp.jpl)});">
                    <i class="fas fa-bell me-1"></i> Kirim Notifikasi
                </button>
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold small" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-custom {
        border-radius: 1rem !important;
    }

    .uppercase-tracking {
        letter-spacing: 0.6px;
        font-size: 0.72rem !important;
    }

    .text-hover-red:hover {
        color: #ce2127 !important;
    }

    /* Nav Tabs Active State */
    .nav-pills .nav-link {
        color: #6c757d !important;
    }
    .nav-pills .nav-link.active {
        background-color: #ce2127 !important;
        color: white !important;
    }

    #tablePeserta tr.dtrg-group th {
        background-color: #212529 !important;
        color: #ffffff !important;
        font-weight: 700;
        padding: 10px 16px;
        font-size: 0.8rem;
        border-top: 1px solid #212529;
    }

    #customSearch:focus {
        box-shadow: none !important;
        border-color: transparent !important;
    }

    /* Override Paginasi DataTables */
    .page-item.active .page-link {
        background-color: #ce2127 !important;
        border-color: #ce2127 !important;
        color: #fff !important;
    }

    .page-link {
        color: #212529 !important;
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
    }

    .page-link:hover {
        color: #ce2127 !important;
        background-color: #fff5f5 !important;
        border-color: #dee2e6 !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function changeYear(yr) {
        window.location.href = "<?= base_url('pelatihan/admin/monitoring') ?>?tahun=" + yr;
    }

    $(document).ready(function() {
        // ==========================================
        //  KONFIGURASI TAB 3: ANALISIS PER RUANGAN
        // ==========================================
        var tableRuangan = $('#tableRuangan').DataTable({
            pageLength: 25,
            responsive: false,
            dom: 'lrtip',
            order: [
                [0, 'asc']
            ],
            language: {
                emptyTable: "Tidak ada data ruangan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ ruangan",
                paginate: {
                    next: "›",
                    previous: "‹"
                }
            }
        });

        // Filter Input Pencarian Ruangan
        $('#searchRuangan').on('keyup change', function() {
            tableRuangan.search(this.value).draw();
        });

        // Filter Dropdown Nama Ruangan spesifik (Kolom ke-1 indeks 0)
        $('#filterRuangan').on('change', function() {
            var selectedRoom = $(this).val();
            tableRuangan.column(0).search(selectedRoom ? '^' + $.fn.dataTable.util.escapeRegex(selectedRoom) + '$' : '', true, false).draw();
        });

        // Filter Custom DataTables Khusus untuk Status Target Persentase (≥80% atau <80%)
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'tableRuangan') return true;

            var filterStatus = $('#filterRuanganStatus').val();
            if (!filterStatus) return true;

            // Baca langsung dari DOM cell untuk menghindari masalah data-search
            var rowNode = settings.aoData[dataIndex].nTr;
            if (!rowNode) return true;
            var cellText = $(rowNode).find('td').eq(7).text().trim();
            var pct = parseFloat(cellText.replace(/[^0-9,]/g, '').replace(',', '.'));

            if (isNaN(pct)) return true;
            return filterStatus === 'Tercapai' ? pct >= 80 : pct < 80;
        });

        // Jalankan draw ulang ketika filter status target diubah
        $('#filterRuanganStatus').on('change', function() {
            tableRuangan.draw();
        });

        // Fix Bug Bootstrap Tab: Menjaga lebar kolom table agar tidak reot/rusak saat tab diklik
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust();
        });

        // Initialize Table History
        $('#tableHistory').DataTable({
            pageLength: 20,
            ordering: true,
            info: true,
            lengthChange: false,
            language: {
                search: "",
                searchPlaceholder: "Cari Nama / NIK Karyawan...",
                emptyTable: "Tidak ada data riwayat JPL",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ karyawan",
                paginate: {
                    next: "›",
                    previous: "‹"
                }
            }
        });
    });

    function changeYear(year) {
        window.location.href = '<?= base_url("pelatihan/admin/monitoring") ?>?tahun=' + year;
    }

    let currentHistoryNik = null;
    let currentHistoryUser = null;
    const statsGlobal = <?= json_encode($stats) ?>;

    function showStatList(title, type) {
        let list = [];
        if (type === 'all') {
            list = statsGlobal;
        } else if (type === 'kurang') {
            list = statsGlobal.filter(s => s.jpl < s.target_jpl);
        } else if (type === 'cukup') {
            list = statsGlobal.filter(s => s.jpl >= s.target_jpl);
        } else if (type === 'tidak_aktif') {
            list = statsGlobal.filter(s => s.pelatihan === 'Belum Ada');
        }

        let contentHtml = `
            <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                <table class="table table-sm table-hover align-middle text-start mb-0" style="font-size:0.78rem;">
                    <thead class="table-light text-muted fw-bold">
                        <tr>
                            <th>NAMA / NIK</th>
                            <th>PROFESI</th>
                            <th class="text-center">JPL / TARGET</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        if (list.length > 0) {
            list.forEach(emp => {
                let jplClass = emp.jpl < emp.target_jpl ? 'text-danger fw-bold' : 'text-dark fw-bold';
                let remindBtn = emp.jpl < emp.target_jpl ?
                    `<button class="btn btn-xs btn-outline-warning rounded-pill fw-bold px-2 py-0" style="font-size:0.6rem;" onclick="Swal.close(); openRemindModal({nik:'${emp.nik}',nama:'${emp.nama}',jpl:${emp.jpl},target:${emp.target_jpl},kurang:${Math.max(0, emp.target_jpl - emp.jpl)}})"><i class="fas fa-bell"></i> Ingatkan</button>` :
                    `<span class="text-success small fw-bold"><i class="fas fa-check"></i> AMAN</span>`;
                contentHtml += `
                    <tr>
                        <td>
                            <div class="fw-bold text-uppercase text-dark">${emp.nama}</div>
                            <div class="text-muted font-monospace" style="font-size:0.65rem;">${emp.nik}</div>
                        </td>
                        <td class="text-muted small">${emp.profesi}</td>
                        <td class="text-center"><span class="${jplClass}">${emp.jpl}</span> / ${emp.target_jpl}</td>
                        <td class="text-center">${remindBtn}</td>
                    </tr>
                `;
            });
        } else {
            contentHtml += '<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data</td></tr>';
        }
        contentHtml += '</tbody></table></div>';

        Swal.fire({
            title: '<span class="fw-bold fs-5 text-dark"><i class="fas fa-users text-danger me-2"></i>' + title + '</span>',
            html: contentHtml,
            width: '700px',
            confirmButtonColor: '#212529',
            confirmButtonText: 'Tutup',
            customClass: { popup: 'rounded-4' }
        });
    }

    function openHistoryModal(user) {
        currentHistoryNik = user.nik;
        currentHistoryUser = user;
        document.getElementById('histNama').innerText = user.nama.toUpperCase();
        document.getElementById('histNik').innerText = user.nik;
        document.getElementById('histAvatar').innerText = user.nama.charAt(0).toUpperCase();

        let historyModalEl = document.getElementById('modalHistory');
        let modalInstance = bootstrap.Modal.getInstance(historyModalEl);
        if (!modalInstance) modalInstance = new bootstrap.Modal(historyModalEl);
        modalInstance.show();

        fetchHistory();
    }

    function fetchHistory() {
        if (!currentHistoryUser) return;

        let container = document.getElementById('historyListContainer');
        let year = document.getElementById('histYearFilter').value;
        
        container.innerHTML = '';
        
        let filtered = currentHistoryUser.history.filter(item => {
            if (!item.tanggal || item.tanggal === '-') return false;
            return item.tanggal.endsWith(year);
        });

        if (filtered.length === 0) {
            container.innerHTML = `<div class="text-center py-4 text-muted small"><i class="fas fa-folder-open fa-2x mb-2 d-block opacity-25"></i> Tidak ada data riwayat JPL untuk tahun ${year}.</div>`;
            return;
        }

        filtered.forEach(item => {
            let namaParts = item.nama.match(/^\[(.*?)\] (.*)$/);
            let badgeColor = 'bg-primary';
            let jenisTxt = 'Lainnya';
            let judul = item.nama;

            if (namaParts) {
                jenisTxt = namaParts[1];
                judul = namaParts[2];
                if (jenisTxt.includes('Internal')) badgeColor = 'bg-danger';
                if (jenisTxt.includes('Eksternal')) badgeColor = 'bg-primary';
            }

            container.innerHTML += `
                <div class="list-group-item px-3 py-2.5 border-bottom border-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="small fw-bold text-dark text-truncate" style="max-width:70%;" title="${judul}">${judul}</div>
                        <span class="badge bg-dark rounded-pill px-2.5 py-1 fw-bold" style="font-size:0.6rem;">+${item.jpl} JPL</span>
                    </div>
                    <div class="small text-muted mt-1 d-flex justify-content-between align-items-center" style="font-size:0.65rem;">
                        <span>Tgl: ${item.tanggal}</span>
                        <span class="badge ${badgeColor} rounded-pill" style="font-size:0.55rem;">${jenisTxt}</span>
                    </div>
                </div>
            `;
        });
    }

    function openRemindModal(emp) {
        let templateMsg = `Capaian JPL Anda tahun ini baru ${emp.jpl} JPL dari target ${emp.target} JPL, masih kurang ${emp.kurang} JPL. Mohon segera mengikuti program diklat yang tersedia.`;

        Swal.fire({
            title: '<span class="fw-bold fs-5 text-dark"><i class="fas fa-bell text-danger me-2"></i>Pengingat Capaian JPL</span>',
            html: `
                <div class="text-start">
                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3 border">
                        <div class="me-3">
                            <div class="bg-dark text-white p-2 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                <i class="fas fa-user-md text-danger"></i>
                            </div>
                        </div>
                        <div>
                            <div class="fw-bold text-dark text-uppercase">${emp.nama}</div>
                            <div class="text-muted small font-monospace">${emp.nik}</div>
                        </div>
                    </div>
                    <div class="alert alert-dark p-3 rounded-3 mb-3 border">
                        <strong>Capaian JPL Saat Ini:</strong> ${emp.jpl} / ${emp.target} JPL <br>
                        <span class="text-danger fw-bold">(Kekurangan: ${emp.kurang} JPL)</span>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold small text-dark mb-1">Pesan Pengingat:</label>
                        <textarea id="remindMsgIndividual" class="form-control border-dark rounded-3 small" rows="3">${templateMsg}</textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#212529',
            confirmButtonText: 'Kirim Pengingat',
            cancelButtonText: 'Batal',
            width: '500px',
            preConfirm: () => {
                return document.getElementById('remindMsgIndividual').value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let msg = result.value;
                Swal.fire({
                    title: 'Mengirim Pengingat...',
                    html: 'Mohon tunggu, sedang mengirim pengingat.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                fetch('<?= base_url("pelatihan/admin/monitoring/remind_individual") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            nik: emp.nik,
                            message: msg
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terkirim',
                                text: 'Pengingat berhasil dikirim ke pegawai.',
                                confirmButtonColor: '#ce2127'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan.',
                                confirmButtonColor: '#ce2127'
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan sistem: ' + err.message,
                            confirmButtonColor: '#ce2127'
                        });
                    });
            }
        });
    }

    let currentDetailsList = [];

    function openRemindModalFromIndex(index) {
        let emp = currentDetailsList[index];
        if (emp) {
            openRemindModal(emp);
        }
    }

    function showRoomDetails(title, list) {
        currentDetailsList = list;
        let contentHtml = `
            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                <table class="table table-sm table-hover align-middle text-start mb-0" style="font-size:0.78rem;">
                    <thead class="table-light text-muted fw-bold">
                        <tr>
                            <th>NAMA / NIK</th>
                            <th>JPL / TARGET</th>
                            <th class="text-center">KURANG</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        if (list && list.length > 0) {
            list.forEach((emp, index) => {
                let kurangText = emp.kurang > 0 ? `<span class="text-danger fw-bold">${emp.kurang} JPL</span>` : `<span>-</span>`;
                let actionBtn = emp.status !== 'Tercapai' ?
                    `<button class="btn btn-xs btn-outline-danger rounded-pill fw-bold px-2 py-0" style="font-size:0.6rem;" onclick="Swal.close(); openRemindModalFromIndex(${index})"><i class="fas fa-bell"></i></button>` :
                    `<span class="text-dark small fw-bold">&#10003; AMAN</span>`;
                contentHtml += `
                    <tr>
                        <td>
                            <div class="fw-bold text-uppercase text-dark">${emp.nama}</div>
                            <div class="text-muted font-monospace" style="font-size:0.65rem;">${emp.nik}</div>
                        </td>
                        <td class="fw-bold">${emp.jpl} / ${emp.target}</td>
                        <td class="text-center">${kurangText}</td>
                        <td class="text-center">${actionBtn}</td>
                    </tr>
                `;
            });
        } else {
            contentHtml += '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data pegawai</td></tr>';
        }
        contentHtml += '</tbody></table></div>';

        Swal.fire({
            title: title,
            html: contentHtml,
            width: '650px',
            confirmButtonColor: '#212529',
            confirmButtonText: 'Tutup'
        });
    }

    function remindRoom(roomName, employees) {
        let templateMsg = `Pemberitahuan kepada seluruh pegawai di Ruangan ${roomName}. Harap perhatikan capaian JPL tahunan Anda.`;
        Swal.fire({
            title: '<span class="fw-bold fs-5 text-dark"><i class="fas fa-bell text-danger me-2"></i>Kirim Pengingat Ruangan</span>',
            html: `
                <div class="text-start">
                    <p class="small text-muted">Kirim pengingat ke seluruh nakes di <b>Ruangan ${roomName}</b>.</p>
                    <div class="alert alert-dark p-3 rounded-3 mb-3 border">
                        <strong>Penerima:</strong> ${employees.length} Pegawai Ruangan
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold small text-dark mb-1">Pesan Broadcast Ruangan:</label>
                        <textarea id="remindMsgRoom" class="form-control border-dark rounded-3 small" rows="3">${templateMsg}</textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#212529',
            confirmButtonText: 'Kirim Pengingat',
            cancelButtonText: 'Batal',
            width: '500px',
            preConfirm: () => {
                return document.getElementById('remindMsgRoom').value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let msg = result.value;
                Swal.fire({
                    title: 'Mengirim Pengingat Ruangan...',
                    html: 'Mohon tunggu, sedang mengirim pengingat ke seluruh pegawai ruangan.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                let niksArray = employees.map(emp => emp.nik);
                
                fetch('<?= base_url("pelatihan/admin/monitoring/broadcast_room") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        niks: niksArray,
                        message: msg
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terkirim',
                            text: 'Pengingat berhasil dikirim ke seluruh pegawai di ruangan ini.',
                            confirmButtonColor: '#ce2127'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message || 'Terjadi kesalahan saat mengirim pengingat.',
                            confirmButtonColor: '#ce2127'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan sistem: ' + err.message,
                        confirmButtonColor: '#ce2127'
                    });
                });
            }
        });
    }

    function showProfesiPeserta(id, name) {
        let yr = document.getElementById('selectTahun').value;
        fetch('<?= base_url("pelatihan/admin/monitoring/peserta_by_profesi") ?>/' + id + '?tahun=' + yr)
            .then(res => res.json())
            .then(list => {
                currentDetailsList = list;
                let contentHtml = `
                <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle text-start mb-0" style="font-size:0.78rem;">
                        <thead class="table-light text-muted fw-bold">
                            <tr>
                                <th>NAMA / NIK</th>
                                <th>JPL / TARGET</th>
                                <th class="text-center">KURANG</th>
                                <th class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
                if (list && list.length > 0) {
                    list.forEach((emp, index) => {
                        let kurangText = emp.kurang > 0 ? `<span class="text-danger fw-bold">${emp.kurang} JPL</span>` : `<span>-</span>`;
                        let actionBtn = emp.status !== 'Tercapai' ?
                            `<button class="btn btn-xs btn-outline-danger rounded-pill fw-bold px-2 py-0" style="font-size:0.6rem;" onclick="Swal.close(); openRemindModalFromIndex(${index})"><i class="fas fa-bell"></i></button>` :
                            `<span class="text-dark small fw-bold">&#10003; AMAN</span>`;
                        contentHtml += `
                        <tr>
                            <td>
                                <div class="fw-bold text-uppercase text-dark">${emp.nama}</div>
                                <div class="text-muted font-monospace" style="font-size:0.65rem;">${emp.nik}</div>
                            </td>
                            <td class="fw-bold">${emp.jpl} / ${emp.target}</td>
                            <td class="text-center">${kurangText}</td>
                            <td class="text-center">${actionBtn}</td>
                        </tr>
                    `;
                    });
                } else {
                    contentHtml += '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data pegawai</td></tr>';
                }
                contentHtml += '</tbody></table></div>';

                Swal.fire({
                    title: 'Profesi: ' + name,
                    html: contentHtml,
                    width: '650px',
                    confirmButtonColor: '#212529',
                    confirmButtonText: 'Tutup'
                });
            });
    }
</script>

<?= $this->endSection() ?>
