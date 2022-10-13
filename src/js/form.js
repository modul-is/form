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

async function inputSignal(input, url)
{
	let loading = 'fa-spinner fa-spin';
	let error = 'fa-times color-red';
	let success = 'fa-check color-green';
	let progressId = input.attr('id') + '_ajax_progress';
	let progressEl = $('#' + progressId);

	if(progressEl.length === 0)
	{
		let inputProgress = '<span id="' + progressId + '" class="input-group-text"><span class="fal ' + loading + ' fa-fw"></span></span>';
		
		input.closest('div').append(inputProgress);
	}
	else
	{
		$('#' + progressId).find('span').removeClass(error + ' ' + success).addClass(loading);
	}
	
	let iconSpan = $('#' + progressId).find('span');

	naja.makeRequest('POST', url, JSON.stringify({value: input.val()}))
		.then(data => {
			iconSpan.removeClass('fa-spinner fa-spin');
	
			if(data.errorMessage)
			{
				iconSpan.addClass(error);
				iconSpan.attr('title', data.errorMessage);
			}
			else
			{
				iconSpan.addClass(success);
			}
		})
		.catch((error) => {
			iconSpan.addClass(error);
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
		fetch: function (text, callback)
		{
			let match = text.toLowerCase();
			let parentArray = {};

			$.each(parents, function(name, id)
			{
				let parentElement = $('#' + id);

				if (parentElement.length > 0)
				{
					let val;

					if (parentElement.prop('type') === 'checkbox')
					{
						val = parentElement.prop('checked') ? 1 : 0;
					}
					else
					{
						val = $(parentElement).val();

						if (!val)
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
			var itemElement = document.createElement("div");

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
			if (maxHeight < 100)
			{
				container.style.top = "";
				container.style.bottom = (window.innerHeight - inputRect.bottom + input.offsetHeight) + "px";
				container.style.maxHeight = "140px";
			}
		}
	});
}

function initForm()
{
	$('[data-on-focusout]').unbind();
	$('[data-on-change]').unbind();
	$('[data-whisperer], [data-whisperer-onselect], [data-whisperer-delay]').unbind();
	
	$('[data-on-focusout]').focusout(function()
	{
		inputSignal($(this), $(this).attr('data-on-focusout'));
	});

	$('[data-on-change]').change(function()
	{
		inputSignal($(this), $(this).attr('data-on-change'));
	});

	$('.form-control-chosen').chosen({
		allow_single_deselect: true,
		no_results_text: "Nebyla nalezena žádná položka - ",
		width: '100%'
	});

	$('.form-control-chosen-required').chosen({
		allow_single_deselect: false,
		no_results_text: "Nebyla nalezena žádná položka - ",
		width: '100%'
	});

	$('[data-whisperer], [data-whisperer-onselect], [data-whisperer-delay]').whisperer();
	$('select[data-dependentselectbox]').dependentSelectBox();

	$('div.select-image li').on('click', function()
	{
		let parentDiv = $(this).parents('.select-image');

		parentDiv.find('.selected').removeClass('selected');
		$(this).find('.dropdown-item').addClass('selected');

		let value = $(this).attr('value');
		let selectedLabel = $(this).find('.label-text').text();

		parentDiv.find('.prompt-text').text(selectedLabel);
		$('#' + parentDiv.attr('data-parent-id')).val(value);
	});

	var inputs = document.getElementsByClassName("autocomplete-input");

	for(let input of inputs)
	{
		registerAutocomplete(input);
	};
}

$(document).ready(function()
{
    initForm();
});

if(typeof naja !== "undefined")
{
	const formExtension =
	{
		initialize(naja)
		{
			naja.snippetHandler.addEventListener('afterUpdate', (event) =>
			{
				initForm();
			});
		}
	};

	naja.registerExtension(formExtension);
}