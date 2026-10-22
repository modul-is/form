<?php

declare(strict_types = 1);

namespace ModulIS\Form\Demo;

use Nette\Bootstrap\Configurator;

/**
 * Minimal Nette application for developing the library - one page with a form showing all inputs.
 */
class Bootstrap
{
	public static function boot(): Configurator
	{
		$rootDir = dirname(__DIR__);

		$configurator = new Configurator;
		$configurator->setDebugMode(true);
		$configurator->enableTracy($rootDir . '/log');
		$configurator->setTempDirectory($rootDir . '/temp');
		$configurator->addConfig($rootDir . '/config/common.neon');

		return $configurator;
	}
}
