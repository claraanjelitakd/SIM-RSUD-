<?php

namespace App\Controllers;

class TestDb extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        echo "<pre>";
        echo "Database hostname: " . $db->hostname . "\n";
        echo "Database username: '" . $db->username . "'\n";
        echo "Database database: " . $db->database . "\n";
        echo "</pre>";
    }
}
