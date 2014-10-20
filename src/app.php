<?php

include __DIR__.'/../vendor/autoload.php';

$app = include __DIR__.'/bootstrap.php';

$app['session.storage.handler'] = null; // no sessions

use ImageStack\Provider\ConvertersProvider;
$app->register(new ConvertersProvider());

use Silex\Provider\ServiceControllerServiceProvider;
$app->register(new ServiceControllerServiceProvider);

use ImageStack\Provider\PublicControllersProvider;
$provider = new PublicControllersProvider;
$app->register($provider);
$app->mount($app['config']['mount.root'], $provider);

return $app;