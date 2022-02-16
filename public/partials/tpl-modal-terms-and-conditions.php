<?php

/**
 * Fornece o modal de termos e condições de uso do site.
 *
 * @link       unitycode.tech
 * @since      1.0.0
 *
 * @package    A2
 * @subpackage A2/public/partials
 */
?>
<!-- Modal -->
<div class="modal fade" id="modalTermsAndConditions" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalTermsAndConditions" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center">
                <h5 class="modal-title text-uppercase fw-bolder" id="modalTermsAndConditionsLabel"><?php _e( 'Conteúdo Adulto', 'texdomain' ); ?></h5>
            </div>
            <div class="modal-body text-center">
                <div class="">
                    <p class=""><?php _e( 'Entendo que o site <b>Acompanhantes A2</b> apresenta <b>conteúdo explícito</b> destinado a <b>adultos</b>.', 'textdomain' );?></p>
                    <a href="" class=""><?php _e( 'Termos de uso', 'textdomain' ) ?></a>
                </div>
                <div class="mt-3">
                    <h6 class="text-uppercase"><?php _e( 'Aviso de cookies', 'textdomain' ) ?></h6>
                    <p class=""><?php _e( 'Nós usamos cookies e outras tecnologias semelhantes para melhorar a sua experiência em nosso site.', 'textdomain' );?></p>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center flex-column">
                <div class="form-check form-switch d-flex justify-content-center">
                    <input class="form-check-input me-2" type="checkbox" role="switch" id="comeOfAgeConfirmation">
                    <label class="form-check-label" for="comeOfAgeConfirmation"><?php _e( 'Sou maior de 18 anos.') ?></label>
                </div>
                <button type="button" class="btn btn-primary" onclick="acceptTermsAndConditions()"><?php _e( 'Aceitar', 'textdomain' ); ?></button>
            </div>
        </div>
    </div>
</div>