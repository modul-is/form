<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette\Utils\Html;

class TimeInput extends TextInput
{
	private ?string $min = null;

	private ?string $max = null;


	public function getControl(): Html
	{
		$control = parent::getControl()->addAttributes(['type' => 'time']);

		if($this->min)
		{
			$control->addAttributes(['min' => $this->min]);
		}

		if($this->max)
		{
			$control->addAttributes(['max' => $this->max]);
		}

		return $control;
	}


	public function getMin(): ?string
	{
		return $this->min;
	}


	public function getMax(): ?string
	{
		return $this->max;
	}


	public function setMin(?string $min): self
	{
		$this->min = $min;

		return $this;
	}


	public function setMax(?string $max): self
	{
		$this->max = $max;

		return $this;
	}
}
