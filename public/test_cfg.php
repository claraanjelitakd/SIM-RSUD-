<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
$pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
require $pathsPath;
$paths = new \Config\Paths();
require $paths->systemDirectory . '/Boot.php';
\CodeIgniter\Boot::bootWeb($paths);
