<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;
use ModulIS\Form\Helper;

class Link extends \Nette\Forms\Controls\BaseControl implements Renderable
{
	use Helper\Icon;
	use Helper\Color;
	use Helper\AutoRenderSkip;
	use Helper\ControlClass;

	protected string|null $link = null;


	public function getLabel($caption = null)
	{
		return $this->translate($caption ?? $this->caption);
	}


	public function getControl(): Html
	{
		$control = parent::getControl();
		
		$currentClass = $control->getAttribute('class') ? ' ' . $control->getAttribute('class') : '';
		$this->setOption('rendered', true);

		$btnColor = $this->color ? ' btn-' . $this->color : ' btn-default';
		$btnIcon = $this->icon ? Html::el('span')->class('fal fa-' . $this->icon) : null;

		$el = Html::el('a');
		$el->href($this->link);
		$el->setHtml(trim($btnIcon . ' ' . $this->caption));
		$el->class('btn' . $btnColor . $currentClass);
		return $el;
	}


	public function render(): Html
	{
		return $this->getCoreControl();
	}


	public function getCoreControl(): Html
	{
		return $this->getControl();
	}


	public function setLink(string $link): self
	{
		$this->link = $link;
		return $this;
	}
}
