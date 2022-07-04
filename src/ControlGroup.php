<?php

declare(strict_types=1);

namespace ModulIS\Form;

class ControlGroup extends \Nette\Forms\ControlGroup
{
	public function getInputArray(): array
	{
		$controlArray = [];

		/**
		 * Skip submitters
		 */
		foreach($this->getControls() as $control)
		{
			if($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link)
			{
				continue;
			}

			$controlArray[] = $control;
		}

		return $controlArray;
	}


	public function getSubmitterArray(): array
	{
		$controlArray = [];

		/**
		 * Only submitters
		 */
		foreach($this->getControls() as $control)
		{
			if($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link)
			{
				$controlArray[] = $control;
			}
		}

		return $controlArray;
	}


	public function setColor(string $color): self
	{
		return $this->setOption('color', $color);
	}


	public function add(...$items): static
	{
		foreach($items as $item)
		{
			if($item instanceof \Nette\Forms\Control || $item instanceof Container)
			{
				$this->controls->attach($item);
			}
			elseif(is_iterable($item))
			{
				$this->add(...$item);
			}
			else
			{
				$type = is_object($item) ? $item::class : gettype($item);
				throw new \Nette\InvalidArgumentException("Control or Container items expected, $type given.");
			}
		}

		return $this;
	}
}
