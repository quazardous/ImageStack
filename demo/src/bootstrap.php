<?php

setlocale(LC_ALL, 'fr_FR.utf8', 'fr_FR', 'fr');

date_default_timezone_set('Europe/Paris');

$loader = include __DIR__.'/../vendor/autoload.php';

$loader->set('ImageStack\\', realpath(__DIR__ . '/../../src'));

//TODO
//https://igor.io/2012/11/09/scaling-silex.html
// http://gonzalo123.com/2012/09/03/dependency-injection-containers-with-php-when-pimple-is-not-enough/
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

$container = new ContainerBuilder();
$loader = new YamlFileLoader($container, new FileLocator(__DIR__));
$loader->load(__DIR__ . '/services.yml');

//$container->setParameter("ROOT_PATH", dirname(__DIR__));

//http://gonzalo123.com/2013/02/11/scaling-silex-applications/

use ImageStack\Application as ImageStackApplication;
$app = new ImageStackApplication;

$app['config'] = include __DIR__.'/config/config.php';


$app['debug'] = $app['config']['debug'];

use Silex\Provider\MonologServiceProvider;
$app->register(new MonologServiceProvider(), array(
        'monolog.name' => 'ImageStackDemo',
		'monolog.logfile' => __DIR__.'/../var/logs/imagestackdemo.log',
));

use ImageStack\Provider\StoragesProvider;
$app->register(new StoragesProvider());

use ImageStack\Provider\MountLoadersProvider;
$app->register(new MountLoadersProvider());

use ImageStack\Provider\MountsProvider;
$app->register(new MountsProvider());

use ImageStack\Provider\ImagineProvider;
$app->register(new ImagineProvider());

return $app;