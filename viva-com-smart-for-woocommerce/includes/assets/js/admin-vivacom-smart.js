( function() {

	function init() {

		function getRow( element ) {
			return element ? element.closest( 'tr' ) : null;
		}

		function getRowById( id ) {
			return getRow( document.getElementById( id ) );
		}

		function show( element ) {
			if ( element ) {
				element.style.display = '';
			}
		}

		function hide( element ) {
			if ( element ) {
				element.style.display = 'none';
			}
		}

		function toggle( element ) {
			if ( ! element ) {
				return;
			}

			if ( window.getComputedStyle( element ).display === 'none' ) {
				show( element );
			} else {
				hide( element );
			}
		}

		function isVisible( element ) {
			return !! ( element && element.offsetParent );
		}

		var demoCheckbox = document.getElementById( 'woocommerce_vivacom_smart_test_mode' );
		var demoMode     = demoCheckbox ? demoCheckbox.checked : false;

		var advanced_settings_checkbox = document.getElementById( 'woocommerce_vivacom_smart_advanced_settings_enabled' );
		var advancedSettingsEnabled    = advanced_settings_checkbox ? advanced_settings_checkbox.checked : false;

		var brandColorField = document.getElementById( 'woocommerce_vivacom_smart_brand_color' );

		if ( brandColorField ) {
			var brandColorPicker = document.createElement( 'input' );

			brandColorPicker.type                = 'color';
			brandColorPicker.id                  = 'vivacom_brand_color_picker';
			brandColorPicker.style.marginLeft    = '8px';
			brandColorPicker.style.verticalAlign = 'middle';

			if ( /^#?[0-9a-fA-F]{6}$/.test( brandColorField.value.trim() ) ) {
				brandColorPicker.value = '#' + brandColorField.value.trim().replace( '#', '' );
			}

			brandColorField.insertAdjacentElement( 'afterend', brandColorPicker );

			brandColorPicker.addEventListener(
				'input',
				function() {
					brandColorField.value = brandColorPicker.value;
				}
			);

			brandColorField.addEventListener(
				'input',
				function() {
					var value = brandColorField.value.trim();

					if ( /^#?[0-9a-fA-F]{6}$/.test( value ) ) {
						brandColorPicker.value = '#' + value.replace( '#', '' );
					}
				}
			);
		}

		var descriptor = document.getElementById( 'woocommerce_vivacom_smart_dynamic_descriptor' );

		if ( descriptor ) {
			descriptor.maxLength = 13;
		}

		var descriptorText = descriptor ? descriptor.value : '';
		var descriptorRow  = getRow( descriptor );

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

		if ( descriptorRow ) {
			descriptorRow.insertAdjacentHTML( 'afterend', descriptorPreview );
		}

		if ( demoMode ) {
			hide( getRowById( 'woocommerce_vivacom_smart_client_id' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_client_secret' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_source_code' ) );
			hide( document.getElementById( 'woocommerce_vivacom_smart_title_live' ) );
		} else {
			hide( getRowById( 'woocommerce_vivacom_smart_demo_client_id' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_demo_client_secret' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_demo_source_code' ) );
			hide( document.getElementById( 'woocommerce_vivacom_smart_title_demo' ) );
		}

		if ( advancedSettingsEnabled ) {
			show( document.getElementById( 'woocommerce_vivacom_smart_main_descr' ) );
			show( getRowById( 'woocommerce_vivacom_smart_title' ) );
			show( getRowById( 'woocommerce_vivacom_smart_description' ) );
			show( getRowById( 'woocommerce_vivacom_smart_order_status' ) );
			show( getRowById( 'woocommerce_vivacom_smart_logo_enabled' ) );
			show( getRowById( 'woocommerce_vivacom_smart_installments' ) );
			show( getRowById( 'woocommerce_vivacom_smart_brand_color' ) );
			show( getRowById( 'woocommerce_vivacom_smart_enable_preauthorizations' ) );
			show( descriptorRow );
			show( document.getElementById( 'vivacom_descriptor_preview' ) );

			if ( demoMode ) {
				show( getRowById( 'woocommerce_vivacom_smart_demo_source_code' ) );
			} else {
				show( getRowById( 'woocommerce_vivacom_smart_source_code' ) );
			}

		} else {
			hide( document.getElementById( 'woocommerce_vivacom_smart_main_descr' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_title' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_description' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_order_status' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_logo_enabled' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_installments' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_demo_source_code' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_source_code' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_brand_color' ) );
			hide( getRowById( 'woocommerce_vivacom_smart_enable_preauthorizations' ) );
			hide( descriptorRow );
			hide( document.getElementById( 'vivacom_descriptor_preview' ) );

		}

		if ( advanced_settings_checkbox ) {
			advanced_settings_checkbox.addEventListener(
				'change',
				function() {
					toggle( document.getElementById( 'woocommerce_vivacom_smart_main_descr' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_title' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_description' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_order_status' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_logo_enabled' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_installments' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_brand_color' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_enable_preauthorizations' ) );
					toggle( descriptorRow );
					toggle( document.getElementById( 'vivacom_descriptor_preview' ) );

					if ( demoCheckbox && demoCheckbox.checked ) {
						toggle( getRowById( 'woocommerce_vivacom_smart_demo_source_code' ) );
					} else {
						toggle( getRowById( 'woocommerce_vivacom_smart_source_code' ) );
					}
				}
			);
		}

		if ( demoCheckbox ) {
			demoCheckbox.addEventListener(
				'change',
				function() {
					toggle( getRowById( 'woocommerce_vivacom_smart_client_id' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_client_secret' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_demo_client_id' ) );
					toggle( getRowById( 'woocommerce_vivacom_smart_demo_client_secret' ) );
					toggle( document.getElementById( 'woocommerce_vivacom_smart_title_live' ) );
					toggle( document.getElementById( 'woocommerce_vivacom_smart_title_demo' ) );

					if ( advanced_settings_checkbox && advanced_settings_checkbox.checked ) {
						toggle( getRowById( 'woocommerce_vivacom_smart_demo_source_code' ) );
						toggle( getRowById( 'woocommerce_vivacom_smart_source_code' ) );
					}
				}
			);
		}

		if ( descriptor ) {
			descriptor.addEventListener(
				'input',
				function( e ) {
					var previewText = document.getElementById( 'vivacom_descriptor_preview_text' );

					if ( previewText ) {
						previewText.textContent = e.target.value;
					}
				}
			);
		}

		var installmentsField = document.getElementById( 'woocommerce_vivacom_smart_installments' );
		var installmentsError = vivacom_smart_admin_trans.installmentsError;

		// Validates the instalments pattern: comma-separated amount:instalments pairs (e.g. 90:3,180:6). Empty is allowed.
		function isValidInstallments( value ) {
			var installmentsPattern = value.trim();

			if ( installmentsPattern === '' ) {
				return true;
			}

			var pairs = installmentsPattern.split( ',' );

			for ( var i = 0; i < pairs.length; i++ ) {
				if ( ! /^\s*\d+(\.\d+)?\s*:\s*\d+\s*$/.test( pairs[ i ] ) ) {
					return false;
				}
			}

			return true;
		}

		function toggleInstallmentsError( showError ) {
			var error = installmentsField.nextElementSibling;

			if ( error && ! error.classList.contains( 'vivacom-installments-error' ) ) {
				error = null;
			}

			if ( showError ) {
				installmentsField.style.borderColor = '#dc3232';
				if ( ! error ) {
					installmentsField.insertAdjacentHTML(
						'afterend',
						'<p class="vivacom-installments-error" style="color:#dc3232; margin:4px 0 0;">' + installmentsError + '</p>'
					);
				}
			} else {
				installmentsField.style.borderColor = '';
				if ( error ) {
					error.remove();
				}
			}
		}

		if ( installmentsField ) {
			var installmentsForm = installmentsField.closest( 'form' );

			function refreshInstallmentsState() {
				// Skip while the field is hidden (advanced settings disabled) so a
				// stale saved value doesn't show an error against an invisible field.
				if ( ! isVisible( installmentsField ) ) {
					toggleInstallmentsError( false );
					return;
				}
				toggleInstallmentsError( ! isValidInstallments( installmentsField.value ) );
			}

			installmentsField.addEventListener( 'input', refreshInstallmentsState );
			installmentsField.addEventListener( 'blur', refreshInstallmentsState );

			if ( installmentsForm ) {
				installmentsForm.addEventListener(
					'submit',
					function( e ) {
						if ( isVisible( installmentsField ) && ! isValidInstallments( installmentsField.value ) ) {
							e.preventDefault();
							refreshInstallmentsState();

							var installmentsRow = installmentsField.closest( 'tr' );
							var scrollTop       = installmentsRow.getBoundingClientRect().top + window.pageYOffset - 100;

							window.scrollTo( { top: scrollTop, behavior: 'smooth' } );
							installmentsField.focus();
						}
					}
				);
			}

			// Re-evaluate when the field is shown/hidden via the advanced settings toggle.
			if ( advanced_settings_checkbox ) {
				advanced_settings_checkbox.addEventListener( 'change', refreshInstallmentsState );
			}

			// Run once on load to cover an already-saved invalid value.
			refreshInstallmentsState();
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

} )();
