<?php

declare(strict_types = 1);

namespace ModulIS\Form\Renderer;

use ModulIS\Form\Container;
use ModulIS\Form\ControlGroup;
use ModulIS\Form\Control;
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


	public function __construct(?IconRenderer $iconRenderer = null)
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

		if($renderType === RenderType::Inline)
		{
			$label = $this->createLabel($control, 'col-form-label ' . ($control->getLabelWrapClass() ?? 'sm:col-4'));

			$field = $this->createFieldElement('form-field ' . ($control->getInputWrapClass() ?? 'sm:col-8'))
				->addHtml($this->createInputBlock($control, $input))
				->addHtml($this->createFeedback($control))
				->addHtml($this->createDescription($control));

			$row = Html::el('div')
				->class($control->getRowClass() ?? 'row')
				->addHtml($label ?? Html::el('div')->class('col-form-label ' . ($control->getLabelWrapClass() ?? 'sm:col-4')))
				->addHtml($field);

			return $control->createWrap()
				->addHtml($row);
		}

		if($control->getInputWrapClass())
		{
			$input->appendAttribute('class', $control->getInputWrapClass());
		}

		$field = $this->createFieldElement('form-field')
			->addHtml($this->createLabel($control, $this->joinClass('form-label', $control->getLabelWrapClass())) ?? '')
			->addHtml($this->createInputBlock($control, $input))
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

		$input->appendAttribute('class', $this->joinClass($control->isSwitch() ? null : 'check', $control->getValidationClass(), $control->getInputWrapClass()));

		if($control->isSwitch())
		{
			$input->setAttribute('role', 'switch')
				->setAttribute('switch', true);

			$input = Html::el('div')
				->class('switch')
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
			$label = $this->createListLabel($control, 'col-form-label ' . ($control->getLabelWrapClass() ?? 'sm:col-4'));

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
			$items = Html::el('div')
				->class('btn-group flex-wrap')
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

		$input->appendAttribute('class', $this->joinClass(
			$control instanceof Control\RadioList ? 'radio' : 'check',
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

		$button->class($this->joinClass(
			'btn-' . ($control->getVariant() ?? 'solid'),
			$this->themeClass($control->color ?? 'secondary'),
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

		$input->appendAttribute('class', $this->joinClass($control->getIcon() !== null ? 'form-ghost' : 'form-control', $control->getValidationClass()));

		$this->addSignals($control, $input);

		return $input;
	}


	/**
	 * Control with its adornments - icon inside (form-adorn), input group around (prepend, append, quick copy),
	 * floating label
	 */
	protected function createInputBlock(BaseControl&Control\InputField $control, Html $input, ?Html $floatingLabel = null): Html
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
			 * Icon inside a floating field is not supported by Bootstrap - it goes to the input group instead
			 */
			if($icon !== null)
			{
				$input->class(str_replace('form-ghost', 'form-control', (string) $input->getAttribute('class')));
			}

			$core = Html::el('div')
				->class($this->joinClass('form-floating', $control->getValidationClass()))
				->addHtml($floatingLabel)
				->addHtml($input);
		}
		elseif($icon !== null)
		{
			$core = Html::el('label')
				->class('form-control form-adorn')
				->addHtml(Html::el('span')->class('form-adorn-icon')->setAttribute('aria-hidden', 'true')->addHtml($this->icon($icon)))
				->addHtml($input);
		}
		else
		{
			$core = $input;
		}

		$before = [];
		$after = [];

		if($floating && $icon !== null)
		{
			$before[] = Html::el('span')->class('input-group-text')->addHtml($this->icon($icon));
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

		if(!$before && !$after)
		{
			return $core;
		}

		$group = Html::el('div')
			->class('input-group');

		foreach([...$before, $core, ...$after] as $part)
		{
			$group->addHtml($part);
		}

		return $group;
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
	 * Bootstrap floating labels do not support file inputs and multiple selects
	 */
	protected function supportsFloating(BaseControl $control): bool
	{
		return !$control instanceof \Nette\Forms\Controls\UploadControl
			&& !$control instanceof \Nette\Forms\Controls\MultiSelectBox;
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
