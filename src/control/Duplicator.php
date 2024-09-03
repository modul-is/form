<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use ModulIS\Form\DuplicatorContainer;
use Nette;
use Nette\Application\UI\Presenter;
use Nette\Utils\Html;

class Duplicator extends \ModulIS\Form\Container implements Renderable
{
	use \ModulIS\Form\Helper\AutoRenderSkip;
	use \ModulIS\Form\Helper\Template;

	public bool $forceDefault = false;

	public int $createDefault = 0;

	public static ?string $containerClass = null;

	protected $factoryCallback;

	private ?string $title = null;

	private bool $submittedBy = false;

	private array $created = [];

	private ?array $httpPost = null;

	private array $options = [];

	private ?string $buttonWrapClass = null;

	private ?string $duplicatorBodyClass = null;

	private ?string $duplicatorFooterClass = null;

	private ?string $duplicatorContainerClass = null;


	public function __construct($factory, int $createDefault = 0, bool $forceDefault = false)
	{
		$this->monitor(Presenter::class, function()
		{
			$this->loadHttpData();
			$this->createDefault();
		});
		$this->monitor(\Nette\Forms\Form::class);

		if(!self::$containerClass)
		{
			self::$containerClass = DuplicatorContainer::class;
		}

		try
		{
			$this->factoryCallback = \Closure::fromCallable($factory);
		}
		catch(Nette\InvalidArgumentException $e)
		{
			$type = is_object($factory) ? 'instanceof ' . $factory::class : gettype($factory);

			throw new Nette\InvalidArgumentException('Duplicator requires callable factory, ' . $type . ' given.', 0, $e);
		}

		$this->createDefault = $createDefault;
		$this->forceDefault = $forceDefault;
	}


	public function setOption(string $key, $value): self
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


	public function getOption($key, $default = null)
	{
		return $this->options[$key] ?? $default;
	}


	public function render(): Html|string
	{
		if($this->templatePath)
		{
			return (new \Latte\Engine)->renderToString($this->templatePath, $this);
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
			\assert($container instanceof DuplicatorContainer || $container instanceof DuplicatorCreateSubmit);
			if($container instanceof DuplicatorCreateSubmit)
			{
				continue;
			}

			$inputs = $key === 0 ? null : '<hr />';
			$buttons = null;

			foreach($container->getComponents() as $duplicatorInput)
			{
				\assert($duplicatorInput instanceof Renderable);
				if($duplicatorInput instanceof Button || $duplicatorInput instanceof DuplicatorRemoveSubmit || $duplicatorInput instanceof Link)
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
			->id('container' . \Nette\Utils\Strings::capitalize($this->getName()))
			->class($duplicatorContainerClass)
			->addHtml($header . $body . $footer);

		return Html::el('div')
			->class('mb-3 col-12')
			->addHtml($card);
	}


	public function setFactory($factory): void
	{
		$this->factoryCallback = \Closure::fromCallable($factory);
	}


	public function getContainers(?bool $recursive = false)
	{
		return $this->getComponents($recursive, \ModulIS\Form\Container::class);
	}


	public function getButtons(?bool $recursive = false)
	{
		return $this->getComponents($recursive, Nette\Forms\SubmitterControl::class);
	}


	protected function createComponent($name): ?Nette\ComponentModel\IComponent
	{
		$container = $this->createContainer();

		$container->currentGroup = null;
		$this->addComponent($container, $name, $this->getFirstControlName());

		call_user_func($this->factoryCallback, $container);

		return $this->created[$container->name] = $container;
	}


	private function getFirstControlName()
	{
		$controls = iterator_to_array($this->getComponents(false, \Nette\Forms\Control::class));
		$firstControl = reset($controls);
		/* @phpstan-ignore-next-line */
		return $firstControl ? $firstControl->name : null;
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


	public function createOne($name = null)
	{
		if($name === null)
		{
			$names = array_keys(iterator_to_array($this->getContainers()));
			$name = $names ? max($names) + 1 : 0;
		}

		// Container is overriden, therefore every request for getComponent($name, FALSE) would return container
		if(isset($this->created[$name]))
		{
			throw new Nette\InvalidArgumentException("Container with name '$name' already exists.");
		}

		return $this[$name];
	}


	public function setValues(array|object $values, bool $erase = false, bool $onlyDisabled = false): static
	{
		if(!$this->form->isAnchored() || !$this->form->isSubmitted())
		{
			foreach($values as $name => $value)
			{
				if((is_array($value) || $value instanceof \Traversable) && !$this->getComponent(strval($name), false))
				{
					$this->createOne($name);
				}
			}
		}

		return parent::setValues($values, $erase, $onlyDisabled);
	}


	protected function loadHttpData()
	{
		if(!$this->getForm()->isSubmitted())
		{
			return;
		}

		foreach((array) $this->getHttpData() as $name => $value)
		{
			if((is_array($value) || $value instanceof \Traversable) && !$this->getComponent(strval($name), false))
			{
				$this->createOne($name);
			}
		}
	}


	protected function createDefault()
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
			while(iterator_count($this->getContainers()) < $this->createDefault)
			{
				$this->createOne();
			}
		}
	}


	private function getHttpData()
	{
		if($this->httpPost === null)
		{
			$path = explode(self::NameSeparator, $this->lookupPath(\Nette\Forms\Form::class));
			$this->httpPost = Nette\Utils\Arrays::get($this->getForm()->getHttpData(), $path, null);
		}

		return $this->httpPost;
	}


	/**
	 * Counts filled values, filtered by given names
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

				return strlen($value);
			};
			$rows[] = array_filter(array_diff_key($item, $subComponents), $filter) ?: false;
		}

		return count(array_filter($rows));
	}


	public function isAllFilled(array $exceptChildren = []): bool
	{
		$components = [];

		foreach($this->getComponents(false, \Nette\Forms\Control::class) as $control)
		{
			$components[] = $control->getName();
		}

		foreach($this->getContainers() as $container)
		{
			foreach($container->getComponents(true, \Nette\Forms\SubmitterControl::class) as $button)
			{
				$exceptChildren[] = $button->getName();
			}
		}

		$filled = $this->countFilledWithout($components, array_unique($exceptChildren));

		return $filled === iterator_count($this->getContainers());
	}


	public function addSubmit(string $name, $caption = '', $callback = null): SubmitButton
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
