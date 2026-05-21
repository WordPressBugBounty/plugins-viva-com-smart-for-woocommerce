<?php
// phpcs:disable WordPress.Files.FileName.InvalidClassFileName -- class name predates sniff; renaming would break require chain
/**
 * Vivacom Smart
 *
 * @package   VivaComSmartForWooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Core\Source\SourceItem;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Application;
use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Core\Source\SourceList;

/**
 * Class WC_Vivacom_Smart_Payment_Gateway
 *
 * @class WC_Vivacom_Smart_Payment_Gateway
 * @package VivaComSmartForWoocommerce
 */
class WC_Vivacom_Smart_Payment_Gateway extends WC_Payment_Gateway {

    const VIVA_DEVELOPER_PORTAL = 'https://developer.viva.com/webhooks-for-payments/#whitelist-the-viva-addresses';
	/**
	 * Source code
	 *
	 * @var string
	 */
	protected $source_code;

	/**
	 * Test mode
	 *
	 * @var string
	 */
	protected $test_mode;

	/**
	 * Test source code
	 *
	 * @var string
	 */
	protected $demo_source_code;

	/**
	 * Credentials
	 *
	 * @var array
	 */
	protected $credentials;


	/**
	 * Demo source list
	 *
	 * @var object
	 */
	private $demo_source_list;

	/**
	 * Live source list
	 *
	 * @var object
	 */
	private $live_source_list;

	/**
	 * Installments
	 *
	 * @var string
	 */
	private $installments;

	/**
	 * Demo client id
	 *
	 * @var string
	 */
	private $demo_client_id;

	/**
	 * Client id
	 *
	 * @var string
	 */
	private $client_id;

	/**
	 * Demo client secret
	 *
	 * @var string
	 */
	private $demo_client_secret;

	/**
	 * Client secret
	 *
	 * @var string
	 */
	private $client_secret;

    /**
     * Order status
     *
     * @var string
     */
    private $order_status;

	/**
	 * WC_Vivacom_Smart_Payment_Gateway constructor.
	 */
	public function __construct() {
		$plugin_dir = plugin_dir_url( __FILE__ );
		$this->id   = WC_Vivacom_Smart_Helpers::TECHNICAL_NAME;

		if ( $this->get_option( 'logo_enabled' ) === 'yes' ) {
			/**
			 * Apply filter cards icons.
			 *
			 * @since 1.0.0
			 */
			$this->icon = apply_filters( 'viva_com_smart_wc_icon', '' . $plugin_dir . 'assets/viva_plugin_logo.svg' );
		}

		$this->has_fields = false;

		$this->method_title       = __( 'Viva.com | Smart Checkout', 'viva-com-smart-for-woocommerce' );
		$this->method_description = __( 'Pay using 30+ methods (cards, digital wallets, local payment methods, online banking, and more)', 'viva-com-smart-for-woocommerce' );

		// SDK INIT INFORMATION.
		Application::setInformation(
			array(
				'vivaWallet'        => array(
					'version' => WC_VIVA_COM_SMART_VERSION,
				),
				'ecommercePlatform' => array(
					'version'      => defined( 'WC_VERSION' ) ?? '',
					'abbreviation' => 'WC',
				),
				'cms'               => array(
					'version'      => get_bloginfo( 'version' ),
					'abbreviation' => 'WP',
					'name'         => 'WordPress',
				),
			)
		);

		$this->supports = array(
			'products',
			'refunds',
			'tokenization',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'multiple_subscriptions',
		);

		$this->init_form_fields();

		$this->init_settings();

		// Define user set variables.
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->client_id          = $this->get_option( 'client_id' );
		$this->client_secret      = $this->get_option( 'client_secret' );
		$this->demo_client_id     = $this->get_option( 'demo_client_id' );
		$this->demo_client_secret = $this->get_option( 'demo_client_secret' );
		$this->source_code        = $this->get_option( 'source_code' );
		$this->demo_source_code   = $this->get_option( 'demo_source_code' );
		$this->installments       = $this->get_option( 'installments' );
		$this->test_mode          = $this->get_option( 'test_mode' );
		$this->order_status       = $this->get_option( 'order_status' );

		set_transient( 'viva_com_smart_wc_admin_notice', true, 0 );

		add_action( 'woocommerce_receipt_vivacom_smart', array( $this, 'receipt_page' ) );
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_and_styles' ) );

		add_action( 'woocommerce_settings_start', array( $this, 'admin_settings_start' ) );

	}

