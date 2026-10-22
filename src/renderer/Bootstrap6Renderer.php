<?php

declare(strict_types = 1);

namespace ModulIS\Form\Renderer;

use ModulIS\Form\Container;
use ModulIS\Form\Control;
use ModulIS\Form\ControlGroup;
use ModulIS\Form\DuplicatorContainer;
use ModulIS\Form\Enum\RenderListType;
use ModulIS\Form\Enum\RenderType;
use ModulIS\Form\Form;
use ModulIS\Form\Helper\QuickCopyable;
use ModulIS\Form\Icon\ClassIconRenderer;
use ModulIS\Form\Icon\IconRenderer;
use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Controls\HiddenField;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\HtmlStringable;
use Nette\Utils\Html;
use Nette\Utils\Json;

/**
 * The only place that knows Bootstrap 6 markup - controls hold just their settings and ask the renderer
 * for HTML. Extend it to change the markup of the whole application.
 *
 * Every field is wrapped in a column (setWrapClass(), setWidth()) and the element marked with
 * data-mis-field is the one validation feedback belongs to - client side validation (assets/js)
 * looks it up the same way.
 */
class Bootstrap6Renderer implements \Nette\Forms\FormRenderer
{
	private const array ColorAliases = [
		'default' => 'secondary',
		'gray' => 'secondary',
		'grey' => 'secondary',
		'light' => 'secondary',
		'dark' => 'inverse'
	];

	private static ?IconRenderer $defaultIconRenderer = null;

	private IconRenderer $iconRenderer;


	public function __construct
	(
		?IconRenderer $iconRenderer = null
	)
	{
		$this->iconRenderer = $iconRenderer ?? self::getDefaultIconRenderer();
	}


	/**
	 * Icon set for all forms of the application (default Bootstrap Icons)
	 */
	public static function setDefaultIconRenderer(IconRenderer $iconRenderer): void
	{
		self::$defaultIconRenderer = $iconRenderer;
	}


	public static function getDefaultIconRenderer(): IconRenderer
	{
		return self::$defaultIconRenderer ??= ClassIconRenderer::bootstrapIcons();
	}


	/**
	 * Renderer of the form the component belongs to
	 */
	public static function of(\Nette\ComponentModel\Component $component): self
	{
		$form = $component instanceof \Nette\Forms\Form
			? $component
			: $component->lookup(\Nette\Forms\Form::class);

		$renderer = $form->getRenderer();

		if(!$renderer instanceof self)
		{
			throw new \Nette\InvalidStateException('Form "' . $form->getName() . '" has to be rendered by ' . self::class . ', ' . $renderer::class . ' set.');
		}

		return $renderer;
	}


	public function icon(string $icon): Html
	{
		return $this->iconRenderer->render($icon);
	}


	/**
	 * Theme class for a color - accepts Bootstrap 6 theme names and a few Bootstrap 5 ones (light, dark, gray)
	 */
	public function themeClass(string $color): string
	{
		return 'theme-' . (self::ColorAliases[$color] ?? $color);
	}


	/**
	 * Whole form including <form> tag, used by $form->render() and {control form}
	 */
	public function render(\Nette\Forms\Form $form): string
	{
		\assert($form instanceof Form);

		$netteRenderer = new DefaultFormRenderer;

		return $netteRenderer->render($form, 'begin')
			. $this->renderBody($form)
			. $netteRenderer->render($form, 'end');
	}


	/**
	 * Form content - groups as cards, buttons in the footer of the last one
	 */
	public function renderBody(Form $form): string
	{
		$submitters = '';

		foreach($form->getSubmitterArray() as $submitter)
		{
			$submitters .= $submitter->render();
		}

		/** @var list<array{group: ControlGroup, content: string}> $cards */
		$cards = [];

		foreach($form->getGroups() as $group)
		{
			$inputs = '';

			foreach($group->getInputArray() as $input)
			{
				/**
				 * Duplicator container render is handled within duplicator
				 */
				if($input instanceof DuplicatorContainer)
				{
					continue;
				}

				$inputs .= $input instanceof HiddenField ? $input->getControl() : $input->render();
				$inputs .= $form->getDivider((string) $input->getName()) ?? '';
			}

			if($inputs === '')
			{
				continue;
			}

			/**
			 * Form title (setTitle / setIcon) is shown on the first card unless its group has a title of its own
			 */
			$isFirstCard = $cards === [];

			$header = $this->createGroupHeader($group, $isFirstCard ? $form->getTitle() : null, $isFirstCard ? $form->icon : null, $group->getOption('color') ?? $form->color);

			$body = Html::el('div')
				->class('card-body')
				->addHtml($this->createRow($inputs));

			$cards[] = ['group' => $group, 'content' => $header . $body];
		}

		if($submitters !== '')
		{
			$footer = $this->createFooter($submitters);

			/**
			 * Footer belongs to the last rendered card - a form (or last group) with submitters only gets a card of its own
			 */
			if($cards)
			{
				$lastCard = array_pop($cards);
				$lastCard['content'] .= $footer;
				$cards[] = $lastCard;
			}
			else
			{
				$groupArray = $form->getGroups();

				$cards[] = ['group' => end($groupArray) ?: new ControlGroup, 'content' => (string) $footer];
			}
		}

		$columns = '';

		foreach($cards as ['group' => $group, 'content' => $content])
		{
			$card = $this->createCard($group->getOption('color') ?? $form->color)
				->addHtml($content);

			if($group->getOption('id'))
			{
				$card->id($group->getOption('id'));
			}

			$columns .= Html::el('div')
				->class($group->getClass() ?? 'col-12')
				->addHtml($card);
		}

		return $this->createFormErrors($form) . $this->createRow($columns);
	}


