<?php
ob_start();
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://mbjtechnolabs.com/
 * @since      1.0.0
 *
 * @package    Credit_Card_Payment_Gateway
 * @subpackage Credit_Card_Payment_Gateway/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Credit_Card_Payment_Gateway
 * @subpackage Credit_Card_Payment_Gateway/public
 * @author     mbjwplugindev <mbjwplugindev@gmail.com>
 */
class Credit_Card_Payment_Gateway_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_shortcode('credit_card_payment_code', array(__CLASS__, 'credit_card_payment_button_wordpress'));
        add_shortcode('display_credit_form', array(__CLASS__, 'display_credit_form_own'));
        add_shortcode('credit_card_payment_thankyou_page', array(__CLASS__, 'credit_card_payment_thankyou_page'));
        add_shortcode('credit_card_payment_error_page', array(__CLASS__, 'credit_card_payment_error_page'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Credit_Card_Payment_Gateway_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Credit_Card_Payment_Gateway_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name . 'public', plugin_dir_url(__FILE__) . 'css/credit-card-payment-gateway-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Credit_Card_Payment_Gateway_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Credit_Card_Payment_Gateway_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        global $post;
        $current_page_id = $post->ID;
        $page_select_load_card_js_id = get_option('credit_card_payment_credit_card_form_general_settings');
        if ((!isset($page_select_load_card_js_id) && empty($page_select_load_card_js_id)) || $page_select_load_card_js_id == false) {
            $page_select_load_card_js_id = $this->get_defualt_page_selected();
        }
        if ($current_page_id == $page_select_load_card_js_id) {
            wp_enqueue_script($this->plugin_name . 'card', plugin_dir_url(__FILE__) . 'lib/js/card.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . 'public', plugin_dir_url(__FILE__) . 'js/credit-card-payment-gateway-public.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . 'twitter_boostrap', plugin_dir_url(__FILE__) . 'js/credit-card-payment-gateway-public_twitter_boostrap.js', array('jquery'), $this->version, false);
        }
    }

    public static function credit_card_payment_button_wordpress($atts) {

        global $custom_shortcode_page_id;
        $button_url = "";
        $pccg_dropdown_string = "";
        $pccg_payment_table_border = 0;
        $pccg_form_alignment = "";
        if (isset($atts['border']) && !empty($atts['border'])) {
            $pccg_payment_table_border = ($atts['border']) ? $atts['border'] : 0;
        }
        if (isset($atts['align']) && !empty($atts['align'])) {
            $pccg_form_alignment = ($atts['align']) ? $atts['align'] : '';
        }
        $set_amount = '';
        $pccg_quantity = '';
        $string = '';
        $string_tr = '';
        $amount_empty_error = get_transient('empty_amount_paypal_pro_and_payflow');
        if (isset($amount_empty_error) && !empty($amount_empty_error)) {
            $amount_error_msg = get_transient('empty_amount_paypal_pro_and_payflow');
            delete_transient('empty_amount_paypal_pro_and_payflow');
            $string .='<tr><td><div class="alert alert-info"><strong></strong>' . $amount_error_msg . '</div></td></tr>';
        }
        ?>
        <style>
            #credit_card_payment td{
                border-top: <?php echo $pccg_payment_table_border; ?>px solid #ddd;
            }
            #credit_card_payment table{               
                border-bottom: <?php echo $pccg_payment_table_border; ?>px solid #ddd;
                margin: auto;
                width: auto;
            }
        </style>
        <?php
        if (isset($atts) && !empty($atts)) {
            if (is_array($atts)) {
                $pccg_dropdown_string = self::pccg_create_dropdown_option_button($atts);
            }
        }

        if (isset($atts['quantity']) && !empty($atts['quantity'])) {
            $pccg_quantity = '<tr><td><input type="text" name="quantity" value="" placeholder="Quantity"></td></tr>';
        }

        if (isset($atts['price']) && !empty($atts['price'])) {
            $pccg_payment_amount = ($atts['price']) ? $atts['price'] : '';
            $set_amount = '<input type="hidden" name="creditcard_pay_amount" value="' . esc_attr($pccg_payment_amount) . '">';
        } elseif (isset($atts['pccg_0']) && !empty($atts['pccg_0'])) {
            $set_amount = self::pccg_create_dropdown_option_button_option_code($atts['pccg_0'], $atts);
        }

        $button_name = get_option('credit_card_payment_button_general_settings');
        $button_url = self::get_button_shortcode($button_name);
        $output = '';
        ob_start();
        $page_select_id = (get_option('credit_card_payment_credit_card_form_general_settings')) ? get_option('credit_card_payment_credit_card_form_general_settings') : '';

        if (isset($page_select_id) && empty($page_select_id)) {
            $page_select_id = self::get_defualt_page_selected();
        }
        if (isset($string) && !empty($string)) {
            $string_tr .= $string;
        }

        $output .= '<div class="page-sidebar widget" id="credit_card_payment" style="background:none;">';
        $output .= '<form action="' . get_permalink($page_select_id) . '" method="post" target="_blank" align="' . $pccg_form_alignment . '">';
        if (isset($pccg_dropdown_string) && !empty($pccg_dropdown_string)) {
            $output .= '<table align="' . $pccg_form_alignment . '"><tbody>' . $string_tr . $set_amount . $pccg_dropdown_string . $pccg_quantity . '<tr><td><input type="image" name="submit" style="" border="0" src="' . esc_url($button_url) . '" alt="PayPal - The safer, easier way to pay online"></td></tr></tbody></table>';
        } else {
            $output .= '<table align="' . $pccg_form_alignment . '"><tbody>' . $string_tr . $set_amount . $pccg_quantity . '<tr><td><input type="image" name="submit" style="" border="0" src="' . esc_url($button_url) . '" alt="PayPal - The safer, easier way to pay online"></td></tr></tbody></table>';
        }
        $output .= '<input type="hidden" name="custom" value="' . get_the_ID() . '">';

        $output .= '</form></div>';
        return $output;
        return ob_get_clean();
    }

    public static function get_button_shortcode($button_name) {
        try {
            $result = plugins_url() . "/credit-card-payment/admin/image/6.png";

            for ($i = 1; $i <= 12; $i++) {
                if ('button12' == $button_name) {
                    $result = get_option('credit_card_payment_custom_button_general_settings');
                } elseif ('button' . $i == $button_name) {
                    $result = plugins_url() . "/credit-card-payment/admin/image/" . $i . ".png";
                }
            }
            return $result;
        } catch (Exception $ex) {
            
        }
    }

    public static function pccg_create_dropdown_option_button($atts) {

        $result = "";
        $loop_count = 0;
        if (isset($atts['pccg_name']) && !empty($atts['pccg_name'])) {
            $lable_name = self::pccg_get_Lable_name($atts['pccg_name']);
            if (isset($atts['pccg_0']) && !empty($atts['pccg_0'])) {
                unset($lable_name[0]);
                $lable_name = array_values($lable_name);
            }
            foreach ($atts as $key => $value) {
                if ("price" != $key && "pccg_name" != $key && "align" != $key && "border" != $key && "pccg_0" != $key && "quantity" != $key) {
                    $result .= self::pccg_array_value_replace_hear($lable_name[$loop_count], $value, $loop_count);
                    $loop_count++;
                }
            }
            return $result;
        }
    }

    public static function pccg_create_dropdown_option_button_option_code($atts, $lable_name) {
        $result = "";
        $currency_selected = get_option('credit_card_payment_currency_general_settings');

        if (isset($currency_selected) && empty($currency_selected)) {
            $currency_selected = "USD";
        }
        $currency_symbol = self::get_credit_card_currency_payment_symbol($currency_selected);
        $lable_name = self::pccg_get_Lable_name($lable_name['pccg_name']);

        $result .= self::pccg_array_value_replace_hear_price($lable_name[0], $atts, $currency_symbol, $currency_selected);
        unset($lable_name[0]);
        $lable_name = array_values($lable_name);

        return $result;
    }

    public static function get_credit_card_currency_payment_symbol($currency) {

        $currency_symbol = '';

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

    public static function pccg_array_value_replace_hear_price($lable, $data, $currency_symbol, $currency_selected) {

        $result = "<tr><td><input type='hidden' name='option_price_hidden' value='" . $lable . "'>" . $lable . "</td></tr><tr><td><select name='creditcard_pay_amount'>";
        $string = "";
        $data = trim($data);
        $data = trim($data);
        $sub_option = explode(' | ', $data);
        foreach ($sub_option as $key => $value) {
            $array_export_data = array();
            $array_export_data = self::pccg_value_expload_with_regex($value);
            $string .= "<option value=\"" . $array_export_data['key'] . "\">" . $array_export_data['value'] . ' - ' . $currency_symbol . $array_export_data['key'] . ' ' . $currency_selected . "</option>";
        }
        $result .= $string . "</select></td></tr>";
        return $result;
    }

    public static function pccg_array_value_replace_hear($lable, $data, $i) {

        $result = "<tr><td><input type='hidden' name='on" . $i . "' value='" . $lable . "'>" . $lable . "</td></tr><tr><td><select name='os" . $i . "'>";
        $string = "";
        $data = trim($data);
        $data = trim($data);
        $sub_option = explode(' | ', $data);
        foreach ($sub_option as $key => $value) {
            $array_export_data = array();
            $array_export_data = self::pccg_value_expload_with_regex($value);
            $string .= "<option value=\"" . $array_export_data['key'] . "\">" . $array_export_data['value'] . "</option>";
        }
        $result .= $string . "</select></td></tr>";
        return $result;
    }

    public static function pccg_value_expload_with_regex($value) {
        $result_array = array();

        $value_regex = "/value=('|\")+[^*]+(price=)/";
        $price_regex = "/price=('|\")+[^*]+/";
        //$value_regex = "/value=('|\")+[^('|\")]+/";
        //$price_regex = "/price=('|\")+[^('|\")]+/";
        $value_name = preg_match($value_regex, $value, $matches_out_value);
        $price_name = preg_match($price_regex, $value, $matches_out_price);
        $matches_out_value[0] = str_replace(" price=", "", $matches_out_value[0]);
        $result_array['value'] = trim(str_replace("value='", "", $matches_out_value[0]), "'");
        $result_array['key'] = trim(str_replace("price='", "", $matches_out_price[0]), "'");

        return $result_array;
    }

    public static function pccg_get_Lable_name($data) {
        $result = "";
        $result = explode(', ', $data);
        return $result;
    }

    public static function get_defualt_page_selected() {
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

    public static function display_credit_form_own($atts) {

        $enable_pro = (get_option('credit_card_payment_enable')) ? get_option('credit_card_payment_enable') : '';
        $enable_pro_payflow = (get_option('pccg_pro_payflow_enable')) ? get_option('pccg_pro_payflow_enable') : '';
        $button_hidden_no_data = '';
        $i = 0;
        $str_result_option = "";
        while (isset($_POST['on' . $i])) {

            $on = (isset($_POST['on' . $i])) ? $_POST['on' . $i] : false;
            $os = (isset($_POST['os' . $i])) ? $_POST['os' . $i] : false;

            $str_result_option .= strtoupper($on) . ': ' . strtoupper($os) . ', ';
            $i++;
        }

        $option_string = trim($str_result_option, ', ');
        ?>
        <script>
            jQuery('.payment_process_button').show();
            jQuery('.payment_process_bar').hide();
        </script>    
        <?php
        $output = '';
        $string = '';
        $get_amount = '';
        $get_quantity = 1;
        $payment_button_dispaly = '';

        if (isset($_POST['creditcard_pay_amount']) && !empty($_POST['creditcard_pay_amount'])) {
            $get_amount = (isset($_POST['creditcard_pay_amount'])) ? $_POST['creditcard_pay_amount'] : '';
        }

        if (isset($_POST['quantity']) && !empty($_POST['quantity'])) {
            $get_quantity = (isset($_POST['quantity'])) ? $_POST['quantity'] : '';
        }

        $error_message = "";
        $error_pccg_message = get_transient('responce_error_pccg');

        if (!empty($error_pccg_message) && isset($error_pccg_message)) {
            $get_amount = get_transient('responce_amt_pccg');
            $get_quantity = get_transient('responce_qty_pccg');
            $error_message = get_transient('responce_error_pccg');
            $option_string = get_transient('responce_options_pccg');
            delete_transient('responce_options_pccg');
            delete_transient('responce_amt_pccg');
            delete_transient('responce_error_pccg');
            $string .='<div class="alert alert-error"><strong>Error! </strong>' . $error_message . '</div>';
        }
        if (isset($string) && !empty($string)) {
            $output .= $string;
        }
        $get_enable_methods = self::get_enabal_methods();
        if (isset($get_amount) && !empty($get_amount)) {
            $radio_button_div_create = '';
            if (is_array($get_enable_methods) && count($get_enable_methods) > 0) {

                $radio_button_string = self::get_enable_methods_required_data($get_enable_methods);

                if (false == $radio_button_string) {
                    $radio_button_div_create = '<div class="ccp-checkout" style="padding-bottom: 12px;"><div class="alert alert-error" style="text-align:center">Paypal Payment Methods Required Field Empty!.</div></div>';
                    $payment_button_dispaly = '';
                } else {
                    $radio_button_div_create = $radio_button_string;
                    $payment_button_dispaly = '<p class="payment_process_button"><input type="submit" id="ccpayment" name="ccpayment" value="Pay" class="ccp-checkout-btn"></p>';
                }
            } else {
                $radio_button_div_create = '<div class="ccp-checkout" style="padding-bottom: 12px;"><div class="alert alert-error" style="text-align:center"> Please Enable Payment Methods. </div></div>';
                $payment_button_dispaly = '';
            }
            $output .= '<div class="demo-container">
                        <div class="card-wrapper"></div>
                            <div class="form-container active">                                                        
                                <form class="" method="post" action="" id="credit_card_payment_data"> 
                                    ' . $radio_button_div_create . '
                                    <div class="ccp-checkout">
                                    <div class="show_empty_fileds" hidden></div>                                    
                                    <input type="hidden" name="creditcard_pay_amount" value="' . $get_amount . '">
                                    <input type="hidden" name="creditcard_pay_quantity" value="' . $get_quantity . '">
                                        <input type="hidden" name="creditcard_options" value="' . $option_string . '">
                                    <input type="hidden" class="creditcard_type" name="creditcard_type" value="">
                                    <p><input type="text" id="number" name="number" class="ccp-checkout-input ccp-checkout-card credit_card_number" placeholder="•••• •••• •••• ••••"></p>
                                    <p><input type="text" id="name" name="name" class="ccp-checkout-input ccp-checkout-name credit_card_user_name" placeholder="Your name" autofocus></p>                                 
                                    <p>
                                        <input placeholder="MM / YYYY" type="text" name="expiry" id="expiry" class="ccp-checkout-input ccp-checkout-exp credit_card_expire_date">
                                        <input type="text" id="cvc" name="credit_card_csv" class="ccp-checkout-input ccp-checkout-cvc credit_card_csv" placeholder="CVC">
                                    </p>
                                    ' . $payment_button_dispaly . '
                                    <p class="payment_process_bar" style="padding: 0px; text-align: center;" hidden>
                                        <img src="' . plugins_url() . '/credit-card-payment/admin/image/loading.GIF">
                                    </p>
                                   </div>
                                </form>
                            </div>
                  </div>';
            return $output;
        } else {

            if (isset($_POST['custom']) && !empty($_POST['custom'])) {
                set_transient('empty_amount_paypal_pro_and_payflow', ' Please Set Price in Shortcode ', 20);
                $redirrect_id = $_POST['custom'];
                $redirect_link = get_permalink($redirrect_id);
                wp_redirect($redirect_link, 301);
                exit;
            } else {
                wp_redirect(home_url());
                exit;
            }
        }
    }

    public static function get_enabal_methods() {
        $is_methods_enable = array();
        $enable_pro = (get_option('credit_card_payment_enable')) ? get_option('credit_card_payment_enable') : '';
        $enable_pro_payflow = (get_option('pccg_pro_payflow_enable')) ? get_option('pccg_pro_payflow_enable') : '';
        if ((isset($enable_pro) && !empty($enable_pro) && 'yes' == $enable_pro ) || (isset($enable_pro_payflow) && !empty($enable_pro_payflow) && 'yes' == $enable_pro_payflow )) {

            if ((isset($enable_pro) && !empty($enable_pro) && 'yes' == $enable_pro)) {
                $is_methods_enable['paypal_pro'] = 'paypal pro';
            }

            if ((isset($enable_pro_payflow) && !empty($enable_pro_payflow) && 'yes' == $enable_pro_payflow)) {
                $is_methods_enable['paypal_payflow'] = 'paypal payflow';
            }
            return $is_methods_enable;
        }
        return false;
    }

    public static function credit_card_payment_thankyou_page() {
        try {
            $output = '';
            if (isset($_GET['id']) && !empty($_GET['id'])) {
                $result = get_post_meta($_GET['id'], 'credit_card_payment_confirm');
                $result = $result[0];
                if (isset($result['ACK']) && "Success" == $result['ACK']) {
                    $output .= '<table class="widefat" cellspacing="0" ><tbody class=""><tr><td colspan="5"><div class="alert alert-success"><strong>Success! </strong>You have successfully Credit Card Payment Pro done it.</div></td></tr>';
                    $output .= '<tr>                         
                         <td>ORDER NUMBER:<br><b>' . $_GET['id'] . '</b></td>                        
                         <td>DATE:<br><b>' . date('d-F-Y', strtotime($result['ORDERTIME'])) . '</b></td>                         
                         <td>TOTAL:<br><b>' . $result['AMT'] . '</b></td>                         
                         <td>PAYMEN METHOD:<br><b>' . $result['PAYMENTMETHOD'] . '</b></td>
                        </tr>';
                    $output .= '</tbody></table>';
                } else if (in_array($result['RESULT'], array(0, 126, 127))) {
                    $output .= '<table class="widefat" cellspacing="0" ><tbody class=""><tr><td colspan="5"><div class="alert alert-success"><strong>Success! </strong>You have successfully Credit Card Payment Pro Payflow done it.</div></td></tr>';
                    $output .= '<tr>                         
                         <td>ORDER NUMBER:<br><b>' . $_GET['id'] . '</b></td>                        
                         <td>DATE:<br><b>' . date('d-F-Y', strtotime($result['ORDERTIME'])) . '</b></td>                         
                         <td>TOTAL:<br><b>' . number_format($result['AMT'], 2) . '</b></td>                        
                         <td>PAYMEN METHOD:<br><b>' . $result['PAYMENTMETHOD'] . '</b></td>
                        </tr>';
                    $output .= '</tbody></table>';
                }
            } else {
                wp_redirect(home_url());
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return $output;
    }

    public static function get_enable_methods_required_data($get_enable_methods) {
        try {

            $output = '';
            $result = '';

            if (array_key_exists('paypal_pro', $get_enable_methods)) {

                $result .= self::get_paypal_pro_detail();
            }

            if (array_key_exists('paypal_payflow', $get_enable_methods)) {

                $result .= self::get_paypal_payflow_detail();
            }

            if (isset($result) && !empty($result)) {
                $output = '<div class="ccp-checkout">' . $result . '</div>';
            } else {
                $output = false;
            }

            return $output;
        } catch (Exception $ex) {
            
        }
    }

    public static function get_paypal_pro_detail() {

        try {
            $result = '';
            $api = get_option('credit_card_payment_api_username_general_settings');
            $password = get_option('credit_card_payment_api_password_general_settings');
            $signature = get_option('credit_card_payment_api_signature_general_settings');
            $pro_title = (get_option('credit_card_payment_title_general_settings')) ? get_option('credit_card_payment_title_general_settings') : 'pro';
            if ((isset($api) && !empty($api)) && (isset($password) && !empty($password)) && (isset($signature) && !empty($signature))) {
                $result = ' <div style="clear: both;margin-bottom: 10px;">
                            <input type="radio" name="pccg_radio_button" id="pccg0" value="' . $pro_title . '" class="switch pccg_list_radio" checked/>
                            <label for="pccg0" title="">' . $pro_title . '</label>
                            </div>';
            }

            return $result;
        } catch (Exception $e) {
            
        }
    }

    public static function get_paypal_payflow_detail() {

        try {
            $result = '';
            $checked_radio = '';
            $vendor = get_option('pccg_pro_payflow_vendor_settings');
            $password = get_option('pccg_pro_payflow_password_settings');
            $partner = get_option('pccg_pro_payflow_partner_settings');
            $enable_pro = (get_option('credit_card_payment_enable')) ? get_option('credit_card_payment_enable') : '';
            $payflow_title = (get_option('pccg_pro_payflow_title_settings')) ? get_option('pccg_pro_payflow_title_settings') : 'payflow';
            if ((isset($vendor) && !empty($vendor)) && (isset($password) && !empty($password)) && (isset($partner) && !empty($partner))) {

                $is_checked = self::get_paypal_pro_detail();

                if ('no' == $enable_pro || empty($is_checked)) {
                    $checked_radio = 'checked';
                }

                $result = '<div style="clear: both; margin-bottom: 10px;">
                            <input type="radio" name="pccg_radio_button" id="pccg1" value="' . $payflow_title . '" class="switch pccg_list_radio" ' . $checked_radio . '/>
                            <label for="pccg1" title="">' . $payflow_title . '</label>
                            </div>';
            }

            return $result;
        } catch (Exception $e) {
            
        }
    }

}