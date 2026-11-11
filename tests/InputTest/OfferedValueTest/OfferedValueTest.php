<?php

declare(strict_types=1);

namespace ModulIS\Form\Tests;

require_once __DIR__ . '/../../bootstrap.php';

use ModulIS\Form\Helper\DependentData;
use Tester\Assert;

/**
 * Odeslany formular s danymi POST daty - bez presenteru a signalu
 */
class SubmittedForm extends \ModulIS\Form\Form
{
	/**
	 * @param array<string, mixed> $post
	 */
	public function __construct(private array $post)
	{
		parent::__construct();
	}


	protected function receiveHttpData(): ?array
	{
		return $this->post;
	}
}

/**
 * Odeslana hodnota mimo nabidku (podvrzeny pozadavek, zastarala stranka) se nesmi dostat do hodnot formulare
 */
class OfferedValueTest extends TestCase
{
	private const string Message = 'Please select a valid option.';


	/**
	 * @param array<string, mixed> $post
	 */
	private function submit(array $post): SubmittedForm
	{
		$form = new SubmittedForm($post + ['save' => '']);
		$form->addSubmit('save');

		(new TestPresenter)->addComponent($form, 'form');

		return $form;
	}


	private function addDependent(SubmittedForm $form, bool $disabledWhenEmpty = false): \ModulIS\Form\Control\DependentSelect
	{
		$form->addSelect('parent', 'Parent', ['a' => 'A', 'b' => 'B', 'c' => 'C'])
			->setPrompt('–');

		$dependent = $form->addDependentSelect('dependent', 'Dependent', [$form->getComponent('parent')])
			->setDependentCallback(fn(array $values): DependentData => new DependentData(
				items: match($values['parent'])
				{
					'a' => ['a1' => 'A1', 'a2' => 'A2'],
					'b' => ['b1' => 'B1'],
					default => []
				},
				prompt: '–'
			));

		if($disabledWhenEmpty)
		{
			$dependent->setDisabledWhenEmpty();
		}

		return $dependent;
	}


	public function testWhispererOffered(): void
	{
		$form = $this->submit(['client' => '2']);
		$whisperer = $form->addWhisperer('client', 'Client', [1 => 'One', 2 => 'Two']);

		$form->validate();

		Assert::same(2, $whisperer->getValue());
		Assert::same([], $whisperer->getErrors());
	}


	public function testWhispererNotOffered(): void
	{
		$form = $this->submit(['client' => '3']);
		$whisperer = $form->addWhisperer('client', 'Client', [1 => 'One', 2 => 'Two']);

		$form->validate();

		Assert::null($whisperer->getValue());
		Assert::same([self::Message], $whisperer->getErrors());
	}


	public function testWhispererNotOfferedRequired(): void
	{
		$form = $this->submit(['client' => '3']);
		$whisperer = $form->addWhisperer('client', 'Client', [1 => 'One', 2 => 'Two'])
			->setRequired();

		$form->validate();

		Assert::null($whisperer->getValue());
		Assert::true($whisperer->hasErrors());
		Assert::contains(self::Message, $whisperer->getErrors());
	}


	public function testWhispererEmpty(): void
	{
		$form = $this->submit(['client' => '']);
		$whisperer = $form->addWhisperer('client', 'Client', [1 => 'One', 2 => 'Two']);

		$form->validate();

		Assert::same('', $whisperer->getValue());
		Assert::same([], $whisperer->getErrors());
	}


	/**
	 * Polozky nacita AJAX hledani - server je nezna a hodnotu musi overit aplikace
	 */
	public function testWhispererSearchKeepsValue(): void
	{
		$form = $this->submit(['client' => '3']);
		$whisperer = $form->addWhisperer('client', 'Client', [])
			->setOnSearchChangeCallback(fn(string $query): array => []);

		$form->validate();

		Assert::same(3, $whisperer->getValue());
		Assert::same([], $whisperer->getErrors());
	}


	/**
	 * Prekresleny formular (chyba validace) polozku z AJAX hledani nezna - vykresli ji z odeslane hodnoty a popisku
	 */
	public function testWhispererSearchRendersSubmittedOption(): void
	{
		$form = $this->submit(['client' => '3', 'client-label' => 'Three <b>']);
		$whisperer = $form->addWhisperer('client', 'Client', [])
			->setOnSearchChangeCallback(fn(string $query): array => []);

		Assert::contains('<option value="3" selected>Three &lt;b&gt;</option>', (string) $whisperer->getControl());
	}


	public function testWhispererSearchRendersSubmittedOptionInContainer(): void
	{
		$form = $this->submit(['person' => ['client' => '3', 'client-label' => 'Three']]);
		$whisperer = $form->addContainer('person')->addWhisperer('client', 'Client', [])
			->setOnSearchChangeCallback(fn(string $query): array => []);

		Assert::contains('<option value="3" selected>Three</option>', (string) $whisperer->getControl());
	}


