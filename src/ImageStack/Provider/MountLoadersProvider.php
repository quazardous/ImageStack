<?php
namespace ImageStack\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use ImageStack\Mount\Loader\PhpLoader;
use ImageStack\Mount\Loader\YamlLoader;

class MountLoadersProvider implements ServiceProviderInterface
{

    function boot(Container $app)
    {}

    function register(Container $app)
    {
        
        $php = function() {
            return new PhpLoader();
        };
		
	    $app['mount_loader.php'] = $php;
	    $app['mount_loader.inc'] = $php;

	    $yml = function() {
	        return new YamlLoader();
	    };
	    
        $app['mount_loader.yaml'] = $yml;
        $app['mount_loader.yml'] = $yml;
	    
	}
}