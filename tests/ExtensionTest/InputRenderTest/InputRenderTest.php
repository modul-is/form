<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use ModulIS\Form\Form;
use Tester\Assert;

class InputRenderTest extends TestCase
{
	private string $tempDir;


	public function setUp()
	{
		$this->tempDir = __DIR__ . '/output';

		if(!is_dir($this->tempDir))
		{
			mkdir($this->tempDir, 0777, true);
		}
	}


	/**
	 * Vykresli sablonu s telem uvnitr <form>, aby byl k dispozici scope pro form tagy.
	 */
	private function renderBody(string $body, Form $form, \Nette\Application\UI\Presenter $presenter): string
	{
		$file = $this->tempDir . '/tpl_' . md5($body) . '.latte';

		file_put_contents($file, '<form n:name="form">' . $body . '</form>');

		$latte = new \Latte\Engine;
		$latte->setTempDirectory($this->tempDir . '/cache');
		$latte->addExtension(new \Nette\Bridges\FormsLatte\FormsExtension);
		$latte->addExtension(new \Nette\Bridges\ApplicationLatte\UIExtension($presenter));
		$latte->addExtension(new \ModulIS\Extension\FormExtension);

		$output = $latte->renderToString($file, ['form' => $form, 'presenter' => $presenter, 'control' => $presenter]);

		return trim(preg_replace('~^<form[^>]*>|<input type="hidden"[^>]*>|</form>$~', '', $output));
	}


	private function getFormWithPresenter(): array
	{
		$presenter = new TestPresenter;
		$form = new Form;

		$presenter->addComponent($form, 'form');

		return [$form, $presenter];
	}


	public function testMatchesManualRender()
	{
		[$form, $presenter] = $this->getFormWithPresenter();

		$form->addText('text', 'Text');

		$expected = $form->getComponent('text')->render()->__toString();

		Assert::same($expected, $this->renderBody('{inputRender text}', $form, $presenter));
	}


	public function testRendersMoreThanInputCore()
	{
		[$form, $presenter] = $this->getFormWithPresenter();

		$form->addText('text', 'Text');

		$core = $this->renderBody('{inputCore text}', $form, $presenter);
		$full = $this->renderBody('{inputRender text}', $form, $presenter);

		Assert::notSame($core, $full);
		Assert::contains('<label for="frm-form-text">Text</label>', $full);
		Assert::notContains('<label', $core);
	}


	public function testRespectsRenderType()
	{
		[$form, $presenter] = $this->getFormWithPresenter();

		$form->addText('text', 'Text')
			->setRenderFloating();

		$output = $this->renderBody('{inputRender text}', $form, $presenter);

		Assert::contains('form-floating', $output);
	}


	public function testAttributes()
	{
		[$form, $presenter] = $this->getFormWithPresenter();

		$form->addText('text', 'Text');

		$output = $this->renderBody("{inputRender text, class => 'extra'}", $form, $presenter);

		Assert::contains('class="extra"', $output);
	}


	public function testSkippedInputRendersNothing()
	{
		[$form, $presenter] = $this->getFormWithPresenter();

		$form->addText('text', 'Text')
			->setAutoRenderSkip();

		Assert::same('', $this->renderBody('{inputRender text}', $form, $presenter));
	}


	public function testContainerControl()
	{
		[$form, $presenter] = $this->getFormWithPresenter();

		$form->addDuplicator('dup', function(\ModulIS\Form\DuplicatorContainer $container)
		{
			$container->addText('text', 'Text');
		}, 1);

		$output = $this->renderBody('{inputRender dup}', $form, $presenter);

		Assert::contains('frm-form-dup-0-text', $output);
	}


	public function testPartThrows()
	{
		[$form, $presenter] = $this->getFormWithPresenter();

		$form->addText('text', 'Text');

		Assert::exception(
			fn() => $this->renderBody('{inputRender text:1}', $form, $presenter),
			\Latte\CompileException::class,
			'Tag {inputRender} cannot render a part of an input, use {inputCore} instead%a%'
		);
	}


	public function testNonRenderableThrows()
	{
		[$form, $presenter] = $this->getFormWithPresenter();

		$form->addComponent(new \Nette\Forms\Controls\TextInput('Plain'), 'plain');

		Assert::exception(
			fn() => $this->renderBody('{inputRender plain}', $form, $presenter),
			\Nette\InvalidArgumentException::class,
			'Expected instance of ModulIS\Form\Control\Renderable, Nette\Forms\Controls\TextInput given.'
		);
	}
}

(new InputRenderTest)->run();
