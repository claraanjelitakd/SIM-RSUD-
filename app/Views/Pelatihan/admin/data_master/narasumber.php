<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm rounded-lg overflow-hidden mt-1">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
       
        <button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="showModalTambah()">
            <i class="fas fa-plus-circle me-1"></i> TAMBAH NARASUMBER
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light small fw-bold text-muted">
                <tr>
                    <th class="ps-4 py-3" style="width: 50px;">NO</th>
                    <th>NAMA LENGKAP</th>
                    <th>GELAR</th>
                    <th>PENDIDIKAN</th>
                    <th>KEAHLIAN</th>
                    <th>KONTAK</th>
                    <th>STATUS</th>
                    <th class="text-center" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach($list as $item): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-muted"><?= $no++ ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if(!empty($item['foto'])): ?>
                                    <a href="javascript:void(0)" onclick="previewImage('<?= str_starts_with($item['foto'], 'uploads/') ? base_url($item['foto']) : base_url('uploads/pelatihan/'.$item['foto']) ?>', 'Foto Profil')" title="Preview Foto">
                                        <img src="<?= str_starts_with($item['foto'], 'uploads/') ? base_url($item['foto']) : base_url('uploads/pelatihan/'.$item['foto']) ?>" class="rounded-circle me-2 border border-secondary shadow-sm" width="35" height="35" style="object-fit:cover;">
                                    </a>
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-2 fw-bold" style="width:35px;height:35px;font-size:0.75rem;">
                                        <?= strtoupper(substr($item['nama_pejabat'], 0, 2)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-dark"><?= esc($item['nama_pejabat']) ?></div>
                                    <?php if(!empty($item['email'])): ?>
                                        <div class="text-muted small"><?= esc($item['email']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $gelar = [];
                                if (!empty($item['gelar_depan'])) $gelar[] = $item['gelar_depan'];
                                if (!empty($item['gelar_belakang'])) $gelar[] = $item['gelar_belakang'];
                                echo esc(implode(', ', $gelar)) ?: '-';
                            ?>
                        </td>
                        <td><span class="text-dark small"><?= esc($item['pendidikan'] ?? '-') ?></span></td>
                        <td><span class="text-dark small"><?= esc($item['keahlian'] ?? '-') ?></span></td>
                        <td><span class="text-muted small"><?= esc($item['kontak'] ?? '-') ?></span></td>
                        <td>
                            <?php if (($item['status'] ?? 'Narasumber') == 'Narasumber'): ?>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Narasumber</span>
                            <?php else: ?>
                                <span class="badge bg-info-subtle text-info rounded-pill px-3">Pejabat</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <?php if(!empty($item['ttd_image'])): ?>
                                    <a href="javascript:void(0)" onclick="previewImage('<?= str_starts_with($item['ttd_image'], 'uploads/') ? base_url($item['ttd_image']) : base_url('uploads/pelatihan/'.$item['ttd_image']) ?>', 'Tanda Tangan')" class="btn btn-outline-info btn-sm rounded-circle border-0" style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;" title="Preview TTD"><i class="fas fa-eye"></i></a>
                                <?php endif; ?>
                                <button class="btn btn-outline-dark btn-sm rounded-circle" onclick='editNarasumber(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;" title="Edit"><i class="fas fa-edit"></i></button>
                                <a href="javascript:void(0)" class="btn btn-outline-danger btn-sm rounded-circle border-0" onclick="confirmDelete(<?= $item['id'] ?>)" style="width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center; color:#ce2127;" title="Hapus"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($list)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted fw-bold italic">Belum ada data narasumber.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah/Edit Narasumber -->
<div class="modal fade" id="modalNarasumber" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold" id="modalNarasumberTitle"><i class="fas fa-plus-circle me-2 text-warning"></i> TAMBAH NARASUMBER</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('pelatihan/admin/master/simpan_narasumber') ?>" method="POST" id="formNarasumber" enctype="multipart/form-data">
                <div class="modal-body p-4 bg-light">
                    <input type="hidden" name="id" id="n_id" value="">

                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Status</label>
                            <select name="status" id="n_status" class="form-select rounded-pill border shadow-sm px-3">
                                <option value="Narasumber">Narasumber</option>
                                <option value="Pejabat">Pejabat TTD</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-bold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pejabat" id="n_nama" class="form-control rounded-pill border shadow-sm px-3" placeholder="Contoh: Dr. Budi Santoso, Sp.A" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Gelar Depan</label>
                            <input type="text" name="gelar_depan" id="n_gelar_depan" class="form-control rounded-pill border" placeholder="Dr.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Gelar Belakang</label>
                            <input type="text" name="gelar_belakang" id="n_gelar_belakang" class="form-control rounded-pill border" placeholder="M.Kes">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pendidikan</label>
                        <input type="text" name="pendidikan" id="n_pendidikan" class="form-control rounded-pill border" placeholder="S.Ked, Sp.PD">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keahlian</label>
                        <input type="text" name="keahlian" id="n_keahlian" class="form-control rounded-pill border" placeholder="Penyakit Dalam, Kardiologi">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Atas Nama Pejabat (a.n)</label>
                        <input type="text" name="an_pejabat" id="n_an" class="form-control rounded-pill border" placeholder="Contoh: a.n Direktur">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jabatan / Kedudukan</label>
                        <input type="text" name="jabatan" id="n_jabatan" class="form-control rounded-pill border" placeholder="Contoh: Direktur RSUD">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">NIP Pejabat</label>
                        <input type="text" name="nip_pejabat" id="n_nip" class="form-control rounded-pill border" placeholder="Contoh: 19690124XXXXXX">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Kontak / No. Telp</label>
                            <input type="text" name="kontak" id="n_kontak" class="form-control rounded-pill border" placeholder="08xxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" id="n_email" class="form-control rounded-pill border" placeholder="email@contoh.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Riwayat / Bio</label>
                        <textarea name="riwayat" id="n_riwayat" class="form-control border p-3" rows="3" placeholder="Riwayat singkat, pengalaman, publikasi, dll." style="border-radius: 15px;"></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Foto Profil</label>
                            <input type="file" name="foto" id="n_foto" class="form-control rounded-pill border shadow-sm px-3" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Upload TTD (PNG Transparan)</label>
                            <input type="file" name="ttd_image" id="n_ttd" class="form-control rounded-pill border shadow-sm px-3" accept=".png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">BATAL</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.narasumberModal = new bootstrap.Modal(document.getElementById('modalNarasumber'));
    });

    function showModalTambah() {
        document.getElementById('modalNarasumberTitle').innerHTML = '<i class="fas fa-plus-circle me-2 text-warning"></i> TAMBAH NARASUMBER';
        document.getElementById('formNarasumber').reset();
        document.getElementById('n_id').value = '';
        narasumberModal.show();
    }

    function editNarasumber(data) {
        document.getElementById('modalNarasumberTitle').innerHTML = '<i class="fas fa-edit me-2 text-warning"></i> EDIT NARASUMBER';
        document.getElementById('n_id').value = data.id;
        if(document.getElementById('n_status')) document.getElementById('n_status').value = data.status || 'Narasumber';
        document.getElementById('n_an').value = data.an_pejabat || '';
        document.getElementById('n_jabatan').value = data.jabatan || '';
        document.getElementById('n_nama').value = data.nama_pejabat || '';
        document.getElementById('n_nip').value = data.nip_pejabat || '';
        document.getElementById('n_gelar_depan').value = data.gelar_depan || '';
        document.getElementById('n_gelar_belakang').value = data.gelar_belakang || '';
        document.getElementById('n_pendidikan').value = data.pendidikan || '';
        document.getElementById('n_keahlian').value = data.keahlian || '';
        document.getElementById('n_kontak').value = data.kontak || '';
        document.getElementById('n_email').value = data.email || '';
        document.getElementById('n_riwayat').value = data.riwayat || '';
        narasumberModal.show();
    }

    function confirmDelete(id) {
        confirmAction('Hapus Narasumber?', 'Data narasumber akan dihapus permanen dari sistem.', function() {
            location.href = "<?= base_url('pelatihan/admin/master/hapus_narasumber/') ?>" + id;
        });
    }

    function previewImage(url, title) {
        document.getElementById('previewImageSrc').src = url;
        document.getElementById('previewImageTitle').innerText = title || '';
        new bootstrap.Modal(document.getElementById('modalPreviewImage')).show();
    }
</script>
<?= $this->endSection() ?>
