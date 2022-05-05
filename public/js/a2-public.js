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
			elements += '<option value="'+ item.id +'" term-id="'+ item.id +'" children-type="'+ _childType +'">'+ item.name +'</option>'
		});

		_select.html(elements);
		_select.attr('disabled', false);
		_select.formSelect();
	}
}

/**
 * Função responsável pelo upload de arquivos via ajax
 * Utilizada principalmente no upload da foto de perfil e capa
 * 
 * @param {*} el 
 */
function uploadImage(el){

	let formData 	= new FormData(),
		fileList 	= el[0].files,
		img 		= fileList[0],
		viewEl 		= el.parents('div.mb-3').siblings('div.thumbnail-view'),
		inputId 	= el.parent().siblings('input'),
		spinner 	= viewEl.children('div.spinner-border');

	formData.append('action', 'upload_attachment');
	formData.append('file', img);
	
	if (fileList.length > 0) {
		jQuery.ajax({
			type: 'POST',
			url: publicAjax.url,
			processData: false,
			contentType: false,
			data: formData,
			beforeSend: function () {
				spinner.removeClass('d-none');
			},
			success: function(response) {
				spinner.addClass('d-none');
			},
			error: function( request, status, error ) {
				console.log(status);
				console.log(request);
			},
		})
		.done(function( data ){
			data = JSON.parse(data);
			applyAttachOnElement( data.attachUrl, viewEl );
			inputId.val(data.attachId);
		});
	}
}

/**
 * Está função recebe uma URL de imagem e aplica ela no elemento destino
 * Está função deve ser chamada após o upload de uma imagem
 * 
 * @param {*} attachUrl
 * @param {*} viewEl
 */
function applyAttachOnElement( attachUrl, viewEl ){
	if( attachUrl.length < 55 ){
		console.log('Url Inválida.')
		console.log(attachUrl);
		return;
	}
	viewEl.css({
		'background-image': 'url('+ attachUrl +')'
	});
}

/**
 * Função responsável pelo upload de fotos e vídeos da galeria do acompanhante
 * @param {*} el 
 */
function uploadGallery(el){

	let formData 		= new FormData(),
		fileList 		= el[0].files;

	formData.append('action', 'upload_gallery');

	if (fileList.length > 0 ) {
		var payload = galleryPayload( fileList, formData );
		
		jQuery.ajax({
			type: 'POST',
			url: publicAjax.url,
			processData: false,
			contentType: false,
			data: payload,
			beforeSend: function () {
				
			},
			success: function(response) {
				// spinner.addClass('d-none');
			},
			error: function( request, status, error ) {
				console.log(status);
				console.log(request);
			},
		})
		.done(function( data ){
			jQuery(".galleryItemLoad").remove();
			let listGallery = JSON.parse( data );
			listGallery.forEach( function(item){
				addItemGallery(item);
			});
		});
	}
}

/**
 * Recebe a lista de arquivos que serão enviados e o objeto formData
 * monta e retorna o payload para que será passado no ajax
 * @returns payload
 */
function galleryPayload( files, formData ){
	jQuery.each( files, function( i, file ){
		formData.append('files['+ i +']', file);
		addItemSpinner();
	});

	return formData;
}

/**
 * Função responsável por inserir "spinner" em itens da galeria
 * enquanto os arquivos estão sendo enviados.
 * 
 * @returns html
 */
function addItemSpinner(){
	galleryWrapper 	= jQuery('#galleryList'),
	galleryWrapper.prepend( 
		`<li id="" data-attachment="" class="galleryItemLoad col-4">
			<div class="spinner-border text-secondary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</li>`
	);
}

/**
 * Está função recebe um objeto json e retorna o template de item da galeria a mídia upada
 *  
 * @param {*} attachData
 * @returns html
 */
function addItemGallery( data ){
	galleryWrapper 	= jQuery('#galleryList');
	galleryWrapper.prepend(
		`<li id="${data.attachId}" data-attachment="${data.attachId}" class="galleryItem col-4">
			<div class="thumbActions py-2 row">
				<span class="col-8">
					<input class="form-check-input" type="checkbox" id="" data-attachment="${data.attachId}">
				</span>
				<div class="col-4 d-flex justify-content-end">
					<span class="me-3" id="close" data-attachment="${data.attachId}">
						<i class="bi bi-pencil-square"></i>
					</span>
					<span class="" id="edit" data-attachment="${data.attachId}">
						<i class="bi bi-trash3-fill"></i>
					</span>
				</div>
			</div>
			<div class="thumbnail">
				<img src="${data.attachUrl}" alt="${data.title}" class="">
			</div>
			<div class="caption">
				<span class="thumb-title">${data.title}</span>
			</div>
		</li>`
	);
};

