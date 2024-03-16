<?php
class SL_WoocTemplates{

    public function __construct()
    {
		$this->helper = new A2_Helper();

		$this->metaKeys = [
			'_profile_whatsapp',
			'_profile_birthdate',
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
			'_profile_cover',
			'_profile_page_id'
		];
        # Register shortcodes
        add_shortcode('sl_tplEditAccount', [$this, 'tplEditAccount']);
		add_shortcode('sl_tplGallery', [$this, 'tplGallery']);
		add_shortcode('sl_tplNavigation', [$this, 'tplNavigation']);
		add_shortcode('sl_tplDashboard', [$this, 'tplDashboard']);
    }
   
	/**
	 * Formulário perfil - Editar conta;
	 * @template form-edit-account.php
	 * 
	 */
	public function tplEditAccount($atts)
	{
		$a = shortcode_atts( 
            [
                'title' => ''
			], 
            $atts
        );

		defined( 'ABSPATH' ) || exit;

		# Coleta de dados do usuário
		$userId 	= get_current_user_id();
		$user 		= get_userdata($userId);
		$userData 	= [];
		foreach( $this->metaKeys as $key ){
			$userData[$key] = get_user_meta( $user->ID, $key, true );
		}

		#Signos
		$args = array(
			'taxonomy'		=> 'profile_sign',
			'hide_empty'	=> false,
			'orderby'		=> 'name',
			'order'			=> 'ASC'
		);
		$signos = get_terms( $args );

		# Etnias
		$args['taxonomy'] = 'profile_ethnicity';
		$etnias = get_terms( $args );

		# Generos
		$args['taxonomy'] = 'profile_genre';
		$generos = get_terms( $args );

		# Atendimento
		$args['taxonomy'] = 'profile_preference';
		$preferencias = get_terms( $args );

		# Serviços
		$args['taxonomy'] = 'profile_services';
		$servicos = get_terms( $args );

		# Locais de atendimento
		$args['taxonomy'] = 'profile_place_of_service';
		$locaisAtendimento = get_terms( $args );

		# Dias da semana
		$args['taxonomy'] = 'profile_work_days';
		$diasTrabalho = get_terms( $args );

		# Especialidades
		$args['taxonomy'] = 'profile_specialties';
		$especialidades = get_terms( $args );

		# Idiomas
		$args['taxonomy'] = 'profile_languages';
		$idiomas = get_terms( $args );


		# Formas de pagamento
		$args['taxonomy'] = 'profile_payment_methods';
		$paymentMethods = get_terms( $args );
		if( !empty( $paymentMethods ) ){
			foreach( $paymentMethods as $pagamento ){
				$metaKeys[] = '_profile_payment_method_' . $pagamento->term_id;
			}
		}

		# Paises
		$args['taxonomy'] = 'profile_localization';
		$args['parent'] = 0;
		$countries = get_terms($args);

		# Estados
		$states = '';
		if($userData['_profile_country']){
			$args['parent']	= $userData['_profile_country'];
			$states = get_terms($args);
		}

		# Cidade 
		$city = '';
		if($userData['_profile_city']){
			$city = get_term_by('name', $userData['_profile_city'], 'profile_localization');
		}

		# Cidades pelo estado
		$cities = '';
		if($userData['_profile_state']){
			$stateObj = get_term_by('id', $userData['_profile_state'], 'profile_localization');
			$cities = $this->helper->getCitiesByUfFromIBGE($stateObj->slug);
			$sanitizedCities = $this->helper->sanitizeCitiesFromIBGE($cities);
		}

		// Coletando a url da foto de perfil, se existir uma
		$profilePhotoUrl = '';
		if( strlen($userData['_profile_photo'] ) != 0 ){
			$profilePhotoUrl = wp_get_attachment_url( $userData['_profile_photo'] );
		}

		// Coletando a url da foto de capa, se existir uma
		$profileCoverUrl = '';
		if( strlen($userData['_profile_cover']) != 0 ){
			$profileCoverUrl = wp_get_attachment_url( $userData['_profile_cover'] );
		}

		ob_start();
		require plugin_dir_path( __DIR__ ) . 'templates/dashboard/edit-profile.php';
		return ob_get_clean();
	}

	public function tplGallery($atts)
	{
		$a = shortcode_atts( 
            [
                'title' => ''
			], 
            $atts
        );

		defined( 'ABSPATH' ) || exit;
		# Coleta de informações
		$profileHelper      = new A2_ProfileHelper();
		$userId 		    = get_current_user_id();
		$profilePageLink    = $profileHelper->getProfileLink();
		$galleryList 	    = $profileHelper->getGallery();

		if( !empty( $galleryList ) ){
			$totalMidias 	= count($galleryList);
		} else {
			$totalMidias  	= 0;
		}
		ob_start();
		require plugin_dir_path( __DIR__ ) . 'templates/dashboard/my-gallery.php';
		return ob_get_clean();
	}

	/**
	 * Navigation Escort
	 */
	public function tplNavigation($atts)
	{
		$a = shortcode_atts( 
            [
                'title' => ''
			], 
            $atts
        );
		
		$user 		= wp_get_current_user();
		$userId 	= $user->ID;
		$userData 	= [];
		foreach( $this->metaKeys as $key ){
			$userData[$key] = get_user_meta( $userId, $key, true );
		}

		$profilePhotoUrl = wp_get_attachment_url( $userData['_profile_photo'] );
		$profileCoverUrl = wp_get_attachment_image_url($userData['_profile_cover'], 'full');

		ob_start();
		require plugin_dir_path( __DIR__ ) . 'templates/dashboard/navigation.php';
		return ob_get_clean();
	}

	/**
	 * Dashboard Escort
	 * 
	 */
	public function tplDashboard($atts)
	{
		$a = shortcode_atts( 
            [
                'title' => ''
			], 
            $atts
        );

		$profileHelper      = new A2_ProfileHelper();
		$verificationStatus = $profileHelper->getVerifyStatus();
		$profilePage 		= $profileHelper->getProfileLink();
		$defaultUserThumb 	= get_stylesheet_directory_uri() . '/images/default-user-profile.png';
		$showNotifications 	= true;

		if( strlen($verificationStatus) > 1 ){
			$showNotifications = false;
		}
		if( $profilePage ){
			$showNotifications = false;
		}

		$args = [
			'post_type'		=> 'post',
			'numberposts'	=> -1,
			'category_name'	=> 'guias',
		];
		$guides = new WP_Query($args);

		ob_start();
		require plugin_dir_path( __DIR__ ) . 'templates/dashboard/dashboard.php';
		return ob_get_clean();
	}
}