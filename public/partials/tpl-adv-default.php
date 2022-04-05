<div class="advCard col-xs-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 col-xxl-3">
    <div class="advCard--bgNone card position-relative p-0">

        <div class="advCard__thumb">
            <div class="advCard__image card-img-top" style="background-image: url('<?php echo $thumbUrl; ?>');"></div>
        </div>
        <!-- /End .adv__thubm -->

        <div class="advCard__body card-body">
            <h6 class="card-title fw-bold"><?php echo $title; ?></h6>
            <p class="advCard__text card-text" style="opacity: .8"><?php echo wp_trim_words( $content, 8, '...' ); ?></p>
        
            <div class="row">
                <span class="col"><i class="bi bi-pin-map-fill"></i> <?php echo $neighborhood; ?></span>
                <span class="col"><i class="bi bi-gender-ambiguous"></i> <?php echo $genre; ?></span>
            </div>
        </div>
        <!-- /End .adv__content -->

        <div class="card-footer p-0">
            <div class="d-grid gap-2">
                <a href="" class="btn advCard__btn p-0 pt-2 pb-2"><?php _e( 'Visitar perfil', 'textdomain' ); ?> <i class="bi bi-link-45deg"></i></a>
            </div>
        </div>
    </div>
    <!-- /End .adv--default -->
</div>