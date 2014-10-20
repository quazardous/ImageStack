<?php

return array(
    'debug' => true,
    'storage.default' => array(
        'root' => __DIR__ . '/../../fo/web/m',
        'jpegtran' => '/usr/bin/jpegtran',
        'pngcrush' => '/usr/bin/pngcrush'
    ),
    'mount.root' => '/',
    'caches' => array(
        'barc' => array('cache_dir' => __DIR__ . '/../../var/cache/barc'),
    ),
    'imagine.driver' => 'gd',
    'debug' => true,
    'mount.d' => __DIR__.'/../../mount.d',
    );