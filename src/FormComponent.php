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


	public function getForm()
	{
		return new Form;
	}


	abstract public function createComponentForm(): Form;


	abstract public function prepare(): void;
}
