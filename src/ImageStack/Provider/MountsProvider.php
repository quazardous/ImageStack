<?php
namespace ImageStack\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use ImageStack\ApplicationAwareInterface;

class MountsProvider implements ServiceProviderInterface
{

    function boot(Container $app)
    {}

    function register(Container $app)
    {
        foreach ((array) $app['config']['mount.d'] as $folder) {
            $this->loadConfigsFromFolder($app, $folder);
        }
    }

    protected function loadConfigsFromFolder(Container $app, $folder)
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