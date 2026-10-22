<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../bootstrap.php';

use ModulIS\Form\Icon\ClassIconRenderer;
use ModulIS\Form\Renderer\Bootstrap6Renderer;
use Tester\Assert;

class RendererTest extends TestCase
{
	public function testWidth()
	{
		$form = $this->getForm();

		$form->addText('a', 'A')->setWidth(6);
		$form->addText('b', 'B')->setWidth(['' => 12, 'md' => 6, 'xl' => 4]);
		$form->addText('c', 'C')->setWrapClass('my-col')->setWidth(3);

		Assert::contains('<div class="col-12 md:col-6">', (string) $form->getComponent('a')->render());
		Assert::contains('<div class="col-12 md:col-6 xl:col-4">', (string) $form->getComponent('b')->render());
		Assert::contains('<div class="my-col md:col-3">', (string) $form->getComponent('c')->render());
	}


	public function testDescription()
	{
		$form = $this->getForm();

		$form->addText('text', 'Text')
			->setDescription('<b>escaped</b>');

		$form->addCheckbox('check', 'Check')
			->setDescription('Help');

		Assert::contains('<div class="form-text">&lt;b&gt;escaped&lt;/b&gt;</div>', (string) $form->getComponent('text')->render());
		Assert::contains('<label for="frm-form-check">Check</label><div class="form-text">Help</div>', (string) $form->getComponent('check')->render());
	}


	public function testFloatingTextareaHasPlaceholder()
	{
		$form = $this->getForm();

		$form->addTextArea('area', 'Area')
			->setRenderFloating();

		Assert::contains('placeholder="Area"', (string) $form->getComponent('area')->render());
	}


	public function testSwitch()
	{
		$form = $this->getForm();

		$form->addCheckbox('check', 'Check')
			->setSwitch();

		Assert::contains('<div class="switch"><input type="checkbox" name="check" id="frm-form-check" role="switch" switch></div>', (string) $form->getComponent('check')->render());
	}


	public function testToggleButtons()
	{
		$form = $this->getForm();

		$form->addRadioList('radio', 'Radio', ['a' => 'A', 'b' => 'B'])
			->setToggleButton()
			->setButtonColor(['a' => 'outline-success', 'b' => 'danger']);

		$html = (string) $form->getComponent('radio')->render();

		Assert::contains('<div class="btn-group flex-wrap" role="group">', $html);
		Assert::contains('<label class="btn-check btn-outline theme-success"><input type="radio" name="radio" id="frm-form-radio-a" value="a">A</label>', $html);
		Assert::contains('<label class="btn-check btn-outline theme-danger">', $html);
	}


	public function testColorAliases()
	{
		$form = $this->getForm();

		$form->addLink('link', 'Link')->setColor('light')->setVariant('outline');
		$form->addButton('button', 'Button')->setColor('dark');

		Assert::contains('class="btn-outline theme-secondary"', (string) $form->getComponent('link')->render());
		Assert::contains('class="btn-solid theme-inverse"', (string) $form->getComponent('button')->render());
	}


	public function testFontAwesomeIcons()
	{
		$form = $this->getForm();
		$form->setRenderer(new Bootstrap6Renderer(ClassIconRenderer::fontAwesome('fa-light')));

		$form->addText('text', 'Text')
			->setTooltip('Help');

		Assert::contains('<i class="fa-light fa-circle-question fa-fw" aria-hidden="true"></i>', (string) $form->getComponent('text')->render());
	}


	public function testWholeForm()
	{
		$form = $this->getForm();
		$form->setAjax();

		$form->addGroup('Group')
			->setColor('info');

		$form->addText('text', 'Text');

		$html = (string) $form;

		Assert::match('<form action="#" method="post" id="frm-form" class="ajax" novalidate data-mis-validate="server">%A%', $html);
		Assert::contains('<div class="card card-subtle"><div class="card-header flex-row align-items-center gap-2 theme-info">Group</div>', $html);
		Assert::contains('<div class="card-footer d-flex flex-wrap gap-2"><button name="save"', $html);
		Assert::contains('</form>', $html);
	}


	/**
	 * Server only validation is the default - nette-forms validates on the client only when it is switched on
	 * (it skips a form with novalidate and sets the attribute itself)
	 */
	public function testClientValidation()
	{
		$server = $this->getForm();
		$server->addText('text', 'Text')->setRequired();

		$client = $this->getForm();
		$client->setClientValidation();
		$client->addText('text', 'Text')->setRequired();

		Assert::false($server->isClientValidation());
		Assert::match('<form action="#" method="post" id="frm-form" novalidate data-mis-validate="server">%A%', (string) $server);
		Assert::match('<form action="#" method="post" id="frm-form">%A%', (string) $client);
		Assert::contains('data-nette-rules', (string) $server, 'rules stay for toggles');

		\ModulIS\Form\Form::setDefaultClientValidation(true);

		Assert::true($this->getForm()->isClientValidation());
		Assert::false($this->getForm()->setClientValidation(false)->isClientValidation(), 'form setting wins over the default');

		\ModulIS\Form\Form::setDefaultClientValidation(false);
	}


	public function testInvalidField()
	{
		$form = $this->getForm();

		$text = $form->addText('text', 'Text');
		$text->setSubmitted();
		$text->addError('Error <b>message</b>');

		$html = (string) $text->render();

		Assert::contains('class="form-control is-invalid"', $html);
		Assert::contains('<div class="invalid-feedback">Error &lt;b&gt;message&lt;/b&gt;</div>', $html);
	}
}

(new RendererTest)->run();
