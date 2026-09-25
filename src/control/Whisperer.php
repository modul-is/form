<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Dial\SignalDial;
use Nette\Application\UI\Presenter;
use Nette\Utils\Html;

class Whisperer extends SelectBox implements \Nette\Application\UI\SignalReceiver
{
	use \ModulIS\Form\Helper\Dependent;

	/** @var ?\Closure(mixed, array<mixed>): void */
	private ?\Closure $onSelectCallback = null;

	/** @var ?\Closure(mixed, array<string, mixed>): array<int|string, mixed> */
	private ?\Closure $onSearchChangeCallback = null;

	private ?string $noResultMessage = null;

	private int|string|null $dividerValue = null;


	/**
	 * @param ?array<mixed> $items
	 */
	public function __construct
	(
		string|\Stringable|null $label = null,
		?array $items = null
	)
	{
		parent::__construct($label, $items);

		$this->controlClass = 'form-control-chosen';
	}


	/**
	 * @param callable(mixed, array<mixed>): void $callback
	 */
	public function setOnSelectCallback(callable $callback): static
	{
		if($this->onChangeCallback !== null)
		{
			throw new \Nette\InvalidStateException('Cannot use onSelectCallback and onChangeCallback together for input "' . $this->getName() . '"');
		}

		$this->onSelectCallback = $callback(...);

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


	/**
	 * @param callable(mixed, array<string, mixed>): array<int|string, mixed> $callback
	 */
	public function setOnSearchChangeCallback(callable $callback): static
	{
		$this->onSearchChangeCallback = $callback(...);

		return $this;
	}


	/**
	 * @param array<\Nette\Forms\Controls\BaseControl> $parents
	 */
	public function setParents(array $parents): static
	{
		$this->parents = $parents;

		return $this;
	}


	public function signalReceived(string $signal): void
	{
		$presenter = $this->lookup(Presenter::class);

		if(!$presenter->isAjax() || $this->isDisabled())
		{
			return;
		}

		if($signal === SignalDial::Load)
		{
			$this->sendDependentPayload($presenter);
		}
		elseif($signal === SignalDial::OnSearchChange)
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
					$parentArray[$parent->getName()] = $parentValueArray[$this->getNormalizeName($parent)] ?? null;
				}
			}

			/**
			 * Empty option (needed by chosen for deselect) is added by whisperer.js
			 */
			$data = call_user_func_array($this->onSearchChangeCallback, [$presenter->getParameter('param'), $parentArray]);

			$presenter->payload->suggestions = [];

			foreach($data as $key => $value)
			{
				$presenter->payload->suggestions[] = ['value' => (string) $value, 'data' => $key];
			}

			$presenter->sendPayload();
		}
		elseif($signal === SignalDial::OnSelect)
		{
			if(!is_callable($this->onSelectCallback))
			{
				throw new \Nette\InvalidStateException('OnSelect callback not set for input "' . $this->getName() . '"');
			}

			call_user_func_array($this->onSelectCallback, [$presenter->getParameter('selected'), \ModulIS\Form\Helper\FormData::parse($presenter->getParameter('formdata'))]);

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
		/**
		 * Items have to be loaded before parent builds the options
		 */
		if($this->dependentCallback !== null)
		{
			$this->tryLoadItems();
		}

		$control = parent::getControl();

		if($this->dividerValue !== '' && $this->dividerValue !== null)
		{
			$control = $this->addDividerToOption($control);
		}

		$presenter = $this->lookup(Presenter::class);

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
			$control->attrs['no-result-message'] = $this->translate($this->noResultMessage);
		}

		if($control->getAttribute('data-placeholder'))
		{
			$control->setAttribute('data-placeholder', $this->translate($control->getAttribute('data-placeholder')));
		}

		return $control;
	}


	private function getLinkPath(string $signal): string
	{
		return $this->lookupPath(Presenter::class) . self::NameSeparator . $signal . '!';
	}


	public function getCoreControl(): Html
	{
		$input = $this->getControl();

		$errorClass = '';
		$errorMessage = null;

		if($this->hasErrors())
		{
			$errorClass = ' is-invalid';

			$errorMessage = $this->createErrorFeedback();
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
			/**
			 * Nette considers the empty '' item as filled, therefore the required rule is checked here once more
			 */
			if(!$rule->branch && !$rule->isNegative && $rule->control === $this && $rule->validator === \ModulIS\Form\Form::Filled && in_array($this->getValue(), [null, false, ''], true))
			{
				$this->addError(\Nette\Forms\Validator::formatMessage($rule, true), false);
			}
		}
	}


	/**
	 * Whisperer already has its own empty item for chosen - a Nette prompt would add a second one
	 * with key "\t" (submitted as the value), so the prompt is shown as chosen placeholder instead
	 */
	public function setPrompt(string|\Stringable|false $prompt): static
	{
		if($prompt !== false)
		{
			$this->setHtmlAttribute('data-placeholder', (string) $prompt);
		}

		return $this;
	}


	public function setNoResultMessage(?string $noResultMessage = null): static
	{
		$this->noResultMessage = $noResultMessage;

		return $this;
	}


	private function addDividerToOption(Html $control): Html
	{
		$value = htmlspecialchars((string) $this->dividerValue, ENT_QUOTES | ENT_HTML5, 'UTF-8');

		$optionString = preg_replace(
			'~<option(?=[^>]*\svalue="' . preg_quote($value, '~') . '")~',
			'<option class="border-bottom"',
			(string) $control->getChildren()[0],
			1
		);

		$control->removeChildren();
		$control->addHtml((string) $optionString);

		return $control;
	}


	public function setDividerValue(int|string|null $dividerValue): static
	{
		$this->dividerValue = $dividerValue;

		return $this;
	}
}
