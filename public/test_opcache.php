<?php
if (function_exists('opcache_get_status')) {
    echo "OPcache is ENABLED\n";
    $status = opcache_get_status();
    echo "Files cached: " . (isset($status['scripts']) ? count($status['scripts']) : 0) . "\n";
    opcache_reset();
    echo "OPcache reset!\n";
} else {
    echo "OPcache is DISABLED\n";
}
