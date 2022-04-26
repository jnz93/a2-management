<div class="container mb-5" style="margin-top: 150px;">
    <div class="row g-4">
        <?php
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $postId     = get_the_ID();
                $authorId   = get_the_author_ID();
                $profileId  = get_user_meta( $authorId, '_profile_page_id', true );
                
                # Definição de dados do anúncio
                $title          = get_the_title( $postId );
                $content        = get_the_content( $postId );
                $thumbUrl       = get_the_post_thumbnail_url( $postId );
                // $gallery        = get_post_meta( $postId, '' ); # get_post_meta()
                // $isVerified     = get_post_meta( $postId, '', true ); # get_post_meta()
                // $priceForHour   = get_post_meta( $postId, '', true ); # get_post_meta()
                // $havePlace      = get_post_meta( $postId, '', true ); # get_post_meta() - Adicionar configuração na edição do perfil
                // $location       = get_the_terms( $postId, '' );
                // $genre          = get_the_terms( $postId, '' );
                // $age            = $this->helper->getAge( $postId ); # Criar classe e método 
                $planLevel      = get_post_meta( $postId, '_plan_level', true );
                $contactMessage = 'Olá, ' . $title . '! Encontrei seu anúncio no A2 Acompanhantes. Gostaria de contratar seus serviços.'; # get_post_meta();

                # 0=silver; 1=gold; 2=diamond;
                switch($planLevel){
                    case 0:
                        require plugin_dir_path( __DIR__ ) . 'partials/cards/adv-card-silver.php';
                        break;
                    case 1:
                        require plugin_dir_path( __DIR__ ) . 'partials/cards/adv-card-gold.php';
                        break;
                    case 2:
                        require plugin_dir_path( __DIR__ ) . 'partials/cards/adv-card-diamond.php';
                        break;
                    default:
                        require plugin_dir_path( __DIR__ ) . 'partials/cards/adv-card-default.php';
                        break;
                }
            }
        } else {
            // no posts found
        }

        // Restore original Post Data
        wp_reset_postdata();
        ?>
    </div>
</div>