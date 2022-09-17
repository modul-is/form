$('.autocomplete-input').on('input', function()
{
	autocompleteInput(this);
});

function autocompleteInput(element)
{
	var allowedChars = new RegExp(/^[a-zA-Z\s]+$/);
	var jqueryElement = $('#' + element.id);
	var varUrlOnChange = jqueryElement.data('autocomplete');
	var delay = jqueryElement.data('autocomplete-delay');
	var label = jqueryElement.data('autocomplete-label');
	var items = jqueryElement.data('autocomplete-items');
	if(items === undefined)
	{
		var items = [];
	}

	function charsAllowed(value)
	{
		return allowedChars.test(value);
	}

	autocomplete({
		input: element,
		minLength: 1,
		container: document.createElement('div'),
		disableAutoSelect: true,
		preventSubmit: true,
		debounceWaitMs: delay,
		onSelect: function (item, inputfield)
		{
			inputfield.value = item.label;

			$('#' + inputfield.id).addClass('bg-success bg-opacity-10');
			setTimeout(function()
			{
				$('#' + inputfield.id).removeClass('bg-success bg-opacity-10');
			}, 900);
		},
		fetch: function (text, callback)
		{
			var match = text.toLowerCase();

			if(typeof varUrlOnChange !== 'undefined')
			{
				naja.makeRequest('POST', varUrlOnChange, {param: text}, {dataType: "json"}).then((response) => {

					var itemArray = [];
					$.each(response.suggestions, function(index, el)
					{
						itemArray.push(el.value);
					});
					var onChangeItems = itemArray.map(function(n) { return { label: n, group: label };});

					callback(onChangeItems.filter(function(n) { return n.label.toLowerCase().indexOf(match) !== -1; }));
				});
			}
			else
			{
				var defaultItems = items.map(function(n) { return { label: n, group: label };});

				callback(defaultItems.filter(function(n) { return n.label.toLowerCase().indexOf(match) !== -1; }));
			}
		},
		render: function(item, value)
		{
			var itemElement = document.createElement("div");
			if (charsAllowed(value)) {
				var regex = new RegExp(value, 'gi');
				var inner = item.label.replace(regex, function(match) { return "<strong>" + match + "</strong>" });
				itemElement.innerHTML = inner;
			} else {
				itemElement.textContent = item.label;
			}
			return itemElement;
		},
		emptyMsg: "Nic nenalezeno",
		customize: function(input, inputRect, container, maxHeight) {
			if (maxHeight < 100) {
				container.style.top = "";
				container.style.bottom = (window.innerHeight - inputRect.bottom + input.offsetHeight) + "px";
				container.style.maxHeight = "140px";
			}
		}
	});
}