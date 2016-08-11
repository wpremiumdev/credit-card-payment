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
class Credit_Card_Payment_Gateway_Payment_payflow {

    public function __construct() {

        $this->id = 'paypal_pro_payflow';
        $this->method_title = __('PayPal Pro PayFlow', 'all-in-one-paypal-for-woocommerce');
        $this->method_description = __('PayPal Pro PayFlow Edition works by adding credit card fields on the checkout and then sending the details to PayPal for verification.', 'all-in-one-paypal-for-woocommerce');
        $this->liveurl = 'https://payflowpro.paypal.com';
        $this->testurl = 'https://pilot-payflowpro.paypal.com';
        $this->currencies = get_option('credit_card_payment_currency_general_settings') ? get_option('credit_card_payment_currency_general_settings') : 'USD';
        $this->title = get_option('pccg_pro_payflow_title_settings');
        $this->description = get_option('pccg_pro_payflow_description_settings');
        $this->enabled = get_option('pccg_pro_payflow_enable', "no") === "yes" ? true : false;
        $this->paypal_vendor = get_option('pccg_pro_payflow_vendor_settings');
        $this->paypal_partner = get_option('pccg_pro_payflow_partner_settings');
        $this->paypal_password = trim(get_option('pccg_pro_payflow_password_settings'));
        $this->paypal_user = get_option('pccg_pro_payflow_user_settings');
        $this->testmode = get_option('pccg_pro_payflow_test_mode_settings') === "yes" ? true : false;
        $this->debug = get_option('pccg_pro_payflow_log_enable_settings', "no") === "yes" ? true : false;
        $this->transparent_redirect = get_option('pccg_pro_payflow_transparent_redirect_settings') === "yes" ? true : false;
        $this->soft_descriptor = str_replace(' ', '-', preg_replace('/[^A-Za-z0-9\-\.]/', '', get_option('pccg_pro_payflow_soft_descriptor_settings', "")));
        $this->paymentaction = strtoupper(get_option('pccg_pro_payflow_payment_action_settings', 'S'));
        $this->page_select_id = (get_option('credit_card_payment_thankyou_general_settings')) ? get_option('credit_card_payment_thankyou_general_settings') : '';
        $this->page_error_select_id = (get_option('credit_card_payment_credit_card_form_general_settings')) ? get_option('credit_card_payment_credit_card_form_general_settings') : '';
        $this->payer_firstname = '';
        $this->payer_lastname = '';
    }

    public function get_user_ip() {

        return !empty($_SERVER['HTTP_X_FORWARD_FOR']) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
    }

