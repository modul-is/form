<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

/**
 * Text input with suggestions offered while typing (<datalist>)
 */
interface HasSuggestions
{
	/**
	 * @return array<int|string, string> key => text
	 */
	public function getSuggestions(): array;

	public function hasSuggestionsCaret(): bool;

	/**
	 * Suggestions are loaded from the server while typing - the datalist is rendered even when empty
	 */
	public function hasRemoteSuggestions(): bool;
}
