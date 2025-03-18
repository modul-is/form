<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Dial\SignalDial;
use Nette\Application\UI\Presenter;
use Nette\Utils\Html;
use Nette\Utils\Strings;

class Whisperer extends SelectBox implements \Nette\Application\UI\SignalReceiver
{
	use \ModulIS\Form\Helper\Dependent;

	private $onSelectCallback;

	private $onSearchChangeCallback;

	private ?string $noResultMessage = null;

	private int|string|null $dividerValue = null;


	public function __construct($label = null, ?array $items = null)
	{
		parent::__construct($label, $items);

		$this->controlClass = 'form-control-chosen';
	}


	public function setOnSelectCallback(callable $callback): self
	{
		if($this->onChangeCallback !== null)
		{
			throw new \Nette\InvalidStateException('Cannot use onSelectCallback and onChangeCallback together for input "' . $this->getName() . '"');
		}

		$this->onSelectCallback = $callback;

		return $this;
	}


	public function setOnChangeCallback(callable $callback): static
	{
		if($this->onSelectCallback !== null)
		{
			throw new \Nette\InvalidStateException('Cannot use onChangeCallback and onSelectCallback together for input "' . $this->getName() . '"');
		}

		return parent::setOnChangeCallback($callback);
	}


	public function setOnSearchChangeCallback(callable $callback): self
	{
		$this->onSearchChangeCallback = $callback;

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

		if(!$presenter->isAjax() || $this->isDisabled())
		{
			return;
		}

		if($signal === SignalDial::Load)
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
		elseif($signal == SignalDial::OnSearchChange)
		{
			if(!is_callable($this->onSearchChangeCallback))
			{
				throw new \Nette\InvalidStateException('OnSearchChange callback not set for input "' . $this->getName() . '"');
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

			$data = ['' => ''] + call_user_func_array($this->onSearchChangeCallback, [$presenter->getParameter('param'), $parentArray]);

			$presenter->payload->suggestions = [];

			foreach($data as $key => $value)
			{
				$presenter->payload->suggestions[] = ['value' => (string) $value, 'data' => $key];
			}

			$presenter->sendPayload();
		}
		elseif($signal == SignalDial::OnSelect)
		{
			if(!is_callable($this->onSelectCallback))
			{
				throw new \Nette\InvalidStateException('OnSelect callback not set for input "' . $this->getName() . '"');
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
		else
		{
			parent::signalReceived($signal);
		}
	}


	public function setValue($value): static
	{
		if($this->dependentCallback !== null)
		{
			$this->tempValue = $value;
		}

		return parent::setValue($value);
	}


	public function getValue(): mixed
	{
		if($this->dependentCallback !== null)
		{
			$this->tryLoadItems();
		}

		if(!in_array($this->tempValue, [null, '', []], true))
		{
			return $this->tempValue;
		}

		return $this->value;
	}


	public function getControl(): Html
	{
		$control = parent::getControl();

		if($this->dividerValue !== '' && $this->dividerValue !== null)
		{
			$control = $this->addDividerToOption($control);
		}

		$presenter = $this->lookup(Presenter::class);
		\assert($presenter instanceof Presenter);

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

			$control->setAttribute('data-dependentselectbox', $presenter->link($this->getLinkPath(SignalDial::Load)));
		}

		if($this->onSearchChangeCallback !== null)
		{
			$control->setAttribute('data-whisperer', $presenter->link($this->getLinkPath(SignalDial::OnSearchChange)));
		}

		if($this->onSelectCallback !== null)
		{
			$control->setAttribute('data-whisperer-onSelect', $presenter->link($this->getLinkPath(SignalDial::OnSelect)));
		}

		if($this->hasSignal())
		{
			$this->addSignalsToInput($control);
		}

		if($this->noResultMessage !== null)
		{
			$control->attrs['no-result-message'] = $this->noResultMessage;
		}

		return $control;
	}


	private function getLinkPath(string $signal): string
	{
		return $this->lookupPath(Presenter::class) . self::NameSeparator . $signal . '!';
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
			if($rule->control == $this && $rule->validator == \ModulIS\Form\Form::Filled && in_array($this->getValue(), [null, false, ''], true))
			{
				$this->addError(\Nette\Forms\Validator::formatMessage($rule, true), false);
			}
		}
	}


	public function setNoResultMessage(?string $noResultMessage = null): self
	{
		$this->noResultMessage = $noResultMessage;

		return $this;
	}


	private function addDividerToOption(Html $control): Html
	{
		$optionString = '';
		$items = explode('</option>', $control->getChildren()[0]);

		foreach($items as $item)
		{
			if(str_contains($item, 'value="' . $this->dividerValue . '"'))
			{
				$optionString .= '<option class="border-bottom"' . Strings::trim($item, '<option') . '</option>';
			}
			else
			{
				$optionString .= $item . '</option>';
			}
		}

		$control->removeChildren();
		$control->addHtml($optionString);

		return $control;
	}


	public function setDividerValue(int|string|null $dividerValue): self
	{
		$this->dividerValue = $dividerValue;

		return $this;
	}
}
