<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class CheckboxlistTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second']);

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label class="col-form-label ">Checklist</label></div><div class="col-sm-8"><div class="container"><div class="row"><div class="form-check form-check-inline mr-0 col"><label><input type="checkbox" name="checkboxlist[]" id="frm-checkboxlist-first" value="first"><span class="label-text "><label for="frm-checkboxlist-first">First</label></span></label></div><div class="form-check form-check-inline mr-0 col"><label><input type="checkbox" name="checkboxlist[]" id="frm-checkboxlist-second" value="second"><span class="label-text "><label for="frm-checkboxlist-second">Second</label></span></label></div></div></div></div></div>';

		Assert::same($string, $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setOption('id', 'customId');

		$string = '<div class="form-group row" id="customId"><div class="col-sm-4 control-label align-self-center"><label class="col-form-label ">Checklist</label></div><div class="col-sm-8"><div class="container"><div class="row"><div class="form-check form-check-inline mr-0 col"><label><input type="checkbox" name="checkboxlist[]" id="frm-checkboxlist-first" value="first"><span class="label-text "><label for="frm-checkboxlist-first">First</label></span></label></div><div class="form-check form-check-inline mr-0 col"><label><input type="checkbox" name="checkboxlist[]" id="frm-checkboxlist-second" value="second"><span class="label-text "><label for="frm-checkboxlist-second">Second</label></span></label></div></div></div></div></div>';

		Assert::same($string, $form->getComponent('checkboxlist')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setTemplate(__DIR__ . '/customCheckboxlist.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}


	public function testRenderHidden()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}


	public function testRenderSkip()
	{
		$form = new Form;

		$form->addCheckboxList('checkboxlist', 'Checklist', ['first' => 'First', 'second' => 'Second'])
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('checkboxlist')->render());
	}
}

$testcase = new CheckboxlistTest;
$testcase->run();
