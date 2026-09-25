/**
 * DependentSelectBox
 * @author Dusan Hudak <admin@dusan-hudak.com>
 */

(function ($)
{
    $.fn.dependentSelectBox = function(options, listener)
	{
        let callback = function () {};

        if(typeof(options) === 'function')
		{
            callback = options;
            options = null;
        }

        if(typeof(listener) === 'function')
		{
            callback = listener;
        }

        let dsb = this;
        dsb.timeout = [];
        dsb.settings = $.extend({
            suggestTimeout: 350,
            dataLinkName: 'dependentselectbox',
            dataParentsName: 'dependentselectboxParents'
        }, options);


        /**
         * Collect parent values - sent as request data, naja takes care of URL encoding
         * @param element
         * @returns {object}
         */
        this.getParentValues = function(element)
		{
            let parents = element.data(dsb.settings.dataParentsName);
            let values = {};

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

                    values[name] = val;
                }
                else if($("[id^='" + id + "-']").length > 0)
                {
                    values[name] = $("[id^='" + id + "-']:checked").val();
                }
            });

            return values;
        };


        /**
         * Create option from prepared item (DependentData::getPreparedItems)
         */
        this.createOption = function(item, selectedValue)
		{
            let option = $('<option>')
                .attr('value', item.key).text(item.value);

            if('attributes' in item)
			{
                $.each(item.attributes, function(attr, attrValue)
				{
                    option.attr(attr, attrValue);
                });
            }

            if(selectedValue !== null && item.key == selectedValue)
			{
                option.attr('selected', true);
            }

            return option;
        };


        /**
         * process
         * @param e
         * @param parentElement
         */
        this.process = function(e, parentElement, dependentSelect)
		{
            let signalLink = dependentSelect.data(dsb.settings.dataLinkName);

            if(signalLink === undefined)
			{
                return false;
            }

            // own unique key - naja would otherwise abort a pending request when one parent has more dependent selects
            naja.makeRequest('GET', signalLink, dsb.getParentValues(dependentSelect), {history: false, unique: 'dependentSelect-' + dependentSelect.attr('id')})
                .then(function(payload)
				{
                    let data = payload.dependentselectbox;

                    if(data !== undefined)
					{
                        let $select = $('#' + data.id);
                        $select.empty();

                        // multiselect has no prompt - an empty option would be a selectable item
                        if(data.prompt != false && !$select.prop('multiple'))
						{
                            $('<option>')
                                .attr('value', '').text(data.prompt)
                                .appendTo($select);
                        }

                        if(Object.keys(data.items).length > 0)
						{
                            if(data.disabledWhenEmpty)
							{
                                $select.prop('disabled', false);
                            }

                            $.each(data.items, function (i, item) {

                                if(typeof item.value === 'object')
								{
                                    let otpGroup = $('<optgroup>')
                                        .attr('label', item.key);

                                    // group value is a map of prepared items, not of plain labels
                                    $.each(item.value, function(objI, objItem)
									{
                                        otpGroup.append(dsb.createOption(objItem, data.value));
                                    });

                                    otpGroup.appendTo($select);
                                }
                                else
								{
                                    dsb.createOption(item, data.value).appendTo($select);
                                }
                            });
                        }
						else
						{
                            if(data.disabledWhenEmpty)
							{
                                $select.prop('disabled', true);
                            }
                        }

                        $select.trigger("chosen:updated");

                        // chained dependents listen to change of their parent - whisperer clears itself on change, so it is skipped
                        if(!$select.is('[data-whisperer]'))
						{
                            $select.trigger('change');
                        }
                    }
                })
                .catch(function(error)
				{
                    console.error(error);
                })
                .finally(callback);
        };


        /**
         * Event onChange
         * @param e
         * @param parentElement
         * @returns {boolean}
         */
        this.onChange = function(e, parentElement, dependentSelect)
		{
            dsb.process(e, parentElement, dependentSelect);
        };


        /**
         * Event onKeyup
         * @param e
         * @param parentElement
         * @returns {boolean}
         */
        this.onKeyup = function(e, parentElement, dependentSelect)
		{
            // reset timeout
            let timeoutKey = dependentSelect.attr('id');

            if(dsb.timeout[timeoutKey] != undefined && dsb.timeout[timeoutKey] != false)
			{
                clearTimeout(dsb.timeout[timeoutKey]);
            }

            dsb.timeout[timeoutKey] = setTimeout(function()
			{
                dsb.process(e, parentElement, dependentSelect);
            }, dsb.settings.suggestTimeout);
        };

        /**
         * Process
         */
        return this.each(function()
		{
            let $dependentSelect = $(this);
            let parents = $($dependentSelect).data(dsb.settings.dataParentsName);

            // namespace per dependent select - keeps rebinding after snippet redraw idempotent
            let ns = '.dependentSelect_' + $dependentSelect.attr('id');

            $.each(parents, function(name, id)
			{
                let parentElement = $('#' + id);

                if(parentElement.length > 0)
				{
                    if(parentElement.prop('type') === 'text' || parentElement.prop('nodeName').toLowerCase() === 'textarea')
					{
                        $(parentElement).off('keyup' + ns).on('keyup' + ns, function(e)
						{
                            dsb.onKeyup(e, $(this), $dependentSelect);
                        });
                    }
					else
					{
                        $(parentElement).off('change' + ns).on('change' + ns, function (e)
						{
                            dsb.onChange(e, $(this), $dependentSelect);
                        });
                    }
                }
                else
                {
                    $("[id^='" + id + "-']").off('change' + ns).on('change' + ns, function(e)
					{
                        dsb.onChange(e, $(this), $dependentSelect);
                    });
                }
            });
        });
    };
})(jQuery);