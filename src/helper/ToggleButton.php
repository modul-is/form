<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait ToggleButton
{
	protected array $outlineColorArray = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

	protected bool $toggleButton = false;

	protected string|array $outlineColor = 'primary';


	public function setToggleButton(bool $toggleButton = true): self
	{
		$this->toggleButton = $toggleButton;

		return $this;
	}


	public function setOutlineColor(string|array $color = 'primary'): self
	{
		$this->outlineColor = $color;

		return $this;
	}
}
