<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class Link extends \Nette\Forms\Controls\BaseControl
{
	use Helper\Icon;
	use Helper\Color;
	use Helper\AutoRenderSkip;

	protected string|null $link = null;


	public function getLabel($caption = null)
	{
		return $this->translate($caption ?? $this->caption);
	}


	public function getControl()
	{
		$this->setOption('rendered', true);

		$btnColor = $this->color ? ' btn-' . $this->color : ' btn-default';
		$btnIcon = $this->icon ? Html::el('span')->class('fal fa-' . $this->icon) : null;

		$el = Html::el('a');
		$el->href($this->link);
		$el->setHtml(trim($btnIcon . ' ' . $this->caption));
		$el->class('btn' . $btnColor);
		return $el;
	}


	public function render()
	{
		return $this->getCoreControl();
	}


	public function getCoreControl()
	{
		return $this->getControl();
	}


	public function setLink(string $link): self
	{
		$this->link = $link;
		return $this;
	}
}
