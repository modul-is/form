# Nette form with custom BS5 renderer
This library allows you to spend less time writing templates for Nette forms - it contains renderers for form, containers as well as all inputs

> **v2.0** reworks rendering. If you are coming from v1, see [Upgrading from v1 to v2.0](#upgrading-from-v1-to-v20).

## Getting started
Easiest way to create form is to create component which extends `FormComponent` class

```
class MyForm extends \ModulIS\Form\FormComponent
{
	public function createComponentForm(): Form
	{
		$form = $this->getForm();
		
		...
		
		return $form;
	}
}
```

Then you just add inputs to form, create `MyForm` component and all done!

## Inputs
Form supports all of the default Nette inputs and adds new ones

+ `addLink()` - Button with link (eg. reset, go back buttons)
+ `addWhisperer()` - select box with whisperer which filters options
+ `addMultiWhisperer()` - same as whisperer, more options can be selected
+ `addDuplicator()` - container which can be duplicated many times
+ `addDependentSelect()` - select box that can change options via ajax based on change of another input(s) 
+ `addDependentMultiSelect()` - same as DependentSelect, but more options can be selected 
+ `addDate()` - date input, can limit min and max date
+ `addSlider()` - range slider, single value or an interval

## Custom settings

### Form

+ `setTitle()` - add `card-header` div with title
+ `setColor()` - set color of form
+ `setAjax()` - form is submitted via ajax
+ `setRenderType()` - render type for all inputs of the form, see [Render types](#render-types)
+ `setRenderDefault()`, `setRenderFloating()`, `setRenderInline()` - shortcuts for `setRenderType()`
+ `setButtonClass()` - default CSS class for all form buttons (e.g. `rounded rounded-4`); can be overridden by `setClass()` on individual buttons
+ `setRenderManually()` - set manual render, template with same name as form is used (eg. file `MyForm.php` -> `myForm.latte`), see `FormComponent`

### Groups
Form is rendered in BS5 [card](https://getbootstrap.com/docs/5.0/components/card/) - each card represents one group

Inputs are rendered in `card-body` div

Submitters, links and buttons in `card-footer`

### Container
Container works as standard Nette Container and has these new features

+ `setId()` - add html id to outer div of container
+ `showCard()` - show container as BS5 [card](https://getbootstrap.com/docs/5.0/components/card/)
+ `setTitle()` - show title of container (only works when container is rendered as card)
+ `setColor()` - set color of container (only works when container is rendered as card)

### Currency input

`addCurrency()` - numeric input that visually formats values with thousands separators (e.g. `1 000 000`) but returns a plain integer on submit. Currency label is appended to the input.

Default currency can be set globally for the whole project (e.g. in bootstrap or DI extension):

```php
\ModulIS\Form\Control\CurrencyInput::setDefaultCurrency('CZK');
```

Per-input currency can be set via:

```php
$form->addCurrency('price', 'Price', 'EUR');
// or
$form->addCurrency('price', 'Price')->setCurrency('EUR');
```

### Inputs
Some inputs provide new features

+ `setIcon()` - add icon to input or button (Buttons, Links, Text inputs)
+ `setColor()` - add color to input or button (Buttons, Links, Checkbox, Lists)
+ `setTemplate()` - add custom latte template instead of basic render (All inputs)
+ `setPrepend()` - adds prepend part to [input group](https://getbootstrap.com/docs/5.0/forms/input-group/) (Text inputs, Select boxes)
+ `setAppend()` - adds append part to [input group](https://getbootstrap.com/docs/5.0/forms/input-group/) (Text inputs, Select boxes)
+ `setRenderType()` - render type of a single input, overwrites the setting from Form, see [Render types](#render-types) (All non-button inputs)
+ `setRenderDefault()`, `setRenderFloating()`, `setRenderInline()` - shortcuts for `setRenderType()`; use `setRenderDefault()` to opt a single input out of a form-wide floating/inline setting
+ `setAutoRenderSkip()` - skips rendering of input, eg. if input is rendered as part of another input with custom template (All inputs)
+ `setTooltip()` - add icon with tooltip to input (Text inputs, Checkbox, Lists, Select boxes)
+ `setQuickCopy()` - add button to copy input value to clipboard (Text inputs, TextArea)
+ `setWrapClass()` - set class to outer div around label and input - overwrites basic `col-` class (Text inputs, Checkbox, Lists, Select boxes)
+ `setLabelWrapClass()` - set class to wrap div around label - overwrites basic `col-` class (Text inputs, Checkbox, Lists, Select boxes)
+ `setInputWrapClass()` - set class to wrap div around input - overwrites basic `col-` class (Text inputs, Checkbox, Lists, Select boxes)

### Render types

Every input is rendered in one of the modes of `\ModulIS\Form\Enum\RenderType`. The form sets the default,
a single input can override it.

+ `RenderType::Default` - label above the input (default)
+ `RenderType::Floating` - [floating label](https://getbootstrap.com/docs/5.0/forms/floating-labels/)
+ `RenderType::Inline` - label and input side by side

```php
$form->setRenderFloating();

$form->addText('name', 'Name')
	->setRenderDefault();
```

`CheckboxList` and `RadioList` use `\ModulIS\Form\Enum\RenderListType` instead, which adds
`Big` (tiles) and `Compact` on top of `Default`, `Floating` and `Inline`.

### Slider

`addSlider()` renders a slider built on [rSlider.js](https://github.com/slawomir-zaziablo/range-slider).
The value domain is either a continuous range or an explicit list of values.

```php
// continuous range - min, max and step can be passed straight to addSlider()
$form->addSlider('age', 'Age', 0, 100, 5);

// ...or set later
$form->addSlider('age', 'Age')
	->setMinMax(0, 100, 5);

// explicit list of values
$form->addSlider('year', 'Year')
	->setItems([2020, 2021, 2022]);
```

`setRange()` turns on a second handle - the value then becomes an array `[from, to]`:

```php
$form->addSlider('span', 'Span', 0, 100, 10)
	->setRange()
	->setValue([20, 60]);

$slider->getValue();     // [20, 60]
```

Without `setRange()` the value is a single `int` or `float`:

```php
$form->addSlider('age', 'Age', 0, 100, 5)
	->setValue(35);

$slider->getValue();     // 35
```

Appearance of the slider itself:

+ `showScale()` - ticks under the track (default `true`)
+ `showLabels()` - value labels under the ticks (default `true`)
+ `showTooltip()` - bubble with the current value above the handle (default `true`)

`setMinMax()` and `setItems()` are mutually exclusive - the later call wins. Calling neither
throws when the input is rendered.

### Duplicator example
```
$duplicator = $form->addDuplicator('duplicator', function(\ModulIS\Form\DuplicatorContainer $container)
{
	$container->addText('text', 'Text input');

	$container->addSubmit('del', 'Smazat');
}, 1);

$duplicator->addSubmit('add', 'Přidat');
```

## Upgrading from v1 to v2.0

### Render types

`setFloatingLabel()` and the boolean `setRenderInline()` are gone. Rendering is now driven by
`RenderType` / `RenderListType` - see [Render types](#render-types).

| v1 | v2.0 |
| --- | --- |
| `$form->setFloatingLabel()` | `$form->setRenderFloating()` |
| `$form->setRenderInline()` | unchanged (no longer takes a bool) |
| `$input->setFloatingLabel()` | `$input->setRenderFloating()` |
| `$input->setFloatingLabel(false)` | `$input->setRenderDefault()` |
| `$input->setRenderInline(false)` | `$input->setRenderDefault()` |
| `$form->addBox()` | removed, use `addGroup()` |

### CSS classes

The temporary `new-design-*` / `*-new-*` class names were replaced by the `mis-` prefix.
If you style or script against them in your project, rename accordingly:

| v1 | v2.0 |
| --- | --- |
| `new-design-input` | `mis-input` |
| `new-design-label` | `mis-field` |
| `new-design-btn` | `mis-btn` |
| `text-input-new-inline` | `mis-field-inline` |
| `text-input-new-label` | `mis-field-inline-label` |
| `text-input-new-control` | `mis-field-inline-control` |
| `new-design-radio-wrap` | `mis-radio` |
| `new-design-radio-input-wrap` | `mis-radio-items` |
| `new-design-radio-input` | `mis-radio-input` |
| `new-design-radio-label` | `mis-radio-label` |
| `new-design-checkbox-row` | `mis-checkbox` |
| `new-design-checkbox-right` | `mis-checkbox-right` |
| `checkbox-new-row` | `mis-checklist` |
| `checkbox-new-row-item-wrap` | `mis-checklist-items` |
| `new-design-compact` | `mis-compact` |
| `new-design-compact-label` | `mis-compact-label` |
| `new-design-compact-input-wrap` | `mis-compact-items` |
| `new-design-compact-input-field` | `mis-compact-field` |
| `new-design-checkbox-big-block` | `mis-tiles` |
| `new-design-checkbox-big-block-head` | `mis-tiles-head` |
| `new-design-checkbox-big-block-title` | `mis-tiles-title` |
| `new-design-checkbox-big-block-sub` | `mis-tiles-sub` |
| `new-design-checkbox-big-tiles` | `mis-tiles-list` |
| `new-design-checkbox-big-tile` | `mis-tile` |
| `new-design-checkbox-big-ico` | `mis-tile-ico` |
| `new-design-checkbox-big-lbl` | `mis-tile-lbl` |
| `new-design-checkbox-big-desc` | `mis-tile-desc` |
| `new-design-checkbox-big-chk` | `mis-tile-chk` |

`new-design-checkbox-wrap`, `new-design-checkbox-item` and `new-design-checkbox-item-label`
were dropped - the render path that used them was unreachable.

### Removed internals

`ModulIS\Form\Helper\RenderFloatingList` and `WrapControl::renderWrap()` were removed as dead code.
