<?php

return array(
    'debug' => false,
    'mounts' => array(
        'agl' => array(
            'patterns' => array(
                // on recupère le profile imagecache
                '|/imagecache/+([^/]+)/|' => array(
                    1
                )
            )
        )
    ),
    'imagine.driver' => 'gmagick',
    'backends' => array(
        'aglnm' => array(
            'name' => 'aglnm',
            'backend_url' => 'http://fo-web5.nicematin.ghm-s.fr/'
        ),
        'aglvm' => array(
            'name' => 'aglvm',
            'backend_url' => 'http://fo-web5.varmatin.ghm-s.fr/'
        ),
        'aglcm' => array(
            'name' => 'aglcm',
            'backend_url' => 'http://fo-web5.corsematin.ghm-s.fr/'
        ),
        'aglmm' => array(
            'name' => 'aglmm',
            'backend_url' => 'http://fo-web5.monacomatin.ghm-s.fr/'
        ),
        'ouch' => array(
            'name' => 'ouch',
            'backend_url' => 'http://ouch.nicematin.fr/sites/default/files/',
            'patterns' => array(
                // pour le manipulateur ouch on veut que le chemin de base qui commence par image
                // truc bizar
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/[^/]+/media_(nicematin|varmatin|corsematin)/(image/.*)$#' => array(
                    3
                ),
                '#^/*(media_(nicematin|varmatin|corsematin)/)imagecache/[^/]+/(image/.*)$#' => array(
                    3
                )
            )
        ),
        'agl' => array(
            'name' => 'agile',
            'patterns' => array(
                // pour agile
                // truc bizar
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/[^/]+/(media_(nicematin|varmatin|corsematin)/)(.*)$#' => array(
                    'path' => array(
                        2,
                        3
                    )/*, 'cache'=> array(3)*/),
                '#^/*(media_(nicematin|varmatin|corsematin)/)imagecache/[^/]+/(.*)$#' => array(
                    'path' => array(
                        1,
                        3
                    )/*, 'cache'=> array(3)*/)
            )
        ),
        'immo' => array(
            'immonm' => array(
                'name' => 'immonm',
                'backend_url' => 'http://immo.nicematin.com/pa_photo/',
                'patterns' => array(
                    // le premier slug est le format suivit du chemin
                    // on recupère la première parenthèse
                    '#^/*[^/]*/pa_photo/(.*)$#' => array(
                        1
                    )
                )
            ),
            'ubiflow' => array(
                'name' => 'ubiflow',
                'backend_url' => 'http://photos.ubiflow.net/',
                'patterns' => array(
                    // le premier slug est le format suivit du chemin
                    // on recupère la première parenthèse
                    '#^/*[^/]*/ubiflow/(.*)$#' => array(
                        1
                    )
                )
            ),
            'vacance' => array(
                'name' => 'vacance',
                'backend_url' => 'http://www.vacances.com/upload/images/source/',
                'patterns' => array(
                    // le premier slug est le format suivit du chemin
                    // on recupère la première parenthèse
                    '#^/*[^/]*/vacance/(.*)$#' => array(
                        1
                    )
                )
            )
        )
    ),
    'storage.default' => array(
        'root' => __DIR__ . '/../../fo/web/m',
        'jpegtran' => '/usr/bin/jpegtran',
        'pngcrush' => '/usr/bin/pngcrush'
    ),
    'mount.root' => '/',
    'caches' => array(
        'immo' => array(
            'cache_dir' => __DIR__ . '/../../var/cache/m/immo'
        ),
        'agl' => array(
            'cache_dir' => __DIR__ . '/../../var/cache/m/agl'
        )
    ),
    'imagine.driver' => 'gd',
    'debug' => true,
    'mount.d' => __DIR__.'/../../contrib/mount.d',
    );