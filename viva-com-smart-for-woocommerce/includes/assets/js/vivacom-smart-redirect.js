( function () {
	var submit = document.getElementById( 'submit_vivacom_smart_payment_form' );

	if ( ! submit ) {
		return;
	}

	var params = window.vivacom_smart_redirect_params || {};

	var overlay = document.createElement( 'div' );
	overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:#fff;opacity:0.6;z-index:9998;cursor:wait;';

	var message = document.createElement( 'div' );
	message.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);padding:20px;text-align:center;color:#555;border:3px solid #aaa;background-color:#fff;cursor:wait;line-height:32px;z-index:9999;max-width:90%;';
	message.textContent = params.redirect_message || '';

	document.body.appendChild( overlay );
	document.body.appendChild( message );

	submit.click();
} )();
