<?php
$configs = array();

$configs['whaovh'] = array(
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
    'manipulators' => array(
        'immo' => array(
            'profiles' => array(
                // Formats mobile
                '|^/*lpa/.*$|' => '241x157',
                '|^/*art/.*$|' => '<320x221',
                '|^/*mlpa/.*$|' => '240x180',
                '|^/*mpa/.*$|' => '<360x270',
                // HP à la une 320×220 : hp
                '|^/*hp/.*$|' => '320x220',
                // HP miniature 160×112 : hpm
                '|^/*hpm/.*$|' => '160x112',
                // Résultat de recherche 241×157 : lsr
                '|^/*lsr/.*$|' => '241x157',
                // Détail d'annonce <220×200 : pa
                '|^/*pa/.*$|' => '<220x200',
                // Détail d'annonce miniature <60×45 : pam
                '|^/*pam/.*$|' => '<60x45',
                // Lightbox d'une photo <800×600 : pal
                '|^/*pal/.*$|' => '<800x600',
                // Photo éditoriale <320×221 : ed
                '|^/*ed/.*$|' => '<320x221'
            )
        ),
        'agl' => array(
            'profiles' => array(
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/article-miniature-1/#' => '<100x100',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/article-miniature-2/#' => '<200x165',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/article-miniature-iphone/#' => '<170x300',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/article-taille-normale-nm/#' => '<500x800',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/article-taille-normale/#' => '<500x800',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/article-diaporama-iphone/#' => '<640x1300',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/article-taille-normale-iphone/#' => '<350x600',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/ba_cityguide-cartouche-principal-carrousel/#' => '670x370',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/ba_cityguide-cartouche-principal-carrousel-thumb/#' => '165x100',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/ba_cityguide-cartouche-principal-logo/#' => '153x53',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/ba_cityguide-resultat-recherche/#' => '154x109',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/ba_cityguide-bulle-gmap/#' => '150x62',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/bloc_droite_diaporama_vignette/#' => '99x90',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/cityguide_agenda-cartouche-principal/#' => '<305x700',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/cityguide_agenda-resultat-recherche/#' => '<204x500',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/cityguide_agenda-principal-carrousel-thumb/#' => '<104x300',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/cityguide_agenda-principal-carrousel/#' => '<430x202',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/diaporama/#' => '<512x700',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/diaporama_auto_vignette/#' => '<50x50',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/diaporama_auto_affichage/#' => '<470x1200',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/diaporama-miniature/#' => '<100x67',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/diaporama-bloc-home/#' => '<245x163',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/diaporama_auto_vignette_list/#' => '<150x400',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/home_image_article1/#' => '<196x500',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/home_image_article2/#' => '130x130',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/nm_article_les_plus_miniatures/#' => '90x90',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/home_image_asuivre/#' => '150x100',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/mobile_detail_article/#' => '<195x500',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/home_image_focus/#' => '260x150',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/fvideos_148x110/#' => '148x110',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/fdiapox3_158x105/#' => '158x105',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/fdiapox1_258x172/#' => '258x172',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/hp_sortir_100xX/#' => '<100x300',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/pages_thematiques-miniature/#' => '90x100',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/newsletter_160xX/#' => '<160x400',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/bloc_main_actu_en_images/#' => '140x150',
                // en cours 153x53 '#^/*media_(nicematin|varmatin|corsematin)/ba_cityguide-cartouche-principal-logo/#' => '<160x400',
                '#^/*media_(nicematin|varmatin|corsematin)/imagecache/([^/]+)/#' => 2, // reporting sur la 2ème () et 404
                '#.*#' => false // ramasse panier : si il n'y apas de format correspondant on fait un 404
	        )// ramasse panier : si il n'y apas de format correspondant on fait un 404
        )
    ),
    'caches' => array(
        'immo' => array(
            'cache_dir' => __DIR__ . '/../../var/cache/m/immo'
        ),
        'agl' => array(
            'cache_dir' => __DIR__ . '/../../var/cache/m/agl'
        )
    )
);

$configs['development'] = array(
    'imagine.driver' => 'gd',
    'debug' => true,
    'mount.d' => __DIR__.'/../../contrib/mount.d',
    );


//     'backends' => array(
//         'foo' => array(
//             'backend_url' => 'http://localhost/berlioz/ImageStack/contrib/image/public/'
//         ),
//         'fool' => array(
//             'backend_dir' => __DIR__ . '/../../contrib/image/private/'
//         ),
//         'bar' => array(
//             'patterns' => array(
//                 // le premier slug est le format suivit du chemin
//                 // on recupère la première parenthèse
//                 // si on veut différencier le chemin de récup du chemin de cache
//                 // on peut spécifier un tableau associatif array('path'=>..., 'cache'=>...)
//                 '|^/*[^/]*/(.*)$|' => array(
//                     1
//                 )
//             )
//         )
//     ),
//     'manipulators' => array(
//         'bar' => array(
//             'profiles' => array(
//                 '|^/*100/.*$|' => '100x100',
//                 '|^/*mini/.*$|' => '200x150'
//             )
//         )
//     ),
//     'caches' => array(
//         'barc' => array(
//             'cache_dir' => __DIR__ . '/../../var/cache/barc'
//         )
//     )
// );

require_once __DIR__ . '/tools.php';

$last = null;
foreach ($configs as &$config) {
    if ($last) {
        $config = array_merge_recursive_distinct($last, $config);
    }
    $last = $config;
}

unset($last);
unset($config);

return $configs;