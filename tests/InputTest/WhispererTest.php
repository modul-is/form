<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;

class SelectTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new \ModulIS\Form\Form;
		
		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second']);
		
		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-select" class="col-form-label ">Select</label></div><div class="col-sm-8"><div class="input-group"><select name="select" id="frm-select" class="form-control  "><option value="first">First</option><option value="second">Second</option></select></div></div></div>';
		
		Assert::same($string, $form->getComponent('select')->render()->__toString());
	}
	
	
	public function testRenderOptionId()
	{
		$form = new \ModulIS\Form\Form;
		
		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setOption('id', 'customId');
		
		$string = '<div class="form-group row" id="customId"><div class="col-sm-4 control-label align-self-center"><label for="frm-select" class="col-form-label ">Select</label></div><div class="col-sm-8"><div class="input-group"><select name="select" id="frm-select" class="form-control  "><option value="first">First</option><option value="second">Second</option></select></div></div></div>';
		
		Assert::same($string, $form->getComponent('select')->render()->__toString());
	}
	
	
	public function testRenderCustomTemplate()
	{
		$form = new \ModulIS\Form\Form;
		
		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setTemplate(__DIR__ . '/customSelect.latte');
		
		$string = 'custom-template';
		
		Assert::same($string, $form->getComponent('select')->render());
	}
	
	
	public function testRenderHidden()
	{
		$form = new \ModulIS\Form\Form;
		
		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setOption('hide', true);
		
		$string = '';
		
		Assert::same($string, $form->getComponent('select')->render());
	}
	
	
	public function testRenderSkip()
	{
		$form = new \ModulIS\Form\Form;
		
		$form->addSelect('select', 'Select', ['first' => 'First', 'second' => 'Second'])
			->setAutoRenderSkip();
		
		$string = '';
		
		Assert::same($string, $form->getComponent('select')->render());
	}

}

$testcase = new SelectTest;
$testcase->run();