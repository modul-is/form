<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class TextTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;

		$form->addText('text', 'Text');

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Text</label></div><div class="col-sm-8"><div class="input-group"><input type="text" name="text" id="frm-text" class="form-control  "></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderPrepend()
	{
		$form = new Form;

		$form->addText('text', 'Text')
			->setPrepend('prepend');

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Text</label></div><div class="col-sm-8"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">prepend</span></div><input type="text" name="text" id="frm-text" class="form-control  "></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderAppend()
	{
		$form = new Form;

		$form->addText('text', 'Text')
			->setAppend('append');

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Text</label></div><div class="col-sm-8"><div class="input-group"><input type="text" name="text" id="frm-text" class="form-control  "><div class="input-group-append"><span class="input-group-text">append</span></div></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = new Form;

		$form->addText('text', 'Text')
			->setIcon('user');

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Text</label></div><div class="col-sm-8"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><span class="fal fa-user fa-fw"></span></span></div><input type="text" name="text" id="frm-text" class="form-control  "></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}
	
	
	public function testRenderInputFloatingLabel()
	{
		$form = new Form;

		$form->addText('text', 'Text')
			->setFloatingLabel(true);

		$string = '<div class="form-floating mb-3"><input type="text" name="text" id="frm-text" class=" form-control" placeholder="Text"><label for="frm-text">Text</label></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}
	
	
	public function testRenderFormFloatingLabel()
	{
		$form = new Form;
		
		$form->setFloatingLabel(true);

		$form->addText('text', 'Text');

		$string = '<div class="form-floating mb-3"><input type="text" name="text" id="frm-text" class=" form-control" placeholder="Text"><label for="frm-text">Text</label></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}
	
	
	public function testRenderFormFloatingLabelInputDisable()
	{
		$form = new Form;
		
		$form->setFloatingLabel(true);

		$form->addText('text', 'Text')
			->setFloatingLabel(false);

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Text</label></div><div class="col-sm-8"><div class="input-group"><input type="text" name="text" id="frm-text" class="form-control  "></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = new Form;

		$form->addText('text', 'Text')
			->setOption('id', 'customId');

		$string = '<div class="form-group row" id="customId"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Text</label></div><div class="col-sm-8"><div class="input-group"><input type="text" name="text" id="frm-text" class="form-control  "></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = new Form;

		$form->addText('text', 'Text')
			->setTemplate(__DIR__ . '/customText.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('text')->render());
	}


	public function testRenderHidden()
	{
		$form = new Form;

		$form->addText('text', 'Text')
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('text')->render());
	}


	public function testRenderSkip()
	{
		$form = new Form;

		$form->addText('text', 'Text')
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('text')->render());
	}
}

$testcase = new TextTest;
$testcase->run();