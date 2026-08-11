import naja from 'naja';

Nette.validators.CodeComponentFormValidator_greater = function(elem, args, val)
{
	return parseInt(val) > parseInt(args);
};

Nette.validators.CodeComponentFormValidator_less = function(elem, args, val)
{
	return parseInt(val) < parseInt(args);
};

Nette.validators.CodeComponentFormValidator_sameLength = function(elem, args, val)
{
	return args.length === val.length;
};

async function inputSignal(input, url, event)
{
	if(event.type === 'focusout')
	{
		let originValue = event.target.defaultValue ?? '';

		if(input.val() === originValue)
		{
			return;
		}
	}

	let loading = 'fa-spinner fa-spin';
	let success = 'fa-check color-green';
	let progressId = input.attr('id') + '_ajax_progress';
	let progressEl = $('#' + progressId);

	let inputTypeArray = ['radio', 'checkbox', 'checkboxlist'];
	let showProgress = !inputTypeArray.includes(input.attr('type'));
	let iconSpan = null;

	if(showProgress)
	{
		if(progressEl.length === 0)
		{
			let inputProgress = '<span id="' + progressId + '" class="input-group-text"><span class="fal ' + loading + ' fa-fw"></span></span>';

			input.closest('div.input-group').append(inputProgress);
		}
		else
		{
			$('#' + progressId).find('span').removeClass(success).addClass(loading);
		}

		iconSpan = $('#' + progressId).find('span');
	}

	let value = null;
	let inputName = null;

	if(input.attr('type') === 'checkbox')
	{
		if(input.attr('name').includes('['))
		{
			inputName = input.attr('name') + input.val();
		}
		else
		{
			inputName = input.attr('name');
		}

		value = input.is(':checked') === true ? 1 : 0;
	}
	else
	{
		value = input.val();
		inputName = input.attr('name');
	}

	let form = input.closest('form');
	let focusElement = event.relatedTarget ? event.relatedTarget.id : input.attr('id');

	naja.makeRequest('POST', url, {value: value, input: inputName, formdata: form.serialize()})
		.then(response =>
		{
			if(showProgress)
			{
				iconSpan.removeClass('fa-spinner fa-spin');

				iconSpan.addClass(success);
			}

			if(focusElement)
			{
				$('#' + focusElement).focus();
			}
		})
		.catch((errorMessage) =>
		{
			if(showProgress)
			{
				iconSpan.removeClass('fa-spinner fa-spin');

				iconSpan.addClass(success);
			}
		});
}

function registerAutocomplete(element)
{
	let allowedChars = new RegExp(/^[a-zA-Zěščřžýáíéťďň0-9\s]+$/);
	let jqueryElement = $('#' + element.id);
	let parents = jqueryElement.data('autocomplete-parents');
	let varUrlOnChange = jqueryElement.data('autocomplete');
	let varUrlOnSelect = jqueryElement.data('autocomplete-onselect');
	let delay = jqueryElement.data('autocomplete-delay');
	let items = jqueryElement.data('autocomplete-items');

	if(items === undefined)
	{
		let items = [];
	}

	autocomplete({
		input: element,
		minLength: 1,
		container: document.createElement('div'),
		disableAutoSelect: true,
		preventSubmit: true,
		debounceWaitMs: delay,
		onSelect: function(item, inputfield)
		{
			inputfield.value = item.value;

			let inputElement = $('#' + inputfield.id);

			inputElement.addClass('bg-success bg-opacity-10');

			setTimeout(function()
			{
				inputElement.removeClass('bg-success bg-opacity-10');
			}, 900);

			if(typeof varUrlOnSelect !== 'undefined')
			{
				let form = jqueryElement.closest('form');

				naja.makeRequest('GET', varUrlOnSelect, {selected: item.data, formdata: form.serialize()});
			}
		},
		fetch: function(text, callback)
		{
			let match = text.toLowerCase();
			let parentArray = {};

			$.each(parents, function(name, id)
			{
				let parentElement = $('#' + id);

				if(parentElement.length > 0)
				{
					let val;

					if(parentElement.prop('type') === 'checkbox')
					{
						val = parentElement.prop('checked') ? 1 : 0;
					}
					else
					{
						val = $(parentElement).val();

						if(!val)
						{
							return;
						}
					}

					parentArray[name] = val;
				}
				else if($("[id^='" +id + "']").length > 0)
				{
					parentArray[name] = $("[id^='" +id + "']:checked").val();
				}
			});

			if(typeof varUrlOnChange !== 'undefined')
			{
				naja.makeRequest('POST', varUrlOnChange, {param: text, parent: parentArray}, {dataType: "json"}).then((response) => {
					callback(response.suggestions);
				});
			}
			else
			{
				callback(items.filter(function(n) { return n.value.toLowerCase().indexOf(match) !== -1; }));
			}
		},
		render: function(item, value)
		{
			let itemElement = document.createElement("div");

			itemElement.setAttribute('data-key', item.data);

			if(allowedChars.test(value))
			{
				var regex = new RegExp(value, 'gi');
				var inner = item.value.replace(regex, function(match) { return "<strong>" + match + "</strong>";});
				itemElement.innerHTML = inner;
			}
			else
			{
				itemElement.textContent = item.value;
			}

			return itemElement;
		},
		emptyMsg: "Nic nenalezeno",
		customize: function(input, inputRect, container, maxHeight)
		{
			if(maxHeight < 100)
			{
				container.style.top = "";
				container.style.bottom = (window.innerHeight - inputRect.bottom + input.offsetHeight) + "px";
				container.style.maxHeight = "140px";
			}
		}
	});
}

