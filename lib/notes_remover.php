<?php

class WebWeb_WP_NotesRemover {
    private $log_file = null;
    private $permalinks = 0;
    private static $instance = null; // singleton
    private $site_url = null; // filled in later
    private $plugin_url = null; // filled in later
    private $plugin_settings_key = null; // filled in later
    private $plugin_dir_name = null; // filled in later
    private $plugin_data_dir = null; // plugin data directory. for reports and data storing. filled in later
    private $plugin_name = 'WP NotesRemover'; //
    private $plugin_id_str = 'wp_notes_remover'; //
    private $plugin_business_sandbox = false; // sandbox or live ???
    private $plugin_business_email_sandbox = 'seller_1264288169_biz@slavi.biz'; // used for paypal payments
    private $plugin_business_email = 'billing@Orbisius.com'; // used for paypal payments
    private $plugin_business_ipn = 'http://orbisius.com/wp/hosted/payment/ipn.php'; // used for paypal IPN payments
    //private $plugin_business_status_url = 'http://localhost/wp/hosted/payment/status.php'; // used after paypal TXN to to avoid warning of non-ssl return urls
    private $plugin_business_status_url = 'https://ssl.orbisius.com/Orbisius.com/wp/hosted/payment/status.php'; // used after paypal TXN to to avoid warning of non-ssl return urls
    private $plugin_support_email = 'help@orbisius.com'; //
    private $plugin_support_link = 'http://miniads.ca/widgets/contact/profile/wp-notes-remover/?height=200&width=500&description=Please enter your enquiry below.'; //
    private $plugin_admin_url_prefix = null; // filled in later
    private $plugin_home_page = 'http://orbisius.com/products/wordpress-plugins/wp-notes-remover/';
    private $plugin_tinymce_name = 'wwwpdigishop'; // if you change it update the tinymce/editor_plugin.js and reminify the .min.js file.
    private $plugin_cron_hook = __CLASS__;
    private $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
    private $paypal_submit_image_src = 'https://www.paypal.com/en_GB/i/btn/btn_buynow_LG.gif';
    private $db_version = '1.0';
    private $plugin_cron_freq = 'daily';
    private $plugin_default_opts = array(
        'status' => 0,
    );

	private $app_title = 'Removes unuseful notes from WordPress!';
	private $plugin_description = '';

    private $plugin_uploads_path = null; // E.g. /wp-content/uploads/PLUGIN_ID_STR/
    private $plugin_uploads_url = null; // E.g. http://yourdomain/wp-content/uploads/PLUGIN_ID_STR/
    private $plugin_uploads_dir = null; // E.g. DOC_ROOT/wp-content/uploads/PLUGIN_ID_STR/

    private $download_key = null; // the param that will hold the download hash
    private $web_trigger_key = null; // the param will trigger something to happen. (e.g. PayPal IPN, test check etc.)

    // can't be instantiated; just using get_instance
    private function __construct() {

    }

    /**
     * handles the singleton
     */
    public static function get_instance() {
		if (is_null(self::$instance)) {
            global $wpdb;

			$cls = __CLASS__;
			$inst = new $cls;

			$site_url = site_url('/');
			$site_url = rtrim($site_url, '/') . '/'; // e.g. http://domain.com/blog/

			$inst->site_url = $site_url;
			$inst->plugin_dir_name = basename(dirname(WEBWEB_WP_NOTES_REMOVER_PLUGIN_FILE)); // e.g. wp-command-center; this can change e.g. a 123 can be appended if such folder exist
			$inst->plugin_data_dir = dirname(WEBWEB_WP_NOTES_REMOVER_PLUGIN_FILE) . '/data';
			$inst->plugin_url = plugins_url('/', WEBWEB_WP_NOTES_REMOVER_PLUGIN_FILE);
			$inst->plugin_settings_key = $inst->plugin_id_str . '_settings';
            $inst->plugin_support_link .= '&css_file=' . urlencode(get_bloginfo('stylesheet_url'));
            $inst->plugin_admin_url_prefix = $site_url . 'wp-admin/admin.php?page=' . $inst->plugin_dir_name;

            $opts = $inst->get_options();

			add_action('plugins_loaded', array($inst, 'init'), 100);

			define('WebWeb_WP_NotesRemover_BASE_DIR', dirname(WEBWEB_WP_NOTES_REMOVER_PLUGIN_FILE)); // e.g. // htdocs/wordpress/wp-content/plugins/wp-command-center
			define('WebWeb_WP_NotesRemover_DIR_NAME', $inst->plugin_dir_name);

            self::$instance = $inst;
        }

		return self::$instance;
	}

    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup() {
        trigger_error('Unserializing is not allowed.', E_USER_ERROR);
    }

