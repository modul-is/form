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

				/**
				 * Never send the signal without a value - the callback would receive an empty id
				 */
				var sendOnSelect = function()
				{
					var selected = element.val();

					if(!selected)
					{
						return null;
					}

					return naja.makeRequest('GET', varUrlOnSelect, {
						selected: selected,
						formdata: form.serialize()
					});
				};

				var searchEvent = function(e)
				{
					if((e.keyCode || e.which) === 13)
					{
						var request = sendOnSelect();

						if(request)
						{
							request.then(function()
							{
								$('#' + chosenId).removeClass('chosen-with-drop');
							});
						}
					}
				};

				/**
				 * Event namespace keeps the binding idempotent - the previous check relied on
				 * the handler function name, which minifiers mangle, so handlers piled up
				 */
				getSearchInput()
					.off('keyup.whisperer')
					.on('keyup.whisperer', searchEvent);

				/**
				 * Delegate to the result item - a click on the list padding or on the
				 * "no results" row must not trigger the signal
				 */
				$('#' + chosenId).find('ul.chosen-results')
					.off('click.whisperer touchend.whisperer')
					.on('click.whisperer touchend.whisperer', 'li.active-result', function()
					{
						sendOnSelect();
					});
			}

			if(typeof varUrlOnChange !== 'undefined')
			{
				element.on('change', function()
				{
					if(!element.val())
					{
						element.empty();
						element.append($('<option>', {value: '', text: ''}));
						element.trigger('chosen:updated');

						if(typeof varUrlOnSelect !== 'undefined')
						{
							var form = element.closest('form');
							naja.makeRequest('GET', varUrlOnSelect, {
								selected: '',
								formdata: form.serialize()
							});
						}
					}
				});

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
								element.append($('<option>', {value: '', text: ''}));

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

				$(document).off('keydown.whisperer.' + chosenId);
				$(document).on('keydown.whisperer.' + chosenId, '#' + chosenId + ' input.chosen-search-input', function(event)
				{
					var searchInput = $(this);
					var code = (event.keyCode || event.which);

					if(jQuery.inArray(code, disallowedKeyArray) === -1)
					{
						runWhisper(searchInput);
					}
				});

				$(document).off('paste.whisperer.' + chosenId);
				$(document).on('paste.whisperer.' + chosenId, '#' + chosenId + ' input.chosen-search-input', function()
				{
					var searchInput = $(this);

					setTimeout(function()
					{
						runWhisper(searchInput);
					}, 0);
				});
			}
		});
		return this;
	};
})(jQuery);