function formatSelectData(data)
{
	if(!data.id)
	{
		return data.text;
	}

	let selectId = data.element.parentElement.getAttribute('id');

	let image = $(
		'<span><img class="' + selectId + ' img-flag" /> <span></span></span>'
	);

	let imageDiv = $('#' + selectId + '-select2').find("div[data-key='" + data.id + "']");;

	image.find("span").text(data.text);
	image.find("img").attr("src", imageDiv.attr('data-src'));

	return image;
};

function summernoteIsEmpty(noteEditable)
{
	if(!noteEditable)
	{
		return true;
	}

	let html = (noteEditable.innerHTML || '').trim().toLowerCase();

	if(html === '' || html === '<br>' || html === '<p><br></p>')
	{
		return true;
	}

	let text = (noteEditable.textContent || '').replace(/\u200B/g, '').trim();

	if(text.length > 0)
	{
		return false;
	}

	return noteEditable.querySelector('img,video,audio,iframe,table,hr,ul,ol,li,blockquote') === null;
}

function syncSummernoteRequiredLabels()
{
	$('.form-floating textarea.form-control').each(function()
	{
		let $textarea = $(this);
		let $wrapper = $textarea.closest('.form-floating');
		let $noteEditor = $wrapper.find('> .note-editor');

		if($noteEditor.length === 0)
		{
			return;
		}

		let $label = $wrapper.find('> label.required');

		if($label.length === 0)
		{
			return;
		}

		if($label.find('.summernote-required-star').length === 0)
		{
			$label.append('<span class="summernote-required-star" aria-hidden="true">★</span>');
		}

		let update = function()
		{
			let editable = $noteEditor.find('.note-editable').get(0);
			let hasContent = !summernoteIsEmpty(editable);
			let isFocused = !!(editable && editable === document.activeElement);

			$label.toggleClass('summernote-has-content', hasContent);
			$label.toggleClass('summernote-focused', isFocused);
		};

		$textarea.off('.summernoteRequired');
		$textarea.on('summernote.change.summernoteRequired summernote.blur.summernoteRequired', update);

		update();
	});

	$(document).off('.summernoteRequiredEditable');
	$(document).on('input.summernoteRequiredEditable keyup.summernoteRequiredEditable paste.summernoteRequiredEditable blur.summernoteRequiredEditable', '.form-floating .note-editor .note-editable', function()
	{
		let $editable = $(this);
		let $wrapper = $editable.closest('.form-floating');
		let $label = $wrapper.find('> label.required');

		if($label.length === 0)
		{
			return;
		}

		let hasContent = !summernoteIsEmpty(this);
		$label.toggleClass('summernote-has-content', hasContent);
		$label.toggleClass('summernote-focused', this === document.activeElement);
	});

	$(document).on('focusin.summernoteRequiredEditable focusout.summernoteRequiredEditable', '.form-floating .note-editor .note-editable', function(e)
	{
		let $editable = $(this);
		let $wrapper = $editable.closest('.form-floating');
		let $label = $wrapper.find('> label.required');

		if($label.length === 0)
		{
			return;
		}

		let focused = e.type === 'focusin';
		$label.toggleClass('summernote-focused', focused);
	});
}

function formatCurrencyInput(input)
{
	let raw = input.val().replace(/[^0-9]/g, '');

	if(raw === '')
	{
		input.val('');
		return;
	}

	let formatted = parseInt(raw, 10).toLocaleString('cs-CZ').replace(/\u202f/g, '\u00a0');
	let cursorFromEnd = input[0].selectionEnd !== undefined ? input[0].value.length - input[0].selectionEnd : 0;

	input.val(formatted);

	let newPos = input[0].value.length - cursorFromEnd;
	input[0].setSelectionRange(newPos, newPos);
}

async function buttonSignal(button, url, event)
{
	let form = button.closest('form');

	naja.makeRequest('POST', url, {formdata: form.serialize()});
}