    /**
     * Logs whatever is passed IF logs are enabled.
     */
    function log($msg = '') {
        if ($this->log_enabled) {
            $msg = '[' . date('r') . '] ' . '[' . $_SERVER['REMOTE_ADDR'] . '] ' . $msg . "\n";
            error_log($msg, 3, $this->log_file);
        }
    }

    function enqueue_assets() {
        wp_register_style($this->plugin_dir_name, $this->plugin_url . 'css/main.css', false, 0.1);
        wp_enqueue_style($this->plugin_dir_name);
    }

    /**
     * handles the init
     */
    function init() {
        global $wpdb;

        if (is_admin()) {
            // Administration menus
            add_action('admin_menu', array($this, 'administration_menu'));
            add_action('admin_init', array($this, 'register_settings'));
			add_action('admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 20 );
        } else {
            add_action('wp_head', array($this, 'add_plugin_credits'), 1); // be the first in the header
            add_action('wp_head', array($this, 'process'), 999); // be the second to last in the footer
            add_action('wp_footer', array($this, 'add_plugin_credits'), 1000); // be the last in the footer
        }
    }

    /**
     * Handles the plugin activation. creates db tables and uploads dir with an htaccess file
     */
    function on_activate() {
    }

    /**
     * Handles the plugin deactivation.
     */
    function on_deactivate() {
        $opts['status'] = 0;
        $this->set_options($opts);
    }

    /**
     * Handles the plugin uninstallation.
     */
    function on_uninstall() {
        delete_option($this->plugin_settings_key);
    }

    /**
     * Allows access to some private vars
     * @param str $var
     */
    public function get($var) {
        if (isset($this->$var) /* && (strpos($var, 'plugin') !== false) */) {
            return $this->$var;
        }
    }

    /**
     * gets current options and return the default ones if not exist
     * @param void
     * @return array
     */
    function get_options() {
        $opts = get_option($this->plugin_settings_key);
        $opts = empty($opts) ? array() : (array) $opts;

        // if we've introduced a new default key/value it'll show up.
        $opts = array_merge($this->plugin_default_opts, $opts);

        return $opts;
    }

    /**
     * Updates options but it merges them unless $override is set to 1
     * that way we could just update one variable of the settings.
     */
    function set_options($opts = array(), $override = 0) {
        if (!$override) {
            $old_opts = $this->get_options();
            $opts = array_merge($old_opts, $opts);
        }

        update_option($this->plugin_settings_key, $opts);

        return $opts;
    }

    /**
     * This is what the plugin admins will see when they click on the main menu.
     * @var string
     */
    private $plugin_landing_tab = '/menu.dashboard.php';

    /**
     * Adds the settings in the admin menu
     */
    public function administration_menu() {
        // Settings > DigiShop
        add_options_page(__($this->plugin_name, "WebWeb_WP_NotesRemover"), __($this->plugin_name, "WebWeb_WP_NotesRemover"),
                'manage_options', $this->plugin_dir_name . '/menu.settings.php');

        // when plugins are show add a settings link near my plugin for a quick access to the settings page.
        add_filter('plugin_action_links', array($this, 'add_plugin_settings_link'), 10, 2);
    }

    /**
     * Allows access to some private vars
     * @param str $var
     */
    public function generate_newsletter_box($params = array()) {
        $file = WebWeb_WP_NotesRemover_BASE_DIR . '/zzz_newsletter_box.html';

        $buffer = WebWeb_WP_NotesRemoverUtil::read($file);

        wp_get_current_user();
        global $current_user;
        $user_email = $current_user->user_email;

        $replace_vars = array(
            '%%PLUGIN_URL%%' => $this->get('plugin_url'),
            '%%USER_EMAIL%%' => $user_email,
            '%%PLUGIN_ID_STR%%' => $this->get('plugin_id_str'),
            '%%admin_sidebar%%' => $this->get('plugin_id_str'),
        );

        if (!empty($params['form_only'])) {
            $replace_vars['NEWSLETTER_QR_EXTRA_CLASS'] = "app_hide";
        } else {
            $replace_vars['NEWSLETTER_QR_EXTRA_CLASS'] = "";
        }

        if (!empty($params['src2'])) {
            $replace_vars['SRC2'] = $params['src2'];
        } elseif (!empty($params['SRC2'])) {
            $replace_vars['SRC2'] = $params['SRC2'];
        }

        $buffer = WebWeb_WP_NotesRemoverUtil::replace_vars($buffer, $replace_vars);

        return $buffer;
    }

    /**
     * Allows access to some private vars
     * @param str $var
     */
    public function generate_donate_box() {
        $msg = '';
        $file = WebWeb_WP_NotesRemover_BASE_DIR . '/zzz_donate_box.html';

        if (!empty($_REQUEST['error'])) {
            $msg = $this->message('There was a problem with the payment.');
        }

        if (!empty($_REQUEST['ok'])) {
            $msg = $this->message('Thank you so much!', 1);
        }

        $return_url = WebWeb_WP_NotesRemoverUtil::add_url_params($this->get('plugin_business_status_url'), array(
            'r' => $this->get('plugin_admin_url_prefix') . '/menu.dashboard.php&ok=1', // paypal de/escapes
            'status' => 1,
        ));

        $cancel_url = WebWeb_WP_NotesRemoverUtil::add_url_params($this->get('plugin_business_status_url'), array(
            'r' => $this->get('plugin_admin_url_prefix') . '/menu.dashboard.php&error=1', //
            'status' => 0,
        ));

        $replace_vars = array(
            '%%MSG%%' => $msg,
            '%%AMOUNT%%' => '2.99',
            '%%BUSINESS_EMAIL%%' => $this->plugin_business_email,
            '%%ITEM_NAME%%' => $this->plugin_name . ' Donation',
            '%%ITEM_NAME_REGULARLY%%' => $this->plugin_name . ' Donation (regularly)',
            '%%PLUGIN_URL%%' => $this->get('plugin_url'),
            '%%CUSTOM%%' => http_build_query(array('site_url' => $this->site_url, 'product_name' => $this->plugin_id_str)),
            '%%NOTIFY_URL%%' => $this->get('plugin_business_ipn'),
            '%%RETURN_URL%%' => $return_url,
            '%%CANCEL_URL%%' => $cancel_url,
        );

        // Let's switch the Sandbox settings.
        if ($this->plugin_business_sandbox) {
            $replace_vars['paypal.com'] = 'sandbox.paypal.com';
            $replace_vars['%%BUSINESS_EMAIL%%'] = $this->plugin_business_email_sandbox;
        }

        $buffer = WebWeb_WP_NotesRemoverUtil::read($file);
        $buffer = str_replace(array_keys($replace_vars), array_values($replace_vars), $buffer);

        return $buffer;
    }

    /**
     * Outputs some options info. No save for now.
     */
    function options() {
		$WebWeb_WP_NotesRemover_obj = WebWeb_WP_NotesRemover::get_instance();
        $opts = get_option('settings');

        include_once(WebWeb_WP_NotesRemover_BASE_DIR . '/menu.settings.php');
    }

    /**
     * Sets the setting variables
     */
    function register_settings() { // whitelist options
        register_setting($this->plugin_dir_name, $this->plugin_settings_key);
    }

    // Add the ? settings link in Plugins page very good
    function add_plugin_settings_link($links, $file) {
        if ($file == plugin_basename(WEBWEB_WP_NOTES_REMOVER_PLUGIN_FILE)) {
            //$prefix = 'options-general.php?page=' . dirname(plugin_basename(WEBWEB_WP_NOTES_REMOVER_PLUGIN_FILE)) . '/';
            $prefix = $this->plugin_admin_url_prefix . '/';

            $settings_link = "<a href=\"{$prefix}menu.settings.php\">" . __("Settings", $this->plugin_dir_name) . '</a>';

            array_unshift($links, $settings_link);
        }

        return $links;
    }

    /**
     * adds the CSS tags so the annoying tech stuff are not shown
     */
    function process() {
        echo PHP_EOL . "<!-- {$this->plugin_name} -->
				<style>.form-allowed-tags, .nocomments, .nocomments2 { display: none !important; } </style>
			<!-- /{$this->plugin_name} -->" . PHP_EOL;
	}

    /**
     * adds some HTML comments in the page so people would know that this plugin powers their site.
     */
    function add_plugin_credits() {
        //printf("\n" . '<meta name="generator" content="Powered by ' . $this->plugin_name . ' (' . $this->plugin_home_page . ') " />' . PHP_EOL);
        printf(PHP_EOL . '<!-- ' . PHP_EOL . 'Powered by ' . $this->plugin_name
                . ': ' . $this->app_title . PHP_EOL
                . 'URL: ' . $this->plugin_home_page . PHP_EOL
                . '-->' . PHP_EOL . PHP_EOL);
    }

    /**
     * Outputs a message (adds some paragraphs)
     */
    function message($msg, $status = 0) {
        $id = $this->plugin_id_str;
        $cls = empty($status) ? 'error fade' : 'success';

        $str = <<<MSG_EOF
<div id='$id-notice' class='$cls'><p><strong>$msg</strong></p></div>
MSG_EOF;
        return $str;
    }

    /**
     * a simple status message, no formatting except color
     */
    function msg($msg, $status = 0, $use_inline_css = 0) {
        $inline_css = '';
        $id = $this->plugin_id_str;
        $cls = empty($status) ? 'app_error' : 'app_success';

        if ($use_inline_css) {
            $inline_css = empty($status) ? 'background-color:red;' : 'background-color:green;';
            $inline_css .= 'text-align:center;margin-left: auto; margin-right:auto; padding-bottom:10px;color:white;';
        }

        $str = <<<MSG_EOF
<div id='$id-notice' class='$cls' style="$inline_css"><strong>$msg</strong></div>
MSG_EOF;
        return $str;
    }

    /**
     * a simple status message, no formatting except color, simpler than its brothers
     */
    function m($msg, $status = 0, $use_inline_css = 0) {
        $cls = empty($status) ? 'app_error' : 'app_success';
        $inline_css = '';

        if ($use_inline_css) {
            $inline_css = empty($status) ? 'color:red;' : 'color:green;';
            $inline_css .= 'text-align:center;margin-left: auto; margin-right: auto;';
        }

        $str = <<<MSG_EOF
<span class='$cls' style="$inline_css">$msg</span>
MSG_EOF;
        return $str;
    }

    private $errors = array();

    /**
     * accumulates error messages
     * @param array $err
     * @return void
     */
    function add_error($err) {
        return $this->errors[] = $err;
    }

    /**
     * @return array
     */
    function get_errors() {
        return $this->errors;
    }

    function get_errors_str() {
        $str  = join("<br/>", $this->get_errors());
        return $str;
    }

    /**
     *
     * @return bool
     */
    function has_errors() {
        return !empty($this->errors) ? 1 : 0;
    }
}
