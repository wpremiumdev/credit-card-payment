<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://mbjtechnolabs.com/
 * @since      1.0.0
 *
 * @package    Credit_Card_Payment_Gateway
 * @subpackage Credit_Card_Payment_Gateway/admin/partials
 */
class Credit_Card_Payment_Admin_Display {

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_settings_menu'));
    }

    public static function add_settings_menu() {
        add_options_page('Credit Card Payment Option', 'Payment Credit Card', 'manage_options', 'credit-card-payment', array(__CLASS__, 'credit_card_payment'));
    }

    public static function credit_card_payment() {
        $setting_tabs = apply_filters('credit_card_options_setting_tab', array('pccg_general' => 'General', 'pccg_pro' => 'Paypal Pro', 'pccg_payflow' => 'Paypal Pro PayFlow'));
        $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'pccg_general';
        ?>
        <h2 class="nav-tab-wrapper">
        <?php
        foreach ($setting_tabs as $name => $label)
            echo '<a href="' . admin_url('admin.php?page=credit-card-payment&tab=' . $name) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
        ?>
        </h2>
            <?php
            foreach ($setting_tabs as $setting_tabkey => $setting_tabvalue) {
                switch ($setting_tabkey) {
                    case $current_tab:
                        do_action('credit_card_payment_' . $setting_tabkey . '_setting_save_field');
                        do_action('credit_card_payment_' . $setting_tabkey . '_setting');
                        break;
                }
            }
        }

    }

    Credit_Card_Payment_Admin_Display::init();