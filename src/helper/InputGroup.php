<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Nette\Utils\Html;

trait InputGroup
{
	public string $append = '';

	public string $prepend = '';

	public string $appendClass = '';

	public string $prependClass = '';


	public function getPrepend(): ?Html
	{
		$prepend = Html::el('span')
			->class('input-group-text' . ($this->prependClass ? ' ' . $this->prependClass : null))
			->addHtml($this->prepend);

		if(!empty($this->renderFloating) && $this->tooltip)
		{
			$tooltip = Html::el('span')
				->title($this->tooltip)
				->class('input-group-text')
				->addAttributes(['data-bs-placement' => 'right', 'data-bs-toggle' => 'tooltip'])
				->addHtml(\Kravcik\LatteFontAwesomeIcon\Extension::render('question-circle', color: 'blue'));

			return Html::el()->addHtml($this->prepend ? $tooltip . $prepend : $tooltip);
		}

		if(!$this->prepend)
		{
			return null;
		}

		return $prepend;
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


	public function setAppend(string|\Stringable $text, string $class = ''): self
	{
		$this->append = $text instanceof \Stringable ? (string) $text : $text;

		$this->appendClass = $class;

		return $this;
	}


	public function setPrepend(string|\Stringable $text, string $class = ''): self
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
