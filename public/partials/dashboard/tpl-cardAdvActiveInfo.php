<div class="col-xs-12 col-md-6 col-lg-6 mb-6">
    <span class="mb-1 d-block text-capitalize text-muted"><?php _e( 'Anúncio <b>'. $advType . '</b>', 'textdomain'); ?></span>

    <div class="cardDashboard card border border-0 shadow-sm pt-2 pb-2">
        <div class="d-flex align-items-center">
            <div class="col-3">
                <div class="d-flex justify-content-center align-items-center">
                    <?php echo $advIcon; ?>
                </div>
            </div>
    
            <div class="col-7 ps-2">
                <span class="text-muted fw-light"><?php _e( 'Vinculado até:', 'textdomain' ); ?></span>
                <h5 class="mt-1 mb-0 fw-bolder"><?php echo $date ?></h5>
                <span class="text-muted fw-normal"><?php _e( 'às ' . $hour, 'textdomain'); ?></span>
            </div>

            <div class="col-2">
                <a href="#" class="cardDashboard__roundBtn d-flex align-items-center justify-content-center" id=""><i class="bi bi-clipboard2-data"></i></a>
            </div>
        </div>
    </div>
</div>