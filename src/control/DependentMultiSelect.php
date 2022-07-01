<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

use Nette\Utils\Html;

class DependentMultiSelect extends \NasExt\Forms\Controls\DependentMultiSelectBox implements Renderable
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\WrapClassHelper;
	use Helper\RenderInlineHelper;

	public function __construct($label = null, array $parents = [], callable $dependentCallback = null)
	{
		parent::__construct($label, $parents);

		$this->setDependentCallback($dependentCallback);
	}


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
		$input = $this->getCoreControl();
		
		$labelClass = 'align-self-center ' . ($this->labelClass ?? 'col-sm-4');
		$inputClass = 'align-self-center ' . ($this->inputClass ?? 'col-sm-8');
		$wrapClass = 'mb-3 ' . ($this->wrapClass ?? 'col-12');

		$labelDiv = Html::el('div')
			->class($labelClass)
			->addHtml($label);

		$inputDiv = Html::el('div')
			->class($inputClass)
			->addHtml($input);

		$rowDiv = Html::el('div')
			->class('row')
			->addHtml($labelDiv . $inputDiv);
		
		$outerDiv = Html::el('div')
			->class($wrapClass)
			->addHtml($rowDiv);

		if($this->getOption('id'))
		{
			$outerDiv->id($this->getOption('id'));
		}

		return $outerDiv;
	}
}
