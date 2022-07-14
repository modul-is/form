<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait ControlPart
{
	public string $controlClass = 'form-control';


	public function getCoreControlPart()
	{
		return $this->getCoreControl();
	}


	public function getCoreLabelPart()
	{
		return $this->getCoreLabel();
	}
}