/**
 * Na seleção em massa: Faz a coleta dos ids dos itens selecionados para exclusão 
 * Adiciona os valores em formato string no input#_profile_gallery_remove_list
 * Invoca a função de ativação do botão de exclusão quando algum check for selecionado
 * Invoca a função de desativação do botão de exclusão quando não há check selecionado
 * 
 * @param {*} self
 * @return void
 */
function collectItemsToRemoveFromGallery( self ){
	let excludeActionBar 	= jQuery('#excludeActionBar')
		countBadge 			= excludeActionBar.find('span.badge'),
		inputRemove			= jQuery('#_profile_gallery_remove_list'),
		currExcludeList		= inputRemove.val(),
		excludeList 		= [],
		attachID 			= self.attr('data-attachment');

	if( currExcludeList.length > 0 ){
		excludeList = currExcludeList.split(',');
	}

	if( self.is(':checked') ){
		excludeList.push(attachID);
	} else {
		let index = excludeList.indexOf( attachID );

		if( index > -1 ){
			excludeList.splice(index, 1);
		}
	}

	/** Adicionar lista de exclusão no input */
	let countExcludeList 	= excludeList.length,
		excludeListStr 		= excludeList.join(',');

	/** Ativando #excludeActionBar */
	if( excludeListStr.length > 0 ){
		countBadge.text(countExcludeList);
		excludeActionBar.removeClass('d-none').addClass('d-flex');
	} else {
		countBadge.text(countExcludeList);
		excludeActionBar.removeClass('d-flex').addClass('d-none');
	}
	inputRemove.val(excludeListStr);
}

/**
 * Está função é responsável por fazer a requisição ajax de exclusão de itens da galeria
 * Chamada no click do botão "#btnSubmitItemsToRemoveFromGallery"
 * Pega a lista de Id's no input "#_profile_gallery_remove_list"
 * 
 */
function submitItemsToRemoveFromGallery(){
	let excludeList 		= jQuery('#_profile_gallery_remove_list').val(),
		excludeActionBar 	= jQuery('#excludeActionBar'),
		excludeListArr;

	if( excludeList.length > 0 ){
		excludeListArr = excludeList.split(',');
	}

	if ( excludeListArr.length > 0 && excludeListArr != 'undefined' ) {
		var payload = {
			'action': 'remove_gallery_items',
			'excludeList': excludeListArr
		};
		
		jQuery.ajax({
			type: 'POST',
			url: publicAjax.url,
			data: payload,
			beforeSend: function () {
				excludeListArr.forEach( function( id ){
					let item = '#'+id;
					jQuery(item).prepend(`
						<div class="spinner-border text-secondary" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>`
					);
				});
			},
			success: function(response) {
				// spinner.addClass('d-none');
			},
			error: function( request, status, error ) {
				console.log(status);
				console.log(request);
			},
		})
		.done(function( data ){
			console.log(data);
			excludeListArr.forEach( function( id ){
				let item = '#'+id;
				jQuery(item).remove();
			});
			excludeActionBar.addClass('d-none').removeClass('d-flex');
		});
	}
}

/**
 * Função responsável por upload de arquivos de vídeo via ajax
 * 
 * @param {*} el 
 */
 function uploadMedia(el){

	let formData 	= new FormData(),
		fileList 	= el[0].files,
		img 		= fileList[0],
		viewEl 		= el.parents('div.mb-3').siblings('div.thumbnail-view'),
		inputId 	= el.parent().siblings('input'),
		spinner 	= viewEl.children('div.spinner-border');

	formData.append('action', 'upload_video');
	formData.append('file', img);
	
	if (fileList.length > 0) {
		jQuery.ajax({
			type: 'POST',
			url: publicAjax.url,
			processData: false,
			contentType: false,
			data: formData,
			beforeSend: function () {
				spinner.removeClass('d-none');
			},
			success: function(response) {
				spinner.addClass('d-none');
			},
			error: function( request, status, error ) {
				console.log(status);
				console.log(request);
			},
		})
		.done(function( data ){
			data = JSON.parse(data);
			applyAttachOnElement( data.attachUrl, viewEl );
			inputId.val(data.attachId);
		});
	}
}


