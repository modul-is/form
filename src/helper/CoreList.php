<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Enum\RenderListType;

/**
 * Settings of radio and checkbox lists - rendered by the renderer
 */
trait CoreList
{
	/** @var array<int|string, string> */
	protected array $tooltips = [];

	/** @var array<int|string, string> */
	protected array $iconArray = [];

	protected int $itemsPerRow = 1;

	protected ?string $itemClass = null;

	/** @var array<string, mixed> */
	protected array $wrapRowAttributes = [];

	protected ?RenderListType $renderType = null;

	protected bool $toggleButton = false;

	/** @var string|array<int|string, string> */
	protected string|array $buttonColor = 'primary';


	public function setRenderType(RenderListType $renderType): static
	{
		$this->renderType = $renderType;

		return $this;
	}


	/**
	 * Null means derived from the form
	 */
	public function getRenderType(): ?RenderListType
	{
		return $this->renderType;
	}


	public function setRenderDefault(): static
	{
		return $this->setRenderType(RenderListType::Default);
	}


	/**
	 * Bootstrap has no floating lists - rendered as Default
	 */
	public function setRenderFloating(): static
	{
		return $this->setRenderType(RenderListType::Floating);
	}


	public function setRenderInline(): static
	{
		return $this->setRenderType(RenderListType::Inline);
	}


	/**
	 * Items as cards (form-field-card) - with icons (setIconArray()) and descriptions (setTooltips())
	 */
	public function setRenderBig(): static
	{
		return $this->setRenderType(RenderListType::Big);
	}


	/**
	 * Items next to each other
	 */
	public function setRenderCompact(): static
	{
		return $this->setRenderType(RenderListType::Compact);
	}


	/**
	 * Description of the items (item key => text), rendered under the item label
	 * @param array<int|string, string> $tooltips
	 */
	public function setTooltips(array $tooltips): static
	{
		$this->tooltips = $tooltips;

		return $this;
	}


	public function getItemDescription(string|int $key): ?string
	{
		return isset($this->tooltips[$key]) ? $this->translate($this->tooltips[$key]) : null;
	}


	/**
	 * Icons of the items (item key => icon name)
	 * @param array<int|string, string> $iconArray
	 */
	public function setIconArray(array $iconArray): static
	{
		$this->iconArray = $iconArray;

		return $this;
	}


	public function getItemIcon(string|int $key): ?string
	{
		return $this->iconArray[$key] ?? null;
	}


	public function setItemsPerRow(int $number): static
	{
		$this->itemsPerRow = $number;

		return $this;
	}


	public function getItemsPerRow(): int
	{
		return $this->itemsPerRow;
	}


	/**
	 * Class of every item (form-field element)
	 */
	public function setItemClass(string $itemClass): static
	{
		$this->itemClass = $itemClass;

		return $this;
	}


	public function getItemClass(): ?string
	{
		return $this->itemClass;
	}


	/**
	 * Attributes of the element around the items
	 * @param array<string, mixed> $attributes
	 */
	public function setWrapAttributes(array $attributes): static
	{
		$this->wrapRowAttributes = $attributes;

		return $this;
	}


	/**
	 * @return array<string, mixed>
	 */
	public function getWrapAttributes(): array
	{
		return $this->wrapRowAttributes;
	}


	/**
	 * Items as a group of toggle buttons
	 */
	public function setToggleButton(bool $toggleButton = true): static
	{
		$this->toggleButton = $toggleButton;

		return $this;
	}


	public function isToggleButton(): bool
	{
		return $this->toggleButton;
	}


	/**
	 * Theme color of the toggle buttons - one for all, or per item (item key => color)
	 * @param string|array<int|string, string> $color
	 */
	public function setButtonColor(string|array $color = 'primary'): static
	{
		$this->buttonColor = $color;

		return $this;
	}


	/**
	 * Bootstrap 5 names (outline-primary) are accepted, toggle buttons are always outlined
	 */
	public function getButtonColor(string|int $key): string
	{
		$color = is_array($this->buttonColor) ? ($this->buttonColor[$key] ?? 'primary') : $this->buttonColor;

		return str_starts_with($color, 'outline-') ? substr($color, 8) : $color;
	}


	/**
	 * Theme color of the items
	 */
	public function setItemsColor(string $color): static
	{
		$this->color = $color;

		return $this;
	}
}
