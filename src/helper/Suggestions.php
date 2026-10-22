<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

trait Suggestions
{
	/** @var list<string> */
	protected array $suggestions = [];

	protected bool $suggestionsCaret = true;


	/**
	 * Values offered by the browser while typing (<datalist>) - the user may still type anything else
	 *
	 * @param array<int|string, string|int|float> $suggestions
	 * @param bool $caret show a dropdown arrow in the input
	 */
	public function setSuggestions(array $suggestions, bool $caret = true): static
	{
		$this->suggestions = array_map(strval(...), array_values($suggestions));
		$this->suggestionsCaret = $caret;

		return $this;
	}


	/**
	 * @return list<string>
	 */
	public function getSuggestions(): array
	{
		return $this->suggestions;
	}


	public function hasSuggestionsCaret(): bool
	{
		return $this->suggestionsCaret;
	}
}
