<script>
    var cities = <?php echo json_encode($terms); ?>;
</script>
<div class="bg-light rounded-3 shadow-lg p-5 mb-5" id="searchComponent">
    <div class="container">
        <div class="row">
            <div class="searchHeader">
                <h2 class="display-5"><?php _e( '<strong class="text-bolder">Acompanhantes</strong> na sua cidade', 'textdomain'); ?></h2>
                <p class="text-muted"><?php _e( 'A vida é mais colorida quando compartilhada A2', 'textdomain' ); ?></p>
            </div>
            <div class="searchBody position-relative">
                <div class="input-group input-group-lg">
                    <input type="text" class="form-control form-control-lg" id="citiesFilter" placeholder="<?php _e('Digite 3 caracteres para encontrar sua Cidade', 'textdomain'); ?>" style="width: 680px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
                    <span class="input-group-text"><i class="bi bi-search-heart-fill"></i></span>
                </div>
            
                <div id="boxCities" class="position-absolute mt-1 overflow-auto" style="width: 100%; max-height: 230px; left:0; padding-left: calc(var(--bs-gutter-x) * .5); padding-right: calc(var(--bs-gutter-x) * .5); z-index: 9;">

                    <div class="list-group d-none" id="citiesList">
                        <?php 
                        if( !empty($terms) ){
                            foreach( $terms as $term ){
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
        </div>
    </div>
</div>
