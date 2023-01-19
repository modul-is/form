<?php

namespace ModulIS\Form\Helper;

trait ToggleButton {
	protected array $outlineColorArray = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

	protected bool $toggleButton = false;

	protected string $outlineColor = 'primary';


	public function setToggleButton(bool $toggleButton = true): self
	{
		$this->toggleButton = $toggleButton;

		return $this;
	}


	public function setOutlineColor(string $color = 'primary'): self
	{
		if(in_array($color, $this->outlineColorArray, true))
		{
			$this->outlineColor = $color;
		}

		return $this;
	}
}
