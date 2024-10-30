<?php
require_once 'partials/csf-facebook-feeder.php';
require_once 'partials/csf-twitter-feeder.php';
require_once 'partials/csf_cron_scheduler.php';
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.civicuk.com/
 * @since      1.0.0
 *
 * @package    Csf
 * @subpackage Csf/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Csf
 * @subpackage Csf/admin
 * @author     CIVIC UK <info@civicuk.com>
 */
class Csf_Admin
{
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
     * The security nonce
     *
     * @var string
     */
    private $_nonce = 'feedier_admin';

    /**
     * The option name
     *
     * @var string
     */
    private $option_name = 'feedier_data';


    /**
     * Twitter variables
     */
        private $twitter_api_key;
        private $twitter_api_secret;
        private $twitter_account_name;

  
    /**
     * Facebook variables
     */
    private $facebook_account_id;
    private $facebook_token;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
        $this->csf_getFeeds();
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('admin_menu',                array($this,'csf_addAdminMenu'));
        add_action('wp_ajax_store_admin_data',  array($this,'csf_storeAdminData'));
        add_action ('civic_social_feeds_cron',  array($this,'csf_getFeeds'));
    }

    public function csf_getFeeds()
    {
        $data = $this->csf_getData();
        //TWITTER SETTINGS
        $this->twitter_api_key = isset($data['twitter_api_key']) ? sanitize_text_field( wp_filter_nohtml_kses($data['twitter_api_key'])) : '';
        $this->twitter_api_secret = isset($data['twitter_api_secret']) ? sanitize_text_field( wp_filter_nohtml_kses($data['twitter_api_secret'])) : '';
        $this->twitter_account_name = isset($data['twitter_account_name']) ? sanitize_text_field( wp_filter_nohtml_kses($data['twitter_account_name'])) : '';
        //FACEBOOK SETTINGS
        $this->facebook_account_id = isset($data['facebook_account_id']) ? sanitize_text_field( wp_filter_nohtml_kses($data['facebook_account_id'])) : '';
        $this->facebook_token = isset($data['facebook_token']) ? sanitize_text_field( wp_filter_nohtml_kses($data['facebook_token'])) : '';
        new Csf_Twitter_Feeder($this->twitter_api_key, $this->twitter_api_secret, $this->twitter_account_name);
        new Csf_Facebook_Feeder($this->facebook_account_id, $this->facebook_token);
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function csf_enqueue_styles()
    {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Csf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Csf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/csf-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function csf_enqueue_scripts()
    {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Csf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Csf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/csf-admin.js', array( 'jquery' ), $this->version, false );
        $admin_options = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            '_nonce'   => wp_create_nonce( $this->_nonce ),
        );

        wp_localize_script($this->plugin_name, 'feedier_exchanger', $admin_options);
	}

    /**
     * Returns the saved options data as an array
     *
     * @return array
     */
    private function csf_getData()
    {
        return get_option($this->option_name, array());
    }

    /**
     * Callback for the Ajax request
     *
     * Updates the options data
     *
     * @return void
     */
    public function csf_storeAdminData()
    {

        if (wp_verify_nonce($_POST['security'], $this->_nonce ) === false)
            die('Invalid Request! Reload your page please.');

        $data = $this->csf_getData();

        foreach ($_POST as $field=>$value) {

            if (substr($field, 0, 8) !== "feedier_")
                continue;

            if (empty($value))
                unset($data[$field]);

            $field = substr($field, 8);

            $data[$field] = sanitize_text_field($value);

        }

        update_option($this->option_name, $data);

        echo __('Saved!', 'csf');
        die();

    }

    /**
     * Adds the Civic Social Feeds label to the WordPress Admin Sidebar Menu
     */
    public function csf_addAdminMenu()
    {
        add_menu_page(
            __( 'Civic Social Feeds', 'csf' ),
            __( 'Civic Social Feeds', 'csf' ),
            'manage_options',
            'csf',
            array($this, 'csf_adminLayout'),
            'dashicons-share'
        );
    }

    /**
     * Outputs the Admin Dashboard layout containing the form with all its options
     *
     * @return void
     */
    public function csf_adminLayout()
    {
        include_once ('partials/csf-admin-display.php');
    }

    /**
     * Provide a admin area view for the plugin
     *
     * This file is used to markup the admin-facing aspects of the plugin.
     *
     * @link       https://www.civicuk.com/
     * @since      1.0.0
     *
     * @package    Civic_Social_Feeds
     */

    /**
     * Get a Dashicon for a given status
     *
     * @param $valid boolean
     *
     * @return string
     */
    public function csf_getStatusIcon($valid)
    {
        return ($valid) ? '<span class="dashicons dashicons-yes csf-success-message"></span>' : '<span class="dashicons dashicons-no-alt error-message"></span>';
    }
}
