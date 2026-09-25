<?php

declare(strict_types = 1);

namespace ModulIS\Form;

abstract class FormComponent extends \Nette\Application\UI\Control
{
	protected bool $renderManually = false;


	protected function beforeRender(): void
	{
	}


	public function render(): void
	{
		$this->beforeRender();

		$reflection = new \Nette\Application\UI\ComponentReflection(self::class);

		$template = $this->getLatteName($this->getClassFileName($reflection));

		if($this->renderManually)
		{
			$this->template->formTemplatePath = $template;
			$this->template->setFile($this->getLatteName($this->getClassFileName($this->getReflection())));
		}
		else
		{
			$this->template->setFile($template);
		}

		$this->template->form = $this->getComponent('form');
		$this->template->render();
	}


	protected function setRenderManually(bool $renderManually): void
	{
		$this->renderManually = $renderManually;
	}


	/**
	 * @param \ReflectionClass<object> $reflection
	 */
	private function getClassFileName(\ReflectionClass $reflection): string
	{
		$fileName = $reflection->getFileName();

		if($fileName === false)
		{
			throw new \Nette\InvalidStateException('Class ' . $reflection->getName() . ' is not defined in a file.');
		}

		return $fileName;
	}


	/**
	 * Return path to latte file for given class path
	 */
	protected function getLatteName(string $path): string
	{
		$array = explode(DIRECTORY_SEPARATOR, $path);

		end($array);

		$array[key($array)] = lcfirst(str_replace('.php', '.latte', $array[key($array)]));

		return implode(DIRECTORY_SEPARATOR, $array);
	}


	/**
	 * Magic function for render functions, render latte file with the same name as component and name of called function
	 */
	public function __call(string $name, array $arguments)
	{
		if(str_starts_with($name, 'render'))
		{
			$array = explode(DIRECTORY_SEPARATOR, $this->getClassFileName($this->getReflection()));

			end($array);

			/**
			 * Strip 'render' from called function name and put the rest after name of component eg. renderJs -> componentNameJs
			 */
			$array[key($array)] = lcfirst(str_replace('.php', ucfirst(str_replace('render', '', $name)) . '.latte', $array[key($array)]));

			$this->template->setFile(implode(DIRECTORY_SEPARATOR, $array));
			$this->template->render();
		}
		else
		{
			parent::__call($name, $arguments);
		}
	}


	public function getForm(): Form
	{
		$form = new Form;

		$form->onError[] = function()
		{
			if($this->getPresenter()->isAjax())
			{
				$this->redrawControl('form');
			}
		};

		return $form;
	}


	abstract public function createComponentForm(): Form;


	abstract public function prepare(): void;
}
