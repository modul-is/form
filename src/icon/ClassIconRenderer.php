<?php

declare(strict_types = 1);

namespace ModulIS\Form\Icon;

use Nette\Utils\Html;

/**
 * Icon font rendered as <i class="..."> - e.g. Bootstrap Icons or Font Awesome.
 * The font itself (CSS) has to be loaded by the application.
 */
class ClassIconRenderer implements IconRenderer
{
	/**
	 * @param string $pattern class of the element, %s is replaced by the icon name
	 * @param array<string, string> $aliases icon name => name in the icon set (semantic names used by the library)
	 */
	public function __construct
	(
		private string $pattern,
		private array $aliases = []
	)
	{
	}


	public static function bootstrapIcons(): self
	{
		return new self('bi bi-%s', [
			'help' => 'question-circle',
			'save' => 'floppy',
			'add' => 'plus-lg',
			'remove' => 'x-lg',
			'copy' => 'copy',
			'info' => 'info-circle'
		]);
	}


	/**
	 * @param string $style e.g. fa-solid, fa-regular, fa-light
	 */
	public static function fontAwesome(string $style = 'fa-solid'): self
	{
		return new self($style . ' fa-%s fa-fw', [
			'help' => 'circle-question',
			'save' => 'floppy-disk',
			'add' => 'plus',
			'remove' => 'xmark',
			'copy' => 'copy',
			'info' => 'circle-info'
		]);
	}


	public function render(string $icon): Html
	{
		return Html::el('i')
			->class(sprintf($this->pattern, $this->aliases[$icon] ?? $icon))
			->setAttribute('aria-hidden', 'true');
	}
}
