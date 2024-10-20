<?php

/**
 * Plugin Name: WooCommerce Rewardful
 * Plugin URI: https://www.getrewardful.com/
 * Description: Enhance the WooCommerce Stripe checkout with Rewardful's conversion tracking
 * Version: 1.0.0
 * Author: Rewardful
 * Author URI: https://www.getrewardful.com/
 * Text Domain: woocommerce-rewardful
 *
 * WC requires at least: 5.6.0
 * WC tested up to: 5.6.0
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WOOCOMMERCE_REWARDFUL_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-rewardful-activator.php
 */
function activate_woocommerce_rewardful() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-rewardful-activator.php';
	Woocommerce_Rewardful_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-rewardful-deactivator.php
 */
function deactivate_woocommerce_rewardful() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-rewardful-deactivator.php';
	Woocommerce_Rewardful_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_rewardful' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_rewardful' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-rewardful.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_rewardful() {

	$plugin = new Woocommerce_Rewardful();
	$plugin->run();

}
run_woocommerce_rewardful();

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );