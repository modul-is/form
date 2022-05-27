<?php

declare(strict_types=1);

namespace ModulIS\Form;

use ModulIS\Form\Control;

final class Box
{
	protected \SplObjectStorage $controls;

	private array $options = [];


	public function __construct()
	{
		$this->controls = new \SplObjectStorage;
	}


	public function add($item): self
	{
		$this->controls->attach($item);

		return $this;
	}


	public function remove(\Nette\Forms\IControl $control)
	{
			$this->controls->detach($control);
	}


	public function removeOrphans()
	{
		foreach($this->controls as $control)
		{
			if(!$control->getForm(false))
			{
				$this->controls->detach($control);
			}
		}
	}
	
	
	public function getControls(): array
	{
		return iterator_to_array($this->controls);
	}


	public function getInputArray(): array
	{
		$controlArray = [];
		
		/**
		 * Skip submitters
		 */
		foreach(iterator_to_array($this->controls) as $control)
		{
			if($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link)
			{
				continue;
			}
			
			$controlArray = $control;
		}
		
		return $controlArray;
	}
	
	
	public function getSubmitterArray(): array
	{
		$controlArray = [];
		
		/**
		 * Only submitters
		 */
		foreach(iterator_to_array($this->controls) as $control)
		{
			if($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link)
			{
				$controlArray = $control;
			}
		}
		
		return $controlArray;
	}


	/**
	 * Sets user-specific option.
	 * Options recognized by DefaultFormRenderer
	 * - 'label' - textual or IHtmlString object label
	 * - 'visual' - indicates visual group
	 * - 'container' - container as Html object
	 * - 'description' - textual or IHtmlString object description
	 * - 'embedNext' - describes how render next group
	 */
	public function setOption(string $key, ?string $value): self
	{
		if($value === null)
		{
			unset($this->options[$key]);
		}
		else
		{
			$this->options[$key] = $value;
		}

		return $this;
	}


	public function getOption($key, $default = null)
	{
		return $this->options[$key] ?? $default;
	}


	public function getOptions(): array
	{
		return $this->options;
	}


	public function setColor(string $color): self
	{
		return $this->setOption('color', $color);
	}
}
