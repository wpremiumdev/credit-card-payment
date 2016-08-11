<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://mbjtechnolabs.com/
 * @since      1.0.0
 *
 * @package    Credit_Card_Payment_Gateway
 * @subpackage Credit_Card_Payment_Gateway/public/partials
 */
class Credit_Card_Payment_Gateway_Payment_Pro {

    public function __construct() {

        $this->id = 'credit_card_payment';
        $this->api_version = '120';
        $this->liveurl = 'https://api-3t.paypal.com/nvp';
        $this->testurl = 'https://api-3t.sandbox.paypal.com/nvp';
        $this->api_username = get_option('credit_card_payment_api_username_general_settings');
        $this->api_password = get_option('credit_card_payment_api_password_general_settings');
        $this->api_signature = get_option('credit_card_payment_api_signature_general_settings');
        $this->api_currency_code = get_option('credit_card_payment_currency_general_settings') ? get_option('credit_card_payment_currency_general_settings') : 'USD';
        $this->page_select_id = (get_option('credit_card_payment_thankyou_general_settings')) ? get_option('credit_card_payment_thankyou_general_settings') : '';
        $this->page_error_select_id = (get_option('credit_card_payment_credit_card_form_general_settings')) ? get_option('credit_card_payment_credit_card_form_general_settings') : '';
        $this->testmode = get_option('credit_card_payment_test_mode_general_settings', "no") === "yes" ? true : false;
        $this->debug = get_option('log_enable_general_settings', "no") === "yes" ? true : false;
        $this->enabled = get_option('credit_card_payment_enable', "no") === "yes" ? true : false;
        $this->title = get_option('credit_card_payment_title_general_settings', "no") === "yes" ? true : false;
        $this->description = get_option('credit_card_payment_description_general_settings', "no") === "yes" ? true : false;
    }

    public function get_user_ip() {
        return !empty($_SERVER['HTTP_X_FORWARD_FOR']) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
    }

