<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;
use Nette\Utils\Html;

class SelectBox extends \Nette\Forms\Controls\SelectBox implements InputField, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\Signals;
	use Helper\Combobox
	{
		isCombobox as protected comboboxIsCombobox;
	}

	/** @var array<int|string, string> */
	private array $imageArray = [];


	/**
	 * Images of the items (item key => image URL) - the select is rendered as a combobox
	 * @param array<int|string, string> $imageArray
	 */
	public function setImageArray(array $imageArray): static
	{
		$this->imageArray = $imageArray;

		return $this;
	}


	/**
	 * @return array<int|string, string>
	 */
	public function getImageArray(): array
	{
		return $this->imageArray;
	}


	public function isCombobox(): bool
	{
		return $this->comboboxIsCombobox() || $this->imageArray !== [];
	}


	public function getControl(): Html
	{
		if($this->imageArray)
		{
			$this->setOptionAttribute('data-mis-image:', $this->imageArray);
		}

		return parent::getControl();
	}
}
