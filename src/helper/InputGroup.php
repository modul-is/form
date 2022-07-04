<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

trait InputGroup
{
	public ?string $append = null;

	public ?string $prepend = null;


	public function setAppend(string $text): self
	{
		$this->append = $text;

		return $this;
	}


	public function setPrepend(string $text): self
	{
		$this->prepend = $text;

		return $this;
	}


	public function setIcon(string $icon): self
	{
		$this->prepend = \Kravcik\LatteFontAwesomeIcon\Extension::render($icon)->toHtml();

		return $this;
	}
}
