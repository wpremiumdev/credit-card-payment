<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://mbjtechnolabs.com/
 * @since             1.0.0
 * @package           Credit_Card_Payment_Gateway
 *
 * @wordpress-plugin
 * Plugin Name:       Credit Card Payment Gateway
 * Plugin URI:        credit-card-payment-gateway
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.1.1
 * Author:            mbjwplugindev
 * Author URI:        http://mbjtechnolabs.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       credit-card-payment-gateway
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
if (!defined('CCP_FOR_WORDPRESS_LOG_DIR')) {
    $upload_dir = wp_upload_dir();
    define('CCP_FOR_WORDPRESS_LOG_DIR', $upload_dir['basedir'] . '/credit-card-payment-logs/');
}
if (!defined('PCCG_PLUGIN_URL'))
    define('PCCG_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-credit-card-payment-gateway-activator.php
 */
function activate_credit_card_payment_gateway() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-credit-card-payment-gateway-activator.php';
    Credit_Card_Payment_Gateway_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-credit-card-payment-gateway-deactivator.php
 */
function deactivate_credit_card_payment_gateway() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-credit-card-payment-gateway-deactivator.php';
    Credit_Card_Payment_Gateway_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_credit_card_payment_gateway');
register_deactivation_hook(__FILE__, 'deactivate_credit_card_payment_gateway');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-credit-card-payment-gateway.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_credit_card_payment_gateway() {

    $plugin = new Credit_Card_Payment_Gateway();
    $plugin->run();
}

add_action('plugins_loaded', 'credit_card_payment_gateway', 99);

function credit_card_payment_gateway() {
    run_credit_card_payment_gateway();
}