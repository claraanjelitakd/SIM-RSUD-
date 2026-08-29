<?= $this->include('Pendidikan/AdminDiklat/layout/header') ?>
<?= $this->include('Pendidikan/AdminDiklat/layout/sidebar') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-dark">Manajemen Institusi</h5>
    <div>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn <?= ($tab === 'inbox') ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="location.href='<?= base_url('pendidikan/admin/diklat/institusi?tab=inbox') ?>'">
                Inbox <span class="badge bg-light text-dark ms-1"><?= $counts['inbox'] ?? 0 ?></span>
            </button>
            <button type="button" class="btn <?= ($tab === 'approved') ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="location.href='<?= base_url('pendidikan/admin/diklat/institusi?tab=approved') ?>'">
                Disetujui <span class="badge bg-light text-dark ms-1"><?= $counts['approved'] ?? 0 ?></span>
            </button>
            <button type="button" class="btn <?= ($tab === 'revision') ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="location.href='<?= base_url('pendidikan/admin/diklat/institusi?tab=revision') ?>'">
                Revisi <span class="badge bg-light text-dark ms-1"><?= $counts['revision'] ?? 0 ?></span>
            </button>
            <button type="button" class="btn <?= ($tab === 'declined') ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="location.href='<?= base_url('pendidikan/admin/diklat/institusi?tab=declined') ?>'">
                Ditolak <span class="badge bg-light text-dark ms-1"><?= $counts['declined'] ?? 0 ?></span>
            </button>
        </div>
    </div>
</div>

<?php if ($viewMode === 'detail' && isset($detail)): ?>
<div class="mb-3">
    <a href="<?= base_url('pendidikan/admin/diklat/institusi') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-4 text-center">
            <div class="bg-light rounded mx-auto d-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
                <span class="fw-bold text-muted" style="font-size:2rem;"><?= strtoupper(substr($detail['nama_institusi'] ?? '-', 0, 1)) ?></span>
            </div>
            <h5 class="fw-bold"><?= $detail['nama_institusi'] ?? '-' ?></h5>
            <span class="badge bg-primary mb-3">Institusi</span>
            <?php $status = $detail['status_verifikasi'] ?? 'pending'; ?>
            <?php if ($status === 'approved'): ?>
                <span class="badge badge-disetujui">Disetujui</span>
            <?php elseif ($status === 'rejected'): ?>
                <span class="badge badge-ditolak">Ditolak</span>
            <?php elseif ($status === 'revision'): ?>
                <span class="badge badge-revisi">Revisi</span>
            <?php else: ?>
                <span class="badge badge-menunggu">Menunggu</span>
            <?php endif; ?>

            <hr class="my-3">
            <div class="text-start small">
                <p><i class="fas fa-envelope text-muted me-2"></i> <?= $detail['email'] ?? '-' ?></p>
                <p><i class="fas fa-phone text-muted me-2"></i> <?= $detail['no_telp'] ?? '-' ?></p>
                <p><i class="fas fa-map-marker-alt text-muted me-2"></i> <?= $detail['alamat'] ?? '-' ?></p>
            </div>

            <?php if ($status === 'pending'): ?>
            <div class="d-grid gap-2 mt-3">
                <button class="btn btn-primary" onclick="approveInstitusi(<?= $detail['id'] ?>)">
                    <i class="fas fa-check me-1"></i> Setujui
                </button>
                <button class="btn btn-outline-warning" onclick="showRevisionModal(<?= $detail['id'] ?>)">
                    <i class="fas fa-edit me-1"></i> Minta Revisi
                </button>
                <button class="btn btn-outline-danger" onclick="showDeclineModal(<?= $detail['id'] ?>)">
                    <i class="fas fa-times me-1"></i> Tolak
                </button>
            </div>
            <?php elseif ($status === 'revision'): ?>
            <div class="d-grid mt-3">
                <button class="btn btn-outline-primary" onclick="resubmitInstitusi(<?= $detail['id'] ?>)">
                    <i class="fas fa-undo me-1"></i> Tandai Sudah Direvisi
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-4">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link <?= ($subTab === 'documents' || !$subTab) ? 'active' : '' ?>" href="<?= base_url('pendidikan/admin/diklat/institusi/detail/' . $detail['id']) ?>">Dokumen</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($subTab === 'students') ? 'active' : '' ?>" href="<?= base_url('pendidikan/admin/diklat/institusi/detail/' . $detail['id'] . '?tab=students') ?>">Mahasiswa</a>
                </li>
            </ul>

            <?php if ($subTab === 'students'): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($mahasiswaList)): ?>
                            <?php foreach ($mahasiswaList as $m): ?>
                            <tr>
                                <td class="fw-semibold"><?= $m['nama_lengkap'] ?? '-' ?></td>
                                <td><?= $m['nim'] ?? '-' ?></td>
                                <td><?= $m['program_studi'] ?? '-' ?></td>
                                <td>
                                    <?php $mStatus = $m['status'] ?? 'Menunggu'; ?>
                                    <?php if (in_array($mStatus, ['Disetujui', 'Aktif', '1'])): ?>
                                        <span class="badge badge-disetujui">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-menunggu">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada mahasiswa</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="row g-3">
                <?php if (!empty($dokumenList)): ?>
                    <?php foreach ($dokumenList as $d): ?>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <i class="fas fa-file-alt fa-2x text-primary"></i>
                                <div>
                                    <small class="fw-semibold d-block"><?= $d['judul'] ?? '-' ?></small>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($d['created_at'] ?? 'now')) ?></small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <?php if ($d['nama_file']): ?>
                                <?php 
                                if (isset($d['is_pengajuan']) && $d['is_pengajuan']) {
                                    $fileUrl = base_url('uploads/dokumen_pengajuan/' . $d['nama_file']);
                                    $dlUrl = $fileUrl;
                                } else {
                                    $fileUrl = $d['id'] ? base_url('pendidikan/admin/diklat/api/dokumen/view/' . $d['id']) : base_url('pendidikan/admin/diklat/api/institusi/file/' . $detail['id'] . '/' . ($d['judul'] === 'MOU / Perjanjian Kerja Sama' ? 'mou' : 'permohonan'));
                                    $dlUrl = $d['id'] ? base_url('pendidikan/admin/diklat/api/dokumen/download/' . $d['id']) : $fileUrl . '?download=1'; 
                                }
                                ?>
                                <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> Lihat
                                </a>
                                <a href="<?= $dlUrl ?>" class="btn btn-sm btn-outline-secondary" <?php if (isset($d['is_pengajuan']) && $d['is_pengajuan']) echo 'download'; ?>>
                                    <i class="fas fa-download"></i>
                                </a>
                                <?php else: ?>
                                <span class="text-muted small">File tidak tersedia</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted py-4">Belum ada dokumen</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php else: ?>
