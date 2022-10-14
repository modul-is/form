<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class Whisperer extends SelectBox implements \Nette\Application\UI\ISignalReceiver
{
	use \NasExt\Forms\DependentTrait;

	public const SIGNAL_NAME = 'load';

	public const SIGNAL_ONCHANGE = 'onChange';

	public const SIGNAL_ONSELECT = 'onSelect';

	public array|\Closure|null $onSelectCallback = null;

	public array|\Closure|null $onChangeCallback = null;

	private $parents;

	private int $delay = 500;


	public function setOnSelectCallback(array|\Closure $callback): self
	{
		$this->onSelectCallback = $callback;

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
		$presenter = $this->lookup('Nette\\Application\\UI\\Presenter');

		if($signal == $this->onFocusOutSignal || $signal === $this->onChangeSignal)
		{
			$value = $presenter->getParameter('value');

			if($signal == $this->onFocusOutSignal)
			{
				call_user_func_array($this->onFocusOut, [$value]);
			}
			else
			{
				call_user_func_array($this->onChange, [$value]);
			}

			$presenter->sendPayload();
		}

		if($presenter->isAjax() && !$this->isDisabled())
		{
			/**
			 * Dependant signal
			 */
			if($signal == self::SIGNAL_NAME)
			{
				$parentsNames = [];

				foreach($this->parents as $parent)
				{
					$value = $presenter->getParameter($this->getNormalizeName($parent));

					$parent->setValue($value);

					$parentsNames[$parent->getName()] = $parent->getRawValue();
				}

				$data = $this->getDependentData([$parentsNames]);

				$presenter->payload->dependentselectbox = [
					'id' => $this->getHtmlId(),
					'items' => $data->getPreparedItems(!is_array($this->disabled) ?: $this->disabled),
					'value' => $data->getValue(),
					'prompt' => $this->translate($data->getPrompt()),
					'disabledWhenEmpty' => $this->disabledWhenEmpty
				];

				$presenter->sendPayload();
			}
			/**
			 * OnChange
			 */
			elseif($signal == self::SIGNAL_ONCHANGE)
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
			/**
			 * OnSelect
			 */
			elseif($signal == self::SIGNAL_ONSELECT)
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
		}
	}


	private function tryLoadItems(): void
	{
		if($this->parents === array_filter($this->parents, fn($p) => !$p->hasErrors()))
		{
			$parentsValues = [];

			foreach($this->parents as $parent)
			{
				$parentsValues[$parent->getName()] = $parent->getValue();
			}

			$data = $this->getDependentData([$parentsValues]);
			$items = $data->getItems();

			if($this->getForm()->isSubmitted())
			{
				$this->setValue($this->value);
			}
			elseif($this->tempValue !== null)
			{
				$this->setValue($this->tempValue);
			}
			else
			{
				$this->setValue($data->getValue());
			}

			if(count($items) > 0)
			{
				$this->loadHttpData();

				$this->setItems($items)
					->setPrompt($data->getPrompt() === '' ? $this->getPrompt() : $data->getPrompt());
			}
			else
			{
				if($this->disabledWhenEmpty === true && !$this->isDisabled())
				{
					$this->setDisabled();
				}
			}
		}
	}


	public function setValue($value): self
	{
		if($this->dependentCallback !== null)
		{
			$this->tempValue = $value;
		}

		parent::setValue($value);

		return $this;
	}


	public function getValue()
	{
		if($this->dependentCallback !== null)
		{
			$this->tryLoadItems();
		}

		if(!in_array($this->tempValue, [null, '', []], true))
		{
			return $this->tempValue;
		}

		return $this->getRawValue();
	}


	public function getControl(): Html
	{
		$control = parent::getControl();

		$form = $this->getForm();

		/** @var \Nette\Application\UI\Presenter $presenter */
		$presenter = $this->lookup(\Nette\Application\UI\Presenter::class);

		if($this->parents)
		{
			$parents = [];

			foreach($this->parents as $parent)
			{
				$parents[$this->getNormalizeName($parent)] = $parent->getHtmlId();
			}

			$control->setAttribute('data-dependentselectbox-parents', \Nette\Utils\Json::encode($parents));
		}

		if($this->dependentCallback !== null)
		{
			$this->tryLoadItems();

			$link = $this->lookupPath('Nette\\Application\\UI\\Presenter') . \Nette\ComponentModel\IComponent::NAME_SEPARATOR . self::SIGNAL_NAME . '!';

			$control->setAttribute('data-dependentselectbox', $presenter->link($link));
		}

		if($this->onChangeCallback !== null)
		{
			$form = $this->getForm();

			$control->attrs['data-whisperer'] = $presenter->link(
				$this->lookupPath('Nette\Application\UI\Presenter') . self::NAME_SEPARATOR . self::SIGNAL_ONCHANGE . '!');
		}

		if($this->onSelectCallback !== null)
		{
			$control->attrs['data-whisperer-onSelect'] = $presenter->link(
				$this->lookupPath('Nette\Application\UI\Presenter') . self::NAME_SEPARATOR . self::SIGNAL_ONSELECT . '!');
		}

		if($this->hasSignal())
		{
			$this->addSignalsToInput($control);
		}

		$control->attrs['data-whisperer-delay'] = $this->delay;

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

		$chosenClass = $this->isRequired() ? ' form-control-chosen-required' : ' form-control-chosen';

		$input->addAttributes(['class' => 'form-control ' . $input->getAttribute('class') . $errorClass . $chosenClass]);

		return Html::el('div')->class('input-group')
			->addHtml($this->getPrepend() . $input . $this->getAppend() . $errorMessage);
	}


	public function validate(): void
	{
		parent::validate();

		foreach($this->getRules() as $rule)
		{
			if($rule->control == $this && $rule->validator == \ModulIS\Form\Form::FILLED && in_array($this->getValue(), [null, false, ''], true))
			{
				$this->addError(\Nette\Forms\Validator::formatMessage($rule, true), false);
			}
		}
	}


	public function setDelay(int $delay): self
	{
		$this->delay = $delay;

		return $this;
	}
}
