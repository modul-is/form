<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class SelectBox extends \Nette\Forms\Controls\SelectBox implements Renderable
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Input;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\FloatingLabel;

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

		$label = $this->getCoreLabel();

		$labelDiv = Html::el('div')
			->class('col-sm-4 control-label align-self-center')
			->addHtml($label);

		$input = $this->getCoreControl();

		$inputDiv = Html::el('div')
			->class('col-sm-8')
			->addHtml($input);
		
		$floatingLabel = $this->getFloatingLabel();
		
		/**
		 * If floating label not set - take it from form
		 */
		if($floatingLabel === null)
		{
			$floatingLabel = $this->getForm()->getFloatingLabel();
		}

		$outerDiv = Html::el('div')
			->class('form-group row' . ($floatingLabel ? ' form-floating' : ''))
			->addHtml($labelDiv . $inputDiv);

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}
