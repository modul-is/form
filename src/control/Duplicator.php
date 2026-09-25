<?php

declare(strict_types = 1);

namespace ModulIS\Form\Control;

use Closure;
use Latte\Engine;
use ModulIS\Form\Container;
use ModulIS\Form\DuplicatorContainer;
use ModulIS\Form\Helper\AutoRenderSkip;
use ModulIS\Form\Helper\Template;
use Nette;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Nette\Utils\Strings;
use Stringable;
use Traversable;
use function assert;

class Duplicator extends Container implements Renderable
{
	use AutoRenderSkip;
	use Template;

	public bool $forceDefault = false;

	public int $createDefault = 0;

	public static ?string $containerClass = null;

	/** @var Closure(DuplicatorContainer): void */
	protected Closure $factoryCallback;

	private ?string $title = null;

	private bool $submittedBy = false;

	/** @var array<int|string, DuplicatorContainer> */
	private array $created = [];

	/** @var ?array<mixed> */
	private ?array $httpPost = null;

	/** @var array<string, mixed> */
	private array $options = [];

	private ?string $buttonWrapClass = null;

	private ?string $duplicatorBodyClass = null;

	private ?string $duplicatorFooterClass = null;

	private ?string $duplicatorContainerClass = null;


	/**
	 * @param callable(DuplicatorContainer): void $factory
	 */
	public function __construct
	(
		callable $factory, int $createDefault = 0, bool $forceDefault = false
	)
	{
		$this->monitor(Presenter::class, function()
		{
			$this->loadHttpData();
			$this->createDefault();
		});

		if(!self::$containerClass)
		{
			self::$containerClass = DuplicatorContainer::class;
		}

		$this->factoryCallback = Closure::fromCallable($factory);

		$this->createDefault = $createDefault;
		$this->forceDefault = $forceDefault;
	}


	public function setOption(string $key, mixed $value): self
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


	public function getOption(string $key, mixed $default = null): mixed
	{
		return $this->options[$key] ?? $default;
	}


	public function render(): Html|string
	{
		if($this->templatePath)
		{
			return (new Engine)->renderToString($this->templatePath, $this);
		}

		if($this->autoRenderSkip === true)
		{
			return '';
		}

		if($this->getTitle())
		{
			$header = Html::el('div')
				->class('card-header')
				->addHtml($this->getTitle());
		}
		else
		{
			$header = null;
		}

		$body = null;
		$bodyRow = null;
		$buttonWrapClass = $this->buttonWrapClass ?? 'mb-3 col-12';
		$duplicatorBodyClass = $this->duplicatorBodyClass ?? 'card-body';
		$duplicatorFooterClass = $this->duplicatorFooterClass ?? 'card-footer';
		$duplicatorContainerClass = $this->duplicatorContainerClass ?? 'card card-accent-primary';

		foreach($this->getComponents() as $key => $container)
		{
			assert($container instanceof DuplicatorContainer || $container instanceof DuplicatorCreateSubmit);
			if($container instanceof DuplicatorCreateSubmit)
			{
				continue;
			}

			$inputs = null;
			$buttons = null;

			$containerHeader = Html::el('div')
				->class('duplicator-head');

			if($container->getTitle())
			{
				$title = Html::el('div')
					->class('lbl')
					->addText($container->getTitle());

				$containerHeader->addHtml($title);
			}

			$removeSubmit = $container->getComponent('del', false);

			if($removeSubmit instanceof DuplicatorRemoveSubmit)
			{
				$containerHeader->addHtml($removeSubmit->render());
			}

			foreach($container->getComponents() as $duplicatorInput)
			{
				assert($duplicatorInput instanceof Renderable);

				if($duplicatorInput instanceof DuplicatorRemoveSubmit)
				{
					continue;
				}

				if($duplicatorInput instanceof Button || $duplicatorInput instanceof Link)
				{
					$buttons .= $duplicatorInput->render();
				}
				else
				{
					$inputs .= $duplicatorInput->render();
				}
			}

			if($buttons)
			{
				$inputs .= Html::el('div')
					->class($buttonWrapClass)
					->addHtml($buttons);
			}

			$bodyRow .= Html::el('div')
				->class('row')
				->addHtml($containerHeader)
				->addHtml($inputs);
		}

		if($bodyRow)
		{
			$body = Html::el('div')
				->class($duplicatorBodyClass)
				->addHtml($bodyRow);
		}

		$footer = null;
		$createButton = null;

		foreach($this->getButtons() as $button)
		{
			if($button instanceof DuplicatorCreateSubmit)
			{
				$createButton .= $button->render();
			}
		}

		if($createButton)
		{
			$footer = Html::el('div')
				->class($duplicatorFooterClass)
				->addHtml($createButton);
		}

		$card = Html::el('div')
			->id('container' . Strings::capitalize($this->getName()))
			->class($duplicatorContainerClass)
			->addHtml($header . $body . $footer);

		return Html::el('div')
			->class('mb-3 col-12')
			->addHtml($card);
	}


