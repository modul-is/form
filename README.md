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
+ `addDependantSelect()` - select box that can change options via ajax based on change of another input(s) 
+ `addDependantMultiSelect()` - same as DependantSelect, but more options can be selected

## Custom settings

### Form

+ `setTitle()` - add `card-header` div with title
+ `setColor()` - set color of form
+ `setAjax()` - form is submitted via ajax
+ `addBox()` - all inputs added after this call will render in new card
+ `setFloatingLabel()` - inputs will be rendered with [floating labels](https://getbootstrap.com/docs/5.0/forms/floating-labels/)
+ `setRenderManually()` - set manual render, template with same name as form is used (eg. file `MyForm.php` -> `myForm.latte`)

### Boxes
Form is rendered in BS5 [card](https://getbootstrap.com/docs/5.0/components/card/) - each card represents box

Inputs are rendered in `card-body` div

Submitters, links and buttons in `card-footer`

### Container
Container works as standard Nette Continer and adds these new features

+ `setId()` - add html id to outer div of container
+ `setInputsPerRow()` - sets how many inputs render in one row
+ `showCard()` - show container as BS card
+ `setTitle()` - show title of container (only works when container is rendered as card)
+ `setColor()` - set color of conatiner (only works when container is rendered as card)

### Inputs
Some inputs provide new features

+ `setIcon()` - add icon to input or button (Buttons, Links, Text inputs)
+ `setColor()` - add color to input or button (Buttons, Links, Checkbox, Lists)
+ `setTemplate()` - add custom latte template insted of basic render (All inputs)
+ `setItemsPerRow()` - how many items should be rendered in one row (Lists)
+ `setPrepend()` - adds prepend part to [input group](https://getbootstrap.com/docs/5.0/forms/input-group/) (Text inputs, Select boxes)
+ `setAppend()` - adds append part to [input group](https://getbootstrap.com/docs/5.0/forms/input-group/) (Text inputs, Select boxes)
+ `setFloatingLabel()` - input will be rendered with [floating labels](https://getbootstrap.com/docs/5.0/forms/floating-labels/) (Text inputs, Select box)
+ `setAutorenderSkip()` - skips rendering of input, eg. if input is rendered as part of another input with custom template (All inputs)
+ `setTooltip()` - add icon with tooltip to input (Text inputs, Checkbox, Lists, Selec boxes)
