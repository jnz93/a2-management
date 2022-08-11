<div class="">

    <div id="<?php echo $pID; ?>" class="card cardProduct mb-1 text-center">
        <?php 
        if( $title == 'Anúncio Diamante' ){
            echo '<span class="cardProduct__label d-flex justify-content-center align-items-center text-light fw-bolder" style="background-color: '. $primaryColor .'">'. __('popular', 'textdomain') .'</span>';
        }
        ?>
        <div class="cardProduct__header">
            <div class="cardProduct__icon mb-2">
                <img src="<?php echo $thumbnailUrl ?>" alt="" class="cardProduct__thumb">
            </div>
            <span class="cardProduct__text text-uppercase fw-bold" style="color: <?php echo $primaryColor; ?>;"><?php _e( $title, 'textdomain' ); ?></span>
        </div>
        <div class="cardProduct__body">
            <a href="" id="">
                <span style="color: <?php echo $secondaryColor ?>"><?php _e( 'Conheça as </br><b>Vantagens</b>', 'textdomain'  ) ?></span>
            </a>
            <div class="cardProduct__description d-none">
                <?php _e( $content, 'textdomain' ); ?>
            </div>
        </div>
        <div class="cardProduct__footer">
            <label for="<?php echo '_period_' . $pID ?>" class="mb-1"><?php _e( 'Selecione o período', 'textdomain' ); ?></label>
            <select class="form-select cardProduct__selectPeriod" name="<?php echo '_period_' . $pID ?>" id="<?php echo '_period_' . $pID ?>">
                <?php 
                    if( !is_wp_error($durations) ){
                        foreach( $durations as $duration ){
                            $duration   = trim($duration);
                            $slug       = str_replace( ' ', '-', $duration );

                            echo '<option value="'. $slug .'">'. __( $duration, 'textdomain' ) .'</option>';
                        }
                    }
                ?>
            </select>
            <div class="cardProduct__prices">
                <?php
                if( !is_wp_error($prices) ){
                    foreach( $prices as $key => $value ){
                        echo '<h3 class="mt-3 mb-3 fw-bolder '. ($key != "15-dias" ? "d-none" : "active-price") .'" style="color: '. $primaryColor .'" data-var-period="'. $key .'" data-var-id="'. $value['id'] .'">'. __("R$" . $value['price'], "textdomain") .'</h3>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <button class="btn cardProduct__buyButton d-flex justify-content-center align-items-center text-light fw-bolder" style="background-color: <?php echo $primaryColor; ?>" onclick=""><i class="bi bi-bag-heart-fill me-1"></i><?php _e( 'Comprar', 'textdomain' ); ?></button>
</div>