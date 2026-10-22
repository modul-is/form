<?php

declare(strict_types = 1);

namespace ModulIS\Form;

use ModulIS\Form\Enum\RenderType;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Utils\Html;
use Stringable;

class Form extends UIForm
{
	use ControlFactories;

	public const GreaterEqual = UIForm::Min;

	public const LessEqual = UIForm::Max;

	public const Greater = 'ModulIS\Form\FormValidator::greater';

	public const Less = 'ModulIS\Form\FormValidator::less';

	public const SameLength = 'ModulIS\Form\FormValidator::sameLength';

	public const ValidateRC = 'ModulIS\Form\FormValidator::validateRC';

	public const ValidateIC = 'ModulIS\Form\FormValidator::validateIC';

	public ?string $color = null;

	public bool $ajax = false;

	public Html|string|null $title = null;

	public ?string $icon = null;

	public bool $noValidate = true;

	private static bool $defaultClientValidation = false;

	private ?bool $clientValidation = null;

	private ?string $buttonClass = null;

	private ?string $size = null;

	private RenderType $renderType = RenderType::Default;

	/** @var array<int|string, ControlGroup> */
	private array $groups = [];

	private string $defaultInputWrapClass = 'col-12';


	public function __construct
	(
		?IContainer $parent = null,
		?string $name = null
	)
	{
		parent::__construct($parent, $name);

		$this->setRenderer(new Renderer\Bootstrap6Renderer);
		$this->onRender[] = $this->prepareElement(...);

		$this->addGroup();
	}


	/**
	 * Form content without the <form> tag - the tag is rendered by {form} in formComponent.latte
	 */
	public function renderForm(): string
	{
		return Renderer\Bootstrap6Renderer::of($this)->renderBody($this);
	}


	public function addGroup(string|Stringable|null $caption = null, bool $setAsCurrent = true): ControlGroup
	{
		$group = new ControlGroup;
		$group->setOption('label', $caption);
		$group->setOption('visual', true);

		if($setAsCurrent)
		{
			$this->setCurrentGroup($group);
		}

		return !is_scalar($caption) || isset($this->groups[$caption])
			? $this->groups[] = $group
			: $this->groups[$caption] = $group;
	}


	public function getGroup(string|int $name): ?ControlGroup
	{
		return $this->groups[$name] ?? null;
	}


	/**
	 * Parent keeps groups in a private property, therefore it has to be overridden together with addGroup()
	 */
	public function removeGroup(string|int|\Nette\Forms\ControlGroup $name): void
	{
		if($name instanceof \Nette\Forms\ControlGroup)
		{
			$key = array_search($name, $this->groups, true);
		}
		else
		{
			$key = isset($this->groups[$name]) ? $name : false;
		}

		if($key === false)
		{
			throw new \Nette\InvalidArgumentException("Group not found in form '{$this->getName()}'");
		}

		foreach($this->groups[$key]->getControls() as $control)
		{
			if($control instanceof \Nette\ComponentModel\IComponent)
			{
				$control->getParent()?->removeComponent($control);
			}
		}

		unset($this->groups[$key]);
	}


	/**
	 * @return array<int|string, ControlGroup>
	 */
	public function getGroups(): array
	{
		return $this->groups;
	}


	/**
	 * @return list<Control\Button|Control\SubmitButton|Control\Link>
	 */
	public function getSubmitterArray(): array
	{
		$submitterArray = [];

		foreach($this->getGroups() as $group)
		{
			$submitterArray = array_merge($submitterArray, $group->getSubmitterArray());
		}

		return $submitterArray;
	}


	/**
	 * Errors of the form itself (not of its controls), already translated
	 * @return list<string|Stringable>
	 */
	public function getFormErrors(): array
	{
		return $this->getOwnErrors();
	}


	/**
	 * Class of the column around every field (default col-12, see also setWidth() of the fields)
	 */
	public function setDefaultInputWrapClass(string $defaultInputWrapClass): static
	{
		$this->defaultInputWrapClass = $defaultInputWrapClass;

		return $this;
	}


	public function getDefaultInputWrapClass(): string
	{
		return $this->defaultInputWrapClass;
	}


	public function setRenderType(RenderType $renderType): static
	{
		$this->renderType = $renderType;

		return $this;
	}


	public function setRenderDefault(): static
	{
		return $this->setRenderType(RenderType::Default);
	}


	public function setRenderFloating(): static
	{
		return $this->setRenderType(RenderType::Floating);
	}


	public function setRenderInline(): static
	{
		return $this->setRenderType(RenderType::Inline);
	}


	public function getRenderType(): RenderType
	{
		return $this->renderType;
	}


	public function setAjax(bool $ajax = true): static
	{
		$this->ajax = $ajax;

		return $this;
	}


	public function setTitle(string|Html $title): static
	{
		$this->title = $title;

		return $this;
	}


	public function getTitle(): null|Html|string
	{
		return $this->title;
	}


	/**
	 * Theme color of the cards which have no color of their own
	 */
	public function setColor(string $color): static
	{
		$this->color = $color;

		return $this;
	}


	public function setIcon(string $icon): static
	{
		$this->icon = $icon;

		return $this;
	}


	/**
	 * Default class(es) for all form buttons (e.g. "rounded rounded-4").
	 * Priority: setClass() on the button overrides this.
	 */
	public function setButtonClass(string $class): static
	{
		$this->buttonClass = $class;

		return $this;
	}


	public function getButtonClass(): ?string
	{
		return $this->buttonClass;
	}


	/**
	 * Size of all inputs and buttons - sm or lg (a control may override it by its own setSize())
	 */
	public function setSize(?string $size): static
	{
		if($size !== null && !in_array($size, ['sm', 'lg'], true))
		{
			throw new \Nette\InvalidArgumentException("Size has to be 'sm' or 'lg', '$size' given.");
		}

		$this->size = $size;

		return $this;
	}


	public function getSize(): ?string
	{
		return $this->size;
	}


	/**
	 * Without client side validation (default) the browser does not validate the form either (novalidate),
	 * false lets it check HTML attributes like required
	 */
	public function setNoValidate(bool $noValidate = true): static
	{
		$this->noValidate = $noValidate;

		return $this;
	}


	/**
	 * Default for all forms of the application - false (default) means the rules are checked by the server only
	 */
	public static function setDefaultClientValidation(bool $clientValidation): void
	{
		self::$defaultClientValidation = $clientValidation;
	}


	/**
	 * Rules are checked by nette-forms on the client before the form is sent. Off by default - the form is validated
	 * by the server only, so all errors (also of rules the client does not know) come at once.
	 * Toggles (addCondition()->toggle()) work in both cases.
	 */
	public function setClientValidation(bool $clientValidation = true): static
	{
		$this->clientValidation = $clientValidation;

		return $this;
	}


	public function isClientValidation(): bool
	{
		return $this->clientValidation ?? self::$defaultClientValidation;
	}


	/**
	 * Attributes of the <form> element - set right before rendering, so the setters may be called in any order
	 */
	private function prepareElement(): void
	{
		$element = $this->getElementPrototype();

		if($this->ajax)
		{
			$element->appendAttribute('class', 'ajax');
		}

		/**
		 * nette-forms skips a form with novalidate - with client validation it sets novalidate itself
		 * and validates on submit, assets/js skips forms marked as validated by the server
		 */
		if($this->isClientValidation())
		{
			$element->setAttribute('novalidate', null);
			$element->setAttribute('data-mis-validate', null);
		}
		else
		{
			$element->setAttribute('novalidate', $this->noValidate ?: null);
			$element->setAttribute('data-mis-validate', 'server');
		}
	}
}
