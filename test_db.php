<?php
$mysqli = new mysqli("localhost", "root", "", "sim_diklat");
$res = $mysqli->query("SELECT * FROM mahasiswa_pendidikan WHERE id = 3");
$row = $res->fetch_assoc();
if ($row) {
    print_r($row);
} else {
    echo "Mahasiswa ID 3 NOT FOUND.\n";
}
