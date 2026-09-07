<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js">

<style>
    .table-detail th { font-size: 0.75rem; text-transform: uppercase; color: #6c757d; }
    .table-detail td { font-size: 0.82rem; vertical-align: middle; }
    .badge-jpl { font-size: 0.7rem; }
</style>

<div class="container-fluid px-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="<?= site_url('pelatihan/admin/monitoring') ?>" class="btn btn-outline-dark rounded-pill fw-bold px-3 mb-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Monitoring
            </a>
            <h4 class="fw-bold text-dark mb-0">
                <?php
                $iconMap = [
                    'all' => 'fa-users text-primary',
                    'kurang' => 'fa-exclamation-triangle text-danger',
                    'cukup' => 'fa-check-circle text-success',
                    'tidak_aktif' => 'fa-user-slash text-secondary'
                ];
                $colorMap = [
                    'all' => 'primary',
                    'kurang' => 'danger',
                    'cukup' => 'success',
                    'tidak_aktif' => 'secondary'
                ];
                ?>
                <i class="fas <?= $iconMap[$type] ?? 'fa-list' ?> me-2"></i> <?= esc($title) ?>
            </h4>
            <span class="text-muted small">Tahun Evaluasi: <strong class="text-danger"><?= esc($selectedYear) ?></strong> &bull; Total: <strong><?= $totalCount ?> orang</strong></span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-detail" id="detailTable" style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:40px;">No</th>
                            <th>Nama / NIK</th>
                            <th>Profesi</th>
                            <th>Divisi / Ruangan</th>
                            <th class="text-center">JPL / Target</th>
                            <th class="text-center">Persentase</th>
                            <th class="text-center">Status</th>
                            <th class="text-center pe-3" style="width:160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($stats)): ?>
                            <?php foreach ($stats as $i => $s): ?>
                                <?php
                                    $pct = $s['target_jpl'] > 0 ? round(($s['jpl'] / $s['target_jpl']) * 100, 1) : 0;
                                    $pct = min($pct, 100);
                                    $barColor = $s['jpl'] >= $s['target_jpl'] ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger');
                                    $statusBadge = $s['jpl'] >= $s['target_jpl']
                                        ? '<span class="badge bg-success rounded-pill badge-jpl"><i class="fas fa-check me-1"></i>Tercapai</span>'
                                        : '<span class="badge bg-danger rounded-pill badge-jpl"><i class="fas fa-times me-1"></i>Belum</span>';
                                    $isInactive = $s['pelatihan'] === 'Belum Ada';
                                ?>
                                <tr>
                                    <td class="ps-3 text-muted"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-bold text-dark text-uppercase"><?= esc($s['nama']) ?></div>
                                        <div class="text-muted font-monospace" style="font-size:0.7rem;"><?= esc($s['nik']) ?></div>
                                    </td>
                                    <td class="text-muted"><?= esc($s['profesi']) ?></td>
                                    <td class="text-muted"><?= esc($s['divisi']) ?></td>
                                    <td class="text-center">
                                        <span class="fw-bold <?= $s['jpl'] < $s['target_jpl'] ? 'text-danger' : 'text-dark' ?>"><?= $s['jpl'] ?></span>
                                        <span class="text-muted">/ <?= $s['target_jpl'] ?></span>
                                    </td>
                                    <td class="text-center" style="min-width:120px;">
                                        <div class="progress" style="height:6px; border-radius:10px;">
                                            <div class="progress-bar <?= $barColor ?>" style="width: <?= $pct ?>%; border-radius:10px;"></div>
                                        </div>
                                        <small class="text-muted"><?= $pct ?>%</small>
                                    </td>
                                    <td class="text-center"><?= $statusBadge ?></td>
                                    <td class="text-center pe-3">
                                        <button class="btn btn-xs btn-outline-primary rounded-pill fw-bold px-2 py-0" style="font-size:0.65rem;"
                                            onclick='openHistoryModal(<?= json_encode(["nik"=>$s["nik"],"nama"=>$s["nama"]]) ?>)'>
                                            <i class="fas fa-history"></i> Riwayat
                                        </button>
                                        <?php if ($s['jpl'] < $s['target_jpl'] && !$isInactive): ?>
                                            <button class="btn btn-xs btn-outline-warning rounded-pill fw-bold px-2 py-0" style="font-size:0.65rem;"
                                                onclick='openRemindModal(<?= json_encode(["nik"=>$s["nik"],"nama"=>$s["nama"],"jpl"=>$s["jpl"],"target"=>$s["target_jpl"],"kurang"=>max(0,$s["target_jpl"]-$s["jpl"])]) ?>)'>
                                                <i class="fas fa-bell"></i> Ingatkan
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted py-5">Tidak ada data untuk kategori ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Riwayat JPL -->
<div class="modal fade" id="modalHistory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h6 class="fw-bold text-dark mb-0">Riwayat JPL Karyawan</h6>
                    <small class="text-muted" id="histInfo"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="historyContent" class="text-center text-muted py-4">Memuat data...</div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#detailTable').DataTable({
        order: [[0, 'asc']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            emptyTable: "Tidak ada data",
            paginate: { first: "Pertama", last: "Terakhir", next: "Selanjutnya", previous: "Sebelumnya" }
        },
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100]
    });
});

