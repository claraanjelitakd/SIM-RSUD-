<?php
$conn = @mysqli_connect('127.0.0.1', 'root', '', 'sim_diklat_test');
if (!$conn) {
    echo "MySQLi Connect Error: " . mysqli_connect_error() . "\n";
} else {
    echo "Connected successfully!\n";
}
