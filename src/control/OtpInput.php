<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use ModulIS\Form\Form;
use ModulIS\Form\Helper;

/**
 * One-time code - a single text input rendered as separate slots (Bootstrap otp)
 */
class OtpInput extends \Nette\Forms\Controls\TextInput implements InputField, Signalable, \Nette\Application\UI\SignalReceiver
{
	use Helper\Field;
	use Helper\Signals;

	private const array Patterns = [
		'numeric' => '[0-9]',
		'alphanumeric' => '[A-Za-z0-9]',
		'alpha' => '[A-Za-z]'
	];

	private string $type = 'numeric';

	private bool $mask = false;

	/** @var list<int> */
	private array $groups = [];


	public function __construct
	(
		string|\Stringable|null $label = null,
		private int $length = 6
	)
	{
		if($length < 1)
		{
			throw new \Nette\InvalidArgumentException('Length of the code must be at least 1, ' . $length . ' given.');
		}

		parent::__construct($label, $length);

		$this->addRule(Form::Pattern, 'Zadejte celý kód.', $this->getPattern());
	}


	/**
	 * Allowed characters - numeric (default), alphanumeric or alpha
	 */
	public function setType(string $type): static
	{
		if(!isset(self::Patterns[$type]))
		{
			throw new \Nette\InvalidArgumentException("Type has to be one of '" . implode("', '", array_keys(self::Patterns)) . "', '$type' given.");
		}

		$this->type = $type;

		/**
		 * Pattern rule added by the constructor follows the type
		 */
		foreach($this->getRules() as $rule)
		{
			if($rule->validator === Form::Pattern && $rule->control === $this)
			{
				$rule->arg = $this->getPattern();
			}
		}

		/**
		 * Nette copies the pattern of the rule to the HTML attribute when the rule is added
		 */
		$this->getControlPrototype()->pattern = $this->getPattern();

		return $this;
	}


	public function getType(): string
	{
		return $this->type;
	}


	public function getLength(): int
	{
		return $this->length;
	}


	/**
	 * Slots show a dot instead of the character
	 */
	public function setMask(bool $mask = true): static
	{
		$this->mask = $mask;

		return $this;
	}


	public function isMasked(): bool
	{
		return $this->mask;
	}


	/**
	 * Sizes of slot groups divided by a separator, e.g. [3, 3]
	 * @param list<int> $groups
	 */
	public function setGroups(array $groups): static
	{
		$this->groups = $groups;

		return $this;
	}


	/**
	 * @return list<int>
	 */
	public function getGroups(): array
	{
		return $this->groups;
	}


	private function getPattern(): string
	{
		return self::Patterns[$this->type] . '{' . $this->length . '}';
	}
}
