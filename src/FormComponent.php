<?php

declare(strict_types=1);

namespace ModulIS\Form;

abstract class FormComponent extends \Nette\Application\UI\Control
{
	protected bool $renderManually = false;


	public function render()
	{
		$this->beforeRender();

		$template = $this->getLatteName($this->getReflection()->getParentClass()->getFileName());

		if($this->renderManually)
		{
			$this->template->formTemplatePath = $template;
			$this->template->setFile($this->getLatteName($this->getReflection()->getFileName()));
		}
		else
		{
			$this->template->setFile($template);
		}

		$this->template->form = $this->getComponent('form');
		$this->template->render();
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
	 * Magic fucntion for render functions, render latte file with the same name as component and name of called function
	 */
	public function __call($name, $arguments)
	{
		if(\Nette\Utils\Strings::startsWith($name, 'render'))
		{
			$array = explode(DIRECTORY_SEPARATOR, $this->getReflection()->getFileName());

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


	public function getForm()
	{
		return new Form;
	}


	abstract public function createComponentForm(): Form;


	abstract public function prepare(): void;
}
