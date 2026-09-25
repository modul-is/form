<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait ControlPart
{
	public string $controlClass = 'form-control';


	public function getCoreControlPart(): Html|string
	{
		return $this->getCoreControl();
	}


	public function getCoreLabelPart(): Html|string|null
	{
		return $this->getCoreLabel();
	}
}
