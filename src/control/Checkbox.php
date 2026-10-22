<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class Checkbox extends \Nette\Forms\Controls\Checkbox implements Field, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Tooltip;
	use Helper\Description;
	use Helper\Render;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\ControlClass;
	use Helper\Signals;
	use Helper\WrapControl;
	use Helper\Color;
	use Helper\Size;

	private bool $switch = false;

	private ?string $checkboxClass = null;

	private bool $indeterminate = false;


	public function setSwitch(bool $switch = true): static
	{
		$this->switch = $switch;

		return $this;
	}


	public function isSwitch(): bool
	{
		return $this->switch;
	}


	/**
	 * Class of the form-field element around the checkbox and its label
	 */
	public function setCheckboxWrapClass(string $class): static
	{
		$this->checkboxClass = $class;

		return $this;
	}


	public function getCheckboxWrapClass(): ?string
	{
		return $this->checkboxClass;
	}


	/**
	 * Mixed state (neither checked nor unchecked) - visual only, set by JS, the value is still a bool
	 */
	public function setIndeterminate(bool $indeterminate = true): static
	{
		$this->indeterminate = $indeterminate;

		return $this;
	}


	public function isIndeterminate(): bool
	{
		return $this->indeterminate;
	}
}
