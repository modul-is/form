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

	private bool $switch = false;

	private ?string $checkboxClass = null;


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
}
