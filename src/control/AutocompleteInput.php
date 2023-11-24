<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;
use ModulIS\Form\Dial\SignalDial;
use Nette\Application\UI\Presenter;

class AutocompleteInput extends \Nette\Forms\Controls\TextInput implements Renderable, FloatingRenderable, Signalable, \Nette\Application\UI\SignalReceiver
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
	use Helper\Signals;
	use Helper\WrapControl;
	use Helper\RenderInline;
	use Helper\ControlClass;
	use Helper\RenderBasic;


	public array|\Closure|null $onChangeCallback = null;

	public array|\Closure|null $onSelectCallback = null;

	private array $parents = [];

	private int $delay = 200;

	private ?string $prompt = null;

	private ?array $items;


	public function __construct($label = null, ?int $maxLength = null, ?array $items = null)
	{
		parent::__construct($label, $maxLength);

		if(isset($items))
		{
			$this->items = $items;
		}
	}


	public function setPrompt(string $prompt): self
	{
		$this->prompt = $prompt;

		return $this;
	}


	public function setOnChangeCallback(array|\Closure $callback): self
	{
		$this->onChangeCallback = $callback;

		return $this;
	}


	public function setOnSelectCallback(array|\Closure $callback): self
	{
		$this->onSelectCallback = $callback;

		return $this;
	}


	public function setParents(array $parents): self
	{
		$this->parents = $parents;

		return $this;
	}


	public function signalReceived($signal): void
	{
		$presenter = $this->lookup(Presenter::class);
		\assert($presenter instanceof Presenter);

		if($signal === SignalDial::OnChange)
		{
			if(!is_callable($this->onChangeCallback))
			{
				throw new \Nette\InvalidStateException('On change callback not set.');
			}

			$parentArray = [];

			if($presenter->getParameter('parent'))
			{
				$parentValueArray = $presenter->getParameter('parent');

				foreach($this->parents as $parent)
				{
					$parentArray[$parent->getName()] = $parentValueArray[$this->getNormalizeName($parent)];
				}
			}
			$data = call_user_func_array($this->onChangeCallback, [$presenter->getParameter('param'), $parentArray]);

			if(!is_array($data))
			{
				throw new \Nette\InvalidStateException('Callback for:"' . $this->getHtmlId() . '" must return array!');
			}

			$presenter->payload->suggestions = $this->prepareData($data);

			$presenter->sendPayload();
		}
		elseif($signal === SignalDial::OnSelect)
		{
			if(!is_callable($this->onSelectCallback))
			{
				throw new \Nette\InvalidStateException('OnSelect callback not set.');
			}

			$currentValues = [];

			parse_str($presenter->getParameter('formdata'), $currentValues);

			call_user_func_array($this->onSelectCallback, [$presenter->getParameter('selected'), array_filter($currentValues)]);

			/**
			 * If there is no snippet to redraw -> send empty response
			 */
			if(!$presenter->isControlInvalid())
			{
				$presenter->sendResponse(new \Nette\Application\Responses\TextResponse(null));
			}
		}
		elseif($signal === SignalDial::OnFocusOut)
		{
			$value = $presenter->getParameter('value');
			$inputName = $presenter->getParameter('input');

			$currentValues = [];

			parse_str($presenter->getParameter('formdata'), $currentValues);

			call_user_func_array($this->onFocusOut, [$value, $inputName, array_filter($currentValues)]);

			$presenter->sendPayload();
		}
	}


	public function getControl(): Html
	{
		$control = parent::getControl();

		$presenter = $this->lookup(Presenter::class);
		\assert($presenter instanceof Presenter);

		if($this->parents)
		{
			$parents = [];

			foreach($this->parents as $parent)
			{
				$parents[$this->getNormalizeName($parent)] = $parent->getHtmlId();
			}

			$control->setAttribute('data-autocomplete-parents', \Nette\Utils\Json::encode($parents));
		}

		if($this->onChangeCallback !== null)
		{
			$control->attrs['data-autocomplete'] = $presenter->link($this->lookupPath(Presenter::class) . self::NAME_SEPARATOR . SignalDial::OnChange . '!');
		}
		else
		{
			$control->attrs['data-autocomplete-items'] = $this->prepareData($this->items);
		}

		if($this->onSelectCallback !== null)
		{
			$control->attrs['data-autocomplete-onselect'] = $presenter->link($this->lookupPath(Presenter::class) . self::NAME_SEPARATOR . SignalDial::OnSelect . '!');
		}

		$control->attrs['data-autocomplete-delay'] = $this->delay;
		$control->attrs['data-autocomplete-label'] = $this->prompt;

		return $control;
	}


	private function getNormalizeName(\Nette\Forms\Controls\BaseControl $parent)
	{
		return str_replace('-', '_', $parent->getHtmlId());
	}


	private function prepareData(array $data): array
	{
		$array = [];

		foreach($data as $key => $value)
		{
			$array[] = ['value' => (string) $value, 'data' => $key];
		}

		return $array;
	}


	public function getCoreControl()
	{
		$input = $this->getControl();

		$input->addAttributes(['class' => 'form-control autocomplete-input ' . $input->getAttribute('class') . ' ' . $this->getValidationClass()]);

		return Html::el('div')->class('input-group')
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $this->getValidationFeedback());
	}


	public function setDelay(int $delay): self
	{
		$this->delay = $delay;

		return $this;
	}
}
