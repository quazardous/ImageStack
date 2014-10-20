<?php
namespace ImageStack\Converter;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ImageStack\OptionableComponent;

class Mount extends OptionableComponent
{

	public function convert($mount)
	{
		$app = $this->app;
		if (!isset($app['mount.' . $mount])) {
			$app->abort(404, sprintf('Mount %s does not exist', $mount));
		}
		return $app['mount.' . $mount];
	}
}