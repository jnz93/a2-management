<div class="advCard col-xs-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-3">
    <a href="<?php echo $pageProfileUrl; ?>" class="link-secondary text-decoration-none">
        <div class="advCard--gold card position-relative p-0">
            <?php if( $isVerified == 'yes'): ?>
                <div class="advCard__tag advCard__tag--verified position-absolute top-0 start-0">
                    <span class=""><i class="bi bi-shield-check"></i> <?php _e( 'perfil verificado', 'textdomain'); ?></span>
                </div>
            <?php endif; ?>

            <div class="advCard__thumb">
                <div class="advCard__image card-img-top" style="background-image: url('<?php echo $thumbUrl; ?>');"></div>
            </div>
            <!-- /End .advCard__thumb -->

            <div class="advCard__textColor--primary card-body">
                <h6 class="card-title fw-bold"><?php echo $title; ?></h6>
                <p class="advCard__text card-text" style="opacity: .8"><?php echo wp_trim_words( $content, 8, '...' ); ?></p>
            
                <div class="row d-flex justify-content-center">
                    <div class="col-5 d-flex">
                        <?php if( $havePlace == 'yes' ) : ?>
                            <span class="p-0" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php _e( 'Com local', 'textdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-house-heart-fill"></i></span>
                        <?php else : ?>
                            <span class="p-0" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php _e( $genre, 'texdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-gender-trans"></i></span>
                        <?php endif; ?>
                    </div>
                    <span class="col-7 fs-4 fw-bold text-center p-0"><?php echo __('R$ ', 'textdomain' ) . $dataProfile['_profile_cache_hour']; ?><span class="fs-6 fw-regular">/h</span></span>
                </div>
            </div>
            <!-- /End .advCard__body -->
        </div>
    </a>
    <div class="card-footer p-0 border-0">
        <div class="d-grid gap-2">
            <a href="<?php echo $contactLink; ?>" target="_blank" rel="nofollow" class="btn advCard__btn advCard__btn--whatsapp"><?php _e( 'converse comigo', 'textdomain' ); ?> <i class="bi bi-whatsapp"></i></a>
        </div>
    </div>
</div>
<!-- /End .advCard -->
