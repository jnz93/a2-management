/**
 * Função responsável por chamar o modal de termos e condições
 * 
 */
 function callModalTermsAndConditions(){

	let hasAcceptedTermsAndConditions = checkACookieExists('acceptTermsAndConditions');
	if( hasAcceptedTermsAndConditions ){ return; }

	let termsAndConditionsElement   = document.querySelector('#modalTermsAndConditions'),
		termsAndConditionModalBS    = bootstrap.Modal.getOrCreateInstance(termsAndConditionsElement);

	termsAndConditionModalBS.show();
}

/**
 * Função responsável por desabilitar o modal de termos e condições
 * 
 */
function disableModalTermsAndConditions(){
	var termsAndConditionsElement   = document.querySelector('#modalTermsAndConditions'),
		termsAndConditionModalBS    = bootstrap.Modal.getOrCreateInstance(termsAndConditionsElement);

		termsAndConditionModalBS.hide();
}

/**
 * Função responsável por checar se o usuário confirmou a maior idade
 * 
 */
function isComeOfAge(){
	let isComeOfAgeInput 	= jQuery('#comeOfAgeConfirmation'),
		confirmAge 			= false;

	if( isComeOfAgeInput.prop('checked') === true ){
		confirmAge = true;
	}

	return confirmAge;
}

/**
 * Função que verifica a existência de um cookie a partir do nome
 * que deve ser passado como parâmetro
 * 
 * @param name string 		Nome do cookie procurado
 */
function checkACookieExists( name ) {
	let cookieExists = false;
	if (document.cookie.split(';').some((item) => item.trim().startsWith( name + '=' ))) {
		cookieExists = true;
	}

	return cookieExists;
}
/**
 * Função responsável por registrar o consentimento do usuário com os termos e condições
 * e um cookie e também por desabilitar o modal
 * 
 */
function acceptTermsAndConditions(){
	document.cookie = 'acceptTermsAndConditions=yes; path=/; secure';
	let confirmAge = isComeOfAge();

	if( confirmAge ){
		document.cookie = 'isComeOfAge=yes; path=/; secure';
		disableModalTermsAndConditions();
	}
}


/**
 * instanciando funções quando o DOM estiver pronto
 * 
 */
document.addEventListener('DOMContentLoaded', function() {
	callModalTermsAndConditions();
});