function openHistoryModal(user) {
    document.getElementById('histInfo').innerText = user.nama.toUpperCase() + ' — ' + user.nik;
    let modal = new bootstrap.Modal(document.getElementById('modalHistory'));
    modal.show();

    document.getElementById('historyContent').innerHTML = '<div class="spinner-border text-primary" role="status"></div>';

    fetch('<?= site_url("pelatihan/admin/monitoring/jpl_history/" ) ?>' + user.nik + '?tahun=<?= $selectedYear ?>')
        .then(r => r.json())
        .then(data => {
            if (data.length === 0) {
                document.getElementById('historyContent').innerHTML = '<p class="text-muted">Belum ada riwayat pelatihan.</p>';
                return;
            }
            let html = '<div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0" style="font-size:0.8rem;">';
            html += '<thead class="table-light"><tr><th>Pelatihan</th><th class="text-center">JPL</th><th>Tanggal</th></tr></thead><tbody>';
            data.forEach(h => {
                html += '<tr><td class="fw-bold text-dark">' + h.nama + '</td><td class="text-center"><span class="badge bg-primary rounded-pill">' + h.jpl + ' JPL</span></td><td class="text-muted">' + h.tanggal + '</td></tr>';
            });
            html += '</tbody></table></div>';
            document.getElementById('historyContent').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('historyContent').innerHTML = '<p class="text-danger">Gagal memuat data riwayat.</p>';
        });
}

function openRemindModal(user) {
    let kurang = Math.max(0, user.target - user.jpl);
    let msg = 'Yth. Bapak/Ibu ' + user.nama + ',\n\nAnda masih kekurangan ' + kurang + ' JPL (Target: ' + user.target + ' JPL, Capaian: ' + user.jpl + ' JPL). Mohon segera melengkapi pelatihan yang dibutuhkan. Terima kasih.';

    Swal.fire({
        title: '<span class="fw-bold fs-6">Kirim Pengingat</span>',
        html: '<div class="text-start"><p class="small text-muted mb-2">Kepada: <strong>' + user.nama + '</strong> (' + user.nik + ')</p><p class="small text-muted mb-2">Kekurangan: <strong class="text-danger">' + kurang + ' JPL</strong></p><textarea id="swalMsg" class="form-control" rows="4" style="font-size:0.8rem;">' + msg + '</textarea></div>',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Kirim',
        confirmButtonColor: '#212529',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' },
        preConfirm: () => {
            return document.getElementById('swalMsg').value;
        }
    }).then(result => {
        if (result.isConfirmed) {
            fetch('<?= site_url("pelatihan/admin/monitoring/remind_individual") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ nik: user.nik, message: result.value })
            }).then(r => r.json()).then(resp => {
                if (resp.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Terkirim!', text: 'Pengingat berhasil dikirim.', timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: resp.message || 'Gagal mengirim pengingat.' });
                }
            }).catch(() => {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan jaringan.' });
            });
        }
    });
}
</script>

<?= $this->endSection() ?>
