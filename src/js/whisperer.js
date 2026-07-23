(function ($) {
	$.fn.whisperer = function()
	{
		var disallowedKeyArray = [13, 37, 38, 39, 40];

		this.each(function()
		{
			var element = $(this);
			var chosenId = (element.attr('id') + '_chosen').replace(/-/g, "_");
			var varUrlOnChange = element.data('whisperer');
			var varUrlOnSelect = element.data('whisperer-onselect');
			var delay = element.data('whisperer-delay') || 400;

			var typingTimer;
			var currentAbortController = null;

			var getSearchInput = function()
			{
				return $('#' + chosenId).find('input.chosen-search-input');
			};

			if(typeof varUrlOnSelect !== 'undefined')
			{
				var form = element.closest('form');

				var searchEvent = function(e)
				{
					if((e.keyCode || e.which) === 13)
					{
						naja.makeRequest('GET', varUrlOnSelect, {
							selected: element.val(),
							formdata: form.serialize()
						}).then(function()
						{
							$(this).closest('.chosen-with-drop').removeClass('chosen-with-drop');
						});
					}
				};

				var searchInput = getSearchInput();
				var events = $._data(searchInput[0], "events");

				if(!(events && events.keyup && events.keyup.some(function(ev){ return ev.handler.name === "searchEvent"; })))
				{
					searchInput.on('keyup', searchEvent);
				}

				$('#' + chosenId).find('ul.chosen-results').on('click touchend', function()
				{
					naja.makeRequest('GET', varUrlOnSelect, {
						selected: element.val(),
						formdata: form.serialize()
					});
				});
			}

			if(typeof varUrlOnChange !== 'undefined')
			{
				element.on('chosen:no_results', function()
				{
					$('#' + chosenId).find('li.no-results').html('<span class="color-black"><i class="fal fa-spinner fa-spin"></i>&nbsp;&nbsp;Načítají se položky</span>');
				});

				var runWhisper = function(searchInput)
				{
					clearTimeout(typingTimer);

					typingTimer = setTimeout(function()
					{
						var param = searchInput.val();
						if(param.length < 1)
						{
							return;
						}

						if(currentAbortController)
						{
							currentAbortController.abort();
						}
						currentAbortController = new AbortController();

						var parents = element.data('dependentselectbox-parents');
						var parentArray = {};

						$.each(parents || {}, function(name, id)
						{
							var parentElement = $('#' + id);
							if(parentElement.length > 0)
							{
								var val;
								if(parentElement.prop('type') === 'checkbox')
								{
									val = parentElement.prop('checked') ? 1 : 0;
								}
								else
								{
									val = parentElement.val();
								}
								parentArray[name] = val;
							}
							else if($("[id^='" + id + "']").length > 0)
							{
								parentArray[name] = $("[id^='" + id + "']:checked").val();
							}
						});

						naja.makeRequest('POST', varUrlOnChange,
							{param: param, parent: parentArray},
							{
								dataType: "json",
								signal: currentAbortController.signal
							}
						).then(function(response)
						{
							if(searchInput.val() !== param)
							{
								return;
							}

							var empty = true;
							element.empty();

							$.each(response.suggestions || [], function(index, el)
							{
								if(el.data !== "")
								{
									empty = false;
								}
								element.append($('<option>', {value: el.data, text: el.value}));
							});

							element.trigger("chosen:updated");

							var refreshedContainer = $('#' + chosenId);
							refreshedContainer.addClass('chosen-with-drop chosen-container-active');

							var refreshedInput = refreshedContainer.find('input.chosen-search-input');
							refreshedInput.val(param);
							refreshedInput.focus();

							if(empty === true)
							{
								var editedId = chosenId.replaceAll('_', '-');
								var lastDashIndex = editedId.lastIndexOf('-');
								var resultId = editedId.substring(0, lastDashIndex);

								var message = $('#' + resultId).attr('no-result-message')
									?? 'Nebyla nalezena žádná položka - ' + param;

								refreshedContainer.find('ul.chosen-results').append($('<li class="no-results">').text(message));
							}
						}).catch(function(err)
						{
							if(err.name !== 'AbortError')
							{
								console.error(err);
							}
						});
					}, delay);
				};

				$(document).on('keydown', '#' + chosenId + ' input.chosen-search-input', function(event)
				{
					var code = (event.keyCode || event.which);

					if(jQuery.inArray(code, disallowedKeyArray) === -1)
					{
						runWhisper($(this));
					}
				});

				$(document).on('paste input', '#' + chosenId + ' input.chosen-search-input', function()
				{
					runWhisper($(this));
				});
			}
		});
		return this;
	};
})(jQuery);