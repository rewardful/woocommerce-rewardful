<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Rewardful
 * @subpackage Woocommerce_Rewardful/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Rewardful
 * @subpackage Woocommerce_Rewardful/admin
 * @author     Your Name <email@example.com>
 */
class Woocommerce_Rewardful_Admin {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        add_options_page( 'WooCommerce Rewardful', 'WooCommerce Rewardful', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page' ));
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links( $links ) {

        /**
         * Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
         */
        $settings_link = array( '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', $this->plugin_name ) . '</a>', );

        return array_merge(  $settings_link, $links );

    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setup_page() {
        if (isset($_GET['error_message'])) {
            add_action('admin_notices', array($this,'settingsMessages'));
            do_action( 'admin_notices', $_GET['error_message'] );
        }

        include_once( 'partials/' . $this->plugin_name . '-admin-display.php' );
    }

    /**
     * Render error message for form
     *
     * @param $error_message
     */
    public function settingsMessages($error_message){
        switch ($error_message) {
            case '1':
                $message = __( 'There was an error adding the API key. Please try again or contact support.' );
                $err_code = esc_attr(  $this->plugin_name . '_api_key' );
                $setting_field =  $this->plugin_name . '_api_key';
                break;
        }
        $type = 'error';

        add_settings_error($setting_field, $err_code, $message, $type);
    }

    /**
     * Adds section + fields for settings through settings API
     * Registers setting fields for saving on submit
     *
     * Only called if woocommerce is active
     */
    public function registerAndBuildFields() {
        // add setting section
        $sectionName = $this->plugin_name . '_general_settings';
        add_settings_section(
            $sectionName,
            '',
            array( $this, 'displayGeneralAccount' ),
            $this->plugin_name
        );

        // add api field
        $optionKey = $this->plugin_name . '_api_key';
        add_settings_field(
            $optionKey,
            'Rewardful API Key (required)',
            array( $this, 'generateField' ),
            $this->plugin_name,
            $sectionName,
            array(
                'type' => 'text',
                'id' => $optionKey,
                'name' => $optionKey,
                'required' => 'true',
                'description' => 'You can find your API Key on the <a href="https://app.getrewardful.com/company/edit" target="_blank">Company Settings</a> page in Rewardful',
            )
        );

        // allow saving of option
        register_setting(
            $this->plugin_name,
            $optionKey
        );

        // add script URl field
        $optionKey = $this->plugin_name . '_script_url';
        add_settings_field(
            $optionKey,
            'Script URL',
            array( $this, 'generateField' ),
            $this->plugin_name,
            $sectionName,
            array(
                'type' => 'text',
                'id' => $optionKey,
                'name' => $optionKey,
                'description' => '<strong>Advanced:</strong> The full URL to Rewardful\'s tracking script. Leave blank to use the default URL.'
            )
        );

        // allow saving of option
        register_setting(
            $this->plugin_name,
            $optionKey
        );

        // add script URl field
        $optionKey = $this->plugin_name . '_is_enabled';
        add_settings_field(
            $optionKey,
            'Enabled',
            array( $this, 'generateField' ),
            $this->plugin_name,
            $sectionName,
            array(
                'type' => 'checkbox',
                'id' => $optionKey,
                'name' => $optionKey,
                'description' => 'Uncheck this box to temporarily disable Rewardful',
            )
        );

        // allow saving of option
        register_setting(
            $this->plugin_name,
            $optionKey
        );
    }

    /**
     * description above settings
     */
    public function displayGeneralAccount() {
        $markup = '<p>Configure these settings for WooCommerce Rewardful.</p>';
        $markup .= '<p>Your Stripe account <strong>must be connected to Rewardful</strong> with <a href="https://help.getrewardful.com/en/articles/2051884-connect-your-stripe-account" target="_blank">read-write permission.</a></p>';

        echo $markup;
    }

    /**
     * For generating text and checkbox settings fields
     *
     * @param $args
     */
    public function generateField($args) {
        if (!isset($args['name'], $args['id'])) {
            return;
        }

        // get option
        // return null instead of false if option does not exist, used for rendering default checked state on checkbox field
        $value = get_option($args['name'], null);

        $required = '';
        if (isset($args['required']) && $args['required']) {
            $required = 'required="required"';
        }

        // generate field markup based on type
        $type = 'text';
        if (isset($args['type']) && $args['type']) {
            $type = $args['type'];
        }

        if ($type === 'checkbox') {
            // set to checked if populated or null, as default should be checked
            $checked = ($value || $value === null) ? 'checked' : '';

            $input = '<input
                type="' . $type . '"
                id="' . $args['id'] . '"
                ' . $required . '
                name="' . $args['name'] . '"
                size="40"
                value="1"
                ' . $checked . '
            />';
        } else {
            $input = '<input
                type="' . $type . '"
                id="' . $args['id'] . '"
                ' . $required . '
                name="' . $args['name'] . '"
                size="40"
                value="' . esc_attr($value) . '"
            />';
        }

        // displays additional text by input
        $description = '';
        if (isset($args['description']) && $args['description']) {
            $openTag = '<p class="description">';
            $closeTag = '</p>';

            if ($type === 'checkbox') {
                $openTag = '<label for="' . $args['id'] . '">';
                $closeTag = '</label>';
            }

            $description = $openTag . $args['description'] . $closeTag;
        }

        echo $input . $description;
    }

}
