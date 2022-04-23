<?php

/**
 * Este arquivo define a classe A2_Advertisement
 * Está classe recebe métodos e executa tarefas relacionadas aos anúncios da plataforma.
 * 
 * @package    A2
 * @subpackage A2/includes
 * @since      1.0.0
 * @author     jnz93 <box@unitycode.tech>
*/
class A2_Advertisement{

	/**
	 * Post type for Advertisement
	 */
	private $postType;

	/**
	 * Status default: draft
	 */
	private $postStatus;

	/**
	 * Meta to save list of activated advertisements
	 */
	private $metaKeyActivatedItems;

	/**
	 * Meta to save plan duration on advertisement post
	 */
	private $metaKeyPlanDuration;

	/**
	 * Meta to save expiration date of advertisments
	 */
	private $metaKeyExpirateDate;

    public function __construct()
    {
		$this->postType 				= 'a2_advertisement';
		$this->postStatus				= 'publish';
		$this->metaKeyActivatedItems 	= '_activated_advertisements';
		$this->metaKeyPlanDuration 		= '_plan_duration';
		$this->metaKeyExpirateDate		= '_expiration_date';
    }

	/**
     * Método que executa a criação do anúncio
	 * Este método normalmente é chamado quando a confirmação do pagamento do pedido é confirmada.
     * 
     * @hook woocommerce_order_status_completed
     * @link https://woocommerce.com/document/managing-orders/
     * 
     * @param integer   $order_id
     * @param mixed     $result
     */
    public function create( $order_id )
    {
        $dataOrder      = $this->getDataOrder( $order_id );
        $customerId     = $dataOrder['customer_id'];
        $dataProfile    = $this->getDataProfile( $customerId );

		# Pegar URL da página de perfil
		$metaKeyProfilePage	= '_profile_page_id';
		$profilePageId		= get_user_meta( $customerId, $metaKeyProfilePage, true );
		$profilePageUrl		= '';
		if( !is_wp_error($profilePageId) ){
			$profilePageUrl = get_permalink( $profilePageId );
		}

		# Setup Advertisement
        $postarr = [
            'post_title'	=> $dataProfile['display_name'],
            'post_author'	=> $customerId,
            'post_content'	=> $dataProfile['_profile_description'],
            'post_status'	=> $this->postStatus,
            'post_type'		=> $this->postType
        ];
        $postid = wp_insert_post( $postarr );

        # Salvando meta-posts e taxonomias
		if( !is_wp_error( $postid ) ){
            $taxonomies             = array(
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

			# Coletando Formas de pagamento p/ $taxonomies
			$args = array(
				'taxonomy'		=> 'profile_payment_methods',
				'hide_empty'	=> false,
				'orderby'		=> 'name',
				'order'			=> 'ASC'
			);
			$paymentMethods = get_terms( $args );
			if( !empty( $paymentMethods ) ){
				foreach ($paymentMethods as $method ){
					$key 				= '_profile_payment_method_' . $method->term_id;
					$taxonomies[$key] 	= 'profile_payment_methods';
				}
			}
			
			# Salvando e coletando dados a serem salvos(meta-campos e taxonomias)
			$selectedPayments 		= array();
			$selectedLocalization 	= array();
			foreach( $dataProfile as $key => $value ){
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

			# Salvando Localização(país, estado, cidade e bairro)
			if( !empty( $selectedLocalization ) ){
				wp_set_post_terms( $postid, $selectedLocalization, 'profile_localization' );
			}

            # Salvando tag do plano adquirido
            if( strlen($dataOrder['product_name'] > 0 ) ){
                $sanitizePlan  = strtolower(str_replace( ' ', '-', $dataOrder['product_name'] ));
                wp_set_post_terms( $postid, $sanitizePlan, 'advertisement_level' );
            }

			# Salvar o tempo de duração do plano adquirido
			$planDuration	= explode( '-', $dataOrder['product_duration'] );
			$planDuration	= (integer) trim($planDuration[0]);
			update_post_meta( $postid, $this->metaKeyPlanDuration, $planDuration );

			# Salvar a URL da página de perfil
			update_post_meta( $postid, '_profile_url', $profilePageUrl );
		}
    }

    /**
	 * Este método recebe o ID do pedido para checar a validade do mesmo
	 * se for válido retornar o payload $dataOrder
	 * 
     * @param integer   $orderId
     * @return mixed    $dataOrder
     */
    public function getDataOrder( $orderId )
    {
        $dataOrder = false;
        $order = wc_get_order( $orderId );

        if( !is_wp_error( $order ) ){
            $orderStatus = $order->get_status();
            if( $orderStatus === 'completed' ){

                # Product data
                foreach ($order->get_items() as $item_key => $item ){
                    $dataItem = $item->get_data();
                    
                    $pName      = $dataItem['name'];
                    $pId        = $dataItem['product_id'];
                    $pDuration  = wc_get_order_item_meta( $pId, 'pa_duracao', true );
                    
                    # Sanitização $pName
                    if( $pName ){
                        $pieces     = explode( '-', trim( $pName ) );
                        $pName      = str_replace( ' ', '-', strtolower(trim($pieces[0])) );

                        # extração da duração direto do nome, caso $pDuration seja uma string vazia
                        if( strlen($pDuration) == 0 || empty( $pDuration ) ){
                            $pDuration  = str_replace( ' ', '-', strtolower(trim($pieces[1])) );
                        }
                    }

                    # Payload $dataOrder
                    $dataOrder = [
                        'order_id'          => $order->get_id(),
                        'customer_id'       => $order->get_user_id(),
                        'product_name'      => $pName,
                        'product_duration'  => $pDuration
                    ];
                }
            }
        }

        return $dataOrder;
    }

    /**
     * Este método recebe o ID do usuário e retornar o payload $dataProfile
	 * Dados de $dataProfile são utilizados na composição do anúncio
     * 
     * @param integer   $userId
     * @return mixed    $dataProfile
     */
    public function getDataProfile( $userId )
    {
        $user 						= get_userdata( $userId );
        $userData 					= array();
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

        return $userData;
    }


	/**
	 * Este método recebe o ID do anúncio para salvar a data de expiração
	 * A contagem é feita a partir do momento em que este método é chamado
	 * A data é convertida e salva no padrão Timestamp unix(https://timestamp.online/)
	 * 
	 * @hook draft_to_publish
	 * @param int 	$postId
	 * @return void
	 */
	public function saveExpirationDate( $postId )
	{
		if( is_null( $postId ) ) return;
		date_default_timezone_set('America/Sao_Paulo'); # Setando GMT padrão
		
		$planDuration	= get_post_meta( $postId, $this->metaKeyPlanDuration, true );
		$now 			= time();
		$expiresDate	= $now + ($planDuration * 24 * 60 * 60);

		update_post_meta( $postId, $this->metaKeyExpirateDate, $expiresDate );
	}


	/**
	 * Este método recebe o ID do anúncio e retorna a data de expiração
	 * A data precisa ser convertida de timestamp p/ o padrão "dd/mm/YY H:m:s"
	 * 
	 * @param int 		$postId
	 * @return string 	$date
	 */
	public function getExpirationDate( $postId )
	{
		if( is_null( $postId ) ) return;
		date_default_timezone_set('America/Sao_Paulo'); # Setando GMT padrão

		$timeStampDate = get_post_meta( $postId, $this->metaKeyExpirateDate, true );
		$date = date( 'd-m-Y H:i:s', $timeStampDate );

		return $date;
	}

	/**
	 * Este método salva o ID do anúncio ativado no usuário
	 * Este método é chamado quando um post, do tipo "a2_advertisement", é publicado
	 * 
	 * @hook draft_to_publish
	 * @param int 	$postId			Id a ser salvo
	 * @param int 	$customerId		Id do usuário dono do anúncio
	 * 
	 * @return void
	 */
	public function saveActivatedItem( $postId, $customerId )
	{
		$currActiveList 			= get_user_meta( $customerId, $this->metaKeyActivatedItems );
		$activatedItemsList			= array();
		$activatedItemsList[]		= $postId;
		if( is_array($currActiveList) && !empty($currActiveList) ){
			$activatedItemsList = array_merge( $activatedItemsList, $currActiveList[0] );
		}
		update_user_meta( $customerId, $this->metaKeyActivatedItems, $activatedItemsList );
	}

	/**
	 * Este método remove o ID do anúncio da lista de ativos do usuário
	 * quando o anúncio é alterado para "Rascunho"
	 * 
	 * @hook draft_to_publish
	 * @param int 	$postId			Id a ser removido
	 * @param int 	$customerId		Id do usuário dono do anúncio
	 * 
	 * @return bool
	 */
	public function removeActivatedItem( $postId, $customerId )
	{
		$activatedItemsList	= $this->getActivatedItems( $customerId );
		$pos = array_search( $postId, $activatedItemsList );
		if( $pos !== false ){
			unset( $activatedItemsList[$pos] );
			update_user_meta( $customerId, $this->metaKeyActivatedItems, $activatedItemsList );
		}
	}

	/**
	 * Este método retorna a lista de anúncios ativos por usuário.
	 * 
	 * @param int 		$customerId
	 * @return array 	$activatedItemsList
	 */
	public function getActivatedItems( $customerId )
	{
		if( is_null( $customerId ) ) return;

		$activatedItemsList	= get_user_meta( $customerId, $this->metaKeyActivatedItems );		
		if( is_array($activatedItemsList) && !empty($activatedItemsList) ){
			$activatedItemsList = $activatedItemsList[0];
		}

		return $activatedItemsList;
	}
}
