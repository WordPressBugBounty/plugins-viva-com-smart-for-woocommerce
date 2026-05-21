<?php
/**
 * Uninstall script for the Viva.com Smart for WooCommerce plugin.
 *
 * Removes plugin settings and the four custom tables created on activation
 * (orders, transaction_types, transactions, recurring).
 *
 * @package VivaCom_Smart_For_WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check for plugin uninstall
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Remove plugin settings.
delete_option( 'woocommerce_vivacom_smart_settings' );

// Drop the four custom tables created on activation. The table names are
// derived from $wpdb->prefix (controlled by wp-config) plus literal suffixes —
// no user input is interpolated. $wpdb->prepare cannot bind table names, so
// direct query is required for DDL.
$tables = array(
    $wpdb->prefix . 'viva_com_smart_wc_checkout_recurring',
    $wpdb->prefix . 'viva_com_smart_wc_checkout_transactions',
    $wpdb->prefix . 'viva_com_smart_wc_checkout_transaction_types',
    $wpdb->prefix . 'viva_com_smart_wc_checkout_orders',
);

foreach ( $tables as $table ) {
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );
}
