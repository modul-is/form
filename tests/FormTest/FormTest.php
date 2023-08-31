<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class FormTest extends Tester\TestCase
{
	public function testInputs()
	{
		$form = new Form;

		$form->addCheckbox('check');

		$form->addSubmit('submit');

		$form->addText('text');

		$form->addLink('link');

		$form->addButton('button');

		/** @var \ModulIS\Form\ControlGroup $group */
		$group = $form->getGroups()[0];

		$inputArray = $group->getInputArray();

		$inputNameArray = [];

		foreach($inputArray as $input)
		{
			$inputNameArray[] = $input->getName();
		}

		Assert::same($inputNameArray, ['check', 'text']);
	}


	public function testSubmitters()
	{
		$form = new Form;

		$form->addCheckbox('check');

		$form->addSubmit('submit');

		$form->addText('text');

		$form->addLink('link');

		$form->addButton('button');

		$submitterArray = [];

		foreach($form->getSubmitterArray() as $submitter)
		{
			$submitterArray[] = $submitter->getName();
		}

		Assert::same($submitterArray, ['submit', 'link', 'button']);
	}


	public function testGroup()
	{
		$form = new Form;

		$form->addCheckbox('check');

		$form->addSubmit('submit');

		$form->addGroup('second');

		$form->addText('text');

		$form->addLink('link');

		$form->addButton('button');

		$groupArray = $form->getGroups();

		/** @var \ModulIS\Form\ControlGroup $firstGroup */
		$firstGroup = $groupArray[0];
		/** @var \ModulIS\Form\ControlGroup $secondGroup */
		$secondGroup = $groupArray['second'];

		Assert::same(count($firstGroup->getInputArray()), 1);
		Assert::same(count($firstGroup->getSubmitterArray()), 1);
		Assert::same(count($secondGroup->getInputArray()), 1);
		Assert::same(count($secondGroup->getSubmitterArray()), 2);
	}
}

$testcase = new FormTest;
$testcase->run();
