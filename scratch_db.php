<?php
define('FCPATH', __DIR__ . '/public/');
require __DIR__ . '/app/Config/Paths.php';
$paths = new \Config\Paths();
require $paths->systemDirectory . '/Common.php';
require $paths->systemDirectory . '/Autoloader/Autoloader.php';
$loader = new \CodeIgniter\Autoloader\Autoloader();
$loader->initialize(new \Config\Autoload(), new \Config\Modules());
$loader->register();
require __DIR__ . '/app/Config/Database.php';
$db = new \Config\Database();
var_dump($db->default);
