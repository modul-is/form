<?php

declare(strict_types = 1);

namespace ModulIS\Form\DI;

use ModulIS\Form\Control\CurrencyInput;
use ModulIS\Form\Form;
use ModulIS\Form\Icon\ClassIconRenderer;
use ModulIS\Form\Icon\IconRenderer;
use ModulIS\Form\Renderer\Bootstrap6Renderer;
use Nette\Bridges\ApplicationDI\LatteExtension;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * Configuration of modul-is/form for the whole application
 *
 *     extensions:
 *         modulisForm: ModulIS\Form\DI\FormExtension
 *
 *     modulisForm:
 *         icons: fontawesome          # bootstrap (default), fontawesome, or a class implementing IconRenderer
 *         iconStyle: fa-light         # Font Awesome style
 *         currency: CZK
 *         currencyDecimals: 2
 *         latte: true                 # tags {inputRender}, {inputCore}, {labelCore}
 *         clientValidation: false     # rules checked by the server only (Form::setClientValidation())
 *
 * @property-read \stdClass $config
 */
final class FormExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'icons' => Expect::string('bootstrap'),
			'iconStyle' => Expect::string('fa-solid'),
			'currency' => Expect::string()->nullable(),
			'currencyDecimals' => Expect::int()->nullable()->min(0),
			'latte' => Expect::bool(true),
			'clientValidation' => Expect::bool(false)
		]);
	}


	/**
	 * Static defaults of the library are set when the container is created
	 */
	public function loadConfiguration(): void
	{
		$icons = $this->config->icons;

		if($icons === 'fontawesome')
		{
			$this->initialization->addBody('?::setDefaultIconRenderer(?::fontAwesome(?));', [
				new \Nette\PhpGenerator\Literal('\\' . Bootstrap6Renderer::class),
				new \Nette\PhpGenerator\Literal('\\' . ClassIconRenderer::class),
				$this->config->iconStyle
			]);
		}
		elseif($icons !== 'bootstrap')
		{
			$icons = ltrim($icons, '\\');

			if(!class_exists($icons) || !in_array(IconRenderer::class, class_implements($icons), true))
			{
				throw new \Nette\DI\InvalidConfigurationException("Option '{$this->name}.icons' has to be 'bootstrap', 'fontawesome' or a class implementing " . IconRenderer::class . ", '$icons' given.");
			}

			$this->initialization->addBody('?::setDefaultIconRenderer(new ?);', [
				new \Nette\PhpGenerator\Literal('\\' . Bootstrap6Renderer::class),
				new \Nette\PhpGenerator\Literal('\\' . $icons)
			]);
		}

		if($this->config->currency !== null)
		{
			$this->initialization->addBody('?::setDefaultCurrency(?);', [new \Nette\PhpGenerator\Literal('\\' . CurrencyInput::class), $this->config->currency]);
		}

		if($this->config->currencyDecimals !== null)
		{
			$this->initialization->addBody('?::setDefaultDecimals(?);', [new \Nette\PhpGenerator\Literal('\\' . CurrencyInput::class), $this->config->currencyDecimals]);
		}

		if($this->config->clientValidation)
		{
			$this->initialization->addBody('?::setDefaultClientValidation(true);', [new \Nette\PhpGenerator\Literal('\\' . Form::class)]);
		}
	}


	public function beforeCompile(): void
	{
		if(!$this->config->latte)
		{
			return;
		}

		foreach($this->compiler->getExtensions(LatteExtension::class) as $latte)
		{
			$latte->addExtension(new Statement(\ModulIS\Extension\FormExtension::class));
		}
	}
}
