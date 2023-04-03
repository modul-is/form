<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait Tooltip
{
	protected string|\Nette\Utils\Html|null $tooltip = null;


	public function setTooltip($text)
	{
		$this->tooltip = $text;
		return $this;
	}


	public function getTooltip(): string|\Nette\Utils\Html|null
	{
		return $this->tooltip;
	}
}