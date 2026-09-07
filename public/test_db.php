<?php
require_once __DIR__ . '/../system/Boot.php';
$paths = new \Config\Paths();
\CodeIgniter\Boot::bootWeb($paths);
$db = \Config\Database::connect();
echo "Connected as: '" . $db->username . "'\n";
