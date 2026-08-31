<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<?php 
$role = session()->get('role'); 
$pelatihan = $pelatihan ?? [];
$pejabat = $pejabat ?? [];
$sertifikat = $sertifikat ?? [];
$templates = $templates ?? [];
?>

<div class="row g-3 align-items-center mb-4">
    <div class="col-12">
        <div class="bg-white p-3 rounded-lg shadow-sm border-start border-danger border-5 h-100 d-flex align-items-center">
            <div class="text-muted small mb-0"><i class="fas fa-info-circle me-1 text-danger"></i> Kelola sertifikat resmi RSUD, verifikasi kegiatan mandiri/external, surat tugas, pengabdian nakes, dan digital signatures.</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-lg overflow-hidden bg-white mb-5">
    <div class="card-header bg-white p-0 border-0">
        <ul class="nav nav-tabs nav-fill border-0" id="certTab" role="tablist">
            <?php if ($role === 'admin'): ?>
                <li class="nav-item">
                    <button class="nav-link active py-3 fw-bold small text-uppercase" id="issued-tab" data-bs-toggle="tab" data-bs-target="#tab-rsud" type="button">
                        <i class="fas fa-award me-2"></i> DITERBITKAN RSUD
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-bold small text-uppercase" id="mandiri-tab" data-bs-toggle="tab" data-bs-target="#tab-mandiri" type="button">
                        <i class="fas fa-user-edit me-2"></i> MANDIRI / EXTERNAL
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-bold small text-uppercase" id="stugas-tab" data-bs-toggle="tab" data-bs-target="#tab-st" type="button">
                        <i class="fas fa-file-signature me-2"></i> SURAT TUGAS
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-bold small text-uppercase" id="pejabat-tab" data-bs-toggle="tab" data-bs-target="#tab-pejabat" type="button">
                        <i class="fas fa-signature me-2"></i> PEJABAT TTD
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-bold small text-uppercase" id="templates-tab" data-bs-toggle="tab" data-bs-target="#tab-templates" type="button">
                        <i class="fas fa-cogs me-2"></i> TEMPLATE SERTIFIKAT
                    </button>
                </li>
            <?php endif; ?>

            <?php if ($role === 'admin_pengabdian'): ?>
                <li class="nav-item">
                    <button class="nav-link <?= $role === 'admin_pengabdian' ? 'active' : '' ?> py-3 fw-bold small text-uppercase" id="pengabdian-tab" data-bs-toggle="tab" data-bs-target="#tab-pengabdian" type="button">
                        <i class="fas fa-users me-2"></i> PENGABDIAN
                    </button>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    
    <div class="tab-content" id="certTabContent">
        <?php if ($role === 'admin'): ?>
            <div class="tab-pane fade show active p-4" id="tab-rsud" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white text-muted small">
                            <tr>
                                <th class="ps-4">NAMA PELATIHAN</th>
                                <th>RANAH & KATEGORI</th>
                                <th class="text-center">BEBAN BELAJAR (JPL)</th>
                                <th class="text-center">STATUS</th>
                                <th class="pe-4 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pelatihan as $p) : ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold small text-dark"><?= esc($p['nama']) ?></div>
                                    <div class="text-muted" style="font-size: 0.65rem;">ID: #<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?> | <?= esc($p['metode']) ?></div>
                                </td>
                                <td>
                                    <div class="badge bg-dark text-white small mb-1 text-uppercase" style="font-size: 0.55rem; background-color: #ce2127 !important;"><?= esc($p['ranah_skp'] ?? 'PEMBELAJARAN') ?></div>
                                    <div class="small fw-bold text-muted" style="font-size: 0.65rem;"><?= esc($p['kategori_kegiatan'] ?? '-') ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="small fw-bold text-danger" style="color: #ce2127 !important;"><?= esc($p['jpl']) ?> JPL</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= ($p['cert_published'] ?? false) ? 'bg-success' : 'bg-secondary' ?> rounded-pill px-3 py-1.5 fw-bold text-white small">
                                        <?= ($p['cert_published'] ?? false) ? 'DITERBITKAN' : 'DRAFT' ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm border-0 text-nowrap" onclick="showIssuedParticipants(<?= $p['id'] ?>, '<?= esc($p['nama'], 'js') ?>')">
                                            <i class="fas fa-list me-1"></i> LIHAT DAFTAR
                                        </button>
                                        <?php if ($p['cert_published'] ?? false): ?>
                                            <a href="javascript:void(0)" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm border-0 text-nowrap" style="background-color: #212529;" onclick="confirmUnpublish(<?= $p['id'] ?>, '<?= esc($p['nama'], 'js') ?>')"
                                               title="Batalkan penerbitan sertifikat">
                                                <i class="fas fa-times-circle me-1 text-danger"></i> BATALKAN
                                            </a>
                                        <?php else: ?>
                                            <?php
                                                $hasTemplate = false;
                                                $isValidTemplate = false;
                                                foreach ($templates as $t) { 
                                                    if ($t['pelatihan_id'] == $p['id']) { 
                                                        $hasTemplate = true; 
                                                        if (!empty($t['no_sertifikat']) && !empty($t['pejabat_id_1']) && ($t['status'] ?? 'draft') === 'diterbitkan') {
                                                            $isValidTemplate = true;
                                                        }
                                                        break; 
                                                    } 
                                                }
                                            ?>
                                            <?php if ($hasTemplate && $isValidTemplate): ?>
                                            <a href="javascript:void(0)" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm border-0 text-nowrap" style="background-color: #ce2127;" onclick="confirmPublish(<?= $p['id'] ?>, '<?= esc($p['nama'], 'js') ?>')"
                                               title="Terbitkan sertifikat">
                                                <i class="fas fa-paper-plane me-1"></i> TERBITKAN
                                            </a>
                                            <?php else: ?>
                                            <button class="btn btn-secondary btn-sm rounded-pill px-3 fw-bold shadow-sm text-nowrap" disabled title="Sebelum diterbitkan, lengkapi dulu No Sertifikat & Pejabat Utama Penandatangan di tab Template Sertifikat.">
                                                <i class="fas fa-lock me-1"></i> BELUM LENGKAP
                                            </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php 
                $subTabs = [
                    'tab-mandiri' => ['type' => 'mandiri', 'label' => 'Mandiri / External'],
                    'tab-st' => ['type' => 'surat_tugas', 'label' => 'Surat Tugas']
                ];
                foreach ($subTabs as $tabId => $conf):
            ?>
            <div class="tab-pane fade p-4" id="<?= $tabId ?>" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white text-muted small fw-bold">
                            <tr>
                                <th class="ps-4">NAMA / NIK</th>
                                <th>JUDUL KEGIATAN</th>
                                <th class="text-center">JPL</th>
                                <th class="text-center">BERKAS</th>
                                <th class="text-center">STATUS</th>
                                <th class="pe-4 text-center" style="width:250px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $filtered = array_filter($sertifikat, function($s) use ($conf) {
                                $type = strtolower(str_replace(' ', '_', trim($s['jenis_dokumen'] ?? '')));
                                return $type === $conf['type'];
                            });
                            if (empty($filtered)):
                            ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted fw-bold">Belum ada unggahan <?= esc($conf['label']) ?>.</td></tr>
                            <?php else: foreach($filtered as $s): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold small text-dark"><?= esc($s['user_nama']) ?></div>
                                    <div class="text-muted small font-monospace" style="font-size: 0.65rem;"><?= esc($s['user_id']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold small text-dark"><?= esc($s['judul']) ?></div>
                                    <div class="text-muted" style="font-size: 0.65rem;">Penerbit: <?= esc($s['penerbit'] ?? '-') ?></div>
                                </td>
                                <td class="text-center fw-bold text-danger" style="color: #ce2127 !important;"><?= number_format($s['skp'], 0) ?> JPL</td>
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-1 align-items-center">
                                        <?php if(!empty($s['file_path'])): ?>
                                            <a href="<?= base_url($s['file_path']) ?>" target="_blank" class="btn btn-xs btn-outline-danger px-2.5 py-1 rounded-pill fw-bold" style="font-size:0.65rem; color: #ce2127; border-color: #ce2127;"><i class="fas fa-file-pdf"></i> Sertifikat</a>
                                        <?php endif; ?>
                                        <?php if(!empty($s['surat_tugas_path'])): ?>
                                            <a href="<?= base_url($s['surat_tugas_path']) ?>" target="_blank" class="btn btn-xs btn-outline-dark px-2.5 py-1 rounded-pill fw-bold" style="font-size:0.65rem;"><i class="fas fa-file-contract"></i> Surat Tugas</a>
                                        <?php endif; ?>
                                        <?php if(empty($s['file_path']) && empty($s['surat_tugas_path'])): ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if($s['verifikasi'] == 'approved'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1.5 fw-bold"><i class="fas fa-check-circle me-1"></i> DISETUJUI</span>
                                    <?php elseif($s['verifikasi'] == 'rejected'): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-bold" title="<?= esc($s['alasan_penolakan']) ?>"><i class="fas fa-times-circle me-1"></i> DITOLAK</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-bold"><i class="fas fa-clock me-1"></i> PENDING</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <?php if($s['verifikasi'] == 'pending'): ?>
                                            <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold" onclick="approveCert(<?= $s['id'] ?>, '<?= esc($s['user_nama']) ?>', <?= $s['skp'] ?>)">TERIMA</button>
                                            <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" style="background-color: #ce2127;" onclick="rejectCert(<?= $s['id'] ?>)">TOLAK</button>
                                        <?php else: ?>
                                            <a href="javascript:void(0)" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" onclick="confirmAction('<?= site_url('pelatihan/admin/certificate/unverify/'.$s['id']) ?>', 'Kembalikan status verifikasi ke Pending?')"><i class="fas fa-undo me-1"></i> Batal</a>
                                        <?php endif; ?>
                                        <a href="javascript:void(0)" class="btn btn-outline-info btn-sm rounded-circle" onclick='showDetailCert(<?= json_encode($s, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)' style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;" title="Detail"><i class="fas fa-info"></i></a>
                                        <button class="btn btn-outline-dark btn-sm rounded-circle" onclick='editCert(<?= json_encode($s, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)' style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;"><i class="fas fa-edit"></i></button>
                                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm rounded-circle border-0" onclick="confirmAction('<?= site_url('pelatihan/admin/certificate/delete/'.$s['id']) ?>', 'Yakin ingin menghapus sertifikat ini?')" style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center; color:#ce2127;"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="tab-pane fade p-4" id="tab-pejabat" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-signature text-danger me-2"></i> Daftar Pejabat Penandatangan</h6>
                    <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" style="background-color: #ce2127;" onclick="openPejabatModal()"><i class="fas fa-plus"></i> Tambah Pejabat</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small fw-bold">
                            <tr>
                                <th>NAMA / NIP</th>
                                <th>JABATAN & ATAS NAMA</th>
                                <th class="text-center">TTD FILE</th>

                                <th class="pe-4 text-center" style="width:120px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pejabat)): ?>
                                <tr><td colspan="5" class="text-center py-4 text-muted fw-bold">Belum ada pejabat penandatangan.</td></tr>
                            <?php else: foreach($pejabat as $pj): ?>
                            <tr>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <?php if(!empty($pj['foto'])): ?>
                                            <a href="javascript:void(0)" onclick="previewImage('<?= str_starts_with($pj['foto'], 'uploads/') ? base_url($pj['foto']) : base_url('uploads/pelatihan/'.$pj['foto']) ?>', 'Foto Profil')" title="Preview Foto">
                                                <img src="<?= str_starts_with($pj['foto'], 'uploads/') ? base_url($pj['foto']) : base_url('uploads/pelatihan/'.$pj['foto']) ?>" class="rounded-circle me-2 border border-secondary shadow-sm" width="35" height="35" style="object-fit:cover;">
                                            </a>
                                        <?php else: ?>
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 fw-bold" style="width:35px;height:35px;font-size:0.75rem;">
                                                <?= strtoupper(substr($pj['nama_pejabat'], 0, 2)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark"><?= esc($pj['nama_pejabat']) ?></div>
                                            <div class="text-muted small">NIP: <?= esc($pj['nip_pejabat'] ?? '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark small"><?= esc($pj['jabatan']) ?></div>
                                    <div class="text-muted small"><?= esc($pj['an_pejabat'] ?? '-') ?></div>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($pj['ttd_image'])): ?>
                                        <img src="<?= base_url('uploads/pelatihan/' . $pj['ttd_image']) ?>" height="40" style="mix-blend-mode: multiply; max-width:120px;">
                                    <?php else: ?>
                                        <span class="text-muted small">Belum Upload</span>
                                    <?php endif; ?>
                                </td>

                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                          <?php if(!empty($pj['ttd_image'])): ?>
                                              <a href="javascript:void(0)" onclick="previewImage('<?= str_starts_with($pj['ttd_image'], 'uploads/') ? base_url($pj['ttd_image']) : base_url('uploads/pelatihan/'.$pj['ttd_image']) ?>', 'Tanda Tangan')" class="btn btn-outline-info btn-sm rounded-circle border-0" style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;" title="Preview TTD"><i class="fas fa-eye"></i></a>
                                          <?php endif; ?>
                                        <button class="btn btn-outline-dark btn-sm rounded-circle" onclick='editPejabat(<?= json_encode($pj, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)' style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;"><i class="fas fa-edit"></i></button>
                                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm rounded-circle border-0" onclick="confirmAction('<?= site_url('pelatihan/admin/certificate/delete_pejabat/'.$pj['id']) ?>', 'Hapus pejabat ini?')" style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center; color: #ce2127;"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade p-4" id="tab-templates" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-file-invoice text-danger me-2"></i> Pengaturan Template Sertifikat RSUD</h6>
                    <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" style="background-color: #ce2127;" onclick="toggleTemplateForm(true)"><i class="fas fa-plus"></i> Tambah Template</button>
                </div>
                
                <div id="templateFormContainer" class="card border-0 shadow-sm rounded-4 mb-4" style="display: none;">
                    <div class="card-header bg-dark text-white p-3 rounded-top-4">
                        <h6 class="fw-bold mb-0"><i class="fas fa-file-invoice me-2 text-danger"></i> Form Template Sertifikat</h6>
                    </div>
                    <div class="card-body p-4 bg-light bg-opacity-50">
                        <form action="<?= site_url('pelatihan/admin/certificate/save_template') ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="tmp_id">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Pilih Pelatihan / Program</label>
                                    <select name="pelatihan_id" id="tmp_pelatihan" class="form-select rounded-pill border" required>
                                        <?php foreach($pelatihan as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= esc($p['nama']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Format Nomor Sertifikat</label>
                                    <input type="text" name="no_sertifikat" id="tmp_nomor" class="form-control rounded-pill border" placeholder="Contoh: 001/SERT/RSUD-PEL/VII/2026" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Warna Background (Hex)</label>
                                    <input type="color" name="background_color" id="tmp_background" class="form-control border w-100" style="height:40px; border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Pejabat Utama Penandatangan</label>
                                    <select name="pejabat_id_1" id="tmp_pejabat_1" class="form-select rounded-pill border" required>
                                        <option value="">-- Pilih Pejabat 1 --</option>
                                        <?php foreach($pejabat as $pj): ?>
                                            <option value="<?= $pj['id'] ?>"><?= esc($pj['nama_pejabat']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Pejabat Pendamping (Opsional)</label>
                                    <select name="pejabat_id_2" id="tmp_pejabat_2" class="form-select rounded-pill border">
                                        <option value="">-- Tidak ada / Pilih Pejabat 2 --</option>
                                        <?php foreach($pejabat as $pj): ?>
                                            <option value="<?= $pj['id'] ?>"><?= esc($pj['nama_pejabat']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Upload Logo Header (PNG/JPG)</label>
                                    <input type="file" name="logo_header" class="form-control rounded-pill border" accept="image/*">
                                </div>
                            </div>
                            <div class="mt-4 text-end">
                                <button type="button" class="btn btn-light rounded-pill px-4" onclick="toggleTemplateForm(false)">Batal</button>
                                <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold">Simpan Template</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="table-responsive" id="templateTableContainer">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small fw-bold">
                            <tr>
                                <th>NAMA PELATIHAN</th>
                                <th>FORMAT NOMOR</th>
                                <th>WARNA BACKGROUND</th>
                                <th>PEJABAT PENANDATANGAN</th>
                                <th class="text-center">STATUS</th>
                                <th class="pe-4 text-center" style="width:220px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($templates)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted fw-bold">Belum ada template sertifikat.</td></tr>
                            <?php else: foreach($templates as $tmp): ?>
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark"><?= esc($tmp['nama_pelatihan']) ?></div>
                                </td>
                                <td class="font-monospace small"><?= esc($tmp['no_sertifikat']) ?></td>
                                <td>
                                    <span class="badge rounded-pill border px-3 text-dark small" style="background-color: <?= esc($tmp['background_color']) ?>;"><?= esc($tmp['background_color']) ?></span>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">1. <?= esc($tmp['nama_pejabat_1'] ?? 'Belum dipilih') ?></div>
                                    <?php if($tmp['pejabat_id_2']): ?>
                                        <div class="small text-muted">2. <?= esc($tmp['nama_pejabat_2']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (($tmp['status'] ?? 'draft') === 'diterbitkan'): ?>
                                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1 fw-bold text-uppercase small">DIKONFIRMASI</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fw-bold text-uppercase small">DRAFT</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="<?= site_url('pelatihan/admin/certificate/preview_template/' . $tmp['id']) ?>" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill fw-bold"><i class="fas fa-eye text-danger"></i> Preview</a>
                                        <button class="btn btn-outline-dark btn-sm rounded-circle" onclick='editTemplate(<?= json_encode($tmp, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)' style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;"><i class="fas fa-edit"></i></button>
                                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm rounded-circle border-0" onclick="confirmAction('<?= site_url('pelatihan/admin/certificate/delete_template/'.$tmp['id']) ?>', 'Hapus template ini?')" style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center; color: #ce2127;"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($role === 'admin_pengabdian'): ?>
            <div class="tab-pane fade show active p-4" id="tab-pengabdian" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white text-muted small fw-bold">
                            <tr>
                                <th class="ps-4">NAMA / NIK</th>
                                <th>JUDUL KEGIATAN</th>
                                <th class="text-center">JPL</th>
                                <th class="text-center">BERKAS</th>
                                <th class="text-center">STATUS</th>
                                <th class="pe-4 text-center" style="width:250px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $filtered = array_filter($sertifikat, function($s) {
                                $type = strtolower(str_replace(' ', '_', trim($s['jenis_dokumen'] ?? '')));
                                return $type === 'pengabdian';
                            });
                            if (empty($filtered)):
                            ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted fw-bold">Belum ada unggahan Pengabdian Masyarakat.</td></tr>
                            <?php else: foreach($filtered as $s): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold small text-dark"><?= esc($s['user_nama']) ?></div>
                                    <div class="text-muted small font-monospace" style="font-size: 0.65rem;"><?= esc($s['user_id']) ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold small text-dark"><?= esc($s['judul']) ?></div>
                                    <div class="text-muted" style="font-size: 0.65rem;">Penerbit: <?= esc($s['penerbit'] ?? '-') ?></div>
                                </td>
                                <td class="text-center fw-bold text-danger" style="color: #ce2127 !important;"><?= number_format($s['skp'], 0) ?> JPL</td>
                                <td class="text-center">
                                    <?php if(!empty($s['file_path'])): ?>
                                        <a href="<?= base_url($s['file_path']) ?>" target="_blank" class="btn btn-xs btn-outline-danger px-2.5 py-1 rounded-pill fw-bold" style="font-size:0.65rem; color:#ce2127; border-color:#ce2127;"><i class="fas fa-file-pdf"></i> Lihat File</a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($s['verifikasi'] == 'approved'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1.5 fw-bold"><i class="fas fa-check-circle me-1"></i> DISETUJUI</span>
                                    <?php elseif($s['verifikasi'] == 'rejected'): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-bold" title="<?= esc($s['alasan_penolakan']) ?>"><i class="fas fa-times-circle me-1"></i> DITOLAK</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 fw-bold"><i class="fas fa-clock me-1"></i> PENDING</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <?php if($s['verifikasi'] == 'pending'): ?>
                                            <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold" onclick="approveCert(<?= $s['id'] ?>, '<?= esc($s['user_nama']) ?>', <?= $s['skp'] ?>)">TERIMA</button>
                                            <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" style="background-color: #ce2127;" onclick="rejectCert(<?= $s['id'] ?>)">TOLAK</button>
                                        <?php else: ?>
                                            <a href="javascript:void(0)" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold" onclick="confirmAction('<?= site_url('pelatihan/admin/certificate/unverify/'.$s['id']) ?>', 'Kembalikan status verifikasi ke Pending?')"><i class="fas fa-undo me-1"></i> Batal</a>
                                        <?php endif; ?>
                                        <a href="javascript:void(0)" class="btn btn-outline-info btn-sm rounded-circle" onclick='showDetailCert(<?= json_encode($s, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)' style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;" title="Detail"><i class="fas fa-info"></i></a>
                                        <button class="btn btn-outline-dark btn-sm rounded-circle" onclick='editCert(<?= json_encode($s, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS) ?>)' style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;"><i class="fas fa-edit"></i></button>
                                        <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm rounded-circle border-0" onclick="confirmAction('<?= site_url('pelatihan/admin/certificate/delete/'.$s['id']) ?>', 'Yakin ingin menghapus sertifikat ini?')" style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center; color:#ce2127;"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="pejabatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-signature me-2 text-danger"></i> Form Pejabat / Narasumber</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('pelatihan/admin/certificate/save_pejabat') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="pj_id">
                <div class="modal-body p-4">
                    <?php
                    $pejabat = $pj_edit ?? [];
                    $prefix = 'pj';
                    $showStatus = true;
                    $showFoto = true;
                    $showTtd = true;
                    $showRiwayat = true;
                    ?>
                    <?= $this->include('Pelatihan/admin/_partials/form_pejabat_fields') ?>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold">Simpan Pejabat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCertModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2 text-danger"></i> Edit Data Kegiatan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('pelatihan/admin/certificate/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_cert_id">
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Judul Kegiatan</label>
                        <input type="text" name="judul" id="edit_cert_judul" class="form-control rounded-pill border" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Penerbit / Penyelenggara</label>
                        <input type="text" name="penerbit" id="edit_cert_penerbit" class="form-control rounded-pill border" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nomor Sertifikat</label>
                        <input type="text" name="no_sertifikat" id="edit_cert_nomor" class="form-control rounded-pill border">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Beban Belajar (JPL)</label>
                        <input type="number" name="skp" id="edit_cert_skp" class="form-control rounded-pill border" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Ranah</label>
                        <select name="ranah" id="edit_cert_ranah" class="form-select rounded-pill border">
                            <option value="Pembelajaran">Pembelajaran</option>
                            <option value="Pengabdian">Pengabdian</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tgl Mulai</label>
                            <input type="date" name="tgl_mulai" id="edit_cert_tgl_mulai" class="form-control rounded-pill border">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tgl Selesai</label>
                            <input type="date" name="tgl_selesai" id="edit_cert_tgl_selesai" class="form-control rounded-pill border">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold shadow">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="approveJplModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-dark text-white p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-check-circle me-2 text-success"></i> Verifikasi JPL Kegiatan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveJplForm" method="POST">
                <div class="modal-body p-4 bg-light">
                    <div class="bg-white p-3 rounded-4 shadow-sm border-start border-danger border-5 mb-4">
                        <div class="small text-muted fw-bold text-uppercase mb-1">PENGIRIM</div>
                        <div class="fw-bold text-dark mb-2" id="app_user_display">-</div>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-danger">KONFIRMASI JUMLAH JAM PELAJARAN (JPL)</label>
                        <div class="input-group input-group-lg">
                            <input type="number" step="1" name="jpl" id="app_jpl_input" class="form-control border-danger fw-bold text-center" required>
                            <span class="input-group-text bg-danger text-white border-danger fw-bold" style="background-color: #ce2127 !important; border-color: #ce2127 !important;">JPL</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold shadow">Setujui & Plot JPL</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOfficialParticipants" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-bold text-uppercase" id="official_modal_title">Daftar Penerima Sertifikat</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white text-muted small fw-bold">
                            <tr>
                                <th class="ps-4">PESERTA</th>
                                <th class="text-center">STATUS KELULUSAN</th>
                                <th class="pe-4 text-center">AKSI PRATINJAU</th>
                            </tr>
                        </thead>
                        <tbody id="official_participant_list">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button class="btn btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Sertifikat -->
<div class="modal fade" id="detailCertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-bold text-uppercase"><i class="fas fa-info-circle me-2"></i> Detail Informasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">NAMA PESERTA</label>
                        <div class="fw-bold" id="detail_nama"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">NIK / ID USER</label>
                        <div class="fw-bold font-monospace" id="detail_nik"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">PROFESI</label>
                        <div class="fw-bold" id="detail_profesi"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">KATEGORI KEGIATAN</label>
                        <div class="fw-bold text-uppercase" id="detail_kategori"></div>
                    </div>
                    <div class="col-md-12">
                        <label class="small text-muted fw-bold">JUDUL KEGIATAN</label>
                        <div class="fw-bold text-dark" id="detail_judul"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">RANAH</label>
                        <div class="fw-bold text-uppercase" id="detail_ranah"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">TOTAL SKP / JPL</label>
                        <div class="fw-bold text-danger" id="detail_skp"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">TANGGAL MULAI</label>
                        <div class="fw-bold" id="detail_mulai"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">TANGGAL SELESAI</label>
                        <div class="fw-bold" id="detail_selesai"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">PENERBIT</label>
                        <div class="fw-bold" id="detail_penerbit"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">JENIS DOKUMEN</label>
                        <div class="fw-bold text-uppercase" id="detail_jenis"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">TANGGAL UPLOAD</label>
                        <div class="fw-bold" id="detail_upload"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="small text-muted fw-bold">STATUS VERIFIKASI</label>
                        <div class="fw-bold text-uppercase" id="detail_verifikasi"></div>
                    </div>
                    
                    <div class="col-12 mt-4" id="detail_berkas_container">
                        <label class="small text-muted fw-bold mb-2">BERKAS TERLAMPIR</label>
                        <div class="d-flex gap-2" id="detail_berkas_links">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <button class="btn btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">TUTUP</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.pjModal = new bootstrap.Modal(document.getElementById('pejabatModal'));
        window.appJplModal = new bootstrap.Modal(document.getElementById('approveJplModal'));
        window.officialModal = new bootstrap.Modal(document.getElementById('modalOfficialParticipants'));
        window.editCertModalObj = new bootstrap.Modal(document.getElementById('editCertModal'));

        // === LOGIC: MEMPERTAHANKAN ACTIVE TAB SETELAH REFRESH / SAVE ===
        const activeTabId = localStorage.getItem('activeCertTab');
        if (activeTabId) {
            const tabTrigger = document.querySelector(`#${activeTabId}`);
            if (tabTrigger) {
                const tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        } else {
            // Default jika tidak ada tab yang tersimpan di localStorage
            const firstTabTrigger = document.querySelector('#certTab button');
            if (firstTabTrigger) {
                const tab = new bootstrap.Tab(firstTabTrigger);
                tab.show();
            }
        }

        // Simpan id tab setiap kali tab diklik
        const tabButtons = document.querySelectorAll('#certTab button[data-bs-toggle="tab"]');
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', function(event) {
                localStorage.setItem('activeCertTab', event.target.id);
            });
        });



        // Exclusive Pejabat Dropdowns Validation
        const p1 = document.getElementById('tmp_pejabat_1');
        const p2 = document.getElementById('tmp_pejabat_2');

        if (p1 && p2) {
            function updatePejabatDropdowns() {
                const v1 = p1.value;
                const v2 = p2.value;

                Array.from(p1.options).forEach(opt => {
                    if (opt.value && opt.value === v2) {
                        opt.disabled = true;
                    } else {
                        opt.disabled = false;
                    }
                });

                Array.from(p2.options).forEach(opt => {
                    if (opt.value && opt.value === v1) {
                        opt.disabled = true;
                    } else {
                        opt.disabled = false;
                    }
                });
            }

            p1.addEventListener('change', updatePejabatDropdowns);
            p2.addEventListener('change', updatePejabatDropdowns);
            window.updatePejabatDropdowns = updatePejabatDropdowns;
        }
    });

    function openPejabatModal() {
        document.getElementById('pj_id').value = '';
        document.getElementById('pj_status').value = 'Narasumber';
        document.getElementById('pj_an').value = '';
        document.getElementById('pj_jabatan').value = '';
        document.getElementById('pj_nama').value = '';
        document.getElementById('pj_nip').value = '';
        document.getElementById('pj_gelar_depan').value = '';
        document.getElementById('pj_gelar_belakang').value = '';
        document.getElementById('pj_pendidikan').value = '';
        document.getElementById('pj_keahlian').value = '';
        document.getElementById('pj_kontak').value = '';
        document.getElementById('pj_email').value = '';
        document.getElementById('pj_riwayat').value = '';
        pjModal.show();
    }

    function editPejabat(pj) {
        document.getElementById('pj_id').value = pj.id;
        if(document.getElementById('pj_status')) document.getElementById('pj_status').value = pj.status || 'Narasumber';
        document.getElementById('pj_an').value = pj.an_pejabat || '';
        document.getElementById('pj_jabatan').value = pj.jabatan || '';
        document.getElementById('pj_nama').value = pj.nama_pejabat || '';
        document.getElementById('pj_nip').value = pj.nip_pejabat || '';
        document.getElementById('pj_gelar_depan').value = pj.gelar_depan || '';
        document.getElementById('pj_gelar_belakang').value = pj.gelar_belakang || '';
        document.getElementById('pj_pendidikan').value = pj.pendidikan || '';
        document.getElementById('pj_keahlian').value = pj.keahlian || '';
        document.getElementById('pj_kontak').value = pj.kontak || '';
        document.getElementById('pj_email').value = pj.email || '';
        document.getElementById('pj_riwayat').value = pj.riwayat || '';
        pjModal.show();
    }

    function toggleTemplateForm(show) {
        if(show) {
            document.getElementById('templateTableContainer').style.display = 'none';
            document.getElementById('templateFormContainer').style.display = 'block';
        } else {
            document.getElementById('templateTableContainer').style.display = 'block';
            document.getElementById('templateFormContainer').style.display = 'none';
            document.getElementById('tmp_id').value = '';
            document.getElementById('tmp_pelatihan').value = '';
            document.getElementById('tmp_nomor').value = '';
            document.getElementById('tmp_background').value = '#ffffff';
            document.getElementById('tmp_pejabat_1').value = '';
            document.getElementById('tmp_pejabat_2').value = '';
            if (window.updatePejabatDropdowns) {
                window.updatePejabatDropdowns();
            }
        }
    }

    function editTemplate(tmp) {
        document.getElementById('tmp_id').value = tmp.id;
        document.getElementById('tmp_pelatihan').value = tmp.pelatihan_id;
        document.getElementById('tmp_nomor').value = tmp.no_sertifikat;
        document.getElementById('tmp_background').value = tmp.background_color;
        document.getElementById('tmp_pejabat_1').value = tmp.pejabat_id_1 || '';
        document.getElementById('tmp_pejabat_2').value = tmp.pejabat_id_2 || '';
        
        toggleTemplateForm(true);
        if (window.updatePejabatDropdowns) {
            window.updatePejabatDropdowns();
        }
    }

    function editCert(s) {
        document.getElementById('edit_cert_id').value = s.id;
        document.getElementById('edit_cert_judul').value = s.judul;
        document.getElementById('edit_cert_penerbit').value = s.penerbit || '';
        document.getElementById('edit_cert_nomor').value = s.no_sertifikat || '';
        document.getElementById('edit_cert_skp').value = parseInt(s.skp) || 0;
        document.getElementById('edit_cert_ranah').value = s.ranah || 'Pembelajaran';
        document.getElementById('edit_cert_tgl_mulai').value = s.tgl_mulai || '';
        document.getElementById('edit_cert_tgl_selesai').value = s.tgl_selesai || '';
        editCertModalObj.show();
    }

    function approveCert(id, userName, skp) {
        document.getElementById('app_user_display').innerText = userName;
        document.getElementById('app_jpl_input').value = skp;
        document.getElementById('approveJplForm').action = '<?= site_url("pelatihan/admin/certificate/approve") ?>/' + id;
        appJplModal.show();
    }

    function rejectCert(id) {
        Swal.fire({
            title: '<span class="fw-bold fs-5 text-dark">Tolak Sertifikat</span>',
            input: 'textarea',
            inputLabel: 'Masukkan alasan penolakan berkas:',
            inputPlaceholder: 'Tulis alasan penolakan di sini...',
            inputAttributes: {
                'aria-label': 'Tulis alasan penolakan di sini'
            },
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#212529',
            confirmButtonText: 'Kirim Penolakan',
            cancelButtonText: 'Batal',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('Alasan penolakan harus diisi!');
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= site_url("pelatihan/admin/certificate/reject") ?>/' + id;
                
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'alasan_penolakan';
                input.value = result.value;
                form.appendChild(input);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function showIssuedParticipants(id, title) {
        document.getElementById('official_modal_title').innerText = title;
        
        fetch('<?= site_url("pelatihan/admin/certificate/peserta_by_pelatihan") ?>/' + id)
        .then(res => res.json())
        .then(list => {
            const tbody = document.getElementById('official_participant_list');
            tbody.innerHTML = '';
            
            if(list && list.length > 0) {
                list.forEach(p => {
                    let statusBadge = 'bg-secondary';
                    let statusText = 'DAFTAR';
                    if (p.status_peserta === 'Lulus') {
                        statusBadge = 'bg-success';
                        statusText = 'LULUS';
                    } else if (p.status_peserta === 'Tidak Lulus') {
                        statusBadge = 'bg-danger';
                        statusText = 'TIDAK LULUS';
                    } else if (p.status_peserta) {
                        statusText = p.status_peserta.toUpperCase();
                        if (statusText !== 'DAFTAR') statusBadge = 'bg-info';
                    }

                    tbody.innerHTML += `
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="fw-bold small text-dark">${p.nama}</div>
                                <div class="text-muted font-monospace" style="font-size: 0.6rem;">NIK: ${p.nik}</div>
                            </td>
                            <td class="text-center">
                                <span class="badge ${statusBadge} text-white rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.6rem;">${statusText}</span>
                            </td>
                            <td class="pe-4 text-center">
                                ${p.status_peserta === 'Lulus' ? `<a href="<?= site_url('pelatihan/admin/certificate/preview_pelatihan') ?>/${id}/${p.nik}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill fw-bold"><i class="fas fa-eye text-danger me-1"></i> LIHAT</a>` : `<span class="text-muted small">-</span>`}
                            </td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada peserta terdaftar.</td></tr>`;
            }
            officialModal.show();
        });
    }

    function confirmPublish(id, nama) {
        Swal.fire({
            title: '<span class="fw-bold fs-5 d-block mt-2">Terbitkan Sertifikat?</span>',
            html: `<p class="text-muted fw-bold px-2">Semua e-Sertifikat untuk pelatihan <b>${nama}</b> akan diterbitkan dan JPL peserta yang lulus akan diperbarui.</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Ya, Terbitkan!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg border-0', confirmButton: 'rounded-pill px-4 py-2 fw-bold', cancelButton: 'rounded-pill px-4 py-2 fw-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= site_url("pelatihan/admin/certificate/publish/") ?>' + id;
            }
        });
    }

    function confirmUnpublish(id, nama) {
        Swal.fire({
            title: '<span class="fw-bold fs-5 d-block mt-2">Batalkan Penerbitan?</span>',
            html: `<p class="text-muted fw-bold px-2">Semua e-Sertifikat untuk pelatihan <b>${nama}</b> akan ditarik dan JPL peserta akan dikurangi.</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-times me-1"></i> Ya, Batalkan!',
            cancelButtonText: 'Kembali',
            customClass: { popup: 'rounded-4 shadow-lg border-0', confirmButton: 'rounded-pill px-4 py-2 fw-bold', cancelButton: 'rounded-pill px-4 py-2 fw-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= site_url("pelatihan/admin/certificate/unpublish/") ?>' + id;
            }
        });
    }
    function confirmAction(url, message) {
        Swal.fire({
            title: '<span class="fw-bold fs-5 d-block mt-2">Konfirmasi Aksi</span>',
            html: `<p class="text-muted fw-bold px-2">${message}</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ce2127',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-4 shadow-lg border-0', confirmButton: 'rounded-pill px-4 py-2 fw-bold', cancelButton: 'rounded-pill px-4 py-2 fw-bold' }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    function getPreviewHtml(path, title) {
        if (!path) return '';
        let ext = path.split('.').pop().toLowerCase();
        let baseUrl = "<?= base_url() ?>";
        if (!baseUrl.endsWith('/')) baseUrl += '/';
        let cleanPath = path.startsWith('/') ? path.substring(1) : path;
        let url = baseUrl + cleanPath;
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            return `<div class="mb-4 w-100"><div class="fw-bold mb-2 text-dark"><i class="fas fa-image text-danger me-1"></i> ${title}:</div><div class="text-center"><img src="${url}" class="img-fluid rounded shadow-sm border" style="max-height: 500px;"></div></div>`;
        } else if (ext === 'pdf') {
            return `<div class="mb-4 w-100"><div class="fw-bold mb-2 text-dark"><i class="fas fa-file-pdf text-danger me-1"></i> ${title}:</div><iframe src="${url}" class="w-100 rounded border shadow-sm" style="height: 500px;"></iframe></div>`;
        }
        return `<div class="mb-4 w-100"><div class="fw-bold mb-2 text-dark"><i class="fas fa-file text-muted me-1"></i> ${title}:</div><a href="${url}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3"><i class="fas fa-download me-1"></i> Unduh / Lihat File</a></div>`;
    }

    function showDetailCert(cert) {
        document.getElementById('detail_nama').innerText = cert.user_nama || '-';
        document.getElementById('detail_nik').innerText = cert.user_id || '-';
        document.getElementById('detail_profesi').innerText = cert.user_profesi || '-';
        document.getElementById('detail_kategori').innerText = cert.kategori_kegiatan || '-';
        document.getElementById('detail_judul').innerText = cert.judul || '-';
        document.getElementById('detail_ranah').innerText = cert.ranah || '-';
        document.getElementById('detail_skp').innerText = cert.skp ? parseFloat(cert.skp) + ' JPL' : '-';
        document.getElementById('detail_mulai').innerText = cert.tgl_mulai || '-';
        document.getElementById('detail_selesai').innerText = cert.tgl_selesai || '-';
        document.getElementById('detail_penerbit').innerText = cert.penerbit || '-';
        document.getElementById('detail_jenis').innerText = cert.jenis_dokumen || '-';
        document.getElementById('detail_upload').innerText = cert.tgl_upload || '-';
        
        let statusText = cert.verifikasi || '-';
        if (cert.verifikasi === 'approved') statusText = 'DISETUJUI';
        else if (cert.verifikasi === 'rejected') statusText = 'DITOLAK: ' + (cert.alasan_penolakan || '');
        else if (cert.verifikasi === 'pending') statusText = 'PENDING';
        document.getElementById('detail_verifikasi').innerText = statusText;

        const berkasLinks = document.getElementById('detail_berkas_links');
        berkasLinks.innerHTML = '';
        berkasLinks.classList.remove('d-flex', 'gap-2'); // Remove flex so they stack block-level
        
        if (cert.file_path) {
            berkasLinks.innerHTML += getPreviewHtml(cert.file_path, 'Dokumen Sertifikat/Bukti');
        }
        if (cert.surat_tugas_path) {
            berkasLinks.innerHTML += getPreviewHtml(cert.surat_tugas_path, 'Surat Tugas');
        }
        if (!cert.file_path && !cert.surat_tugas_path) {
            berkasLinks.innerHTML = '<span class="text-muted small">Tidak ada lampiran berkas</span>';
        }

        const modal = new bootstrap.Modal(document.getElementById('detailCertModal'));
        modal.show();
    }
    function previewImage(url, title) {
        document.getElementById('previewImageSrc').src = url;
        document.getElementById('previewImageTitle').innerText = title || '';
        new bootstrap.Modal(document.getElementById('modalPreviewImage')).show();
    }
</script>

<!-- Modal Preview Image -->
<div class="modal fade" id="modalPreviewImage" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg bg-transparent">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 0.5rem;"></button>
            </div>
            <div class="modal-body text-center p-0">
                <div class="bg-white p-3 rounded shadow d-inline-block">
                    <img id="previewImageSrc" src="" class="img-fluid rounded" style="max-height: 70vh;">
                </div>
                <div id="previewImageTitle" class="text-white fw-bold mt-2 fs-5" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.8);"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link { color: #64748b !important; border: none; border-bottom: 3px solid transparent; transition: 0.3s; }
    .nav-tabs .nav-link:hover { color: #ce2127 !important; border-bottom: 3px solid transparent; }
    .nav-tabs .nav-link.active { color: #ce2127 !important; border-bottom: 3px solid #ce2127 !important; background: #fff; box-shadow: inset 0 -2px 0 #ce2127; }
    .rounded-lg { border-radius: 1.25rem !important; }
</style>
<?= $this->endSection() ?>