	/**
	 * @param callable(DuplicatorContainer): void $factory
	 */
	public function setFactory(callable $factory): void
	{
		$this->factoryCallback = Closure::fromCallable($factory);
	}


	/**
	 * @template T of Nette\ComponentModel\IComponent
	 * @param class-string<T> $filterClass
	 * @return array<int|string, T>
	 */
	private function getFilteredComponents(bool $recursive, string $filterClass): array
	{
		$components = $recursive ? $this->getComponentTree() : $this->getComponents();

		$componentArray = [];

		foreach($components as $component)
		{
			if($component instanceof $filterClass)
			{
				/**
				 * Names are unique only within a single level, therefore the recursive tree stays a plain list.
				 * On a single level the name must be preserved - createOne() derives the new container name from it.
				 */
				if($recursive)
				{
					$componentArray[] = $component;
				}
				else
				{
					$componentArray[$component->getName()] = $component;
				}
			}
		}

		return $componentArray;
	}


	/**
	 * @return array<int|string, Container>
	 */
	public function getContainers(bool $recursive = false): array
	{
		return $this->getFilteredComponents($recursive, Container::class);
	}


	/**
	 * @return array<int|string, Nette\Forms\Controls\SubmitButton>
	 */
	public function getButtons(bool $recursive = false): array
	{
		return $this->getFilteredComponents($recursive, Nette\Forms\Controls\SubmitButton::class);
	}


	protected function createComponent(string $name): ?Nette\ComponentModel\IComponent
	{
		$container = $this->createContainer();

		$container->currentGroup = null;
		$this->addComponent($container, $name, $this->getFirstControlName());

		call_user_func($this->factoryCallback, $container);

		return $this->created[$container->getName()] = $container;
	}


	public function removeComponent(Nette\ComponentModel\IComponent $component): void
	{
		if($component->getName() !== null)
		{
			unset($this->created[$component->getName()]);
		}

		parent::removeComponent($component);
	}


	private function getFirstControlName(): ?string
	{
		$controls = $this->getFilteredComponents(false, BaseControl::class);
		$firstControl = reset($controls);

		return $firstControl ? $firstControl->getName() : null;
	}


	protected function createContainer(): ?DuplicatorContainer
	{
		$class = self::$containerClass;
		return new $class;
	}


	public function isSubmittedBy(): bool
	{
		if($this->submittedBy)
		{
			return true;
		}

		foreach($this->getButtons(true) as $button)
		{
			if($button->isSubmittedBy())
			{
				return $this->submittedBy = true;
			}
		}

		return false;
	}


	public function createOne(string|int|null $name = null): DuplicatorContainer
	{
		$containers = $this->getContainers();

		if($name === null)
		{
			$names = array_filter(array_keys($containers), 'is_numeric');

			$name = $names ? max(array_map('intval', $names)) + 1 : 0;
		}

		// Container is overriden, therefore every request for getComponent($name, FALSE) would return container
		if(isset($this->created[$name]))
		{
			throw new Nette\InvalidArgumentException("Container with name '$name' already exists.");
		}

		$container = $this->getComponent((string) $name);
		assert($container instanceof DuplicatorContainer);

		return $container;
	}


