jQuery(function ($) {

    jQuery(document).on('click', '#ccpayment', function (e) {

        var name = jQuery('.credit_card_user_name').val();
        var exp_date = jQuery('.credit_card_expire_date').val();
        var number = jQuery('.credit_card_number').val();
        var csv = jQuery('.credit_card_csv').val();
        var string = "";

        if (name.length == 0) {
            string += '<div class="alert alert-error">User Name Empty.</div>';
        }

        if (exp_date.match(/\d{2}(\s+)?[/](\s+)?\d{4}/g) == null) {
            string += '<div class="alert alert-error">Expiry Date Invalid.</div>';
        }

        if (number.length == 0) {
            string += '<div class="alert alert-error">Card Number Empty.</div>';
        }
        if (csv.length == 0) {
            string += '<div class="alert alert-error">CVC Number Empty.</div>';
        }

        if (string.length > 0) {
            e.preventDefault();
            jQuery('.show_empty_fileds').show();
            jQuery('.show_empty_fileds').html(string);
            jQuery('form#credit_card_payment_data #ccpayment').attr('onclick', '').unbind('click');
        } else {
            jQuery('.show_empty_fileds').hide();
            jQuery('.payment_process_button').hide();
            jQuery('.payment_process_bar').show();
        }
    });

    jQuery(document).ready(function () {
        var card = new Card({
            form: '#credit_card_payment_data',
            container: '.card-wrapper',
            formSelectors: {
                numberInput: 'input#number',
                expiryInput: 'input#expiry',
                cvcInput: 'input#cvc',
                nameInput: 'input#name'
            },
            width: 200,
            formatting: true,
            messages: {
                validDate: 'valid\ndate',
                monthYear: 'mm/yyyy',
            },
            placeholders: {
                number: '•••• •••• •••• ••••',
                name: 'Full Name',
                expiry: '••/••',
                cvc: '•••'
            },
            debug: true
        });
    });
});