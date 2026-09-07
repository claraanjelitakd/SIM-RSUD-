<?php
// Initialize CodeIgniter lightly
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH);
$pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
require $pathsPath;
$paths = new \Config\Paths();
require $paths->systemDirectory . '/Boot.php';
\CodeIgniter\Boot::bootWeb($paths);
// After bootWeb, all configs are loaded
$db = \Config\Database::connect();
echo "\n==== DB TEST ====\n";
echo "Username: '" . $db->username . "'\n";
echo "Hostname: '" . $db->hostname . "'\n";
echo "Database: '" . $db->database . "'\n";
echo "Driver: '" . $db->DBDriver . "'\n";
echo "=================\n";
