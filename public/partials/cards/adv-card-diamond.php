<div class="advCard col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 col-xxl-6">
    <div class="advCard--diamond card position-relative p-0">
        <?php do_action( 'profileCheckmark', $authorId ); ?>

        <div class="advCard__thumb d-flex">
            <?php
            if( empty($gallery) ){
                echo '<div class="advCard__image" style="background-image: url('. $thumbUrl .'); height: 315px;"></div>';
            } else {
                echo '<div class="owl-carousel carousel-gallery">';
                    foreach( $gallery as $img ){
                        echo '<div class="advCard__image" style="background-image: url('. $img .'); height: 315px;"></div>';
                    }
                echo '</div>';
            }
            ?>
        </div>
        <!-- /End .advCard__thumb -->

        <div class="advCard__textColor--primary card-body">
            <a href="<?php echo $pageProfileUrl; ?>" class="text-decoration-none">
                <div class="row d-flex justify-content-center">
                    <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 col-xl-9">
                        <h6 class="card-title fs-3 fw-bold"><?php echo $title; ?></h6>
                        <p class="advCard__text card-text" style="opacity: .8"><?php echo wp_trim_words( $content, 16, '...' ); ?></p>
                        <span class="col-7 fs-3 fw-bold text-center p-0"><?php echo __('R$ ', 'textdomain' ) . $dataProfile['_profile_cache_hour']; ?><span class="fs-6 fw-regular">/h</span></span>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 ps-0 d-flex flex-column align-items-start">
                        <?php if( $havePlace == 'yes' ) : ?>
                            <span class="p-0 mt-1 mb-1 text-capitalize" data-bs-toggle="tooltip" data-bs-placement="left" title="<?php _e( 'Com local', 'textdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-house-heart-fill"></i> <?php _e( 'com local', 'textdomain' );?></span>
                        <?php endif; ?>
                        <span class="p-0 mt-1 mb-1 text-capitalize" data-bs-toggle="tooltip" data-bs-placement="left" title="<?php _e( 'gênero', 'texdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-gender-trans"></i> <?php _e( $genre, 'textdomain' );?></span>
                        <span class="p-0 mt-1 mb-1 text-capitalize" data-bs-toggle="tooltip" data-bs-placement="left" title="<?php _e( 'idade', 'texdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-calendar2-week"></i> <?php echo $age .' '. __('anos', 'textdomain'); ?></span>
                        <span class="p-0 mt-1 mb-1 text-capitalize" data-bs-toggle="tooltip" data-bs-placement="left" title="<?php _e( 'endereço', 'texdomain' ); ?>"><i class="advCard__icon advCard__icon--gold bi bi-geo-alt"></i> <?php echo wp_trim_words( $dataProfile['_profile_address'], 2, '...' ); ?></span>
                    
                    </div>
                </div>
            </a>
        </div>
        <!-- /End .advCard__body -->
    </div>
    
    <div class="card-footer p-0 border-0">
        <div class="d-grid gap-2">
            <a href="<?php echo $contactLink; ?>" target="_blank" rel="nofollow" class="btn advCard__btn advCard__btn--whatsapp"><?php _e( 'converse comigo', 'textdomain' ); ?> <i class="bi bi-whatsapp"></i></a>
        </div>
    </div>
</div>
<!-- /End .advCard -->
