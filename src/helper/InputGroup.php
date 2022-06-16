<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait InputGroup
{
	public ?string $append = null;

	public ?string $prepend = null;


	public function setAppend(string $text)
	{
		$this->append = $text;

		return $this;
	}


	public function setPrepend(string $text)
	{
		$this->prepend = $text;

		return $this;
	}


	public function setIcon(string $icon)
	{
		$this->prepend = \Kravcik\LatteFontAwesome\IconExtenstion::renderIcon($icon, [])->toHtml();

		return $this;
	}
}
