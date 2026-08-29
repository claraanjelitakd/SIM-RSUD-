<?= $this->extend('Pelatihan/layout/admin_layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<?php
$list = $list ?? [];
$history = $history ?? [];
?>

<div class="card border-0 shadow-sm rounded-custom overflow-hidden bg-white mb-5">
    <div class="card-header bg-white p-0 border-bottom">
        <ul class="nav nav-tabs nav-fill border-0 m-0" id="verifikasiTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active py-3 fw-bold small text-uppercase toggle-tab-custom" id="antrean-tab" data-bs-toggle="tab" data-bs-target="#antrean" type="button">
                    <i class="fas fa-hourglass-half text-warning me-1-5 small"></i> Antrean Verifikasi (<?= count($list) ?>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link py-3 fw-bold small text-uppercase toggle-tab-custom" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button">
                    <i class="fas fa-history text-secondary me-1-5 small"></i> Riwayat Tindakan (<?= count($history) ?>)
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="verifikasiTabContent">
        <!-- Tab Antrean -->
        <div class="tab-pane fade show active p-4" id="antrean" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="tableAntrean">
                    <thead class="bg-light text-muted small fw-bold">
                        <tr>
                            <th class="ps-3">PESERTA / NIP</th>
                            <th>PELATIHAN</th>
                            <th class="text-center">STATUS AKSES</th>
                            <th class="text-center">STATUS BAYAR</th>
                            <th class="text-center">BUKTI BAYAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $row): ?>
                            <?php 
                                $isNew = false;
                                if (!empty($row['waktu_daftar'])) {
                                    $isNew = (date('Y-m-d', strtotime($row['waktu_daftar'])) == date('Y-m-d'));
                                }
                            ?>
                            <tr class="<?= $isNew ? 'bg-primary bg-opacity-10' : '' ?>">
                                <td class="ps-3 fw-bold text-dark">
                                    <a href="javascript:void(0)" class="fw-bold text-dark text-decoration-none hover-danger btn-detail-pendaftar"
                                       data-nama="<?= esc($row['nama']) ?>"
                                       data-nip="<?= esc($row['user_id']) ?>"
                                       data-email="<?= esc($row['email']) ?>"
                                       data-wa="<?= esc($row['no_wa']) ?>"
                                       data-profesi="<?= esc($row['profesi_peserta'] ?? '-') ?>"
                                       data-unit="<?= esc($row['unit_peserta'] ?? '-') ?>">
                                        <?= esc($row['nama']) ?> <i class="fas fa-user-circle text-muted ms-1" style="font-size:0.85rem;"></i>
                                        <?php if($isNew): ?>
                                            <span class="badge bg-danger ms-1" style="font-size:0.6rem;">Baru</span>
                                        <?php endif; ?>
                                    </a>
                                    <br>
                                    <small class="text-muted font-monospace"><?= esc($row['user_id']) ?></small>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="fw-bold text-dark text-decoration-none hover-danger btn-detail-pelatihan" 
                                       data-nama="<?= esc($row['judul_pelatihan']) ?>"
                                       data-metode="<?= esc($row['metode']) ?>"
                                       data-program="<?= esc($row['program']) ?>"
                                       data-jadwal="<?= tanggal_indo($row['jadwal_mulai']) . ' s.d ' . tanggal_indo($row['jadwal_selesai']) ?>"
                                       data-kuota="<?= esc($row['kuota']) ?>"
                                       data-biaya="<?= esc($row['biaya']) . ($row['biaya_nominal'] > 0 ? ' (Rp ' . number_format($row['biaya_nominal'],0,',','.') . ')' : '') ?>"
                                       data-profesi="<?= esc($row['target_profesi']) ?>"
                                       data-deskripsi="<?= esc($row['deskripsi']) ?>">
                                        <?= esc($row['judul_pelatihan']) ?> <i class="fas fa-info-circle text-muted ms-1 small" style="font-size:0.75rem;"></i>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <span class="badge bg-secondary"><?= $row['mekanisme'] ?></span>
                                        <?php if($row['biaya_nominal'] > 0): ?>
                                            <span class="badge bg-warning text-dark">Rp <?= number_format($row['biaya_nominal'],0,',','.') ?></span>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php if(in_array($row['status_akses'], ['Terbuka'])): ?>
                                        <span class="badge bg-success"><i class="fas fa-globe"></i> Terbuka</span>
                                    <?php else: ?>
                                        <select class="form-select form-select-sm select-status mx-auto fw-bold rounded-pill shadow-sm" style="width:130px; text-align-last: center; cursor: pointer;" 
                                                data-id="<?= $row['id'] ?>" data-field="status_akses" data-old="<?= $row['status_akses'] ?>">
                                            <option class="bg-white text-dark" value="Pending" <?= $row['status_akses'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option class="bg-white text-dark" value="Diterima" <?= $row['status_akses'] == 'Approved' ? 'selected' : '' ?>>Diterima</option>
                                            <option class="bg-white text-dark" value="Ditolak" <?= $row['status_akses'] == 'Rejected' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(in_array($row['status_pembayaran'], ['Gratis'])): ?>
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Gratis</span>
                                    <?php else: ?>
                                        <select class="form-select form-select-sm select-status mx-auto fw-bold rounded-pill shadow-sm" style="width:130px; text-align-last: center; cursor: pointer;" 
                                                data-id="<?= $row['id'] ?>" data-field="status_pembayaran" data-old="<?= $row['status_pembayaran'] ?>">
                                            <option class="bg-white text-dark" value="Pending" <?= $row['status_pembayaran'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option class="bg-white text-dark" value="Diterima" <?= $row['status_pembayaran'] == 'Verified' ? 'selected' : '' ?>>Diterima</option>
                                            <option class="bg-white text-dark" value="Ditolak" <?= $row['status_pembayaran'] == 'Rejected' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(!empty($row['bukti_bayar'])): ?>
                                        <a href="<?= base_url('uploads/pelatihan/' . $row['bukti_bayar']) ?>" target="_blank" class="btn btn-sm btn-outline-dark fw-bold rounded-pill" style="font-size:0.7rem;">
                                            <i class="fas fa-file-invoice"></i> Lihat Bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab Riwayat -->
        <div class="tab-pane fade p-4" id="riwayat" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="tableRiwayat">
                    <thead class="bg-light text-muted small fw-bold">
                        <tr>
                            <th class="ps-3">PESERTA / NIP</th>
                            <th>PELATIHAN</th>
                            <th class="text-center">STATUS AKSES</th>
                            <th class="text-center">STATUS BAYAR</th>
                            <th class="text-center">STATUS AKHIR</th>
                            <th class="text-center pe-3" style="width: 120px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $row): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">
                                    <a href="javascript:void(0)" class="fw-bold text-dark text-decoration-none hover-danger btn-detail-pendaftar"
                                       data-nama="<?= esc($row['nama']) ?>"
                                       data-nip="<?= esc($row['user_id']) ?>"
                                       data-email="<?= esc($row['email']) ?>"
                                       data-wa="<?= esc($row['no_wa']) ?>"
                                       data-profesi="<?= esc($row['profesi_peserta'] ?? '-') ?>"
                                       data-unit="<?= esc($row['unit_peserta'] ?? '-') ?>">
                                        <?= esc($row['nama']) ?> <i class="fas fa-user-circle text-muted ms-1" style="font-size:0.85rem;"></i>
                                    </a>
                                    <br>
                                    <small class="text-muted font-monospace"><?= esc($row['user_id']) ?></small>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="fw-bold text-dark text-decoration-none hover-danger btn-detail-pelatihan" 
                                       data-nama="<?= esc($row['judul_pelatihan']) ?>"
                                       data-metode="<?= esc($row['metode']) ?>"
                                       data-program="<?= esc($row['program']) ?>"
                                       data-jadwal="<?= tanggal_indo($row['jadwal_mulai']) . ' s.d ' . tanggal_indo($row['jadwal_selesai']) ?>"
                                       data-kuota="<?= esc($row['kuota']) ?>"
                                       data-biaya="<?= esc($row['biaya']) . ($row['biaya_nominal'] > 0 ? ' (Rp ' . number_format($row['biaya_nominal'],0,',','.') . ')' : '') ?>"
                                       data-profesi="<?= esc($row['target_profesi']) ?>"
                                       data-deskripsi="<?= esc($row['deskripsi']) ?>">
                                        <?= esc($row['judul_pelatihan']) ?> <i class="fas fa-info-circle text-muted ms-1 small" style="font-size:0.75rem;"></i>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <span class="badge bg-secondary"><?= $row['mekanisme'] ?></span>
                                        <?php if($row['biaya_nominal'] > 0): ?>
                                            <span class="badge bg-warning text-dark">Rp <?= number_format($row['biaya_nominal'],0,',','.') ?></span>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <?php if(in_array($row['status_akses'], ['Terbuka'])): ?>
                                        <span class="badge bg-success"><i class="fas fa-globe"></i> Terbuka</span>
                                    <?php else: ?>
                                        <select class="form-select form-select-sm select-status mx-auto fw-bold rounded-pill shadow-sm" style="width:130px; text-align-last: center; cursor: pointer;" 
                                                data-id="<?= $row['id'] ?>" data-field="status_akses" data-old="<?= $row['status_akses'] ?>">
                                            <option class="bg-white text-dark" value="Pending" <?= $row['status_akses'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option class="bg-white text-dark" value="Diterima" <?= $row['status_akses'] == 'Approved' ? 'selected' : '' ?>>Diterima</option>
                                            <option class="bg-white text-dark" value="Ditolak" <?= $row['status_akses'] == 'Rejected' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if(in_array($row['status_pembayaran'], ['Gratis'])): ?>
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Gratis</span>
                                    <?php else: ?>
                                        <select class="form-select form-select-sm select-status mx-auto fw-bold rounded-pill shadow-sm" style="width:130px; text-align-last: center; cursor: pointer;" 
                                                data-id="<?= $row['id'] ?>" data-field="status_pembayaran" data-old="<?= $row['status_pembayaran'] ?>">
                                            <option class="bg-white text-dark" value="Pending" <?= $row['status_pembayaran'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option class="bg-white text-dark" value="Diterima" <?= $row['status_pembayaran'] == 'Verified' ? 'selected' : '' ?>>Diterima</option>
                                            <option class="bg-white text-dark" value="Ditolak" <?= $row['status_pembayaran'] == 'Rejected' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($row['status_peserta'] == 'Daftar'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1 fw-bold text-uppercase" style="font-size:0.65rem;">AKTIF / ACC</span>
                                    <?php elseif($row['status_peserta'] == 'Gagal'): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-1 fw-bold text-uppercase" style="font-size:0.65rem;">DITOLAK</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold text-uppercase" style="font-size:0.65rem;"><?= $row['status_peserta'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-3">
                                    <a href="<?= base_url('pelatihan/admin/verifikasi_pendaftaran/reset/' . $row['id']) ?>" 
                                       class="btn btn-sm btn-outline-danger fw-bold rounded-pill shadow-sm" style="font-size: 0.65rem;"
                                       onclick="return confirm('Apakah Anda yakin ingin membatalkan semua verifikasi dan mengembalikan ke antrean pending?')">
                                        <i class="fas fa-undo"></i> BATALKAN
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pelatihan -->
<div class="modal fade" id="modalDetailPelatihan" tabindex="-1" aria-labelledby="modalDetailPelatihanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white py-3 rounded-top-4">
                <h6 class="modal-title fw-bold text-uppercase" id="modalDetailPelatihanLabel"><i class="fas fa-chalkboard-teacher me-2"></i> Detail Program Pelatihan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <h5 class="fw-bold text-dark mb-3" id="detNama">-</h5>
                <hr>
                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted d-block">Metode</small>
                        <span class="fw-bold text-dark small" id="detMetode">-</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Program</small>
                        <span class="fw-bold text-dark small" id="detProgram">-</span>
                    </div>

                    <div class="col-6">
                        <small class="text-muted d-block">Jadwal Pelaksanaan</small>
                        <span class="fw-bold text-dark small" id="detJadwal">-</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Kuota</small>
                        <span class="fw-bold text-dark small" id="detKuota">-</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Biaya</small>
                        <span class="fw-bold text-dark small" id="detBiaya">-</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Target Profesi</small>
                        <span class="fw-bold text-dark small" id="detProfesi">-</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">Deskripsi</small>
                        <p class="text-muted small mb-0" id="detDeskripsi">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill fw-bold shadow-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pendaftar -->
<div class="modal fade" id="modalDetailPendaftar" tabindex="-1" aria-labelledby="modalDetailPendaftarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white py-3 rounded-top-4">
                <h6 class="modal-title fw-bold text-uppercase" id="modalDetailPendaftarLabel"><i class="fas fa-user-circle me-2"></i> Profil Pendaftar</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="avatar bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px; font-size: 1.25rem; font-weight: bold;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="pendNama">-</h5>
                    <span class="badge bg-light text-dark border font-monospace" id="pendNip">-</span>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-12">
                        <small class="text-muted d-block"><i class="fas fa-envelope me-1"></i> Email</small>
                        <span class="fw-bold text-dark" id="pendEmail">-</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block"><i class="fas fa-phone me-1"></i> WhatsApp</small>
                        <span class="fw-bold text-dark" id="pendWa">-</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block"><i class="fas fa-user-md me-1"></i> Profesi</small>
                        <span class="fw-bold text-dark" id="pendProfesi">-</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block"><i class="fas fa-hospital me-1"></i> Ruangan/Unit</small>
                        <span class="fw-bold text-dark" id="pendUnit">-</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill fw-bold shadow-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
.rounded-custom { border-radius: 1rem !important; }
.hover-danger:hover {
    color: #ce2127 !important;
}
.me-1-5 { margin-right: 0.35rem !important; }
.toggle-tab-custom {
    color: #64748b !important;
    border: none !important;
    border-bottom: 3px solid transparent !important;
    transition: all 0.2s ease;
    background: transparent !important;
}
.nav-tabs .nav-link.active {
    color: #ce2127 !important;
    border-bottom: 3px solid #ce2127 !important;
    font-weight: 800 !important;
    background-color: #fff !important;
}
.nav-tabs .nav-link:hover:not(.active) {
    color: #212529 !important;
    border-bottom: 3px solid #e2e8f0 !important;
}
.page-item.active .page-link {
    background-color: #ce2127 !important;
    border-color: #ce2127 !important;
    color: #fff !important;
}
.page-link { color: #212529 !important; font-size: 0.75rem; padding: 0.35rem 0.65rem; }
.page-link:hover {
    color: #ce2127 !important;
    background-color: #fff5f5 !important;
    border-color: #dee2e6 !important;
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables
    var dtConfig = {
        pageLength: 10,
        responsive: true,
        dom: '<"mb-3"l>rtip',
        language: { 
            emptyTable: "Tidak ada data verifikasi.", 
            info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data", 
            paginate: { previous: "‹", next: "›" },
            search: "Cari Pendaftar/Pelatihan:"
        }
    };

    $('#tableAntrean').DataTable(dtConfig);
    $('#tableRiwayat').DataTable(dtConfig);

    // Tab adjust DataTable layout
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });

    function updateSelectColor(selectObj) {
        var val = selectObj.value;
        selectObj.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'text-white', 'text-dark', 'border-success', 'border-danger', 'border-warning');
        if (val === 'Diterima') {
            selectObj.classList.add('bg-success', 'text-white', 'border-success');
        } else if (val === 'Ditolak') {
            selectObj.classList.add('bg-danger', 'text-white', 'border-danger');
        } else {
            selectObj.classList.add('bg-warning', 'text-dark', 'border-warning');
        }
    }

    // Dropdown Status Change Handler
    const selects = document.querySelectorAll('.select-status');
    selects.forEach(select => {
        updateSelectColor(select); // Init color
        
        select.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const field = this.getAttribute('data-field');
            const value = this.value;
            const oldValue = this.getAttribute('data-old');
            const self = this;
            
            updateSelectColor(self); // Update immediately for UI responsiveness

            if (value === 'Ditolak') {
                Swal.fire({
                    title: 'Alasan Penolakan',
                    text: 'Tuliskan alasan mengapa pendaftaran ditolak:',
                    input: 'textarea',
                    inputPlaceholder: 'Alasan penolakan...',
                    inputAttributes: {
                        'aria-label': 'Alasan penolakan'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Kirim',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ce2127',
                    preConfirm: (reason) => {
                        if (!reason) {
                            Swal.showValidationMessage('Alasan penolakan harus diisi!');
                        }
                        return reason;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        sendUpdateRequest(id, field, value, result.value, self);
                    } else {
                        // Revert dropdown value
                        self.value = oldValue;
                        updateSelectColor(self);
                    }
                });
            } else {
                sendUpdateRequest(id, field, value, '', self);
            }
        });
    });

    function sendUpdateRequest(id, field, value, reason, element) {
        $.ajax({
            url: '<?= base_url('pelatihan/admin/verifikasi_pendaftaran/update_status') ?>',
            method: 'POST',
            data: {
                id: id,
                field: field,
                value: value,
                reason: reason
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal', response.message, 'error');
                    element.value = element.getAttribute('data-old');
                    updateSelectColor(element);
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                element.value = element.getAttribute('data-old');
                updateSelectColor(element);
            }
        });
    }

    // Pelatihan Detail Popup
    const detailButtons = document.querySelectorAll('.btn-detail-pelatihan');
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('detNama').innerText = this.getAttribute('data-nama');
            document.getElementById('detMetode').innerText = this.getAttribute('data-metode');
            document.getElementById('detProgram').innerText = this.getAttribute('data-program');
            document.getElementById('detJadwal').innerText = this.getAttribute('data-jadwal');
            document.getElementById('detKuota').innerText = this.getAttribute('data-kuota') + ' Mhs';
            document.getElementById('detBiaya').innerText = this.getAttribute('data-biaya');
            document.getElementById('detProfesi').innerText = this.getAttribute('data-profesi') || '-';
            document.getElementById('detDeskripsi').innerText = this.getAttribute('data-deskripsi') || '-';
            
            const modal = new bootstrap.Modal(document.getElementById('modalDetailPelatihan'));
            modal.show();
        });
    });

    // Pendaftar Detail Popup
    const pendaftarButtons = document.querySelectorAll('.btn-detail-pendaftar');
    pendaftarButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('pendNama').innerText = this.getAttribute('data-nama');
            document.getElementById('pendNip').innerText = this.getAttribute('data-nip');
            document.getElementById('pendEmail').innerText = this.getAttribute('data-email');
            document.getElementById('pendWa').innerText = this.getAttribute('data-wa');
            document.getElementById('pendProfesi').innerText = this.getAttribute('data-profesi') || '-';
            document.getElementById('pendUnit').innerText = this.getAttribute('data-unit') || '-';
            
            const modal = new bootstrap.Modal(document.getElementById('modalDetailPendaftar'));
            modal.show();
        });
    });
});
</script>
<?= $this->endSection() ?>
