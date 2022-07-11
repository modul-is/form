<?php

declare(strict_types=1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait InputGroup
{
	public ?string $append = null;

	public ?string $prepend = null;

	public ?string $appendClass = null;

	public ?string $prependClass = null;


	public function getPrepend(): ?Html
	{
		if(!$this->prepend)
		{
			return null;
		}

		return Html::el('span')
			->class('input-group-text' . ($this->prependClass ? ' ' . $this->prependClass : null))
			->addHtml($this->prepend);
	}


	public function getAppend(): ?Html
	{
		if(!$this->append)
		{
			return null;
		}

		return Html::el('span')
			->class('input-group-text' . ($this->appendClass ? ' ' . $this->appendClass : null))
			->addHtml($this->append);
	}


	public function setAppend(string|\Stringable $text, string $class = null): self
	{
		$this->append = $text instanceof \Stringable ? (string) $text : $text;

		$this->appendClass = $class;

		return $this;
	}


	public function setPrepend(string|\Stringable $text, string $class = null): self
	{
		$this->prepend = $text instanceof \Stringable ? (string) $text : $text;

		$this->prependClass = $class;

		return $this;
	}


	public function setIcon(string $icon): self
	{
		$this->prepend = \Kravcik\LatteFontAwesomeIcon\Extension::render($icon)->toHtml();

		return $this;
	}
}
