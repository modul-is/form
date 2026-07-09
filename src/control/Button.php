<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Kravcik\LatteFontAwesomeIcon\Extension;
use ModulIS\Form\Dial\SignalDial;
use ModulIS\Form\Helper;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;
use Nette\Utils\Html;

class Button extends \Nette\Forms\Controls\Button implements Renderable, Signalable
{
	use Helper\Icon;
	use Helper\Color;
	use Helper\AutoRenderSkip;
	use Helper\ControlClass;
	use Helper\ButtonRounded;
	use Helper\RenderBasic;

	protected $onChangeCallback;


	public function setOnChangeCallback(callable $callback): static
	{
		$this->onChangeCallback = $callback;

		return $this;
	}


	public function getOnChangeCallback()
	{
		return $this->onChangeCallback;
	}


	public function hasSignal(): bool
	{
		return is_callable($this->onChangeCallback);
	}


	public function signalReceived($signal): void
	{
		$presenter = $this->lookup(Presenter::class);

		if($signal !== SignalDial::OnClick)
		{
			throw new \Exception("Unknown signal '$signal' for button '" . $this->getName() . "'");
		}

		$formData = $presenter->getParameter('formdata');

		if(!$formData)
		{
			return;
		}

		$currentValues = [];

		parse_str($formData, $currentValues);

		call_user_func_array($this->onChangeCallback, [array_filter($currentValues)]);
	}


	public function addSignalsToInput(Html &$input): void
	{
		if(!empty($this->onChangeCallback))
		{
			$presenter = $this->lookup(Presenter::class);

			$input->setAttribute('data-on-click', $presenter->link($this->lookupPath(Presenter::class) . IComponent::NameSeparator . SignalDial::OnClick . '!'));
		}
	}


	public function getCoreControl(): Html|string
	{
		$input = $this->getControl();

		$label = $this->getCaption();

		$color = $this->color ?? '';

		$button = Html::el('button')
			->name($this->getName())
			->type('button')
			->appendAttribute('class', 'btn-' . $color)
			->appendAttribute('class', (string) $input->getAttribute('class'))
			->appendAttribute('class', 'new-design-btn');

		$button->addHtml($this->icon ? Extension::render($this->icon) : '')
			->addHtml($this->translate($label));

		if($this->getOption('id'))
		{
			$button->id($this->getOption('id'));
		}

		foreach($input->attrs as $name => $value)
		{
			if(in_array($name, ['name', 'required', 'data-nette-rules', 'class'], true))
			{
				continue;
			}

			$button->$name = $value;
		}

		$this->addSignalsToInput($button);

		return $button;
	}


	public function render(): Html|string
	{
		return $this->getCoreControl();
	}
}
