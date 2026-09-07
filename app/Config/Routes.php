<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// Disable auto-routing to prevent conflicts with namespaced groups
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Test Route
$routes->get('run-migrate', 'MigrateController::index');

$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::login');
$routes->post('/auth/check', 'Auth::check');
$routes->get('/logout', 'Auth::logout');

// --- RESTORED: Riset Module ---
// Riset Module - Admin
$routes->group('riset/admin', ['namespace' => 'App\Controllers\Riset\Admin'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('review', 'Review::index');
    $routes->get('review/detail/(:any)', 'Review::detail/$1');
    $routes->get('review/print/(:any)', 'Review::print/$1');
    $routes->post('review/uploadSuratIzin', 'Review::uploadSuratIzin');
    $routes->post('review/approve', 'Review::approve');
    
    $routes->get('izin', 'Izin::index');
    $routes->get('izin/detail/(:any)', 'Izin::detail/$1');
    $routes->get('izin/print/(:any)', 'Izin::print/$1');
    $routes->post('izin/uploadSuratIzin', 'Izin::uploadSuratIzin');
    $routes->post('izin/approve', 'Izin::approve');

    $routes->get('publikasi', 'Publikasi::index');
    $routes->get('publikasi/detail/(:any)', 'Publikasi::detail/$1');
    $routes->get('publikasi/print/(:any)', 'Publikasi::print/$1');
    $routes->post('publikasi/uploadSuratIzin', 'Publikasi::uploadSuratIzin');
    $routes->post('publikasi/approve', 'Publikasi::approve');
    $routes->get('publikasi/buka_arsip/(:any)', 'Publikasi::buka_arsip/$1');

    $routes->get('profil', 'Profil::index');
    $routes->post('profil/update', 'Profil::update');
    
    $routes->get('pengaturan-surat', 'PengaturanSurat::index');
    $routes->post('pengaturan-surat/save', 'PengaturanSurat::save');
});



// Riset Module - Peneliti
$routes->group('riset/peneliti', ['namespace' => 'App\Controllers\Riset\Peneliti'], function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('profil', 'Dashboard::profil');
    $routes->get('profil/edit', 'Dashboard::profil_edit');
    $routes->post('profil/update', 'Dashboard::profil_update');
    $routes->post('profil/update_password', 'Dashboard::update_password');



    // Tahap 2: Pendaftaran Riset (Studi Pendahuluan)
    $routes->get('pengajuan/stupen', function() { return redirect()->to(base_url('riset/peneliti/status')); });
    $routes->get('pengajuan/stupen/baru', 'Pengajuan::stupen_form');
    $routes->get('pengajuan/stupen/detail/(:any)', 'Pengajuan::stupen_detail/$1');
    $routes->post('pengajuan/stupen/submit', 'Pengajuan::stupen_submit');
    $routes->post('pengajuan/stupen/bayar', 'Pengajuan::stupen_bayar');
    $routes->get('pengajuan/stupen/print/(:any)', 'Pengajuan::surat_izin_print/$1');

    // Tahap 3: Izin Penelitian
    $routes->get('pengajuan/izin', function() { return redirect()->to(base_url('riset/peneliti/status')); });
    $routes->get('pengajuan/izin/baru', 'Pengajuan::izin_form');
    $routes->get('pengajuan/izin/detail/(:any)', 'Pengajuan::izin_detail/$1');
    $routes->post('pengajuan/izin/submit', 'Pengajuan::izin_submit');
    $routes->post('pengajuan/izin/bayar', 'Pengajuan::izin_bayar');
    $routes->get('pengajuan/izin/print/(:any)', 'Pengajuan::surat_izin_penelitian_print/$1');

    // Status Pengajuan (Refined: List & Detail)
    $routes->get('status', 'Status::index');
    $routes->get('status/detail/(:any)', 'Status::detail/$1');

    // Hasil & Publikasi
    $routes->get('publikasi', 'Publikasi::index');
    $routes->get('publikasi/form', 'Publikasi::form');
    $routes->get('publikasi/detail', 'Publikasi::detail');
    $routes->post('publikasi/submit', 'Publikasi::submit');
    $routes->post('publikasi/bayar', 'Publikasi::publikasi_bayar');
    $routes->get('publikasi/print_izin/(:any)', 'Publikasi::print_izin/$1');
});

