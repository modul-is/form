<?php

declare(strict_types = 1);

namespace ModulIS\Form\Demo\Presenter;

use ModulIS\Form\Demo\Form\DemoForm;
use ModulIS\Form\Demo\Form\ManualForm;


class DemoPresenter extends \Nette\Application\UI\Presenter
{
	public function actionDefault(): void
	{
		$this->getComponent('demoForm')->prepare();
	}


	public function renderDefault(): void
	{
		$this->template->submittedValues = $this->getComponent('demoForm')->getSubmittedValues();
	}


	protected function createComponentDemoForm(): DemoForm
	{
		return new DemoForm;
	}


	protected function createComponentManualForm(): ManualForm
	{
		return new ManualForm;
	}
}
