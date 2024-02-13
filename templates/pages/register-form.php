<?php 
/**
 * Template: Formulário de cadastro
 */
?>
<div class="row m-0 p-0 vh-100">
    <div class="col-11 col-md-6 col-lg-6 d-flex align-content-center register__formContainer position-relative">
        <div class="row register__form">
            <div class="col-11 col-md-8 col-lg-8 m-auto">
                <div class="register__header">
                    <h1 class="text-center fw-bolder"><?php _e( 'Cadastre-se: Faça parte da nossa comunidade!', 'textdomain' ); ?></h1>
                    <p class="text-center"><?php _e( 'Complete o formulário abaixo para criar sua conta de forma GRATUITA.', 'textdomain' ); ?></p>
                </div>
                <div class="">
                    <form id="registerForm" class="mt-5 register__form needs-validation" action="cadastrar-acompanhante" method="post" autocomplete="true" novalidate>
                        <div class="row text-center">
                            <div class="col-12 mb-3">
                                <label for="sl_escort_full_name" class="form-label d-block"><?php _e('Seu Nome Completo', 'textdomain'); ?></label>
                                <input id="sl_escort_full_name" name="sl_escort_full_name" class="form-control form-control-lg text-center" type="text" required aria-describedby="invalidFeedbackName">
                                <div id="invalidFeedbackName" class="invalid-feedback"><?php _e('Por favor, corrija o campo <b>nome</b> para continuar.', 'textdomain'); ?></div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="sl_escort_email" class="form-label d-block"><?php _e('Seu principal E-mail', 'textdomain'); ?></label>
                                <input id="sl_escort_email" name="sl_escort_email" class="form-control form-control-lg text-center" type="email" required aria-describedby="invalidFeedbackEmail">
                                <div id="invalidFeedbackEmail" class="invalid-feedback"><?php _e('Por favor, insira um endereço de e-mail válido.', 'textdomain'); ?></div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="sl_escort_password" class="form-label d-block"><?php _e('Adicione uma Senha', 'textdomain'); ?></label>
                                <input id="sl_escort_password" name="sl_escort_password" class="form-control form-control-lg text-center" type="password" required aria-describedby="invalidFeedbackPassword">
                                <div id="invalidFeedbackPassword" class="invalid-feedback"><?php _e('Por favor, insira sua senha.', 'textdomain'); ?></div>
                            </div>
                        </div>
    
                        <div class="row mb-3">
                            <div class="form-check form-switch ms-2 col-12">
                                <input class="form-check-input" type="checkbox" role="switch" name="sl_escort_terms_agree" id="sl_escort_terms_agree" required aria-describedby="invalidFeedbackTerms">
                                <label class="form-check-label" for="sl_escort_terms_agree"><?php _e('Concordo com os <b><a href="#">termos de uso e políticas de privacidade</a></b> da plataforma A2 Acompanhantes.', 'textdomain') ?></label>
                                <div id="invalidFeedbackTerms" class="invalid-feedback"><?php _e('Você deve concordar com os termos de uso e políticas de privacidade.', 'textdomain') ?></div>
                            </div>
    
                            <div class="form-check form-switch ms-2 col-12">
                                <input class="form-check-input" type="checkbox" role="switch" name="sl_escort_age_confirmation" id="sl_escort_age_confirmation" required aria-describedby="invalidFeedbackAgeConfirmation">
                                <label class="form-check-label" for="sl_escort_age_confirmation"><?php _e('Declaro que <b>sou maior de 18 anos</b>.', 'textdomain') ?></label>
                                <div id="invalidFeedbackAgeConfirmation" class="invalid-feedback"><?php _e('Você deve confirmar que é maior de 18 anos.', 'textdomain'); ?></div>
                            </div>
                        </div>
    
                        <input id="sl_user_type" name="sl_user_type" type="hidden" value="a2_scort">
                        <button class="btn btn-primary btn-lg w-100" type="submit" name="submit"><i class="bi bi-person-plus-fill me-2"></i><?php _e('Cadastrar Agora! (Grátis)', 'textdomain') ?></button>
                    </form>
                </div>
            </div>
        </div>
        <div id="slLoader" class="spinner-border text-secondary position-absolute mb-5 bottom-0 start-50 d-none" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="col-md-6 col-lg-6 register__bgContainer">
        <div class="register__backgroundImage"></div>
        <div class="">
            <p><?php _e('🎉 Bem-vindo à nossa comunidade exclusiva para acompanhantes de alto nível! 🌟 Estamos animados para iniciar esta jornada com você.', 'textdomain'); ?></p>
            <p><?php _e('Ao se cadastrar em nossa plataforma, você desfrutará de diversas vantagens:', 'textdomain'); ?></p>
            <ul>
                <li><?php _e('Cadastro Grátis: Não há custos para se inscrever e criar seu perfil profissional.', 'textdomain'); ?></li>
                <li><?php _e('Painel de Controle Exclusivo: Tenha acesso a um painel de controle intuitivo e completo para gerenciar seu perfil e anúncios.', 'textdomain'); ?></li>
                <li><?php _e('Gerenciamento de Anúncios: Controle total sobre seus anúncios na plataforma, podendo editá-los conforme desejar.', 'textdomain'); ?></li>
                <li><?php _e('Pontos de Carma: Receba 200 pontos de carma ao se cadastrar, o que ajudará a destacar seu perfil entre os concorrentes.', 'textdomain'); ?></li>
                <li><?php _e('Upload de Fotos e Vídeos: Compartilhe suas melhores fotos e vídeos para atrair mais clientes e mostrar seu talento.', 'textdomain'); ?></li>
            </ul>
            <p><?php _e('Estas são apenas algumas das vantagens que oferecemos. Junte-se a nós e descubra como nossa plataforma pode impulsionar sua carreira como acompanhante! 🚀', 'textdomain'); ?></p>
        </div>
    </div>
