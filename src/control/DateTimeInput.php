<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class DateTimeInput extends \Nette\Forms\Controls\DateTimeControl implements InputField, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\Signals;

	private bool $picker = false;

	private int $dateType;


	/**
	 * @param self::TypeDate|self::TypeTime|self::TypeDateTime $type
	 */
	public function __construct
	(
		string|\Stringable|null $label = null,
		int $type = self::TypeDate,
		bool $withSeconds = false
	)
	{
		parent::__construct($label, $type, $withSeconds);

		$this->dateType = $type;
	}


	/**
	 * Bootstrap datepicker instead of the native date input of the browser (date only, not time)
	 */
	public function setPicker(bool $picker = true): static
	{
		if($picker && $this->dateType !== self::TypeDate)
		{
			throw new \Nette\InvalidStateException('Datepicker is available for addDate() only, input "' . $this->getName() . '".');
		}

		$this->picker = $picker;

		return $this;
	}


	public function isPicker(): bool
	{
		return $this->picker;
	}


	/**
	 * Limit of the Min / Max rule in Y-m-d for the datepicker
	 */
	public function getPickerLimit(string $validator): ?string
	{
		foreach($this->getRules() as $rule)
		{
			if($rule->validator === $validator && $rule->control === $this && !$rule->isNegative && $rule->arg !== null && $rule->arg !== '')
			{
				$limit = $this->convertToValueFormat($rule->arg);

				return $limit instanceof \DateTimeInterface ? $limit->format('Y-m-d') : (is_string($limit) ? substr($limit, 0, 10) : null);
			}
		}

		return null;
	}


	/**
	 * Nette compares Min/Max arguments with getValue() as they are, so e.g. addDate() (value 'Y-m-d')
	 * with a DateTime limit always failed - arguments are converted to the same representation as the value
	 */
	public function validate(): void
	{
		$this->normalizeLimitArgs($this->getRules());

		parent::validate();
	}


	private function normalizeLimitArgs(\Nette\Forms\Rules $rules): void
	{
		foreach($rules as $rule)
		{
			if($rule->branch)
			{
				$this->normalizeLimitArgs($rule->branch);
			}

			if(($rule->validator === \Nette\Forms\Form::Min || $rule->validator === \Nette\Forms\Form::Max)
				&& $rule->control === $this && $rule->arg !== null && $rule->arg !== '')
			{
				$rule->arg = $this->convertToValueFormat($rule->arg);
			}
		}
	}


	/**
	 * Uses setValue()/getValue() so the limit gets exactly the same normalization (type, format) as the value
	 */
	private function convertToValueFormat(mixed $limit): mixed
	{
		$value = $this->value;

		try
		{
			return $this->setValue($limit)->getValue();
		}
		catch(\Throwable)
		{
			return $limit;
		}
		finally
		{
			$this->value = $value;
		}
	}
}
