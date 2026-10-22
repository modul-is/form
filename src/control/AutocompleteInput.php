<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Dial\SignalDial;
use ModulIS\Form\Helper;
use Nette\Application\UI\Presenter;
use Nette\Utils\Html;

class AutocompleteInput extends \Nette\Forms\Controls\TextInput implements InputField, HasSuggestions, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\Suggestions;
	use Helper\Signals
	{
		setOnChangeCallback as public signalsSetOnChangeCallback;
		signalReceived as public signalsSignalReceived;
	}

	/** @var ?\Closure(mixed, array<string, mixed>): mixed */
	public ?\Closure $onSearchChangeCallback = null;

	/** @var ?\Closure(mixed, array<mixed>): void */
	public ?\Closure $onSelectCallback = null;

	/** @var array<\Nette\Forms\Controls\BaseControl> */
	private array $parents = [];

	private ?string $prompt = null;


	/**
	 * @param ?array<mixed> $items
	 */
	public function __construct
	(
		string|\Stringable|null $label = null,
		?int $maxLength = null,
		?array $items = null
	)
	{
		parent::__construct($label, $maxLength);

		if($items)
		{
			$this->setSuggestions($items, false);
		}
	}


	public function setPrompt(string $prompt): static
	{
		$this->prompt = $prompt;

		return $this;
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

		return $this->signalsSetOnChangeCallback($callback);
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

		if($signal === SignalDial::OnSearchChange)
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
			$data = call_user_func_array($this->onSearchChangeCallback, [$presenter->getParameter('param'), $parentArray]);

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
				throw new \Nette\InvalidStateException('OnSelect callback not set for input "' . $this->getName() . '"');
			}

			call_user_func_array($this->onSelectCallback, [$presenter->getParameter('selected'), Helper\FormData::parse($presenter->getParameter('formdata'))]);

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

			if($this->onFocusOutCallback === null)
			{
				throw new \Nette\InvalidStateException('OnFocusOut callback not set for input "' . $this->getName() . '"');
			}

			call_user_func_array($this->onFocusOutCallback, [$value, $inputName, Helper\FormData::parse($presenter->getParameter('formdata'))]);

			$presenter->sendPayload();
		}
		else
		{
			$this->signalsSignalReceived($signal);
		}
	}


	public function getControl(): Html
	{
		$control = parent::getControl();

		$presenter = $this->lookup(Presenter::class);

		if($this->parents)
		{
			$parents = [];

			foreach($this->parents as $parent)
			{
				$parents[$this->getNormalizeName($parent)] = $parent->getHtmlId();
			}

			$control->setAttribute('data-autocomplete-parents', \Nette\Utils\Json::encode($parents));
		}

		if($this->onSearchChangeCallback !== null)
		{
			$control->attrs['data-autocomplete'] = $presenter->link($this->lookupPath(Presenter::class) . self::NameSeparator . SignalDial::OnSearchChange . '!');
		}

		if($this->onSelectCallback !== null)
		{
			$control->attrs['data-autocomplete-onselect'] = $presenter->link($this->lookupPath(Presenter::class) . self::NameSeparator . SignalDial::OnSelect . '!');
		}

		$control->attrs['data-autocomplete-delay'] = 200;

		if($this->prompt !== null && $control->getAttribute('placeholder') === null)
		{
			$control->placeholder($this->translate($this->prompt));
		}

		return $control;
	}


	private function getNormalizeName(\Nette\Forms\Controls\BaseControl $parent): string
	{
		return str_replace('-', '_', (string) $parent->getHtmlId());
	}


	/**
	 * @param array<int|string, mixed> $data
	 * @return list<array{value: string, data: int|string}>
	 */
	private function prepareData(array $data): array
	{
		$array = [];

		foreach($data as $key => $value)
		{
			$array[] = ['value' => (string) $value, 'data' => $key];
		}

		return $array;
	}


	public function hasRemoteSuggestions(): bool
	{
		return $this->onSearchChangeCallback !== null;
	}
}
