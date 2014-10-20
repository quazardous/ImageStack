<?php
namespace ImageStack\Controller;

use ImageStack\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ImageStack\Mount\MountInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Front {
	
	public function image(Application $app, Request $request, MountInterface $mount, $path) {
		$response = new Response();
		$image = $mount->mountImage($path);

		if (!$image) {
			$app->abort(404, sprintf("%s not found", $path));
		}
		
		$response->headers->set('Content-Type', $image->getMime());

		return $response->setContent($image->getData());
	}
	
}