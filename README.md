# Nette form with custom BS5 renderer
This library allows you to spend less time writing templates for Nette forms - it contains renderers for form, containers as well as all inputs

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
+ `addDependentMultiSelect()` - same as DependantSelect, but more options can be selected 
+ `addDate()` - date input, can limit min and max date

## Custom settings

### Form

+ `setTitle()` - add `card-header` div with title
+ `setColor()` - set color of form
+ `setAjax()` - form is submitted via ajax
+ `addBox()` - all inputs added after this call will render in new card
+ `setFloatingLabel()` - inputs will be rendered with [floating labels](https://getbootstrap.com/docs/5.0/forms/floating-labels/)
+ `setRenderInline()` - label and input are rendered each in separate row
+ `setRenderManually()` - set manual render, template with same name as form is used (eg. file `MyForm.php` -> `myForm.latte`)

### Groups
Form is rendered in BS5 [card](https://getbootstrap.com/docs/5.0/components/card/) - each card represents one group

Inputs are rendered in `card-body` div

Submitters, links and buttons in `card-footer`

### Container
Container works as standard Nette Continer and has these new features

+ `setId()` - add html id to outer div of container
+ `showCard()` - show container as BS5 [card](https://getbootstrap.com/docs/5.0/components/card/)
+ `setTitle()` - show title of container (only works when container is rendered as card)
+ `setColor()` - set color of conatiner (only works when container is rendered as card)

### Inputs
Some inputs provide new features

+ `setIcon()` - add icon to input or button (Buttons, Links, Text inputs)
+ `setColor()` - add color to input or button (Buttons, Links, Checkbox, Lists)
+ `setTemplate()` - add custom latte template insted of basic render (All inputs)
+ `setPrepend()` - adds prepend part to [input group](https://getbootstrap.com/docs/5.0/forms/input-group/) (Text inputs, Select boxes)
+ `setAppend()` - adds append part to [input group](https://getbootstrap.com/docs/5.0/forms/input-group/) (Text inputs, Select boxes)
+ `setRenderInline()` - render label and input each in separate row, overwrites `renderInline` setting from Form (All non-button inputs)
+ `setFloatingLabel()` - input will be rendered with [floating labels](https://getbootstrap.com/docs/5.0/forms/floating-labels/) (Text inputs, Select box)
+ `setAutorenderSkip()` - skips rendering of input, eg. if input is rendered as part of another input with custom template (All inputs)
+ `setTooltip()` - add icon with tooltip to input (Text inputs, Checkbox, Lists, Selec boxes)
+ `setWrapClass()` - set class to outer div around label and input - overwrites basic `col-` class (Text inputs, Checkbox, Lists, Selec boxes)
+ `setLabelWrapClass()` - set class to wrap div around label - overwrites basic `col-` class (Text inputs, Checkbox, Lists, Selec boxes)
+ `setInputWrapClass()` - set class to wrap div around input - overwrites basic `col-` class (Text inputs, Checkbox, Lists, Selec boxes)

### Duplicator example
```
	$duplicator = $form->addDuplicator('duplicator', function(\ModulIS\Form\DuplicatorContainer $container)
	{
		$container->addText('text', 'Text input');

		$container->addSubmit('del', 'Smazat');
	}, 1);

	$duplicator->addSubmit('add', 'Přidat');
```
