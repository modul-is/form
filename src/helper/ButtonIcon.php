<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait ButtonIcon
{
	protected ?string $icon = null;

	protected ?string $iconPosition = 'float-start';


	public function setIcon(string $icon, ?string $position = null): self
	{
		$this->icon = $icon;

		if($position)
		{
			$this->iconPosition = $position;
		}

		return $this;
	}
}
