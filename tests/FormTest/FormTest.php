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


	private function getAnchoredForm(): Form
	{
		$form = new Form;

		(new ModulIS\Form\Tests\TestPresenter)->addComponent($form, 'form');

		return $form;
	}


	public function testFooterWhenLastGroupHasOnlySubmitters()
	{
		$form = $this->getAnchoredForm();

		$form->addText('text', 'Text');
		$form->addGroup('Buttons');
		$form->addSubmit('save', 'Save');

		Assert::contains('card-footer', $form->renderForm());
	}


	public function testFooterInFormWithSubmittersOnly()
	{
		$form = $this->getAnchoredForm();

		$form->addSubmit('delete', 'Delete');

		Assert::contains('name="delete"', $form->renderForm());
	}


	public function testUntitledGroupHasNoHeader()
	{
		$form = $this->getAnchoredForm();

		$form->addText('a', 'A');
		$form->addGroup();
		$form->addText('b', 'B');

		Assert::notContains('card-header', $form->renderForm());
	}


	public function testFormTitleOnFirstCard()
	{
		$form = $this->getAnchoredForm();

		$form->setTitle('<b>Title</b>');
		$form->addText('a', 'A');
		$form->addGroup();
		$form->addText('b', 'B');

		$html = $form->renderForm();

		Assert::same(1, substr_count($html, 'card-header'));
		Assert::contains('&lt;b&gt;Title&lt;/b&gt;', $html);
	}


	public function testFormErrorsEscaped()
	{
		$form = $this->getAnchoredForm();

		$form->addText('a', 'A');
		$form->addError('<script>x</script>');

		$html = $form->renderForm();

		Assert::contains('&lt;script&gt;x&lt;/script&gt;', $html);
		Assert::notContains('<script>', $html);
	}


	public function testRemoveGroup()
	{
		$form = new Form;

		$form->addGroup('second');
		$form->addText('text');

		$form->removeGroup('second');

		Assert::null($form->getGroup('second'));
		Assert::null($form->getComponent('text', false));
	}


	public function testSubmitOnSubmitHandler()
	{
		$form = new Form;

		$submit = $form->addSubmit('save', 'Save', function() {});

		Assert::count(1, $submit->onClick);
	}


	public function testSubmitWithoutCaption()
	{
		$form = $this->getAnchoredForm();

		$submit = $form->addSubmit('save');

		Assert::noError(fn() => $submit->render());
	}


	public function testSubmitNameInContainer()
	{
		$form = $this->getAnchoredForm();

		$submit = $form->addContainer('cont')->addSubmit('save', 'Save');

		Assert::contains('name="cont[save]"', (string) $submit->render());
	}


	public function testEmailMaxLength()
	{
		$form = new Form;

		Assert::same(50, $form->addEmail('email', 'Email', 50)->getControl()->maxlength);
	}


	public function testLinkIsOmitted()
	{
		$form = $this->getAnchoredForm();

		$form->addText('text');
		$form->addLink('link', 'Link');

		Assert::same(['text'], array_keys($form->getValues('array')));
	}


	public function testLabelsEscaped()
	{
		$form = $this->getAnchoredForm();

		$radio = $form->addRadioList('radio', '<b>Radio</b>', ['a' => '<i>A</i>']);

		$html = (string) $radio->render();

		Assert::contains('&lt;b&gt;Radio&lt;/b&gt;', $html);
		Assert::contains('&lt;i&gt;A&lt;/i&gt;', $html);
	}


	public function testErrorMessageEscaped()
	{
		$form = $this->getAnchoredForm();

		$text = $form->addText('text', 'Text')
			->addRule(Form::MinLength, 'Value %value is too short', 50);

		$text->setValue('<img src=x>');
		$text->validate();
		$text->setSubmitted();

		$html = (string) $text->render();

		Assert::contains('Value &lt;img src=x&gt; is too short', $html);
	}


	public function testValidateRC()
	{
		$form = new Form;

		$input = $form->addText('rc');

		foreach(['7801011230' => true, '780101123' => true, '7813011239' => false, '7801351239' => false] as $rc => $valid)
		{
			$input->setValue((string) $rc);

			Assert::same($valid, ModulIS\Form\FormValidator::validateRC($input), "RC $rc");
		}
	}
}

$testcase = new FormTest;
$testcase->run();
