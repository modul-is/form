<?php

declare(strict_types = 1);

namespace ModulIS\Form;

use Nette\Utils\Html;

class Container extends \Nette\Forms\Container implements Control\Renderable
{
	use ControlFactories;

	private ?string $color = null;

	private Html|string|null $title = null;

	private ?string $id = null;

	private bool $showCard = false;

	private ?string $wrapClass = null;


	public function setId(string $id): static
	{
		$this->id = $id;

		return $this;
	}


	public function getId(): ?string
	{
		return $this->id;
	}


	/**
	 * Theme color of the card (see showCard())
	 */
	public function setColor(string $color): static
	{
		$this->color = $color;

		return $this;
	}


	public function getColor(): ?string
	{
		return $this->color;
	}


	public function setTitle(Html|string $title): static
	{
		$this->title = $title;

		return $this;
	}


	public function getTitle(): Html|string|null
	{
		return $this->title;
	}


	/**
	 * Class of the column around the container (default col-12)
	 */
	public function setWrapClass(string $wrapClass): static
	{
		$this->wrapClass = $wrapClass;

		return $this;
	}


	public function getWrapClass(): ?string
	{
		return $this->wrapClass;
	}


	public function showCard(bool $showCard): static
	{
		$this->showCard = $showCard;

		return $this;
	}


	public function isCard(): bool
	{
		return $this->showCard;
	}


	public function render(): string|Html
	{
		return Renderer\Bootstrap6Renderer::of($this)->renderContainer($this);
	}


	/**
	 * @return list<\Nette\ComponentModel\IComponent>
	 */
	public function getInputArray(): array
	{
		$controlArray = [];

		/**
		 * Skip submitters
		 */
		foreach($this->getComponents() as $control)
		{
			if($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link)
			{
				continue;
			}

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
		foreach($this->getComponents() as $control)
		{
			if($control instanceof Control\Button || $control instanceof Control\SubmitButton || $control instanceof Control\Link)
			{
				$controlArray[] = $control;
			}
		}

		return $controlArray;
	}
}