	/**
	 * Bez popisku (formular odeslany bez combobox.js) se zobrazi aspon hodnota
	 */
	public function testWhispererSearchRendersSubmittedOptionWithoutLabel(): void
	{
		$form = $this->submit(['client' => '3']);
		$whisperer = $form->addWhisperer('client', 'Client', [])
			->setOnSearchChangeCallback(fn(string $query): array => []);

		Assert::contains('<option value="3" selected>3</option>', (string) $whisperer->getControl());
	}


	/**
	 * Polozka, kterou server zna, se nepridava podruhe a popisek z pozadavku se nepouzije
	 */
	public function testWhispererSearchKnownItemIsNotDuplicated(): void
	{
		$form = $this->submit(['client' => '1', 'client-label' => 'Forged']);
		$whisperer = $form->addWhisperer('client', 'Client', [1 => 'One'])
			->setOnSearchChangeCallback(fn(string $query): array => []);

		$html = (string) $whisperer->getControl();

		Assert::same(1, substr_count($html, 'value="1"'));
		Assert::notContains('Forged', $html);
	}


	public function testDependentOffered(): void
	{
		$form = $this->submit(['parent' => 'a', 'dependent' => 'a2']);
		$dependent = $this->addDependent($form);

		$form->validate();

		Assert::same('a2', $dependent->getValue());
		Assert::same([], $dependent->getErrors());
	}


	/**
	 * Hodnota patri k jinemu rodici - drive vyhodilo InvalidArgumentException
	 */
	public function testDependentOfOtherParent(): void
	{
		$form = $this->submit(['parent' => 'a', 'dependent' => 'b1']);
		$dependent = $this->addDependent($form);

		Assert::noError(fn() => $form->validate());
		Assert::null($dependent->getValue());
		Assert::same([self::Message], $dependent->getErrors());
	}


	/**
	 * Rodic bez polozek - drive se vratila odeslana hodnota bez kontroly
	 */
	public function testDependentWithoutItems(): void
	{
		$form = $this->submit(['parent' => 'c', 'dependent' => 'a1']);
		$dependent = $this->addDependent($form);

		$form->validate();

		Assert::null($dependent->getValue());
		Assert::same([self::Message], $dependent->getErrors());
	}


	public function testDependentWithoutItemsDisabled(): void
	{
		$form = $this->submit(['parent' => 'c', 'dependent' => 'a1']);
		$dependent = $this->addDependent($form, disabledWhenEmpty: true);

		$form->validate();

		Assert::null($dependent->getValue());
	}


	public function testDependentDefaultValue(): void
	{
		$form = $this->getForm();

		$form->addSelect('parent', 'Parent', ['a' => 'A', 'b' => 'B'])
			->setDefaultValue('a');

		$dependent = $form->addDependentSelect('dependent', 'Dependent', [$form->getComponent('parent')])
			->setDependentCallback(fn(array $values): DependentData => new DependentData(['a1' => 'A1', 'a2' => 'A2']));

		$dependent->setDefaultValue('a2');

		Assert::same('a2', $dependent->getValue());
	}


	public function testDependentMultiNotOffered(): void
	{
		$form = $this->submit(['parent' => 'a', 'dependent' => ['a1', 'b1']]);

		$form->addSelect('parent', 'Parent', ['a' => 'A', 'b' => 'B']);

		$dependent = $form->addDependentMultiSelect('dependent', 'Dependent', [$form->getComponent('parent')])
			->setDependentCallback(fn(array $values): DependentData => new DependentData(
				$values['parent'] === 'a' ? ['a1' => 'A1', 'a2' => 'A2'] : ['b1' => 'B1']
			));

		Assert::noError(fn() => $form->validate());
		Assert::same([], $dependent->getValue());
		Assert::same([self::Message], $dependent->getErrors());
	}


	public function testDependentMultiOffered(): void
	{
		$form = $this->submit(['parent' => 'a', 'dependent' => ['a1', 'a2']]);

		$form->addSelect('parent', 'Parent', ['a' => 'A', 'b' => 'B']);

		$dependent = $form->addDependentMultiSelect('dependent', 'Dependent', [$form->getComponent('parent')])
			->setDependentCallback(fn(array $values): DependentData => new DependentData(
				$values['parent'] === 'a' ? ['a1' => 'A1', 'a2' => 'A2'] : ['b1' => 'B1']
			));

		$form->validate();

		Assert::same(['a1', 'a2'], $dependent->getValue());
		Assert::same([], $dependent->getErrors());
	}
}

$testcase = new OfferedValueTest;
$testcase->run();
