<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class Link extends \Nette\Forms\Controls\BaseControl implements Renderable
{
	use Helper\ButtonIcon;
	use Helper\ButtonVariant;
	use Helper\Color;
	use Helper\AutoRenderSkip;
	use Helper\ControlClass;
	use Helper\Render;

	protected ?string $link = null;


	public function __construct
	(
		string|\Stringable|null $caption = null
	)
	{
		parent::__construct($caption);

		/**
		 * Link carries no value - same as Nette buttons, it must not appear in form values
		 */
		$this->setOmitted();
	}


	public function getControl(): Html
	{
		$control = parent::getControl();
		\assert($control instanceof Html);

		return $control;
	}


	public function getLabel(string|\Stringable|null $caption = null)
	{
		return $this->translate($caption ?? $this->getCaption());
	}


	public function setLink(string $link): static
	{
		$this->link = $link;

		return $this;
	}


	public function getLink(): ?string
	{
		return $this->link;
	}
}
