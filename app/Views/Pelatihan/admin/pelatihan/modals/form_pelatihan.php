<div class="modal fade" id="modalPelatihan" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-dark text-white border-0 p-4">
                <h5 class="modal-title fw-bold" id="modalTitle"><i class="fas fa-plus-circle me-2 text-warning"></i> INPUT MASTER DIKLAT BARU</h5>
                <div class="d-flex align-items-center gap-2">

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-4 p-md-5 bg-light">
                <form id="formPelatihan" method="POST" action="<?= base_url('pelatihan/admin/pelatihan/simpan') ?>" class="needs-validation" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="f_id">
                    <div class="row g-4">
                        <div class="col-md-12"><h6 class="fw-bold text-danger border-bottom pb-2 text-uppercase small" style="letter-spacing: 1px;">Informasi Dasar Program</h6></div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">NAMA PELATIHAN (JUDUL)</label>
                            <input type="text" name="nama" id="f_nama" class="form-control rounded-pill border shadow-sm px-4" placeholder="Contoh: Pelatihan K3 Rumah Sakit" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">TEMA PELATIHAN</label>
                            <input type="text" name="tema" id="f_tema" class="form-control rounded-pill border shadow-sm px-4" placeholder="Contoh: Keselamatan dan Kesehatan Kerja">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">PROGRAM</label>
                            <select name="program" id="f_program" class="form-select rounded-pill border shadow-sm px-4">
                                <option value="Webinar">Webinar</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Bimtek">Bimtek</option>
                                <option value="Pelatihan">Pelatihan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">KATEGORI ILMU</label>
                            <input type="text" name="kategori" id="f_kategori" class="form-control rounded-pill border shadow-sm px-4" placeholder="Kesehatan, IT, dll" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">RANAH PEMENUHAN SKP</label>
                            <select name="ranah_skp" id="f_ranah_skp" class="form-select rounded-pill border shadow-sm px-4" onchange="updateTrainingCategories(this.value)">
                                <?php foreach ($ranah_skp_list ?? [] as $r): ?>
                                <option value="<?= esc($r) ?>"><?= esc($r) ?></option>
                                <?php endforeach; ?>
                                <?php if (empty($ranah_skp_list ?? [])): ?>
                                <option value="Pembelajaran">Pembelajaran (Learning)</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">KATEGORI KEGIATAN SKP</label>
                            <select name="kategori_kegiatan" id="f_kategori_kegiatan" class="form-select rounded-pill border shadow-sm px-4" required>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">BOBOT SKP</label>
                            <input type="number" step="0.1" name="skp" id="f_skp" class="form-control rounded-pill border shadow-sm px-4" placeholder="Contoh: 2.5" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">BIAYA</label>
                            <select name="biaya" id="f_biaya" class="form-select rounded-pill border shadow-sm px-4" onchange="togglePaidFields(this.value)">
                                <option value="Gratis">Gratis</option>
                                <option value="Berbayar">Berbayar</option>
                            </select>
                        </div>
                        <div id="paid_fields" class="col-md-9 d-none">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">BANK</label>
                                    <input type="text" name="nama_bank" id="f_nama_bank" class="form-control rounded-pill border shadow-sm px-4" placeholder="Mandiri/BCA/dll">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">NO. REKENING</label>
                                    <input type="text" name="no_rekening" id="f_rekening" class="form-control rounded-pill border shadow-sm px-4" placeholder="123-45-678">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">A.N REKENING</label>
                                    <input type="text" name="atas_nama" id="f_atas_nama" class="form-control rounded-pill border shadow-sm px-4" placeholder="Nama Pemilik">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark">NOMINAL (RP)</label>
                                    <input type="number" name="biaya_nominal" id="f_biaya_nominal" class="form-control rounded-pill border shadow-sm px-4" placeholder="150000" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">LEVEL</label>
                            <select name="level" id="f_level" class="form-select rounded-pill border shadow-sm px-4">
                                <option value="Pemula">Pemula</option>
                                <option value="Menengah">Menengah</option>
                                <option value="Lanjut">Lanjut</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">JPL</label>
                            <input type="number" name="jpl" id="f_jpl" class="form-control rounded-pill border shadow-sm px-4" placeholder="Contoh: 10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">CAKUPAN</label>
                            <select name="cakupan" id="f_cakupan" class="form-select rounded-pill border shadow-sm px-4">
                                <option value="Lokal">Lokal</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-4"><h6 class="fw-bold text-danger border-bottom pb-2 text-uppercase small">Metode & Penyelenggaraan</h6></div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">MEKANISME</label>
                            <select name="mekanisme" id="f_mekanisme" class="form-select rounded-pill border shadow-sm px-4" onchange="toggleClosedFields(this.value)">
                                <option value="Terbuka">Terbuka (Umum)</option>
                                <option value="Tertutup">Tertutup (Khusus)</option>
                            </select>
                        </div>
                        <div id="closed_fields" class="col-md-6 d-none">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">TARGET KHUSUS (PROFESI)</label>
                                    <select name="target_khusus_profesi[]" id="f_target_khusus_profesi" class="form-select border shadow-sm" multiple="multiple">
                                        <option value="Semua Profesi">Semua Profesi</option>
                                        <?php if(isset($profesi)): foreach($profesi as $prf): ?>
                                            <option value="<?= $prf['nama_profesi'] ?>"><?= $prf['nama_profesi'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark">TARGET KHUSUS (UNIT KERJA)</label>
                                    <select name="target_khusus_unit[]" id="f_target_khusus_unit" class="form-select border shadow-sm" multiple="multiple">
                                        <option value="Semua Unit Kerja">Semua Unit Kerja</option>
                                        <?php if(isset($unit_kerja)): foreach($unit_kerja as $uk): ?>
                                            <option value="<?= $uk['nama_unit'] ?>"><?= $uk['nama_unit'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">METODE</label>
                            <select name="metode" id="f_metode" class="form-select rounded-pill border shadow-sm px-4">
                                <option value="Online">Online</option>
                                <option value="Offline / Clasical">Offline / Clasical</option>
                                <option value="Blended / Hybrid">Blended / Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">PILIH NARASUMBER</label>
                                <a href="javascript:void(0)" onclick="showModalTambahNarasumber()" class="text-primary text-decoration-none small" style="font-size: 0.7rem;"><i class="fas fa-plus-circle me-1"></i>Buat Data Baru</a>
                            </div>
                            <select name="narasumber[]" id="f_narasumber" class="form-select border shadow-sm" multiple="multiple" required>
                                <?php if(!empty($master_narasumber_list)): foreach($master_narasumber_list as $mn): ?>
                                    <option value="<?= $mn['id'] ?>"><?= htmlspecialchars(($mn['gelar_depan'] ? $mn['gelar_depan'].' ' : '').$mn['nama_pejabat'].($mn['gelar_belakang'] ? ', '.$mn['gelar_belakang'] : '')) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                            <div class="mt-1 px-1 bg-light border rounded text-muted" style="font-size: 0.65rem; padding-top: 4px; padding-bottom: 4px;">
                                <i class="fas fa-hand-pointer text-primary ms-1 me-1"></i> Klik area kotak di atas untuk mencari dan menambah nama ke daftar.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">PILIH PENYELENGGARA</label>
                                <a href="javascript:void(0)" onclick="showModalTambahPenyelenggara()" class="text-primary text-decoration-none small" style="font-size: 0.7rem;"><i class="fas fa-plus-circle me-1"></i>Buat Data Baru</a>
                            </div>
                            <select name="penyelenggara[]" id="f_penyelenggara" class="form-select border shadow-sm" multiple="multiple" required>
                                <?php if(!empty($master_penyelenggara_list)): foreach($master_penyelenggara_list as $mp): ?>
                                    <option value="<?= $mp['id'] ?>"><?= htmlspecialchars($mp['nama']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                            <div class="mt-1 px-1 bg-light border rounded text-muted" style="font-size: 0.65rem; padding-top: 4px; padding-bottom: 4px;">
                                <i class="fas fa-hand-pointer text-primary ms-1 me-1"></i> Klik area kotak di atas untuk mencari dan menambah nama ke daftar.
                            </div>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-bold text-dark">KONTAK PENYEDIA</label>
                            <input type="text" name="kontak" id="f_kontak" class="form-control rounded-pill border shadow-sm px-4" placeholder="No Telp / WA" required>
                        </div>
                        
                        <div class="col-md-12 mt-4"><h6 class="fw-bold text-danger border-bottom pb-2 text-uppercase small">Jadwal & Kuota</h6></div>
                        
                        <div class="col-md-12">
                            <div class="p-3 bg-white border rounded-lg shadow-sm mb-3">
                                <div class="small fw-bold text-muted mb-3 text-uppercase"><i class="fas fa-calendar-check me-2 text-danger"></i> Masa Pendaftaran (Registrasi)</div>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Pendaftaran Buka</label>
                                        <input type="date" name="reg_buka_tgl" id="f_reg_buka_tgl" class="form-control rounded-pill border shadow-sm px-3">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Waktu Buka</label>
                                        <input type="time" name="reg_buka_jam" id="f_reg_buka_jam" class="form-control rounded-pill border shadow-sm px-3">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Pendaftaran Tutup</label>
                                        <input type="date" name="reg_tutup_tgl" id="f_reg_tutup_tgl" class="form-control rounded-pill border shadow-sm px-3">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Waktu Tutup</label>
                                        <input type="time" name="reg_tutup_jam" id="f_reg_tutup_jam" class="form-control rounded-pill border shadow-sm px-3">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">TGL MULAI PELAKSANAAN</label>
                            <input type="date" name="jadwal_mulai" id="f_jadwal_mulai" class="form-control rounded-pill border shadow-sm px-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">JAM MULAI</label>
                            <input type="time" name="jam_mulai" id="f_jam_mulai" class="form-control rounded-pill border shadow-sm px-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">TGL BERAKHIR</label>
                            <input type="date" name="jadwal_selesai" id="f_jadwal_selesai" class="form-control rounded-pill border shadow-sm px-3" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">JAM BERAKHIR</label>
                            <input type="time" name="jam_selesai" id="f_jam_selesai" class="form-control rounded-pill border shadow-sm px-3" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">KUOTA</label>
                            <input type="number" name="kuota" id="f_kuota" class="form-control rounded-pill border shadow-sm px-4 text-center" placeholder="Jumlah" min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold text-dark mb-0">TARGET PROFESI</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAllProfesi">
                                    <label class="form-check-label small fw-bold text-secondary" for="checkAllProfesi">Pilih Semua</label>
                                </div>
                            </div>
                            <div class="border rounded shadow-sm p-3 bg-white" style="max-height: 200px; overflow-y: auto;">
                                <div class="row">
                                <?php if(isset($profesi)): foreach($profesi as $index => $prf): ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input target-profesi-checkbox" type="checkbox" name="target_profesi[]" value="<?= $prf['nama_profesi'] ?>" id="profesi_<?= $index ?>">
                                            <label class="form-check-label small" for="profesi_<?= $index ?>">
                                                <?= $prf['nama_profesi'] ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; endif; ?>
                                </div>
                            </div>
                            <!-- Input hidden to enforce requirement visually if needed, though checkboxes can be validated manually -->
                            <div class="invalid-feedback d-block" id="profesi-error" style="display: none;">Harap pilih minimal satu target profesi.</div>
                        </div>

                        <div class="col-md-12 mt-4"><h6 class="fw-bold text-danger border-bottom pb-2 text-uppercase small">Deskripsi & Konten</h6></div>
                        
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">PENGUMUMAN (OPSIONAL)</label>
                            <input type="text" name="pengumuman" id="f_pengumuman" class="form-control rounded-pill border shadow-sm px-4" placeholder="Informasi penting">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">TUJUAN PELATIHAN</label>
                            <textarea name="tujuan" id="f_tujuan" class="form-control border shadow-sm p-3" rows="2" style="border-radius: 15px;" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">DESKRIPSI LENGKAP</label>
                            <textarea name="deskripsi" id="f_deskripsi" class="form-control border shadow-sm p-3" rows="3" style="border-radius: 15px;" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">KOMPETENSI (PISAHKAN DENGAN KOMA)</label>
                            <input type="text" name="kompetensi" id="f_kompetensi" class="form-control rounded-pill border shadow-sm px-4" placeholder="Leadership, Komunikasi Medis" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">GAMBAR PELATIHAN (OPSIONAL)</label>
                            <input type="file" name="gambar_pelatihan" id="f_gambar_pelatihan" class="form-control form-control-file rounded-pill border shadow-sm px-4 py-2" accept="image/*">
                            <small class="text-muted d-block mt-1 ms-3">Format yang didukung: JPG, JPEG, PNG, WEBP. Maksimal 8MB. Biarkan kosong jika tidak ingin mengubah gambar.</small>
                            <small id="gambar_pelatihan_info" class="text-danger fw-bold d-none mt-1 ms-3"></small>
                        </div>
                    </div>
                    <div class="mt-5 text-center">
                        <button type="button" class="btn btn-dark rounded-pill px-5 py-2 fw-bold me-2" data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-5 py-2 fw-bold shadow">SIMPAN DATA PELATIHAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quick Tambah Narasumber -->
<div class="modal fade" id="modalQuickNarasumber" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-success text-white border-0">
                <h6 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i> TAMBAH NARASUMBER BARU</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Status</label>
                        <select id="q_status" class="form-select form-select-sm rounded-pill border px-3">
                            <option value="Narasumber">Narasumber</option>
                            <option value="Pejabat">Pejabat TTD</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="q_nama" class="form-control form-control-sm rounded-pill px-3" placeholder="Contoh: Dr. Budi Santoso, Sp.A" required>
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Gelar Depan</label>
                        <input type="text" id="q_gelar_depan" class="form-control form-control-sm rounded-pill px-3" placeholder="Dr.">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Gelar Belakang</label>
                        <input type="text" id="q_gelar_belakang" class="form-control form-control-sm rounded-pill px-3" placeholder="M.Kes">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Pendidikan</label>
                        <input type="text" id="q_pendidikan" class="form-control form-control-sm rounded-pill px-3" placeholder="S.Ked, Sp.PD">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Keahlian</label>
                        <input type="text" id="q_keahlian" class="form-control form-control-sm rounded-pill px-3" placeholder="Penyakit Dalam, Kardiologi">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Atas Nama Pejabat (a.n)</label>
                        <input type="text" id="q_an" class="form-control form-control-sm rounded-pill px-3" placeholder="Contoh: a.n Direktur">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Jabatan / Kedudukan</label>
                        <input type="text" id="q_jabatan" class="form-control form-control-sm rounded-pill px-3" placeholder="Contoh: Direktur RSUD">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">NIP Pejabat</label>
                        <input type="text" id="q_nip" class="form-control form-control-sm rounded-pill px-3" placeholder="Contoh: 19690124XXXXXX">
                    </div>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Kontak / No. Telp</label>
                        <input type="text" id="q_kontak" class="form-control form-control-sm rounded-pill px-3" placeholder="08xxx">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" id="q_email" class="form-control form-control-sm rounded-pill px-3" placeholder="email@contoh.com">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Riwayat / Bio</label>
                    <textarea id="q_riwayat" class="form-control form-control-sm border p-2" rows="2" placeholder="Riwayat singkat, pengalaman, publikasi, dll." style="border-radius: 15px;"></textarea>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Foto Profil</label>
                        <input type="file" id="q_foto" class="form-control form-control-sm rounded-pill border px-3" accept="image/*">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Upload TTD (PNG Transparan)</label>
                        <input type="file" id="q_ttd" class="form-control form-control-sm rounded-pill border px-3" accept=".png">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white py-2">
                <button type="button" class="btn btn-light btn-sm rounded-pill fw-bold" data-bs-dismiss="modal">BATAL</button>
                <button type="button" class="btn btn-success btn-sm rounded-pill fw-bold" onclick="saveQuickNarasumber()"><i class="fas fa-check me-1"></i> SIMPAN</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Quick Tambah Penyelenggara -->
<div class="modal fade" id="modalQuickPenyelenggara" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h6 class="modal-title fw-bold"><i class="fas fa-building me-2"></i> TAMBAH PENYELENGGARA BARU</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <div class="mb-2">
                    <label class="form-label small fw-bold">Nama Penyelenggara <span class="text-danger">*</span></label>
                    <input type="text" id="q_nama_penyelenggara" class="form-control form-control-sm rounded-pill" placeholder="Nama instansi/organisasi" required>
                </div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Fokus Bidang</label>
                        <input type="text" id="q_fokus_bidang" class="form-control form-control-sm rounded-pill" placeholder="Kesehatan">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Kontak</label>
                        <input type="text" id="q_kontak_penyelenggara" class="form-control form-control-sm rounded-pill" placeholder="021-xxxx">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white py-2">
                <button type="button" class="btn btn-light btn-sm rounded-pill fw-bold" data-bs-dismiss="modal">BATAL</button>
                <button type="button" class="btn btn-primary btn-sm rounded-pill fw-bold" onclick="saveQuickPenyelenggara()"><i class="fas fa-check me-1"></i> SIMPAN</button>
            </div>
        </div>
    </div>
</div>

<script>
function showModalTambahNarasumber() {
    document.getElementById('q_nama').value = '';
    document.getElementById('q_gelar_depan').value = '';
    document.getElementById('q_gelar_belakang').value = '';
    document.getElementById('q_pendidikan').value = '';
    document.getElementById('q_keahlian').value = '';
    document.getElementById('q_kontak').value = '';
    document.getElementById('q_email').value = '';
    document.getElementById('q_jabatan').value = '';
    document.getElementById('q_nip').value = '';
    document.getElementById('q_an').value = '';
    document.getElementById('q_riwayat').value = '';
    document.getElementById('q_status').value = 'Narasumber';
    document.getElementById('q_foto').value = '';
    document.getElementById('q_ttd').value = '';
    new bootstrap.Modal(document.getElementById('modalQuickNarasumber')).show();
}

function saveQuickNarasumber() {
    var nama = document.getElementById('q_nama').value.trim();
    if (!nama) { alert('Nama harus diisi!'); return; }

    var fd = new FormData();
    fd.append('status', document.getElementById('q_status').value);
    fd.append('nama_pejabat', nama);
    fd.append('gelar_depan', document.getElementById('q_gelar_depan').value);
    fd.append('gelar_belakang', document.getElementById('q_gelar_belakang').value);
    fd.append('pendidikan', document.getElementById('q_pendidikan').value);
    fd.append('keahlian', document.getElementById('q_keahlian').value);
    fd.append('kontak', document.getElementById('q_kontak').value);
    fd.append('email', document.getElementById('q_email').value);
    fd.append('jabatan', document.getElementById('q_jabatan').value);
    fd.append('nip_pejabat', document.getElementById('q_nip').value);
    fd.append('an_pejabat', document.getElementById('q_an').value);
    fd.append('riwayat', document.getElementById('q_riwayat').value);

    var fotoFile = document.getElementById('q_foto').files[0];
    if (fotoFile) fd.append('foto', fotoFile);
    var ttdFile = document.getElementById('q_ttd').files[0];
    if (ttdFile) fd.append('ttd_image', ttdFile);

    fetch('<?= base_url('pelatihan/admin/master/simpan_narasumber_ajax') ?>', {
        method: 'POST',
        body: fd
    }).then(r => r.json()).then(res => {
        if (res.status === 'success') {
            var sel = document.getElementById('f_narasumber');
            var opt = document.createElement('option');
            opt.value = res.data.id;
            var displayName = (res.data.gelar_depan ? res.data.gelar_depan + ' ' : '') + res.data.nama_pejabat + (res.data.gelar_belakang ? ', ' + res.data.gelar_belakang : '');
            opt.text = displayName;
            opt.selected = true;
            sel.appendChild(opt);
            bootstrap.Modal.getInstance(document.getElementById('modalQuickNarasumber')).hide();
        }
    });
}

function showModalTambahPenyelenggara() {
    document.getElementById('q_nama_penyelenggara').value = '';
    document.getElementById('q_fokus_bidang').value = '';
    document.getElementById('q_kontak_penyelenggara').value = '';
    new bootstrap.Modal(document.getElementById('modalQuickPenyelenggara')).show();
}

function saveQuickPenyelenggara() {
    var nama = document.getElementById('q_nama_penyelenggara').value.trim();
    if (!nama) { alert('Nama harus diisi!'); return; }

    var fd = new FormData();
    fd.append('nama', nama);
    fd.append('fokus_bidang', document.getElementById('q_fokus_bidang').value);
    fd.append('kontak', document.getElementById('q_kontak_penyelenggara').value);

    fetch('<?= base_url('pelatihan/admin/master/simpan_penyelenggara_ajax') ?>', {
        method: 'POST',
        body: fd
    }).then(r => r.json()).then(res => {
        if (res.status === 'success') {
            var sel = document.getElementById('f_penyelenggara');
            var opt = document.createElement('option');
            opt.value = res.data.id;
            opt.text = res.data.nama;
            opt.selected = true;
            sel.appendChild(opt);
            bootstrap.Modal.getInstance(document.getElementById('modalQuickPenyelenggara')).hide();
        }
    });
}
</script>