	public function setValues(array|object $values, bool $erase = false, bool $onlyDisabled = false): static
	{
		if(!$this->form->isAnchored() || !$this->form->isSubmitted())
		{
			foreach($values instanceof Traversable ? iterator_to_array($values) : (array) $values as $name => $value)
			{
				if((is_array($value) || $value instanceof Traversable) && !$this->getComponent(strval($name), false))
				{
					$this->createOne($name);
				}
			}
		}

		return parent::setValues($values, $erase, $onlyDisabled);
	}


	protected function loadHttpData(): void
	{
		if(!$this->getForm()->isSubmitted())
		{
			return;
		}

		foreach((array) $this->getHttpData() as $name => $value)
		{
			if((is_array($value) || $value instanceof Traversable) && !$this->getComponent(strval($name), false))
			{
				$this->createOne($name);
			}
		}
	}


	protected function createDefault(): void
	{
		if(!$this->createDefault)
		{
			return;
		}

		if(!$this->getForm()->isSubmitted())
		{
			foreach(range(0, $this->createDefault - 1) as $key)
			{
				$this->createOne($key);
			}
		}
		elseif($this->forceDefault)
		{
			while(count($this->getContainers()) < $this->createDefault)
			{
				$this->createOne();
			}
		}
	}


	/**
	 * @return ?array<mixed>
	 */
	private function getHttpData(): ?array
	{
		if($this->httpPost === null)
		{
			$path = explode(self::NameSeparator, $this->lookupPath(Form::class));

			$httpData = $this->getForm()->getHttpData();
			$post = is_array($httpData) ? Nette\Utils\Arrays::get($httpData, $path, null) : null;

			/** @phpstan-ignore function.impossibleType (Nette types HTTP data as flat strings, nested containers yield arrays) */
			$this->httpPost = is_array($post) ? $post : null;
		}

		return $this->httpPost;
	}


	/**
	 * Counts filled values, filtered by given names
	 * @param list<string> $components
	 * @param list<string> $subComponents
	 */
	public function countFilledWithout(array $components = [], array $subComponents = []): int
	{
		$httpData = array_diff_key((array) $this->getHttpData(), array_flip($components));

		if(!$httpData)
		{
			return 0;
		}

		$rows = [];

		$subComponents = array_flip($subComponents);

		foreach($httpData as $item)
		{
			$filter = function($value) use (&$filter)
			{
				if(is_array($value))
				{
					return count(array_filter($value, $filter)) > 0;
				}

				return strlen($value) > 0;
			};
			$rows[] = array_filter(array_diff_key($item, $subComponents), $filter) ?: false;
		}

		return count(array_filter($rows));
	}


	/**
	 * @param list<string> $exceptChildren
	 */
	public function isAllFilled(array $exceptChildren = []): bool
	{
		$components = [];

		foreach($this->getFilteredComponents(false, BaseControl::class) as $control)
		{
			$components[] = $control->getName();
		}

		foreach($this->getFilteredComponents(true, Nette\Forms\Controls\SubmitButton::class) as $button)
		{
			$exceptChildren[] = $button->getName();
		}

		$filled = $this->countFilledWithout($components, array_values(array_unique($exceptChildren)));

		return $filled === count($this->getContainers());
	}


	public function addSubmit(string $name, Stringable|string|null $caption = null, $callback = null): SubmitButton
	{
		$control = new DuplicatorCreateSubmit($caption);

		$control->setValidationScope([])
			->addCreateOnClick(true, $callback);

		return $this[$name] = $control;
	}


	public function setTitle(string $title): self
	{
		$this->title = $title;
		return $this;
	}


	public function getTitle(): ?string
	{
		return $this->title;
	}


	public function setDuplicatorButtonWrapClass(string $class): self
	{
		$this->buttonWrapClass = $class;

		return $this;
	}


	public function setDuplicatorBodyClass(string $class): self
	{
		$this->duplicatorBodyClass = $class;

		return $this;
	}


	public function setDuplicatorFooterClass(string $class): self
	{
		$this->duplicatorFooterClass = $class;

		return $this;
	}


	public function setDuplicatorContainerClass(string $class): self
	{
		$this->duplicatorContainerClass = $class;

		return $this;
	}
}
