<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class CheckboxTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;
		
		$form->addCheckbox('check', 'Check');
		
		$checkboxString = '<div class="form-group row"><div class="col-sm-8 offset-sm-4"><div class="form-check "><label><input type="checkbox" name="check" id="frm-check"><span class="label-text ">Check</span></label></div></div></div>';
		
		Assert::same($checkboxString, $form->getComponent('check')->render()->__toString());
	}
	
	
	public function testRenderOptionId()
	{
		$form = new Form;
		
		$form->addCheckbox('check', 'Check')
			->setOption('id', 'customId');
		
		$checkboxString = '<div class="form-group row" id="customId"><div class="col-sm-8 offset-sm-4"><div class="form-check "><label><input type="checkbox" name="check" id="frm-check"><span class="label-text ">Check</span></label></div></div></div>';
		
		Assert::same($checkboxString, $form->getComponent('check')->render()->__toString());
	}
	
	
	public function testRenderCustomTemplate()
	{
		$form = new Form;
		
		$form->addCheckbox('check', 'Check')
			->setTemplate(__DIR__ . '/customCheckbox.latte');
		
		$checkboxString = 'custom-template';
		
		Assert::same($checkboxString, $form->getComponent('check')->render());
	}
	
	
	public function testRenderHidden()
	{
		$form = new Form;
		
		$form->addCheckbox('check', 'Check')
			->setOption('hide', true);
		
		$checkboxString = '';
		
		Assert::same($checkboxString, $form->getComponent('check')->render());
	}
	
	
	public function testRenderSkip()
	{
		$form = new Form;
		
		$form->addCheckbox('check', 'Check')
			->setAutoRenderSkip();
		
		$checkboxString = '';
		
		Assert::same($checkboxString, $form->getComponent('check')->render());
	}
	
	
	public function testRenderTooltip()
	{
		$form = new Form;
		
		$form->addCheckbox('check', 'Check')
			->setTooltip('MyTooltip');
		
		$checkboxString = '<div class="form-group row"><div class="col-sm-8 offset-sm-4"><div class="form-check "><label><input type="checkbox" name="check" id="frm-check"><span class="label-text ">Check</span></label><span title="MyTooltip" data-placement="top" data-toggle="tooltip"><span class="fal fa-question-circle color-blue fa-fw"></span></span></div></div></div>';
		
		Assert::same($checkboxString, $form->getComponent('check')->render()->__toString());
	}

}

$testcase = new CheckboxTest;
$testcase->run();