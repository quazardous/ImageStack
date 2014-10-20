<?php
namespace ImageStack\Provider;

use Silex\Application;
use Pimple\ServiceProviderInterface;
use ImageStack\Backend\ProxyBackend;
use ImageStack\Backend\StackBackend;
use ImageStack\Backend\FileBackend;
use ImageStack\Backend\CallbackBackend;
use ImageStack\Backend\ManipulatorBackend;
use ImageStack\Backend\BackendInterface;
use ImageStack\Mount\DefaultMount;
use ImageStack\Manipulator\ThumbnailerManipulator;
use ImageStack\Cache\NullCache;
use ImageStack\Cache\FileCache;
use ImageStack\Mount\ProfilerMount;

use ImageStack\ApplicationAwareInterface;

class MountsProvider implements ServiceProviderInterface
{

    function boot(Application $app)
    {}

    function register(Application $app)
    {
        foreach ((array)$app['config']['mount.d'] as $folder) {
        	$this->loadConfigsFromFolder($app, $folder);
        }
        
        
        
        $aglAutoFolderCallback = function (BackendInterface $backend, $path)
        {
            // si $path au format (media_nicematin/)?i/m/a/image.jpg $path devient image.jpg (i/m/a est artificiel)
            $s = 3; // index de ref
            if (preg_match('#^(media_(nicematin|varmatin|corsematin)/)?([^/])/([^/])/([^/])/([^/]+)(\.(jpe?g|gif|png))$#i', $path, $matches) || preg_match('#^(media_(nicematin|varmatin|corsematin))/([^/])/([^/])/([^/])/([^/]+)$#i', $path, $matches)) {
                if (! isset($matches[1])) {
                    $matches[1] = '';
                }
                if (! isset($matches[$s + 4])) {
                    $matches[$s + 4] = '';
                }
                $basename = str_pad(str_replace('.', '_', $matches[$s + 3]), 3, '_');
                $rbasename = strrev($basename);
                if ($basename[0] == $matches[$s + 0] && $basename[1] == $matches[$s + 1] && $basename[2] == $matches[$s + 2]) {
                    // nouveau path media_nicematin/image.jpg
                    $path = $matches[1] . $matches[$s + 3] . $matches[$s + 4];
                } elseif ($rbasename[0] == $matches[$s + 0] && $rbasename[1] == $matches[$s + 1] && $rbasename[2] == $matches[$s + 2]) {
                    // nouveau path media_nicematin/image.jpg
                    $path = $matches[1] . $matches[$s + 3] . $matches[$s + 4];
                }
            }
            return $backend->getImage($path);
        };
        
        $app['mount.immo'] = $app->share(function () use($app)
        {
            
            $thumbnailer = new ThumbnailerManipulator($app['config']['manipulators']['immo']);
            $cache = new FileCache($app['config']['caches']['immo']);
            return new DefaultMount('immo', new StackBackend(array(
                // on cherche sur nicematin pour les images /pa_photo/
                new ManipulatorBackend(new ProxyBackend($app['config']['backends']['immo']['immonm']), $thumbnailer, $cache, $app['config']['backends']['immo']['immonm']),
                // on cherche sur vacances.com pour les images /vacance
                new ManipulatorBackend(new ProxyBackend($app['config']['backends']['immo']['vacance']), $thumbnailer, $cache, $app['config']['backends']['immo']['vacance']),
                // on cherche sur ubiflow pour les images /ubiflow
                new ManipulatorBackend(new ProxyBackend($app['config']['backends']['immo']['ubiflow']), $thumbnailer, $cache, $app['config']['backends']['immo']['ubiflow'])
            )), $app['storage.default']);
        });
        
        $app['mount.aglnm'] = $app->share(function () use($app, $aglAutoFolderCallback)
        {
            $agile = new CallbackBackend(new ProxyBackend($app['config']['backends']['aglnm']), $aglAutoFolderCallback);
            $ouch = new CallbackBackend(new ProxyBackend($app['config']['backends']['ouch']), $aglAutoFolderCallback);
            $thumbnailer = new ThumbnailerManipulator($app['config']['manipulators']['agl']);
            $cache = new FileCache($app['config']['caches']['agl']);
            return new ProfilerMount('aglnm', new StackBackend(array(
                // on essaye de faire une imagette si on reconnait l'URL
                // depuis ouch
                new ManipulatorBackend($ouch, $thumbnailer, $cache, $app['config']['backends']['ouch']),
                // depuis agile
                new ManipulatorBackend($agile, $thumbnailer, $cache, $app['config']['backends']['agl']),
                // on passe en proxy direct
                $agile
            )), $app['storage.default'], $app['config']['mounts']['agl']);
        });
        $app['mount.aglvm'] = $app->share(function () use($app, $aglAutoFolderCallback)
        {
            $agile = new CallbackBackend(new ProxyBackend($app['config']['backends']['aglvm']), $aglAutoFolderCallback);
            $ouch = new CallbackBackend(new ProxyBackend($app['config']['backends']['ouch']), $aglAutoFolderCallback);
            $thumbnailer = new ThumbnailerManipulator($app['config']['manipulators']['agl']);
            $cache = new FileCache($app['config']['caches']['agl']);
            return new ProfilerMount('aglvm', new StackBackend(array(
                // on essaye de faire une imagette si on reconnait l'URL
                // depuis ouch
                new ManipulatorBackend($ouch, $thumbnailer, $cache, $app['config']['backends']['ouch']),
                // depuis agile
                new ManipulatorBackend($agile, $thumbnailer, $cache, $app['config']['backends']['agl']),
                // on passe en proxy direct
                $agile
            )), $app['storage.default'], $app['config']['mounts']['agl']);
        });
        $app['mount.aglcm'] = $app->share(function () use($app, $aglAutoFolderCallback)
        {
          $agile = new CallbackBackend(new ProxyBackend($app['config']['backends']['aglcm']),
		      $aglAutoFolderCallback);
		  $ouch = new CallbackBackend(
		      new ProxyBackend($app['config']['backends']['ouch']),
		      $aglAutoFolderCallback);
		  $thumbnailer = new ThumbnailerManipulator($app['config']['manipulators']['agl']);
		  $cache = new FileCache($app['config']['caches']['agl']);
			return new ProfilerMount('aglcm',
					new StackBackend(array(
			        // on essaye de faire une imagette si on reconnait l'URL
				    // depuis ouch
				    new ManipulatorBackend(
				        $ouch,
				        $thumbnailer,
				        $cache,
				        $app['config']['backends']['ouch']),
				        // depuis agile
					new ManipulatorBackend(
			            $agile,
			            $thumbnailer,
			            $cache,
			            $app['config']['backends']['agl']),
			        // on passe en proxy direct
			        $agile,
			    )),
					$app['storage.default'],
			    $app['config']['mounts']['agl']);
		});
		$app['mount.aglmm'] = $app->share(function() use ($app, $aglAutoFolderCallback) {
		  $agile = new CallbackBackend(
		      new ProxyBackend($app['config']['backends']['aglmm']),
		      $aglAutoFolderCallback);
		  $ouch = new CallbackBackend(
		      new ProxyBackend($app['config']['backends']['ouch']),
		      $aglAutoFolderCallback);
		  $thumbnailer = new ThumbnailerManipulator($app['config']['manipulators']['agl']);
		  $cache = new FileCache($app['config']['caches']['agl']);
			return new ProfilerMount('aglmm',
			    new StackBackend(array(
			      // on essaye de faire une imagette si on reconnait l'URL
				  // depuis ouch
				  new ManipulatorBackend(
			        $ouch,
			        $thumbnailer,
			        $cache,
			        $app['config']['backends']['ouch']),
				        // depuis agile
			      new ManipulatorBackend(
    	            $agile,
    	            $thumbnailer,
    	            $cache,
    	            $app['config']['backends']['agl']),
			      // on passe en proxy direct
					  $agile,
			    )),
			    $app['storage.default'],
			    $app['config']['mounts']['agl']);
		});
		
		    // DEBUT TEST DEV
		    
		    $autoFolderCallback = function(BackendInterface $backend, $path) {
		        // si $path au format i/m/a/image.jpg $path devient image.jpg (i/m/a est artificiel)
		        // si $path au format e/g/a/image.jpg $path devient image.jpg (e/g/a est artificiel)
		         
		        //echo $path; die();
		        if (preg_match('#^([^/])/([^/])/([^/])/([^/]+)(\.(jpe?g|gif|png))$#i', $path, $matches) || preg_match('#^([^/])/([^/])/([^/])/([^/]+)$#i', $path, $matches)) {
		            if (!isset($matches[5])) {
		                $matches[5] = '';
		            }
		            $basename = str_pad(preg_replace('/\s/', '_', $matches[4]), 3, '_');
		            $rbasename = strrev($basename);
		            if ($basename[0] == $matches[1] && $basename[1] == $matches[2] && $basename[2] == $matches[3]) {
		                $path = $matches[4] . $matches[5];
		            }
		            elseif ($rbasename[0] == $matches[1] && $rbasename[1] == $matches[2] && $rbasename[2] == $matches[3]) {
		                $path = $matches[4] . $matches[5];
		            }
		        }
		        return $backend->getImage($path);
		    };
		
		    $app['mount.foo'] = $app->share(function() use ($app, $autoFolderCallback) {
		        return new DefaultMount('foo',
		            new CallbackBackend(
		                new ProxyBackend($app['config']['backends']['foo']),
		                $autoFolderCallback),
		            $app['storage.default']);
		    });
		    $app['mount.foos'] = $app->share(function() use ($app) {
		        return new DefaultMount('foos',
		            // on crée un mount en pile
		            new StackBackend(array(
		                // on essaye en HTTP
		                new ProxyBackend($app['config']['backends']['foo']),
		                // on essaye en fichier
		                new FileBackend($app['config']['backends']['fool']),
		            )),
		            $app['storage.default']);
		    });
		    $app['mount.bar'] = $app->share(function() use ($app) {
		        return new DefaultMount('bar',
		            new CallbackBackend(new ManipulatorBackend(
		                // on crée un backend manipulateur qui récupère l'image originale depuis un proxybackend...
		                new ProxyBackend($app['config']['backends']['foo']),
		                // on crée des imagettes
		                new ThumbnailerManipulator($app['config']['manipulators']['bar']),
		                // on cache pas la source
		                new NullCache(),
		                $app['config']['backends']['bar']),
		                function(BackendInterface $backend, $path) {
		                    return $backend->getImage($path);
		                }
		            ),
		            // on monte sur le le storage par defaut (/m)
		        $app['storage.default']);
		    });
		    $app['mount.barl'] = $app->share(function() use ($app) {
		        return new DefaultMount('barl',
		            new ManipulatorBackend(
		                // on crée un backend manipulateur qui récupère l'image originale depuis un filebackend...
		                new FileBackend($app['config']['backends']['fool']),
		                // on crée des imagettes
		                new ThumbnailerManipulator($app['config']['manipulators']['bar']),
		                // on cache pas la source
		                new NullCache(),
		                $app['config']['backends']['bar']),
		            // on monte sur le le storage par defaut (/m)
		            $app['storage.default']);
		    });
		    $app['mount.barc'] = $app->share(function() use ($app) {
		        return new DefaultMount('barc',
		            new ManipulatorBackend(
		                // on crée un backend manipulateur qui récupère l'image originale depuis un proxybackend...
		                new ProxyBackend($app['config']['backends']['foo']),
		                // on crée des imagettes
		                new ThumbnailerManipulator($app['config']['manipulators']['bar']),
		                // on cache la source
		                new FileCache($app['config']['caches']['barc']),
		                $app['config']['backends']['bar']),
		            // on monte sur le le storage par defaut (/m)
		            $app['storage.default']);
		    });
		    // FIN TEST DEV
		    
	}
	
	protected function loadConfigsFromFolder(Application $app, $folder) {
	    foreach( scandir($folder) as $key => $entry) {
	        $entryPath = $folder.'/'.$entry;
	        if($entry == '.' || $entry == '..') continue;
	        if(is_dir($entryPath)) continue; //non recursive
	        $fileinfo = new \SplFileInfo($entryPath);
	        $loaderName = 'mount_loader.' . strtolower($fileinfo->getExtension());
	        if (isset($app[$loaderName])) {
	            $app[$loaderName]->load($entryPath);
	        }
	    }
	}
}