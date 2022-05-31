<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait FloatingLabel
{
	public ?bool $floatingLabel = null;
	
	
	public function setFloatingLabel(bool $floatingLabel): void
	{
		$this->floatingLabel = $floatingLabel;
	}
	
	
	public function getFloatingLabel(): ?bool
	{
		return $this->floatingLabel;
	}
}
