<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://mbjtechnolabs.com/
 * @since      1.0.0
 *
 * @package    Credit_Card_Payment_Gateway
 * @subpackage Credit_Card_Payment_Gateway/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Credit_Card_Payment_Gateway
 * @subpackage Credit_Card_Payment_Gateway/admin
 * @author     mbjwplugindev <mbjwplugindev@gmail.com>
 */
class Credit_Card_Payment_Gateway_Admin {

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
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->load_dependencies();
        $this->define_constants();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-credit-card-payment-admin-display.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-credit-card-payment-general-setting.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-credit-card-payment-html-output.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/credit-card-payment-gateway-payment-pro.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/credit-card-payment-gateway-payment-payflow.php';
    }

    private function define_constants() {
        if (!defined('CCP_FOR_WORDPRESS_LOG_DIR')) {
            define('CCP_FOR_WORDPRESS_LOG_DIR', ABSPATH . 'credit-card-payment-logs/');
        }
    }

    /**
     * Register the stylesheets for the admin area.
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
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/credit-card-payment-gateway-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
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
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/credit-card-payment-gateway-admin.js', array('jquery'), $this->version, false);
    }

    public function credit_card_payment_getway_pccg_order() {
        global $wpdb;
        if (post_type_exists('pccg_order')) {
            return;
        }
        do_action('pccg_order_register_post_type');
        register_post_type('pccg_order', apply_filters('pccg_order_register_post_type', array(
            'labels' => array(
                'name' => __('Order List', 'credit-card-payment'),
                'singular_name' => __('PCCG List', 'credit-card-payment'),
                'menu_name' => _x('Order', 'Admin menu name', 'credit-card-payment'),
                'add_new' => __('Add PCCG List', 'credit-card-payment'),
                'add_new_item' => __('Add New PCCG List', 'credit-card-payment'),
                'edit' => __('Edit', 'credit-card-payment'),
                'edit_item' => __('View PCCG List', 'credit-card-payment'),
                'new_item' => __('New PCCG List', 'credit-card-payment'),
                'view' => __('View PCCG List', 'credit-card-payment'),
                'view_item' => __('View PCCG List', 'credit-card-payment'),
                'search_items' => __('Search PCCG List', 'credit-card-payment'),
                'not_found' => __('No PCCG List found', 'credit-card-payment'),
                'not_found_in_trash' => __('No PCCG List found in trash', 'credit-card-payment'),
                'parent' => __('Parent PCCG List', 'credit-card-payment')
            ),
            'description' => __('This is PCCG store.', 'credit-card-payment'),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => false,
            ),
            'map_meta_cap' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'hierarchical' => false,
            'rewrite' => array('slug' => 'pccg_order'),
            'query_var' => true,
            'menu_icon' => PCCG_PLUGIN_URL . 'admin/image/pccg.png',
            'supports' => array('', ''),
            'has_archive' => true,
            'show_in_nav_menus' => true
                        )
                )
        );
    }

    public function set_custom_edit_pccg_order_columns($columns) {
        unset($columns['date']);
        unset($columns['title']);
        $columns['id'] = __('Order', 'credit-card-payment');
        $columns['name'] = __('Payers Name', 'credit-card-payment');
        $columns['transaction_id'] = __('Transaction ID', 'credit-card-payment');
        $columns['amt'] = __('Amount', 'credit-card-payment');
        $columns['paymentstatus'] = __('Paymentstatus', 'credit-card-payment');
        $columns['paymentmethod'] = __('Paymentmethod', 'credit-card-payment');
        $columns['date'] = __('Date', 'credit-card-payment');
        return $columns;
    }

    public function custom_pccg_order_columns($column, $post_id) {

        global $post;

        $all_result_array = get_post_meta($post->ID, 'credit_card_payment_confirm');
        $result_array = $all_result_array[0];
        switch ($column) {
            case 'id' :
                echo $post->ID;
                break;
            case 'name' :
                echo $result_array['FIRSTNAME'];
                break;
            case 'transaction_id' :
                echo $post->post_content;
                break;
            case 'amt' :
                echo Credit_Card_Payment_Setting::get_credit_card_payment_symbol($result_array['CURRENCYCODE']) . '' . number_format($result_array['AMT'], 2);
                break;
            case 'paymentstatus' :
                echo $result_array['PAYMENTSTATUS'];
                break;
            case 'paymentmethod' :
                echo $result_array['PAYMENTMETHOD'];
                break;
            case 'date' :
                echo $post->post_date;
                break;
        }
    }

    public function pccg_order_table_sorting($columns) {
        $columns['id'] = 'id';
        $columns['name'] = 'name';
        $columns['transaction_id'] = 'transaction_id';
        $columns['amt'] = 'amt';
        $columns['paymentstatus'] = 'paymentstatus';
        $columns['paymentmethod'] = 'paymentmethod';
        $columns['date'] = 'date';
        return $columns;
    }

    public function page_create() {

        if (get_page_by_title('CCPay') == NULL) {
            $post = array(
                'comment_status' => 'open',
                'ping_status' => 'closed',
                'post_date' => date('Y-m-d H:i:s'),
                'post_name' => 'ccpay',
                'post_status' => 'publish',
                'post_title' => 'CCPay',
                'post_type' => 'page',
                'post_content' => '[display_credit_form]',
            );
            $newvalue = wp_insert_post($post, false);
        }
        if (get_page_by_title('CCThankyou') == NULL) {
            $post = array(
                'comment_status' => 'open',
                'ping_status' => 'closed',
                'post_date' => date('Y-m-d H:i:s'),
                'post_name' => 'ccthankyou',
                'post_status' => 'publish',
                'post_title' => 'CCThankyou',
                'post_type' => 'page',
                'post_content' => '[credit_card_payment_thankyou_page]',
            );
            $newvalue = wp_insert_post($post, false);
        }
    }

    public function payment_confirm() {
        if (isset($_POST['ccpayment']) && $_POST['ccpayment'] == 'Pay') {


            $PCCG_Paypal_Pro = (get_option('credit_card_payment_title_general_settings')) ? get_option('credit_card_payment_title_general_settings') : '';
            $PCCG_Paypal_Pro_Payflow = (get_option('pccg_pro_payflow_title_settings')) ? get_option('pccg_pro_payflow_title_settings') : '';

            if (isset($_POST['pccg_radio_button']) && !empty($_POST['pccg_radio_button']) && $PCCG_Paypal_Pro_Payflow == $_POST['pccg_radio_button']) {


                $card = new Credit_Card_Payment_Gateway_Payment_payflow();
                $result = $card->do_payment($_POST);

                if ("Success" == $result['status']) {
                    if (isset($result['custom_post_id'])) {
                        $location = add_query_arg('id', $result['custom_post_id'], $result['redirect']);
                        wp_redirect($location, 301);
                        exit;
                    }
                } else {
                    set_transient('responce_error_pccg', $result['status'], 20);
                    set_transient('responce_amt_pccg', $result['amt'], 20);
                    set_transient('responce_qty_pccg', $result['qty'], 20);
                    set_transient('responce_options_pccg', $result['pccg_option'], 20);
                    $location = add_query_arg($result['redirect']);
                    wp_redirect($location, 301);
                    exit;
                }
            } else if (isset($_POST['pccg_radio_button']) && !empty($_POST['pccg_radio_button']) && $PCCG_Paypal_Pro == $_POST['pccg_radio_button']) {

                $card = new Credit_Card_Payment_Gateway_Payment_Pro();
                $result = $card->do_payment($_POST);

                if ("Success" == $result['status']) {
                    if (isset($result['custom_post_id'])) {
                        $location = add_query_arg('id', $result['custom_post_id'], $result['redirect']);
                        wp_redirect($location, 301);
                        exit;
                    }
                } else {
                    set_transient('responce_error_pccg', $result['status'][0], 20);
                    set_transient('responce_amt_pccg', $result['amt'], 20);
                    set_transient('responce_qty_pccg', $result['qty'], 20);
                    set_transient('responce_options_pccg', $result['pccg_option'], 20);
                    $location = add_query_arg($result['redirect']);
                    wp_redirect($location, 301);
                    exit;
                }
            }
        }
    }

    public function pccg_media_button() {
        ?>

        <a href="javascript:;" class="button pccg_popup_container_button" style="background-color: #0091cd; border: 1px solid #0091cd;box-shadow: inset 0px 1px 0px 0px #0091cd;color: #FFFFFF;">PCCG Button</a>		
        <?php
        add_thickbox();
        echo '<a style="display: none;" href="#TB_inline?height=&amp;width=470&amp;&inlineId=pccg_popup_container" class="thickbox pccg_popup_container">PCCG Button</a>';
        ?>              
        <div id="pccg_popup_container" style="display: none;" class="wrap">  
            <div class="pccg-payment-form-style-9" id="pccg-payment-accordion">
                <ul>
                    <li>
                        <a href="#pccg_enable_table_border">Optional Shortcode</a>
                        <div id="pccg_enable_table_border" class="pccg-payment-accordion">
                            <div class="wrap" style="margin:0px;">
                                <table class="widefat">
                                    <tr>
                                        <td style="padding-top: 20px;font-size: 15px;">
                                            <input type="checkbox" id="pccg_payment_enable_quantity" name="pccg_payment_enable_quantity" value="">  Enable Quantity TaxtBox Front-End
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 20px;font-size: 15px;">
                                            <input type="checkbox" id="pccg_payment_enable_border" name="pccg_payment_enable_border" value=""> Enable Table Border Front-End
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select hidden style="height: 38px;" name="pccg_payment_table_border" id="pccg_payment_table_border" class="pccg-payment-field-style pccg-payment-class-select">
                                                <option value="0">Select Table Border</option>
                                                <option value="1">1px</option>
                                                <option value="2">2px</option>
                                                <option value="3">3px</option>
                                                <option value="4">4px</option>
                                                <option value="5">5px</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a href="#pccg_paypal_align">Set Button Align Front-End</a>
                        <div id="pccg_paypal_align" class="pccg-payment-accordion">
                            <div class="wrap" style="margin:0px;"><table class="widefat"><tr><td><select style="height: 38px;" name="pccg_payment_align" id="pccg_payment_align" class="pccg-payment-field-style pccg-payment-class-select"><option value="align">Set Button Alignment</option><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></td></tr></table></div>
                        </div>
                    </li>
                    <li>
                        <a href="#pccg_create_price_shortcode">Price Shortcode</a>
                        <div id="pccg_create_price_shortcode" class="pccg-payment-accordion">
                            <div class="wrap" style="margin:0px;"><table class="widefat"><tr><td><select style="height: 38px;" name="PCCG_payment_tab_price_shortcode_price" id="PCCG_payment_tab_price_shortcode_price" class="pccg-payment-field-style pccg-payment-class-select"><option value="none">Select Price Shortcode</option><option value="1">Simple Price Shortcode</option><option value="2">Options Price Shortcode</option></select></td></tr></table></div>
                            <div class="wrap pccg-payment-div-option-create-price"></div>                                                               
                        </div>
                    </li>
                    <li>
                        <a href="#pccg_create_custom_shortcode">Custom Shortcode</a>
                        <div id="pccg_create_custom_shortcode" style=" height: 330px;overflow: auto;" class="pccg-payment-accordion">
                            <div class="wrap" style="margin:0px;">                                

                                <table style="box-shadow: inset 0 0 10px green;" id="pccg-payment-table-0" class="widefat" data-custom="0">
                                    <tr>
                                        <td colspan="2">
                                            <input class="pccg_payment_add_new_custom_button" type="button" id="pccg_payment_add_new_custom_button" value="Add New Custom Option">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input style="height: 38px;width: 100%;" type = "text" name ="pccg_payment_custom_lable0" id = "pccg_payment_custom_lable0" class = "pccg-payment-field-style" placeholder = "Enter Custom Lable Name">
                                        </td>
                                    </tr>
                                    <tr id="pccg-payment-table-option-0" data-tr="0">
                                        <td>
                                            <input style="height: 38px;width: 90%;" type = "text" name = "on00" id = "on00" class = "pccg-payment-field-style" placeholder = "Key">
                                        </td>
                                        <td>
                                            <input style="height: 38px;width: 90%;" type = "text" name = "os00" id = "os00" class = "pccg-payment-field-style" placeholder = "Value">
                                            <span id="pccg-payment-add-row-0" class="pccg-payment-custom-add pccg-add-remove-icon-paypal" data-custom-span="0">
                                                <img src="<?php echo plugin_dir_url(__FILE__); ?>image/add.png">
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </li>
                </ul>                     
                <input class="pccg-payment-background-color-table" type="button" id="pccg_payment_insert" value="Create PCCG Button">     
                <input type="hidden" class="PCCG_PAYMENT_SITE_URL" name="PCCG_PAYMENT_SITE_URL" value="<?php echo plugin_dir_url(__FILE__); ?>">
                <input type="hidden" class="PCCG_PAYMENT_NUMBER_OF_TABLE" name="PCCG_PAYMENT_NUMBER_OF_TABLE" value="0">
            </div>
        </div>
        </form>
        <?php
    }

}