    public function do_payment($post) {
        try {

            if ($this->debug) {
                $log = new Credit_Card_Payment_Logger();
            }
            if ($this->enabled) {

                $card_exp = $post['expiry'];
                $card_exp = preg_replace('/(\s+)?[\/](\s+)?/', '', $card_exp);
                $card_start = '';
                $url = $this->testmode ? $this->testurl : $this->liveurl;
                $post_data = array(
                    'VERSION' => '121',
                    'SIGNATURE' => $this->api_signature,
                    'USER' => $this->api_username,
                    'PWD' => $this->api_password,
                    'METHOD' => 'DoDirectPayment',
                    'PAYMENTACTION' => 'sale',
                    'IPADDRESS' => $this->get_user_ip(),
                    'AMT' => $post['creditcard_pay_amount'] * $post['creditcard_pay_quantity'],
                    'INVNUM' => substr(microtime(), -5),
                    'CURRENCYCODE' => $this->api_currency_code,
                    'CREDITCARDTYPE' => $post['creditcard_type'],
                    'ACCT' => str_replace(' ', '', $post['number']),
                    'EXPDATE' => $card_exp,
                    'STARTDATE' => $card_start,
                    'CVV2' => $post['credit_card_csv'],
                    'EMAIL' => '',
                    'FIRSTNAME' => $post['name'],
                    'DESC' => $_POST['creditcard_options'],
                    'BUTTONSOURCE' => 'mbjtechnolabs_SP'
                );
                if ($this->debug) {
                    $create_log_data = $this->pro_get_credentials_sequre_methods($post_data);
                    $log->add('PCCG_Paypal_Pro', 'PCCG Paypal Pro Parsed' . print_r($create_log_data, true));
                }

                $response = wp_remote_post($url, array(
                    'method' => 'POST',
                    'headers' => array(
                        'PAYPAL-NVP' => 'Y'
                    ),
                    'body' => $post_data,
                    'timeout' => 70,
                    'user-agent' => 'credit card',
                    'httpversion' => '1.1'
                ));

                $page_redirect_id = '';
                $post_id = "";
                $status = '';
                $responce_error_pro = "false";
                if (is_wp_error($response)) {
                    $responce_error_pro = "true";
                    if ($this->debug) {
                        $log->add('PCCG_Paypal_Pro', 'PCCG Paypal Pro Error ' . print_r($response->get_error_message(), true));
                        $status[0] = $response->get_error_message();
                        if (!isset($this->page_error_select_id) && empty($this->page_error_select_id)) {
                            $page_redirect_id = $this->get_defualt_error_page_selected();
                        } else {
                            $page_redirect_id = $this->page_error_select_id;
                        }
                    }
                }

                if ("false" == $responce_error_pro) {
                    if (isset($response['body']) && empty($response['body'])) {
                        if ($this->debug) {
                            $log->add('PCCG_Paypal_Pro', 'PCCG Paypal Pro Empty Response!', print_r($response['body'], true));
                        }
                    }
                    parse_str($response['body'], $parsed_response);
                    if ($this->debug) {
                        $log->add('PCCG_Paypal_Pro', 'PCCG Paypal Pro Response ' . print_r($parsed_response, true));
                    }


                    $result_data = "";
                    $custom_page_id = '';

                    if ($parsed_response['ACK'] == 'Success') {
                        $status = "Success";
                        $txn_id = $parsed_response['TRANSACTIONID'];
                        $details = $this->get_transaction_details($txn_id);
                        $details['set_amount'] = $_POST['creditcard_pay_amount'];
                        $details['PAYMENTMETHOD'] = 'Credit Card Paypal Pro';
                        $post_id = $this->add_new_post_and_postmeta($details);
                        if (isset($this->page_select_id) && empty($this->page_select_id)) {
                            $page_redirect_id = $this->get_defualt_page_selected();
                        } else {
                            $page_redirect_id = $this->page_select_id;
                        }
                    }
                    if ($parsed_response['ACK'] == 'Failure') {

                        if (!empty($parsed_response['L_LONGMESSAGE0']))
                            $details[] = $parsed_response['L_LONGMESSAGE0'];
                        elseif (!empty($parsed_response['L_SHORTMESSAGE0']))
                            $details[] = $parsed_response['L_SHORTMESSAGE0'];
                        elseif (!empty($parsed_response['L_SEVERITYCODE0']))
                            $details[] = $parsed_response['L_SEVERITYCODE0'];
                        elseif ($this->testmode)
                            $details[] = print_r($parsed_response, true);
                        $status = $details;
                        if (isset($this->page_error_select_id) && empty($this->page_error_select_id)) {
                            $page_redirect_id = $this->get_defualt_error_page_selected();
                        } else {
                            $page_redirect_id = $this->page_error_select_id;
                        }
                    }
                }

                $redirect = get_permalink($page_redirect_id);
                if (isset($redirect)) {
                    return array(
                        'custom_post_id' => $post_id,
                        'redirect' => $redirect,
                        'status' => $status,
                        'amt' => $_POST['creditcard_pay_amount'],
                        'qty' => $_POST['creditcard_pay_quantity'],
                        'pccg_option' => $_POST['creditcard_options']
                    );
                } else {
                    return;
                }
            } else {
                if ($this->debug) {
                    $log->add('PCCG_Paypal_Pro', 'PCCG Paypal Pro', 'Plese Enable Payment Credit Card Pro');
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function pro_get_credentials_sequre_methods($post_data) {
        try {
            $result = $post_data;
            foreach ($result as $key => $value) {
                if ("USER" == $key || "VENDOR" == $key || "PARTNER" == $key || "PWD" == $key || "BUTTONSOURCE" == $key || "ACCT" == $key || "EXPDATE" == $key || "CVV2" == $key || "EMAIL" == $key || "SIGNATURE" == $key) {

                    $str_length = strlen($value);
                    $ponter_data = "";
                    for ($i = 0; $i <= $str_length; $i++) {
                        $ponter_data .= '*';
                    }
                    $result[$key] = $ponter_data;
                }
            }
            return $result;
        } catch (Exception $EX) {
            
        }
    }

    public function get_defualt_page_selected() {
        try {
            $result = '';
            $pages = get_pages();
            foreach ($pages as $page) {
                if ('CCThankyou' == $page->post_title) {
                    $result = $page->ID;
                }
            }
            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function get_defualt_error_page_selected() {
        try {
            $result = '';
            $pages = get_pages();
            foreach ($pages as $page) {
                if ('CCPay' == $page->post_title) {
                    $result = $page->ID;
                }
            }
            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function add_new_post_and_postmeta($details) {
        try {
            $post = "";
            $newid = "";
            $post = array(
                'comment_status' => 'open',
                'ping_status' => 'closed',
                'post_date' => date('Y-m-d H:i:s'),
                'post_name' => 'PCCG',
                'post_status' => 'publish',
                'post_title' => 'PCCG',
                'post_type' => 'pccg_order',
                'post_content' => $details['TRANSACTIONID'],
            );
            $newid = wp_insert_post($post, false);
            update_post_meta($newid, 'credit_card_payment_confirm', $details);
            return $newid;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function get_transaction_details($transaction_id = 0) {
        $url = $this->testmode ? $this->testurl : $this->liveurl;
        $post_data = array(
            'VERSION' => $this->api_version,
            'SIGNATURE' => $this->api_signature,
            'USER' => $this->api_username,
            'PWD' => $this->api_password,
            'METHOD' => 'GetTransactionDetails',
            'TRANSACTIONID' => $transaction_id
        );
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'headers' => array(
                'PAYPAL-NVP' => 'Y'
            ),
            'body' => $post_data,
            'timeout' => 70,
            'user-agent' => 'credit card',
            'httpversion' => '1.1'
        ));
        if (is_wp_error($response)) {

            if ($this->debug) {
                $log->add('PCCG_Paypal_Pro', 'PCCG Paypal Pro Error', print_r($response->get_error_message(), true));
            }
            return false;
        }
        parse_str($response['body'], $parsed_response);
        switch (strtolower($parsed_response['ACK'])) {
            case 'success':
            case 'successwithwarning':
                return $parsed_response;
                break;
        }
        return false;
    }

}