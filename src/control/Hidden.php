<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

class Hidden extends \Nette\Forms\Controls\HiddenField
{
	public function getCoreControl()
	{
		return $this->getControl();
	}
}