	public function renderContainer(Container $container): Html|string
	{
		$inputs = '';

		foreach($container->getInputArray() as $control)
		{
			\assert($control instanceof Control\Renderable);

			$inputs .= $control->render();
			$inputs .= $container->getDivider((string) $control->getName()) ?? '';
		}

		$submitters = '';

		foreach($container->getSubmitterArray() as $submitter)
		{
			$submitters .= $submitter->render();
		}

		if($inputs === '' && $submitters === '')
		{
			return '';
		}

		$wrap = Html::el('div')
			->class($container->getWrapClass() ?? 'col-12');

		if($container->getId())
		{
			$wrap->id($container->getId());
		}

		if(!$container->isCard())
		{
			return $wrap->addHtml($this->createRow($inputs . $submitters));
		}

		$card = $this->createCard($container->getColor());

		if($container->getTitle() !== null)
		{
			$card->addHtml(
				Html::el('div')
					->class($this->joinClass('card-header', $container->getColor() ? $this->themeClass($container->getColor()) : null))
					->addText($container->getTitle())
			);
		}

		if($inputs !== '')
		{
			$card->addHtml(
				Html::el('div')
					->class('card-body')
					->addHtml($this->createRow($inputs))
			);
		}

		if($submitters !== '')
		{
			$card->addHtml($this->createFooter($submitters));
		}

		return $wrap->addHtml($card);
	}


	/**
	 * Card with one bordered item per duplicated container - remove button in the head of the item, buttons for adding
	 * new items in the footer
	 */
	public function renderDuplicator(Control\Duplicator $duplicator): Html
	{
		$card = Html::el('div')
			->id('container' . ucfirst((string) $duplicator->getName()))
			->class($duplicator->getDuplicatorContainerClass() ?? ($duplicator->getColor() ? 'card card-subtle' : 'card'));

		if($duplicator->getTitle() !== null)
		{
			$card->addHtml(
				Html::el('div')
					->class($this->joinClass('card-header', $duplicator->getColor() ? $this->themeClass($duplicator->getColor()) : null))
					->addText($duplicator->getTitle())
			);
		}

		$body = Html::el('div')
			->class($duplicator->getDuplicatorBodyClass() ?? 'card-body vstack gap-3');

		foreach($duplicator->getContainers() as $container)
		{
			$body->addHtml($this->createDuplicatorItem($duplicator, $container));
		}

		if($body->count())
		{
			$card->addHtml($body);
		}

		$createButtons = '';

		foreach($duplicator->getButtons() as $button)
		{
			if($button instanceof Control\DuplicatorCreateSubmit)
			{
				$createButtons .= $button->render();
			}
		}

		if($createButtons !== '')
		{
			$card->addHtml(
				Html::el('div')
					->class($duplicator->getDuplicatorFooterClass() ?? 'card-footer d-flex flex-wrap gap-2')
					->addHtml($createButtons)
			);
		}

		$wrap = Html::el('div')
			->class('col-12')
			->addHtml($card);

		if($duplicator->getOption('id'))
		{
			$wrap->id($duplicator->getOption('id'));
		}

		return $wrap;
	}


	protected function createDuplicatorItem(Control\Duplicator $duplicator, Container $container): Html
	{
		$head = Html::el('div')
			->class('d-flex align-items-center gap-2 mb-3');

		if($container->getTitle() !== null)
		{
			$head->addHtml(Html::el('span')->class('fw-semibold')->addText($container->getTitle()));
		}

		$inputs = '';
		$buttons = '';

		foreach($container->getComponents() as $component)
		{
			\assert($component instanceof Control\Renderable);

			/**
			 * Remove button is rendered in the head of the item regardless of its name
			 */
			if($component instanceof Control\DuplicatorRemoveSubmit)
			{
				$head->addHtml($component->render());
			}
			elseif($component instanceof Control\Button || $component instanceof Control\Link)
			{
				$buttons .= $component->render();
			}
			else
			{
				$inputs .= $component->render();
			}
		}

		if($buttons !== '')
		{
			$inputs .= Html::el('div')
				->class($duplicator->getDuplicatorButtonWrapClass() ?? 'col-12 d-flex flex-wrap gap-2')
				->addHtml($buttons);
		}

		$item = Html::el('div')
			->class($duplicator->getDuplicatorItemClass() ?? 'border rounded-3 p-3')
			->setAttribute('data-mis-duplicator-item', true);

		if($head->count())
		{
			$item->addHtml($head);
		}

		return $item->addHtml($this->createRow($inputs));
	}


	/**
	 * Complete control - wrapper, label, control, validation
	 */
	public function renderControl(BaseControl $control): Html|string
	{
		if($control->getOption('hide') || (method_exists($control, 'isAutoRenderSkip') && $control->isAutoRenderSkip()))
		{
			return '';
		}

		if(method_exists($control, 'renderTemplate') && ($template = $control->renderTemplate()) !== null)
		{
			return $template;
		}

		$html = match(true)
		{
			$control instanceof HiddenField => $control->getControl(),
			$control instanceof Control\Checkbox => $this->renderCheckbox($control),
			$control instanceof Control\RadioList, $control instanceof Control\CheckboxList => $this->renderList($control),
			$control instanceof Control\SubmitButton, $control instanceof Control\Button, $control instanceof Control\Link => $this->renderButton($control),
			$control instanceof Control\ChipsInput => $this->renderChips($control),
			$control instanceof Control\InputField => $this->renderField($control),
			default => throw new \Nette\NotSupportedException('Control ' . $control::class . ' cannot be rendered by ' . self::class)
		};

		if($control->getOption('id'))
		{
			$html->id($control->getOption('id'));
		}

		return $html;
	}


