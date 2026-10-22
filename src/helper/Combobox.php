<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

/**
 * Select enhanced by Bootstrap combobox - the native select stays (hidden) and carries the value
 */
trait Combobox
{
	protected bool $search = false;

	protected ?string $noResultMessage = null;


	/**
	 * Menu with a search field filtering the items
	 */
	public function setSearch(bool $search = true): static
	{
		$this->search = $search;

		return $this;
	}


	public function isSearch(): bool
	{
		return $this->search;
	}


	/**
	 * Message shown when the search finds nothing
	 */
	public function setNoResultMessage(?string $noResultMessage = null): static
	{
		$this->noResultMessage = $noResultMessage;

		return $this;
	}


	public function isCombobox(): bool
	{
		return $this->search;
	}


	/**
	 * Options for assets/js/combobox.js
	 *
	 * @return array<string, mixed>
	 */
	public function getComboboxOptions(): array
	{
		$placeholder = $this->getControlPrototype()->getAttribute('data-placeholder')
			?? ($this instanceof \Nette\Forms\Controls\SelectBox && $this->getPrompt() !== false ? $this->getPrompt() : null);

		return array_filter([
			'search' => $this->search,
			'placeholder' => $placeholder !== null ? $this->translate($placeholder) : null,
			'noResults' => $this->noResultMessage !== null ? $this->translate($this->noResultMessage) : null
		], fn(mixed $value): bool => $value !== null);
	}
}
