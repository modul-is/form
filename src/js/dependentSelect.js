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
         * Get link to signal
         * @param element
         * @returns {*}
         */
        this.getSignalLink = function(element)
		{
            let signalLink = element.data(dsb.settings.dataLinkName);
            let parents = element.data(dsb.settings.dataParentsName);

            if(signalLink === undefined)
			{
                return false;
            }

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

                    signalLink = signalLink + '&' + name + '=' + val;
                }
                else if($("[id^='" +id + "']").length > 0)
                {
                    signalLink = signalLink + '&' + name + '=' + $("[id^='" +id + "']:checked").val();
                }
            });

            return signalLink;
        };


        /**
         * process
         * @param e
         * @param parentElement
         */
        this.process = function(e, parentElement, dependentSelect)
		{
            // Validate if signalLink exist
            var signalLink = dsb.getSignalLink(dependentSelect);

            if(signalLink == false)
			{
                return false;
            }

            // Send ajax request
            $.ajax(signalLink, {
                async: false,
                success: function(payload)
				{
                    let data = payload.dependentselectbox;

                    if(data !== undefined)
					{
                        let $select = $('#' + data.id);
                        $select.empty();

                        if(data.prompt != false)
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

                                    $.each(item.value, function(objI, objItem)
									{
                                        let option = $('<option>').attr('value', objI).text(objItem);

                                        if(data.value !== null && objI == data.value)
										{
                                            option.attr('selected', true);
                                        }

                                        otpGroup.append(option);
                                    });

                                    otpGroup.appendTo($select);
                                }
                                else
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

                                    if(data.value !== null && item.key == data.value)
									{
                                        option.attr('selected', true);
                                    }

                                    option.appendTo($select);
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
                    }
                },
                complete: callback
            });
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

            $.each(parents, function(name, id)
			{
                let parentElement = $('#' + id);

                if(parentElement.length > 0)
				{
                    if(parentElement.prop('type') === 'text' || parentElement.prop('nodeName').toLowerCase() === 'textarea')
					{
                        $(parentElement).on("keyup", function(e)
						{
                            dsb.onKeyup(e, $(this), $dependentSelect);
                        });
                    }
					else
					{
                        $(parentElement).on("change", function (e)
						{
                            dsb.onChange(e, $(this), $dependentSelect);
                        });
                    }
                }
                else
                {
                    $("[id^='" +id + "-']").on("change", function(e)
					{
                        dsb.onChange(e, $(this), $dependentSelect);
                    });
                }
            });
        });
    };
})(jQuery);