</div>
<div id="slToastNotification" class="toast position-absolute" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-body text-center">
        <div id="toastMessage"></div>
    </div>
    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
</div>

<div id="slAlertPlaceholder" class="position-fixed z-3"></div>

<style>
    .elementor-location-footer{
        display: none !important;
    }
    .form-disabled {
        pointer-events: none;
        opacity: 0.5;
    }
    #slAlertPlaceholder{
        bottom: 30px;
        width: calc(50% - 30px);
        left: 15px;
    }
</style>

<script>
// Bootstrap validation
(function () {
    'use strict'
    const form  = document.querySelector('#registerForm');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const loader    = jQuery('#slLoader');

        form.classList.add('form-disabled');
        loader.removeClass('d-none');
        var msg         = "",
            redirect    = false,
            typeAlert   = "info",
            wasValidate = true;
        if(form.checkValidity()) {
            const response = await submitEscortForm(form);
            msg = response.msg;
            typeAlert = "success";

            if(!response.success){
                var emailPattern = /\be-mail\b/gi,
                    userPattern = /\busuário\b/gi;

                var resultEmail = response.msg.match(emailPattern),
                    resultName = response.msg.match(userPattern);
                    
                if(resultEmail){
                    jQuery('#sl_escort_email').addClass('is-invalid').focus();
                    typeAlert = 'warning';
                    wasValidate = false;
                }
                
                if(resultName){
                    jQuery('#sl_escort_full_name').addClass('is-invalid').focus();
                    typeAlert = 'warning';
                    wasValidate = false;
                }
            }

            if(response.url){
                redirect = response.url;
            }
        } else {
            msg = 'Preencha o(s) campo(s) destacado(s) e tente novamente.';
            typeAlert = 'danger';
            event.stopPropagation();
        }

        
        setTimeout(() => {
            loader.addClass('d-none');
            if(wasValidate){
                form.classList.add('was-validated');
            } else {
                form.classList.remove('was-validated');
            }
            form.classList.remove('form-disabled');
            showAlert(msg, typeAlert);
            
            if(redirect){                                  
                setTimeout(() => {
                    window.location.href = redirect;
                }, 5000);
            }
        }, 1500);

    }, false)
})();
</script>