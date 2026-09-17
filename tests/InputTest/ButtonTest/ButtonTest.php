<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class ButtonTest extends TestCase
{
	public function testRenderButton()
	{
		$form = $this->getForm();

		$form->addButton('button', 'Button');

		$this->assertRender(__DIR__ . '/button.latte', $form->getComponent('button')->render()->__toString());
	}


	/**
	 * Regrese - bez setColor() vznikala neplatna trida "btn-".
	 */
	public function testButtonWithoutColorHasValidClass()
	{
		$form = $this->getForm();

		$form->addButton('button', 'Button');

		$html = $form->getComponent('button')->render()->__toString();

		Assert::contains('btn-default', $html);
		Assert::false((bool) preg_match('~class="[^"]*btn-(\s|")~', $html), 'dangling "btn-" class');
	}


	public function testRenderButtonColor()
	{
		$form = $this->getForm();

		$form->addButton('button', 'Button')
			->setColor('primary');

		$this->assertRender(__DIR__ . '/buttonColor.latte', $form->getComponent('button')->render()->__toString());
	}


	public function testRenderButtonIcon()
	{
		$form = $this->getForm();

		$form->addButton('button', 'Button')
			->setIcon('user');

		$this->assertRender(__DIR__ . '/buttonIcon.latte', $form->getComponent('button')->render()->__toString());
	}


	public function testRenderSubmit()
	{
		$form = $this->getForm();

		$form->addSubmit('submit', 'Submit');

		$this->assertRender(__DIR__ . '/submit.latte', $form->getComponent('submit')->render()->__toString());
	}


	public function testRenderSubmitColor()
	{
		$form = $this->getForm();

		$form->addSubmit('submit', 'Submit')
			->setColor('danger');

		$this->assertRender(__DIR__ . '/submitColor.latte', $form->getComponent('submit')->render()->__toString());
	}


	public function testRenderLink()
	{
		$form = $this->getForm();

		$form->addLink('link', 'Link')
			->setLink('#target');

		$this->assertRender(__DIR__ . '/link.latte', $form->getComponent('link')->render()->__toString());
	}


	public function testRenderLinkColorIcon()
	{
		$form = $this->getForm();

		$form->addLink('link', 'Link')
			->setLink('#target')
			->setColor('primary')
			->setIcon('user');

		$this->assertRender(__DIR__ . '/linkColorIcon.latte', $form->getComponent('link')->render()->__toString());
	}


	/**
	 * Regrese - setButtonClass() se drive aplikoval jen na Link, Button a SubmitButton ho ignorovaly.
	 */
	public function testFormButtonClassAppliesToAllButtons()
	{
		$form = $this->getForm();
		$form->setButtonClass('rounded-4');

		$form->addButton('button', 'Button');
		$form->addSubmit('submit', 'Submit');
		$form->addLink('link', 'Link')->setLink('#');

		foreach(['button', 'submit', 'link'] as $name)
		{
			Assert::contains('rounded-4', $form->getComponent($name)->render()->__toString(), $name);
		}
	}


	/**
	 * Vlastni trida na tlacitku ma prednost pred formularovou.
	 */
	public function testOwnClassBeatsFormButtonClass()
	{
		$form = $this->getForm();
		$form->setButtonClass('rounded-4');

		$form->addLink('link', 'Link')
			->setLink('#')
			->setClass('own-class');

		$html = $form->getComponent('link')->render()->__toString();

		Assert::contains('own-class', $html);
		Assert::notContains('rounded-4', $html);
	}


	public function testRenderSkip()
	{
		$form = $this->getForm();

		$form->addButton('button', 'Button')
			->setAutoRenderSkip();

		Assert::same('', $form->getComponent('button')->render());
	}
}

(new ButtonTest)->run();
