<?php
namespace ImageStack\Storage;

use ImageStack\Image;

class OptimizedFileStorage extends FileStorage {

	public function storeImage(Image $image, $mount, $path) {
		$data = &$image->getData();
		$this->optimize($data, $image->getType());
		$this->writeData($data, $mount . '/' . ltrim($path, '/'));
	}
	
	protected function getTempnam($prefix, $extention) {
		// tempnam() ne gère pas les extensions...
		while (true) {
			$filename = sys_get_temp_dir() . '/' . $prefix . substr(md5(uniqid().rand()), 0, 8) . '.' . $extention;
			if (!is_file($filename)) {
				return $filename;
			}
		}
	}
	
	protected function optimize(&$data, $type) {
		$src = $this->getTempnam("ops", $type);
		if (!file_put_contents($src, $data)) {
			throw new \RuntimeException("Error : cannot write $src");
		}
		switch ($type) {
			case 'png':
				if (!isset($this->options['pngcrush'])) return;
				$dst = $this->getTempnam("opd", $type);
				$pngcrush = $this->options['pngcrush'];
				exec("$pngcrush -rem allb -brute -reduce $src $dst", $output, $ret);
				unlink($src);
				if ($ret != 0) {
					throw new \RuntimeException("Error : pngcrush ($ret)");
				}
				$data = file_get_contents($dst);
				unlink($dst);
				if ($data === FALSE) {
					throw new \RuntimeException("Error : cannot read $dst");
				}
				$this->app['logger']->info($this->getName()." pngcrush =");
				break;
				
			case 'jpeg':
				if (!isset($this->options['jpegtran'])) return;
				$jpegtran = $this->options['jpegtran'];

				ob_start();
				passthru("$jpegtran -copy none -optimize $src", $ret);
				unlink($src);
				$data = ob_get_clean();
				if ($ret != 0) {
					throw new \RuntimeException("Error : jpegtran ($ret)");
				}
				$this->app['logger']->info($this->getName()." jpegtran =");
				break;
			default:
			  unlink($src);
		}
		
	}
}