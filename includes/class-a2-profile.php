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
		// Actions devem ser movidas para um arquivo único pois não podem ser registradas no __contruct de uma classe que é invocada por outras.
    }

    /**
	 * Salvar dados customs no perfil do usuário 
	 * 
	 * @param integer $userId 
	 */
	public function saveData( $userId ) 
	{

		$metaKeys = array(
			'_profile_whatsapp',
			'_profile_birthday',
			'_profile_description',
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
			'_profile_specialties',
			'_profile_languages',
			'_profile_instagram',
			'_profile_tiktok',
			'_profile_onlyfans',
			'_profile_country',
			'_profile_state',
			'_profile_city',
			'_profile_district',
			'_profile_address',
			'_profile_cep',
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
			'taxonomy'		=> 'profile_payment_methods',
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
			if( in_array($key, ['_profile_he_meets', '_profile_services', '_profile_place', '_profile_work_days', '_profile_specialties', '_profile_languages']) ){
				update_user_meta( $userId, $key, $_POST[$key] );
			} else {
				update_user_meta( $userId, $key, sanitize_text_field( $_POST[$key] ) );
			}

			$log[$key] = $_POST[$key];
		}
		
		$profileIsReady = $this->validateAccount( $userId, $metaKeys );
		if( $profileIsReady == true ){
			$this->setupPage( $userId );
			$this->markAsComplete( $userId );
		} else {
			$this->markAsIncomplete( $userId );
			$this->saveIncompleteLog( $userId, $profileIsReady );
		}
	}

	/**
	 * Este método retorna se o perfil foi marcado como pronto ou não
	 * 
	 * @param int $userId
	 */
	public function isReady( $userId )
	{
		$metaKey	= '_is_ready';
		$isReady 	= get_user_meta( $userId, $metaKey, true );

		$result 	= false;
		if( $isReady == 'yes' ){
			// $result = 'O perfil do usuário #'. $userId .' está pronto.';
			$result = true;
		}

		return $result;
	}

    /**
     * Este método recebe o ID do usuário e executa ações para publicar um perfil publico da acompanhante.
     * 
     * @param int $userId
     */
    private function setupPage( $userId )
    {
		$userData 					= array();
		$user 						= get_userdata( $userId );
		$userData['id']				= $userId;
		$userData['first_name'] 	= $user->first_name;
		$userData['last_name'] 		= $user->last_name;
		$userData['display_name'] 	= $user->display_name;
		$userData['full_name']		= $user->first_name . ' ' . $user->last_name;
		$userData['email']			= $user->user_email;

		# Meta post
		$metaKeys = array(
			'_profile_whatsapp',
			'_profile_birthday',
			'_profile_description',
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
			'_profile_specialties',
			'_profile_languages',
			'_profile_instagram',
			'_profile_tiktok',
			'_profile_onlyfans',
			'_profile_country',
			'_profile_state',
			'_profile_city',
			'_profile_district',
			'_profile_address',
			'_profile_cep',
			'_profile_cache_quickie',
			'_profile_cache_half_an_hour',
			'_profile_cache_hour',
			'_profile_cache_overnight_stay',
			'_profile_cache_promotion',
			'_profile_cache_promotion_activated',
			'_profile_work_days',
			'_profile_office_hour',
		);		

		# Taxonomias
		// profile meta key => taxonomy
		$taxonomies = array(
			'_profile_ethnicity' 		=> 'profile_ethnicity',
			'_profile_genre'			=> 'profile_genre',
			'_profile_sign'				=> 'profile_sign',
			'_profile_he_meets'			=> 'profile_preference',
			'_profile_services'			=> 'profile_services',
			'_profile_place'			=> 'profile_place_of_service',
			'_profile_work_days'		=> 'profile_work_days',
			'_profile_country'			=> 'profile_localization',
			'_profile_state'			=> 'profile_localization',
			'_profile_city'				=> 'profile_localization',
			'_profile_district'			=> 'profile_localization',
			'_profile_languages'		=> 'profile_languages',
			'_profile_specialties'		=> 'profile_specialties',
		);

		# Coletando Formas de pagamento
		$args = array(
			'taxonomy'		=> 'profile_payment_methods',
			'hide_empty'	=> false,
			'orderby'		=> 'name',
			'order'			=> 'ASC'
		);
		$formasPagamento = get_terms( $args );
		if( !empty( $formasPagamento ) ){
			foreach ($formasPagamento as $pagamento ){
				$key 				= '_profile_payment_method_' . $pagamento->term_id;
				$metaKeys[] 		= $key;
				$taxonomies[$key] 	= 'profile_payment_methods';
			}
		}

		# Preenchendo $userData
		foreach( $metaKeys as $key ){
			$userData[$key] = get_user_meta( $userId, $key, true );
		}

		# Criando a página de perfil
		$postarr = [
			'post_title'	=> $userData['display_name'],
			'post_author'	=> $userId,
			'post_content'	=> $userData['_profile_description'],
			'post_status'	=> 'draft',
			'post_type'		=> 'a2_escort',
		];
		$postid = wp_insert_post( $postarr );

		# Salvando meta-posts e taxonomias
		if( !is_wp_error( $postid ) ){
			foreach( $userData as $key => $value ){
				# Se for taxonomia
				if( array_key_exists( $key, $taxonomies ) ){
					$taxonomy = $taxonomies[$key];
					
					# Se for método de pagamento
					if( $taxonomy == 'profile_payment_methods' && $value == 'on' ){
						$arr 		= explode( '_', $key );
						$termId 	= end( $arr );
						$term 		= get_term( $termId, $taxonomy );
						$value 		= $term->name;
					}

					# Se $value != null salva o termo
					if ( !is_null( $value ) ){
						wp_set_post_terms( $postid, $value, $taxonomy );
					}
				} else {
					update_post_meta( $postid, $key, $value );
				}
			}
		}
    }

	/**
	 * Responsável por fazer verificações e validar dados do perfil de um usuário
	 * Perfil incompleto será rejeitados e deletado em 30 dias.
	 * Perfil completo passará pela validação.
	 * Perfil validado terá sua página de perfil publicada no site(ainda aguardando aquisição de algum plano de vantagem)
	 * 
	 * @param int $userId		
	 * @param array $metaKeys 	para retornar os dados gravados na conta
	 */
	private function validateAccount( $userId, $metaKeys )
	{
        if( is_null( $userId ) || !user_can( $userId, 'a2_scort' ) )
			return;

		$profileData = array();
		foreach( $metaKeys as $key ){
			$profileData[$key] = get_user_meta( $userId, $key, true );
		}

		# Checagem
		return $this->checkData( $profileData );
	}

	/**
	 * Checagem dos dados de perfil de um usuário
	 * Dados completos: Retorna True
	 * Dados incompletos: Retorna um array com as chaves incompletas
	 * 
	 * @param array $profileData 	Array com dados que serão testados
	 * @return bool $result
	 */
	private function checkData( $profileData )
	{
		if( empty( $profileData ) || is_null( $profileData ) )
			return false;

		$log 	= array();
		foreach( $profileData as $key => $value ){
			if( strlen( $value ) == 0 && !is_array( $value ) ){
				$log[$key] = $value;
			} else if( is_array( $value ) && empty($value) ){
				$log[$key] = $value;
			}
		}

		$result = true;
		if( !empty( $log ) ){
			$result	= $log;
		}

		return $result;
	}

	/**
	 * Método responsável por marcar o perfil como completo. 
	 * Este método deve ser chamado após a validação do perfil.
	 * 
	 * @param int $userId
	 * @return bool
	 */
	private function markAsComplete( $userId )
	{
		$metaKey 	= '_is_ready';
		return update_user_meta( $userId, $metaKey, 'yes');
	}

	/**
	 * Método responsável por marcar o perfil como incompleto.
	 * Este método deve ser chamado após a validação do perfil.
	 * 
	 * @param int $userId
	 * @return bool
	 */
	private function markAsIncomplete( $userId )
	{
		$metaKey	= '_is_ready';		
		return update_user_meta( $userId, $metaKey, 'no' );
	}

	/**
	 * Método responsável por salvar o log de informações inválidas ou incompletas em um perfil
	 *  
	 * @param int $userId
	 * @return bool
	 */
	private function saveIncompleteLog( $userId, $data )
	{
		$metaKeyLog 	= '_incomplete_data';		
		return update_user_meta( $userId, $metaKeyLog, $log );
	}
}