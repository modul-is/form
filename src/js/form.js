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

function initForm()
{
	$('[data-on-focusout]').unbind();
	$('[data-on-change]').unbind();
	$('[data-whisperer], [data-whisperer-onselect], [data-whisperer-delay]').unbind();

	$('[data-on-focusout]').focusout(function(e)
	{
		inputSignal($(this), $(this).attr('data-on-focusout'), e);
	});

	$('[data-on-change]').change(function(e)
	{
		inputSignal($(this), $(this).attr('data-on-change'), e);
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

	if(typeof naja !== "undefined")
	{
		const formExtension =
		{
			initialize(naja)
			{
				naja.snippetHandler.addEventListener('afterUpdate', () =>
				{
					initForm();
				});
			}
		};

		naja.registerExtension(formExtension);
	}
});