	/**
	 * {inputCore} - control without its wrapper and label (with a key a single item of a list)
	 */
	public function renderCore(BaseControl $control, string|int|null $key = null): Html|string
	{
		return match(true)
		{
			$control instanceof Control\Checkbox => $this->createCheckboxField($control),
			$control instanceof Control\RadioList, $control instanceof Control\CheckboxList => $key === null
				? $this->createListItems($control) . $this->createFeedback($control)
				: $this->createListItem($control, $key),
			$control instanceof Control\SubmitButton, $control instanceof Control\Button, $control instanceof Control\Link => $this->renderButton($control),
			$control instanceof Control\ChipsInput => $this->createChipsBlock($control) . $this->createFeedback($control),
			$control instanceof Control\InputField => $this->createInputBlock($control, $this->createInput($control)) . $this->createFeedback($control),
			default => $control->getControl()
		};
	}


	/**
	 * {labelCore} - caption with required mark and tooltip (with a key the label of a single item of a list)
	 */
	public function renderLabel(BaseControl $control, string|int|null $key = null): Html|string|null
	{
		if($control instanceof Control\RadioList || $control instanceof Control\CheckboxList)
		{
			return $key === null
				? $this->createListLabel($control, 'form-label')
				: $control->getLabelPart($key);
		}

		if($control instanceof Control\Checkbox)
		{
			return null;
		}

		if($control instanceof Control\Field)
		{
			return $this->createLabel($control, 'form-label');
		}

		return null;
	}


	protected function renderField(BaseControl&Control\InputField $control): Html
	{
		$renderType = $control->getRenderType() ?? $this->getFormRenderType($control);

		if($renderType === RenderType::Floating && !$this->supportsFloating($control))
		{
			$renderType = RenderType::Default;
		}

		$input = $this->createInput($control);

		if($renderType === RenderType::Floating)
		{
			$field = $this->createFieldElement('form-field')
				->addHtml($this->createInputBlock($control, $input, $this->createLabel($control)))
				->addHtml($this->createFeedback($control))
				->addHtml($this->createDescription($control));

			return $control->createWrap()
				->addHtml($field);
		}

		/**
		 * In Default the input wrap class goes to the control itself, in Inline it is the class of the control column
		 */
		if($renderType === RenderType::Default && $control->getInputWrapClass())
		{
			$input->appendAttribute('class', $control->getInputWrapClass());
		}

		return $this->layoutField($control, $this->createInputBlock($control, $input), $renderType);
	}


	/**
	 * Label, control block, feedback and description - label above (Default) or in its own column (Inline)
	 */
	protected function layoutField(BaseControl&Control\Field $control, Html $block, RenderType $renderType): Html
	{
		if($renderType === RenderType::Inline)
		{
			$labelClass = $this->getColumnLabelClass($control);

			$field = $this->createFieldElement('form-field ' . ($control->getInputWrapClass() ?? 'sm:col-8'))
				->addHtml($block)
				->addHtml($this->createFeedback($control))
				->addHtml($this->createDescription($control));

			$row = Html::el('div')
				->class($control->getRowClass() ?? 'row')
				->addHtml($this->createLabel($control, $labelClass) ?? Html::el('div')->class($labelClass))
				->addHtml($field);

			return $control->createWrap()
				->addHtml($row);
		}

		$field = $this->createFieldElement('form-field')
			->addHtml($this->createLabel($control, $this->joinClass('form-label', $control->getLabelWrapClass())) ?? '')
			->addHtml($block)
			->addHtml($this->createFeedback($control))
			->addHtml($this->createDescription($control));

		return $control->createWrap()
			->addHtml($field);
	}


	protected function renderCheckbox(Control\Checkbox $control): Html
	{
		$field = $this->createCheckboxField($control);

		if($this->getFormRenderType($control) !== RenderType::Inline)
		{
			return $control->createWrap()
				->addHtml($field);
		}

		/**
		 * Horizontal form - checkbox is aligned with the controls column, the label column stays empty
		 */
		$column = Html::el('div')
			->class('sm:col-8 sm:offset-4')
			->addHtml($field);

		return $control->createWrap()
			->addHtml(Html::el('div')->class($control->getRowClass() ?? 'row')->addHtml($column));
	}


	protected function createCheckboxField(Control\Checkbox $control): Html
	{
		$input = $control->getControlPart();

		$this->addSignals($control, $input);

		$size = $this->getSize($control);
		$theme = $control->color ? $this->themeClass($control->color) : null;

		$input->appendAttribute('class', $this->joinClass(
			$control->isSwitch() ? null : 'check',
			$control->isSwitch() || !$size ? null : 'check-' . $size,
			$control->isSwitch() ? null : $theme,
			$control->getValidationClass(),
			$control->getInputWrapClass()
		));

		if($control->isIndeterminate())
		{
			$input->setAttribute('data-mis-indeterminate', true);
		}

		if($control->isSwitch())
		{
			$input->setAttribute('role', 'switch')
				->setAttribute('switch', true);

			$input = Html::el('div')
				->class($this->joinClass('switch', $size ? 'switch-' . $size : null, $theme))
				->addHtml($input);
		}

		$label = Html::el('label')
			->for($control->getHtmlId())
			->class($control->getLabelWrapClass())
			->addText($control->translate($control->getCaption()));

		$this->addLabelMarks($control, $label);

		$content = Html::el('div')
			->class('form-field-content')
			->setAttribute('data-mis-feedback', true)
			->addHtml($label)
			->addHtml($this->createFeedback($control))
			->addHtml($this->createDescription($control));

		return $this->createFieldElement($this->joinClass('form-field', $control->getCheckboxWrapClass()))
			->addHtml($input)
			->addHtml($content);
	}


