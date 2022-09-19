<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

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


	public const SIGNAL_ONCHANGE = 'onChange';

	public array|\Closure|null $onChangeCallback = null;

	private $parents;

	private int $delay = 100;

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


	public function setParents(array $parents): self
	{
		$this->parents = $parents;

		return $this;
	}


	public function signalReceived($signal): void
	{
		/** @var \Nette\Application\UI\Presenter $presenter */
		$presenter = $this->lookup(\Nette\Application\UI\Presenter::class);

		if($presenter->isAjax() && !$this->isDisabled())
		{
			/**
			 * OnChange
			 */
			if($signal == self::SIGNAL_ONCHANGE)
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

				$data = ['' => ''] + call_user_func_array($this->onChangeCallback, [$presenter->getParameter('param'), $parentArray]);

				if(!is_array($data))
				{
					throw new \Nette\InvalidStateException('Callback for:"' . $this->getHtmlId() . '" must return array!');
				}

				$presenter->payload->suggestions = [];

				foreach($data as $key => $value)
				{
					$presenter->payload->suggestions[] = ['value' => (string) $value, 'data' => $key];
				}

				$presenter->sendPayload();
			}
		}
	}


	public function getControl(): Html
	{
		$control = parent::getControl();

		$form = $this->getForm();

		/** @var \Nette\Application\UI\Presenter $presenter */
		$presenter = $this->lookup(\Nette\Application\UI\Presenter::class);

		if($this->onChangeCallback !== null)
		{
			$form = $this->getForm();

			$control->attrs['data-autocomplete'] = $presenter->link(
				$this->lookupPath('Nette\Application\UI\Presenter') . self::NAME_SEPARATOR . self::SIGNAL_ONCHANGE . '!');
		}
		else
		{
			$control->attrs['data-autocomplete-items'] = $this->items;
		}

		$control->attrs['data-autocomplete-delay'] = $this->delay;
		$control->attrs['data-autocomplete-label'] = $this->prompt;

		return $control;
	}


	public function getCoreControl()
	{
		$input = $this->getControl();

		$errorClass = '';
		$errorMessage = null;

		if($this->hasErrors())
		{
			$errorClass = ' is-invalid';

			$errorMessage = Html::el('div')
				->class('invalid-feedback')
				->addHtml($this->getError());
		}

		$input->addAttributes(['class' => 'form-control ' . $input->getAttribute('class') . $errorClass . ' autocomplete-input']);

		return Html::el('div')->class('input-group')
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $errorMessage);
	}


	public function setDelay(int $delay): self
	{
		$this->delay = $delay;

		return $this;
	}
}
