<?php
/**
 * Template: my-gallery
 * Não está funcionando 
 * @package a2 plugin
 */
?>
<style>
.actionBar{
    margin: 16px auto;
}
</style>
<!-- Remover styles -->

<h4 class=""><i class="bi bi-images me-1"></i><?php echo __('Galeria', 'textdomain'); ?></h4>
<p class="text-muted"><?php echo __('Fotos e vídeos da sua galeria são compartilhados publicamente na <a href="'. $profilePageLink .'" target="_blank">sua página de perfil</a>', 'textdomain'); ?></p>

<div class="row">
    <!-- Container #galleryWrapper -->
    <div id="galleryWrapper" class="col-xs-12 col-sm-12 col-md-12 col-lg-9 col-xl-9 col-xxl-9">
        <div class="row">
            <div class="col-12 mb-3">
                <?php if( $totalMidias < 10 ): ?>
                    <p class="text-muted"><?php echo __('Mostrando '. $totalMidias .' foto(s)', 'textdomain') ?></p>
                <?php else:  ?>
                    <p class="text-muted"><?php echo __('Mostrando 10 de '. $totalMidias, 'textdomain') ?></p>
                <?php endif; ?>
            </div>

            <!-- photosWrapper -->
            <ul id="galleryList" class="row col-12 mb-5">
                <?php 
                    $output = '';
                    if( !empty( $galleryList ) ){
                        foreach( $galleryList as $item ){
                            $attachUrl = wp_get_attachment_url( $item );
                            $attachCaption = wp_get_attachment_caption( $item );
                            $output .= '<li id="'. $item .'" data-attachment="'. $item .'" class="galleryItem position-relative col-6 col-sm-6 col-md-4 col-lg-4 p-1 mb-2 rounded">
                                <div class="thumbActions py-2 d-flex position-absolute" style="background: rgba(0, 0, 0, .5); width: 92%; left: 4%; top: 2%;">
                                    <span class="ms-1">
                                        <input class="form-check-input" type="checkbox" id="" data-attachment="'. $item .'">
                                    </span>
                                </div>
                                <div class="thumbnail img-thumbnail">
                                    <img src="'. $attachUrl .'" alt="'. $attachCaption .'" class="">
                                </div>
                                <div class="caption">
                                    <span class="thumb-title">'. $attachCaption .'</span>
                                </div>
                            </li>';
                        }
                    } else {
                        $output .= '<div class="alert alert-warning" role="alert">'. __('Parece que sua galeria está vazia.  <label for="_profile_gallery_upload" class="text-primary text-decoration-underline">Faça o envio de algumas fotos e vídeos.</label>', 'textdomain') .'</div>';
                    }

                    echo $output;
                ?>
            </ul>

            <div class="col-12 p-3 shadow-sm mb-5 bg-light rounded">
                <form action="#">
                    <div class="mb-3">
                        <label for="_profile_gallery_upload" class="form-label text-muted text-center p-3" style="width: 100%;border: 5px dashed lightgrey;">
                            <i class="bi bi-cloud-upload-fill fs-1"></i>
                            <span class="d-block fs-5"><?php _e( 'Clique para enviar Fotos e Vídeos', 'textdomain' ); ?></span>
                        </label>
                        <input class="form-control" type="file" accept="image/png, image/jpeg" id="_profile_gallery_upload" name="_profile_gallery_upload[]" multiple>
                        <input type="hidden" name="_profile_gallery" id="_profile_gallery" value="<?php echo strlen(get_user_meta( $userId, '_profile_gallery', true )) != 0 ? get_user_meta( $userId, '_profile_gallery', true ) : '' ?>">
                    </div>
                </form>
            </div>			

            <!-- Exclude action bar -->
            <div id="excludeActionBar" class="d-none justify-content-center align-items-center position-fixed bottom-0 end-0" style="width: 200px;">
                <button type="button" id="btnSubmitItemsToRemoveFromGallery" class="btn btn-primary mb-3 position-relative">
                    Excluir <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary"></span>
                </button>
                <input type="hidden" name="_profile_gallery_remove_list" id="_profile_gallery_remove_list" value="">
            </div>
        </div>
    </div>

    <!-- Cotainer #asideWrapper -->
    <div id="asideWrapper" class="col-xs-12 col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3 d-none">
        <!-- .followersWidget -->
        <div class="followersWidget">
            <div class="">
                <h6 class=""><?php echo __('Seguidores', 'textdomain'); ?> <span class="new badge" data-badge-caption="">4</span></h6> 
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card-panel grey lighten-5 z-depth-1 no-padding">
                        <div class="row valign-wrapper">
                            <div class="col-2">
                                <img src="<?php echo $defaultUserThumb; ?>" alt="" class="circle responsive-img"> <!-- notice the "circle" class -->
                            </div>
                            <div class="col-10">
                                <span class="black-text">Victor Sousa Ferreira</span>
                                <span class="black-text">Homem</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card-panel grey lighten-5 z-depth-1 no-padding">
                        <div class="row valign-wrapper">
                            <div class="col-2">
                                <img src="<?php echo $defaultUserThumb; ?>" alt="" class="circle responsive-img"> <!-- notice the "circle" class -->
                            </div>
                            <div class="col-10">
                                <span class="black-text">Victor Sousa Ferreira</span>
                                <span class="black-text">Homem</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- .adsenseWidget -->
        <div class="adsenseWidget center-align z-depth-1">
            <div class="">
                <h4>BANNER ANÚNCIO</h4>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_before_my_account' );

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_after_my_account' );