/**
 * Validační stav (#380)
 *
 * Červené zvýraznění a hlášku vykreslí server až při odeslání formuláře a do dalšího odeslání
 * je nepřekreslí. Jakmile pole projde klientskou validací (typicky se vyplní required),
 * stav uklidíme na klientovi - a když se hodnota zase pokazí, vrátíme ho zpět.
 */
const invalidClass = 'is-invalid';
const invalidMarkClass = 'validation-was-invalid';
const feedbackHiddenClass = 'validation-feedback-hidden';

function hasValidationMarkup(element)
{
	return element.classList.contains(invalidClass)
		|| element.classList.contains(invalidMarkClass)
		|| element.querySelector('.' + invalidClass + ', .' + invalidMarkClass + ', .invalid-feedback') !== null;
}

/**
 * Nejvyšší obal, ve kterém leží validační stav pole. Bere jen ty, které neobsahují jiné pole -
 * jinak bychom uklidili i chybu u souseda ve stejném řádku.
 */
function getValidationScope(input)
{
	let name = input.getAttribute('name');
	let element = input.parentElement;
	let scope = null;

	while(element && element.tagName !== 'FORM')
	{
		let hasForeignInput = Array.from(element.querySelectorAll('input[name], select[name], textarea[name], button[name]'))
			.some(function(foreignInput)
			{
				return foreignInput.getAttribute('name') !== name;
			});

		if(hasForeignInput)
		{
			break;
		}

		if(hasValidationMarkup(element))
		{
			scope = element;
		}

		element = element.parentElement;
	}

	return scope ?? input;
}

function getElementsWithClass(scope, className)
{
	let elements = Array.from(scope.querySelectorAll('.' + className));

	if(scope.classList.contains(className))
	{
		elements.push(scope);
	}

	return elements;
}

/**
 * Pravidla nese u skupin (radio/checkbox list) jen první input, ale změnu vyvolá kterýkoli z nich
 */
function getRuleElement(input)
{
	if(input.getAttribute('data-nette-rules'))
	{
		return input;
	}

	let group = input.form.elements.namedItem(input.getAttribute('name'));

	if(group && !(group instanceof Element))
	{
		return Array.from(group).find(function(element)
		{
			return element.getAttribute('data-nette-rules');
		}) ?? input;
	}

	return input;
}

function clearValidationState(scope)
{
	getElementsWithClass(scope, invalidClass).forEach(function(element)
	{
		element.classList.remove(invalidClass);
		element.classList.add(invalidMarkClass);
	});

	/** Hlášku necháme v DOMu (kvůli has-validation a zaoblení input-group), jen ji schováme */
	scope.querySelectorAll('.invalid-feedback').forEach(function(element)
	{
		element.classList.add(feedbackHiddenClass);
	});
}

function restoreValidationState(scope)
{
	getElementsWithClass(scope, invalidMarkClass).forEach(function(element)
	{
		element.classList.remove(invalidMarkClass);
		element.classList.add(invalidClass);
	});

	scope.querySelectorAll('.' + feedbackHiddenClass).forEach(function(element)
	{
		element.classList.remove(feedbackHiddenClass);
	});
}

function refreshValidationState(input)
{
	if(!input.form || !input.getAttribute('name') || typeof Nette === 'undefined')
	{
		return;
	}

	/** Formulář bez chyby řešit nemusíme */
	if(!input.form.querySelector('.' + invalidClass + ', .' + invalidMarkClass))
	{
		return;
	}

	let scope = getValidationScope(input);

	if(!getElementsWithClass(scope, invalidClass).length && !getElementsWithClass(scope, invalidMarkClass).length)
	{
		return;
	}

	if(Nette.validateControl(getRuleElement(input), undefined, true))
	{
		clearValidationState(scope);
	}
	else
	{
		restoreValidationState(scope);
	}
}

