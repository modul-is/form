<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait ControlPart
{
	public function getCoreControlPart()
	{
		return $this->getCoreControl();
	}


	public function getCoreLabelPart()
	{
		return $this->getCoreLabel();
	}
}