// Public Repository
$routes->addRedirect('repository', 'repository/catalog');
$routes->get('repository/catalog', 'Riset\Publik\Repository::catalog');
$routes->get('repository/detail/(:any)', 'Riset\Publik\Repository::detail/$1');
$routes->post('repository/request', 'Riset\Publik\Repository::request');

// Unified Login Riset
$routes->get('riset/login', 'Riset\Auth::login');
$routes->post('riset/authenticate', 'Riset\Auth::authenticate');
$routes->get('riset/logout', 'Riset\Auth::logout');
$routes->get('riset/register', 'Riset\Auth::register');
$routes->post('riset/register/submit', 'Riset\Auth::register_submit');

// Lupa Password Riset
$routes->get('riset/lupa-password', 'Riset\Auth::forgotPassword');
$routes->post('riset/lupa-password/submit', 'Riset\Auth::forgotPasswordSubmit');


// --- RESTORED: Pendidikan Module ---
$routes->group('pendidikan', function ($routes) {
    $routes->get('/', 'Pendidikan\Auth::login'); // Redirect root to login
    $routes->get('login', 'Pendidikan\Auth::login');
    $routes->post('login/process', 'Pendidikan\Auth::processLogin');
    $routes->get('register', 'Pendidikan\Auth::register');
    $routes->post('register/process', 'Pendidikan\Auth::processRegister');
    $routes->get('logout', 'Pendidikan\Auth::logout');
    $routes->get('forgot-password', 'Pendidikan\Auth::forgotPassword');
    $routes->post('forgot-password/process', 'Pendidikan\Auth::processForgotPassword');

    $routes->group('institusi', ['namespace' => 'App\Controllers\Pendidikan\Institusi', 'filter' => 'pendidikan_auth:institusi'], function ($routes) {
        $routes->get('dashboard', 'Dashboard::index');
        $routes->post('dashboard/update', 'Dashboard::update_profile');

        $routes->get('profil', 'Profil::index');
        $routes->post('profil/update', 'Profil::update');

        $routes->get('pengajuan/create', 'Pengajuan::create');
        $routes->post('pengajuan/store', 'Pengajuan::store');
        $routes->get('pengajuan/status', 'Pengajuan::status');
        $routes->get('pengajuan/detail/(:num)', 'Pengajuan::detail/$1');
        $routes->get('pengajuan/edit/(:num)', 'Pengajuan::edit/$1');
        $routes->post('pengajuan/update/(:num)', 'Pengajuan::update/$1');
        
        $routes->get('mahasiswa', 'Pengajuan::mahasiswa');
        $routes->get('mahasiswa/lulus', 'Pengajuan::mahasiswa_lulus');
        $routes->get('mahasiswa/sertifikat/(:num)', 'Pengajuan::cetak_sertifikat/$1');
        $routes->post('mahasiswa/submit_payment', 'Pengajuan::submit_payment');
        $routes->get('mahasiswa/get_nilai/(:num)', 'Pengajuan::get_nilai_mahasiswa/$1');
        $routes->post('mahasiswa/update_mahasiswa/(:num)', 'Pengajuan::update_mahasiswa/$1');
        $routes->post('mahasiswa/simpan_nilai_akhir', 'Pengajuan::simpan_nilai_akhir');
    });

    // Mahasiswa Portal Group
    $routes->group('mahasiswa', ['namespace' => 'App\Controllers\Pendidikan\Mahasiswa', 'filter' => 'pendidikan_auth:mahasiswa'], function ($routes) {
        $routes->get('/', 'Dashboard::index');
        $routes->get('dashboard', 'Dashboard::index');
        $routes->get('stase', 'Dashboard::stase');
        $routes->get('stase/detail/(:num)', 'Dashboard::stase_detail/$1');

        $routes->post('logbook/upload', 'Dashboard::upload_logbook');
        $routes->post('tugas/upload', 'Dashboard::upload_tugas');
        $routes->get('penilaian', 'Dashboard::penilaian');
        $routes->get('sertifikat', 'Dashboard::sertifikat');
        $routes->get('sertifikat/download', 'Dashboard::download_sertifikat');
        $routes->get('profil', 'Dashboard::profil');
        $routes->post('profil/update', 'Dashboard::update_profil');
        $routes->post('profil/update_password', 'Dashboard::update_password');
        $routes->get('simulate-payment/(:segment)', 'Dashboard::simulate_payment/$1');
    });

    // CI Dashboard & Features
    $routes->group('ci', ['namespace' => 'App\Controllers\Pendidikan\Ci'], function ($routes) {
        $routes->get('dashboard', 'DashboardController::index');
        
        // Room & Student
        $routes->get('room/(:num)/(:num)', 'RoomController::detail/$1/$2');
        $routes->get('student/(:num)/(:num)/(:num)', 'StudentController::detail/$1/$2/$3');
        
        // Tasks (AJAX & Download)
        $routes->post('task/create', 'TaskController::create');
        $routes->post('task/grade/(:num)', 'TaskController::grade/$1');
        $routes->get('task/download/(:num)/(:num)', 'TaskController::download/$1/$2');
        
        // Logbooks (AJAX & Download)
        $routes->post('logbook/validate/(:num)', 'LogbookController::validateLogbook/$1');
        $routes->get('logbook/download/(:num)/(:num)', 'LogbookController::download/$1/$2');
    });
    // Admin Diklat - View Routes (Bootstrap 5)
    $routes->group('', ['filter' => 'pendidikan_auth:diklat'], function($routes) {
    $routes->get('admin/diklat', '\App\Controllers\Pendidikan\AdminDiklat::index');
    $routes->get('admin/diklat/dashboard', '\App\Controllers\Pendidikan\AdminDiklat::dashboard');
    $routes->post('admin/diklat/update_password', '\App\Controllers\Pendidikan\AdminDiklat::update_password');
    $routes->get('admin/diklat/institusi', '\App\Controllers\Pendidikan\AdminDiklat::institusi');
    $routes->get('admin/diklat/institusi/detail/(:num)', '\App\Controllers\Pendidikan\AdminDiklat::institusiDetail/$1');
    $routes->get('admin/diklat/ci', '\App\Controllers\Pendidikan\AdminDiklat::ci');
    $routes->get('admin/diklat/stase', '\App\Controllers\Pendidikan\AdminDiklat::stase');
    $routes->get('admin/diklat/stase/detail/(:num)', '\App\Controllers\Pendidikan\AdminDiklat::staseDetail/$1');
    $routes->get('admin/diklat/user', '\App\Controllers\Pendidikan\AdminDiklat::user');
    $routes->get('admin/diklat/user/detail/(:num)', '\App\Controllers\Pendidikan\AdminDiklat::userDetail/$1');
    $routes->get('admin/diklat/user/detail/(:num)/profesi/(:any)', '\App\Controllers\Pendidikan\AdminDiklat::userProfesi/$1/$2');
    $routes->get('admin/diklat/pengajuan', '\App\Controllers\Pendidikan\AdminDiklat::pengajuan');
    $routes->get('admin/diklat/pengajuan/detail/(:num)', '\App\Controllers\Pendidikan\AdminDiklat::pengajuanDetail/$1');

    // Admin Diklat - RESTful API untuk kelola institusi dan CI
    $routes->group('admin/diklat/api', ['namespace' => 'App\Controllers\Pendidikan'], function ($routes) {
        $routes->get('institusi', 'AdminDiklat::list');
        $routes->get('institusi/(:num)', 'AdminDiklat::detail/$1');
        $routes->post('institusi/approve/(:num)', 'AdminDiklat::approve/$1');
        $routes->post('institusi/decline/(:num)', 'AdminDiklat::decline/$1');
        $routes->post('institusi/revision/(:num)', 'AdminDiklat::revision/$1');
        $routes->post('institusi/resubmit/(:num)', 'AdminDiklat::resubmit/$1');
        $routes->get('dokumen/view/(:num)', 'AdminDiklat::viewDokumen/$1');
        $routes->get('dokumen/download/(:num)', 'AdminDiklat::downloadDokumen/$1');
        $routes->post('dokumen/verifikasi/(:num)', 'AdminDiklat::verifikasiDokumen/$1');
        $routes->get('institusi/file/(:num)/(:any)', 'AdminDiklat::file/$1/$2');
        $routes->get('pengajuan', 'AdminDiklat::pengajuanList');
        $routes->get('pengajuan/(:num)', 'AdminDiklat::apiPengajuanDetail/$1');
        $routes->post('pengajuan/approve/(:num)', 'AdminDiklat::pengajuanApprove/$1');
        $routes->post('pengajuan/decline/(:num)', 'AdminDiklat::pengajuanDecline/$1');
        $routes->post('pengajuan/revision/(:num)', 'AdminDiklat::pengajuanRevision/$1');
        $routes->get('stase', 'AdminDiklat::staseList');
        $routes->post('stase', 'AdminDiklat::staseStore');
        $routes->post('stase/update/(:num)', 'AdminDiklat::staseUpdate/$1');
        $routes->post('stase/delete/(:num)', 'AdminDiklat::staseDelete/$1');
        $routes->post('stase/save-mapping/(:num)', 'AdminDiklat::staseSaveRoomMapping/$1');
        $routes->post('stase/assign-ci/(:num)', 'AdminDiklat::staseAssignCi/$1');
        $routes->post('stase/remove-ci/(:num)', 'AdminDiklat::staseRemoveCi/$1');
        $routes->get('stase/mahasiswa/(:num)', 'AdminDiklat::staseMahasiswaList/$1');
        $routes->get('stase/available-mahasiswa', 'AdminDiklat::staseAvailableMahasiswa');
        $routes->post('stase/add-mahasiswa/(:num)', 'AdminDiklat::staseAddMahasiswa/$1');
        $routes->post('stase/remove-mahasiswa/(:num)', 'AdminDiklat::staseRemoveMahasiswa/$1');
        $routes->get('profesi', 'AdminDiklat::profesiList');
        $routes->get('unit-kerja', 'AdminDiklat::unitKerjaList');
        $routes->get('ci', 'AdminDiklat::ciApiList');
        $routes->post('ci', 'AdminDiklat::ciApiInsert');
        $routes->get('ci/(:num)', 'AdminDiklat::ciApiDetail/$1');
        $routes->post('ci/update/(:num)', 'AdminDiklat::ciApiUpdate/$1');
        $routes->get('ci/delete/(:num)', 'AdminDiklat::ciApiDelete/$1');
        $routes->post('mahasiswa/update/(:num)', 'AdminDiklat::mahasiswaUpdate/$1');
        $routes->post('mahasiswa/delete/(:num)', 'AdminDiklat::mahasiswaDelete/$1');
        $routes->post('mahasiswa/upload-invoice/(:num)', 'AdminDiklat::mahasiswaUploadInvoice/$1');
        $routes->get('mahasiswa/bukti-bayar/(:num)', 'AdminDiklat::mahasiswaBuktiBayar/$1');
        $routes->post('mahasiswa/verifikasi-pembayaran/(:num)', 'AdminDiklat::mahasiswaVerifikasiPembayaran/$1');
    });

    // Institusi - Upload dokumen
    $routes->group('institusi/dokumen', ['namespace' => 'App\Controllers\Pendidikan\Institusi'], function ($routes) {
        $routes->post('upload', 'Dokumen::upload');
        $routes->get('daftar', 'Dokumen::daftar');
    });
    });
});

