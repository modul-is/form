<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

/**
 * List of free text values entered as chips (Bootstrap chip input) - value is a list of strings.
 *
 * The values are submitted by a hidden <select multiple>, which is kept in sync with the chips by JS -
 * Nette rules (required, ...) and the form work with it as with any multiselect.
 */
class ChipsInput extends \Nette\Forms\Controls\BaseControl implements Field, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Tooltip;
	use Helper\Description;
	use Helper\Render;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\ControlClass;
	use Helper\Signals;
	use Helper\WrapControl;
	use Helper\Color;
	use Helper\Size;

	/** @var list<string> */
	protected mixed $value = [];

	private ?string $separator = ',';

	private ?int $maxChips = null;

	private bool $allowDuplicates = false;

	private ?string $placeholder = null;


	public function __construct
	(
		string|\Stringable|null $label = null
	)
	{
		parent::__construct($label);

		$this->control = Html::el('select', ['multiple' => true, 'hidden' => true]);
		$this->setOption('type', 'select');
	}


	/**
	 * @param string|array<int|string, string|int|float>|null $value list of values or values joined by the separator
	 */
	public function setValue($value): static
	{
		if($value === null || $value === '')
		{
			$values = [];
		}
		elseif(is_string($value))
		{
			$values = $this->separator !== null && $this->separator !== '' ? explode($this->separator, $value) : [$value];
		}
		elseif(is_array($value))
		{
			$values = $value;
		}
		else
		{
			throw new \Nette\InvalidArgumentException('Value of input "' . $this->getName() . '" has to be an array or a string, ' . get_debug_type($value) . ' given.');
		}

		$this->value = $this->normalize($values);

		return $this;
	}


	/**
	 * @return list<string>
	 */
	public function getValue(): array
	{
		return $this->value;
	}


	public function isFilled(): bool
	{
		return $this->value !== [];
	}


	public function loadHttpData(): void
	{
		$this->value = $this->normalize((array) $this->getHttpData(\Nette\Forms\Form::DataLine));
	}


	public function getHtmlName(): string
	{
		return parent::getHtmlName() . '[]';
	}


	/**
	 * Hidden select carrying the values - the visible chip input is created by the renderer
	 */
	public function getControl(): Html
	{
		$control = parent::getControl();
		\assert($control instanceof Html);

		$control->id($this->getValuesId());

		foreach($this->value as $value)
		{
			$control->addHtml(Html::el('option', ['value' => $value, 'selected' => true])->setText($value));
		}

		return $control;
	}


	/**
	 * Id of the hidden select - the html id of the control belongs to the visible text input (label points to it)
	 */
	public function getValuesId(): string
	{
		return $this->getHtmlId() . '-values';
	}


	/**
	 * Character which creates a chip when typed (null - only Enter creates it)
	 */
	public function setSeparator(?string $separator): static
	{
		$this->separator = $separator;

		return $this;
	}


	public function getSeparator(): ?string
	{
		return $this->separator;
	}


	public function setMaxChips(?int $maxChips): static
	{
		$this->maxChips = $maxChips;

		return $this;
	}


	public function getMaxChips(): ?int
	{
		return $this->maxChips;
	}


	public function setAllowDuplicates(bool $allowDuplicates = true): static
	{
		$this->allowDuplicates = $allowDuplicates;

		return $this;
	}


	public function isAllowDuplicates(): bool
	{
		return $this->allowDuplicates;
	}


	public function setPlaceholder(?string $placeholder): static
	{
		$this->placeholder = $placeholder;

		return $this;
	}


	public function getPlaceholder(): ?string
	{
		return $this->placeholder === null ? null : $this->translate($this->placeholder);
	}


	/**
	 * @param array<mixed> $values
	 * @return list<string>
	 */
	private function normalize(array $values): array
	{
		$values = array_filter(
			array_map(fn(mixed $value): string => trim(is_scalar($value) ? (string) $value : ''), array_values($values)),
			fn(string $value): bool => $value !== ''
		);

		if(!$this->allowDuplicates)
		{
			$values = array_unique($values);
		}

		if($this->maxChips !== null)
		{
			$values = array_slice($values, 0, $this->maxChips);
		}

		return array_values($values);
	}
}
