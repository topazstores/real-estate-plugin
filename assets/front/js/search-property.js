jQuery(document).ready(function($) {
	if (typeof $('.rem-select2-field').select2 === "function") { 
		$('.rem-select2-field').select2({placeholder: $(this).data('placeholder')});
	    $('.select2-container').width('100%');
	}
	$('.ich-settings-main-wrap .image-fill').each(function(index, el) {
		jQuery(this).imagefill();
	});

	if ($('.search-options .wcp-eq-height').length) {
		$('.search-options .wcp-eq-height > div').matchHeight({byRow: false});
	}

	var currentSortValue = 'date-desc';

	$('ul.page-numbers').addClass('pagination');
	$('.page-numbers.current').closest('li').addClass('active');
	
	$('.search-property-form').submit(function(event) {
		event.preventDefault();
		$(this).data('offset', parseInt(rem_ob.offset));
		var s_wrap = $(this).closest('.ich-settings-main-wrap');
		var results_cont = '';
		if ($(this).data('resselector') != '') {
			selectorTest = $(this).data('resselector');
			if ( selectorTest.indexOf('.') != 0 && selectorTest.indexOf('#') != 0 ){
				if ( $("." + selectorTest).length )
				{
					results_cont = $("." + selectorTest);
				} else if ( $("#" + selectorTest).length ) {
					results_cont = $("#" + selectorTest);
				}
			} else {
				if ( $(selectorTest).length ){
					results_cont = $(selectorTest);
				}
			}
		}

		if ( results_cont == '' || typeof results_cont === "undefined" ){
			results_cont = s_wrap.find('.searched-properties');
		}
		s_wrap.find('.searched-properties').html('');
		s_wrap.find('.loader').show();

	    var ajaxurl = $(this).data('ajaxurl');
	    var formData = $(this).serializeArray();

		 
	    function arrayClean(thisArray, thisName) {
	    	
		    $.each(thisArray, function(index, item) {
		        if (item != undefined ) {

			        if (item.name == thisName) {
			            formData.splice( index, 1);      
			        }
		        };
		    });
		}
	    var fields = $(".rem-field-hide select").map(function() {
        	return $(this).attr('name');
       	}).get();
	    $.each( fields, function(index, key){
	    	arrayClean( formData, key);
	    });

	    $.post(ajaxurl, formData, function(resp) {
			s_wrap.find('.loader').hide();
	    	results_cont.html(resp);
	    	if (s_wrap.data('autoscroll') == 'enable') {
			    $('html, body').animate({
			        scrollTop: results_cont.offset().top
			    }, 2000);
	    	}
			$('.ich-settings-main-wrap .image-fill').each(function(index, el) {
				jQuery(this).imagefill();
			});
			
			if ($('.search-property-form').data('offset') == '-1') {
				$('.rem-load-more-wrap').hide();
			}
			if ($('.masonry-enabled').length) {
				$('.searched-properties').imagesLoaded( function() {
					$('.searched-properties > .row').masonry();
				});
			}
			$('ul.page-numbers').addClass('pagination');
			
			$('.page-numbers.current').closest('li').addClass('active');

			if ($('.searched-properties .rem-results-sort').length) {
				$('.searched-properties .rem-results-sort').val(currentSortValue);
			}
	    });
	}); 

	if ($('.search-property-form').length > 0 && $('.search-property-form').data('offset') != '-1') {
		
		$('body').on('click', '.rem-load-more-wrap a.page-numbers', function(event) {
			event.preventDefault();
			var paged = $(this).text();
			if($(this).hasClass('next')){
				paged = parseInt($(this).closest('.pagination').find('.current').text()) + 1;
			}
			if($(this).hasClass('prev')){
				paged = parseInt($(this).closest('.pagination').find('.current').text()) - 1;
			}

			var form = $('.search-property-form');
			var s_wrap = $(form).closest('.ich-settings-main-wrap');
			var results_cont = '';
			if ($(form).data('resselector') != '') {
				selectorTest = $(form).data('resselector');
				if ( selectorTest.indexOf('.') != 0 && selectorTest.indexOf('#') != 0 ){
					if ( $("." + selectorTest).length )
					{
						results_cont = $("." + selectorTest);
					} else if ( $("#" + selectorTest).length ) {
						results_cont = $("#" + selectorTest);
					}
				} else {
					if ( $(selectorTest).length ){
						results_cont = $(selectorTest);
					}
				}
			}

			if ( results_cont == '' || typeof results_cont === "undefined" ){
				results_cont = s_wrap.find('.searched-properties');
			}

			results_cont.addClass('rem-loading-ajax');

		    var ajaxurl = $(form).data('ajaxurl');
		    var formData = $(form).serializeArray();

			 
		    function arrayClean(thisArray, thisName) {
		    	
			    $.each(thisArray, function(index, item) {
			        if (item != undefined ) {

				        if (item.name == thisName) {
				            formData.splice( index, 1);      
				        }
			        };
			    });
			}
		    var fields = $(".rem-field-hide select").map(function() {
	        	return $(form).attr('name');
	       	}).get();
	       	
		    $.each( fields, function(index, key){
		    	arrayClean( formData, key);
		    });
		    var paged_obj = {
		    	name : 'paged',
		    	value : parseInt(paged)
		    }
		    formData.push(paged_obj);
		    
		    $.post(ajaxurl, formData, function(resp) {
		    	results_cont.html(resp);
				$('.ich-settings-main-wrap .image-fill').each(function(index, el) {
					jQuery(this).imagefill();
				});

				setTimeout(function() {
					if ($('.masonry-enabled').length) {
						$('.searched-properties').imagesLoaded( function() {
							$('.searched-properties > .row').masonry();
						});
					}
				}, 500);

				$('ul.page-numbers').addClass('pagination');
				$('.page-numbers.current').closest('li').addClass('active');
				
				if ($('.searched-properties .rem-results-sort').length) {
					$('.searched-properties .rem-results-sort').val(currentSortValue);
				}
				results_cont.removeClass('rem-loading-ajax');
		    });
		});
	}

	if (jQuery('.labelauty-unchecked-image').length == 0 && jQuery(".labelauty").length) {
		jQuery(".labelauty").labelauty();
	}

	var $filter = jQuery('.filter', '#rem-search-box');

	jQuery('.botton-options', '#rem-search-box').on('click', function(){
		hideSearcher();
	});

	function hideSearcher(navigatorMap){

		if(navigatorMap==true){
			$searcher.slideUp(500);
		} else {
			$searcher.slideToggle(500);
		}
		return false;
	}

	jQuery(".set-searcher", '#rem-search-box').on('click', hideSearcher);

	jQuery(".more-button", '#rem-search-box').on('click', function(){
		$filter.toggleClass('hide-filter');
		return false;
	});

	if ($('.search-container.auto-complete').length > 0) {
	    $('.search-container.auto-complete input[type=text]').autocomplete({
	        source: function (request, response) {
	            var element_id = $(this.element).attr('id');
	            var ajax_url = $('#search-property').data('ajaxurl');

	            // Collect hidden field data
	            var extraData = {};
	            $('.rem-fixed-search-field').each(function () {
	                var name = 'rem_' + $(this).attr('name');
	                var value = $(this).val();
	                if (name && value) {
	                    extraData[name] = value;
	                }
	            });

	            // Merge main data with extra data
	            var data = $.extend({
	                action: 'rem_search_autocomplete',
	                field: element_id,
	                search: request.term
	            }, extraData);

	            $.post(ajax_url, data, function(resp) {
	                if (resp != '') {
	                    response(JSON.parse(resp));	
	                }
	            });
	        }
	    });
	}

	if ($('.rem-widget-search.auto-complete').length > 0) {
		$('.rem-widget-search.auto-complete input[type=text]').autocomplete({
		    source: function (request, response) {
		    	var element_id = $(this.element).attr('id');
		    	var ajax_url = $('.rem-ajax-url').val();
		    	var data = {
		    		action: 'rem_search_autocomplete',
		    		field: element_id,
		    		search: request.term
		    	}
		    	$.post(ajax_url, data, function(resp) {
		    		if (resp != '') {
		    			response( JSON.parse(resp) );	
		    		}
		    	});
		    }
		});		
	}
	$('button[type="reset"]').on('click', function(event) {
		if ($('.rem-niceselect').length) {
		  setTimeout(function() {
		  	$('.rem-niceselect').niceSelect('update');
		  }, 100);
		}		
		$('.p-slide-wrap').each(function(index, el) {
			$(this).find('.price-range').val([parseInt(rem_ob.price_min_default), parseInt(rem_ob.price_max_default) ]);
		});

		$('.slider-range-input').each(function(index, el) {
			$(this).find('.price-range').val([ parseInt($(this).data('default_min')), parseInt($(this).data('default_max')) ]);
		});

		if (typeof $('.rem-select2-field').select2 === "function") { 
			jQuery('.rem-select2-field').val([]).change();
		}
	});

	rerender_price_ranges = function(element, type = '', initial = false){
		if(!initial){
			element.get(0).noUiSlider.destroy();
		}

		var formatter = wNumb({
		    decimals: parseInt(rem_ob.decimal_points),
		    mark: rem_ob.decimal_separator,
		    thousand: rem_ob.thousand_separator,
		});

		var dropDownOptions = rem_ob.price_dropdown_options.trim();
		var rangeObj = {};

		// Minimum and maximum values from rem_ob settings
		var minValue = parseInt(rem_ob['price_min' + type]);
		var maxValue = parseInt(rem_ob['price_max' + type]);

		if (dropDownOptions == '') {
		    // If empty, use only min and max
		    rangeObj['min'] = [minValue];
		    rangeObj['max'] = [maxValue];
		} else {
		    // Split the textarea input by line if it's not empty
		    var dataLines = dropDownOptions.split("\n");

		    rangeObj['min'] = [minValue];

		    // Calculate percentage interval based on the number of entries
		    var percentageStep = 100 / (dataLines.length); // +1 for 'max'

		    dataLines.forEach(function(line, index) {
		        var parts = line.split('-');
		        var rangeValue = parseInt(parts[0]);    // The range value
		        var stepValue = parseInt(parts[1]);     // The step increment

		        // Calculate percentage for this step
		        var percentage = Math.round((index + 1) * percentageStep);

		        // Add to range object with percentage and step value
		        rangeObj[percentage + '%'] = [rangeValue, stepValue];
		    });

		    rangeObj['max'] = [maxValue];
		}

		noUiSlider.create(element.get(0), {
			start: [ parseInt(rem_ob['price_min_default'+type]), parseInt(rem_ob['price_max_default'+type]) ],
			behaviour: 'drag',
			direction: rem_ob.site_direction,
			connect: true,
			step: parseInt(rem_ob['price_step'+type]),
			range: rangeObj,
			format: formatter,
		});

		var wrap = element.closest('.p-slide-wrap');

        element.get(0).noUiSlider.on("update", function (values, handle) {
            var minValue = wrap.find('#price-value-min');
            var maxValue = wrap.find('#price-value-max');
            var minInput = wrap.find('#min-value');
            var maxInput = wrap.find('#max-value');
            
            if (handle === 0) {  // Handle for the lower slider
                minValue.text(values[0]);
                minInput.val(formatter.from(values[0]));
            } else {  // Handle for the upper slider
                maxValue.text(values[1]);
                maxInput.val(formatter.from(values[1]));
            }
        });
	}

	$('.p-slide-wrap').each(function(index, el) {
		if ($(this).find('.price-range').length) {
			rerender_price_ranges($(this).find('.price-range'), '', true);
		}
	});

	$('.rem-search-form-wrap').on('change', '[name='+rem_ob.price_range_name+']', function(event) {
		event.preventDefault();
		if($(this).closest('.rem-search-form-wrap').find('.price-range').length){
			if ($(this).val() == rem_ob.price_range_value) {
				rerender_price_ranges($(this).closest('.rem-search-form-wrap').find('.price-range'), '_r');
			} else {
				rerender_price_ranges($(this).closest('.rem-search-form-wrap').find('.price-range'));
			}
		}
	});

	$('.slider-range-input').each(function(index, element) {

		var formatter = wNumb({
		    decimals: parseInt(rem_ob.range_decimal_points),
		    mark: rem_ob.range_decimal_separator,
		    thousand: rem_ob.range_thousand_separator,
		});

		noUiSlider.create(element, {
			start: [ parseInt($(this).data('default_min')), parseInt($(this).data('default_max')) ],
			behaviour: 'drag',
			direction: rem_ob.site_direction,
			connect: true,
			step: 1,
			range: {
			    'min': parseInt($(this).data('min')),
			    'max': parseInt($(this).data('max'))
			},
			format: formatter,
		});

		var wrap = $(this).closest('.p-slide-wrap');
        element.noUiSlider.on("update", function (values, handle) {
            var minValue = wrap.find('.price-value-min');
            var maxValue = wrap.find('.price-value-max');
            var minInput = wrap.find('.min-value');
            var maxInput = wrap.find('.max-value');
            
            if (handle === 0) {  // Handle for the lower slider
                minValue.text(values[0]);
                minInput.val(formatter.from(values[0]));
            } else {  // Handle for the upper slider
                maxValue.text(values[1]);
                maxInput.val(formatter.from(values[1]));
            }
        });
	});


	$('.price-slider').on('change', '.any-check', function(event) {
		event.preventDefault();
		var rangeWrapper = $(this).closest('.p-slide-wrap');
		if ($(this).find('input').is(":checked")){
			rangeWrapper.find('.noUi-base, span').css({
				'opacity': '0.5',
				'pointer-events': 'none'
			});
			rangeWrapper.find('.min-value').val('');
			rangeWrapper.find('.max-value').val('');
		} else {
			rangeWrapper.find('.noUi-base, span').css({
				'opacity': '1',
				'pointer-events': 'inherit'
			});
			var min_val = rangeWrapper.find('.price-value-min').text();
			var max_val = rangeWrapper.find('.price-value-max').text();
			rangeWrapper.find('.min-value').val(min_val);
			rangeWrapper.find('.max-value').val(max_val);
		}
	});
	
	$('.price-slider .any-check').each(function(index, checkbox) {
		var rangeWrapper = $(this).closest('.p-slide-wrap');
		if ($(checkbox).find('input').is(':checked')) {
			rangeWrapper.find('.noUi-base, span').css({
				'opacity': '0.5',
				'pointer-events': 'none'
			});
			rangeWrapper.find('.min-value').val('');
			rangeWrapper.find('.max-value').val('');
		};
	});

	// price dropdown 
	$('body').on('change', '.rem_price_dropdown', function(event) {
		event.preventDefault();
		/* Act on the event */
		
		var optionSelected = $("option:selected", this);
		var min = optionSelected.data('min');
		var max = optionSelected.data('max');
		var menuWrap = $(this).closest('.p-slide-wrap');
		
		menuWrap.find('#min-value').val(min);
		menuWrap.find('#max-value').val(max);
	});

	$('.rem_ajax_search_property_form').change(function() {
		var ajax_url = $('.rem-ajax-url').val();
		var formData = $(this).serializeArray();
		var result_area_id = $(this).data('result_area_id');
		$(result_area_id).css({
			'opacity': '.5'
		});
		
		$.post(ajax_url, formData, function(resp) {
			$(result_area_id).css({
				'opacity': '1'
			});
	    	$(result_area_id).html(resp);
			    $('html, body').animate({
			        scrollTop: $(result_area_id).offset().top
			    }, 2000);
			$('.ich-settings-main-wrap .image-fill').each(function(index, el) {
				jQuery(this).imagefill();
			});
			
			if ($('.masonry-enabled').length) {
				$('.searched-properties').imagesLoaded( function() {
					$('.searched-properties > .row').masonry();
				});
			}
			$(result_area_id).find('.rem-load-more-wrap').hide();
	    });
	});

    if ($('.rem-states-list').length && $('.rem-countries-list').length) {
        $('#rem-search-box').on('change', '.rem-countries-list', function(event) {
            var currentTab = $(this).closest('.row');
            var ajax_url = $('#search-property').data('ajaxurl');
            event.preventDefault();
            var data = {
                action: 'rem_get_states',
                country: $(this).val(),
                nonce: rem_ob.nonce_states
            }
            $.post(ajax_url, data, function(resp) {
                currentTab.find('.rem-states-list').html(resp);
                var state = currentTab.find('.rem-states-list').data('state');
                currentTab.find('.rem-states-list').val(state);
            });
        });
        if ($('.rem-countries-list').val() != '') {
	        $('.rem-countries-list').trigger('change');
	    }
    }

    // If there is only states field
    if ($('.rem-states-list').length && !$('.rem-countries-list').length) {
        var ajax_url = $('#search-property').data('ajaxurl');
        var data = {
            action: 'rem_get_states',
            country: 'US',
            nonce: rem_ob.nonce_states
        }
        $.post(ajax_url, data, function(resp) {
            $('#search-property').find('.rem-states-list').html(resp);
        });
    }

    // sorting ajax results
    $('.searched-properties').on('change', '.rem-results-sort', function() {
    	currentSortValue = $(this).val();
        var selectedValue = $(this).val().split('-');
        var order = selectedValue[1];
        var orderby = selectedValue[0];

        var searchForm = $('#search-property');

        // Check if the form has fields with name 'order' and 'orderby'
        var orderField = searchForm.find('[name="order"]');
        var orderbyField = searchForm.find('[name="orderby"]');

        // Set or create the 'order' field
        if (orderField.length > 0) {
            orderField.val(order.toUpperCase());
        } else {
            searchForm.append('<input type="hidden" name="order" value="' + order.toUpperCase() + '">');
        }

        // Set or create the 'orderby' field
        if (orderbyField.length > 0) {
            orderbyField.val(orderby);
        } else {
            searchForm.append('<input type="hidden" name="orderby" value="' + orderby + '">');
        }

        searchForm.submit();
    });

    if ($('.rem-min-max-input').length && typeof rem_ob !== 'undefined') {
        var dropDownOptions = rem_ob.price_dropdown_options.trim().split('\n');

        var minSuggestions = [];
        var maxSuggestions = [];

        var formatter = wNumb({
            decimals: parseInt(rem_ob.decimal_points),
            mark: rem_ob.decimal_separator,
            thousand: rem_ob.thousand_separator,
        });

        dropDownOptions.forEach(function(item) {
            item = item.trim();
            if (!item) return;

            if (item.includes('-')) {
                var parts = item.split('-');
                var min = parseFloat(parts[0].trim());
                var max = parseFloat(parts[1].trim());
                if (!isNaN(min)) minSuggestions.push({ label: formatter.to(min), value: min });
                if (!isNaN(max)) maxSuggestions.push({ label: formatter.to(max), value: max });
            } else {
                var val = parseFloat(item);
                if (!isNaN(val)) {
                    minSuggestions.push({ label: formatter.to(val), value: val });
                    maxSuggestions.push({ label: formatter.to(val), value: val });
                }
            }
        });

        $(".rem-min-price").autocomplete({
            source: minSuggestions,
            minLength: 0,
            select: function(event, ui) {
                $(this).val(ui.item.value);
                return false;
            },
            focus: function(event, ui) {
                $(this).val(ui.item.label);
                return false;
            }
        }).on('focus', function() {
            $(this).autocomplete("search", "");
        });

        $(".rem-max-price").autocomplete({
            source: maxSuggestions,
            minLength: 0,
            select: function(event, ui) {
                $(this).val(ui.item.value);
                return false;
            },
            focus: function(event, ui) {
                $(this).val(ui.item.label);
                return false;
            }
        }).on('focus', function() {
            $(this).autocomplete("search", "");
        });
    }
});