// Super Admin
$routes->group('superadmin', ['namespace' => 'App\Controllers', 'filter' => 'pendidikan_auth:superadmin'], function ($routes) {
    $routes->get('dashboard', 'SuperAdmin::dashboard');
    $routes->post('create_admin', 'SuperAdmin::create_admin');
    $routes->post('reset_password_email', 'SuperAdmin::reset_password_email');
});

// --- ENHANCED: Pelatihan Module ---
$routes->group('pelatihan', function ($routes) {
    $routes->get('login', 'Pelatihan\\Auth::index');
    $routes->post('auth/login', 'Pelatihan\\Auth::login');
    $routes->get('register', 'Pelatihan\\Auth::register');
    $routes->post('auth/register', 'Pelatihan\\Auth::processRegister');
    $routes->get('logout', 'Pelatihan\\Auth::logout');
    $routes->get('lupa-password', 'Pelatihan\\Auth::forgotPassword');
    $routes->post('lupa-password/submit', 'Pelatihan\\Auth::forgotPasswordSubmit');
});

// Pelatihan - Admin
$routes->group('pelatihan/admin', ['namespace' => 'App\Controllers\Pelatihan\Admin', 'filter' => 'auth:admin'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    
    // Pelatihan Master
    $routes->get('pelatihan', 'Pelatihan::index');
    $routes->post('pelatihan/simpan', 'Pelatihan::simpan');
    $routes->post('pelatihan/update', 'Pelatihan::update');
    $routes->get('pelatihan/hapus/(:num)', 'Pelatihan::hapus/$1');
    $routes->get('pelatihan/publish/(:num)', 'Pelatihan::publish/$1');
    $routes->get('pelatihan/selesai/(:num)', 'Pelatihan::selesai/$1');
    $routes->get('pelatihan/draft/(:num)', 'Pelatihan::draft/$1');
    $routes->get('pelatihan/batal/(:num)', 'Pelatihan::batal/$1');
    $routes->get('pelatihan/kelola/(:num)', 'Pelatihan::kelola/$1');
    $routes->post('pelatihan/simpan_materi', 'Pelatihan::simpan_materi');
    $routes->post('pelatihan/materi/simpan', 'Pelatihan::simpan_materi');
    $routes->post('pelatihan/materi/update', 'Pelatihan::update_materi');
    $routes->get('pelatihan/materi/hapus/(:num)', 'Pelatihan::hapus_materi/$1');
    $routes->post('toggle_presensi', 'ManajemenPeserta::toggle_presensi');
    $routes->post('tambah_sesi_presensi', 'Pelatihan::tambah_sesi_presensi');
    $routes->post('pelatihan/simpan_sesi', 'Pelatihan::simpan_sesi');
    $routes->get('pelatihan/hapus_sesi/(:num)', 'Pelatihan::hapus_sesi/$1');
    
    $routes->get('pelatihan/evaluasi_soal/(:num)/(:any)', 'Pelatihan::get_evaluasi_soal/$1/$2');
    $routes->post('pelatihan/evaluasi_soal/simpan_kkm', 'Pelatihan::simpan_kkm');
    $routes->post('pelatihan/evaluasi_soal/simpan', 'Pelatihan::simpan_evaluasi_soal');
    $routes->get('pelatihan/evaluasi_soal/hapus/(:num)', 'Pelatihan::hapus_evaluasi_soal/$1');
    $routes->get('pelatihan/evaluasi_soal/hapus_file/(:num)', 'Pelatihan::hapus_file_soal/$1');
    $routes->get('pelatihan/kuesioner/(:num)', 'Pelatihan::get_kuesioner/$1');
    $routes->post('pelatihan/kuesioner/simpan', 'Pelatihan::simpan_kuesioner');
    $routes->post('pelatihan/kuesioner/update', 'Pelatihan::update_kuesioner');
    $routes->get('pelatihan/kuesioner/hapus/(:num)', 'Pelatihan::hapus_kuesioner/$1');
    $routes->get('pelatihan/kuesioner/template/(:num)', 'Pelatihan::generate_template_kuesioner/$1');
    
    // Pengaturan Logo
    $routes->get('pengaturan_logo', 'Pelatihan::pengaturan_logo');
    $routes->post('pengaturan_logo/update', 'Pelatihan::update_logo');
    
    // Monitoring & Users
    $routes->get('akun_peserta', 'ManajemenPeserta::akun_peserta');
    $routes->post('akun_peserta/tambah', 'ManajemenPeserta::tambah_akun');
    $routes->post('akun_peserta/edit', 'ManajemenPeserta::edit_akun');
    $routes->get('akun_peserta/toggle-status/(:any)', 'ManajemenPeserta::toggle_status/$1');
    $routes->get('akun_peserta/delete/(:any)', 'ManajemenPeserta::delete_akun/$1');
    $routes->get('monitoring', 'ManajemenPeserta::index');
    $routes->get('monitoring/detail_stat/(:any)', 'ManajemenPeserta::detail_stat/$1');
    $routes->get('monitoring/export_jpl_excel', 'ManajemenPeserta::export_jpl_excel');
    $routes->get('monitoring/jpl_history/(:any)', 'ManajemenPeserta::jpl_history/$1');
    // Verifikasi Pendaftaran
    $routes->get('verifikasi_pendaftaran', 'VerifikasiPendaftaran::index');
    $routes->post('verifikasi_pendaftaran/update_status', 'VerifikasiPendaftaran::update_status');
    $routes->get('verifikasi_pendaftaran/reset/(:num)', 'VerifikasiPendaftaran::reset/$1');

    $routes->get('monitoring_peserta', 'ManajemenPeserta::monitoring_peserta');
    $routes->get('monitoring/export_excel/(:num)', 'ManajemenPeserta::export_excel/$1');
    $routes->get('presensi/(:num)', 'ManajemenPeserta::presensi/$1');
    $routes->get('hapus_pendaftaran/(:num)/(:num)', 'ManajemenPeserta::hapus_pendaftaran/$1/$2');
    $routes->get('add_peserta', 'ManajemenPeserta::add_peserta');
    $routes->get('add_peserta/(:num)', 'ManajemenPeserta::add_peserta/$1');
    $routes->post('save_peserta', 'ManajemenPeserta::save_peserta');
    
    $routes->post('monitoring/set_target/(:num)', 'ManajemenPeserta::set_target/$1');
    $routes->post('monitoring/save_mapping_kelompok', 'ManajemenPeserta::save_mapping_kelompok');
    $routes->post('monitoring/broadcast_notifikasi', 'ManajemenPeserta::broadcast_notifikasi');
    $routes->post('monitoring/broadcast_room', 'ManajemenPeserta::broadcast_room');
    $routes->post('monitoring/remind_individual', 'ManajemenPeserta::remind_individual');
    $routes->get('monitoring/reminder/(:any)', 'ManajemenPeserta::reminder/$1');
    $routes->get('monitoring/mapping', 'ManajemenPeserta::mapping');
    $routes->post('monitoring/save_mapping', 'ManajemenPeserta::save_mapping');
    $routes->get('monitoring/peserta_by_profesi/(:num)', 'ManajemenPeserta::get_peserta_by_profesi/$1');
    $routes->get('akun', 'User::index');

    // Profil Admin
    $routes->get('profil', 'Profile::index');
    $routes->post('profil/update', 'Profile::update');

    // Pelatihan Aliases & Materials
    $routes->post('pelatihan/materi/simpan', 'Pelatihan::simpan_materi');

    // Grading & Feedback
    $routes->get('grading', 'Grading::index');
    $routes->get('grading/detail/(:num)', 'Grading::detail/$1');
    $routes->get('grading/log_jawaban/(:num)', 'Grading::log_jawaban/$1');
    $routes->get('feedback', 'Feedback::index');
    $routes->get('feedback/detail/(:num)', 'Feedback::detail/$1');

    // Data Master
    $routes->group('master', function($routes) {
        $routes->get('profesi', 'DataMaster::profesi');
        $routes->get('ruangan', 'DataMaster::ruangan');
        $routes->get('kategori_evaluasi', 'DataMaster::kategori_evaluasi');
        $routes->get('kategori_skp', 'DataMaster::kategori_skp');
        $routes->post('simpan_kategori_skp', 'DataMaster::simpan_kategori_skp');
        $routes->get('hapus_kategori_skp/(:num)', 'DataMaster::hapus_kategori_skp/$1');
        $routes->post('rename_ranah', 'DataMaster::rename_ranah');
        $routes->get('hapus_ranah/(.*)', 'DataMaster::hapus_ranah/$1');
        $routes->post('simpan/(:any)', 'DataMaster::simpan/$1');
        $routes->get('hapus/(:segment)/(.*)', 'DataMaster::hapus/$1/$2');
        $routes->get('narasumber', 'DataMaster::narasumber');
        $routes->post('simpan_narasumber', 'DataMaster::simpan_narasumber');
        $routes->get('hapus_narasumber/(:num)', 'DataMaster::hapus_narasumber/$1');
        $routes->post('simpan_narasumber_ajax', 'DataMaster::simpan_narasumber_ajax');
        $routes->get('penyelenggara', 'DataMaster::penyelenggara');
        $routes->post('simpan_penyelenggara', 'DataMaster::simpan_penyelenggara');
        $routes->get('hapus_penyelenggara/(:num)', 'DataMaster::hapus_penyelenggara/$1');
        $routes->post('simpan_penyelenggara_ajax', 'DataMaster::simpan_penyelenggara_ajax');
    });

    // Finance & Certs
    $routes->get('sertifikat', 'Certificate::index');
    $routes->get('sertifikat/template', 'Certificate::template');
    $routes->post('sertifikat/template/save', 'Certificate::template_save');
    $routes->post('certificate/update', 'Certificate::update');
    $routes->post('certificate/approve/(:num)', 'Certificate::approve/$1');
    $routes->get('certificate/unverify/(:num)', 'Certificate::unverify/$1');
    $routes->get('certificate/publish/(:num)', 'Certificate::publish/$1');
    $routes->get('certificate/unpublish/(:num)', 'Certificate::unpublish/$1');
    $routes->get('certificate/preview_template/(:num)', 'Certificate::preview_template/$1');
    $routes->get('certificate/preview_template/(:num)/(:num)', 'Certificate::preview_template/$1/$2');
    $routes->get('certificate/preview_pelatihan/(:num)', 'Certificate::preview_pelatihan/$1');
    $routes->get('certificate/preview_pelatihan/(:num)/(:num)', 'Certificate::preview_pelatihan/$1/$2');
    $routes->get('certificate/preview_external/(:num)', 'Certificate::preview_external/$1');
    $routes->get('certificate/peserta_by_pelatihan/(:num)', 'Certificate::peserta_by_pelatihan/$1');
    $routes->post('certificate/save_template', 'Certificate::save_template');
    $routes->get('certificate/delete_template/(:num)', 'Certificate::delete_template/$1');
    $routes->post('certificate/save_pejabat', 'Certificate::save_pejabat');
    $routes->get('certificate/delete_pejabat/(:num)', 'Certificate::delete_pejabat/$1');
    $routes->post('certificate/reject/(:num)', 'Certificate::reject/$1');

});