function initForm()
{
	$('[data-on-focusout]').unbind();
	$('[data-on-change]').unbind();
	$('[data-on-click]').unbind();
	$('[data-whisperer], [data-whisperer-onselect], [data-whisperer-delay]').unbind();

	$('[data-on-focusout]').focusout(function(e)
	{
		inputSignal($(this), $(this).attr('data-on-focusout'), e);
	});

	$('[data-on-change]').change(function(e)
	{
		inputSignal($(this), $(this).attr('data-on-change'), e);
	});

	$('[data-on-click]').click(function(e)
	{
		buttonSignal($(this), $(this).attr('data-on-click'), e);
	});

	$('.form-control-chosen, .form-control-chosen-required').each(function()
	{
		let $el = $(this);
		if(!$el.length || $el[0].tagName !== 'SELECT')
		{
			return;
		}
		if($el.hasClass('selectpicker'))
		{
			return;
		}
		try
		{
			if($el.data('chosen'))
			{
				$el.chosen('destroy');
			}
			// Chosen zobrazí křížek (allow_single_deselect) pouze pokud je první option prázdná.
			// Pro whisperer elementy s předvyplněnou hodnotou přidáme prázdnou option před inicializací.
			if($el.data('whisperer') && $el.val() && $el.find('option:first').val() !== '')
			{
				$el.prepend($('<option>', {value: '', text: ''}));
			}
			$el.chosen({
				allow_single_deselect: true,
				no_results_text: $el.attr('no-result-message') ?? 'Nebyla nalezena žádná položka - ',
				search_contains: true,
				width: '100%'
			});
		}
		catch(e)
		{
			console.warn('Chosen init failed for', $el[0], e);
		}
	});

	$('.form-control-chosen, .form-control-chosen-required').on('change', function()
	{
		Nette.initOnLoad();
	});

	$('[data-whisperer], [data-whisperer-onselect], [data-whisperer-delay]').whisperer();
	$('select[data-dependentselectbox]').dependentSelectBox();

	$(".select2-image").select2({
		theme: "bootstrap-5",
		templateResult: formatSelectData,
		templateSelection: formatSelectData
	});

	$(document).keydown(function(e)
	{
		if($.inArray(e.code, ['ArrowUp', 'ArrowDown']) === -1)
		{
			return true;
		}

		let element = $(e.target);

		if(element.hasClass('select2-selection'))
		{
			let select = element.closest('div.input-group').children('select.select2-image');

			if(select.siblings('.select2-container--open').length === 0)
			{
				$('#' + select.attr('id')).select2('open');
			}
		}
	});

	var inputs = document.getElementsByClassName("autocomplete-input");

	for(let input of inputs)
	{
		registerAutocomplete(input);
	};

	$('.datepicker-input').each(function()
	{
		let $input = $(this);
		let placeholder = ($input.attr('placeholder') || '').trim();

		if(placeholder === '')
		{
			$input.attr('placeholder', 'dd.mm.rrrr');
		}
	});

	/* Datagrid date filter: default placeholder when empty */
	$('.datagrid table thead .input-group .form-control').each(function()
	{
		let $input = $(this);
		if(($input.attr('placeholder') || '').trim() === '')
		{
			$input.attr('placeholder', 'dd.mm.rrrr');
		}
	});

	syncSummernoteRequiredLabels();

	$(document).off('input.currencyInput', '[data-currency-input]');
	$(document).on('input.currencyInput', '[data-currency-input]', function()
	{
		formatCurrencyInput($(this));
	});

	$(document).off('.validationState');
	$(document).on('input.validationState change.validationState focusout.validationState', 'form input, form select, form textarea', function()
	{
		refreshValidationState(this);
	});

	document.addEventListener('change', function (e) {

		// Radio
		if (e.target.matches('input[type="radio"]')) {
			const groupName = e.target.name;

			document.querySelectorAll(`input[type="radio"][name="${groupName}"]`).forEach(radio => {
				radio.closest('label')?.classList.remove('active');
			});

			e.target.closest('label')?.classList.add('active');
		}

		// Checkbox
		if (e.target.matches('input[type="checkbox"]')) {
			const label = e.target.closest('label');

			label?.classList.toggle('active', e.target.checked);
		}
	});

// Inicializace po načtení stránky
	document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
		radio.closest('label')?.classList.add('active');
	});

	document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
		checkbox.closest('label')?.classList.toggle('active', checkbox.checked);
	});
}

function initQuickCopy()
{
	$(document).off('click.quickCopy', '.quick-copy-btn');
	$(document).on('click.quickCopy', '.quick-copy-btn', function()
	{
		let $btn = $(this);
		let $group = $btn.closest('.input-group');
		let $input = $group.find('input:not([type="hidden"]), textarea').first();
		let value = $input.length ? ($input.val() || '').trim() : '';
		if (value && navigator.clipboard && navigator.clipboard.writeText)
		{
			navigator.clipboard.writeText(value).then(function()
			{
				let $wrap = $btn.closest('.quick-copy-wrap');
				let $popup = $('<div class="quick-copy-popup">Zkopírováno</div>');
				$wrap.append($popup);
				setTimeout(function()
				{
					$popup.addClass('quick-copy-popup-out');
					setTimeout(function() { $popup.remove(); }, 300);
				}, 1200);
			});
		}
	});
}

$(document).ready(function()
{
	initQuickCopy();
	initForm();
});

naja.registerExtension({
	initialize(naja) {
		naja.snippetHandler.addEventListener('afterUpdate', () => {
			initForm();
		});
	}
});
