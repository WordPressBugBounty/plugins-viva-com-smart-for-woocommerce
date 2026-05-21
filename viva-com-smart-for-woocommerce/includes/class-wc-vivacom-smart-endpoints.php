<?php
/**
 * Endpoints
 *
 * @package   VivaComSmartForWooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

use VivaComSmartCheckout\Vivawallet\VivawalletPhp\Application;

if ( ! class_exists( 'WC_Vivacom_Smart_Endpoints' ) ) {
	/**
	 * Class WC_Vivacom_Smart_Endpoints
	 */
	class WC_Vivacom_Smart_Endpoints {

		/**
		 * WC_Vivacom_Smart_Endpoints constructor
		 */
		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'create_payments_methods_endpoint' ) );
			add_action( 'woocommerce_api_wc_vivacom_smart_success', array( $this, 'check_hook_response_success' ) );
			add_action( 'woocommerce_api_wc_vivacom_smart_fail', array( $this, 'check_hook_response_fail' ) );
			add_action( 'woocommerce_thankyou_vivacom_smart', array( $this, 'notify_pending_payment' ) );
		}

		/**
		 * Notify pending payment
		 *
		 *
		 * @return void
		 */
        public function notify_pending_payment() {
            // phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- read-only comparison, value is never stored or output
            if ( 'F' !== $_GET['payment_status'] ) {
            // phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                echo '<p>' . esc_html__( 'Order is currently awaiting payment. After successful payment, we will send you an email confirmation.', 'viva-com-smart-for-woocommerce' ) . '</p>';
            }
        }
		/**
		 * Create payment webhook
		 */
		public function create_payments_methods_endpoint() {
			register_rest_route(
				WC_Vivacom_Smart_Helpers::WEBHOOK_NAMESPACE,
				WC_Vivacom_Smart_Helpers::WEBHOOK_URI,
				array(
					array(
						'methods'             => 'GET',
						'callback'            => 'WC_Vivacom_Smart_Endpoints::payments_methods_endpoint_get_callback',
						'permission_callback' => '__return_true',
					),
					array(
						'methods'             => 'POST',
						'callback'            => 'WC_Vivacom_Smart_Endpoints::payments_methods_endpoint_post_callback',
						'permission_callback' => '__return_true',
						'args'                => array(
							'EventTypeId' => array(
								'required'          => true,
								'type'              => 'integer',
								'sanitize_callback' => 'absint',
							),
							'EventData'   => array(
								'required'          => true,
								'type'              => 'object',
								'validate_callback' => function ( $value ) {
									return is_array( $value ) && isset( $value['OrderCode'] );
								},
							),
						),
					),
				)
			);
		}

		/**
		 * Get callback
		 *
		 * @param object $request request.
		 *
		 * @return mixed|WP_Error|WP_HTTP_Response|WP_REST_Response
		 */
		public static function payments_methods_endpoint_get_callback( $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed

			$response_key = WC_Vivacom_Smart_Helpers::get_verification_token();
			$data         = array( 'key' => $response_key );
			WC_Vivacom_Smart_Logger::log( 'Payments methods endpoint get callback\n Key: ' . wp_json_encode( $response_key ) );

			return rest_ensure_response( new WP_REST_Response( $data ) );
		}


	/**
	 * Post callback
	 *
	 * @param object $request request.
	 *
	 * @return WP_REST_Response
	 */
	public static function payments_methods_endpoint_post_callback( $request ) {

		$parameters = json_decode( $request->get_body(), true, 512, JSON_BIGINT_AS_STRING );
		$res        = array( 'status_message' => 'Success' );
		$event_data = $parameters['EventData'];
		$event_type_id_array = array( 'transaction_created' => 1796, 'transaction_failed' => 1798 );

		// Validate webhook request.
        $order_code  = (string) $event_data['OrderCode'];
		$validation_result = WC_Vivacom_Smart_Helpers::validate_webhook_request( $event_data, $order_code );

		// If validation failed, log reason and return.
		if ( true !== $validation_result['valid'] ) {
			WC_Vivacom_Smart_Logger::log( 'Webhook ignored: ' . $validation_result['reason'] );
			return new WP_REST_Response( $res, 200 );
		}

		$transaction = array(
			'id'     => (string) $event_data['TransactionId'],
			'typeId' => (int) $event_data['TransactionTypeId'],
		);

        $wc_order_id = WC_Vivacom_Smart_Helpers::get_db_order_by_order_code( $order_code );
        $order = wc_get_order( $wc_order_id );

		WC_Vivacom_Smart_Logger::log( "Payments methods endpoint post callback\n Smart checkout\n Request: " . wp_json_encode( $parameters ) );

		$event_type_id = isset( $parameters['EventTypeId'] ) ? $parameters['EventTypeId'] : null;

		if ( $event_type_id_array['transaction_created'] === $event_type_id ) {

			return WC_Vivacom_Smart_Helpers::handle_successful_payment( $order, $order_code, $transaction, $event_data );
		}

		if ( $event_type_id_array['transaction_failed'] === $event_type_id ) {
			$note = __( 'Transaction failed using Viva.com Smart Checkout', 'viva-com-smart-for-woocommerce' );

			if ( isset( $parameters['CorrelationId'] ) && ! empty( $parameters['CorrelationId'] ) ) {
				$note .= __( 'with Viva-CorrelationId: ', 'viva-com-smart-for-woocommerce' ) . $parameters['CorrelationId'];

			} else {
				$note .= '.';
			}

			$order->add_order_note( $note, false );
			$order->save();
		}

		return new WP_REST_Response( $res, 200 );
	}

		/**
		 * Success handler
		 *
		 * @return void
		 */
		public function check_hook_response_success() {

			// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- guard-only empty() check, values are sanitized before use below
			if ( empty( $_GET['t'] ) || empty( $_GET['s'] ) ) {
				wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
                exit();
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$order_code = sanitize_text_field( wp_unslash( $_GET['s'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$wc_order_id = WC_Vivacom_Smart_Helpers::get_db_order_by_order_code( $order_code );

            if ( empty( $wc_order_id ) ) {
                WC_Vivacom_Smart_Logger::log( 'Success callback: no order found for order_code ' . $order_code );
                wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
                exit();
            }

            $order = wc_get_order( $wc_order_id );

            if ( ! $order ) {
                WC_Vivacom_Smart_Logger::log( 'Success callback: wc_get_order returned false for order_id ' . $wc_order_id );
                wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
                exit();
            }

            $valid_statuses = array( 'pending', 'processing', 'completed', 'on-hold' );

            $transaction_id = sanitize_text_field( wp_unslash( $_GET['t'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

            if (  $order->get_status() === 'pending' && $transaction_id ) {
                $viva_settings              = get_option( 'woocommerce_vivacom_smart_settings' );
                $environment               = 'yes' === $viva_settings['test_mode'] ? 'demo' : 'live';
                $bearer_authentication = WC_Vivacom_Smart_Helpers::get_bearer_authentication( $environment );
                $transaction_response  = WC_Vivacom_Smart_Helpers::get_transaction( $bearer_authentication, $transaction_id);

                if ( empty( $transaction_response ) || empty( $transaction_response->orderCode ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
                    WC_Vivacom_Smart_Logger::log( "Cannot retrieve transactionId  " . $transaction_id . "  for orderCode " . $order_code );
                    wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
                    exit();
                }
            }


            if ( in_array( $order->get_status(), $valid_statuses, true ) ) {
                $url = $order->get_checkout_order_received_url();
                if ( isset( $transaction_response ) ) {
                    $url = add_query_arg( 'payment_status', sanitize_text_field( $transaction_response->statusId ), $url ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
                }
                wp_safe_redirect( esc_url_raw( $url ) );
                exit();
            }

            wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
            exit();
		}

		/**
		 * Fail handler
		 *
		 * @return void
		 */
		public function check_hook_response_fail() {

			// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- guard-only empty() check, value is sanitized on assignment below
			if ( empty( $_GET['s'] ) ) {
			// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				wc_add_notice( __( 'There was a problem processing your payment. Please try again', 'viva-com-smart-for-woocommerce' ), 'error' );
				wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
				exit();
			}

			$order_code = sanitize_text_field( wp_unslash( $_GET['s'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$cancelled  = isset( $_GET['cancel'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['cancel'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$wc_order_id = WC_Vivacom_Smart_Helpers::get_db_order_by_order_code( $order_code );

			if ( empty( $wc_order_id ) ) {

				wc_add_notice( __( 'There was a problem processing your payment. Please try again', 'viva-com-smart-for-woocommerce' ), 'error' );
				wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
				exit();

			}

			$order = wc_get_order( $wc_order_id );

			if ( $cancelled ) {
				$viva_settings         = get_option( 'woocommerce_vivacom_smart_settings' );
				$environment           = 'yes' === $viva_settings['test_mode'] ? 'demo' : 'live';
				$bearer_authentication = WC_Vivacom_Smart_Helpers::get_bearer_authentication( $environment );
				$order_response        = WC_Vivacom_Smart_Helpers::get_order( $bearer_authentication, array( 'orderCode' => $order_code ) );
				$state_id               = $order_response->stateId; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			}

			if ( isset( $state_id ) && 2 == $state_id ) {
				$order->update_status( 'cancelled', __( 'Unpaid order cancelled - customer cancelled in smart checkout.', 'viva-com-smart-for-woocommerce' ) );
				wc_add_notice( __( 'The Order was cancelled', 'viva-com-smart-for-woocommerce' ), 'error' );
				wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
				exit();
			}

			wc_add_notice( __( 'There was a problem processing your payment. Please try again', 'viva-com-smart-for-woocommerce' ), 'error' );
			wp_safe_redirect( esc_url_raw( ( wc_get_checkout_url() ) ) );
			exit();
		}
	}

	new WC_Vivacom_Smart_Endpoints();
}
