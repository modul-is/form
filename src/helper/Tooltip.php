<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use Kravcik\LatteFontAwesomeIcon\Extension;
use Nette\Utils\Html;

trait Tooltip
{
	protected ?string $tooltip = null;


	public function setTooltip($text)
	{
		$this->tooltip = $text;
		return $this;
	}


	public function getTooltip(): ?string
	{
		return $this->tooltip;
	}


	/**
	 * Ikona s napovedou k prvku - sdili ji Label::getCoreLabel() i RenderDefault::renderDefault(),
	 * aby tooltip fungoval ve vsech typech vykresleni.
	 */
	public function getTooltipHtml(): ?Html
	{
		if(!$this->tooltip)
		{
			return null;
		}

		return Html::el('span')
			->title($this->tooltip)
			->addAttributes(['data-bs-placement' => 'right', 'data-bs-toggle' => 'tooltip', 'data-bs-html' => 'true'])
			->addHtml(Extension::render('question-circle', color: 'blue'));
	}
}
