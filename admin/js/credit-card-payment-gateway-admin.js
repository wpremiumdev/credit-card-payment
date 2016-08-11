jQuery(function ($) {
    var button_value = jQuery('input[name=credit_card_payment_button_general_settings]:checked').val();
    pccg_set_custom_button_link(button_value);
    jQuery("input:radio[name=credit_card_payment_button_general_settings]").click(function () {
        jQuery(this).is(":checked");
        var value = jQuery(this).val();
        pccg_set_custom_button_link(value);
    });

    function pccg_set_custom_button_link(value) {
        if (typeof value === "undefined") {
            jQuery("input[type=radio][value='button5']").attr('checked', 'checked');
        }
        if ("button12" == value) {
            jQuery('.class_pccg_custom_button').show();
        } else {
            jQuery('.class_pccg_custom_button').hide();
        }
    }
    jQuery('#pccg_payment_enable_border').val('0');
    jQuery('#pccg_payment_enable_quantity').val('0');
    jQuery(document).on('change', '#pccg_payment_enable_border', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#pccg_payment_enable_border').val('1');
            jQuery('#pccg_payment_table_border').show();
        } else {
            jQuery('#pccg_payment_enable_border').val('0');
            jQuery('#pccg_payment_table_border').hide();
        }
    });
    jQuery(document).on('change', '#pccg_payment_enable_quantity', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('#pccg_payment_enable_quantity').val('1');
        } else {
            jQuery('#pccg_payment_enable_quantity').val('0');
        }
    });
    jQuery(document).on('click', '.pccg_popup_container_button', function () {
        jQuery('.pccg_popup_container').trigger('click');
        m7_resize_thickbox();
    });
    jQuery(document).on('change', '#PCCG_payment_tab_price_shortcode_price', function () {
        var image_url = jQuery('.PCCG_PAYMENT_SITE_URL').val();
        if ("1" == jQuery('#PCCG_payment_tab_price_shortcode_price').val()) {
            jQuery('.pccg-payment-div-option-create-price').html('');
            var string = '<table class="widefat" id="pccg_create_priceshortcode_1"><tr><td><input style="height: 38px;width: 100%;" type = "text" name = "os0" id = "os0" class = "pccg-payment-field-style" placeholder = "Value"></td></tr></table>';
            jQuery('.pccg-payment-div-option-create-price').append(string);
        } else if ("2" == jQuery('#PCCG_payment_tab_price_shortcode_price').val()) {
            jQuery('.pccg-payment-div-option-create-price').html('');
            var string = '<table style="box-shadow: inset 0 0 6px green;" id="pccg_payment_option_table" class="widefat"><tr><td colspan="2"><input style="height: 38px;width: 100%;" type = "text" name ="pccg_payment_lable" id = "pccg_payment_lable" class = "pccg-payment-field-style" placeholder = "Enter Lable Name"></td></tr><tr id="pccg_tr_0" data-tr="0"><td><input style="height: 38px;width: 90%;" type = "text" name = "on0" id = "on0" class = "pccg-payment-field-style" placeholder = "Key"></td><td><input style="height: 38px;width: 90%;" type = "text" name = "os0" id = "os0" class = "pccg-payment-field-style" placeholder = "Value"><span id="pccg-payment-add-icon" class="pccg-payment-add-remove-icon pccg-add-remove-icon-paypal"><img src="' + image_url + 'image/add.png"</span></td></tr></table>';
            jQuery('.pccg-payment-div-option-create-price').append(string);
        } else {
            jQuery('.pccg-payment-div-option-create-price').html('');
        }
    });
    jQuery(document).on('click', '#pccg-payment-add-icon', function () {
        var image_url = jQuery('.PCCG_PAYMENT_SITE_URL').val();
        var last_tr_id = jQuery('#pccg_payment_option_table tr:last').attr('data-tr');
        if (last_tr_id < 4)
        {
            var id = parseInt(last_tr_id) + 1;
            var str_row = '<tr id="pccg_tr_' + id + '" data-tr="' + id + '"><td><input style="height: 38px;width: 90%;" type = "text" name = "on' + id + '" id = "on' + id + '" class = "pccg-payment-field-style" placeholder = "Key"></td><td><input style="height: 38px;width: 90%;" type = "text" name = "os' + id + '" id = "os' + id + '" class = "pccg-payment-field-style" placeholder = "Value"><span id="pccg-payment-add-icon' + id + '" class="pccg-payment-add-remove-icon pccg-add-remove-icon-paypal" data-value="' + id + '"><img src="' + image_url + 'image/remove.png"</span></td></tr>';
            jQuery("#pccg_tr_" + last_tr_id).after(str_row);
        }
    });
    jQuery(document).on('click', '.pccg-payment-add-remove-icon', function () {
        var id = jQuery(this).attr("data-value");
        jQuery('#pccg_tr_' + id).remove();
        pccg_reset_name_with_id(id);
    });
    jQuery(document).on('click', '.pccg-payment-custom-add', function () {
        var image_url = jQuery('.PCCG_PAYMENT_SITE_URL').val();
        var table_current_id = jQuery(this).closest('table').attr('id')
        var table_data_custom_id = jQuery(this).closest('table').attr('data-custom')
        var last_tr_id = jQuery('#' + table_current_id + ' tr:last').attr('data-tr');
        if (last_tr_id < 4)
        {
            var id = parseInt(last_tr_id) + 1;
            var str_row = '<tr id="pccg-payment-table-option-' + id + '" data-tr="' + id + '"><td><input style="height: 38px;width: 90%;" type = "text" name = "on' + table_data_custom_id + id + '" id = "on' + table_data_custom_id + id + '" class = "pccg-payment-field-style" placeholder = "Key"></td><td><input style="height: 38px;width: 90%;" type = "text" name = "os' + table_data_custom_id + id + '" id = "os' + table_data_custom_id + id + '" class = "pccg-payment-field-style" placeholder = "Value"><span id="pccg-payment-remove-tr-' + id + '" class="pccg-payment-custom-remove pccg-add-remove-icon-paypal" data-value="' + id + '"><img src="' + image_url + 'image/remove.png"</span></td></tr>';
            jQuery("#" + table_current_id + " #pccg-payment-table-option-" + last_tr_id).after(str_row);

        }
    });
    jQuery(document).on('click', '.pccg-payment-custom-remove', function () {
        var id = jQuery(this).attr("data-value");
        var table_current_id = jQuery(this).closest('table').attr('id');
        var table_value = jQuery(this).closest('table').attr('data-custom')
        jQuery("#" + table_current_id + " #pccg-payment-table-option-" + id).remove();
        pccg_second_tab_reset_name_with_id(id, table_current_id, table_value);
    });
    jQuery(document).on('click', '#pccg_payment_add_new_custom_button', function () {
        var image_url = jQuery('.PCCG_PAYMENT_SITE_URL').val();
        var number_of_table = jQuery('.PCCG_PAYMENT_NUMBER_OF_TABLE').val();

        if (number_of_table < 4) {
            var id = parseInt(number_of_table) + 1;
            var str_row = '<table style="box-shadow: inset 0 0 6px red;" id="pccg-payment-table-' + id + '" class="widefat" data-custom="' + id + '"><tr><td colspan="2"><input class="pccg_payment_remove_new_custom_button" type="button" id="pccg_payment_remove_new_custom_button" name="pccg_payment_remove_new_custom_button" value="Remove Custom Option"></td></tr><tr><td colspan="2"><input style="height: 38px;width: 100%;" type = "text" name ="pccg_payment_custom_lable' + id + '" id = "pccg_payment_custom_lable' + id + '" class = "pccg-payment-field-style" placeholder = "Enter Custom Lable Name"></td></tr><tr id="pccg-payment-table-option-0" data-tr="0"><td><input style="height: 38px;width: 90%;" type = "text" name = "on' + id + '0" id = "on' + id + '0" class = "pccg-payment-field-style" placeholder = "Key"></td><td><input style="height: 38px;width: 90%;" type = "text" name = "os' + id + '0" id = "os' + id + '0" class = "pccg-payment-field-style" placeholder = "Value"><span class="pccg-payment-custom-add pccg-add-remove-icon-paypal"><img src="' + image_url + 'image/add.png"></span></td></tr></table>';
            jQuery("#pccg-payment-table-" + number_of_table).after(str_row);
            jQuery(".PCCG_PAYMENT_NUMBER_OF_TABLE").val(id);
        }
    });
    jQuery(document).on('click', '#pccg_payment_remove_new_custom_button', function () {
        var number_of_table = jQuery('.PCCG_PAYMENT_NUMBER_OF_TABLE').val();
        var new_value = jQuery(this).closest('table').attr('data-custom');
        var new_id = parseInt(new_value) + 1;
        for (var i = new_id; i <= number_of_table; i++)
        {
            var cla_data = parseInt(i) - 1;
            var table_current_id = jQuery("#pccg-payment-table-" + i).closest('table').attr('id');
            jQuery('#' + table_current_id).attr('data-custom', cla_data);
            var last_tr_id = jQuery("#" + table_current_id + " tr:last").attr('data-tr');
            for (var j = 0; j <= last_tr_id; j++)
            {
                jQuery('#' + table_current_id + ' #pccg-payment-table-option-' + j).attr('data-tr', j);
                jQuery('#' + table_current_id + ' #pccg-payment-table-option-' + j).attr('id', 'pccg-payment-table-option-' + j);
                jQuery('#' + table_current_id + ' #on' + i + j).attr('name', 'on' + cla_data + j);
                jQuery('#' + table_current_id + ' #on' + i + j).attr('id', 'on' + cla_data + j);
                jQuery('#' + table_current_id + ' #os' + i + j).attr('name', 'os' + cla_data + j);
                jQuery('#' + table_current_id + ' #os' + i + j).attr('id', 'os' + cla_data + j);
                jQuery('#' + table_current_id + ' #pccg-payment-remove-tr-' + j).attr('data-value', +j);
                jQuery('#' + table_current_id + ' #pccg-payment-remove-tr-' + j).attr('id', 'pccg-payment-remove-tr-' + j);
            }

            jQuery('#' + table_current_id + ' #pccg_payment_custom_lable' + i).attr('id', 'pccg_payment_custom_lable' + cla_data);
            jQuery('#pccg-payment-table-' + i).attr('id', 'pccg-payment-table-' + cla_data);

        }
        var table_current_id = jQuery(this).closest('table').attr('id');
        jQuery("#" + table_current_id).remove();
        var id = parseInt(number_of_table) - 1;
        jQuery(".PCCG_PAYMENT_NUMBER_OF_TABLE").val(id);
    });
    jQuery(document).on('click', '#pccg_payment_insert', function () {
        var pccg_align = pccg_paypal_align_shortcode();
        var pccg_quantity = pccg_paypal_quantity_shortcode();
        var tab_0_string = pccg_enable_border_tab_0();
        var tab_1_string = pccg_create_price_shortcode_tab_1();
        var tab_2_string = pccg_create_price_shortcode_tab_2();
        var tab_lable_string = pccg_create_lable_shortcode();

        window.send_to_editor('[credit_card_payment_code' + pccg_align + pccg_quantity + tab_0_string + tab_1_string + tab_2_string + tab_lable_string + ']');

    });

    jQuery(window).resize(function () {
        m7_resize_thickbox();
    });

    function pccg_paypal_quantity_shortcode() {
        var enable_string = "";
        var enable_check_box = jQuery('#pccg_payment_enable_quantity').val();
        if (enable_check_box == '1') {
            enable_string = ' quantity="true"';
        }
        return enable_string;
    }
    function m7_resize_thickbox() {
        var TB_HEIGHT = 'auto';
        var TB_WIDTH = jQuery('#TB_window').width();
        jQuery(document).find('#TB_window').width(TB_WIDTH).height(TB_HEIGHT).css('margin-left', -TB_WIDTH / 2);
        jQuery(document).find('#TB_ajaxContent').css({'width': '', 'height': ''});
    }
    function pccg_reset_name_with_id(id) {
        var new_id = parseInt(id) + 1;
        var last_tr_id = jQuery('#pccg_payment_option_table tr:last').attr('data-tr');
        for (var i = new_id; i <= last_tr_id; i++) {
            var cla_data = parseInt(i) - 1;
            jQuery('#pccg_tr_' + i).attr('data-tr', cla_data);
            jQuery('#pccg_tr_' + i).attr('id', 'pccg_tr_' + cla_data);
            jQuery('#on' + i).attr('name', 'on' + cla_data);
            jQuery('#on' + i).attr('id', 'on' + cla_data);
            jQuery('#os' + i).attr('name', 'os' + cla_data);
            jQuery('#os' + i).attr('id', 'os' + cla_data);
            jQuery('#pccg-payment-add-icon' + i).attr('data-value', +cla_data);
            jQuery('#pccg-payment-add-icon' + i).attr('id', 'pccg-payment-add-icon' + cla_data);
        }
    }
    function pccg_second_tab_reset_name_with_id(id, table_current_id, table_value) {

        var new_id = parseInt(id) + 1;
        var last_tr_id = jQuery("#" + table_current_id + " tr:last").attr('data-tr');
        for (var i = new_id; i <= last_tr_id; i++) {

            var cla_data = parseInt(i) - 1;
            jQuery('#' + table_current_id + ' #pccg-payment-table-option-' + i).attr('data-tr', cla_data);
            jQuery('#' + table_current_id + ' #pccg-payment-table-option-' + i).attr('id', 'pccg-payment-table-option-' + cla_data);
            jQuery('#' + table_current_id + ' #on' + table_value + i).attr('name', 'on' + table_value + cla_data);
            jQuery('#' + table_current_id + ' #on' + table_value + i).attr('id', 'on' + table_value + cla_data);
            jQuery('#' + table_current_id + ' #os' + table_value + i).attr('name', 'os' + table_value + cla_data);
            jQuery('#' + table_current_id + ' #os' + table_value + i).attr('id', 'os' + table_value + cla_data);
            jQuery('#' + table_current_id + ' #pccg-payment-remove-tr-' + i).attr('data-value', +cla_data);
            jQuery('#' + table_current_id + ' #pccg-payment-remove-tr-' + i).attr('id', 'pccg-payment-remove-tr-' + cla_data);
        }
    }
    function pccg_enable_border_tab_0() {
        var enable_string = "";
        var enable_check_box = jQuery('#pccg_payment_enable_border').val();
        if (enable_check_box == '1') {
            var get_border = jQuery('#pccg_payment_table_border').val();
            if (get_border != '0') {
                enable_string = ' border="' + get_border + '"';
            }
        }
        return enable_string;
    }
    function pccg_paypal_align_shortcode() {
        var pccg_align = "";
        var get_align = jQuery('#pccg_payment_align').val();
        if (get_align != 'align') {
            pccg_align = ' align="' + get_align + '"';
        }
        return pccg_align;
    }
    function pccg_create_price_shortcode_tab_1() {
        var result_string = "";
        var str = "";
        var select_method = jQuery('#PCCG_payment_tab_price_shortcode_price').val();
        if ('1' == select_method) {
            str = jQuery('#pccg_create_priceshortcode_1 #os0').val();
            if (str.toString().length > 0) {
                result_string = ' price="' + str + '"';
            }
        } else if ('2' == select_method) {
            var last_tr_id = jQuery('#pccg_payment_option_table tr:last').attr('data-tr');
            result_string = pccg_loop_option_table(last_tr_id);
        }

        return result_string;
    }
    function pccg_loop_option_table(last_tr_id) {
        var string = "";
        var str = "";
        var count_loop = 0;
        var lable_value = jQuery('#pccg_payment_lable').val();

        if (lable_value.toString().length > 0) {
            lable_value = "PCCG_0";
            for (var i = 0; i <= last_tr_id; i++) {
                var join_str = " | ";
                var key = "";
                var value = "";
                key = jQuery('#on' + i).val();
                value = jQuery('#os' + i).val();

                if (key.toString().length > 0 && value.toString().length > 0) {

                    if (count_loop == '0')
                    {
                        join_str = '';
                    }

                    str += join_str + "value='" + key + "' price='" + value + "'";
                    string = ' ' + lable_value + '=" ' + str + ' "';

                    count_loop = parseInt(count_loop) + 1;
                }

            }
        }
        return string;
    }
    function pccg_create_price_shortcode_tab_2() {
        var result_string = "";
        var table_count = jQuery('.PCCG_PAYMENT_NUMBER_OF_TABLE').val();
        result_string = pccg_loop_option_table_tab_2(table_count);
        return result_string;
    }
    function pccg_loop_option_table_tab_2(table_count) {
        var string = "";
        var str = "";

        for (var i = 0; i <= table_count; i++) {
            var count_loop = 0;
            str = "";
            var last_tr_id = jQuery('#pccg-payment-table-' + i + ' tr:last').attr('data-tr');
            if (last_tr_id.toString().length > 0) {
                for (var j = 0; j <= last_tr_id; j++) {
                    var join_str = " | ";
                    var key = "";
                    var value = "";
                    key = jQuery('#pccg-payment-table-' + i + ' #on' + i + j).val();
                    value = jQuery('#pccg-payment-table-' + i + ' #os' + i + j).val();

                    if (key.toString().length > 0 && value.toString().length > 0) {
                        if (count_loop == '0')
                        {
                            join_str = '';
                        }
                        str += join_str + "value='" + key + "' price='" + value + "'";
                        count_loop = parseInt(count_loop) + 1;
                    }
                }
                var lable_value = jQuery('#pccg_payment_custom_lable' + i).val();
                if (str.toString().length == 0 || lable_value.toString().length == 0) {

                } else {
                    lable_value = "PCCG" + i;
                    if (lable_value.toString().length > 0) {
                        string += ' ' + lable_value + '=" ' + str + ' "';
                    }
                }
            }
        }
        return string;
    }
    function pccg_create_lable_shortcode() {
        var lable_string = "";
        var str = "";
        var lable_value = jQuery('#pccg_payment_lable').val();

        var table_count = jQuery('.PCCG_PAYMENT_NUMBER_OF_TABLE').val();
        if (typeof lable_value != 'undefined') {
            if (lable_value.toString().length > 0) {

                var get_madatory_option_tab_1 = pccg_payment_set_lable_with_taxt_box_value_tab_1();
                if (get_madatory_option_tab_1 == true) {
                    if (table_count == '0') {
                        var table_enable_true_false = pccg_enable_table_0();
                        if (table_enable_true_false == true) {
                            str += lable_value + ', ';
                        } else {
                            str += lable_value + ' ';
                        }
                    } else {
                        str += lable_value + ', ';
                    }
                }

            }
        }
        if (table_count >= '0') {
            for (var i = 0; i <= table_count; i++) {

                var lable = jQuery('#pccg_payment_custom_lable' + i).val();
                var get_madatory_option = pccg_payment_set_lable_with_taxt_box_value(i);

                if (get_madatory_option == true && lable.toString().length > 0) {
                    var join_str = ', ';
                    if (i == table_count) {
                        join_str = '';
                    }
                    str += lable + join_str;
                }
            }
        }
        if (str.toString().length > 2) {
            str = str.match(/[^*]+[^,{\s+}?]/g);
            lable_string = ' pccg_name=" ' + str + ' "';
        }

        return lable_string;
    }
    function pccg_enable_table_0() {
        var result = false;
        var first_lable = jQuery('#pccg-payment-table-0 #pccg_payment_custom_lable0').val();
        var pccg_last_tr = jQuery('#pccg-payment-table-0 tr:last').attr('data-tr');
        if (first_lable.toString().length > 0) {
            for (var i = 0; i <= pccg_last_tr; i++) {
                var first_on = jQuery('#pccg-payment-table-0 #on0' + i).val();
                var first_os = jQuery('#pccg-payment-table-0 #os0' + i).val();
                if (first_on.toString().length > 0 && first_os.toString().length > 0) {
                    return true;
                }
            }
        }
        return result;
    }
    function pccg_payment_set_lable_with_taxt_box_value_tab_1() {

        var return_str = false;
        var last_tr_id = jQuery("#pccg_payment_option_table tr:last").attr('data-tr');
        for (var j = 0; j <= last_tr_id; j++) {
            var key = jQuery('#pccg_payment_option_table #on' + j).val();
            var value = jQuery('#pccg_payment_option_table #os' + j).val();
            if ((typeof key != 'undefined' && key.toString().length > 0) && (typeof value != 'undefined' && value.toString().length > 0)) {
                return_str = true;
                return true;
            }
        }
        return return_str;
    }
    function pccg_payment_set_lable_with_taxt_box_value(i) {

        var return_str = false;
        var last_tr_id = jQuery("#pccg-payment-table-" + i + " tr:last").attr('data-tr');
        for (var j = 0; j <= last_tr_id; j++) {
            var key = jQuery('#pccg-payment-table-' + i + ' #on' + i + j).val();
            var value = jQuery('#pccg-payment-table-' + i + ' #os' + i + j).val();
            if ((typeof key != 'undefined' && key.toString().length > 0) && (typeof value != 'undefined' && value.toString().length > 0)) {
                return_str = true;
                return true;
            }
        }
        return return_str;
    }

});