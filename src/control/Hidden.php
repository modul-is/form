<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class Hidden extends \Nette\Forms\Controls\HiddenField implements Renderable
{
	public function getCoreControl(): Html
	{
		return $this->getControl();
	}


	public function render(): Html
	{
		return $this->getCoreControl();
	}
}
