<?php

/**
 * @class       Donation_Button_General_Setting
 * @version	1.0.0
 * @package	credit-card-payment
 * @category	Class
 * @author      @author     mbj-webdevelopment <mbjwebdevelopment@gmail.com>
 */
class Credit_Card_Payment_Setting {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        add_action('credit_card_payment_pccg_general_setting', array(__CLASS__, 'credit_card_payment_pccg_general_setting'));
        add_action('credit_card_payment_pccg_general_setting_save_field', array(__CLASS__, 'credit_card_payment_pccg_general_setting_save_field'));
        add_action('credit_card_payment_pccg_pro_setting', array(__CLASS__, 'credit_card_payment_pccg_pro_setting'));
        add_action('credit_card_payment_pccg_pro_setting_save_field', array(__CLASS__, 'credit_card_payment_pccg_pro_setting_save_field'));
        add_action('credit_card_payment_pccg_payflow_setting', array(__CLASS__, 'credit_card_payment_pccg_payflow_setting'));
        add_action('credit_card_payment_pccg_payflow_setting_save_field', array(__CLASS__, 'credit_card_payment_pccg_payflow_setting_save_field'));
    }

    public static function credit_card_payment_pccg_general_setting() {
        $credit_card_payment_pccg_general_setting_fields = self::credit_card_payment_pccg_general_setting_fields();
        $Html_output = new Credit_Card_Payment_Html_output();
        ?>
        <form id="credit_card_payment_pro_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($credit_card_payment_pccg_general_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="credit_card_payment_general" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function credit_card_payment_pccg_general_setting_fields() {
        $currency_code_options = self::get_credit_card_payment_currencies();
        foreach ($currency_code_options as $code => $name) {
            $currency_code_options[$code] = $name . ' (' . self::get_credit_card_payment_symbol($code) . ')';
        }
        $fields[] = array('title' => __('Credit Card Payment General Settings', 'credit-card-payment'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array(
            'title' => __('Currency', 'credit-card-payment'),
            'desc' => __('This is the currency for your visitors to make Payments in.', 'credit-card-payment'),
            'id' => 'credit_card_payment_currency_general_settings',
            'css' => 'width:25em;',
            'default' => 'GBP',
            'type' => 'select',
            'class' => 'chosen_select',
            'options' => $currency_code_options
        );
        $fields[] = array(
            'title' => __('Select Credit Card Button', 'credit-card-payment'),
            'id' => 'credit_card_payment_button_general_settings',
            'default' => 'no',
            'type' => 'radio',
            'options' => array(
                'button1' => __('<img style="vertical-align: middle;width: 50%;" alt="small" src="' . plugins_url() . '/credit-card-payment/admin/image/1.png">', 'donation-button'),
                'button2' => __('<img style="vertical-align: middle;width: 40%;" alt="large" src="' . plugins_url() . '/credit-card-payment/admin/image/2.png">', 'donation-button'),
                'button3' => __('<img style="vertical-align: middle;width: 50%;" alt="cards" src="' . plugins_url() . '/credit-card-payment/admin/image/3.png">', 'donation-button'),
                'button4' => __('<img style="vertical-align: middle;width: 35%;" alt="small" src="' . plugins_url() . '/credit-card-payment/admin/image/4.png">', 'donation-button'),
                'button5' => __('<img style="vertical-align: middle;width: 40%;" alt="large" src="' . plugins_url() . '/credit-card-payment/admin/image/5.png">', 'donation-button'),
                'button6' => __('<img style="vertical-align: middle;width: 50%;" alt="cards" src="' . plugins_url() . '/credit-card-payment/admin/image/6.png">', 'donation-button'),
                'button7' => __('<img style="vertical-align: middle;width: 50%;" alt="cards" src="' . plugins_url() . '/credit-card-payment/admin/image/7.png">', 'donation-button'),
                'button8' => __('<img style="vertical-align: middle;width: 50%;" alt="cards" src="' . plugins_url() . '/credit-card-payment/admin/image/8.png">', 'donation-button'),
                'button9' => __('<img style="vertical-align: middle;width: 50%;" alt="cards" src="' . plugins_url() . '/credit-card-payment/admin/image/9.png">', 'donation-button'),
                'button10' => __('<img style="vertical-align: middle;width: 50%;" alt="cards" src="' . plugins_url() . '/credit-card-payment/admin/image/10.png">', 'donation-button'),
                'button11' => __('<img style="vertical-align: middle;width: 50%;" alt="cards" src="' . plugins_url() . '/credit-card-payment/admin/image/11.png">', 'donation-button'),
                'button12' => __('Custom Button ( If you select this option then pleae enter url in Custom Button textbox, Otherwise payment button will not display. )', 'donation-button')
            ),
        );

        $fields[] = array(
            'title' => __('Custom Button', 'credit-card-payment'),
            'type' => 'text',
            'id' => 'credit_card_payment_custom_button_general_settings',
            'desc' => __('Enter a URL to a custom payment button.', 'credit-card-payment'),
            'default' => '',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );
        $fields[] = array(
            'title' => __('Thankyou Page', 'credit-card-payment'),
            'desc' => __('This is the Select A Thankyou Page Redirect.', 'credit-card-payment'),
            'id' => 'credit_card_payment_thankyou_general_settings',
            'css' => 'width:25em;',
            'type' => 'select',
            'default' => self::thankyou_selected_page(),
            'class' => 'chosen_select',
            'options' => self::get_page_list()
        );

        $fields[] = array(
            'title' => __('Credit Card Display Page', 'credit-card-payment'),
            'desc' => __('This is the Display Credit Card Page.', 'credit-card-payment'),
            'id' => 'credit_card_payment_credit_card_form_general_settings',
            'css' => 'width:25em;',
            'type' => 'select',
            'default' => self::credit_card_form_selected_page(),
            'class' => 'chosen_select',
            'options' => self::get_page_list()
        );

        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function credit_card_payment_pccg_general_setting_save_field() {
        $credit_card_payment_pccg_general_setting_fields = self::credit_card_payment_pccg_general_setting_fields();
        $Html_output = new Credit_Card_Payment_Html_output();
        $Html_output->save_fields($credit_card_payment_pccg_general_setting_fields);
    }

    public static function credit_card_payment_pccg_pro_setting() {

        $credit_card_payment_setting_fields = self::credit_card_payment_setting_fields();
        $Html_output = new Credit_Card_Payment_Html_output();
        ?>
        <form id="credit_card_payment_pro_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($credit_card_payment_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="credit_card_payment_pro" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function credit_card_payment_setting_fields() {
        $currency_code_options = self::get_credit_card_payment_currencies();
        foreach ($currency_code_options as $code => $name) {
            $currency_code_options[$code] = $name . ' (' . self::get_credit_card_payment_symbol($code) . ')';
        }
        $fields[] = array('title' => __('PayPal Pro Credit Card Payment', 'credit-card-payment'), 'type' => 'title', 'desc' => 'PayPal Pro works by adding credit card fields on the checkout and then sending the details to PayPal for verification.', 'id' => 'general_options');
        $fields[] = array(
            'title' => __('Enable/Disable', 'credit-card-payment'),
            'type' => 'checkbox',
            'id' => 'credit_card_payment_enable',
            'label' => __('Enable/Disable Paypal Pro Credit Card Payment', 'credit-card-payment'),
            'default' => 'no',
            'css' => 'min-width:300px;',
            'desc' => sprintf(__('Enable/Disable Paypal Pro Credit Card Payment can be used to test payments. Sign up for a developer account <a href="%s">here</a>.', 'credit-card-payment'), 'https://developer.paypal.com/'),
        );
        $fields[] = array(
            'title' => __('Title', 'credit-card-payment'),
            'id' => 'credit_card_payment_title_general_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Description', 'credit-card-payment'),
            'id' => 'credit_card_payment_description_general_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Test Mode', 'credit-card-payment'),
            'id' => 'credit_card_payment_test_mode_general_settings',
            'type' => 'checkbox',
            'label' => __('Enable Paypal Pro Credit Card Payment Sandbox/Test Mode', 'credit-card-payment'),
            'desc' => sprintf(__('Enable Paypal Pro Credit Card Payment Sandbox/Test Mode', 'credit-card-payment'), array()),
            'default' => 'no'
        );
        $fields[] = array(
            'title' => __('API Username', 'credit-card-payment'),
            'id' => 'credit_card_payment_api_username_general_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('API Password', 'credit-card-payment'),
            'id' => 'credit_card_payment_api_password_general_settings',
            'type' => 'password',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('API Signature', 'credit-card-payment'),
            'id' => 'credit_card_payment_api_signature_general_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Debug Log', 'credit-card-payment'),
            'id' => 'log_enable_general_settings',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'credit-card-payment'),
            'default' => 'no',
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function credit_card_payment_pccg_pro_setting_save_field() {
        $credit_card_payment_general_setting_fields = self::credit_card_payment_setting_fields();
        $Html_output = new Credit_Card_Payment_Html_output();
        $Html_output->save_fields($credit_card_payment_general_setting_fields);
    }

    public static function credit_card_payment_pccg_payflow_setting() {

        $credit_card_payment_setting_fields = self::credit_card_payment_payflow_setting_fields();
        $Html_output = new Credit_Card_Payment_Html_output();
        ?>
        <form id="credit_card_payment_pro_payflow_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($credit_card_payment_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="credit_card_payment_payflow" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function credit_card_payment_payflow_setting_fields() {
        $currency_code_options = self::get_credit_card_payment_currencies();
        foreach ($currency_code_options as $code => $name) {
            $currency_code_options[$code] = $name . ' (' . self::get_credit_card_payment_symbol($code) . ')';
        }
        $fields[] = array('title' => __('PayPal Pro PayFlow Credit Card Payment', 'credit-card-payment'), 'type' => 'title', 'desc' => 'PayPal Pro PayFlow Edition works by adding credit card fields on the checkout and then sending the details to PayPal for verification.', 'id' => 'general_options');
        $fields[] = array(
            'title' => __('Enable/Disable', 'credit-card-payment'),
            'type' => 'checkbox',
            'id' => 'pccg_pro_payflow_enable',
            'default' => 'no',
            'css' => 'min-width:300px;',
            'desc' => sprintf(__('Enable/Disable Paypal Pro Payflow Credit Card Payment can be used to test payments. Sign up for a manager account <a href="%s">here</a>.', 'credit-card-payment'), 'https://manager.paypal.com/'),
        );
        $fields[] = array(
            'title' => __('Title', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_title_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Description', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_description_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Soft Descriptor', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_soft_descriptor_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Test Mode', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_test_mode_settings',
            'type' => 'checkbox',
            'desc' => sprintf(__('Enable Paypal Pro PayFlow Credit Card Payment Sandbox/Test Mode', 'credit-card-payment'), array()),
            'default' => 'no'
        );
        $fields[] = array(
            'title' => __('PayPal Vendor', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_vendor_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('PayPal Password', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_password_settings',
            'type' => 'password',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('PayPal User', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_user_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('PayPal Partner', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_partner_settings',
            'type' => 'text',
            'css' => 'width:25em;',
            'default' => ''
        );
        $fields[] = array(
            'title' => __('Payment Action', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_payment_action_settings',
            'css' => 'width:10em;',
            'type' => 'select',
            'default' => '',
            'class' => 'chosen_select',
            'options' => array(
                'S' => __('Capture', 'all-in-one-paypal-for-woocommerce'),
                'A' => __('Authorize', 'all-in-one-paypal-for-woocommerce')
            )
        );
        $fields[] = array(
            'title' => __('Debug Log', 'credit-card-payment'),
            'id' => 'pccg_pro_payflow_log_enable_settings',
            'type' => 'checkbox',
            'label' => __('Enable logging', 'credit-card-payment'),
            'default' => 'no',
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function credit_card_payment_pccg_payflow_setting_save_field() {
        $credit_card_payment_setting_fields = self::credit_card_payment_payflow_setting_fields();
        $Html_output = new Credit_Card_Payment_Html_output();
        $Html_output->save_fields($credit_card_payment_setting_fields);
    }

    public static function get_page_list() {
        try {
            $pages = get_pages();
            $result = array();
            foreach ($pages as $page) {
                $result[$page->ID] = $page->post_title;
            }
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public static function thankyou_selected_page() {
        try {
            $result = '';
            $page_select = (get_option('credit_card_payment_thankyou_general_settings')) ? get_option('credit_card_payment_thankyou_general_settings') : '';
            if (!$page_select) {
                $pages = get_pages();
                foreach ($pages as $page) {
                    if ('CCThankyou' == $page->post_title) {
                        $result = $page->ID;
                    }
                }
            } else {
                $result = $page_select;
            }
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public static function credit_card_form_selected_page() {
        try {
            $result = '';
            $credit_card_form_page = (get_option('credit_card_payment_credit_card_form_general_settings')) ? get_option('credit_card_payment_credit_card_form_general_settings') : '';
            if (!$credit_card_form_page) {
                $pages = get_pages();
                foreach ($pages as $page) {
                    if ('CCPay' == $page->post_title) {
                        $result = $page->ID;
                    }
                }
            } else {
                $result = $credit_card_form_page;
            }
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public static function get_credit_card_payment_currencies() {
        return array(
            'AED' => __('United Arab Emirates Dirham', 'credit-card-payment'),
            'AUD' => __('Australian Dollars', 'credit-card-payment'),
            'BDT' => __('Bangladeshi Taka', 'credit-card-payment'),
            'BRL' => __('Brazilian Real', 'credit-card-payment'),
            'BGN' => __('Bulgarian Lev', 'credit-card-payment'),
            'CAD' => __('Canadian Dollars', 'credit-card-payment'),
            'CLP' => __('Chilean Peso', 'credit-card-payment'),
            'CNY' => __('Chinese Yuan', 'credit-card-payment'),
            'COP' => __('Colombian Peso', 'credit-card-payment'),
            'CZK' => __('Czech Koruna', 'credit-card-payment'),
            'DKK' => __('Danish Krone', 'credit-card-payment'),
            'DOP' => __('Dominican Peso', 'credit-card-payment'),
            'EUR' => __('Euros', 'credit-card-payment'),
            'HKD' => __('Hong Kong Dollar', 'credit-card-payment'),
            'HRK' => __('Croatia kuna', 'credit-card-payment'),
            'HUF' => __('Hungarian Forint', 'credit-card-payment'),
            'ISK' => __('Icelandic krona', 'credit-card-payment'),
            'IDR' => __('Indonesia Rupiah', 'credit-card-payment'),
            'INR' => __('Indian Rupee', 'credit-card-payment'),
            'NPR' => __('Nepali Rupee', 'credit-card-payment'),
            'ILS' => __('Israeli Shekel', 'credit-card-payment'),
            'JPY' => __('Japanese Yen', 'credit-card-payment'),
            'KIP' => __('Lao Kip', 'credit-card-payment'),
            'KRW' => __('South Korean Won', 'credit-card-payment'),
            'MYR' => __('Malaysian Ringgits', 'credit-card-payment'),
            'MXN' => __('Mexican Peso', 'credit-card-payment'),
            'NGN' => __('Nigerian Naira', 'credit-card-payment'),
            'NOK' => __('Norwegian Krone', 'credit-card-payment'),
            'NZD' => __('New Zealand Dollar', 'credit-card-payment'),
            'PYG' => __('Paraguayan Guaraní', 'credit-card-payment'),
            'PHP' => __('Philippine Pesos', 'credit-card-payment'),
            'PLN' => __('Polish Zloty', 'credit-card-payment'),
            'GBP' => __('Pounds Sterling', 'credit-card-payment'),
            'RON' => __('Romanian Leu', 'credit-card-payment'),
            'RUB' => __('Russian Ruble', 'credit-card-payment'),
            'SGD' => __('Singapore Dollar', 'credit-card-payment'),
            'ZAR' => __('South African rand', 'credit-card-payment'),
            'SEK' => __('Swedish Krona', 'credit-card-payment'),
            'CHF' => __('Swiss Franc', 'credit-card-payment'),
            'TWD' => __('Taiwan New Dollars', 'credit-card-payment'),
            'THB' => __('Thai Baht', 'credit-card-payment'),
            'TRY' => __('Turkish Lira', 'credit-card-payment'),
            'USD' => __('US Dollars', 'credit-card-payment'),
            'VND' => __('Vietnamese Dong', 'credit-card-payment'),
            'EGP' => __('Egyptian Pound', 'credit-card-payment')
        );
    }

    public static function get_credit_card_payment_symbol($currency = '') {

        $currency_symbol = '';
        if (!$currency) {
            $currency = get_credit_card_payment_currencies();
        }
        switch ($currency) {
            case 'AED' :
                $currency_symbol = 'د.إ';
                break;
            case 'BDT':
                $currency_symbol = '&#2547;&nbsp;';
                break;
            case 'BRL' :
                $currency_symbol = '&#82;&#36;';
                break;
            case 'BGN' :
                $currency_symbol = '&#1083;&#1074;.';
                break;
            case 'AUD' :
            case 'CAD' :
            case 'CLP' :
            case 'COP' :
            case 'MXN' :
            case 'NZD' :
            case 'HKD' :
            case 'SGD' :
            case 'USD' :
                $currency_symbol = '&#36;';
                break;
            case 'EUR' :
                $currency_symbol = '&euro;';
                break;
            case 'CNY' :
            case 'RMB' :
            case 'JPY' :
                $currency_symbol = '&yen;';
                break;
            case 'RUB' :
                $currency_symbol = '&#1088;&#1091;&#1073;.';
                break;
            case 'KRW' : $currency_symbol = '&#8361;';
                break;
            case 'PYG' : $currency_symbol = '&#8370;';
                break;
            case 'TRY' : $currency_symbol = '&#8378;';
                break;
            case 'NOK' : $currency_symbol = '&#107;&#114;';
                break;
            case 'ZAR' : $currency_symbol = '&#82;';
                break;
            case 'CZK' : $currency_symbol = '&#75;&#269;';
                break;
            case 'MYR' : $currency_symbol = '&#82;&#77;';
                break;
            case 'DKK' : $currency_symbol = 'kr.';
                break;
            case 'HUF' : $currency_symbol = '&#70;&#116;';
                break;
            case 'IDR' : $currency_symbol = 'Rp';
                break;
            case 'INR' : $currency_symbol = 'Rs.';
                break;
            case 'NPR' : $currency_symbol = 'Rs.';
                break;
            case 'ISK' : $currency_symbol = 'Kr.';
                break;
            case 'ILS' : $currency_symbol = '&#8362;';
                break;
            case 'PHP' : $currency_symbol = '&#8369;';
                break;
            case 'PLN' : $currency_symbol = '&#122;&#322;';
                break;
            case 'SEK' : $currency_symbol = '&#107;&#114;';
                break;
            case 'CHF' : $currency_symbol = '&#67;&#72;&#70;';
                break;
            case 'TWD' : $currency_symbol = '&#78;&#84;&#36;';
                break;
            case 'THB' : $currency_symbol = '&#3647;';
                break;
            case 'GBP' : $currency_symbol = '&pound;';
                break;
            case 'RON' : $currency_symbol = 'lei';
                break;
            case 'VND' : $currency_symbol = '&#8363;';
                break;
            case 'NGN' : $currency_symbol = '&#8358;';
                break;
            case 'HRK' : $currency_symbol = 'Kn';
                break;
            case 'EGP' : $currency_symbol = 'EGP';
                break;
            case 'DOP' : $currency_symbol = 'RD&#36;';
                break;
            case 'KIP' : $currency_symbol = '&#8365;';
                break;
            default : $currency_symbol = '';
                break;
        }
        return $currency_symbol;
    }

}

Credit_Card_Payment_Setting::init();