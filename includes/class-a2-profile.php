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
			'_profile_photo',
			'_profile_cover'
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
			$value = $_POST[$key];
			if( in_array($key, ['_profile_he_meets', '_profile_services', '_profile_place', '_profile_work_days', '_profile_specialties', '_profile_languages']) ){
				update_user_meta( $userId, $key, $value );
			} else {
				// Cálculando a idade
				if( $key == '_profile_birthday' ){
					$value 	= $this->calculateAge($value);
					$key 	= '_profile_age';
				}
				update_user_meta( $userId, $key, sanitize_text_field($value) );
			}

			$log[$key] = $_POST[$key];
		}

		// $profileIsReady = $this->validateAccount( $userId, $metaKeys ); Desativado métdo "checkData" está com problema
		$profileIsReady = true;
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
		if( !current_user_can('a2_scort') ) return;

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
			'_profile_age',
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
			'_profile_photo',
			'_profile_cover'
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

		# Checar se a página de perfil já existe
		$foundProfile = $this->checkProfilePageExists( $userId );
		$postid = '';
		if( $foundProfile ){
			# Pegar o ID da página de perfil
			$postid = $this->getProfilePageId( $userId );
		} else {
			# Criando a página de perfil
			$postarr = [
				'post_title'	=> $userData['display_name'],
				'post_author'	=> $userId,
				'post_content'	=> $userData['_profile_description'],
				'post_status'	=> 'publish',
				'post_type'		=> 'a2_escort',
			];
			$postid = wp_insert_post( $postarr );
		}

		# Salvando meta-posts e taxonomias
		if( !is_wp_error( $postid ) ){
			$selectedPayments 		= array();
			$selectedLocalization 	= array();
			foreach( $userData as $key => $value ){
				# Se for taxonomia
				if( array_key_exists( $key, $taxonomies ) ){
					$taxonomy = $taxonomies[$key];
					
					# Coletando métodos de pagamento
					if( $taxonomy == 'profile_payment_methods' && $value == 'on' ){
						$arr 				= explode( '_', $key );
						$termId 			= end( $arr );
						$term 				= get_term( $termId, $taxonomy );
						$selectedPayments[] = $term->name;
						continue;
					}

					# Coletando localizações
					if( $taxonomy == 'profile_localization' && strlen( $value ) > 0 ){
						$selectedLocalization[] = $value;
						continue;
					}

					# Se $value != null salva o termo
					if ( !is_null( $value ) ){
						wp_set_post_terms( $postid, $value, $taxonomy );
					}
				} elseif( $key == '_profile_photo' ){
					set_post_thumbnail( $postid, $value );
				} else {
					update_post_meta( $postid, $key, $value );
				}
			}

			# Salvando métodos de pagamento selecionados
			if( !empty( $selectedPayments ) ){
				wp_set_post_terms( $postid, $selectedPayments, 'profile_payment_methods' );
			}

			# Salvando Localizações
			if( !empty( $selectedLocalization ) ){
				wp_set_post_terms( $postid, $selectedLocalization, 'profile_localization' );
			}

			# Salvar o $postid da página no user meta
			$this->saveIdOfProfilePage( $userId, $postid );
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
			// Este pedaço de código está com erro.
			if( strlen( $value ) == 0 && !is_array( $value ) ){
				$log[$key] = $value;
			}elseif( is_array( $value ) && empty($value) ){
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

	/**
	 * Método responsável por salvar o ID do post de perfil criado em um meta campo do usuário
	 * 
	 * @param int $userId
	 * @param int $postId
	 * @return bool
	 */
	private function saveIdOfProfilePage( $userId, $postId )
	{
		$metaKey 	= '_profile_page_id';
		return update_user_meta( $userId, $metaKey, $postId );
	}

	/**
	 * Método responsável por retornar o ID da página de perfil do usuário
	 * 
	 * @param int $userId
	 * @return int $pageId
	 */
	public function getProfilePageId( $userId )
	{
		$metaKey 		= '_profile_page_id';
		$pageId 		= get_user_meta( $userId, $metaKey, true );

		return $pageId;
	}

	/**
	 * Método responsável por verificar a existência de uma página de perfil daquele usuário
	 * 
	 * @param int $userId
	 * @return bool
	 */
	private function checkProfilePageExists( $userId )
	{
		$metaKey 		= '_profile_page_id';
		$profileExists 	= false;
		$pageId 		= get_user_meta( $userId, $metaKey, true );

		if( strlen( $pageId ) > 0 ){
			$foundProfile 	= get_post_status( $pageId );
			
			if( $foundProfile ){
				$profileExists = true;
			}
		}

		return $profileExists;
	}

	/**
	 * Contagem da idade - Este método deve ser movido para uma nova classe chamada "A2_Profile_Helper()"
	 * Coleta a idade salva no $post, formata o timestamp em data e faz a contagem
	 * 
	 * @param $postId 		Id da página de perfil ou anúncio
	 * 
	 * @return string/bool
	 */
	public function getAge( $postId )
	{
		if( is_null($postId) ) return;

		$birthday 	= get_post_meta( $postId, '_profile_birthday', true );
		$age 		= null;
		if( $birthday ){
			// Coletando datas atuais
			$currDay 	= date ('d');
			$currMonth 	= date ('m');
			$currYear 	= date ('Y');

			// Coletando data do perfil
			$arr 	= explode('-', $birthday);
			$bYear 	= $arr[0];
			$bMonth = $arr[1];
			$bDay 	= $arr[2];

			$age 	= $currYear - $bYear;
			if( $currMonth < $bMonth ){
				$age--;
			}elseif( $currMonth == $bMonth && $currDay <= $bDay ){
				$age--;
			}
		}

		return $age;
	}

	/**
	 * Método que retorna a idade conforme a data de entrada
	 * 
	 * @param string 	$date 	uma data no padrão yyyy/mm/dd
	 */
	public function calculateAge( $date )
	{
		if( !$date ) return;

		$age 		= null;
		$currDay 	= date ('d');
		$currMonth 	= date ('m');
		$currYear 	= date ('Y');

		// Coletando data do perfil
		$arr 	= explode('-', $date);
		$bYear 	= $arr[0];
		$bMonth = $arr[1];
		$bDay 	= $arr[2];

		$age 	= $currYear - $bYear;
		if( $currMonth < $bMonth ){
			$age--;
		}elseif( $currMonth == $bMonth && $currDay <= $bDay ){
			$age--;
		}

		return $age;
	}
	/**
	 * Método responsável por marcar o perfil sob análise
	 * 
	 * @param int 	$userId
	 */
	public function underAnalysis( $userId )
	{
		$key 	= '_verified_profile';
		$value 	= 'under-analisys';
		update_user_meta( $userId, $key, $value );
	}

	/**
	 * Método responsável por marcar o perfil como verificado
	 * 
	 * @param int 	$userId
	 * @param int 	$adminId
	 */
	public function markAsValid( $userId, $adminId )
	{
		$key 	= '_verified_profile';
		$value 	= 'verified';
		update_user_meta( $userId, $key, $value );
		update_user_meta( $userId, '_rated_by', $adminId); # Salvar ID de avaliou
	}

	/**
	 * Método responsável por marcar o perfil como reprovado
	 * 
	 * @param int 	$userId
	 * @param int 	$adminId
	 */
	public function markAsInvalid( $userId, $adminId )
	{
		$key 	= '_verified_profile';
		$value 	= 'invalid';
		update_user_meta( $userId, $key, $value );
		update_user_meta( $userId, '_rated_by', $adminId); # Salvar ID de avaliou
	}

	/**
	 * Método retorna o status da verificação de perfil
	 * 
	 * @param int 	$userId
	 */
	public function validationStatus( $userId )
	{
		$key 	= '_verified_profile';
		$value 	= get_user_meta( $userId, $key, true );

		return $value;
	}
	
	/**
	 * Payload de dados do usuário.
	 * Este payload vai ser largamente utilizado nos cards dos anúncios.
	 * Ao invés de coletarmos os dados do $post no loop vamos coletar dados direto da conta.
	 * 
	 * @param int 		$authorId
	 * @return array 	$payload
	 */
	public function getUserData( $userId ){
		if( is_null($userId) ) return;
		
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
		
	}
}