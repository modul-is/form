<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait AutoRenderSkip
{
	protected bool $autoRenderSkip = false;


	public function setAutoRenderSkip(bool $autoRenderSkip = true): static
	{
		$this->autoRenderSkip = $autoRenderSkip;

		return $this;
	}
}
