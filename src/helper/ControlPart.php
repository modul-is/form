<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Control\CheckboxList;
use ModulIS\Form\Control\RadioList;
use Nette\Utils\Html;

trait ControlPart
{
	public string $controlClass = 'form-control';


	/**
	 * {inputCore name:key} - with a key, lists render a single item
	 */
	public function getCoreControlPart(string|int|null $key = null): Html|string
	{
		if($key !== null && ($this instanceof RadioList || $this instanceof CheckboxList))
		{
			return $this->renderItem($key);
		}

		return $this->getCoreControl();
	}


	/**
	 * {labelCore name:key} - with a key, lists render the label of a single item
	 */
	public function getCoreLabelPart(string|int|null $key = null): Html|string|null
	{
		if($key !== null && ($this instanceof RadioList || $this instanceof CheckboxList))
		{
			return $this->getLabelPart($key);
		}

		return $this->getCoreLabel();
	}
}
