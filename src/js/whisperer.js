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
                //Run onselect after pressing enter
                searchInput.on('keyup', function(event)
                {
                    if((event.keyCode || event.which) === 13)
                    {
                        naja.makeRequest('GET', varUrlOnSelect, {selected: element.val()});
                    }
                });

                var resultField = $('#' + chosenId).find('ul.chosen-results');

                resultField.on('click touchend', function()
                {
                    naja.makeRequest('GET', varUrlOnSelect, {selected: element.val()});
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

                            naja.makeRequest('POST', varUrlOnChange, {param: param}, {dataType: "json"}).then((response) =>
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
                                    $('#' + chosenId).find('ul.chosen-results').append('<li class="no-results">Nebyla nalezena žádná položka - ' + param + '</li>');
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