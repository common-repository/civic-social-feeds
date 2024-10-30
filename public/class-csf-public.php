<?php
require_once 'partials/csf-public-display.php';


/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.civicuk.com/
 * @since      1.0.0
 *
 * @package    Csf
 * @subpackage Csf/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Csf
 * @subpackage Csf/public
 * @author     CIVIC UK <info@civicuk.com>
 */

class Csf_Public {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function csf_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/csf-public.css', array(), $this->version, 'all' );
	}

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function csf_enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         */

    }

    /**
     * Registers all shortcodes at once
     *
     */
    public function csf_register_shortcodes() {
        add_shortcode( 'civic_social_feeds', array( $this, 'csf_civic_feeds' ) );

    }

    /**
     * Processes shortcode for facebook, instagram, twitter
     *
     * $atts the type of social feed to display
     *
     * @return mixed $output Output of the buffer
     */
    public function csf_civic_feeds($atts) {

        $a = shortcode_atts( array(
            'type' => 'type',
            'num' => 'num',
            'cols' => 'cols'
        ), $atts );

        $type = sanitize_text_field( wp_filter_nohtml_kses($a['type']));
        $num = sanitize_text_field( wp_filter_nohtml_kses($a['num']));
        $cols = sanitize_text_field( wp_filter_nohtml_kses($a['cols']));

        switch ($type) {
            case 'facebook':
                $csf_facebook_feeds = new Csf_Public_Display($type, $num, $cols);
                return $csf_facebook_feeds->csf_showFacebookFeeds();
                break;
            case 'instagram':
                $csf_instagram_feeds = new Csf_Public_Display($type, $num, $cols);
                return $csf_instagram_feeds->csf_showInstagramFeeds();
                break;
            case 'twitter':
                $csf_twitter_feeds = new Csf_Public_Display($type, $num, $cols);
                return $csf_twitter_feeds->csf_showTwitterFeeds();
                break;
        }
    }
}
