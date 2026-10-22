<?php

declare(strict_types = 1);

namespace ModulIS\Form\Helper;

use ModulIS\Form\Renderer\Bootstrap6Renderer;
use Nette\Utils\Html;

/**
 * Rendering is delegated to the renderer of the form
 */
trait Render
{
	public function render(): Html|string
	{
		return Bootstrap6Renderer::of($this)->renderControl($this);
	}


	/**
	 * Control without wrapper and label - {inputCore name}
	 */
	public function getCoreControl(): Html|string
	{
		return Bootstrap6Renderer::of($this)->renderCore($this);
	}


	/**
	 * Caption with required mark and tooltip - {labelCore name}
	 */
	public function getCoreLabel(): Html|string|null
	{
		return Bootstrap6Renderer::of($this)->renderLabel($this);
	}


	/**
	 * {inputCore name:key} - with a key, lists render a single item
	 */
	public function getCoreControlPart(string|int|null $key = null): Html|string
	{
		return Bootstrap6Renderer::of($this)->renderCore($this, $key);
	}


	/**
	 * {labelCore name:key} - with a key, lists render the label of a single item
	 */
	public function getCoreLabelPart(string|int|null $key = null): Html|string|null
	{
		return Bootstrap6Renderer::of($this)->renderLabel($this, $key);
	}
}
