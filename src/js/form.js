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
			$('#' + progressId).find('span').removeClass(error + ' ' + success).addClass(loading);
		}

		iconSpan = $('#' + progressId).find('span');
	}

	let value = null;
	let inputName = null;

	if (input.attr('type') === 'checkbox')
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

	naja.makeRequest('GET', url, {value: value, input: inputName, formdata: form.serialize()})
		.then(response =>
		{
			if(showProgress)
			{
				iconSpan.removeClass('fa-spinner fa-spin');

				if(response.errorMessage)
				{
					iconSpan.addClass(error);
					iconSpan.attr('title', response.errorMessage);
				}
				else
				{
					iconSpan.addClass(success);
				}
			}
		})
		.catch((error) =>
		{
			if(showProgress)
			{
				iconSpan.addClass(error);
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

function formatSelectData(data)
{
	if (!data.id)
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

	$('.form-control-chosen, .form-control-chosen-required').chosen({
		allow_single_deselect: true,
		no_results_text: "Nebyla nalezena žádná položka - ",
		width: '100%'
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