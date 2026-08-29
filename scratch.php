<?php
require 'vendor/autoload.php';

$app = new \Config\Paths();
require rtrim($app->systemDirectory, '\\/ ') . '/bootstrap.php';

$db = \Config\Database::connect();
$pengajuan_id = 2; // Dari URL http://localhost:8080/pendidikan/admin/diklat/pengajuan/detail/2

echo "Querying Pengajuan ID $pengajuan_id...\n";

$pengajuan = $db->table('pengajuan_praktik_pendidikan')->where('id', $pengajuan_id)->get()->getRowArray();
print_r($pengajuan);

echo "Querying Penempatan for Pengajuan ID $pengajuan_id...\n";
$penempatan = $db->table('penempatan_peserta_pendidikan')->where('pengajuan_id', $pengajuan_id)->get()->getResultArray();
print_r($penempatan);

echo "Querying all mahasiswa for this institusi...\n";
if ($pengajuan) {
    $mahasiswa = $db->table('mahasiswa_pendidikan')->where('institusi_id', $pengajuan['institusi_id'])->get()->getResultArray();
    print_r($mahasiswa);
}
