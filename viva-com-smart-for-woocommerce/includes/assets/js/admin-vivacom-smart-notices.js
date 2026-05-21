/**
 * Viva.com Smart Checkout - WooCommerce Admin Notices
 *
 * Handles displaying admin notices in the new WooCommerce React-based payment settings page.
 * This script uses WooCommerce's JavaScript notice API instead of PHP admin_notices hooks.
 *
 * Compatible with:
 * - WooCommerce 8.x+ (new React-based payment settings)
 * - WooCommerce 7.x and below (classic admin pages with fallback)
 *
 * @package VivaComSmartForWooCommerce
 */

( function() {
	'use strict';

	// Track if notices have been displayed to avoid duplicates on SPA navigation
	var noticesDisplayed = false;
	var noticeIds = [];

	/**
	 * Initialize notices when the DOM is ready
	 */
	function init() {
		// Check if we have any notices to display
		if ( ! window.viva_com_smart_admin_notices ) {
			return;
		}

		// Avoid duplicate notices on SPA navigation
		if ( noticesDisplayed ) {
			return;
		}

		var notices = window.viva_com_smart_admin_notices;
		var hasNotices = false;

		// Display error notices
		if ( notices.errors && Array.isArray( notices.errors ) && notices.errors.length > 0 ) {
			notices.errors.forEach( function( error ) {
				createNotice( 'error', error );
			} );
			hasNotices = true;
		}

		// Display success notices
		if ( notices.success && Array.isArray( notices.success ) && notices.success.length > 0 ) {
			notices.success.forEach( function( message ) {
				createNotice( 'success', message );
			} );
			hasNotices = true;
		}

		// Display info notices
		if ( notices.info && Array.isArray( notices.info ) && notices.info.length > 0 ) {
			notices.info.forEach( function( message ) {
				var msg = ( message && typeof message === 'object' ) ? message.message : message;
				var opts = ( message && typeof message === 'object' ) ? { actions: message.actions } : undefined;
				createNotice( 'info', msg, opts );
			} );
			hasNotices = true;
		}

		// Display warning notices
		if ( notices.warning && Array.isArray( notices.warning ) && notices.warning.length > 0 ) {
			notices.warning.forEach( function( message ) {
				createNotice( 'warning', message );
			} );
			hasNotices = true;
		}

		if ( hasNotices ) {
			noticesDisplayed = true;
		}
	}

	/**
	 * Create a notice using WooCommerce's notice system
	 *
	 * @param {string} type    - Notice type: 'error', 'success', 'info', 'warning'
	 * @param {string} message - The notice message to display
	 * @param {Object} options - Optional. Supports: actions (Array of { url, label } objects)
	 */
	function createNotice(type, message, options) {
		if (!message) return;

		var actions = (options && Array.isArray(options.actions)) ? options.actions : [];

		// Generate a unique ID for this notice to track it
		var noticeId = 'vivacom-smart-' + type + '-' + hashString(String(message));

		if (noticeIds.indexOf(noticeId) !== -1) {
			return;
		}

		noticeIds.push(noticeId);

		// Try to use WooCommerce's dispatch system (wc-admin)
		if (window.wp && window.wp.data && typeof window.wp.data.dispatch === 'function') {
			try {
				var dispatch = window.wp.data.dispatch('core/notices');
				if (dispatch && typeof dispatch.createNotice === 'function') {
					dispatch.createNotice(type, String(message), {
						id: noticeId,
						context: 'wc-settings',
						isDismissible: true,
						type: 'snackbar',
						actions: actions
					});
				}
			} catch (e) {
				// Continue to next fallback
			}
		}

		createFallbackNotice(type, String(message), noticeId, actions);
	}

	/**
	 * Simple string hash function for generating notice IDs
	 *
	 * @param {string} str - String to hash
	 * @return {string} Hash string
	 */
	function hashString(str) {
		var hash = 0;
		if (!str || str.length === 0) return '0';
		for (var i = 0; i < str.length; i++) {
			hash = ((hash << 5) - hash) + str.charCodeAt(i);
			hash = hash & hash;
		}
		return Math.abs(hash).toString(36);
	}

	/**
	 * Create a fallback DOM-based notice when WooCommerce's React notice system isn't available
	 *
	 * @param {string} type     - Notice type: 'error', 'success', 'info', 'warning'
	 * @param {string} message  - The notice message to display
	 * @param {string} noticeId - Unique notice ID for duplicate prevention
	 * @param {Array}  actions  - Optional array of { url, label } action links
	 */
	function createFallbackNotice(type, message, noticeId, actions) {
		// Check if notice already exists to avoid duplicates
		if (document.getElementById(noticeId)) {
			return;
		}

		var noticeWrapper =
			document.querySelector('#wpbody-content') ||
			document.querySelector('.wrap') ||
			document.body;

		if (!noticeWrapper) {
			console.log('[Viva.com Smart Checkout ' + type + ']:', message);
			return;
		}

		var actionsHtml = '';
		if (actions && actions.length > 0) {
			var links = actions.map(function(action) {
				return '<a href="' + escapeHtml(action.url) + '" target="_blank" rel="noopener noreferrer">' + escapeHtml(action.label) + '</a>';
			});
			actionsHtml = '<p class="vivacom-smart-notice-actions">' + links.join(' ') + '</p>';
		}

		var noticeClass = 'notice notice-' + type + ' is-dismissible vivacom-smart-notice';

		var notice = document.createElement('div');
		notice.id = noticeId;
		notice.className = noticeClass;
		notice.innerHTML =
			'<p><strong>' + escapeHtml(message) + '</strong></p>' +
			actionsHtml +
			'<button type="button" class="notice-dismiss">' +
			'<span class="screen-reader-text">Dismiss this notice.</span>' +
			'</button>';

		// Insert notice at the beginning of the wrapper
		noticeWrapper.insertBefore(notice, noticeWrapper.firstChild);

		// Add dismiss functionality
		var dismissButton = notice.querySelector('.notice-dismiss');
		if (dismissButton) {
			dismissButton.addEventListener('click', function () {
				if (notice.parentNode) {
					notice.parentNode.removeChild(notice);
				}
			});
		}
	}

	/**
	 * Escape HTML to prevent XSS
	 *
	 * @param {string} text - Text to escape
	 * @return {string} Escaped text
	 */
	function escapeHtml( text ) {
		var div = document.createElement( 'div' );
		div.textContent = text;
		return div.innerHTML;
	}

	// Initialize when DOM is ready
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		// DOM is already ready, wait a bit for WC React to initialize
		setTimeout( init, 100 );
	}

	// Also try to initialize when WooCommerce admin page loads (for SPA navigation)
	if ( window.wp && window.wp.hooks && window.wp.hooks.addAction ) {
		window.wp.hooks.addAction( 'woocommerce_admin_page_loaded', 'vivacom-smart', init );
	}

} )();
