<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Helper;

class SelectBox extends \Nette\Forms\Controls\SelectBox implements InputField, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\Signals;

	/** @var array<int|string, string> */
	private array $imageArray = [];


	/**
	 * Images of the items (item key => image URL)
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
}
