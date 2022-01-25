<?php

/**
 * Este arquivo define a classe profile
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    A2
 * @subpackage A2/includes
 * @since      1.0.0
 * @author     jnz93 <box@unitycode.tech>
 */
class A2_Profile{

    public function __construct()
    {
        /** Action para salvar dados na edição de perfil */
        add_action( 'woocommerce_save_account_details', [ $this, 'saveData' ], 12, 1 );
    }

    /**
	 * Salvar dados customs no perfil do usuário 
	 * 
	 * @param integer $user_id 
	 */
	public function saveData( $user_id ) {

		$metaKeys = array(
			'account_phone_number',
			'account_birthday',
			'account_description',
			'_profile_height',
			'_profile_weight',
			'_profile_eye_color',
			'_profile_hair_color',
			'_profile_tits_size',
			'_profile_bust_size',
			'_profile_waist_size',
			'_profile_ethnicity',
			'_profile_genre',
			'_profile_sign',
			'_profile_he_meets',
			'_profile_services',
			'_profile_place',
			'_profile_instagram',
			'_profile_tiktok',
			'_profile_onlyfans',
			'_profile_country',
			'_profile_state',
			'_profile_city',
			'_profile_district',
			'_profile_address',
			'_profile_zip_code',
			'_profile_cache_quickie',
			'_profile_cache_half_an_hour',
			'_profile_cache_hour',
			'_profile_cache_overnight_stay',
			'_profile_cache_promotion',
			'_profile_cache_promotion_activated',
			'_profile_work_days',
			'_profile_office_hour',
		);
		
		# Coletando Formas de pagamento
		$args = array(
			'taxonomy'		=> 'formas-de-pagamento',
			'hide_empty'	=> false,
			'orderby'		=> 'name',
			'order'			=> 'ASC'
		);
		$formasPagamento = get_terms( $args );
		if( !empty( $formasPagamento ) ){
			foreach ($formasPagamento as $pagamento ){
				$metaKeys[] = '_profile_payment_method_' . $pagamento->term_id;
			}
		}

		# Salvando meta-campos
		$log = array();
		foreach( $metaKeys as $key ){
			if( isset( $_POST[$key] ) ){

				if( in_array($key, ['_profile_he_meets', '_profile_services', '_profile_place', '_profile_work_days']) ){
					update_user_meta( $user_id, $key, $_POST[$key] );
				} else {
					update_user_meta( $user_id, $key, sanitize_text_field( $_POST[$key] ) );
				}

				$log[$key] = $_POST[$key];
			}
		}
	}
}