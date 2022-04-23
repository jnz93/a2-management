<div class="advCard">
    <div class="advCard--bgNone advCard__border--default card position-relative p-0">

        <div class="advCard__thumb">
            <div class="advCard__image card-img-top" style="background-image: url('<?php echo $thumbUrl; ?>');"></div>
        </div>
        <!-- /End .adv__thubm -->

        <div class="advCard__textColor--default card-body">
            <h6 class="card-title fw-bold"><?php echo $title; ?></h6>
            <p class="advCard__text card-text" style="opacity: .6"><?php echo wp_trim_words( $content, 8, '...' ); ?></p>
        
            <div class="row" style="opacity: .6">
                <span class="col"><i class="bi bi-pin-map-fill"></i> <?php echo $localization; ?></span>
                <span class="col"><i class="bi bi-gender-ambiguous"></i> <?php echo $genre; ?></span>
            </div>
        </div>
        <!-- /End .adv__content -->

        <div class="card-footer p-0">
            <div class="d-grid gap-2">
                <a href="<?php echo $profileLink; ?>" class="btn advCard__btn advCard__btn--default p-0 pt-2 pb-2"><?php _e( 'Visitar perfil', 'textdomain' ); ?> <i class="bi bi-link-45deg"></i></a>
            </div>
        </div>
    </div>
    <!-- /End .adv--default -->
</div>