jQuery( document ).ready(
	function() {

		var demoCheckbox = jQuery( '#woocommerce_vivacom_smart_test_mode' );
		var demoMode     = demoCheckbox.is( ':checked' );

		var advanced_settings_checkbox = jQuery( '#woocommerce_vivacom_smart_advanced_settings_enabled' );
		var advancedSettingsEnabled    = advanced_settings_checkbox.is( ':checked' );

		jQuery('#woocommerce_vivacom_smart_brand_color').wpColorPicker();

		var descriptor = jQuery('#woocommerce_vivacom_smart_dynamic_descriptor').first();
		descriptor.prop('maxlength', 13);
		var descriptorText = descriptor.val();
		var descriptorRow = jQuery( descriptor ).closest( 'tr' );

		var samplebank = vivacom_smart_admin_trans.sampleBank;
		var transactionReference = vivacom_smart_admin_trans.transactionReference;
		var amount = vivacom_smart_admin_trans.testAmount;
		var yourCompanyName = vivacom_smart_admin_trans.yourCompanyName;
		var currencySymbol = vivacom_smart_admin_trans.currencySymbol;

		var descriptorPreview =
			'<tr id="vivacom_descriptor_preview">' +
			'<td colspan="2" style="text-align:center; padding:0;">' +
			'<div style="display:inline-block;'
			+ 'border:1px solid #ddd; border-radius:4px; '
			+ 'padding:12px; max-width:fit-content;'
			+ 'background-color:lightgrey;">' +
			'<p style="margin:0 0 8px; font-weight:bold; '
			+ 'text-transform:uppercase; text-align:center;">' +
			samplebank +
			'</p>' +
			'<table style="width:100%; border-collapse:collapse;">' +
			'<thead>' +
			'<tr style="display:flex; justify-content:space-between;">' +
			'<th style="padding: 0;">' + transactionReference + '</th>' +
			'<th style="text-align:end; padding: 0;">' + amount + '</th>' +
			'</tr>' +
			'</thead>' +
			'<tbody>' +
			'<tr style="display:flex; justify-content:space-between;">' +
			'<td style="color:#0a4b78; font-size:15px;">' +
			'<span>' + yourCompanyName + ' </span>' +
			'<span id="vivacom_descriptor_preview_text">' +
			descriptorText +
			'</span>' +
			'</td>' +
			'<td>' + currencySymbol + 20.00 + '</td>' +
			'</tr>' +
			'</tbody>' +
			'</table>' +
			'</div>' +
			'</td>' +
			'</tr>';

		descriptorRow.after(descriptorPreview);

		if ( demoMode ) {
			jQuery( '#woocommerce_vivacom_smart_client_id' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_client_secret' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_source_code' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_title_live' ).hide();
		} else {
			jQuery( '#woocommerce_vivacom_smart_demo_client_id' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_demo_client_secret' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_demo_source_code' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_title_demo' ).hide();
		}

		if ( advancedSettingsEnabled) {
			jQuery( '#woocommerce_vivacom_smart_main_descr' ).show();
			jQuery( '#woocommerce_vivacom_smart_title' ).closest( 'tr' ).show();
			jQuery( '#woocommerce_vivacom_smart_description' ).closest( 'tr' ).show();
			jQuery( '#woocommerce_vivacom_smart_order_status' ).closest( 'tr' ).show();
			jQuery( '#woocommerce_vivacom_smart_logo_enabled' ).closest( 'tr' ).show();
			jQuery( '#woocommerce_vivacom_smart_installments' ).closest( 'tr' ).show();
			jQuery( '#woocommerce_vivacom_smart_brand_color' ).closest( 'tr' ).show();
			jQuery( '#woocommerce_vivacom_smart_enable_preauthorizations' ).closest( 'tr' ).show();
			descriptorRow.show();
			jQuery('#vivacom_descriptor_preview').show();

			if (demoCheckbox.is( ':checked' )) {
				jQuery( '#woocommerce_vivacom_smart_demo_source_code' ).closest( 'tr' ).show();
			} else {
				jQuery( '#woocommerce_vivacom_smart_source_code' ).closest( 'tr' ).show();
			}

		} else {
			jQuery( '#woocommerce_vivacom_smart_main_descr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_title' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_description' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_order_status' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_logo_enabled' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_installments' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_demo_source_code' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_source_code' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_brand_color' ).closest( 'tr' ).hide();
			jQuery( '#woocommerce_vivacom_smart_enable_preauthorizations' ).closest( 'tr' ).hide();
			descriptorRow.hide();
			jQuery('#vivacom_descriptor_preview').hide();

		}

		advanced_settings_checkbox.on(
			'change',
			function() {
				jQuery( '#woocommerce_vivacom_smart_main_descr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_title' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_description' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_order_status' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_logo_enabled' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_installments' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_brand_color' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_enable_preauthorizations' ).closest( 'tr' ).toggle();
				descriptorRow.toggle();
				jQuery('#vivacom_descriptor_preview').toggle();

				if (demoCheckbox.is( ':checked' )) {
					jQuery( '#woocommerce_vivacom_smart_demo_source_code' ).closest( 'tr' ).toggle();
				} else {
					jQuery( '#woocommerce_vivacom_smart_source_code' ).closest( 'tr' ).toggle();
				}
			}
		);

		demoCheckbox.on(
			'change',
			function(){
				jQuery( '#woocommerce_vivacom_smart_client_id' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_client_secret' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_demo_client_id' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_demo_client_secret' ).closest( 'tr' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_title_live' ).toggle();
				jQuery( '#woocommerce_vivacom_smart_title_demo' ).toggle();

				if ( advanced_settings_checkbox.is( ':checked' ) ) {
					jQuery( '#woocommerce_vivacom_smart_demo_source_code' ).closest( 'tr' ).toggle();
					jQuery( '#woocommerce_vivacom_smart_source_code' ).closest( 'tr' ).toggle();
				}
			}
		)

		descriptor.on('input', function(e) {
			jQuery('#vivacom_descriptor_preview_text').html(e.target.value);
		});
	}
)
