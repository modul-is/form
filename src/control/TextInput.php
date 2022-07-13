<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class TextInput extends \Nette\Forms\Controls\TextInput implements Renderable, \Nette\Application\UI\SignalReceiver
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\RenderFloating;
	use Helper\Validation;
	use Helper\Signals;
	use Helper\WrapClass;
	use Helper\RenderInline;
	use Helper\ControlClass;

	public function render(): Html|string
	{
		if($this->getOption('hide') || $this->autoRenderSkip)
		{
			return '';
		}

		if($this->getOption('template'))
		{
			return (new \Latte\Engine)->renderToString($this->getOption('template'), $this);
		}

		/** @var \ModulIS\Form\Form $form */
		$form = $this->getForm();

		if($this->getRenderFloating() ?? $form->getRenderFloating())
		{
			$outerDiv = $this->renderFloating();
		}
		else
		{
			$outerDiv = $this->renderBasic();
		}

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}
