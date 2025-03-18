<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Application\UI\Presenter;

class DependentMultiSelect extends \Nette\Forms\Controls\MultiSelectBox implements Renderable, \Nette\Application\UI\SignalReceiver
{
	use Helper\InputGroup;
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputCoreControl;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\WrapControl;
	use Helper\RenderInline;
	use Helper\RenderBasic;
	use Helper\Dependent;

	private ?string $prompt = null;


	public function __construct($label = null, array $parents = [], ?callable $dependentCallback = null)
	{
		$this->parents = $parents;

		if($dependentCallback)
		{
			$this->setDependentCallback($dependentCallback);
		}

		parent::__construct($label);
	}


	public function getValue(): array
	{
		return $this->getValue();
	}


	public function signalReceived($signal): void
	{
		$presenter = $this->lookup(Presenter::class);
		\assert($presenter instanceof Presenter);

		if($signal === \ModulIS\Form\Dial\SignalDial::Load)
		{
			$parentsNames = [];

			foreach($this->parents as $parent)
			{
				$value = $presenter->getParameter($this->getNormalizeName($parent));

				$parent->setValue($value);

				$parentsNames[$parent->getName()] = $parent->getValue();
			}

			$data = $this->getDependentData([$parentsNames]);

			/** @phpstan-ignore-next-line*/
			$items = $data->getPreparedItems(is_array($this->disabled) ? $this->disabled : []);

			$presenter->payload->dependentselectbox = [
				'id' => $this->getHtmlId(),
				'items' => $items,
				'value' => $data->getValue(),
				'prompt' => $this->translate($data->getPrompt()),
				'disabledWhenEmpty' => $this->disabledWhenEmpty
			];

			$presenter->sendPayload();
		}
	}


	public function setPrompt(string $prompt)
	{
		$this->prompt = $prompt;
	}


	public function getPrompt(): ?string
	{
		return $this->prompt;
	}
}