/**
 * Função responsável por criar o payload de verificação
 * e submter os dados via ajax;
 * 
 * @param {*} el
 */
function requestProfileEvaluation(){

	let frontDocId 	= jQuery('#_front_of_document_id').val(),
		backDocId 	= jQuery('#_back_of_document_id').val(),
		media 		= jQuery('#_verification_media_id').val(),
		payload 	= {
			'action': 'request_profile_evaluation',
			'frontDoc': frontDocId,
			'backDoc': backDocId,
			'media': media
		};

	// var modal = document.getElementById('exampleModal');
	var myModalEl 	= document.getElementById('exampleModal'),
		modal 		= bootstrap.Modal.getInstance(myModalEl);
	
	var toastSuccess = document.getElementById('toast-success'),
		toast = new bootstrap.Toast(toastSuccess);

	// Call ajax
	if( Object.values(payload).length > 0 ){
		jQuery.ajax({
			type: 'POST',
			url: publicAjax.url,
			data: payload,
			error: function( request, status, error ) {
				console.log(error);
			},
		})
		.done( function(data){
			console.log(data);

			if(data){
				modal.remove();
				toast.show();
			} else {

			}
		});
	}
}

/**
 * Função que submete o resultado da verificação de perfil
 * Esta função é chamada ao clicar em um dos botões 
 * @param {*} document 
 */
function submitVerificationResult(el){

	let result 	= el.attr('data-value'),
		profile = el.attr('data-profile'),
		adminId = el.attr('data-admin'),
		payload = {
			'action': 'save_profile_evaluation_result',
			'nonce': publicAjax.nonce,
			'adminId': adminId,
			'profile': profile,
			'result': result,
		};

	jQuery.ajax({
		type: 'POST',
		url: publicAjax.url,
		data: payload,
		error: function( request, status, error ) {
			console.log(error);
		},
	})
	.done( function(data){
		console.log(data);
		location.reload();
	});
}

/**
 * Inicialização de funções e monitoramento de elementos
 * */
jQuery(document).ready( function(){
	// Manipulando o upload da foto de perfil
	jQuery('#_select_profile_photo').change( function(){
		uploadImage(jQuery(this));
	});
	
	// Manipulando o upload da foto de capa
	jQuery('#_select_profile_cover').change( function(){
		uploadImage(jQuery(this));
	});
	
	// Manipulando o upload da frente do documento
	jQuery('#_front_of_document').change( function(){
		console.log('Upload da foto');
		uploadImage(jQuery(this));
	});

	// Manipulando o upload do verso do documento
	jQuery('#_back_of_document').change( function(){
		console.log('Upload da foto');
		uploadImage(jQuery(this));
	});
	
	// Manipulando o upload do verso do documento
	jQuery('#_verification_media').change( function(){
		console.log('Upload da vídeo');
		uploadMedia(jQuery(this));
	});

	// Manipulando o envio do resultado da avaliação de perfil
	jQuery('#validationActionBar .btn').click( function(){
		console.log('Enviar resultado');
		submitVerificationResult(jQuery(this));
	});

	// Manipulando o upload da galeria
	jQuery('#_profile_gallery_upload').change( function(){
		uploadGallery(jQuery(this));
	});

	// Manipulando selectbox p/ criar a lista de exclusao de itens na galeria
	jQuery('#galleryList input[type="checkbox"]').click( function(){
		collectItemsToRemoveFromGallery(jQuery(this));
	});

	// Submetendo a lista p/ exclusão da galeria
	jQuery('#btnSubmitItemsToRemoveFromGallery').click( function(){
		submitItemsToRemoveFromGallery();
	});

	// Submetendo a solicitação de verificação de perfil
	jQuery('#submitVerificationProfile').click( function(){
		requestProfileEvaluation();
	});
	// Inicializando Tooltips
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	});

	// Inicializando .carousel-advertisement
	jQuery('.carousel-advertisement').owlCarousel({
		margin: 16,
		nav: true,
		dots: true,
		lazyLoad: true,
		autoplay: true,
		autoplayHoverPause: true,
		responsive:{
			0:{
				items:1,
				stagePadding: 32
			},
			600:{
				items:3,
			},
			1000:{
				items:4,
			}
		}
	});

	// Inicializando .carousel-gallery (Anúncios diamante)
	jQuery('.carousel-gallery').owlCarousel({
		items: 3,
		margin: 3,
		nav: false,
		dots: false,
		lazyLoad: true,
		autoplay: true,
		autoplayHoverPause: true,
	});
});