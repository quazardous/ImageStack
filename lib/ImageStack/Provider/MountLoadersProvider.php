<?php
namespace ImageStack\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use ImageStack\Mount\Loader\PhpLoader;
use ImageStack\Mount\Loader\YamlLoader;

class MountLoadersProvider implements ServiceProviderInterface
{

    function boot(Application $app)
    {}

    function register(Application $app)
    {
        
        $php = $app->share(function() {
            return new PhpLoader();
        });
		
	    $app['mount_loader.php'] = $php;
	    $app['mount_loader.inc'] = $php;

	    $yml = $app->share(function() {
	        return new YamlLoader();
	    });
	    
        $app['mount_loader.yaml'] = $yml;
        $app['mount_loader.yml'] = $yml;
	    
	}
}