	protected function renderList(Control\RadioList|Control\CheckboxList $control): Html
	{
		$renderType = $control->getRenderType() ?? match($this->getFormRenderType($control))
		{
			RenderType::Inline => RenderListType::Inline,
			RenderType::Default, RenderType::Floating => RenderListType::Default
		};

		if($renderType === RenderListType::Inline)
		{
			$label = $this->createListLabel($control, $this->getColumnLabelClass($control));

			$group = $this->createListGroup($control, $control->getInputWrapClass() ?? 'sm:col-8');

			$row = Html::el('div')
				->class($control->getRowClass() ?? 'row')
				->addHtml($label)
				->addHtml($group);

			return $control->createWrap()
				->addHtml($row);
		}

		$label = $this->createListLabel($control, $this->joinClass('form-label', $renderType === RenderListType::Compact ? $control->getLabelWrapClass() : null));

		$group = $this->createListGroup($control, in_array($renderType, [RenderListType::Compact, RenderListType::Big], true) ? $control->getInputWrapClass() : null, $label);

		return $control->createWrap()
			->addHtml($group);
	}


	/**
	 * form-group with the items, feedback and description - label goes inside unless it is rendered in its own column
	 */
	protected function createListGroup(Control\RadioList|Control\CheckboxList $control, ?string $class, ?Html $label = null): Html
	{
		return $this->createFieldElement($this->joinClass('form-group', $class))
			->setAttribute('role', 'group')
			->setAttribute('aria-labelledby', $control->getCaption() !== null ? $this->getListLabelId($control) : null)
			->addHtml($label ?? '')
			->addHtml($this->createListItems($control))
			->addHtml($this->createFeedback($control))
			->addHtml($this->createDescription($control));
	}


	protected function createListLabel(Control\RadioList|Control\CheckboxList $control, string $class): Html
	{
		$label = Html::el('div')
			->id($this->getListLabelId($control))
			->class($class)
			->addText($control->translate($control->getCaption()));

		$this->addLabelMarks($control, $label);

		return $label;
	}


	protected function createListItems(Control\RadioList|Control\CheckboxList $control): Html
	{
		$renderType = $control->getRenderType();

		if($control->isToggleButton())
		{
			$size = $this->getSize($control);

			$items = Html::el('div')
				->class($this->joinClass('btn-group flex-wrap', $size ? 'btn-group-' . $size : null))
				->setAttribute('role', 'group');
		}
		elseif($control->getItemsPerRow() > 1)
		{
			$items = Html::el('div')
				->class('row g-2 row-cols-' . $control->getItemsPerRow());
		}
		elseif($renderType === RenderListType::Compact)
		{
			$items = Html::el('div')
				->class('hstack flex-wrap gap-3');
		}
		else
		{
			$items = Html::el('div')
				->class($renderType === RenderListType::Big ? 'vstack gap-2' : 'vstack gap-1');
		}

		$items->addAttributes($control->getWrapAttributes());

		foreach(array_keys($control->getItems()) as $key)
		{
			$items->addHtml($this->createListItem($control, $key));
		}

		return $items;
	}


	protected function createListItem(Control\RadioList|Control\CheckboxList $control, string|int $key): Html
	{
		$input = $control->getControlPart($key);

		$this->addSignals($control, $input);

		$description = $control->getItemDescription($key);

		$label = $control->getLabelPart($key);

		if($control->isToggleButton())
		{
			$label->for(null)
				->class('btn-check btn-outline ' . $this->themeClass($control->getButtonColor($key)))
				->setAttribute('title', $description)
				->insert(0, $input->appendAttribute('class', $control->getValidationClass()));

			return $label;
		}

		$type = $control instanceof Control\RadioList ? 'radio' : 'check';
		$size = $this->getSize($control);

		$input->appendAttribute('class', $this->joinClass(
			$type,
			$size ? $type . '-' . $size : null,
			$control->color ? $this->themeClass($control->color) : null,
			$control->getValidationClass()
		));

		if(($icon = $control->getItemIcon($key)) !== null)
		{
			$label->insert(0, Html::el('span')->class('me-2')->addHtml($this->icon($icon)));
		}

		$item = Html::el('div')
			->class($this->joinClass(
				'form-field',
				$control->getRenderType() === RenderListType::Big ? 'form-field-card' : null,
				$control->getItemsPerRow() > 1 ? 'col' : null,
				$control->getItemClass()
			))
			->addHtml($input);

		if($description === null)
		{
			return $item->addHtml($label);
		}

		$content = Html::el('div')
			->class('form-field-content')
			->addHtml($label)
			->addHtml(Html::el('small')->class('form-text')->addText($description));

		return $item->addHtml($content);
	}