    public function do_payment($post) {

        try {
            //URL mode check
            if ($this->debug) {
                $log = new Credit_Card_Payment_Logger();
            }
            if ($this->enabled) {

                $url = $this->testmode ? $this->testurl : $this->liveurl;
                $card_number = $post['number'];
                $card_cvc = $post['credit_card_csv'];
                $card_number = str_replace(array(' ', '-'), '', $card_number);
                $card_expiry = preg_replace('/(\s+)?[\/](\s+)?/', '', $post['expiry']);
                $post_data = $this->get_post_data_pro_payflow($post);
                $post_data['ACCT'] = $card_number;
                $post_data['EXPDATE'] = $card_expiry;
                $post_data['CVV2'] = $card_cvc;
                if ($this->debug) {

                    $create_log_data = $this->pro_pay_flow_get_credentials_sequre_methods($post_data);
                    $log->add('PCCG_Paypal_Pro_Payflow', 'PCCG Paypal Pro Payflow Parsed' . print_r($create_log_data, true));
                }

                $response = wp_remote_post($url, array(
                    'method' => 'POST',
                    'body' => $post_data,
                    'timeout' => 70,
                    'user-agent' => 'PCCG',
                    'httpversion' => '1.1'
                ));


                $page_redirect_id = '';
                $post_id = "";
                $status = '';
                $responce_error_proflow = "false";
                if (is_wp_error($response)) {
                    $responce_error_proflow = "true";
                    if ($this->debug) {
                        $log->add('PCCG_Paypal_Pro_Payflow', 'PCCG Paypal Pro Payflow Error! ' . print_r($response->get_error_message(), true));
                        $status[0] = $response->get_error_message();
                        if (isset($this->page_error_select_id) && empty($this->page_error_select_id)) {
                            $page_redirect_id = $this->get_defualt_error_page_selected();
                        } else {
                            $page_redirect_id = $this->page_error_select_id;
                        }
                    }
                }
                if ("false" == $responce_error_proflow) {
                    if (isset($response['body']) && empty($response['body'])) {
                        if ($this->debug) {
                            $log->add('PCCG_Paypal_Pro_Payflow', 'PCCG Paypal Pro Payflow Empty Response!', print_r($response['body'], true));
                        }
                    }
                    parse_str($response['body'], $parsed_response);
                    if ($this->debug) {
                        $log->add('PCCG_Paypal_Pro_Payflow', 'PCCG Paypal Pro Payflow Response ' . print_r($parsed_response, true));
                    }

                    if (isset($parsed_response['RESULT']) && in_array($parsed_response['RESULT'], array(0, 126, 127))) {

                        $txn_id = (!empty($parsed_response['PNREF']) ) ? $parsed_response['PNREF'] : '';
                        $details = $this->get_transaction_details($txn_id);
                        $details['FIRSTNAME'] = $this->payer_firstname;
                        $details['LASTNAME'] = $this->payer_lastname;
                        $details['CURRENCYCODE'] = $this->currencies;
                        $details['AMT'] = $_POST['creditcard_pay_amount'] * $_POST['creditcard_pay_quantity'];
                        $details['DESC'] = $_POST['creditcard_options'];
                        $details['ORDERTIME'] = date('Y-m-d H:i:s');
                        $details['PAYMENTSTATUS'] = 'Completed';
                        $details['PAYMENTMETHOD'] = 'Credit Card Paypal Pro PayFlow';
                        $status = 'Success';
                        $post_id = $this->add_new_post_and_postmeta($details);
                        if (isset($this->page_select_id) && empty($this->page_select_id)) {
                            $page_redirect_id = $this->get_defualt_page_selected();
                        } else {
                            $page_redirect_id = $this->page_select_id;
                        }
                    } else {

                        $status = $parsed_response['RESPMSG'];

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
                    $log->add('PCCG', 'PCCG Paypal Pro Payflow', 'Plese Enable Payment Credit Card Pro Payflow');
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function pro_pay_flow_get_credentials_sequre_methods($post_data) {
        try {
            $result = $post_data;
            foreach ($result as $key => $value) {
                if ("USER" == $key || "VENDOR" == $key || "PARTNER" == $key || "PWD" == $key || "BUTTONSOURCE" == $key || "ACCT" == $key || "EXPDATE" == $key || "CVV2" == $key || "EMAIL" == $key) {

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
                'post_content' => $details['PNREF'],
            );
            $newid = wp_insert_post($post, false);
            update_post_meta($newid, 'credit_card_payment_confirm', $details);
            return $newid;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function get_post_data_pro_payflow($post) {
        try {
            $post_data = array();

            $post_data['USER'] = $this->paypal_user;
            $post_data['VENDOR'] = $this->paypal_vendor;
            $post_data['PARTNER'] = $this->paypal_partner;
            $post_data['PWD'] = $this->paypal_password;
            $post_data['TENDER'] = 'C';
            $post_data['TRXTYPE'] = $this->paymentaction;
            $post_data['AMT'] = $post['creditcard_pay_amount'];
            $post_data['CURRENCY'] = $this->currencies;
            $post_data['CUSTIP'] = $this->get_user_ip();
            $post_data['EMAIL'] = '';
            $post_data['INVNUM'] = substr(microtime(), -5);
            $post_data['BUTTONSOURCE'] = 'mbjtechnolabs_SP';
            if ($this->soft_descriptor) {
                $post_data['MERCHDESCR'] = $this->soft_descriptor;
            }
            $post_data['DESC'] = $post['creditcard_options'];
            $payer_name = $post['name'];
            $payer_name = array_map('trim', explode(' ', $payer_name));
            $post_data['FIRSTNAME'] = isset($payer_name[0]) ? $payer_name[0] : '';
            $post_data['LASTNAME'] = isset($payer_name[1]) ? $payer_name[1] : '';
            $this->payer_firstname = $post_data['FIRSTNAME'];
            $this->payer_lastname = $post_data['LASTNAME'];
            return $post_data;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function get_transaction_details($transaction_id = 0) {
        $url = $this->testmode ? $this->testurl : $this->liveurl;
        $post_data = array();
        $post_data['USER'] = $this->paypal_user;
        $post_data['VENDOR'] = $this->paypal_vendor;
        $post_data['PARTNER'] = $this->paypal_partner;
        $post_data['PWD'] = $this->paypal_password;
        $post_data['TRXTYPE'] = 'I';
        $post_data['ORIGID'] = $transaction_id;
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'body' => $post_data,
            'timeout' => 70,
            'user-agent' => 'PCCG',
            'httpversion' => '1.1'
        ));
        if (is_wp_error($response)) {
            if ($this->debug) {
                $log->add('PCCG_Paypal_Pro_Payflow', 'PCCG Paypal Pro Payflow Error', print_r($response->get_error_message(), true));
            }
            return false;
        }
        parse_str($response['body'], $parsed_response);
        if ($parsed_response['RESULT'] === '0') {
            return $parsed_response;
        }
        return false;
    }

}