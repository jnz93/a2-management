(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

function profileActivateMasks()
{
	jQuery('#_profile_whatsapp').mask('(00) 00000-0000');
	jQuery('#_profile_height').mask('0,00');
	jQuery('#_profile_weight').mask('000');
	jQuery('#_profile_tits_size').mask('000');
	jQuery('#_profile_bust_size').mask('000');
	jQuery('#_profile_waist_size').mask('000');
	jQuery('#_profile_cep').mask('00000-000');
	jQuery('#_profile_cache_quickie').mask('000.000.000.000.000,00', {reverse: true});
	jQuery('#_profile_cache_half_an_hour').mask('000.000.000.000.000,00', {reverse: true});
	jQuery('#_profile_cache_hour').mask('000.000.000.000.000,00', {reverse: true});
	jQuery('#_profile_cache_overnight_stay').mask('000.000.000.000.000,00', {reverse: true});
	jQuery('#_profile_cache_promotion').mask('000.000.000.000.000,00', {reverse: true});
}