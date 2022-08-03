<div class="advCardInfo col-xs-12 col-md-4 col-lg-4 mb-4">
    <span class="advCardInfo__title mb-2 d-block"><?php echo $a['title']; ?></span>

    <div class="card advCardInfo__container advCardInfo--diamond">
        <div class="advCardInfo__top">
            <div class="advCardInfo__icon mb-2">
                <?php echo $advIcon; ?>
            </div>
            <span class="advCardInfo__text text-uppercase fw-bold" style="color: <?php echo $colorOne; ?>;"><?php _e( $advType, 'textdomain' ); ?></span>
        </div>

        <div class="advCardInfo__bottom">
            <span class="advCardInfo__text" style="color: <?php echo $colorTwo; ?>;"><?php _e( 'Válido até:', 'textdomain' ); ?></span>
            <h4 class="mt-2 mb-2" style="color: <?php echo $colorOne; ?>;"><?php echo $date ?></h4>
            <span class="advCardInfo__text" style="color: <?php echo $colorTwo; ?>;"><?php _e( 'às ' . $hour, 'textdomain'); ?></span>
        </div>
    </div>
</div>