	public function renderButton(Control\SubmitButton|Control\Button|Control\Link $control): Html
	{
		$prototype = $control->getControl();

		$button = $control instanceof Control\Link
			? Html::el('a')->setAttribute('href', $control->getLink())
			: Html::el('button')->name($control->getHtmlName());

		/**
		 * Class set on the button (setClass) wins over the default class of the form (setButtonClass)
		 */
		$userClass = trim((string) $prototype->getAttribute('class'));
		$form = $control->getForm(false);

		$size = $this->getSize($control);

		$button->class($this->joinClass(
			'btn-' . ($control->getVariant() ?? 'solid'),
			$this->themeClass($control->color ?? 'secondary'),
			$size ? 'btn-' . $size : null,
			$userClass !== '' ? $userClass : ($form instanceof Form ? $form->getButtonClass() : null)
		));

		foreach($prototype->attrs as $name => $value)
		{
			if(in_array($name, ['name', 'type', 'value', 'required', 'data-nette-rules', 'class'], true))
			{
				continue;
			}

			$button->setAttribute($name, $value);
		}

		if($control instanceof Control\SubmitButton)
		{
			/**
			 * formnovalidate comes from Nette only with setValidationScope() - other buttons validate the form on the client
			 */
			$button->type('submit');
		}
		elseif($control instanceof Control\Button)
		{
			$button->type('button');

			$control->addSignalsToInput($button);
		}

		$icon = $control->getIcon() !== null ? $this->icon($control->getIcon()) : null;
		$caption = $control->translate($control->getCaption());

		if($icon && $control->getIconPosition() === 'end')
		{
			$button->addText($caption)->addHtml($icon);
		}
		else
		{
			$button->addHtml($icon ?? '')->addText($caption);
		}

		return $button;
	}


	/**
	 * Control element with classes, validation state and signal attributes
	 */
	protected function createInput(BaseControl&Control\InputField $control): Html
	{
		$input = $control->getControl();
		\assert($input instanceof Html);

		$class = match(true)
		{
			$control instanceof Control\SliderInput => $control->isRange() ? null : 'form-range-input',
			$control instanceof Control\OtpInput => 'otp-input',
			$control->isPlaintext() => 'form-control-plaintext',
			$this->isAdorned($control) => 'form-ghost',
			$control instanceof Control\ColorPicker => 'form-control form-control-color',
			default => 'form-control'
		};

		$input->appendAttribute('class', $this->joinClass($class, $control->getValidationClass()));

		if($control->isPlaintext())
		{
			$input->setAttribute('readonly', true);
		}

		if($control instanceof Control\HasSuggestions && ($control->getSuggestions() || $control->hasRemoteSuggestions()))
		{
			$input->setAttribute('list', $control->getHtmlId() . '-suggestions');

			if($control->hasSuggestionsCaret())
			{
				$input->appendAttribute('class', 'form-control-caret');
			}
		}

		/**
		 * Select with a search or images is enhanced to a combobox by assets/js/combobox.js
		 */
		if(($control instanceof Control\SelectBox || $control instanceof Control\MultiSelectBox) && $control->isCombobox())
		{
			$input->setAttribute('data-mis-combobox', Json::encode($control->getComboboxOptions()));
		}

		$this->addSignals($control, $input);

		return $input;
	}


	/**
	 * Icon or text inside the control (form-adorn)
	 */
	protected function isAdorned(Control\InputField $control): bool
	{
		return $this->canAdorn($control)
			&& ($control->getIcon() !== null || $control->getPrefix() !== null || $control->getSuffix() !== null);
	}


	/**
	 * Combobox is a form control of its own - its adornments go to the input group
	 */
	protected function canAdorn(Control\InputField $control): bool
	{
		return !(($control instanceof Control\SelectBox || $control instanceof Control\MultiSelectBox) && $control->isCombobox());
	}


	/**
	 * Control with its adornments - icon inside (form-adorn), input group around (prepend, append, quick copy),
	 * floating label
	 */
	protected function createInputBlock(BaseControl&Control\InputField $control, Html $input, ?Html $floatingLabel = null): Html
	{
		if($control instanceof Control\SliderInput)
		{
			return $this->createRangeBlock($control, $input);
		}

		if($control instanceof Control\OtpInput)
		{
			return $this->createOtpBlock($control, $input);
		}

		/**
		 * Datepicker runs on a text input in front of the hidden native one
		 */
		if($control instanceof Control\DateTimeInput && $control->isPicker())
		{
			$native = $input;
			$input = $this->createDatepickerInput($control, $native);

			return Html::el()
				->addHtml($this->createTextBlock($control, $input, $floatingLabel))
				->addHtml($native);
		}

		return $this->createTextBlock($control, $input, $floatingLabel);
	}


