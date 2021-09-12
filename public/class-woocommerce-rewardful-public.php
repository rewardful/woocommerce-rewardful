<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Rewardful
 * @subpackage Woocommerce_Rewardful/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Rewardful
 * @subpackage Woocommerce_Rewardful/public
 * @author     Your Name <email@example.com>
 */
class Woocommerce_Rewardful_Public {

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
	 * Register the JavaScript for the public-facing side of the site.
	 *
     * Only called if woocommerce is active
     *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
        // get options
        $apiKey = get_option($this->plugin_name . '_api_key');
        $isEnabled = get_option($this->plugin_name . '_is_enabled');
        $scriptURL = get_option($this->plugin_name . '_script_url');

        // only load rewardful script if api_key + is_enabled options are set
        if ($apiKey && $isEnabled) {
            // add required inline js to run rewardful functions, to every page in head src: https://developers.getrewardful.com/javascript-api/overview#add-the-javascript-tag-snippet
            // register dummy script to hook wp_add_inline_script() into
            wp_register_script( $this->plugin_name . '-main', '' );
            wp_enqueue_script( $this->plugin_name . '-main' );

            $inlineScript = "(function(w,r){w._rwq=r;w[r]=w[r]||function(){(w[r].q=w[r].q||[]).push(arguments)}})(window,'rewardful');";
            wp_add_inline_script( $this->plugin_name . '-main', $inlineScript );

            // enqueue js rewardful api after required $this->plugin_name . '-main' snippet
            // if script url not set, use default src
            if (!$scriptURL) {
                $scriptURL = 'https://r.wdfl.co/rw.js';
            }

            wp_enqueue_script( $this->plugin_name . '-api', $scriptURL, array($this->plugin_name . '-main'), null, false );
        }
	}

    /**
     * Adds required async and data attribute markup to api script enqueue
     *
     * output should update to ex. <script async src='https://r.wdfl.co/rw.js' data-rewardful='YOUR-API-KEY'></script>
     *
     * @param $tag
     * @param $handle
     * @param $src
     * @return array|mixed|string|string[]
     */
    function addAttributesToAPIScript($tag, $handle, $src) {
        $apiKey = get_option($this->plugin_name . '_api_key');

        // only run on api script, when api_key option is set
        if ($handle === $this->plugin_name . '-api' && $apiKey) {
            // add async tag
            if (stripos($tag, 'async') === false) {
                $tag = str_replace(' src', ' async src', $tag);
            }

            // add data-rewardful attribute with API key from settings
            if (stripos($tag, 'data-rewardful') === false) {
                $tag = str_replace(' id', ' data-rewardful=\'' . $apiKey . '\' id', $tag);
            }
        }

        return $tag;
    }

    /**
     * Adds tracking code for Rewardful Api to thank you page if stripe is payment gateway
     *
     * only run if WooCommerce is active
     *
     * @param $order_id
     */
    public function rewardfulThankYouScript($order_id) {
        if ( $order_id > 0 ) {
            $order = wc_get_order( $order_id );

            if ($order instanceof WC_Order) {
                // get customer info from order
                $orderPaymentMethod = $order->get_payment_method();
                $email = $order->get_billing_email();

                // get options
                $apiKey = get_option($this->plugin_name . '_api_key');
                $isEnabled = get_option($this->plugin_name . '_is_enabled');

                // if the payment method is stripe and api_key and is_enabled setting set, execute the convert function
                if ($orderPaymentMethod === 'stripe' && $apiKey && $isEnabled) :
                    // output conversion code
                ?>
                    <script type="text/javascript">
                      const rewardfulEmail = '<?php echo $email ?>';

                      // wait for rewardful to load
                      rewardful('ready', function() {
                        rewardful('convert', { email: rewardfulEmail });
                      });
                    </script>
                <?php
                endif;
            }
        }
    }


}
