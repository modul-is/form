<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait Suggestions
{
	/** @var array<int|string, string> */
	protected array $suggestions = [];

	protected bool $suggestionsCaret = true;


	/**
	 * Values offered by the browser while typing (<datalist>) - the user may still type anything else
	 *
	 * @param array<int|string, string|int|float|\Stringable> $suggestions
	 * @param bool $caret show a dropdown arrow in the input
	 */
	public function setSuggestions(array $suggestions, bool $caret = true): static
	{
		$this->suggestions = array_map(strval(...), $suggestions);
		$this->suggestionsCaret = $caret;

		return $this;
	}


	/**
	 * @return array<int|string, string>
	 */
	public function getSuggestions(): array
	{
		return $this->suggestions;
	}


	public function hasSuggestionsCaret(): bool
	{
		return $this->suggestionsCaret;
	}


	public function hasRemoteSuggestions(): bool
	{
		return false;
	}
}
