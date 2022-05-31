<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class TextAreaTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;

		$form->addTextArea('text', 'Area');

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Area</label></div><div class="col-sm-8"><div class="input-group"><textarea name="text" id="frm-text" class="form-control  "></textarea></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderPrepend()
	{
		$form = new Form;

		$form->addTextArea('text', 'Area')
			->setPrepend('prepend');

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Area</label></div><div class="col-sm-8"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">prepend</span></div><textarea name="text" id="frm-text" class="form-control  "></textarea></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderAppend()
	{
		$form = new Form;

		$form->addTextArea('text', 'Area')
			->setAppend('append');

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Area</label></div><div class="col-sm-8"><div class="input-group"><textarea name="text" id="frm-text" class="form-control  "></textarea><div class="input-group-append"><span class="input-group-text">append</span></div></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderIcon()
	{
		$form = new Form;

		$form->addTextArea('text', 'Area')
			->setIcon('user');

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Area</label></div><div class="col-sm-8"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><span class="fal fa-user fa-fw"></span></span></div><textarea name="text" id="frm-text" class="form-control  "></textarea></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}
	
	
	public function testRenderInputFloatingLabel()
	{
		$form = new Form;

		$form->addTextArea('text', 'Area')
			->setFloatingLabel(true);

		$string = '<div class="form-floating mb-3"><textarea name="text" id="frm-text" class=" form-control" placeholder="Area"></textarea><label for="frm-text">Area</label></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}
	
	
	public function testRenderFormFloatingLabel()
	{
		$form = new Form;
		
		$form->setFloatingLabel(true);

		$form->addTextArea('text', 'Area');

		$string = '<div class="form-floating mb-3"><textarea name="text" id="frm-text" class=" form-control" placeholder="Area"></textarea><label for="frm-text">Area</label></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}
	
	
	public function testRenderFormFloatingLabelInputDisable()
	{
		$form = new Form;
		
		$form->setFloatingLabel(true);

		$form->addTextArea('text', 'Area')
			->setFloatingLabel(false);

		$string = '<div class="form-group row"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Area</label></div><div class="col-sm-8"><div class="input-group"><textarea name="text" id="frm-text" class="form-control  "></textarea></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderOptionId()
	{
		$form = new Form;

		$form->addTextArea('text', 'Area')
			->setOption('id', 'customId');

		$string = '<div class="form-group row" id="customId"><div class="col-sm-4 control-label align-self-center"><label for="frm-text" class="col-form-label ">Area</label></div><div class="col-sm-8"><div class="input-group"><textarea name="text" id="frm-text" class="form-control  "></textarea></div></div></div>';

		Assert::same($string, $form->getComponent('text')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = new Form;

		$form->addTextArea('text', 'Text')
			->setTemplate(__DIR__ . '/customText.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('text')->render());
	}


	public function testRenderHidden()
	{
		$form = new Form;

		$form->addTextArea('text', 'Text')
			->setOption('hide', true);

		$string = '';

		Assert::same($string, $form->getComponent('text')->render());
	}


	public function testRenderSkip()
	{
		$form = new Form;

		$form->addTextArea('text', 'Text')
			->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('text')->render());
	}
}

$testcase = new TextAreaTest;
$testcase->run();
