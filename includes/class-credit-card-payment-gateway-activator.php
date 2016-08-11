<?php

/**
 * Fired during plugin activation
 *
 * @link       http://mbjtechnolabs.com/
 * @since      1.0.0
 *
 * @package    Credit_Card_Payment_Gateway
 * @subpackage Credit_Card_Payment_Gateway/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Credit_Card_Payment_Gateway
 * @subpackage Credit_Card_Payment_Gateway/includes
 * @author     mbjwplugindev <mbjwplugindev@gmail.com>
 */
class Credit_Card_Payment_Gateway_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        self::create_files();
    }

    public static function create_files() {
        $upload_dir = wp_upload_dir();
        $files = array(
            array(
                'base' => CCP_FOR_WORDPRESS_LOG_DIR,
                'file' => '.htaccess',
                'content' => 'deny from all'
            ),
            array(
                'base' => CCP_FOR_WORDPRESS_LOG_DIR,
                'file' => 'index.html',
                'content' => ''
            )
        );
        foreach ($files as $file) {
            if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
                if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }

}