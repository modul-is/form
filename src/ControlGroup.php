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


	/**
	 * @return list<Control\Renderable|\Nette\Forms\Controls\HiddenField>
	 */
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

			\assert($control instanceof Control\Renderable || $control instanceof \Nette\Forms\Controls\HiddenField);
			$controlArray[] = $control;
		}

		return $controlArray;
	}


	/**
	 * @return list<Control\Button|Control\SubmitButton|Control\Link>
	 */
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


	/**
	 * Title and icon are fallbacks used when the group has none of its own (form title on the first card)
	 */
	public function getHeader(Html|string|null $title = null, ?string $icon = null): Html
	{
		$icon = $this->getIcon() ?? $icon;

		$titleDiv = Html::el('div')
			->class('section-title');

		if($icon)
		{
			$iconSpan = Html::el('span')
				->class('ico')
				->addHtml(Extension::render($icon));

			$titleDiv->addHtml($iconSpan);
		}

		$groupTitle = $this->getOption('label') ?: $title;

		if($groupTitle)
		{
			$titleDiv->addText($groupTitle);
		}

		$groupColor = $this->getOption('color') ? ' ' . $this->getOption('color') : null;

		return Html::el('div')
			->class('card-header section-header' . $groupColor)
			->addHtml($titleDiv);
	}


	public function setColor(string $color): static
	{
		return $this->setOption('color', $color);
	}


	public function setIcon(string $icon): static
	{
		$this->icon = $icon;

		return $this;
	}


	public function getIcon(): ?string
	{
		return $this->icon;
	}


	public function setClass(string $class): static
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
				/** Containers are stored alongside controls on purpose - Nette types the map for controls only */
				/** @phpstan-ignore offsetAssign.dimType */
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
