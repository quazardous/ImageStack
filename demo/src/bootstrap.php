<?php

$env = getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : (defined('APPLICATION_ENV') ? APPLICATION_ENV : 'production');

setlocale(LC_ALL, 'fr_FR.utf8', 'fr_FR', 'fr');

date_default_timezone_set('Europe/Paris');

use ImageStack\Application as MainApplication;
$app = new MainApplication;

$configs = include __DIR__.'/config/config.php';
$app['config'] = $configs[$env];
unset($configs);
unset($env);

$app['debug'] = $app['config']['debug'];

use Silex\Provider\MonologServiceProvider;
$app->register(new MonologServiceProvider(), array(
		'monolog.logfile' => __DIR__.'/../logs/imagestack.log',
));

use Silex\Provider\ValidatorServiceProvider;
$app->register(new ValidatorServiceProvider);

use ImageStack\Provider\ListenersProvider;
$app->register(new ListenersProvider());

use ImageStack\Provider\CoreServicesProvider;
$app->register(new CoreServicesProvider);

use ImageStack\Provider\StoragesProvider;
$app->register(new StoragesProvider());

use ImageStack\Provider\MountLoadersProvider;
$app->register(new MountLoadersProvider());

use ImageStack\Provider\MountsProvider;
$app->register(new MountsProvider());

use ImageStack\Provider\ImagineProvider;
$app->register(new ImagineProvider());

return $app;