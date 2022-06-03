<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait Tooltip
{
	public ?string $tooltip = null;


	public function setTooltip($text)
	{
		$this->tooltip = $text;
		return $this;
	}
}
