jQuery(document).ready(function($) {

    $('.hard-coded-list .trigger-sort').removeClass('btn btn-default');

    var fields_panel_width = $('.hard-coded-list .panel:first-child').width();
    var settings_panel_width = $('.form-meta-setting .panel:first-child').width();


    $(".hard-coded-list .panel").draggable({
        connectToSortable : ".form-meta-setting",
        helper : "clone",
        start : function(event, ui) {
                ui.helper.css('max-width', fields_panel_width);
             },
        revert : "invalid",
        stop : function(event, ui) {
            $('.form-meta-setting').find('.panel').removeClass('ui-draggable ui-draggable-handle').css({
                width: 'auto',
                height: 'auto'
            });
            ui.helper.find('.trigger-sort').addClass('btn btn-default');
            ui.helper.find('.glyphicon-menu-down').addClass('glyphicon-menu-up').removeClass('glyphicon-menu-down');
            setTimeout(function() {
                ui.helper.find('.inside-contents').show();
            }, 500);
        }
    });

    $(".form-meta-setting")
    .sortable({
        axis: "y",
        revert : true,
        handle: ".trigger-sort",
        placeholder: "ui-state-highlight",
        start: function( event, ui ){
            ui.helper.css('max-width', settings_panel_width);
        },
        stop: function( event, ui ) {
            ui.item.children( ".panel-heading" ).triggerHandler( "focusout" );
        }
    });

    $('.form-meta-setting').on('click', '.trigger-toggle', function(event) {
        event.preventDefault();
        var toggle_btn = $(this);
        if (toggle_btn.find('span').hasClass('glyphicon-menu-down')) {
            toggle_btn.find('span').removeClass('glyphicon-menu-down');
            toggle_btn.find('span').addClass('glyphicon-menu-up');
            $(this).closest('.panel').find('.inside-contents').show();
        } else {
            toggle_btn.find('span').removeClass('glyphicon-menu-up');
            toggle_btn.find('span').addClass('glyphicon-menu-down');
            $(this).closest('.panel').find('.inside-contents').hide();
        }
    });

    $('.form-meta-setting').on('click', '.remove-field', function(event) {
        event.preventDefault();
        var field_title = $(this).closest('.panel-heading').find('b').text().replace(' - ', '');
        swal({
          title: "Delete "+field_title+" field?",
          text: "Once deleted, you will not be able to recover this field!",
          icon: "warning",
          buttons: true,
          dangerMode: false,
        })
        .then((willDelete) => {
          if (willDelete) {
            $(this).closest('.panel').remove();
          }
        });
    });
    
    $('body').on('click', '.rem-save-settings',function(e) {
        e.preventDefault();
        swal('Please Wait', 'Saving settings...', 'info');
        var ListData = [];
        $('.form-meta-setting .panel').each(function(index, el) {
            var dataType = $(this).data('type');
            var wrap_panel = $(this);

            if(dataType == 'select' || dataType == 'select2' || dataType == 'checkboxes' ){

                var singleField = {
                    key: wrap_panel.find('.field_key').val(),
                    type: dataType,
                    tab: wrap_panel.find('.field_tab').val(),
                    default: wrap_panel.find('.field_default').val(),
                    title: wrap_panel.find('.field_title').val(),
                    options: wrap_panel.find('.field_options').val(),
                    help: wrap_panel.find('.field_help').val(),
                    editable: wrap_panel.find('.field_editable').val(),
                    accessibility: wrap_panel.find('.field_accessibility').val(),
                    required: wrap_panel.find('.field_required').is(':checked') ? true : false,
                };

                ListData.push(singleField);

            } else if(dataType == 'upload') {
                var singleField = {
                    key: wrap_panel.find('.field_key').val(),
                    type: dataType,
                    tab: wrap_panel.find('.field_tab').val(),
                    default: wrap_panel.find('.field_default').val(),
                    title: wrap_panel.find('.field_title').val(),
                    max_files: wrap_panel.find('.field_max_files').val(),
                    max_files_msg: wrap_panel.find('.field_max_files_msg').val(),
                    file_type: wrap_panel.find('.field_file_type').val(),
                    help: wrap_panel.find('.field_help').val(),
                    accessibility: wrap_panel.find('.field_accessibility').val(),
                    required: wrap_panel.find('.field_required').is(':checked') ? true : false,
                    display_as: wrap_panel.find('.field_display_as').val(),
                    cols: wrap_panel.find('.field_cols').val(),
                };

                ListData.push(singleField);
            } else {
                var singleField = {
                    key: wrap_panel.find('.field_key').val(),
                    type: dataType,
                    tab: wrap_panel.find('.field_tab').val(),
                    default: wrap_panel.find('.field_default').val(),
                    title: wrap_panel.find('.field_title').val(),
                    help: wrap_panel.find('.field_help').val(),
                    editable: wrap_panel.find('.field_editable').val(),
                    max_value: wrap_panel.find('.field_max_value').val(),
                    min_value: wrap_panel.find('.field_min_value').val(),
                    accessibility: wrap_panel.find('.field_accessibility').val(),
                    required: wrap_panel.find('.field_required').is(':checked') ? true : false,
                    range_slider : wrap_panel.find('.field_range_slider').val(),
                    any_value_on_slider : wrap_panel.find('.field_any_value_on_slider').is(':checked') ? true : false,
                };

                ListData.push(singleField);
            }

        });
        var data = {
            action: 'wcp_rem_save_custom_fields',
            fields: ListData,
            nonce: rem_fields_var.nonce,
        }
        $.post(ajaxurl, data, function(resp) {
            swal(resp.title, resp.message, resp.status);
        }, 'json');
    });

    $('body').on('click', '.rem-reset-settings',function(e) {
        event.preventDefault();
        swal({
          title: "Are you sure?",
          text: "Once reset, you will not be able to recover custom fields!",
          icon: "warning",
          buttons: true,
          dangerMode: false,
        })
        .then((willDelete) => {
          if (willDelete) {
            var data = {
                action: 'wcp_rem_reset_custom_fields',
                nonce: rem_fields_var.nonce,
                reset: 'yes'
            }
            $.post(ajaxurl, data, function(resp) {
                swal("Reset is Done!", {
                  icon: "success",
                });
                window.location.reload();
            });
          }
        });
    });

    $('.form-meta-setting').on('keyup', 'input.field_title', function() {
        var input_val = $(this).val();
        var parent = $(this).closest('.panel');
        parent.find('.panel-heading b').text(input_val+' - ');
    });

    $('.form-meta-setting').on('blur', 'input.field_title', function() {
        if ($(this).closest('.inside-contents').find('.field_key').val() == '') {
            var data_name = $(this).val().replace(/[^a-z0-9\s]/gi, '').replace(/[-\s]/g, '_');
            $(this).closest('.inside-contents').find('.field_key').val(data_name.toLowerCase());
        }
    });
});