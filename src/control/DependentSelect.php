<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Dial\SignalDial;
use ModulIS\Form\Helper;
use Nette\Application\UI\Presenter;

class DependentSelect extends \Nette\Forms\Controls\SelectBox implements InputField, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
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
