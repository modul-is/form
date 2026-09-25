<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;
use Nette\Utils\Json;

/**
 * Posuvnik postaveny nad rSlider.js (https://github.com/slawomir-zaziablo/range-slider).
 *
 * Knihovna schova puvodni <input> a vedle nej vykresli svoje ovladani. Hodnota se do inputu
 * zapisuje jako jedno cislo, v rezimu setRange() jako dve oddelena carkou - proto getValue()
 * vraci pole az kdyz je zapnuty rozsah.
 */
class SliderInput extends \Nette\Forms\Controls\TextInput implements Renderable, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Color;
	use Helper\Tooltip;
	use Helper\ControlPart;
	use Helper\Label;
	use Helper\InputCoreControl;
	use Helper\AutoRenderSkip;
	use Helper\Template;
	use Helper\Validation;
	use Helper\Signals;
	use Helper\ControlClass;
	use Helper\RenderBasic;
	use Helper\RenderDefault;
	use Helper\RenderFloating;
	use Helper\RenderInline;
	use Helper\WrapControl;
	use Helper\InputGroup;

	/** @var array<int, int|float|string> */
	private array $items = [];

	private int|float|null $min = null;

	private int|float|null $max = null;

	private int|float $step = 1;

	private bool $range = false;

	private bool $scale = true;

	private bool $labels = true;

	private bool $sliderTooltip = true;


	public function __construct
	(
		string|\Stringable|null $label = null
	)
	{
		parent::__construct($label);

		$this->controlClass = 'mis-slider-input';
	}


	/**
	 * Hodnoty posuvniku jako spojity rozsah.
	 */
	public function setMinMax(int|float $min, int|float $max, int|float $step = 1): self
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
	public function setItems(array $items): self
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
	public function setRange(bool $range = true): self
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
	public function showScale(bool $scale = true): self
	{
		$this->scale = $scale;

		return $this;
	}


	/**
	 * Zobrazit popisky pod ryskami stupnice.
	 */
	public function showLabels(bool $labels = true): self
	{
		$this->labels = $labels;

		return $this;
	}


	/**
	 * Zobrazit bublinu s aktualni hodnotou nad tahitkem.
	 */
	public function showTooltip(bool $tooltip = true): self
	{
		$this->sliderTooltip = $tooltip;

		return $this;
	}


	public function setValue($value): static
	{
		return parent::setValue($this->encodeValue($value));
	}


	/**
	 * @return int|float|array<int, int|float>|null
	 */
	public function getValue(): mixed
	{
		$value = parent::getValue();

		if($value === null || $value === '')
		{
			return null;
		}

		$parts = array_map(
			fn(string $part): int|float => $this->castNumber(trim($part)),
			explode(',', (string) $value)
		);

		if(!$this->range)
		{
			return $parts[0];
		}

		return count($parts) === 2 ? $parts : null;
	}


	public function getControl(): Html
	{
		if(!$this->items && $this->min === null)
		{
			throw new \Nette\InvalidStateException('Call setMinMax() or setItems() before rendering input "' . $this->getName() . '"');
		}

		$input = parent::getControl();

		$input->setAttribute('data-slider', Json::encode($this->getSliderConfig()));
		$input->setAttribute('readonly', true);

		return $input;
	}


	/**
	 * Konfigurace predavana do rSlider.js - klice odpovidaji jeho options.
	 *
	 * @return array<string, mixed>
	 */
	private function getSliderConfig(): array
	{
		$config = [
			'values' => $this->items ?: ['min' => $this->min, 'max' => $this->max],
			'range' => $this->range,
			'scale' => $this->scale,
			'labels' => $this->labels,
			'tooltip' => $this->sliderTooltip,
			'disabled' => $this->isDisabled()
		];

		if(!$this->items)
		{
			$config['step'] = $this->step;
		}

		$value = $this->getValue();

		if($value !== null)
		{
			$config['set'] = is_array($value) ? $value : [$value];
		}

		return $config;
	}


	private function encodeValue(mixed $value): mixed
	{
		if(is_array($value))
		{
			return implode(',', $value);
		}

		return $value;
	}


	private function castNumber(string $value): int|float
	{
		return str_contains($value, '.') ? (float) $value : (int) $value;
	}
}
