<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Dial\SignalDial;
use ModulIS\Form\Helper;
use Nette\Application\UI\Presenter;

class DependentSelect extends \Nette\Forms\Controls\SelectBox implements Renderable, HasInputGroup, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\InputGroup;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputCoreControl;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\ControlClass;
	use Helper\RenderBasic;
	use Helper\RenderDefault;
	use Helper\RenderFloating;
	use Helper\RenderInline;
	use Helper\WrapControl;
	use Helper\Signals
	{
		signalReceived as public signalsSignalReceived;
	}
	use Helper\Dependent;

	/**
	 * @param array<\Nette\Forms\Controls\BaseControl> $parents
	 * @param ?callable(array<string, mixed>): \ModulIS\Form\Helper\DependentData $dependentCallback
	 */
	public function __construct
	(
		string|\Stringable|null $label = null,
		array $parents = [],
		?callable $dependentCallback = null
	)
	{
		$this->controlClass = 'form-select';
		$this->parents = $parents;

		if($dependentCallback)
		{
			$this->setDependentCallback($dependentCallback);
		}

		parent::__construct($label);
	}


	public function signalReceived(string $signal): void
	{
		$presenter = $this->lookup(Presenter::class);

		if($signal === SignalDial::Load)
		{
			$this->sendDependentPayload($presenter);
		}
		else
		{
			$this->signalsSignalReceived($signal);
		}
	}
}
