<?= $this->include('Pendidikan/AdminDiklat/layout/header') ?>
<?= $this->include('Pendidikan/AdminDiklat/layout/sidebar') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-dark">Manajemen Pengajuan</h5>
    <div>
        <span class="badge bg-primary"><?= $totalPengajuan ?? 0 ?> pengajuan</span>
    </div>
</div>

<?php if ($viewMode === 'detail' && isset($detail)): ?>
<div class="mb-3">
    <button class="btn btn-sm btn-outline-secondary" onclick="location.href='<?= base_url('pendidikan/admin/diklat/pengajuan') ?>'">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </button>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-file-alt text-primary fa-lg"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0"><?= $detail['pengajuan']['nama_program'] ?? '-' ?></h6>
                    <small class="text-muted"><?= $detail['pengajuan']['nama_institusi'] ?? '' ?></small>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between mb-2">
                <small class="text-muted">Status</small>
                <?php $status = $detail['pengajuan']['status'] ?? 'Menunggu'; ?>
                <?php if ($status === 'Disetujui' || $status === 'Selesai'): ?>
                    <span class="badge badge-disetujui"><?= $status ?></span>
                <?php elseif ($status === 'Ditolak'): ?>
                    <span class="badge badge-ditolak"><?= $status ?></span>
                <?php elseif ($status === 'Revisi'): ?>
                    <span class="badge badge-revisi"><?= $status ?></span>
                <?php else: ?>
                    <span class="badge badge-menunggu"><?= $status ?></span>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <small class="text-muted">Tanggal Mulai</small>
                <small class="fw-semibold"><?= date('d/m/Y', strtotime($detail['pengajuan']['tanggal_mulai'] ?? 'now')) ?></small>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <small class="text-muted">Tanggal Selesai</small>
                <small class="fw-semibold"><?= date('d/m/Y', strtotime($detail['pengajuan']['tanggal_selesai'] ?? 'now')) ?></small>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <small class="text-muted">Jumlah Peserta</small>
                <small class="fw-semibold"><?= $detail['pengajuan']['jumlah_peserta'] ?? count($detail['all_mahasiswa'] ?? []) ?> orang</small>
            </div>

            <?php if (!empty($detail['pengajuan']['catatan_admin'])): ?>
            <hr>
            <div>
                <small class="text-muted d-block mb-1">Catatan Admin:</small>
                <div class="bg-light rounded p-2">
                    <small><?= $detail['pengajuan']['catatan_admin'] ?></small>
                </div>
            </div>
            <?php endif; ?>

            <?php if (in_array($status, ['Menunggu', 'Revisi'])): ?>
            <hr>
            <div class="d-grid gap-2">
                <?php if ($status === 'Revisi'): ?>
                <button class="btn btn-success btn-sm" disabled style="opacity: 0.5; cursor: not-allowed;">
                    <i class="fas fa-check me-1"></i> Setujui
                </button>
                <small class="text-muted text-center mb-1"><i class="fas fa-info-circle me-1"></i> Menunggu revisi dari institusi</small>
                <?php else: ?>
                <button class="btn btn-success btn-sm" onclick="approvePengajuan(<?= $detail['pengajuan']['id'] ?>)">
                    <i class="fas fa-check me-1"></i> Setujui
                </button>
                <?php endif; ?>
                <button class="btn btn-warning btn-sm" onclick="$('#revisionPengajuanModal').modal('show')">
                    <i class="fas fa-edit me-1"></i> Minta Revisi
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-4 mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-file-alt text-primary me-2"></i>Dokumen Pengajuan</h6>
            <div class="row g-3">
                <?php 
                $docFields = [
                    'file_proposal' => 'Proposal',
                    'file_surat_pengantar' => 'Surat Pengantar',
                    'file_logbook' => 'Logbook',
                    'file_panduan' => 'Panduan',
                    'file_daftar_mhs' => 'Daftar Mahasiswa',
                    'file_kompetensi' => 'Kompetensi',
                    'file_sk_pembimbing' => 'SK Pembimbing',
                    'file_bukti_bayar' => 'Bukti Bayar'
                ];
                $hasDocs = false;
                foreach ($docFields as $field => $label) {
                    if (!empty($detail['pengajuan'][$field])) {
                        $hasDocs = true;
                        break;
                    }
                }
                ?>
                <?php if ($hasDocs): ?>
                    <?php foreach ($docFields as $field => $label): 
                        $filename = $detail['pengajuan'][$field] ?? null;
                        if ($filename):
                    ?>
                    <div class="col-md-6">
                        <div class="border rounded p-3">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                <div>
                                    <small class="fw-semibold d-block"><?= $label ?></small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('uploads/dokumen_pengajuan/' . $filename) ?>" target="_blank" class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> Lihat
                                </a>
                                <a href="<?= base_url('uploads/dokumen_pengajuan/' . $filename) ?>" download class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted py-3">Belum ada dokumen</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-users text-primary me-2"></i>Daftar Mahasiswa</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Jenis Kelamin</th>
                            <th>Program Studi</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $mahasiswaData = $detail['all_mahasiswa'] ?? $detail['mahasiswa'] ?? []; ?>
                        <?php if (!empty($mahasiswaData)): ?>
                            <?php foreach ($mahasiswaData as $m): ?>
                            <tr>
                                <td class="fw-semibold"><?= $m['nama_lengkap'] ?? '-' ?></td>
                                <td><?= $m['nim'] ?? '-' ?></td>
                                <td><?= $m['jenis_kelamin'] ?? '-' ?></td>
                                <td><?= $m['program_studi'] ?? '-' ?></td>
                                <td class="text-center">
                                    <?php $mStatus = $m['status'] ?? 'Menunggu'; ?>
                                    <?php if (in_array($mStatus, ['Disetujui', 'Aktif', '1'])): ?>
                                        <span class="badge badge-disetujui">Aktif</span>
                                    <?php elseif ($mStatus === 'Ditolak'): ?>
                                        <span class="badge badge-ditolak">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge badge-menunggu">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada data mahasiswa
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Revision Modal -->
<div class="modal fade" id="revisionPengajuanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Minta Revisi Pengajuan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="revisionPengajuanForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catatan Revisi</label>
                        <textarea name="catatan_admin" class="form-control" rows="4" placeholder="Jelaskan apa yang perlu diperbaiki..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Kirim Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php else: ?>
