<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
$pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
require $pathsPath;
$paths = new \Config\Paths();
require $paths->systemDirectory . '/Boot.php';
require $paths->systemDirectory . '/Common.php';
require $paths->systemDirectory . '/Autoloader/Autoloader.php';
require $paths->systemDirectory . '/Config/DotEnv.php';
$env = new \CodeIgniter\Config\DotEnv(realpath(FCPATH . '../'));
$env->load();
echo "Username in ENV: '" . env('database.default.username') . "'\n";
echo "Username in SERVER: '" . ($_SERVER['database.default.username'] ?? 'not set') . "'\n";