	/**
	 * Text-like control with adornments inside (icon, prefix, suffix), input group around and floating label
	 */
	protected function createTextBlock(BaseControl&Control\InputField $control, Html $input, ?Html $floatingLabel = null): Html
	{
		$floating = $floatingLabel !== null;
		$icon = $control->getIcon();

		if($floating)
		{
			/**
			 * Floating label needs a placeholder for :placeholder-shown, select and date/time types do without it
			 */
			if($input->getName() !== 'select' && !in_array($input->getAttribute('type'), ['date', 'time', 'datetime-local', 'month', 'week'], true) && $input->getAttribute('placeholder') === null)
			{
				$input->placeholder($control->translate($control->getCaption()));
			}

			/**
			 * Adornments inside a floating field are not supported by Bootstrap - they go to the input group instead
			 */
			if($this->isAdorned($control))
			{
				/**
				 * appendAttribute() keeps the classes as an array (class => true)
				 */
				$class = $input->getAttribute('class');
				$classes = is_array($class) ? array_keys(array_filter($class)) : explode(' ', (string) $class);

				$input->class(str_replace('form-ghost', 'form-control', implode(' ', $classes)));
			}

			$core = Html::el('div')
				->class($this->joinClass('form-floating', $control->getValidationClass()))
				->addHtml($floatingLabel)
				->addHtml($input);
		}
		elseif($this->isAdorned($control))
		{
			$core = Html::el('label')
				->class('form-control form-adorn');

			if($icon !== null)
			{
				$core->addHtml(Html::el('span')->class('form-adorn-icon')->setAttribute('aria-hidden', 'true')->addHtml($this->icon($icon)));
			}

			if($control->getPrefix() !== null)
			{
				$core->addHtml(Html::el('span')->class('form-adorn-text')->setAttribute('aria-hidden', 'true')->setText($control->translate($control->getPrefix())));
			}

			$core->addHtml($input);

			if($control->getSuffix() !== null)
			{
				$core->addHtml(Html::el('span')->class('form-adorn-text')->setAttribute('aria-hidden', 'true')->setText($control->translate($control->getSuffix())));
			}
		}
		else
		{
			$core = $input;
		}

		$before = [];
		$after = [];

		/**
		 * Adornments which cannot be inside the control go to the input group
		 */
		$grouped = $floating || !$this->canAdorn($control);

		if($grouped && $icon !== null)
		{
			$before[] = Html::el('span')->class('input-group-text')->addHtml($this->icon($icon));
		}

		if($grouped && $control->getPrefix() !== null)
		{
			$before[] = Html::el('span')->class('input-group-text')->setText($control->translate($control->getPrefix()));
		}

		if($grouped && $control->getSuffix() !== null)
		{
			$after[] = Html::el('span')->class('input-group-text')->setText($control->translate($control->getSuffix()));
		}

		if($control->getPrepend() !== null)
		{
			$before[] = Html::el('span')->class($this->joinClass('input-group-text', $control->getPrependClass()))->addHtml($control->getPrepend());
		}

		if($control->getAppend() !== null)
		{
			$after[] = Html::el('span')->class($this->joinClass('input-group-text', $control->getAppendClass()))->addHtml($control->getAppend());
		}

		if($control instanceof QuickCopyable && $control->getQuickCopy())
		{
			$after[] = Html::el('button')
				->type('button')
				->class('btn-outline theme-secondary input-group-btn')
				->setAttribute('data-mis-copy', true)
				->setAttribute('title', $control->translate('Zkopírovat do schránky'))
				->setAttribute('aria-label', $control->translate('Zkopírovat do schránky'))
				->addHtml($this->icon('copy'));
		}

		/**
		 * Size goes to the input group as a whole, otherwise to the control (adorn wrapper is the control)
		 */
		$size = $floating ? null : $this->getSize($control);

		if(!$before && !$after)
		{
			if($size)
			{
				$core->appendAttribute('class', 'form-control-' . $size);
			}

			return $this->addInputExtras($control, $core);
		}

		$group = Html::el('div')
			->class($this->joinClass('input-group', $size ? 'input-group-' . $size : null));

		foreach([...$before, $core, ...$after] as $part)
		{
			$group->addHtml($part);
		}

		return $this->addInputExtras($control, $group);
	}


	/**
	 * Parts rendered after the control - suggestions (datalist) and password strength meter
	 */
	protected function addInputExtras(BaseControl&Control\InputField $control, Html $block): Html
	{
		$suggestions = $control instanceof Control\HasSuggestions && ($control->getSuggestions() || $control->hasRemoteSuggestions());
		$strength = $control instanceof Control\TextInput && $control->hasStrengthMeter();

		if(!$suggestions && !$strength)
		{
			return $block;
		}

		$fragment = Html::el()
			->addHtml($block);

		if($control instanceof Control\HasSuggestions && $suggestions)
		{
			$datalist = Html::el('datalist')
				->id($control->getHtmlId() . '-suggestions');

			/**
			 * The key goes along for the select callback of autocomplete
			 */
			foreach($control->getSuggestions() as $key => $suggestion)
			{
				$datalist->addHtml(Html::el('option')->value($suggestion)->setAttribute('data-key', $key));
			}

			$fragment->addHtml($datalist);
		}

		if($control instanceof Control\TextInput && $strength)
		{
			$meter = Html::el('div')
				->class('strength')
				->setAttribute('data-bs-strength', true)
				->setAttribute('data-bs-input', '#' . $control->getHtmlId())
				->setAttribute('data-bs-min-length', $control->getStrengthMinLength())
				->setAttribute('data-bs-messages', Json::encode([
					'weak' => $control->translate('Slabé heslo'),
					'fair' => $control->translate('Průměrné heslo'),
					'good' => $control->translate('Dobré heslo'),
					'strong' => $control->translate('Silné heslo')
				]));

			for($i = 0; $i < 4; $i++)
			{
				$meter->addHtml(Html::el('div')->class('strength-segment'));
			}

			/**
			 * The text is looked up next to the meter - both have their own wrapper
			 */
			$fragment->addHtml(
				Html::el('div')
					->addHtml($meter)
					->addHtml(Html::el('small')->class('strength-text form-text'))
			);
		}

		return $fragment;
	}


