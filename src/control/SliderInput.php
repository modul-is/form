<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

/**
 * Posuvnik - nativni range input (Bootstrap form-range), v rezimu setRange() dva pres sebe.
 *
 * V rezimu setRange() se hodnota zapisuje do skryteho inputu jako dve hodnoty oddelene carkou - proto getValue()
 * vraci pole az kdyz je zapnuty rozsah.
 */
class SliderInput extends \Nette\Forms\Controls\TextInput implements InputField, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\Signals;

	/** @var array<int, int|float|string> */
	private array $items = [];

	private int|float|null $min = null;

	private int|float|null $max = null;

	private int|float $step = 1;

	private bool $range = false;

	private bool $scale = true;

	private bool $labels = true;

	private bool $sliderTooltip = true;


	/**
	 * Hodnoty posuvniku jako spojity rozsah.
	 */
	public function setMinMax(int|float $min, int|float $max, int|float $step = 1): static
	{
		if($max <= $min)
		{
			throw new \Nette\InvalidArgumentException('Max value must be greater than min value for input "' . $this->getName() . '"');
		}

		if($step <= 0)
		{
			throw new \Nette\InvalidArgumentException('Step must be greater than zero for input "' . $this->getName() . '"');
		}

		$this->min = $min;
		$this->max = $max;
		$this->step = $step;
		$this->items = [];

		return $this;
	}


	/**
	 * Hodnoty posuvniku jako vycet - pouziva se misto setMinMax().
	 *
	 * @param array<int, int|float|string> $items
	 */
	public function setItems(array $items): static
	{
		if(count($items) < 2)
		{
			throw new \Nette\InvalidArgumentException('At least two items are required for input "' . $this->getName() . '"');
		}

		$this->items = array_values($items);
		$this->min = null;
		$this->max = null;

		return $this;
	}


	/**
	 * @return array<int, int|float|string>
	 */
	public function getItems(): array
	{
		return $this->items;
	}


	/**
	 * Dve tahitka - hodnota je potom pole [od, do].
	 */
	public function setRange(bool $range = true): static
	{
		$this->range = $range;

		return $this;
	}


	public function isRange(): bool
	{
		return $this->range;
	}


	/**
	 * Zobrazit stupnici s ryskami.
	 */
	public function showScale(bool $scale = true): static
	{
		$this->scale = $scale;

		return $this;
	}


	/**
	 * Zobrazit popisky pod ryskami stupnice.
	 */
	public function showLabels(bool $labels = true): static
	{
		$this->labels = $labels;

		return $this;
	}


	/**
	 * Zobrazit bublinu s aktualni hodnotou nad tahitkem.
	 */
	public function showTooltip(bool $tooltip = true): static
	{
		$this->sliderTooltip = $tooltip;

		return $this;
	}


	public function setValue($value): static
	{
		return parent::setValue($this->encodeValue($value));
	}


	/**
	 * @return int|float|string|array<int, int|float|string>|null
	 */
	public function getValue(): mixed
	{
		$value = parent::getValue();

		if($value === null || $value === '')
		{
			return null;
		}

		$parts = array_map(
			fn(string $part): int|float|string => $this->castPart(trim($part)),
			explode(',', (string) $value)
		);

		if(!$this->range)
		{
			return $parts[0];
		}

		return count($parts) === 2 ? $parts : null;
	}


	/**
	 * Single value is a native range input, with setItems() its value is the index of the item.
	 */
	public function getControl(): Html
	{
		if(!$this->items && $this->min === null)
		{
			throw new \Nette\InvalidStateException('Call setMinMax() or setItems() before rendering input "' . $this->getName() . '"');
		}

		$input = parent::getControl();

		/**
		 * Two handles - the value "from,to" is carried by a hidden input, the handles are created by the renderer
		 */
		if($this->range)
		{
			return $input->type('hidden');
		}

		$value = $this->getValue();

		/**
		 * Without a value the browser would put the handle to the middle - it starts at the beginning as before
		 */
		$input->type('range')
			->setAttribute('min', $this->items ? 0 : $this->min)
			->setAttribute('max', $this->items ? count($this->items) - 1 : $this->max)
			->setAttribute('step', $this->items ? 1 : $this->step)
			->setAttribute('value', $this->items ? ($this->getItemIndex($value) ?? 0) : ($value ?? $this->min))
			->setAttribute('list', $this->scale ? $this->getTicksId() : null);

		return $input;
	}


	/**
	 * Value is posted as the index of the item when setItems() is used
	 */
	public function loadHttpData(): void
	{
		$value = $this->getHttpData(\Nette\Forms\Form::DataLine);

		if(!$this->range && $this->items && is_string($value) && ctype_digit($value))
		{
			$value = $this->items[(int) $value] ?? null;
		}

		$this->setValue($value);
	}


	public function hasBubble(): bool
	{
		return $this->sliderTooltip;
	}


	public function getTicksId(): string
	{
		return $this->getHtmlId() . '-ticks';
	}


	/**
	 * Tick marks under the slider - every step, or ten parts when there are more than 20 steps
	 *
	 * @return list<array{value: int|float, label: ?string}>
	 */
	public function getTicks(): array
	{
		if(!$this->scale)
		{
			return [];
		}

		$ticks = [];

		if($this->items)
		{
			foreach($this->items as $index => $item)
			{
				$ticks[] = ['value' => $index, 'label' => $this->labels ? (string) $item : null];
			}

			return $ticks;
		}

		\assert($this->min !== null && $this->max !== null);

		$steps = (int) floor(($this->max - $this->min) / $this->step + 1e-9);
		$count = $steps <= 20 ? $steps : 10;

		for($i = 0; $i <= $count; $i++)
		{
			$value = $count === $steps
				? $this->min + $i * $this->step
				: $this->min + $i * ($this->max - $this->min) / $count;

			$value = is_float($value) ? round($value, 10) : $value;

			/**
			 * More than 11 labels would overlap - only the ends are labeled then
			 */
			$labeled = $this->labels && ($count <= 10 || $i === 0 || $i === $count);

			$ticks[] = ['value' => $value, 'label' => $labeled ? (string) $value : null];
		}

		return $ticks;
	}


	/**
	 * Items shown in the value bubble - the input itself holds their indexes
	 *
	 * @return list<string>
	 */
	public function getBubbleItems(): array
	{
		return array_values(array_map(strval(...), $this->items));
	}


	/**
	 * Attributes of the range inputs - with setItems() the inputs move over indexes of the items
	 *
	 * @return array{min: int|float, max: int|float, step: int|float}
	 */
	public function getBounds(): array
	{
		if($this->items)
		{
			return ['min' => 0, 'max' => count($this->items) - 1, 'step' => 1];
		}

		\assert($this->min !== null && $this->max !== null);

		return ['min' => $this->min, 'max' => $this->max, 'step' => $this->step];
	}


	/**
	 * Positions of the two handles - numbers, or indexes of the items with setItems()
	 *
	 * @return array{int|float, int|float}
	 */
	public function getRangePositions(): array
	{
		$value = $this->getValue();
		$parts = is_array($value) && count($value) === 2 ? array_values($value) : null;

		if($this->items)
		{
			return $parts
				? [$this->getItemIndex($parts[0]) ?? 0, $this->getItemIndex($parts[1]) ?? count($this->items) - 1]
				: [0, count($this->items) - 1];
		}

		\assert($this->min !== null && $this->max !== null);

		return $parts && is_numeric($parts[0]) && is_numeric($parts[1])
			? [+$parts[0], +$parts[1]]
			: [$this->min, $this->max];
	}


	private function getItemIndex(mixed $value): ?int
	{
		foreach($this->items as $index => $item)
		{
			if((string) $item === (string) $value)
			{
				return $index;
			}
		}

		return null;
	}


	private function encodeValue(mixed $value): mixed
	{
		if(is_array($value))
		{
			return implode(',', $value);
		}

		return $value;
	}


	/**
	 * With setItems() the value is one of the items (may be a string), otherwise a number from setMinMax()
	 */
	private function castPart(string $value): int|float|string
	{
		if($this->items)
		{
			foreach($this->items as $item)
			{
				if((string) $item === $value)
				{
					return $item;
				}
			}

			return $value;
		}

		return str_contains($value, '.') ? (float) $value : (int) $value;
	}
}
