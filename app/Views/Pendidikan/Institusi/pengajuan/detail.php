<?= $this->include('Pendidikan/Institusi/layout/header') ?>
<?= $this->include('Pendidikan/Institusi/layout/sidebar') ?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold">Detail Pengajuan</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('pendidikan/institusi/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('pendidikan/institusi/pengajuan/status') ?>">Status Pengajuan</a></li>
                        <li class="breadcrumb-item active"><?= $pengajuan['no_pengajuan'] ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informasi Utama -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Informasi Institusi & Pengajuan</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Nama Institusi</p>
                        <h6 class="fw-bold"><?= $pengajuan['institusi'] ?></h6>
                    </div>
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Prodi</p>
                        <h6 class="fw-bold"><?= $pengajuan['prodi'] ?></h6>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-4">
                        <p class="text-muted small mb-1">Periode Koas</p>
                        <h6 class="fw-bold"><?= $pengajuan['periode'] ?></h6>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted small mb-1">Tanggal Mulai</p>
                        <h6 class="fw-bold"><?= date('d M Y', strtotime($pengajuan['tgl_mulai'])) ?></h6>
                    </div>
                    <div class="col-sm-4">
                        <p class="text-muted small mb-1">Tanggal Selesai</p>
                        <h6 class="fw-bold"><?= date('d M Y', strtotime($pengajuan['tgl_selesai'])) ?></h6>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text-muted small mb-1">Penanggung Jawab</p>
                        <h6 class="fw-bold"><?= $pengajuan['penanggung_jawab'] ?></h6>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <p class="text-muted small mb-1">Kontak PJ</p>
                        <p class="mb-0 fw-bold"><?= $pengajuan['hp_pj'] ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Daftar Mahasiswa (<?= count($pengajuan['mahasiswa']) ?> Orang)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-uppercase">
                                <th class="ps-3">Mahasiswa</th>
                                <th>NIM</th>
                                <th>Tgl Lahir</th>
                                <th>JK</th>
                                <th>Semester</th>
                                <th>Ijazah Terakhir</th>
                                <th>Surat Ket. Mahasiswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pengajuan['mahasiswa'] as $mhs) : ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <?php if($mhs['file_foto']): ?>
                                                <img src="<?= base_url('uploads/dokumen_mahasiswa/' . $mhs['file_foto']) ?>" class="rounded-circle me-3 shadow-sm" style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #fff;" alt="Foto">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary me-3 shadow-sm" style="width: 45px; height: 45px; border: 2px solid #fff;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="fw-bold"><?= $mhs['nama'] ?></span>
                                        </div>
                                    </td>
                                    <td><?= $mhs['nim'] ?></td>
                                    <td><?= isset($mhs['dob']) ? date('d-m-Y', strtotime($mhs['dob'])) : '-' ?></td>
                                    <td><?= $mhs['jk'] ?></td>
                                    <td><?= $mhs['semester'] ?></td>
                                    <td>
                                        <?php if($mhs['file_ijazah']): ?>
                                            <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mhs['file_ijazah']) ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="Lihat Ijazah"><i class="fas fa-file-pdf"></i></a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($mhs['file_sk']): ?>
                                            <a href="<?= base_url('uploads/dokumen_mahasiswa/' . $mhs['file_sk']) ?>" target="_blank" class="btn btn-sm btn-outline-danger" title="Lihat Surat Keterangan"><i class="fas fa-file-pdf"></i></a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Status -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Status Pengajuan</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <?php 
                        $badge_class = 'badge-menunggu';
                        if ($pengajuan['status'] == 'Disetujui') $badge_class = 'badge-disetujui';
                        if ($pengajuan['status'] == 'Ditolak') $badge_class = 'badge-ditolak';
                        if ($pengajuan['status'] == 'Revisi') $badge_class = 'badge-revisi';
                    ?>
                    <h3 class="badge <?= $badge_class ?> px-4 py-3 w-100" style="font-size: 1.2rem;">
                        <?= $pengajuan['status'] ?>
                    </h3>
                </div>
                
                <div class="p-3 bg-light rounded mb-3">
                    <p class="fw-bold small mb-2"><i class="fas fa-comment-dots me-2"></i> Catatan Diklat:</p>
                    <p class="small text-muted mb-0 italic">"<?= $pengajuan['catatan'] ?? 'Belum ada catatan.' ?>"</p>
                </div>

                <div class="d-grid gap-2">
                    <?php if ($pengajuan['status'] == 'Revisi') : ?>
                        <a href="<?= base_url('pendidikan/institusi/pengajuan/edit/' . $pengajuan['id']) ?>" class="btn btn-danger fw-bold">
                            <i class="fas fa-edit me-1"></i> Edit Pengajuan
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('pendidikan/institusi/pengajuan/status') ?>" class="btn btn-outline-secondary">Kembali ke Daftar</a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Dokumen Terlampir</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 1. Proposal</div>
                        <a href="<?= $pengajuan['file_proposal'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_proposal']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_proposal'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 2. Surat Pengantar</div>
                        <a href="<?= $pengajuan['file_surat_pengantar'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_surat_pengantar']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_surat_pengantar'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 3. Log Book</div>
                        <a href="<?= $pengajuan['file_logbook'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_logbook']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_logbook'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 4. Buku Panduan</div>
                        <a href="<?= $pengajuan['file_panduan'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_panduan']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_panduan'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-excel text-success me-2"></i> 5. Daftar Nama Mahasiswa</div>
                        <a href="<?= $pengajuan['file_daftar_mhs'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_daftar_mhs']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_daftar_mhs'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-danger me-2"></i> 6. Surat Level Kompetensi</div>
                        <a href="<?= $pengajuan['file_kompetensi'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_kompetensi']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_kompetensi'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-pdf text-secondary me-2"></i> 7. SK Pembimbing</div>
                        <a href="<?= $pengajuan['file_sk_pembimbing'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_sk_pembimbing']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_sk_pembimbing'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="small"><i class="fas fa-file-invoice-dollar text-success me-2"></i> 8. Bukti Pembayaran</div>
                        <a href="<?= $pengajuan['file_bukti_bayar'] ? base_url('uploads/dokumen_pengajuan/' . $pengajuan['file_bukti_bayar']) : '#' ?>" class="btn btn-sm btn-light" <?= $pengajuan['file_bukti_bayar'] ? 'target="_blank"' : 'disabled' ?>><i class="fas fa-eye"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->include('Pendidikan/Institusi/layout/footer') ?>
