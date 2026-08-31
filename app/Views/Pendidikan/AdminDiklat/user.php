<?= $this->include('Pendidikan/AdminDiklat/layout/header') ?>
<?= $this->include('Pendidikan/AdminDiklat/layout/sidebar') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-dark">Manajemen Mahasiswa & Akun</h5>
    <div class="d-flex gap-2">
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn <?= ($tab === 'explorer') ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="location.href='<?= base_url('pendidikan/admin/diklat/user?tab=explorer') ?>'">
                <i class="fas fa-users me-1"></i> Mahasiswa
            </button>
            <button type="button" class="btn <?= ($tab === 'ci') ? 'btn-primary' : 'btn-outline-primary' ?>" onclick="location.href='<?= base_url('pendidikan/admin/diklat/user?tab=ci') ?>'">
                <i class="fas fa-user-check me-1"></i> Akun CI
            </button>
        </div>
    </div>
</div>

<?php if ($tab === 'ci'): ?>
<div class="card p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Kontak / Kredensial</th>
                    <th>Role</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($userList)): ?>
                    <?php foreach ($userList as $u): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                    <span class="fw-bold text-primary"><?= strtoupper(substr($u['nama_lengkap'] ?? '-', 0, 1)) ?></span>
                                </div>
                                <div>
                                    <span class="fw-semibold"><?= $u['nama_lengkap'] ?? '-' ?></span>
                                    <br><small class="text-muted"><?= $u['email'] ?? '' ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small class="text-muted"><i class="fas fa-envelope me-1"></i><?= $u['email'] ?? '-' ?></small>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info">CI</span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editCI(<?= htmlspecialchars(json_encode($u)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteCI(<?= $u['id'] ?>, '<?= esc($u['nama_lengkap'] ?? '') ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                            Tidak ada data pengguna CI
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit CI -->
<div class="modal fade" id="modalEditCI" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="formEditCI" onsubmit="saveCI(event)">
            <div class="modal-header bg-primary text-white">
                <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Identitas CI</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_ci_id" name="id">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" id="edit_ci_nama" name="nama_lengkap" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">NIP</label>
                    <input type="text" class="form-control" id="edit_ci_nip" name="nip">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" id="edit_ci_email" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">No. Telepon</label>
                    <input type="text" class="form-control" id="edit_ci_telp" name="nomor_telepon">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Profesi</label>
                    <select class="form-select" id="edit_ci_profesi" name="id_profesi">
                        <option value="">Pilih Profesi</option>
                        <?php foreach($profesiList as $p): ?>
                        <option value="<?= $p['id_profesi'] ?>"><?= esc($p['nama_profesi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Unit Kerja</label>
                    <select class="form-select" id="edit_ci_unit" name="id_unit_kerja">
                        <option value="">Pilih Unit Kerja</option>
                        <?php foreach($unitKerjaList as $u): ?>
                        <option value="<?= $u['id_unit_kerja'] ?>"><?= esc($u['nama_unit']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSaveCI">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCI(data) {
    document.getElementById('edit_ci_id').value = data.id || '';
    document.getElementById('edit_ci_nama').value = data.nama_lengkap || '';
    document.getElementById('edit_ci_nip').value = data.nip || '';
    document.getElementById('edit_ci_email').value = data.email || '';
    document.getElementById('edit_ci_telp').value = data.nomor_telepon || '';
    document.getElementById('edit_ci_profesi').value = data.id_profesi || '';
    document.getElementById('edit_ci_unit').value = data.id_unit_kerja || '';
    
    new bootstrap.Modal(document.getElementById('modalEditCI')).show();
}

function saveCI(e) {
    e.preventDefault();
    const id = document.getElementById('edit_ci_id').value;
    const form = document.getElementById('formEditCI');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    const btn = document.getElementById('btnSaveCI');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled = true;

    fetch('<?= base_url('pendidikan/admin/diklat/api/ci/update') ?>/' + id, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if (res.status === 200 || res.success || res.message === 'Data berhasil diupdate') {
            Swal.fire('Berhasil', 'Data CI berhasil diperbarui.', 'success').then(() => location.reload());
        } else {
            Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
    })
    .finally(() => {
        btn.innerHTML = 'Simpan';
        btn.disabled = false;
    });
}

function deleteCI(id, nama) {
    Swal.fire({
        title: 'Apakah anda benar ingin menghapus user ini?',
        html: `User <b>${nama}</b> akan dihapus secara permanen.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= base_url('pendidikan/admin/diklat/api/ci/delete') ?>/' + id)
            .then(res => res.json())
            .then(res => {
                if (res.status === 200 || res.success || res.message === 'Data berhasil dihapus') {
                    Swal.fire('Terhapus!', 'User CI berhasil dihapus.', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal', res.message || 'Terjadi kesalahan.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
            });
        }
    });
}
</script>

<?php else: ?>
<div class="card p-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center gap-3">
            <span class="fw-semibold">Daftar Mahasiswa</span>
            <span class="badge bg-primary bg-opacity-10 text-primary"><?= count($mahasiswaList) ?> mahasiswa</span>
        </div>
        <div>
            <input type="text" class="form-control form-control-sm" id="searchMahasiswa" placeholder="Cari mahasiswa..." style="width:250px;" onkeyup="filterMahasiswa()">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="mahasiswaTable">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Institusi</th>
                    <th>Program Studi</th>
                    <th>Jenjang</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($mahasiswaList)): ?>
                    <?php foreach ($mahasiswaList as $m): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <span class="fw-bold text-primary"><?= strtoupper(substr($m['nama_lengkap'] ?? '-', 0, 1)) ?></span>
                                </div>
                                <span class="fw-semibold"><?= $m['nama_lengkap'] ?? '-' ?></span>
                            </div>
                        </td>
                        <td><small class="text-muted"><?= $m['nim'] ?? '-' ?></small></td>
                        <td><small><?= $m['nama_institusi'] ?? '-' ?></small></td>
                        <td><small><?= $m['program_studi'] ?? '-' ?></small></td>
                        <td><small><?= $m['jenjang'] ?? '-' ?></small></td>
                        <td>
                            <?php $status = $m['status'] ?? ''; ?>
                            <?php if ($status === 'Disetujui' || $status === 'Aktif' || $status === '1'): ?>
                                <span class="badge badge-disetujui">Aktif</span>
                            <?php elseif ($status === 'Pending'): ?>
                                <span class="badge badge-menunggu">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= $status ?: '-' ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $payStatus = $m['payment_status'] ?? 'Belum Invoice'; ?>
                            <?php if ($payStatus === 'Lunas'): ?>
                                <span class="badge badge-disetujui">Lunas</span>
                            <?php elseif ($payStatus === 'Menunggu Verifikasi'): ?>
                                <span class="badge badge-menunggu">Menunggu</span>
                            <?php elseif ($payStatus === 'Ditolak'): ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php elseif ($payStatus === 'Belum Bayar'): ?>
                                <span class="badge bg-warning text-dark">Belum Bayar</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Belum Invoice</span>
                            <?php endif; ?>
                            <?php if (($m['nominal'] ?? 0) > 0): ?>
                                <br><small class="text-muted">Rp <?= number_format($m['nominal'] ?? 0, 0, ',', '.') ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-info me-1" onclick="kelolaPembayaran(<?= htmlspecialchars(json_encode($m)) ?>)">
                                <i class="fas fa-file-invoice"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning me-1" onclick="editMahasiswa(<?= htmlspecialchars(json_encode($m)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteMahasiswa(<?= $m['id'] ?>, '<?= $m['nama_lengkap'] ?? '' ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                            Tidak ada data mahasiswa
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h6 class="modal-title fw-bold"><i class="fas fa-file-invoice me-2"></i>Kelola Pembayaran</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="payMhsId">
                <div id="payInfo" class="mb-3 p-3 bg-light rounded border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold" id="payMhsNama"></span>
                        <span id="payStatusBadge"></span>
                    </div>
                    <small class="text-muted" id="payMhsNim"></small>
                </div>

                <!-- Upload Invoice -->
                <div class="mb-3 p-3 border rounded">
                    <p class="fw-semibold mb-2"><i class="fas fa-upload me-1"></i> Upload Invoice</p>
                    <form id="uploadInvoiceForm" enctype="multipart/form-data">
                        <div class="mb-2">
                            <label class="form-label">Nominal</label>
                            <input type="text" class="form-control" id="payNominal" placeholder="Rp" oninput="formatNominalInput(this)">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">File PDF</label>
                            <input type="file" class="form-control" id="payInvoiceFile" accept=".pdf">
                        </div>
                        <button type="submit" class="btn btn-info btn-sm text-white"><i class="fas fa-cloud-upload-alt me-1"></i> Upload</button>
                        <small class="text-muted d-block mt-1">Kosongkan nominal jika hanya upload file, atau sebaliknya</small>
                    </form>
                    <div id="payInvoiceInfo" class="mt-2 d-none">
                        <small class="text-muted">Invoice: <a href="#" id="payInvoiceLink" target="_blank">Lihat Invoice</a></small>
                    </div>
                </div>

                <!-- Verifikasi -->
                <div class="mb-2 p-3 border rounded">
                    <p class="fw-semibold mb-2"><i class="fas fa-check-circle me-1"></i> Verifikasi Pembayaran</p>
                    <div id="payVerifikasiContent">
                        <p class="text-muted small mb-2">Belum ada bukti bayar dari institusi</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Mahasiswa Modal -->
<div class="modal fade" id="editMahasiswaModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h6 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Mahasiswa</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMahasiswaForm">
                <div class="modal-body">
                    <input type="hidden" id="editMhsId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="editMhsNama" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIM</label>
                            <input type="text" class="form-control" id="editMhsNim" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-control" id="editMhsJk">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenjang</label>
                            <input type="text" class="form-control" id="editMhsJenjang" placeholder="D3/S1/Profesi">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Program Studi</label>
                            <input type="text" class="form-control" id="editMhsProdi">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Semester</label>
                            <input type="number" class="form-control" id="editMhsSemester">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. HP</label>
                            <input type="text" class="form-control" id="editMhsHp">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="editMhsEmail">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="editMhsTglLahir">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-control" id="editMhsStatus">
                                <option value="Pending">Pending</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function filterMahasiswa() {
    var q = document.getElementById('searchMahasiswa').value.toLowerCase();
    var rows = document.querySelectorAll('#mahasiswaTable tbody tr');
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
    });
}

function editMahasiswa(m) {
    $('#editMhsId').val(m.id);
    $('#editMhsNama').val(m.nama_lengkap || '');
    $('#editMhsNim').val(m.nim || '');
    $('#editMhsJk').val(m.jenis_kelamin || 'L');
    $('#editMhsJenjang').val(m.jenjang || '');
    $('#editMhsProdi').val(m.program_studi || '');
    $('#editMhsSemester').val(m.semester || '');
    $('#editMhsHp').val(m.no_hp || '');
    $('#editMhsEmail').val(m.email || '');
    $('#editMhsTglLahir').val(m.tanggal_lahir || '');
    $('#editMhsStatus').val(m.status || 'Pending');
    $('#editMahasiswaModal').modal('show');
}

$('#editMahasiswaForm').submit(function(e) {
    e.preventDefault();
    var id = $('#editMhsId').val();
    var data = {
        nama_lengkap: $('#editMhsNama').val(),
        nim: $('#editMhsNim').val(),
        jenis_kelamin: $('#editMhsJk').val(),
        jenjang: $('#editMhsJenjang').val() || null,
        program_studi: $('#editMhsProdi').val() || null,
        semester: $('#editMhsSemester').val() || null,
        no_hp: $('#editMhsHp').val() || null,
        email: $('#editMhsEmail').val() || null,
        tanggal_lahir: $('#editMhsTglLahir').val() || null,
        status: $('#editMhsStatus').val()
    };

    $.ajax({
        url: '<?= base_url('pendidikan/admin/diklat/api/mahasiswa/update') ?>/' + id,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(res) {
            if (res.success) {
                $('#editMahasiswaModal').modal('hide');
                location.reload();
            } else {
                alert(res.message || 'Gagal menyimpan');
            }
        },
        error: function(xhr) {
            alert('Gagal: ' + (xhr.responseJSON?.message || 'Server error'));
        }
    });
});

function deleteMahasiswa(id, nama) {
    if (!confirm('Hapus mahasiswa "' + nama + '"? Tindakan ini tidak dapat dibatalkan.')) return;
    $.ajax({
        url: '<?= base_url('pendidikan/admin/diklat/api/mahasiswa/delete') ?>/' + id,
        method: 'POST',
        success: function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.message || 'Gagal menghapus');
            }
        },
        error: function(xhr) {
            alert('Gagal: ' + (xhr.responseJSON?.message || 'Server error'));
        }
    });
}

// --- Payment Management ---
function kelolaPembayaran(m) {
    $('#payMhsId').val(m.id);
    $('#payMhsNama').text(m.nama_lengkap || '-');
    $('#payMhsNim').text(m.nim || '');

    var payStatus = m.payment_status || 'Belum Invoice';
    var badgeHtml = '';
    if (payStatus === 'Lunas') {
        badgeHtml = '<span class="badge badge-disetujui">Lunas</span>';
    } else if (payStatus === 'Menunggu Verifikasi') {
        badgeHtml = '<span class="badge badge-menunggu">Menunggu Verifikasi</span>';
    } else if (payStatus === 'Ditolak') {
        badgeHtml = '<span class="badge bg-danger">Ditolak</span>';
    } else if (payStatus === 'Belum Bayar') {
        badgeHtml = '<span class="badge bg-warning text-dark">Belum Bayar</span>';
    } else {
        badgeHtml = '<span class="badge bg-secondary">Belum Invoice</span>';
    }
    $('#payStatusBadge').html(badgeHtml);

    // Nominal
    var nominalVal = parseFloat(m.nominal) || 0;
    if (nominalVal > 0) {
        $('#payNominal').val(formatRupiah(nominalVal));
    } else {
        $('#payNominal').val('');
    }

    // Invoice info
    if (m.invoice_file) {
        $('#payInvoiceInfo').removeClass('d-none');
        $('#payInvoiceLink').attr('href', '<?= base_url('uploads/invoices') ?>/' + m.invoice_file);
    } else {
        $('#payInvoiceInfo').addClass('d-none');
    }

    // Verifikasi section
    var verifHtml = '';
    if (m.file_bukti_bayar) {
        verifHtml += '<div class="d-flex align-items-center justify-content-between mb-2">';
        verifHtml += '<div><small class="text-muted d-block">Bukti bayar dari institusi</small>';
        verifHtml += '<a href="<?= base_url('pendidikan/admin/diklat/api/mahasiswa/bukti-bayar') ?>/' + m.id + '" target="_blank" class="fw-semibold"><i class="fas fa-file-pdf me-1"></i> Lihat Bukti Bayar</a></div>';
        verifHtml += '</div>';
        if (payStatus === 'Menunggu Verifikasi') {
            verifHtml += '<div class="d-flex gap-2 mt-2">';
            verifHtml += '<button class="btn btn-success btn-sm flex-fill" onclick="verifikasiPembayaran(' + m.id + ', \'Lunas\')"><i class="fas fa-check me-1"></i> Setujui</button>';
            verifHtml += '<button class="btn btn-danger btn-sm flex-fill" onclick="verifikasiPembayaran(' + m.id + ', \'Ditolak\')"><i class="fas fa-times me-1"></i> Tolak</button>';
            verifHtml += '</div>';
        }
        if (payStatus === 'Ditolak' && m.alasan_penolakan) {
            verifHtml += '<div class="alert alert-danger mt-2 mb-0 py-2 px-3"><small class="fw-semibold"><i class="fas fa-exclamation-circle me-1"></i> Alasan Penolakan:</small><br><small>' + m.alasan_penolakan + '</small></div>';
        }
    } else {
        verifHtml += '<p class="text-muted small mb-0">Belum ada bukti bayar dari institusi</p>';
    }
    $('#payVerifikasiContent').html(verifHtml);

    $('#paymentModal').modal('show');
}

function formatRupiah(angka) {
    if (!angka && angka !== 0) return '';
    var parts = angka.toString().split('.');
    var intPart = parts[0];
    var reverse = intPart.split('').reverse().join('');
    var ribuan = reverse.match(/\d{1,3}/g);
    if (ribuan) {
        intPart = ribuan.join('.').split('').reverse().join('');
    }
    return parts[1] ? intPart + ',' + parts[1] : intPart;
}

function formatNominalInput(el) {
    var val = el.value.replace(/[^\d,]/g, '').replace(/,.*$/, '');
    if (val) {
        var num = parseInt(val, 10);
        if (!isNaN(num)) {
            el.value = formatRupiah(num);
        }
    }
}

$('#uploadInvoiceForm').submit(function(e) {
    e.preventDefault();
    var id = $('#payMhsId').val();
    var formData = new FormData();
    var file = $('#payInvoiceFile')[0].files[0];
    var nominal = $('#payNominal').val().replace(/\./g, '');

    if (!file && !nominal) {
        alert('Pilih file PDF atau isi nominal');
        return;
    }

    if (file) {
        if (file.type !== 'application/pdf') {
            alert('File harus berupa PDF');
            return;
        }
        formData.append('invoice_file', file);
    }
    if (nominal) {
        formData.append('nominal', parseInt(nominal, 10));
    }

    $.ajax({
        url: '<?= base_url('pendidikan/admin/diklat/api/mahasiswa/upload-invoice') ?>/' + id,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                location.reload();
            } else {
                alert(res.message || 'Gagal upload');
            }
        },
        error: function(xhr) {
            alert('Gagal: ' + (xhr.responseJSON?.message || 'Server error'));
        }
    });
});

function verifikasiPembayaran(id, status) {
    $('#paymentModal').modal('hide');

    setTimeout(function() {
    if (status === 'Lunas') {
        Swal.fire({
            title: 'Setujui Pembayaran?',
            text: 'Konfirmasi bahwa pembayaran ini telah diterima.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#198754'
        }).then((result) => {
            if (result.isConfirmed) {
                kirimVerifikasi(id, status, '');
            }
        });
    } else {
        Swal.fire({
            title: 'Tolak Pembayaran',
            text: 'Tuliskan alasan mengapa pembayaran ditolak:',
            input: 'textarea',
            inputPlaceholder: 'Alasan penolakan...',
            inputAttributes: {
                'aria-label': 'Alasan penolakan'
            },
            showCancelButton: true,
            confirmButtonText: 'Tolak',
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
                kirimVerifikasi(id, status, result.value);
            }
        });
    }
    }, 300);
}

function kirimVerifikasi(id, status, alasan) {
    $.ajax({
        url: '<?= base_url('pendidikan/admin/diklat/api/mahasiswa/verifikasi-pembayaran') ?>/' + id,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ status: status, alasan_penolakan: alasan }),
        success: function(res) {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Gagal', res.message || 'Gagal', 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.', 'error');
        }
    });
}
</script>
<?php endif; ?>

<?= $this->include('Pendidikan/AdminDiklat/layout/footer') ?>
