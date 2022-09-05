<script>
jQuery( function() {
    // Inicializando o elemento #slide-cache-range
    jQuery( "#slide-cache-range" ).slider({
        range: true,
        min: 50,
        max: 1500,
        step: 25,
        values: [ 50, 300 ],
        slide: function( event, ui ) {
            jQuery("#cache-range").text( "R$" + ui.values[ 0 ] + " - R$" + ui.values[ 1 ] );
            jQuery("#cacheMin").val(ui.values[0]);
            jQuery("#cacheMax").val(ui.values[1]);
        }
    });
    jQuery( "#cache-range" ).text( "R$" + jQuery( "#slide-cache-range" ).slider( "values", 0 ) + " - R$" + jQuery( "#slide-cache-range" ).slider( "values", 1 ) );
    jQuery("#cacheMin").val(jQuery( "#slide-cache-range" ).slider( "values", 0 ));
    jQuery("#cacheMax").val(jQuery( "#slide-cache-range" ).slider( "values", 1 ));

    // Inicializando o elemento #slide-age-range
    jQuery( "#slide-age-range" ).slider({
        range: true,
        min: 18,
        max: 60,
        step: 1,
        values: [ 50, 300 ],
        slide: function( event, ui ) {
            jQuery("#age-range").text( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
            jQuery("#ageMin").val(ui.values[0]);
            jQuery("#ageMax").val(ui.values[1]);
        }
    });
    jQuery( "#age-range" ).text( jQuery( "#slide-age-range" ).slider( "values", 0 ) + " - " + jQuery( "#slide-age-range" ).slider( "values", 1 ) );
    jQuery("#ageMin").val(jQuery( "#slide-age-range" ).slider( "values", 0 ));
    jQuery("#ageMax").val(jQuery( "#slide-age-range" ).slider( "values", 1 ));

});
var cities = <?php echo json_encode($cities); ?>;
</script>

<div class="filterComponent__container text-white-50 d-flex flex-column justify-content-center">
    
    <div class="searchBody position-relative mb-4">
        <div class="input-group input-group-lg">
            <input type="text" class="searchInput form-control form-control-lg" id="citiesFilter" placeholder="<?php _e('Digite 3 caracteres para encontrar sua Cidade', 'textdomain'); ?>" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
            <span class="input-group-text"><i class="bi bi-search-heart-fill"></i></span>
        </div>

        <div id="boxCities" class="position-absolute mt-1 overflow-auto" style="width: 100%; max-height: 230px; left:0; padding-left: calc(var(--bs-gutter-x) * .5); padding-right: calc(var(--bs-gutter-x) * .5); z-index: 9;">

            <div class="list-group d-none" id="citiesList">
                <?php 
                if( !empty($cities) ){
                    foreach( $cities as $term ){
                        $termSlug = str_replace(' - ', ' ', $term);
                        $termSlug = str_replace(' ', '-', $termSlug);
                        $termSlug = str_replace( ['ã', 'à', 'á', 'â', 'é', 'è', 'ê', 'í', 'ì', 'õ', 'ó', 'ò'], ['a', 'a', 'a', 'a', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'o'], $termSlug );

                        $link = home_url() . '/acompanhantes-em-' . strtolower( $termSlug );
                        echo '<a href="'. $link .'" data-name="" class="list-group-item list-group-item-action">'. $term .'</a>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <!-- /end .searchBody -->

    <div id="genderFilter" class="row mb-4">
        <?php 
        if( !empty($terms['profile_genre']) ):
            foreach( $terms['profile_genre'] as $term ):
                ?>
                <div class="form-check form-switch me-3 mb-3-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>">
                    <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                </div>
                <?php
            endforeach;
        endif;
        ?>
    </div>
    <!-- /end gender-filter -->

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#advancedFilter"><?php _e('Filtros Avançados', 'textdomain'); ?></button>
</div>


<div class="modal fade" id="advancedFilter" tabindex="-1" aria-labelledby="advancedFilterLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel"><?php _e('Filtro Avançado', 'textdomain'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo esc_url(get_permalink( get_the_ID() )); ?>" method="GET" id="" class="">
                <div class="container">
                    <div id="searchFilter" class="mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Buscar', 'textdomain' ); ?></p>
                        <input type="text" name="s" id="" class="form-control form-control-lg" placeholder="<?php _e( 'Nome, descrição ou algum detalhe...', 'textdomain' ); ?>"  <?php echo ( strlen($_GET['s']) > 0 ? 'value="'. $_GET['s'] .'"' : '' ); ?>>
                    </div>

                    <div id="priceFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Cachê/h', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap mb-3">
                            <label for="cache-range" class="mr-2 d-none"><?php _e( 'Valor', 'textdomain'); ?></label>
                            <span id="cache-range" class=""></span>
                            <input type="hidden" name="_cache_min" id="cacheMin">
                            <input type="hidden" name="_cache_max" id="cacheMax">
                        </div>
                        <div class="col-12 mb-3">
                            <div id="slide-cache-range"></div>
                        </div>
                    </div>
                    <!-- /end #priceFilter -->

                    <div id="ageFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Idade', 'textdomain' ); ?></p>
                        
                        <div class="col-12 d-flex flex-wrap mb-3">
                            <label for="age-range" class="mr-2"><span id="age-range" class=""></span> <?php _e( 'Anos', 'textdomain'); ?></label>
                            <input type="hidden" name="_age_min" id="ageMin">
                            <input type="hidden" name="_age_max" id="ageMax">
                        </div>
                        <div class="col-12 mb-3">
                            <div id="slide-age-range"></div>
                        </div>
                    </div>
                    <!-- /end #priceFilter -->

                    <div id="ethnicityFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Etnia', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap">
                            <?php 
                            if( !empty($terms['profile_ethnicity']) ):
                                foreach( $terms['profile_ethnicity'] as $term ):
                                    ?>
                                    <div class="form-check form-switch me-3 mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="etnias[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['etnias']) && in_array($term->slug, $_GET['etnias']) ? 'checked="checked"' : '' ); ?>>
                                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- /end #ethnicityFilter -->

                    <div id="signFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Signo', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap">
                            <?php 
                            if( !empty($terms['profile_sign']) ):
                                foreach( $terms['profile_sign'] as $term ):
                                    ?>
                                    <div class="form-check form-switch me-3 mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="signos[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['signos']) && in_array($term->slug, $_GET['signos']) ? 'checked="checked"' : '' ); ?>>
                                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- /end #signFilter -->

                    <div id="specialtiesFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Especialidades', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap">
                            <?php 
                            if( !empty($terms['profile_specialties']) ):
                                foreach( $terms['profile_specialties'] as $term ):
                                    ?>
                                    <div class="form-check form-switch me-3 mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="especialidades[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['especialidades']) && in_array($term->slug, $_GET['especialidades']) ? 'checked="checked"' : '' ); ?>>
                                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- /end #specialtiesFilter -->

                    <div id="servicesFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Serviços', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap">
                            <?php 
                            if( !empty($terms['profile_services']) ):
                                foreach( $terms['profile_services'] as $term ):
                                    ?>
                                    <div class="form-check form-switch me-3 mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="servicos[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['servicos']) && in_array($term->slug, $_GET['servicos']) ? 'checked="checked"' : '' ); ?>>
                                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- /end #servicesFilter -->
                    
                    <div id="placesFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Local de atentimento', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap">
                            <?php 
                            if( !empty($terms['profile_place_of_service']) ):
                                foreach( $terms['profile_place_of_service'] as $term ):
                                    ?>
                                    <div class="form-check form-switch me-3 mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="locais[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['locais']) && in_array($term->slug, $_GET['locais']) ? 'checked="checked"' : '' ); ?>>
                                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- /end #placesFilter -->

                    <div id="languagesFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Idioma', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap">
                            <?php 
                            if( !empty($terms['profile_languages']) ):
                                foreach( $terms['profile_languages'] as $term ):
                                    ?>
                                    <div class="form-check form-switch me-3 mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="idiomas[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['idiomas']) && in_array($term->slug, $_GET['idiomas']) ? 'checked="checked"' : '' ); ?>>
                                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- /end #languagesFilter -->

                    <div id="daysFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Dias de trabalho', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap">
                            <?php 
                            if( !empty($terms['profile_work_days']) ):
                                foreach( $terms['profile_work_days'] as $term ):
                                    ?>
                                    <div class="form-check form-switch me-3 mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="dias[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['dias']) && in_array($term->slug, $_GET['dias']) ? 'checked="checked"' : '' ); ?>>
                                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- /end #daysFilter -->

                    <div id="paymentFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Métodos de pagamento', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap">
                            <?php 
                            if( !empty($terms['profile_payment_methods']) ):
                                foreach( $terms['profile_payment_methods'] as $term ):
                                    ?>
                                    <div class="form-check form-switch me-3 mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="pagar-com[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['pagar-com']) && in_array($term->slug, $_GET['pagar-com']) ? 'checked="checked"' : '' ); ?>>
                                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- /end #paymentFilter -->

                    <div id="preferencesFilter" class="row mt-3 mb-3 border-bottom">
                        <p class="fw-bolder"><?php _e( 'Preferências', 'textdomain' ); ?></p>
                        <div class="col-12 d-flex flex-wrap">
                            <?php 
                            if( !empty($terms['profile_preference']) ):
                                foreach( $terms['profile_preference'] as $term ):
                                    ?>
                                    <div class="form-check form-switch me-3 mb-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="preferencias[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['preferencias']) && in_array($term->slug, $_GET['preferencias']) ? 'checked="checked"' : '' ); ?>>
                                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                                    </div>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- /end #preferencesFilter -->
                </div>
                </form>
                <!-- /end form -->
            </div>
            <div class="modal-footer">
                <button type="button" id="" class="btn btn-secondary"><?php _e( 'Limpar Filtro', 'textdomain' ); ?></button>
                <button type="submit" id="" class="btn btn-primary"><?php _e( 'Aplicar Filtro', 'textdomain' ); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- /end #advancedFilter -->