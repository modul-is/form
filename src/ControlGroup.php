<?php

declare(strict_types = 1);

namespace ModulIS\Form;

use Kravcik\LatteFontAwesomeIcon\Extension;
use Nette\ComponentModel\Component;
use Nette\Utils\Html;

class ControlGroup extends \Nette\Forms\ControlGroup
{
	protected ?string $class = null;

	protected ?string $icon = null;


	public function getInputArray(): array
	{
		$controlArray = [];

		foreach($this->getControls() as $control)
		{
			\assert($control instanceof Component);
			/**
			 * Skip submitters
			 */
			if($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link)
			{
				continue;
			}

			/**
			 * Skip inputs which are part of container
			 */
			if($control->getParent() instanceof Container)
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
			\assert($control instanceof Component);
			/**
			 * Skip submitters which are part of container
			 */
			if($control->getParent() instanceof Container)
			{
				continue;
			}

			if(!$control instanceof Control\DuplicatorCreateSubmit && ($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link))
			{
				$controlArray[] = $control;
			}
		}

		return $controlArray;
	}


	public function getHeader(): Html
	{
		$titleDiv = Html::el('div')
			->class('section-title');

		if($this->getIcon())
		{
			$iconSpan = Html::el('span')
				->class('ico')
				->addHtml(Extension::render($this->getIcon()));

			$titleDiv->addHtml($iconSpan);
		}

		$groupTitle = $this->getOption('label');

		if($groupTitle)
		{
			$titleDiv->addHtml($groupTitle);
		}

		$groupColor = $this->getOption('color') ? ' ' . $this->getOption('color') : null;

		return Html::el('div')
			->class('card-header section-header' . $groupColor)
			->addHtml($titleDiv);
	}


	public function setColor(string $color): self
	{
		return $this->setOption('color', $color);
	}


	public function setIcon(string $icon): self
	{
		$this->icon = $icon;

		return $this;
	}


	public function getIcon(): ?string
	{
		return $this->icon;
	}


	public function setClass(string $class): self
	{
		$this->class = $class;

		return $this;
	}


	public function getClass(): ?string
	{
		return $this->class;
	}


	public function add(...$items): static
	{
		foreach($items as $item)
		{
			if($item instanceof \Nette\Forms\Control || $item instanceof Container)
			{
				$this->controls[$item] = null;
			}
			elseif(is_iterable($item))
			{
				$this->add(...$item);
			}
			else
			{
				throw new \Nette\InvalidArgumentException('Control or Container items expected, ' . $item::class . ' given.');
			}
		}

		return $this;
	}
}
