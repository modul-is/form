<?php

declare(strict_types=1);

namespace ModulIS\Form\Control;

use Nette;
use Nette\Utils\Html;

class Duplicator extends \ModulIS\Form\Container
{
	use \ModulIS\Form\Helper\AutoRenderSkip;

	public bool $forceDefault = false;

	public int $createDefault = 0;

	public static string|null $containerClass = null;

	protected $factoryCallback;

	private ?string $title = null;

	private bool $submittedBy = false;

	private array $created = [];

	private array|null $httpPost = null;


	public function __construct($factory, int $createDefault = 0, bool $forceDefault = false)
	{
		$this->monitor('Nette\Application\UI\Presenter');
		$this->monitor('Nette\Forms\Form');

		if(!self::$containerClass)
		{
			self::$containerClass = \ModulIS\Form\DuplicatorContainer::class;
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


	public function render()
	{
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

		$inputs = null;

		foreach($this->getComponents() as $container)
		{
			if($container instanceof \Nette\Forms\Controls\SubmitButton)
			{
				continue;
			}

			foreach($container->getComponents() as $duplicatorInput)
			{
				$inputs .= $duplicatorInput->render();
			}

			$inputs . '<hr />';
		}

		$body = Html::el('div')
			->class('card-body')
			->addHtml($inputs . '<hr />');

		$createButton = null;

		foreach($this->getButtons() as $button)
		{
			if($button instanceof DuplicatorCreateSubmit)
			{
				$createButton .= $button->render();
			}
		}

		$footer = Html::el('div')
			->class('card-footer')
			->addHtml($createButton);

		return Html::el('div')
			->id('container' . \Nette\Utils\Strings::capitalize($this->getName()))
			->class('card card-accent-primary')
			->addHtml($header . $body . $footer);
	}


	public function setFactory($factory): void
	{
		$this->factoryCallback = \Closure::fromCallable($factory);
	}


	protected function attached($obj): void
	{
		parent::attached($obj);

		if(!$obj instanceof Nette\Application\UI\Presenter && $this->form instanceof Nette\Application\UI\Form)
		{
			return;
		}

		$this->loadHttpData();
		$this->createDefault();
	}


	public function getContainers(?bool $recursive = false)
	{
		return $this->getComponents($recursive, '\ModulIS\Form\Container');
	}


	public function getButtons(?bool $recursive = false)
	{
		return $this->getComponents($recursive, 'Nette\Forms\ISubmitterControl');
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
		$controls = iterator_to_array($this->getComponents(false, 'Nette\Forms\IControl'));
		$firstControl = reset($controls);
		/* @phpstan-ignore-next-line */
		return $firstControl ? $firstControl->name : null;
	}


	protected function createContainer(): ?\ModulIS\Form\DuplicatorContainer
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


	public function setValues($values, $erase = false)
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

		return parent::setValues($values, $erase);
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
			$path = explode(self::NAME_SEPARATOR, $this->lookupPath('Nette\Forms\Form'));
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

		foreach($this->getComponents(false, 'Nette\Forms\IControl') as $control)
		{
			$components[] = $control->getName();
		}

		foreach($this->getContainers() as $container)
		{
			foreach($container->getComponents(true, 'Nette\Forms\ISubmitterControl') as $button)
			{
				$exceptChildren[] = $button->getName();
			}
		}

		$filled = $this->countFilledWithout($components, array_unique($exceptChildren));

		return $filled === iterator_count($this->getContainers());
	}


	public function addSubmit(string $name, $caption = null): SubmitButton
	{
		return $this[$name] = new DuplicatorCreateSubmit($caption);
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
}
