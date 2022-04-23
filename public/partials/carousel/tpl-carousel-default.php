<div class="container mb-5">
    <div class="row mb-4">
        <div class="carousel__title col-8" style="color: #fff; opacity: .8">
            <span class="fs-1 text-capitalize"><?php echo $titleCarousel; echo strlen($subtitleCarousel) > 1 ? ',' : '' ; ?></span>
            <span class="fs-6 fw-bold text-uppercase"><?php echo $subtitleCarousel ?></span>
        </div>
        <div class="d-flex justify-content-end col-4">
            <?php if( $totalPosts > 0 ): ?>
                <a href="<?php echo $pageLocationLink; ?>" class="btn btn-outline-danger position-relative">
                    <?php echo __( 'Acompanhantes em', 'textdomain' ) .' <b>'. ucwords($titleCarousel) .'</b>'; ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-dark"><?php echo $totalPosts; ?></span>
                </a>
            <?php endif;?>
        </div>
        <span class="carousel__lineSeparator mt-3" style="background: #fff; height: 1px; width: 100%; display: block; opacity: .1"></span>
    </div>
    <div class="row mt-3 g-4 owl-carousel carousel-advertisement">
        <?php
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $postId = get_the_ID();
                
                # Definição de dados do anúncio
                $title          = get_the_title( $postId );
                $content        = get_the_content( $postId );
                $thumbUrl       = get_the_post_thumbnail_url( $postId );
                $profileLink    = get_post_meta( $postId, '_profile_url', true );
                $isVerified     = 'yes'; # get_post_meta()
                $genre          = get_the_terms( $postId, 'profile_genre');
                $genre          = $genre[0]->name;

                $localTerms     = get_the_terms( $postId, 'profile_localization' ); # Filtrar termos retornados para encontrar bairro, cidade, estado e país
                $localization   = '';
                $country        = '';
                $state          = '';
                $city           = '';
                $neighborhood   = '';
                if( !is_wp_error($localTerms) ){
                    $firstLvl   = null;
                    $secondLvl  = null;
                    $thirdLvl   = null;
                    $lastLvl    = null;
                    foreach( $localTerms as $term ){
                        if( $term->parent == 0 ){
                            $firstLvl   = $term->term_id;
                            $country    = $term->name;
                        } elseif( $term->parent == $firstLvl ){
                            $secondLvl  = $term->term_id;
                            $state      = $term->name;
                        } else {
                            $childTerms = get_term_children( $term->term_id, 'profile_localization' );
                            if( empty($childTerms) ){
                                $neighborhood   = $term->name;
                                $lastLvl        = $term->term_id;
                            } else {
                                $thirdLvl   = $term->term_id;
                                $city       = $term->name;
                            }
                        }
                    }
                }

                switch($showWhat) {
                    case 'second-lvl':
                        $localization = $state;
                        break;
                    case 'third-lvl':
                        $localization = $city;
                        break;
                    case 'last-lvl':
                        $localization = $neighborhood;
                        break;
                    default:
                        $localization = $country;
                        break;
                }

                require plugin_dir_path( __DIR__ ) . 'cards/tpl-card-carousel.php';
            }
        } else {
            $title          = __('Você é acompanhante?');
            $content        = __('Faça seu cadastro agora e seja destaque em <b>' . ucwords($titleCarousel) .'</b>!');
            $thumbUrl       = 'https://acompanhantesa2.com/wp-content/uploads/2022/04/undraw_Social_bio_re_0t9u.png';
            $profileLink    = get_permalink( 365 );

            require plugin_dir_path( __DIR__ ) . 'cards/tpl-card-carousel-default.php';
        }
        // Restore original Post Data
        wp_reset_postdata();
        ?>
    </div>
    <span class="carousel__lineSeparator mt-4" style="background: #fff; height: 1px; width: 50%; display: block; opacity: .1; margin-left: auto; margin-right: auto"></span>
</div>