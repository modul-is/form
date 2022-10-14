<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class DependentSelect extends \NasExt\Forms\Controls\DependentSelectBox implements Renderable, FloatingRenderable, Signalable
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputCoreControl;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\RenderFloating;
	use Helper\Validation;
	use Helper\WrapControl;
	use Helper\RenderInline;
	use Helper\ControlClass;
	use Helper\RenderBasic;
	use Helper\Signals;

	public function __construct($label = null, array $parents = [], callable $dependentCallback = null)
	{
		parent::__construct($label, $parents);

		$this->setDependentCallback($dependentCallback);

		$this->controlClass = 'form-select';
	}


	public function signalReceived($signal): void
	{
		/** @var \Nette\Application\UI\Presenter $presenter */
		$presenter = $this->lookup('Nette\\Application\\UI\\Presenter');

		if($signal === $this->onChangeSignal)
		{
			$value = $presenter->getParameter('value');
			$inputName = $presenter->getParameter('input');

			call_user_func_array($this->onChange, [$value, $inputName]);

			$presenter->sendPayload();
		}
		else
		{
			parent::signalReceived($signal);
		}
	}
}
