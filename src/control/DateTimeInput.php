<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class DateTimeInput extends \Nette\Forms\Controls\DateTimeControl implements Renderable, Signalable, HasInputGroup, \Nette\Application\UI\SignalReceiver
{
	use Helper\InputGroup;
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
	use Helper\RenderFloating;
	use Helper\RenderInline;
	use Helper\WrapControl;


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
