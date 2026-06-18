<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper\Render;
use Nette\Utils\Html;

class Hidden extends \Nette\Forms\Controls\HiddenField implements Renderable
{
	use Render;

	public function getCoreControl(): Html
	{
		return $this->getControl();
	}


	public function render(): Html
	{
		return $this->getCoreControl();
	}
}
