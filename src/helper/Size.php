<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait Size
{
	protected ?string $size = null;


	/**
	 * Bootstrap size - sm or lg, null means inherited from the form (Form::setSize())
	 */
	public function setSize(?string $size): static
	{
		if($size !== null && !in_array($size, ['sm', 'lg'], true))
		{
			throw new \Nette\InvalidArgumentException("Size has to be 'sm' or 'lg', '$size' given.");
		}

		$this->size = $size;

		return $this;
	}


	public function getSize(): ?string
	{
		return $this->size;
	}
}