<!-- List View -->
<div class="card p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Institusi</th>
                    <th>Program</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th class="text-center">Peserta</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pengajuanList)): ?>
                    <?php foreach ($pengajuanList as $p): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                    <i class="fas fa-building text-primary"></i>
                                </div>
                                <span class="fw-semibold"><?= $p['nama_institusi'] ?? '-' ?></span>
                            </div>
                        </td>
                        <td><span><?= $p['nama_program'] ?? '-' ?></span></td>
                        <td><small class="text-muted"><?= date('d/m/Y', strtotime($p['tanggal_mulai'] ?? 'now')) ?></small></td>
                        <td><small class="text-muted"><?= date('d/m/Y', strtotime($p['tanggal_selesai'] ?? 'now')) ?></small></td>
                        <td class="text-center fw-semibold"><?= $p['jumlah_peserta'] ?? $p['jumlah_mahasiswa'] ?? 0 ?></td>
                        <td class="text-center">
                            <?php $status = $p['status'] ?? 'Menunggu'; ?>
                            <?php if ($status === 'Disetujui' || $status === 'Selesai'): ?>
                                <span class="badge badge-disetujui"><?= $status ?></span>
                            <?php elseif ($status === 'Ditolak'): ?>
                                <span class="badge badge-ditolak"><?= $status ?></span>
                            <?php elseif ($status === 'Revisi'): ?>
                                <span class="badge badge-revisi"><?= $status ?></span>
                            <?php else: ?>
                                <span class="badge badge-menunggu"><?= $status ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('pendidikan/admin/diklat/pengajuan/detail/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            Tidak ada pengajuan baru
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<script>
function approvePengajuan(id) {
    if (!confirm('Setujui pengajuan ini?')) return;
    $.post('<?= base_url('pendidikan/admin/diklat/api/pengajuan/approve') ?>/' + id, function(res) {
        if (res.success) {
            location.reload();
        } else {
            alert(res.message || 'Gagal approve');
        }
    });
}

$('#revisionPengajuanForm').submit(function(e) {
    e.preventDefault();
    var data = $(this).serialize();
    var id = <?= isset($detail['pengajuan']['id']) ? $detail['pengajuan']['id'] : 'null' ?>;
    if (!id) return;
    $.post('<?= base_url('pendidikan/admin/diklat/api/pengajuan/revision') ?>/' + id, data, function(res) {
        if (res.success) {
            location.reload();
        } else {
            alert(res.message || 'Gagal');
        }
    });
});
</script>

<?= $this->include('Pendidikan/AdminDiklat/layout/footer') ?>
