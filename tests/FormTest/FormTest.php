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
		
		$inputArray = $form->getBoxes()[0]->getInputArray();
		
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
	
	
	public function testBox()
	{
		$form = new Form;
		
		$form->addCheckbox('check');
		
		$form->addSubmit('submit');
		
		$form->addBox(1);
		
		$form->addText('text');
		
		$form->addLink('link');
		
		$form->addButton('button');
		
		$boxArray = $form->getBoxes();
		
		Assert::same(count($boxArray[0]->getInputArray()), 1);
		Assert::same(count($boxArray[0]->getSubmitterArray()), 1);
		Assert::same(count($boxArray[1]->getInputArray()), 1);
		Assert::same(count($boxArray[1]->getSubmitterArray()), 2);
	}
}

$testcase = new FormTest;
$testcase->run();