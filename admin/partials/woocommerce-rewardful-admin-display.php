<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Rewardful
 * @subpackage Woocommerce_Rewardful/admin/partials
 */

    // If this file is called directly, abort.
    if ( ! defined( 'WPINC' ) ) die;
?>

<div class="wrap">
    <h2>Rewardful for WooCommerce <?php esc_attr_e('Settings', 'plugin_name' ); ?></h2>

    <?php
        /**
         * Check if WooCommerce is active
         **/
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))):
    ?>

    <form method="POST" action="options.php">
        <?php
            settings_fields($this->plugin_name);
            do_settings_sections($this->plugin_name);
            submit_button( __( 'Save Settings', 'plugin_name' ), 'primary','submit', TRUE );
        ?>
    </form>

    <?php else: ?>
        <?php // todo: extract woocommerce requirement to error flash message ?>
        <p>This plugin requires the <a href="https://en-ca.wordpress.org/plugins/woocommerce/">WooCommerce plugin</a> with WooCommerce Stripe activated.</p>
        <p>Your Stripe account <strong>must be connected to Rewardful</strong> with <a href="https://help.getrewardful.com/en/articles/2051884-connect-your-stripe-account" target="_blank">read-write permission.</a></p>

    <?php endif; ?>
</div>
