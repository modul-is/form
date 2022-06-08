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

async function inputFocusOut(input)
{
	input.siblings('.focusout-waiting').hide();
	input.siblings('.focusout-loading').show();
	
	let requestParams = {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json'
		},
		body: JSON.stringify({value: input.val()})
	};
	
	await fetch(input.attr('data-on-focusout'), requestParams)
		.then(response => response.json())
		.then(data => {
			input.siblings('.focusout-loading').hide();
			if(data.errorMessage)
			{
				errorSpan = input.siblings('.focusout-error');

				errorSpan.attr('title', data.errorMessage);
				errorSpan.show();
			}
			else
			{
				input.siblings('.focusout-success').show();
			}
		})
		.catch((error) => {
			input.siblings('.focusout-loading').hide();
			input.siblings('.focusout-error').show();
		});
}

$('[data-on-focusout]').focusout(function()
{
	inputFocusOut($(this));
});

$('[data-whisperer], [data-whisperer-onselect], [data-whisperer-delay]').whisperer();
$('select[data-dependentselectbox]').dependentSelectBox();
$('.custom-file input').change(function(e)
{
	var files = [];
	
	for (var i = 0; i < $(this)[0].files.length; i++)
	{
		files.push($(this)[0].files[i].name);
	}
	
	$(this).next('.custom-file-label').html(files.join(', '));
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