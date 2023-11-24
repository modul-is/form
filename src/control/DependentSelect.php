<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Application\UI\Presenter;

class DependentSelect extends \Nette\Forms\Controls\SelectBox implements Renderable, FloatingRenderable, Signalable
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
	use Helper\Dependent;

	public function __construct($label = null, array $parents = [], callable $dependentCallback = null)
	{
		$this->controlClass = 'form-select';
		$this->parents = $parents;
		$this->setDependentCallback($dependentCallback);

		parent::__construct($label);
	}


	public function signalReceived($signal): void
	{
		$presenter = $this->lookup(Presenter::class);
		\assert($presenter instanceof Presenter);

		if($signal === \ModulIS\Form\Dial\SignalDial::OnChange)
		{
			$value = $presenter->getParameter('value');
			$inputName = $presenter->getParameter('input');

			call_user_func_array($this->onChange, [$value, $inputName]);
		}
		else
		{
			parent::signalReceived($signal);
		}
	}
}
