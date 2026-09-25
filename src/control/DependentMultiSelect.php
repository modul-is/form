<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Application\UI\Presenter;

class DependentMultiSelect extends \Nette\Forms\Controls\MultiSelectBox implements Renderable, HasInputGroup, Signalable, \Nette\Application\UI\SignalReceiver
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
	use Helper\WrapControl;
	use Helper\RenderBasic;
	use Helper\RenderDefault;
	use Helper\RenderFloating;
	use Helper\RenderInline;
	use Helper\Signals
	{
		signalReceived as public signalsSignalReceived;
	}
	use Helper\Dependent;

	private ?string $prompt = null;


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


	public function loadHttpData(): void
	{
		parent::loadHttpData();

		$parentsValues = [];

		foreach($this->parents as $parent)
		{
			$parentsValues[$parent->getName()] = $parent->getValue();
		}

		$data = $this->getDependentData([$parentsValues]);
		$this->setItems($data->getItems());
	}


	/**
	 * Same as Dependent::getValue(), which cannot be used directly - MultiSelectBox narrows the return type to array
	 * @return array<int|string>
	 */
	public function getValue(): array
	{
		$this->tryLoadItems();

		if(is_array($this->tempValue) && $this->tempValue !== [])
		{
			return $this->tempValue;
		}

		return parent::getValue();
	}


	public function signalReceived(string $signal): void
	{
		$presenter = $this->lookup(Presenter::class);

		if($signal === \ModulIS\Form\Dial\SignalDial::Load)
		{
			$this->sendDependentPayload($presenter);
		}
		else
		{
			$this->signalsSignalReceived($signal);
		}
	}


	public function setPrompt(string $prompt): static
	{
		$this->prompt = $prompt;

		return $this;
	}


	public function getPrompt(): ?string
	{
		return $this->prompt;
	}
}
