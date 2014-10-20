<?php
namespace ImageStack\Mount;

/*
use ImageStack\Storage\StorageInterface;
use ImageStack\Backend\BackendInterface;
use ImageStack\OptionableComponent;
*/

/**
 * Un montage qui log des infos sur les images qu'il gère.
 * Ex : permet de reconnaitre les formats associés à des types d'URLs.
 *
 */
class ProfilerMount extends DefaultMount {
	
	/**
	 * (non-PHPdoc)
	 * @see \ImageStack\Mount\MountInterface::mountImage()
	 */
	public function mountImage($path) {
	  $image = parent::mountImage($path);
	  
	  if ($image) {
  		$size = $image->getImage()->getSize();
  		$w = $size->getWidth();
  		$h = $size->getHeight();
  		$l = strlen($image->getData());
  		$found = 'OK';
	  }
	  else {
	    $w = $h = $l = 0;
	    $found = '404';
	  }
	  
	  $profile = '';
	  if (isset($this->options['patterns'])) {
	    foreach ($this->options['patterns'] as $pattern => $assemble) {
	      if (preg_match($pattern, $path, $matches)) {
	        $profile = '';
	        // on assemble les matches..
	        foreach ((array)$assemble as $item) {
	          if (is_integer($item)) {
	            $profile .= $matches[$item];
	          }
	          else {
	            $profile .= $item;
	          }
	        }
	        break;
	      }
	    }
	  }
	  
	  $this->app['logger']->info(sprintf("%s(%s) %s : %dx%d (%d) [%s] =%s",
	      $this->getName(),
	      $this->mount,
	      $path,
	      $w,
	      $h,
	      $l,
	      $profile,
	      $found));
		
		return $image;
	}
	
}