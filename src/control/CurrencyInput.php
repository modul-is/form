<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class CurrencyInput extends \Nette\Forms\Controls\TextInput implements InputField, Signalable, Helper\QuickCopyable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\QuickCopy;
	use Helper\Signals;

	private static string $defaultCurrency = 'Kč';

	private static int $defaultDecimals = 0;

	private ?string $currency = null;

	private ?int $decimals = null;


	public static function setDefaultCurrency(string $currency): void
	{
		self::$defaultCurrency = $currency;
	}


	public static function getDefaultCurrency(): string
	{
		return self::$defaultCurrency;
	}


	/**
	 * Number of decimal places for all currency inputs (e.g. 2 for cents)
	 */
	public static function setDefaultDecimals(int $decimals): void
	{
		self::$defaultDecimals = self::checkDecimals($decimals);
	}


	public static function getDefaultDecimals(): int
	{
		return self::$defaultDecimals;
	}


	public function setCurrency(string $currency): static
	{
		$this->currency = $currency;

		return $this;
	}


	public function getCurrency(): ?string
	{
		return $this->currency ?? self::$defaultCurrency;
	}


	public function setDecimals(int $decimals): static
	{
		$this->decimals = self::checkDecimals($decimals);

		return $this;
	}


	public function getDecimals(): int
	{
		return $this->decimals ?? self::$defaultDecimals;
	}


	public function getControl(): Html
	{
		$currency = $this->getCurrency();

		if($currency && !$this->getAppend())
		{
			$this->setAppend($currency);
		}

		$input = parent::getControl();

		$value = $this->getValue();
		$decimals = $this->getDecimals();

		if($value !== null)
		{
			$input->value(number_format($value, $decimals, ',', "\u{00A0}"));
		}

		$input->setAttribute('inputmode', $decimals > 0 ? 'decimal' : 'numeric');
		$input->setAttribute('data-currency-input', 'true');

		if($decimals > 0)
		{
			$input->setAttribute('data-currency-decimals', $decimals);
		}

		return $input;
	}


	/**
	 * Without decimals returns int, otherwise float rounded to the set number of decimals
	 */
	public function getValue(): int|float|null
	{
		$value = parent::getValue();

		if($value === null || $value === '')
		{
			return null;
		}

		$number = is_int($value) || is_float($value)
			? (float) $value
			: self::parseNumber((string) $value);

		if($number === null)
		{
			return null;
		}

		$decimals = $this->getDecimals();

		return $decimals === 0 ? (int) round($number) : round($number, $decimals);
	}


	/**
	 * Both decimal comma and decimal point are accepted, the last one of them is the decimal separator
	 * (so pasted "1,234.56" and "1.234,56" both work)
	 */
	private static function parseNumber(string $value): ?float
	{
		/**
		 * Space, no-break space and narrow no-break space (thousands separator of cs locale)
		 */
		$cleaned = str_replace([' ', "\xc2\xa0", "\xe2\x80\xaf", ','], ['', '', '', '.'], $value);
		$lastSeparator = strrpos($cleaned, '.');

		if($lastSeparator !== false)
		{
			$cleaned = str_replace('.', '', substr($cleaned, 0, $lastSeparator)) . substr($cleaned, $lastSeparator);
		}

		return is_numeric($cleaned) ? (float) $cleaned : null;
	}


	private static function checkDecimals(int $decimals): int
	{
		if($decimals < 0)
		{
			throw new \Nette\InvalidArgumentException('Number of decimals must not be negative, ' . $decimals . ' given.');
		}

		return $decimals;
	}
}
