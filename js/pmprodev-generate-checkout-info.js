function generate_checkout_info() {
    jQuery.noConflict().ajax({
      url: 'https://randomuser.me/api/?nat=us',
      dataType: 'json',
      success: function( data ) {
        const results = data['results'][0];
        
        // Generate email address
        const username = results.name.first + '.' + results.name.last;
        const base_email = jQuery('#pmprodev-base-email').val();
        const at_index = base_email.indexOf("@");
        const user_email = base_email.substring(0, at_index) + '+' + username + base_email.substring(at_index);
        
        jQuery('#username').val( username );
        jQuery('#password').val( username );
        jQuery('#password2').val( username );
        jQuery('#bemail').val( user_email );
        jQuery('#bconfirmemail').val( user_email );
        jQuery('#first_name').val( results.name.first );
        jQuery('#last_name').val( results.name.last );
        jQuery('#bfirstname').val( results.name.first );
        jQuery('#blastname').val( results.name.last );
        jQuery('#baddress1').val( results.location.street.number  + ' ' +  results.location.street.name );
        jQuery('#bcity').val( results.location.city );
        jQuery('#bstate').val( results.location.state );
        jQuery('#bzipcode').val( results.location.postcode );
        jQuery('#bphone').val( results.phone );
        jQuery('#AccountNumber').val( "4242424242424242" );
        jQuery('#ExpirationYear').val( "2028" );
        jQuery('#CVV').val( "123" );
      }
    });
}

/**
 * jQuery ready function.
 */
jQuery(document).ready(function ( $ ) {
	$('#pmprodev-generate').click(function () {
		generate_checkout_info();
	});

	/**
	 * Enable the generate button if the email address is valid.
	 */
	$('#pmprodev-base-email').change(function () {
		const email = $(this).val();
		if ( email.includes('@')) {
			//Enable the generate button
			$('#pmprodev-generate').prop('disabled', false);
		} else {
			//Disable the generate button
			$('#pmprodev-generate').prop('disabled', true);
		}
	});
});