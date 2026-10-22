<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../bootstrap.php';

use ModulIS\Form\Control\CurrencyInput;
use ModulIS\Form\Renderer\Bootstrap6Renderer;
use Nette\DI;
use Tester\Assert;

class FormExtensionTest extends TestCase
{
	private function createContainer(string $config): DI\Container
	{
		$tempDir = __DIR__ . '/output/' . md5($config);
		@mkdir($tempDir, 0777, true);

		$loader = new DI\ContainerLoader($tempDir, true);

		$class = $loader->load(function(DI\Compiler $compiler) use ($config, $tempDir): void
		{
			$compiler->addExtension('latte', new \Nette\Bridges\ApplicationDI\LatteExtension($tempDir));
			$compiler->addExtension('modulisForm', new \ModulIS\Form\DI\FormExtension);
			$compiler->addConfig(\Nette\Neon\Neon::decode($config) ?? []);
		}, $config);

		$container = new $class;
		\assert($container instanceof DI\Container);
		$container->initialize();

		return $container;
	}


	public function testConfiguration()
	{
		$container = $this->createContainer("modulisForm:\n\ticons: fontawesome\n\ticonStyle: fa-light\n\tcurrency: EUR\n\tcurrencyDecimals: 2\n");

		Assert::same('EUR', CurrencyInput::getDefaultCurrency());
		Assert::same(2, CurrencyInput::getDefaultDecimals());
		Assert::same('<i class="fa-light fa-circle-question fa-fw" aria-hidden="true"></i>', (string) Bootstrap6Renderer::getDefaultIconRenderer()->render('help'));

		$latte = $container->getByType(\Nette\Bridges\ApplicationLatte\LatteFactory::class)->create();

		Assert::contains('inputRender', array_keys($latte->getExtensions()[array_key_last($latte->getExtensions())]->getTags()));
	}


	public function testInvalidIcons()
	{
		Assert::exception(
			fn() => $this->createContainer("modulisForm:\n\ticons: stdClass\n"),
			DI\InvalidConfigurationException::class,
			"%a%has to be 'bootstrap', 'fontawesome' or a class implementing%a%"
		);
	}
}

(new FormExtensionTest)->run();
