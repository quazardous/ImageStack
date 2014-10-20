<?php
namespace ImageStack\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use ImageStack\ApplicationAwareInterface;

class MountsProvider implements ServiceProviderInterface
{

    function boot(Application $app)
    {}

    function register(Application $app)
    {
        foreach ((array) $app['config']['mount.d'] as $folder) {
            $this->loadConfigsFromFolder($app, $folder);
        }
    }

    protected function loadConfigsFromFolder(Application $app, $folder)
    {
        foreach (scandir($folder) as $key => $entry) {
            $entryPath = $folder . '/' . $entry;
            if ($entry == '.' || $entry == '..')
                continue;
            if (is_dir($entryPath))
                continue; // non recursive
            $fileinfo = new \SplFileInfo($entryPath);
            $loaderName = 'mount_loader.' . strtolower($fileinfo->getExtension());
            if (isset($app[$loaderName])) {
                $app[$loaderName]->load($entryPath);
            }
        }
    }
}