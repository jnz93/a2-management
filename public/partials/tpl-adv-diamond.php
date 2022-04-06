<div class="advCard col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
    <a href="" class="link-secondary text-decoration-none">
        <div class="advCard--diamond card position-relative p-0">
            <?php if( $isVerified == 'yes'): ?>
                <div class="advCard__tag advCard__tag--verified position-absolute top-0 start-0">
                    <span class=""><i class="bi bi-shield-check"></i> <?php _e( 'perfil verificado', 'textdomain'); ?></span>
                </div>
            <?php endif; ?>

            <div class="advCard__thumb d-flex">
                <div class="advCard__image card-img-top" style="background-image: url('<?php echo $thumbUrl; ?>'); height: 315px;"></div>
                <div class="advCard__image card-img-top" style="background-image: url('<?php echo $thumbUrl; ?>'); height: 315px;"></div>
                <div class="advCard__image card-img-top" style="background-image: url('<?php echo $thumbUrl; ?>'); height: 315px;"></div>
            </div>
            <!-- /End .advCard__thumb -->

            <div class="advCard__textColor--primary card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col-9">
                        <h6 class="card-title fs-3 fw-bold"><?php echo $title; ?></h6>
                        <p class="advCard__text card-text" style="opacity: .8"><?php echo wp_trim_words( $content, 16, '...' ); ?></p>
                        <span class="col-7 fs-3 fw-bold text-center p-0"><?php echo __('R$ ', 'textdomain' ) . $priceForHour; ?><span class="fs-6 fw-regular">/h</span></span>
                    </div>
                    <div class="col-3 ps-0 d-flex flex-column align-items-start">
                        <?php if( $havePlace == 'yes' ) : ?>
                            <span class="p-0 mt-1 mb-1 text-capitalize" data-bs-toggle="tooltip" data-bs-placement="left" title="<?php _e( 'Com local', 'textdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-house-heart-fill"></i> <?php _e( 'com local', 'textdomain' );?></span>
                        <?php endif; ?>
                        <span class="p-0 mt-1 mb-1 text-capitalize" data-bs-toggle="tooltip" data-bs-placement="left" title="<?php _e( 'gênero', 'texdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-gender-trans"></i> <?php _e( 'mulher', 'textdomain' );?></span>
                        <span class="p-0 mt-1 mb-1 text-capitalize" data-bs-toggle="tooltip" data-bs-placement="left" title="<?php _e( 'idade', 'texdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-calendar2-week"></i> <?php echo '22 anos'; ?></span>
                        <span class="p-0 mt-1 mb-1 text-capitalize" data-bs-toggle="tooltip" data-bs-placement="left" title="<?php _e( 'endereço', 'texdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-geo-alt"></i> <?php echo wp_trim_words( 'estrada do jatobá, 982', 2, '...' ); ?></span>
                    
                    </div>
                </div>
            </div>
            <!-- /End .advCard__body -->
        </div>
    </a>
    <div class="card-footer p-0 border-0">
        <div class="d-grid gap-2">
            <a href="" class="btn advCard__btn advCard__btn--whatsapp"><?php _e( 'converse comigo', 'textdomain' ); ?> <i class="bi bi-whatsapp"></i></a>
        </div>
    </div>
</div>
<!-- /End .advCard -->