	/**
	 * Receipt page
	 *
	 * @param string $order_id order_id.
	 *
	 * @return void
	 */
	public function receipt_page( string $order_id ) {
		echo '<p>' . esc_html__( 'Thank you for your order, please click the button below to pay.', 'viva-com-smart-for-woocommerce' ) . '</p>';
		$this->generate_form( $order_id );
	}

	/**
	 * Generate pay form
	 *
	 * @param string $order_id order_id.
	 *
	 * @return void
	 */
	public function generate_form( string $order_id ) {

		global $wpdb, $wp_version;
		$order      = wc_get_order( $order_id );
		$order_data = WC_Vivacom_Smart_Helpers::get_order_data( $order );

        $brand_color = str_replace( '#', '', $this->get_option( 'brand_color' ) );

		$allow_recurring = false;
		if ( WC_Vivacom_Smart_Helpers::check_subscription( $order->get_id() ) ) {
			$allow_recurring = true;
		}

		$max_installments = 1;

		if ( WC_Vivacom_Smart_Helpers::check_if_instalments() ) {
			$intallments_logic = $this->get_option( 'installments' );
			if ( ! empty( $intallments_logic ) ) {
				$max_installments = WC_Vivacom_Smart_Helpers::get_max_installments( $intallments_logic, $order_data['amount'] );
			}
		}

        $woocommerce_version = defined( 'WC_VERSION' ) ? 'WC-' . WC_VERSION : '';

		// ARGUMENTS OR AMOUNT, CURRENCY AND OPTIONS.
		$options = array(
			'sourceCode' => 'yes' === $this->test_mode ? $this->demo_source_code : $this->source_code,
			'customer'   => array(
				'email'       => $order_data['email'],
				'fullName'    => $order_data['name'],
				'phone'       => $order_data['phone'],
				'requestLang' => $order_data['lang'],
				'countryCode' => $order_data['countryCode'],
			),
			'payment'    => array(
				'maxInstallments' => $max_installments,
				'allowRecurring'  => $allow_recurring,
				'preauth'         => 'yes' === $this->get_option( 'enable_preauthorizations' ),
                'dynamicDescriptor' => $this->get_option( 'dynamic_descriptor' )
			),
			'messages'   => array(
				'customer' => $order_data['messages']['customer_message'],
				'merchant' => $order_data['messages']['merchant_message'],
				'tags'     => array( 'woocommerce-smart', $woocommerce_version, 'WP-' . $wp_version ),
			),
		);

		$environment = 'yes' === $this->test_mode ? 'demo' : 'live';

		// IF ORDER CODE AND ORDER PENDING EXISTS ELSE CREATE OTHER.
		$vivacom_order = WC_Vivacom_Smart_Helpers::get_smart_checkout_order( $order->get_id() );

		$action_adr = '';
		if ( ! empty( $vivacom_order['vivacom_order_code'] ) ) {
			$order_code = $vivacom_order['vivacom_order_code'];
			$action_adr = Application::getSmartCheckoutUrl(
                [
                    'ref'   => $order_code,
                    'color' => $brand_color,
                ],
                $environment
            );
		} else {
			$bearer_authentication = WC_Vivacom_Smart_Helpers::get_bearer_authentication( $environment );

			if ( $bearer_authentication->hasValidToken() ) {
				$order_response = WC_Vivacom_Smart_Helpers::create_order( $order_data['amount'], $order_data['currency'], $options, $bearer_authentication );

				if ( $order_response->isSuccessful() ) {
					$order_code  = $order_response->getBody()->orderCode;
					$insert_data = array(
						'woocommerce_order_id' => $order->get_id(),
						'vivacom_order_code'   => $order_code,
						'client_id'            => 'yes' === $this->test_mode ? $this->demo_client_id : $this->client_id, // get client id.
						'currency'             => $order_data['currency'],
						'amount'               => $order->get_total(),
						'is_demo'              => 'yes' === $this->test_mode,
					);
					$wpdb->insert(
						$wpdb->prefix . 'viva_com_smart_wc_checkout_orders',
						$insert_data
					);
                    $action_adr = Application::getSmartCheckoutUrl(
                        [
                            'ref'   => $order_code,
                            'color' => $brand_color,
                        ],
                        $environment
                    );				} else {
					$error = __( 'There was a problem processing your payment. Please try again or use an other payment method.', 'viva-com-smart-for-woocommerce' );
					wc_add_notice( $error, 'error' );
					wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
				}
			} else {
				$error = __( 'There was a problem processing your payment. Please try again or use an other payment method.', 'viva-com-smart-for-woocommerce' );
				wc_add_notice( $error, 'error' );
				wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
			}
		}

		$order_ref = $order_code ?? '';

		wc_enqueue_js(
			'
			jQuery("body").block({
					message: "' . __( 'Thank you for your order. We are now redirecting you to make your payment.', 'viva-com-smart-for-woocommerce' ) . '",
					overlayCSS:
					{
						background: "#fff",
						opacity: 0.6
					},
					css: {
				        padding:        20,
				        textAlign:      "center",
				        color:          "#555",
				        border:         "3px solid #aaa",
				        backgroundColor:"#fff",
				        cursor:         "wait",
				        lineHeight:		"32px"
				    }
				});
			jQuery("#submit_vivacom_smart_payment_form").click();
		'
		);

		echo '<form action="' . esc_url( $action_adr ) . '" method="GET" id="vivacom_smart_payment_form">' . "\n" .
			'<input type="hidden" name="Ref" value="' . esc_attr( $order_ref ) . '" />' . "\n" .
            '<input type="hidden" name="color" value="' . esc_attr( $brand_color ) . '" />' . "\n" .
            '<input type="submit" class="button alt" id="submit_vivacom_smart_payment_form" value="' . esc_html__( 'Pay Now', 'viva-com-smart-for-woocommerce' ) . '" /> <a class="button cancel" href="' . esc_url( $order->get_cancel_order_url() ) . '">' . esc_html__( 'Cancel', 'viva-com-smart-for-woocommerce' ) . '</a>' . "\n" .
			'</form>';
	}

