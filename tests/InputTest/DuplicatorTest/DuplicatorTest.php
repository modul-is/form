<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class DuplicatorTest extends TestCase
{
	public function testRender()
	{
		$form = $this->getForm();

		$duplicator = $form->addDuplicator('duplicator', function(\ModulIS\Form\DuplicatorContainer $container)
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

		$this->assertRender(__DIR__ . '/basic.latte', $form->getComponent('duplicator')->render()->__toString());
	}


	public function testRenderCustomTemplate()
	{
		$form = $this->getForm();

		$duplicator = $form->addDuplicator('duplicator', function(\ModulIS\Form\DuplicatorContainer $container)
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
		$form = $this->getForm();

		$duplicator = $form->addDuplicator('duplicator', function(\ModulIS\Form\DuplicatorContainer $container)
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


	public function testSetValuesWithoutForm()
	{
		$duplicator = new \ModulIS\Form\Control\Duplicator(function(\ModulIS\Form\DuplicatorContainer $container)
		{
			$container->addText('text', 'text');
		});

		$duplicator->setValues([['text' => 'First'], ['text' => 'Second']]);

		Assert::count(2, $duplicator->getContainers());
		Assert::same('First', $duplicator->getComponent('0')->getComponent('text')->getValue());
		Assert::same('Second', $duplicator->getComponent('1')->getComponent('text')->getValue());
	}
}

$testcase = new DuplicatorTest;
$testcase->run();
