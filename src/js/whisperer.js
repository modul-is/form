(function ($) {
    $.fn.whisperer = function()
    {
        var disallowedKeyArray = [13, 37, 38, 39, 40];
        this.each(function()
        {
            var element = $(this);

            var chosenId = (element.attr('id') + '_chosen').replace(/-/g, "_");
            var searchInput = $('#' + chosenId).find('input.chosen-search-input');
            var varUrlOnChange = element.data('whisperer');
            var varUrlOnSelect = element.data('whisperer-onselect');
            var delay = element.data('whisperer-delay');
            var typingTimer;

            if(typeof varUrlOnSelect !== 'undefined')
            {
				var form = element.closest('form');

                //Run onselect after pressing enter
				let searchEvent = function(e){
					if((e.keyCode || e.which) === 13)
                    {
                        naja.makeRequest('GET', varUrlOnSelect, {selected: element.val(), formdata: form.serialize()}).then((response) =>
						{
							$(this).closest('.chosen-with-drop').removeClass('chosen-with-drop');
						});
                    }
				};

				let events = $._data(searchInput[0], "events");

				if (events && events.keyup)
				{
					let hasEvent = events.keyup.some(function(event)
					{
						return event.handler.name === "searchEvent";
					});

					if(!hasEvent)
					{
						searchInput.on('keyup', searchEvent);
					}
				}

                var resultField = $('#' + chosenId).find('ul.chosen-results');

                resultField.on('click touchend', function()
                {
                    naja.makeRequest('GET', varUrlOnSelect, {selected: element.val(), formdata: form.serialize()});
                });
            }

            if(typeof varUrlOnChange !== 'undefined')
            {
                //loading
                element.on('chosen:no_results', function()
                {
                    $('#' + chosenId).find('li.no-results').html('<span class="color-black"><i class="fal fa-spinner fa-spin"></i>&nbsp;&nbsp;Načítají se položky</span>');
                });

                searchInput.keydown(function(event)
                {
                    var code = (event.keyCode || event.which);

                    if(jQuery.inArray(code, disallowedKeyArray) === -1)
                    {
                        clearTimeout(typingTimer);
                        typingTimer = setTimeout(function()
                        {
                            var param = searchInput.val();
							var parents = element.data('dependentselectbox-parents');
							var parentArray = {};

							$.each(parents, function (name, id)
							{
								var parentElement = $('#' + id);
								if (parentElement.length > 0)
								{
									var val;
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

                            naja.makeRequest('POST', varUrlOnChange, {param: param, parent: parentArray}, {dataType: "json"}).then((response) =>
                            {
                                var empty = true;
                                element.empty();

                                $.each(response.suggestions,function(index, el)
                                {
                                    if(el.data !== "")
                                    {
                                        empty = false;
                                    }

                                    element.append("<option value=" + el.data + ">" + el.value + "</option>");
                                });

                                element.trigger("chosen:updated");

								if(empty === true)
								{
									editedId = chosenId.replaceAll('_', '-');
									index = editedId.lastIndexOf('-');
									result = editedId.substring(0, index);

									message = $('#' + result).attr('no-result-message') ?? 'Nebyla nalezena žádná položka - ' + param;

									$('#' + chosenId).find('ul.chosen-results').append('<li class="no-results">' + message + '</li>');
								}
                                searchInput.val(param);
                            });
                        }, delay);
                    }
                });
            }
        });
    };
})(jQuery);