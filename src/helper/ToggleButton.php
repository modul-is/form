<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait ToggleButton
{
	protected bool $toggleButton = false;

	protected string|array $buttonColor = 'outline-primary';


	public function setToggleButton(bool $toggleButton = true): static
	{
		$this->toggleButton = $toggleButton;

		return $this;
	}


	public function setButtonColor(string|array $color = 'primary'): static
	{
		$this->buttonColor = $color;

		return $this;
	}
}
