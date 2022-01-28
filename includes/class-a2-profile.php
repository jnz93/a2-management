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

    /**
     * Este método recebe o ID do usuário e executa ações para publicar um perfil publico da acompanhante.
     * 
     * @param int $userId
     */
    public function setupPage( $userId = null )
    {
		$log = array();
        if( is_null( $userId ) || !user_can( $userId, 'a2_scort' ) ){
			$log[] = 'Erro: usuário inválido.';
			return;
		}

		$userData 					= array();
		$user 						= get_userdata( $userId );
		$userData['id']				= $userId;
		$userData['first_name'] 	= $user->first_name;
		$userData['last_name'] 		= $user->last_name;
		$userData['display_name'] 	= $user->display_name;
		$userData['full_name']		= $user->first_name . ' ' . $user->last_name;
		$userData['email']			= $user->user_email;

		# User meta data
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
			'_profile_payment_method_money',
			'_profile_payment_method_card',
			'_profile_payment_method_transfer',
		);

		# Taxonomias
		// profile meta key => taxonomy
		$taxonomies = array(
			'_profile_ethnicity' 	=> 'etnias',
			'_profile_genre'		=> 'generos',
			'_profile_sign'			=> 'signos',
			'_profile_he_meets'		=> 'local-atendimento',
			'_profile_services'		=> 'servicos',
			'_profile_place'		=> 'local-atendimento',
			'_profile_work_days'	=> 'dias-de-trabalho',
			'_profile_country'		=> 'localizacao',
			'_profile_state'		=> 'localizacao',
			'_profile_city'			=> 'localizacao',
			'_profile_district'		=> 'localizacao'
		);
		foreach( $metaKeys as $key ){
			$userData[$key] = get_user_meta( $userId, $key, true );
		}

		# Criando a página de perfil
		$postarr = [
			'post_title'	=> $userData['display_name'],
			'post_author'	=> $userId,
			'post_content'	=> $userData['account_description'],
			'post_status'	=> 'draft',
			'post_type'		=> 'a2_escort',
		];
		$postid = wp_insert_post( $postarr );

		# Salvando meta-posts e taxonomias
		if( !is_wp_error( $postid ) ){
			foreach( $userData as $key => $value ){
				if( array_key_exists( $key, $taxonomies ) ){
					$taxonomy = $taxonomies[$key];
					wp_set_post_terms( $postid, $value, $taxonomy );
				} else {
					update_post_meta( $postid, $key, $value );
				}
			}
		}
    }
}