<div class="col-xs-12 col-md-4 col-lg-4 mb-2">
    <div class="cardDashboard card border border-0 shadow-sm pt-2 pb-2">
        <div class="d-flex align-items-center">
            <div class="col-3">
                <div class="d-flex justify-content-end align-items-center">
                    <?php echo $icon; ?>
                </div>
            </div>
    
            <div class="col-9 ps-2">
                <span class="text-muted fw-light"><?php _e( $title, 'textdomain' ); ?></span>
                <div class="d-flex align-items-end">
                    <h5 class="mt-1 mb-0 fw-bolder"><?php echo $value ? $value : '0'; ?></h5>
                    <span class="text-muted fw-normal"><?php _e( $postFix, 'textdomain'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php echo $name == 'views' ? '<a href="'. $pageLink .'" target="_blank" class="text-muted fw-normal fst-italic">'. __('Visitar sua página de perfil', 'textdomain') .' <i class="bi bi-link-45deg"></i></a>' : '';?>
</div>