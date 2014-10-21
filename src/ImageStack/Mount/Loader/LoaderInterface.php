<?php
namespace ImageStack\Mount\Loader;

/**
 * Mount config loader interface.
 *
 */
interface LoaderInterface {
	public function load($resource);
}