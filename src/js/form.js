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