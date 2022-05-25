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
</script>
<?php
// echo '<pre style="color: #fff;">';
// print_r($_GET);
// echo '</pre>';
?>
<div class="filterComponent__container text-white-50">
    <form action="<?php echo esc_url(get_permalink( get_the_ID() )); ?>" method="GET" id="" class="">
        <div id="genderFilter" class="row">
            <?php 
            if( !empty($terms['profile_genre']) ):
                foreach( $terms['profile_genre'] as $term ):
                    ?>
                    <div class="form-check form-switch col-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>">
                        <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
        <!-- /end gender-filter -->
        
        <div id="advancedFilters" class="">
            
            <div id="searchFilter" class="row mt-4 pb-4 border-bottom d-none">
                <h5 class="fw-bolder"><?php _e( 'Buscar', 'textdomain' ); ?></h5>
                
                <input type="text" name="s" id="" class="form-control form-control-lg" placeholder="<?php _e( 'Nome, descrição ou algum detalhe...', 'textdomain' ); ?>"  <?php echo ( strlen($_GET['s']) > 0 ? 'value="'. $_GET['s'] .'"' : '' ); ?>>
            </div>

            <div id="priceFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Cachê/h', 'textdomain' ); ?></h5>
                
                <div class="d-flex mb-2">
                    <label for="cache-range" class="mr-2 d-none"><?php _e( 'Valor', 'textdomain'); ?></label>
                    <span id="cache-range" class=""></span>
                    <input type="hidden" name="_cache_min" id="cacheMin">
                    <input type="hidden" name="_cache_max" id="cacheMax">
                </div>
                <div id="slide-cache-range"></div>

            </div>
            <!-- /end #priceFilter -->

            <div id="ageFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Idade', 'textdomain' ); ?></h5>
                
                <div class="d-flex mb-2">
                    <label for="age-range" class="mr-2"><span id="age-range" class=""></span> <?php _e( 'Anos', 'textdomain'); ?></label>
                    <input type="hidden" name="_age_min" id="ageMin">
                    <input type="hidden" name="_age_max" id="ageMax">
                </div>
                <div id="slide-age-range"></div>

            </div>
            <!-- /end #priceFilter -->

            <div id="ethnicityFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Etnia', 'textdomain' ); ?></h5>
                <?php 
                if( !empty($terms['profile_ethnicity']) ):
                    foreach( $terms['profile_ethnicity'] as $term ):
                        ?>
                        <div class="form-check form-switch col">
                            <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="etnias[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['etnias']) && in_array($term->slug, $_GET['etnias']) ? 'checked="checked"' : '' ); ?>>
                            <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <!-- /end #ethnicityFilter -->

            <div id="signFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Signo', 'textdomain' ); ?></h5>
                <?php 
                if( !empty($terms['profile_sign']) ):
                    foreach( $terms['profile_sign'] as $term ):
                        ?>
                        <div class="form-check form-switch col">
                            <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="signos[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['signos']) && in_array($term->slug, $_GET['signos']) ? 'checked="checked"' : '' ); ?>>
                            <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <!-- /end #signFilter -->

            <div id="specialtiesFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Especialidades', 'textdomain' ); ?></h5>
                <?php 
                if( !empty($terms['profile_specialties']) ):
                    foreach( $terms['profile_specialties'] as $term ):
                        ?>
                        <div class="form-check form-switch col">
                            <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="especialidades[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['especialidades']) && in_array($term->slug, $_GET['especialidades']) ? 'checked="checked"' : '' ); ?>>
                            <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <!-- /end #specialtiesFilter -->

            <div id="servicesFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Serviços', 'textdomain' ); ?></h5>
                <?php 
                if( !empty($terms['profile_services']) ):
                    foreach( $terms['profile_services'] as $term ):
                        ?>
                        <div class="form-check form-switch col">
                            <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="servicos[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['servicos']) && in_array($term->slug, $_GET['servicos']) ? 'checked="checked"' : '' ); ?>>
                            <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <!-- /end #servicesFilter -->
            
            <div id="placesFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Local de atentimento', 'textdomain' ); ?></h5>
                <?php 
                if( !empty($terms['profile_place_of_service']) ):
                    foreach( $terms['profile_place_of_service'] as $term ):
                        ?>
                        <div class="form-check form-switch col">
                            <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="locais[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['locais']) && in_array($term->slug, $_GET['locais']) ? 'checked="checked"' : '' ); ?>>
                            <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <!-- /end #placesFilter -->

            <div id="languagesFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Idioma', 'textdomain' ); ?></h5>
                <?php 
                if( !empty($terms['profile_languages']) ):
                    foreach( $terms['profile_languages'] as $term ):
                        ?>
                        <div class="form-check form-switch col">idiomas
                            <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="idiomas[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['idiomas']) && in_array($term->slug, $_GET['idiomas']) ? 'checked="checked"' : '' ); ?>>
                            <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <!-- /end #languagesFilter -->

            <div id="daysFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Dias de trabalho', 'textdomain' ); ?></h5>
                <?php 
                if( !empty($terms['profile_work_days']) ):
                    foreach( $terms['profile_work_days'] as $term ):
                        ?>
                        <div class="form-check form-switch col">
                            <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="dias[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['dias']) && in_array($term->slug, $_GET['dias']) ? 'checked="checked"' : '' ); ?>>
                            <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <!-- /end #daysFilter -->

            <div id="paymentFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Métodos de pagamento', 'textdomain' ); ?></h5>
                <?php 
                if( !empty($terms['profile_payment_methods']) ):
                    foreach( $terms['profile_payment_methods'] as $term ):
                        ?>
                        <div class="form-check form-switch col">
                            <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="pagar-com[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['pagar-com']) && in_array($term->slug, $_GET['pagar-com']) ? 'checked="checked"' : '' ); ?>>
                            <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <!-- /end #paymentFilter -->

            <div id="preferencesFilter" class="row mt-4 pb-4 border-bottom">
                <h5 class="fw-bolder"><?php _e( 'Preferências', 'textdomain' ); ?></h5>
                <?php 
                if( !empty($terms['profile_preference']) ):
                    foreach( $terms['profile_preference'] as $term ):
                        ?>
                        <div class="form-check form-switch col">
                            <input class="form-check-input" type="checkbox" role="switch" id="<?php echo $term->term_id; ?>" name="preferencias[]" value="<?php echo $term->slug; ?>" <?php echo ( !empty($_GET['preferencias']) && in_array($term->slug, $_GET['preferencias']) ? 'checked="checked"' : '' ); ?>>
                            <label class="form-check-label" for="<?php echo $term->term_id; ?>"><?php _e( $term->name, 'textdomain' ); ?></label>
                        </div>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>
            <!-- /end #preferencesFilter -->
        </div>

        <button type="submit" id="" class="btn btn-primary"><?php _e( 'Filtrar', 'textdomain' ); ?></button>
    </form>
</div>
<?php
echo '<h1 class="text-white">'.get_post_type( get_the_ID() ).'</h1>';
?>