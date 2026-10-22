<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Nette\HtmlStringable;
use Nette\Utils\Html;

/**
 * Control rendered with a label, validation feedback and description - settings read by the renderer
 */
interface Field extends Renderable
{
	public function getTooltip(): ?string;

	public function getDescription(): string|\Stringable|null;

	public function createWrap(?string $class = null): Html;

	public function getLabelWrapClass(): ?string;

	public function getInputWrapClass(): ?string;

	public function getRowClass(): ?string;

	public function getValidationClass(): ?string;

	public function getValidationSuccessMessage(): string|HtmlStringable|null;

	public function isSubmitted(): bool;

	public function getSize(): ?string;
}
