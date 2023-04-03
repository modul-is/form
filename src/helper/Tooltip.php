<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use \Nette\Utils\Html;

trait Tooltip
{
	protected string|Html|null $tooltip = null;


	public function setTooltip($text)
	{
		$this->tooltip = $text;
		return $this;
	}


	public function getTooltip(): string|Html|null
	{
		return $this->tooltip;
	}
}