<?php
namespace ImageStack\Mount;

/**
 * Le montage est responsable d'associer les différents éléments seravnt à "monter" les images dans ImageStack.
 *
 */
interface MountInterface {
	/**
	 * Mount image in ImageStack
	 * 
	 * @param string $path
	 * @return \ImageStack\Image|false
	 */
	public function mountImage($path);
}