<div class="card p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Institusi</th>
                    <th>Tanggal Pengajuan</th>
                    <th>PIC</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($institusiList)): ?>
                    <?php foreach ($institusiList as $inst): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                    <span class="fw-bold text-muted"><?= strtoupper(substr($inst['nama_institusi'] ?? '-', 0, 1)) ?></span>
                                </div>
                                <div>
                                    <span class="fw-semibold"><?= $inst['nama_institusi'] ?? '-' ?></span>
                                    <br><small class="text-muted"><?= $inst['email'] ?? '' ?></small>
                                </div>
                            </div>
                        </td>
                        <td><small class="text-muted"><?= date('d/m/Y', strtotime($inst['created_at'] ?? 'now')) ?></small></td>
                        <td>
                            <small class="fw-semibold"><?= $inst['nama_kontak'] ?? '-' ?></small>
                            <br><small class="text-muted"><?= $inst['no_telp'] ?? '' ?></small>
                        </td>
                        <td>
                            <?php $status = $inst['status_verifikasi'] ?? 'pending'; ?>
                            <?php if ($status === 'approved'): ?>
                                <span class="badge badge-disetujui">Disetujui</span>
                            <?php elseif ($status === 'rejected'): ?>
                                <span class="badge badge-ditolak">Ditolak</span>
                            <?php elseif ($status === 'revision'): ?>
                                <span class="badge badge-revisi">Revisi</span>
                            <?php else: ?>
                                <span class="badge badge-menunggu">Menunggu</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?= base_url('pendidikan/admin/diklat/institusi/detail/' . $inst['id']) ?>" class="btn btn-sm btn-outline-primary">
                                Detail <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            Tidak ada data untuk filter ini
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Revision Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Minta Revisi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="revisionForm" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Revisi</label>
                        <textarea name="catatan_revisi" class="form-control" rows="4" placeholder="Jelaskan apa yang perlu diperbaiki..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tolak Pendaftaran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="declineForm" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Alasan Penolakan</label>
                        <textarea name="alasan_penolakan" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRevisionModal(id) {
    $('#revisionForm').attr('action', '<?= base_url('pendidikan/admin/diklat/api/institusi/revision') ?>/' + id);
    $('#revisionModal').modal('show');
}
function showDeclineModal(id) {
    $('#declineForm').attr('action', '<?= base_url('pendidikan/admin/diklat/api/institusi/decline') ?>/' + id);
    $('#declineModal').modal('show');
}
function approveInstitusi(id) {
    if (!confirm('Setujui institusi ini?')) return;
    $.post('<?= base_url('pendidikan/admin/diklat/api/institusi/approve') ?>/' + id, function(res) {
        if (res.success) {
            location.reload();
        } else {
            alert(res.message || 'Gagal');
        }
    });
}
function resubmitInstitusi(id) {
    if (!confirm('Tandai sudah direvisi?')) return;
    $.post('<?= base_url('pendidikan/admin/diklat/api/institusi/resubmit') ?>/' + id, function(res) {
        if (res.success) {
            location.reload();
        } else {
            alert(res.message || 'Gagal');
        }
    });
}

$('#revisionForm').submit(function(e) {
    e.preventDefault();
    var action = $(this).attr('action');
    var data = $(this).serialize();
    $.post(action, data, function(res) {
        if (res.success) {
            location.reload();
        } else {
            alert(res.message || 'Gagal');
        }
    }).fail(function(xhr) {
        alert('Gagal: ' + (xhr.responseJSON?.message || 'Server error'));
    });
});

$('#declineForm').submit(function(e) {
    e.preventDefault();
    var action = $(this).attr('action');
    var data = $(this).serialize();
    $.post(action, data, function(res) {
        if (res.success) {
            location.reload();
        } else {
            alert(res.message || 'Gagal');
        }
    }).fail(function(xhr) {
        alert('Gagal: ' + (xhr.responseJSON?.message || 'Server error'));
    });
});
</script>

<?= $this->include('Pendidikan/AdminDiklat/layout/footer') ?>
