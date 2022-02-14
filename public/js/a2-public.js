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

/**
 * Função responsável por buscar children terms via ajax
 * segundo o ID do elemento recebido como parâmetro;
 * 
 * @param element el 
 */
function getLocalizationChildrenTerms( el ){;
	let options = el.children();

	options.each( function( index, element ) {
		if( element.selected === true ){
			let _self = jQuery(this),
				term = _self.attr('term-id')
				type = _self.attr('children-type');
	
				console.log(  );
			dataSend = {
				action: 'listChildrenTerms',
				nonce: publicAjax.nonce,
				termId: term,
			}
			
			jQuery.ajax({
				type: "POST",
				url: publicAjax.url,
				data: dataSend,
			})
			.done( function( data ){
				fillLocalizationOptions(data, type);
			});
		}
	});
}

/**
 * Função responsável por preencher as opções conforme o type do select
 * Opções referentes a localização na edição do perfil
 * 
 * @param string options
 * @param strig type
 */
function fillLocalizationOptions( options, type ){
	let _items = JSON.parse(options),
		_select = '';

	switch ( type ) {
		case 'states':
			_select 	= jQuery('#_profile_state');
			_childType 	= 'cities';
			break;
		case 'cities':
			_select 	= jQuery('#_profile_city');
			_childType 	= 'districts';
			break;
		case 'districts':
			_select 	= jQuery('#_profile_district');
			_childType 	= '';
			break;	
		default:
			break;
	}
	if( _items.length !== 0 ){
		let elements = '<option value="" disabled selected>Selecione uma opção</option>';

		jQuery.each( _items, function( index, item ){
			elements += '<option value="'+ item.name +'" term-id="'+ item.id +'" children-type="'+ _childType +'">'+ item.name +'</option>'
		});

		_select.html(elements);
		_select.attr('disabled', false);
		_select.formSelect();
	}
}