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
	input.siblings('.signal-error').hide();
	input.siblings('.signal-success').hide();
	input.siblings('.signal-waiting').hide();
	input.siblings('.signal-loading').show();;
	
	let requestParams = {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json'
		},
		body: JSON.stringify({value: input.val()})
	};
	
	await fetch(url, requestParams)
		.then(response => response.json())
		.then(data => {
			input.siblings('.signal-loading').hide();
			if(data.errorMessage)
			{
				errorSpan = input.siblings('.signal-error');

				errorSpan.attr('title', data.errorMessage);
				errorSpan.show();
			}
			else
			{
				input.siblings('.signal-success').show();
			}
		})
		.catch((error) => {
			input.siblings('.signal-loading').hide();
			input.siblings('.signal-error').show();
		});
}

function registerAutocomplete(element)
{
	var allowedChars = new RegExp(/^[a-zA-Z\s]+$/);
	var jqueryElement = $('#' + element.id);
	var varUrlOnChange = jqueryElement.data('autocomplete');
	var varUrlOnSelect = jqueryElement.data('autocomplete-onselect');
	var delay = jqueryElement.data('autocomplete-delay');
	var items = jqueryElement.data('autocomplete-items');

	if(items === undefined)
	{
		var items = [];
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

			$('#' + inputfield.id).addClass('bg-success bg-opacity-10');
			
			setTimeout(function()
			{
				$('#' + inputfield.id).removeClass('bg-success bg-opacity-10');
			}, 900);
			
			if(typeof varUrlOnSelect !== 'undefined')
            {
				var form = jqueryElement.closest('form');

				naja.makeRequest('GET', varUrlOnSelect, {selected: item.data, formdata: form.serialize()});
            }
		},
		fetch: function (text, callback)
		{
			var match = text.toLowerCase();

			if(typeof varUrlOnChange !== 'undefined')
			{
				naja.makeRequest('POST', varUrlOnChange, {param: text}, {dataType: "json"}).then((response) => {
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