// Pelatihan - Admin Pengabdian
$routes->group('pelatihan/admin_pengabdian', ['namespace' => 'App\Controllers\Pelatihan\AdminPengabdian', 'filter' => 'auth:admin_pengabdian'], function($routes) {
    $routes->get('sertifikat', 'Certificate::index');
    $routes->post('certificate/updateskp/(:num)', 'Certificate::updateskp/$1');
    $routes->post('certificate/approve/(:num)', 'Certificate::approve/$1');
    $routes->post('certificate/reject/(:num)', 'Certificate::reject/$1');

    // Profil Admin Pengabdian
    $routes->get('profil', 'Profile::index');
    $routes->post('profil/update', 'Profile::update');
});

// Pelatihan - Peserta
$routes->group('pelatihan/peserta', ['namespace' => 'App\Controllers\Pelatihan\Peserta', 'filter' => 'auth:peserta'], function($routes) {
    // Beranda & Core
    $routes->get('beranda', 'Home::index');
    $routes->get('dashboard', 'Home::index'); // Alias
    $routes->get('notifikasi', 'Home::notifikasi');
    $routes->post('notifikasi/read/(:num)', 'Home::mark_read/$1');
    $routes->post('notifikasi/read_all', 'Home::mark_all_read');
    $routes->get('profil', 'Profile::index');
    $routes->post('profil/update', 'Profile::update');
    
    // Pembelajaran (Catalog & My Learning)
    $routes->get('pembelajaran', 'Catalog::index');
    $routes->get('pembelajaran_saya', 'MyLearning::index');
    $routes->get('batalkan_pelatihan/(:num)', 'MyLearning::batalkan_pelatihan/$1');
    $routes->get('detail_pelatihan/(:num)', 'Catalog::detail/$1');
    
    // Training Execution
    $routes->get('pelatihan', 'Training::index');
    $routes->match(['get', 'post'], 'daftar/(:num)', 'Training::daftar/$1');
    $routes->match(['get', 'post'], 'daftar_pelatihan/(:num)', 'Training::daftar/$1');
    $routes->get('belajar/(:num)', 'Training::belajar/$1');
    $routes->get('learn/(:num)', 'Training::belajar/$1');
    $routes->get('tandai_selesai/(:num)/(:num)', 'Training::tandai_selesai/$1/$2');
    $routes->get('presensi/(:num)', 'Training::presensi/$1');
    $routes->post('upload_bukti_bayar/(:num)', 'Training::upload_bukti_bayar/$1');
    $routes->get('pembayaran/(:num)', 'Finance::index/$1');
    $routes->post('pembayaran/upload/(:num)', 'Finance::upload/$1');
    $routes->get('materi/(:num)', 'Training::materi/$1');
    $routes->get('kuis/(:num)', 'Training::kuis/$1');
    $routes->post('submit_kuis/(:num)', 'Training::submit_kuis/$1');
    $routes->get('evaluasi/(:num)', 'Training::evaluasi/$1');
    $routes->post('submit_evaluasi/(:num)', 'Training::submit_evaluasi/$1');
    $routes->post('submit_evaluasi_sesi/(:num)', 'Training::submit_evaluasi_sesi/$1');
    $routes->get('reset_simulasi', 'Training::reset_simulasi');
    $routes->get('reset_simulasi/(:num)', 'Training::reset_simulasi/$1');
    $routes->get('approve_and_start/(:num)', 'Training::approve_and_start/$1');

    // Certification & Log SKP
    $routes->get('sertifikat', 'Certificate::index');
    $routes->get('sertifikat_saya', 'Certificate::index'); // Alias
    $routes->get('log_jpl', 'Certificate::log_jpl');
    $routes->get('upload_sertifikat', 'Certificate::upload');
    $routes->post('submit_upload_sertifikat', 'Certificate::submit_upload');
    $routes->get('edit_sertifikat/(:num)', 'Certificate::edit/$1');
    $routes->post('submit_edit_sertifikat/(:num)', 'Certificate::submit_edit/$1');
    $routes->get('unduh_sertifikat/(:num)', 'Certificate::download/$1');
});

// Pelatihan - Institusi
$routes->group('pelatihan/institusi', ['namespace' => 'App\Controllers\Pelatihan\Institusi'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('pendaftaran', 'Enrollment::index');
    $routes->get('status', 'Enrollment::status');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