	/**
	 * Process Payment
	 *
	 * @param int $order_id order_id.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		return array(
			'result'   => 'success',
			'redirect' => esc_url_raw( add_query_arg( 'order-pay', $order->get_id(), add_query_arg( 'key', $order->get_order_key(), wc_get_page_permalink( 'checkout' ) ) ) ),
		);
	}

	/**
	 * Process refund extend
	 *
	 * @param int    $order_id order_id.
	 * @param null   $amount amount.
	 * @param string $reason reason.
	 *
	 * @return bool|WP_Error
	 *
	 * @throws Exception Throws exception.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		if ( ! is_numeric( $amount ) && 'refunded' === $amount ) {
			$order  = wc_get_order( $order_id );
			$amount = $order->get_total();
		}

		$environment           = 'yes' === $this->test_mode ? 'demo' : 'live';
		$source                = 'yes' === $this->test_mode ? $this->demo_source_code : $this->source_code;
		$bearer_authentication = WC_Vivacom_Smart_Helpers::get_bearer_authentication( $environment );

		if ( $bearer_authentication->hasValidToken() ) {
			return WC_Vivacom_Smart_Helpers::process_refund( $bearer_authentication, $source, $order_id, $amount );
		} else {
			/* translators: error_message */
			return new WP_Error( 'error', sprintf( __( 'Viva.com: Your %s credentials are NOT valid. Please check your credentials!', 'viva-com-smart-for-woocommerce' ), $environment ) );
		}
	}

	/**
	 * Admin page enqueue js and css
	 */
	public function admin_scripts_and_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ? '' : '.min';

		// Detect if we're on the new WooCommerce React-based payment settings page.
		$is_react_settings = false;

		if ( is_admin() ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended -- No action, just page detection
			$page    = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			$tab     = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
			$section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '';
			$path    = isset( $_GET['path'] ) ? sanitize_text_field( wp_unslash( $_GET['path'] ) ) : '';
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			if ( 'wc-settings' === $page ) {
				if ( in_array( $tab, array( 'checkout', 'payment' ), true ) ) {
					if ( empty( $section )) {
						$is_react_settings = true;
					} elseif ($this->id === $section ) {
                        $is_react_settings = true;
                        // Classic admin page logic.
                        if ( ! self::valid_check( false ) ) {
                            return;
                        }

                        wp_enqueue_style('wp-color-picker');
                        wp_enqueue_script('wp-color-picker');


                        wp_enqueue_script(
                            'vivacom_smart_admin',
                            plugins_url( '/assets/js/admin-vivacom-smart' . $suffix . '.js', __FILE__ ),
                            array( 'jquery' ),
                            WC_VIVA_COM_SMART_VERSION,
                            true
                        );

                        $currency_symbol = get_woocommerce_currency_symbol();

                        $translations = array(
                            'sampleBank' => __('Sample Bank', 'viva-com-smart-for-woocommerce'),
                            'transactionReference' => __('Transaction Reference', 'viva-com-smart-for-woocommerce'),
                            'testAmount' => __('Amount', 'viva-com-smart-for-woocommerce'),
                            'yourCompanyName' => __('YourCompanyName', 'viva-com-smart-for-woocommerce'),
                            'currencySymbol' => $currency_symbol,
                        );

                        wp_localize_script( 'vivacom_smart_admin', 'vivacom_smart_admin_trans', $translations );
                    }
				}
			}
			if ( 'wc-admin' === $page ) {
				if ( strpos( $path, '/payments' ) !== false || strpos( $path, '/payment' ) !== false ) {
					$is_react_settings = true;
				}
			}
		}
		if ( $is_react_settings ) {
			// Inline enqueue_react_notices_script.
			wp_enqueue_script(
				'vivacom_smart_admin_notices',
				plugins_url( '/assets/js/admin-vivacom-smart-notices' . $suffix . '.js', __FILE__ ),
				array( 'wp-hooks', 'wp-element', 'wp-data', 'wp-notices' ),
				WC_VIVA_COM_SMART_VERSION,
				true
			);

			$notices = $this->collect_admin_notices();

			wp_localize_script( 'vivacom_smart_admin_notices', 'viva_com_smart_admin_notices', $notices );

            return;
		}
	}

	/**
	 * Collect admin notices as arrays for the React-based WooCommerce settings page (frontend JS usage).
	 *
	 * This is used to pass notices to JavaScript for the React-based payment settings page.
	 *
	 * @return array Array with 'errors', 'success', 'info', and 'warning' keys
	 */
	private function collect_admin_notices() {
		$notices = array(
			'errors'  => array(),
			'success' => array(),
			'info'    => array(),
			'warning' => array(),
		);

        // Check webhook status.
        $webhook_created = $this->get_option( 'webhook_created' );

        // Remind merchants to whitelist Viva.com IPs so webhook notifications are not blocked by firewalls or security plugins.
        if ('yes' !== $webhook_created ) {
            $notices['info'][] = array(
                'message' => __( 'Viva.com: To ensure webhooks work properly, you must whitelist(allow) Viva domains and IP addresses.', 'viva-com-smart-for-woocommerce' ),
                'actions' => array(
                    array(
                        'url'   => esc_url( self::VIVA_DEVELOPER_PORTAL ),
                        'label' => __( 'Visit the documentation.', 'viva-com-smart-for-woocommerce' ),
                    ),
                ),
            );
        }

        // Check if gateway is enabled.
		if ( 'no' === $this->enabled ) {
			return $notices;
		}

		// Determine environment.
		if ( 'yes' === $this->test_mode ) {
			$environment = 'demo';
			$source_key  = 'demo_source_code';
		} else {
			$environment = 'live';
			$source_key  = 'source_code';
		}
		$source_code = $this->get_option( $source_key );

		// Check domain validity.
		$domain = WC_Vivacom_Smart_Helpers::get_domain();
		if ( ! WC_Vivacom_Smart_Helpers::is_valid_domain_name( $domain ) ) {
			$error = __( 'Viva.com Warning: A valid domain name is needed for Viva.com services to work correctly.', 'viva-com-smart-for-woocommerce' );
			/* translators: domain */
			$error .= ' ' . sprintf( __( 'Your domain, "%s", does not seem valid.', 'viva-com-smart-for-woocommerce' ), $domain );
			$error .= ' ' . __( 'To test locally, edit your hosts file and add a domain, for example, "vivademo.test".', 'viva-com-smart-for-woocommerce' );
			$notices['errors'][] = $error;
		}

		// Check credentials.
		$bearer_authentication = WC_Vivacom_Smart_Helpers::get_bearer_authentication( $environment );

		if ( ! $bearer_authentication->hasValidToken() ) {
			/* translators: error_message */
			$notices['errors'][] = sprintf( __( 'Viva.com: Your %s credentials are NOT valid. Please check your credentials!', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ) );
			return $notices;
		} else {
			/* translators: info_message */
			$notices['success'][] = sprintf( __( 'Viva.com: Your %s credentials are valid.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ) );
		}


		if ( ! empty( $webhook_created ) ) {
			if ( 'yes' === $webhook_created ) {
                unset($notices['info']);
				$notices['success'][] = __( 'Viva.com: You are ready to receive payment notifications. Your hooks have been updated successfully.', 'viva-com-smart-for-woocommerce' );
			} elseif ( 'error' === $webhook_created ) {
				$error  = __( 'Viva.com: There was a problem updating hooks for your website.', 'viva-com-smart-for-woocommerce' );
				$error .= ' ' . __( 'Note that your site must be publicly accessible. Endpoints must be accessible from the web.', 'viva-com-smart-for-woocommerce' );
				$error .= ' ' . __( 'You will be not able to receive payment notifications and your orders will not be updated.', 'viva-com-smart-for-woocommerce' );
				$notices['errors'][] = $error;
			}
		}

		// Check source code status.
		if ( ! empty( $source_code ) ) {
			$info_code = WC_Vivacom_Smart_Helpers::check_source( $source_code, $bearer_authentication );
			switch ( $info_code ) {
				case 'active':
					/* translators: info_message */
					$notices['success'][] = sprintf( __( 'Viva.com: Your %1$s source code: %2$s is verified and you are ready to accept payments.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ), $source_code );
					break;
				case 'pending':
					/* translators: info_message */
					$error = sprintf( __( 'Viva.com: Your %s credentials are valid and connection with Viva.com was successful.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ) );
					/* translators: info_message */
					$error .= ' ' . sprintf( __( 'We are in the process of reviewing your %1$s website "%2$s".', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ), $source_code );
					$notices['warning'][] = $error;
					break;
				case 'inactive':
					/* translators: info_message */
					$error = sprintf( __( 'Viva.com: Your %s credentials are valid and connection with Viva.com was successful.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ) );
					/* translators: info_message */
					$error .= ' ' . sprintf( __( 'But your %1$s source code: %2$s has been BLOCKED. Please check your latest email from Viva.com Support for more info.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ), $source_code );
					$notices['errors'][] = $error;
					break;
				case 'unknownState':
					/* translators: info_message */
					$error = sprintf( __( 'Viva.com: Your %s credentials are valid and connection with Viva.com was successful.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ) );
					/* translators: info_message */
					$error .= ' ' . sprintf( __( 'But your %1$s source code: %2$s is NOT active.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ), $source_code );
					$notices['errors'][] = $error;
					break;
				case 'notValidDomain':
					/* translators: info_message */
					$error = sprintf( __( 'Viva.com: Your %s credentials are valid and connection with Viva.com was successful.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ) );
					/* translators: error_message */
					$error .= ' ' . sprintf( __( 'But your %1$s source code: %2$s is not valid for this domain.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ), $source_code );
					$error .= ' ' . __( 'Check the sources selection box in advanced settings to see your available source codes and set one from there if available.', 'viva-com-smart-for-woocommerce' );
					$error .= ' ' . __( 'Please try to save your settings again and if the source is empty, Viva.com plugin will try to create a new source for your website.', 'viva-com-smart-for-woocommerce' );
					$notices['errors'][] = $error;
					break;
				case 'notValidUrls':
					/* translators: info_message */
					$error = sprintf( __( 'Viva.com: Your %s credentials are valid and connection with Viva.com was successful.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ) );
					/* translators: error_message */
					$error .= ' ' . sprintf( __( 'But your %1$s source code: %2$s has wrong redirection urls.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ), $source_code );
					$error .= ' ' . __( 'Check the sources selection box in advanced settings to see your available source codes and set one from there if available.', 'viva-com-smart-for-woocommerce' );
					$error .= ' ' . __( 'Please try to save your settings again and if the source is empty, Viva.com plugin will try to create a new source for your website.', 'viva-com-smart-for-woocommerce' );
					$notices['errors'][] = $error;
					break;
				case 'noSourcesFound':
					/* translators: info_message */
					$error = sprintf( __( 'Viva.com: Your %s credentials are valid and connection with Viva.com was successful.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ) );
					/* translators: error_message */
					$error .= ' ' . sprintf( __( 'But your %1$s source code: %2$s is not found.', 'viva-com-smart-for-woocommerce' ), strtoupper( $environment ), $source_code );
					$error .= ' ' . __( 'Check the sources selection box in advanced settings to see your available source codes and set one from there if available.', 'viva-com-smart-for-woocommerce' );
					$error .= ' ' . __( 'Please try to save your settings again and if the source is empty, Viva.com plugin will try to create a new source for your website.', 'viva-com-smart-for-woocommerce' );
					$notices['errors'][] = $error;
					break;
			}
		} else {
			$notices['errors'][] = __( 'Viva.com: Your source code is empty. Please save your settings. Viva.com plugin will try to create a new source for your website.', 'viva-com-smart-for-woocommerce' );
		}

		// Check currency.
		$currency_code          = get_woocommerce_currency();
		$valid_currencies       = WC_Vivacom_Smart_Helpers::get_valid_currencies( $bearer_authentication );
		$valid_currencies_codes = array();

		foreach ( $valid_currencies as $currency ) {
			$valid_currencies_codes[] = Application::getCurrencyCode( $currency, true );
		}

		if ( ! in_array( $currency_code, $valid_currencies_codes, true ) ) {
			/* translators: error_message */
			$notices['errors'][] = sprintf( __( 'Invalid currency. Please check credentials or store currency. Allowed currencies are: %s', 'viva-com-smart-for-woocommerce' ), implode( ', ', $valid_currencies_codes ) );
		}

		// Check permalink.
		$permalink_structure = get_option( 'permalink_structure' );
		if ( ! $permalink_structure ) {
			$notices['errors'][] = __( 'Viva.com: Pretty permalinks must be enabled ( not plain text permalinks structure ).', 'viva-com-smart-for-woocommerce' );
		}

		return $notices;
	}

	/**
	 * General admin settings
	 *
	 * @return void
	 */
	public function admin_settings_start() {
		if ( ! self::valid_check() ) {
			return;
		}

		$this->admin_sources();
	}

	/**
	 * Save admin settings
	 */
	public function process_admin_options() {

		$old_client_id          = $this->get_option( 'client_id' );
		$old_client_secret      = $this->get_option( 'client_secret' );
		$old_demo_client_id     = $this->get_option( 'demo_client_id' );
		$old_demo_client_secret = $this->get_option( 'demo_client_secret' );

		parent::process_admin_options();

		$this->enabled            = $this->get_option( 'enabled' );
		$this->test_mode          = $this->get_option( 'test_mode' );
		$this->client_id          = $this->get_option( 'client_id' );
		$this->client_secret      = $this->get_option( 'client_secret' );
		$this->demo_client_id     = $this->get_option( 'demo_client_id' );
		$this->demo_client_secret = $this->get_option( 'demo_client_secret' );

		$environment = 'yes' === $this->test_mode ? 'demo' : 'live';

		if ( 'demo' === $environment ) {
			if ( $this->demo_client_id !== $old_demo_client_id || $this->demo_client_secret !== $old_demo_client_secret ) {
				$this->update_option( 'demo_source_code', '' );
			}
		} elseif ( $this->client_id !== $old_client_id || $this->client_secret !== $old_client_secret ) {
			$this->update_option( 'source_code', '' );
		}

		$this->demo_source_code = $this->get_option( 'demo_source_code' );
		$this->source_code      = $this->get_option( 'source_code' );

		$bearer_authentication = WC_Vivacom_Smart_Helpers::get_bearer_authentication( $environment );

		$source_code = '';
		if ( $bearer_authentication->hasValidToken() ) {
			$source = ( 'yes' === $this->test_mode ) ? $this->demo_source_code : $this->source_code;
			if ( empty( $source ) ) {
				$source_list = WC_Vivacom_Smart_Helpers::get_sources( $bearer_authentication );
				if ( count( $source_list ) > 0 ) {
					/**
					 * Source Item.
					 *
					 * @var SourceItem $source_item
					 */
					foreach ( $source_list as $source_item ) {
						$source_code = $source_item->getCode();
						break;
					}
					$this->update_option( 'source_error', ! empty( $source_code ) ? 'code_exists' : 'code_error' );
				} else {
					$source_item = WC_Vivacom_Smart_Helpers::create_source( $bearer_authentication );
					$source_code = ! empty( $source_item ) ? $source_item->getCode() : '';
					$this->update_option( 'source_error', ! empty( $source_code ) ? 'code_created' : 'code_error' );
				}
				if ( 'demo' === $environment ) {
					$this->update_option( 'demo_source_code', ( empty( $source_code ) ? '' : $source_code ) );
					$this->demo_source_code = $this->get_option( 'demo_source_code' );
				} else {
					$this->update_option( 'source_code', ( empty( $source_code ) ? '' : $source_code ) );
					$this->source_code = $this->get_option( 'source_code' );
				}
			}
			$webhook_creation_successful = WC_Vivacom_Smart_Helpers::create_webhook( $bearer_authentication );
			$this->update_option( 'webhook_created', $webhook_creation_successful ? 'yes' : 'error' );
		}
	}

	/**
	 * Loads and displays the sources in admin settings page
	 */
	private function admin_sources() {
		$environment                          = 'yes' == $this->test_mode ? 'demo' : 'live';
		$bearer_authentication                = WC_Vivacom_Smart_Helpers::get_bearer_authentication( $environment );
		$this->{"{$environment}_source_list"} = $bearer_authentication->hasValidToken() ? WC_Vivacom_Smart_Helpers::get_sources( $bearer_authentication ) : new SourceList();

		if ( count( $this->{"{$environment}_source_list"} ) > 0 ) {
            $source_code_key = 'demo' == $environment ? "{$environment}_source_code" : 'source_code';

            foreach ( $this->{"{$environment}_source_list"} as $key => $value ) {
				$this->form_fields[ $source_code_key ]['options'][ $value->getCode() ] =
					$value->getCode() . ' - ' . $value->getname() . ' - ' . $value->getDomain();
			}
		}
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields() {
		if ( ! empty( $this->description ) ) {
			echo '<p>' . esc_html( $this->description ) . '</p>';
		}
	}

	/**
	 * Check if the gateway needs setup (for WooCommerce React settings page).
	 *
	 * Returns true if the gateway is not properly configured, which will show
	 * the "Complete onboarding" button in the new WooCommerce payment settings.
	 *
	 * @return bool
	 */
	public function needs_setup() {
		// Check if credentials are set.
		if ( 'yes' === $this->test_mode ) {
			if ( empty( $this->demo_client_id ) || empty( $this->demo_client_secret ) ) {
				return true;
			}
		} else {
			if ( empty( $this->client_id ) || empty( $this->client_secret ) ) {
				return true;
			}
		}

		// Check if source code is configured.
		$source_code = 'yes' === $this->test_mode ? $this->demo_source_code : $this->source_code;
		if ( empty( $source_code ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Init form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = WC_Vivacom_Smart_Helpers::init_form_fields();
	}

	/**
	 * Validity check
	 *
	 * @param bool $check_enabled check_enabled.
	 *
	 * @return bool
	 */
	public function valid_check( $check_enabled = true ) {
		// Various checks: valid domain, enabled state, versions.
		if ( ! current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce-defined capability
			return false;
		}

		if ( is_cart() || is_checkout() ) {
			return false;
		}

		if ( $check_enabled && 'no' === $this->enabled ) {
			return false;
		}

		if ( function_exists( 'get_current_screen' ) ) {
			if ( 'woocommerce_page_wc-settings' !== get_current_screen()->id ) {
				return false;
			}
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- read-only page-routing comparison, value is never stored or output
		if ( empty( $_GET['section'] ) ) {
			return false;
		}

		if ( isset( $_GET['section'] ) && $this->id !== $_GET['section'] ) {
			return false;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return true;
	}
}
