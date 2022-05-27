<?php

namespace ModulIS\Form\Control;

namespace ModulIS\Form\Control;

class Hidden extends \Nette\Forms\Controls\HiddenField
{
	public function getCoreControl()
	{
		return $this->getControl();
	}
}
