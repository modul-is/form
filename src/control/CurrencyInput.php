<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class CurrencyInput extends \Nette\Forms\Controls\TextInput implements Renderable, Signalable, Helper\QuickCopyable, \Nette\Application\UI\SignalReceiver
{
	use Helper\InputGroup;
	use Helper\QuickCopy;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputCoreControl;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\Signals;
	use Helper\ControlClass;
	use Helper\RenderBasic;
	use Helper\RenderDefault;
	use Helper\RenderInline;
	use Helper\RenderFloating;

	private static string $defaultCurrency = 'Kč';

	private ?string $currency = null;


	public static function setDefaultCurrency(string $currency): void
	{
		self::$defaultCurrency = $currency;
	}


	public static function getDefaultCurrency(): string
	{
		return self::$defaultCurrency;
	}


	public function setCurrency(string $currency): self
	{
		$this->currency = $currency;

		return $this;
	}


	public function getCurrency(): ?string
	{
		return $this->currency ?? self::$defaultCurrency;
	}


	public function getControl(): Html
	{
		$currency = $this->getCurrency();

		if($currency)
		{
			$this->setAppend($currency);
		}

		$input = parent::getControl();

		$value = $this->getValue();

		if($value !== null)
		{
			$input->value(number_format((int) $value, 0, '.', "\u{00A0}"));
		}

		$input->setAttribute('inputmode', 'numeric');
		$input->setAttribute('data-currency-input', 'true');

		return $input;
	}


	public function getValue(): mixed
	{
		$value = parent::getValue();

		if($value === null || $value === '')
		{
			return null;
		}

		$cleaned = str_replace([' ', "\xc2\xa0", "\xe2\x80\x8f"], '', (string) $value);

		return $cleaned === '' ? null : (int) $cleaned;
	}
}
