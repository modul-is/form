<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class MultiSelectBox extends \Nette\Forms\Controls\MultiSelectBox
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Input;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputRender;
	use Helper\AutoRenderSkip;
	
	
	public function render()
	{
		if($this->getOption('hide'))
		{
			return null;
		}
		
		$label = $this->getCoreLabel();
		
		$labelDiv = Html::el('div')
			->class('col-sm-4 control-label align-self-center')
			->addHtml($label);
		
		$input = $this->getCoreControl();
		
		$inputDiv = Html::el('div')
			->class('col-sm-8')
			->addHtml($input);
		
		$outerDiv = Html::el('div')
			->class('form-group row')
			->addHtml($labelDiv . $inputDiv);
		
		if($input->getOption('id'))
		{
			$outerDiv->id($input->getOption('id'));
		}
		
		return $outerDiv;
	}
}