	/**
	 * Native range input with tick marks (datalist) and value bubble
	 */
	protected function createRangeBlock(Control\SliderInput $control, Html $input): Html
	{
		$range = Html::el('div')
			->class('form-range')
			->setAttribute('data-mis-range-items', $control->getItems() ? Json::encode($control->getBubbleItems()) : null);

		if($control->isRange())
		{
			/**
			 * Two handles (assets/js/rangeDual.js) - their values are written to the hidden input, ticks belong to the first one
			 */
			$bounds = $control->getBounds();
			$labels = [$control->translate('Od'), $control->translate('Do')];
			$parts = [];

			foreach(['from', 'to'] as $index => $part)
			{
				$parts[] = Html::el('input')
					->type('range')
					->class($this->joinClass('form-range-input', $control->getValidationClass()))
					->setAttribute('min', $bounds['min'])
					->setAttribute('max', $bounds['max'])
					->setAttribute('step', $bounds['step'])
					->setAttribute('value', $control->getRangePositions()[$index])
					->setAttribute('list', $index === 0 && $control->getTicks() ? $control->getTicksId() : null)
					->setAttribute('disabled', $control->isDisabled())
					->setAttribute('aria-label', $control->translate($control->getCaption()) . ' – ' . $labels[$index])
					->setAttribute('data-mis-range-part', $part);
			}

			$range->appendAttribute('class', 'mis-range-dual')
				->setAttribute('data-mis-range-dual', true)
				->setAttribute('data-mis-bubble', $control->hasBubble() ? true : null)
				->addHtml($parts[0])
				->addHtml($parts[1])
				->addHtml($input);
		}
		else
		{
			$range->setAttribute('data-bs-bubble', $control->hasBubble() ? true : null)
				->addHtml($input);
		}

		$ticks = $control->getTicks();

		if(!$ticks)
		{
			return $range;
		}

		$datalist = Html::el('datalist')
			->id($control->getTicksId());

		foreach($ticks as ['value' => $value, 'label' => $label])
		{
			$datalist->addHtml(Html::el('option')->value($value)->setAttribute('label', $label));
		}

		return Html::el()
			->addHtml($range)
			->addHtml($datalist);
	}


	/**
	 * Read-only text input with Bootstrap datepicker (assets/js/datepicker.js) - it takes over the html id, so the label
	 * points to it, the native input stays hidden and keeps the value in Y-m-d
	 */
	protected function createDatepickerInput(Control\DateTimeInput $control, Html $native): Html
	{
		$value = $native->getAttribute('value');
		$nativeId = $control->getHtmlId() . '-value';

		$native->id($nativeId)
			->setAttribute('hidden', true);

		return Html::el('input')
			->type('text')
			->id($control->getHtmlId())
			->class($native->getAttribute('class'))
			->placeholder($native->getAttribute('placeholder') ?? $control->translate('Vyberte datum'))
			->setAttribute('readonly', true)
			->setAttribute('autocomplete', 'off')
			->setAttribute('disabled', $control->isDisabled())
			->setAttribute('data-bs-toggle', 'datepicker')
			->setAttribute('data-bs-date-min', $control->getPickerLimit(Form::Min))
			->setAttribute('data-bs-date-max', $control->getPickerLimit(Form::Max))
			->setAttribute('data-bs-selected-dates', $value ? Json::encode([$value]) : null)
			->setAttribute('data-mis-datepicker', $nativeId)
			->setAttribute('data-mis-proxy', true);
	}


	/**
	 * One text input shown as slots by Bootstrap otp plugin
	 */
	protected function createOtpBlock(Control\OtpInput $control, Html $input): Html
	{
		$size = $this->getSize($control);

		return Html::el('div')
			->class($this->joinClass('otp', $size ? 'otp-' . $size : null, $control->getValidationClass()))
			->setAttribute('data-bs-otp', true)
			->setAttribute('data-bs-type', $control->getType())
			->setAttribute('data-bs-mask', $control->isMasked() ? 'true' : null)
			->setAttribute('data-bs-groups', $control->getGroups() ? Json::encode($control->getGroups()) : null)
			->addHtml($input);
	}


	protected function renderChips(Control\ChipsInput $control): Html
	{
		return $this->layoutField($control, $this->createChipsBlock($control), $this->getFormRenderType($control));
	}


	/**
	 * Chips with a text input for new ones (Bootstrap chip input) and the hidden select carrying the values
	 */
	protected function createChipsBlock(Control\ChipsInput $control): Html
	{
		$validationClass = $control->getValidationClass();

		$chips = Html::el('div')
			->class($this->joinClass('chip-input', $control->color ? $this->themeClass($control->color) : null))
			->setAttribute('data-bs-chips', true)
			->setAttribute('data-bs-separator', $control->getSeparator() ?? 'null')
			->setAttribute('data-bs-max-chips', $control->getMaxChips())
			->setAttribute('data-bs-allow-duplicates', $control->isAllowDuplicates() ? 'true' : null)
			->setAttribute('data-mis-chips', $control->getValuesId());

		foreach($control->getValue() as $value)
		{
			$chips->addHtml(
				Html::el('span')
					->class('chip')
					->setAttribute('data-bs-chip-value', $value)
					->setText($value)
			);
		}

		$chips->addHtml(
			Html::el('input')
				->type('text')
				->id($control->getHtmlId())
				->class($this->joinClass('form-ghost', $validationClass))
				->placeholder($control->getPlaceholder())
				->disabled($control->isDisabled())
				->setAttribute('data-mis-proxy', true)
		);

		$select = $control->getControl();

		$this->addSignals($control, $select);

		return Html::el()
			->addHtml($chips)
			->addHtml($select);
	}


	/**
	 * Caption with required mark and tooltip
	 */
	protected function createLabel(BaseControl&Control\Field $control, ?string $class = null): ?Html
	{
		$label = $control->getLabel();

		if(!$label instanceof Html)
		{
			return null;
		}

		$label->class($class);

		$this->addLabelMarks($control, $label);

		return $label;
	}


