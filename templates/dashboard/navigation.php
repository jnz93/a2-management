<?php
/**
 * Template: navigation
 * @package a2 plugin
 */
?>
<style>
    .welcomeComponent::before{
        background-image: url('<?php echo $profileCoverUrl; ?>');
    }
</style>
<div class="welcomeComponent mb-3">
    <!-- Perfil Thumb -->
    <div class="position-absolute bottom-0 w-100">
        <div class="row">
            <div class="col-12">
                <div class="welcomeComponent__profileContainer">
                    <?php if(strlen($profilePhotoUrl) < 1): ?>
                        <div class="welcomeComponent__profileThumb"><i class="bi bi-person-fill d-flex justify-content-center align-items-center welcomeComponent__profileIcon"></i></div>
                    <?php else: ?>
                        <div class="welcomeComponent__profileThumb" style="background-image: url('<?php echo $profilePhotoUrl; ?>');"></div>
                    <?php endif; ?>
                </div>
                <h5 class="text-center text-light mt-2 mb-4"><?php echo __("Olá, <b>$user->display_name!</b>", "textdomain"); ?></h5>
            </div>
                
            <div class="d-md-none d-lg-none d-xl-none d-xxl-none col-12 mt-2">
                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuMobileOffCanvas" aria-controls="menuMobileOffCanvas" style="width: 100%; padding: 8px;"><?php _e('Menu da conta', 'textdomain'); ?></button>		
            </div>
        </div>
    </div>
    <?php do_action( 'profileCheckmark', $userId ); ?>
</div>

<!-- Menu Desktop -->
<nav class="woocommerce-MyAccount-navigation d-none d-md-inline-block d-lg-inline-block">
    <ul class="">
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
            <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<!-- Menu mobile -->
<div class="d-md-none d-lg-none d-xl-none d-xxl-none mb-3">	
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="menuMobileOffCanvas" aria-labelledby="menuMobileOffCanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="menuMobileOffCanvasLabel"><?php _e('Menu da conta', 'textdomain') ?></h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="woocommerce-MyAccount-navigation">
                <ul class="">
                    <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                        <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>