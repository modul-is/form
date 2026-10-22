<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait ButtonIcon
{
	protected ?string $icon = null;

	protected string $iconPosition = 'start';


	/**
	 * @param ?string $position start (before the caption) or end
	 */
	public function setIcon(string $icon, ?string $position = null): static
	{
		$this->icon = $icon;

		if($position !== null)
		{
			$this->iconPosition = in_array($position, ['end', 'float-end'], true) ? 'end' : 'start';
		}

		return $this;
	}


	public function getIcon(): ?string
	{
		return $this->icon;
	}


	public function getIconPosition(): string
	{
		return $this->iconPosition;
	}
}