	protected function addLabelMarks(BaseControl&Control\Field $control, Html $label): void
	{
		if($control->isRequired())
		{
			$label->addHtml(' ')
				->addHtml(Html::el('span')->class('mis-required fg-danger')->setAttribute('aria-hidden', 'true')->setText('*'));
		}

		if($control->getTooltip() !== null)
		{
			$tooltip = $control->translate($control->getTooltip());

			$label->addHtml(' ')
				->addHtml(
					Html::el('span')
						->class('mis-tooltip')
						->setAttribute('tabindex', 0)
						->setAttribute('data-bs-toggle', 'tooltip')
						->setAttribute('data-bs-title', $tooltip)
						->setAttribute('aria-label', $tooltip)
						->addHtml($this->icon('help'))
				);
		}
	}


	protected function createFeedback(BaseControl&Control\Field $control): Html|string
	{
		if(!$this->isValidated($control))
		{
			return '';
		}

		/**
		 * Error messages may contain the submitted value (%value), therefore plain strings are escaped - same as Nette renderer
		 */
		if($control->hasErrors())
		{
			$feedback = Html::el('div')
				->class('invalid-feedback');

			foreach($control->getErrors() as $i => $error)
			{
				$feedback->addText(($i > 0 ? ' ' : '') . $error);
			}

			return $feedback;
		}

		$message = $control->getValidationSuccessMessage();

		if($message === null || !$control->isRequired())
		{
			return '';
		}

		return Html::el('div')
			->class('valid-feedback')
			->addText($message instanceof HtmlStringable ? $message : $control->translate($message));
	}


	protected function createDescription(BaseControl&Control\Field $control): Html|string
	{
		$description = $control->getDescription();

		if($description === null)
		{
			return '';
		}

		return Html::el('div')
			->class('form-text')
			->addText($description instanceof HtmlStringable ? $description : $control->translate($description));
	}


	protected function createFieldElement(string $class): Html
	{
		return Html::el('div')
			->class($class)
			->setAttribute('data-mis-field', true);
	}


	protected function createRow(string $content): Html
	{
		return Html::el('div')
			->class('row g-3')
			->addHtml($content);
	}


	/**
	 * Theme color goes to the header only (see createGroupHeader()) - on the card it would color the controls inside
	 */
	protected function createCard(?string $color): Html
	{
		return Html::el('div')
			->class($color ? 'card card-subtle' : 'card');
	}


	protected function createFooter(string $buttons): Html
	{
		return Html::el('div')
			->class('card-footer d-flex flex-wrap gap-2')
			->addHtml($buttons);
	}


	/**
	 * Title and icon are fallbacks used when the group has none of its own (form title on the first card)
	 */
	protected function createGroupHeader(ControlGroup $group, Html|string|null $title, ?string $icon, ?string $color): ?Html
	{
		$icon = $group->getIcon() ?? $icon;
		$title = $group->getOption('label') ?: $title;

		if(!$title && !$icon)
		{
			return null;
		}

		$header = Html::el('div')
			->class($this->joinClass('card-header flex-row align-items-center gap-2', $color ? $this->themeClass($color) : null));

		if($icon)
		{
			$header->addHtml($this->icon($icon));
		}

		if($title)
		{
			$header->addText($title);
		}

		return $header;
	}


	protected function createFormErrors(Form $form): ?Html
	{
		$errors = $form->getFormErrors();

		if(!$errors)
		{
			return null;
		}

		$alert = Html::el('div')
			->class('alert theme-danger mb-3')
			->setAttribute('role', 'alert');

		foreach($errors as $i => $error)
		{
			if($i > 0)
			{
				$alert->addHtml(Html::el('br'));
			}

			$alert->addText($error);
		}

		return $alert;
	}


	protected function addSignals(BaseControl $control, Html $input): void
	{
		if($control instanceof Control\Signalable && $control->hasSignal())
		{
			$control->addSignalsToInput($input);
		}
	}


	protected function getFormRenderType(BaseControl $control): RenderType
	{
		$form = $control->getForm();

		return $form instanceof Form ? $form->getRenderType() : RenderType::Default;
	}


	/**
	 * Bootstrap floating labels support text inputs, textareas and single selects only
	 */
	protected function supportsFloating(BaseControl $control): bool
	{
		return !$control instanceof \Nette\Forms\Controls\UploadControl
			&& !$control instanceof \Nette\Forms\Controls\MultiSelectBox
			&& !$control instanceof \Nette\Forms\Controls\ColorPicker
			&& !$control instanceof Control\SliderInput
			&& !$control instanceof Control\OtpInput;
	}


	/**
	 * Size of the control, or of the form when the control has none
	 */
	protected function getSize(BaseControl $control): ?string
	{
		if(method_exists($control, 'getSize') && $control->getSize() !== null)
		{
			return $control->getSize();
		}

		$form = $control->getForm(false);

		return $form instanceof Form ? $form->getSize() : null;
	}


	/**
	 * Label column of the Inline render type
	 */
	protected function getColumnLabelClass(BaseControl&Control\Field $control): string
	{
		$size = $this->getSize($control);

		return $this->joinClass('col-form-label', $size ? 'col-form-label-' . $size : null, $control->getLabelWrapClass() ?? 'sm:col-4');
	}


	protected function isValidated(BaseControl $control): bool
	{
		$form = $control->getForm(false);

		return $form !== null && $form->isAnchored() && ($form->isSubmitted() || ($control instanceof Control\Field && $control->isSubmitted()));
	}


	private function getListLabelId(BaseControl $control): string
	{
		return $control->getHtmlId() . '-label';
	}


	/**
	 * Class attribute from parts, empty parts are dropped
	 */
	protected function joinClass(?string ...$parts): string
	{
		return implode(' ', array_filter($parts, fn(?string $part): bool => $part !== null && $part !== ''));
	}
}
