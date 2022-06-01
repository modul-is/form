<?php

declare(strict_types=1);

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;
use ModulIS\Form\Form;

class DuplicatorTest extends Tester\TestCase
{
	public function testRender()
	{
		$form = new Form;

		$duplicator = $form->addDuplicator('duplicator', function(ModulIS\Form\DuplicatorContainer $container)
		{
			$container->addText('text', 'text');

			$container->addSubmit('del', 'Smazat')
				->setValidationScope(null)
				->addRemoveOnClick();
		});

		$duplicator->addSubmit('add', 'Přidat')
			->setValidationScope(null)
			->addCreateOnClick(true);

		$duplicator->setValues([['text' => 'Text']]);

		$html = str_replace(["\t", "\n"], '', file_get_contents(__DIR__ . '/basic.latte'));

		Assert::same($html, $form->getComponent('duplicator')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = new Form;

		$duplicator = $form->addDuplicator('duplicator', function(ModulIS\Form\DuplicatorContainer $container)
		{
			$container->addText('text', 'text');

			$container->addSubmit('del', 'Smazat')
				->setValidationScope(null)
				->addRemoveOnClick();
		});

		$duplicator->addSubmit('add', 'Přidat')
			->setValidationScope(null)
			->addCreateOnClick(true);

		$duplicator->setTemplate(__DIR__ . '/custom.latte');

		$string = 'custom-template';

		Assert::same($string, $form->getComponent('duplicator')->render());
	}


	public function testRenderSkip()
	{
		$form = new Form;

		$duplicator = $form->addDuplicator('duplicator', function(ModulIS\Form\DuplicatorContainer $container)
		{
			$container->addText('text', 'text');

			$container->addSubmit('del', 'Smazat')
				->setValidationScope(null)
				->addRemoveOnClick();
		});

		$duplicator->addSubmit('add', 'Přidat')
			->setValidationScope(null)
			->addCreateOnClick(true);

		$duplicator->setAutoRenderSkip();

		$string = '';

		Assert::same($string, $form->getComponent('duplicator')->render());
	}
}

$testcase = new DuplicatorTest;
$testcase->run();
