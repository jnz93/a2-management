<div class="container mb-5" style="margin-top: 150px;">
    <div class="row g-4">
        <?php
        if ($query->have_posts()) {
            # Meta Keys para coletar post metas
            $metaKeys = array(
                'id',
                'first_name',
                'last_name',
                '_plan_level',
                '_profile_url',
                '_expiration_date',
                '_profile_whatsapp',
                '_profile_birthday',
                '_profile_height',
                '_profile_weight',
                '_profile_eye_color',
                '_profile_hair_color',
                '_profile_tits_size',
                '_profile_bust_size',
                '_profile_waist_size',
                '_profile_instagram',
                '_profile_tiktok',
                '_profile_onlyfans',
                '_profile_address',
                '_profile_cep',
                '_profile_cache_quickie',
                '_profile_cache_half_an_hour',
                '_profile_cache_hour',
                '_profile_cache_overnight_stay',
                '_profile_cache_promotion',
                '_profile_cache_promotion_activated',
            );

            while ($query->have_posts()) {
                $query->the_post();
                $postId     = get_the_ID();
                $authorId   = get_the_author_ID();
                
                $dataProfile = [];
                foreach( $metaKeys as $key ){
                    $dataProfile[$key] = get_post_meta( $postId, $key, true );
                }
                
                # Definição de dados do anúncio
                $title          = get_the_title( $postId );
                $content        = get_the_content( $postId );
                $thumbUrl       = get_the_post_thumbnail_url( $postId );
                $age            = $this->profileHelper->getAgeById($postId); # Criar classe e método
                $pageProfileId  = $this->profileHelper->getPageIdByAuthor($authorId);
                $gallery        = $this->profileHelper->getGalleryById($pageProfileId);
                $pageProfileUrl = $this->profileHelper->getProfileLinkById($pageProfileId );
                $genre          = $this->profileHelper->getGenreById($pageProfileId);
                $isVerified     = 'yes'; # Coletar verificação do perfil
                $havePlace      = 'yes'; # Adicionar opção na edição do perfil
                
                // Formatando mensagem de contato
                $baseWaApi      = '';
                if( wp_is_mobile() ){
                    $baseWaApi      = 'https://api.whatsapp.com/send?phone=';
                } else {
                    $baseWaApi      = 'https://web.whatsapp.com/send?phone=';
                }
                $countryCode    = '55';
                $waNumber       = $countryCode . str_replace( ['(', ')', '-', ' '], '', $dataProfile['_profile_whatsapp'] );
                $message        = urlencode('Olá, ' . $title . '! Encontrei seu anúncio no www.acompanhantesa2.com. *Podemos conversar?*');
                $contactLink    = $baseWaApi . $waNumber . '&text=' . $message;
                
                # 0=silver; 1=gold; 2=diamond;
                switch($dataProfile['_plan_level']){
                    case 1:
                        require plugin_dir_path( __DIR__ ) . 'partials/cards/adv-card-silver.php';
                        break;
                    case 2:
                        require plugin_dir_path( __DIR__ ) . 'partials/cards/adv-card-gold.php';
                        break;
                    case 3:
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