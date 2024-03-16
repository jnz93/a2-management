<?php
/**
 * The file that defines the shortcode classes
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package     A2
 * @subpackage  A2/includes
 * @since       1.0.0
 * @author      jnz93 <box@unitycode.tech>
 * @link        https://codex.wordpress.org/Shortcode_API
 */
class A2_Shortcodes{

    /**
     * Instância da classe A2_Register
     */
    private $register;

    /**
     * Instância da classe A2_Query
     */
    private $a2Query;

    /**
     * Instância da classe A2_Gallery
     */
    private $gallery;

    /**
     * Instância da classe A2_Profile
     * Temporário para utilizaro método "getAge()"
     */
    private $dataProfile;

    /**
	 * Inicialização da classe e configurações de hooks, filtros e propriedades.
	 *
	 * @since    1.0.0
	 */
    public function __construct()
    {
        $this->register         = new A2_Register();
        $this->a2Query          = new A2_Query();
        $this->gallery          = new A2_Gallery();
        $this->profileHelper    = new A2_ProfileHelper();
        $this->Adv              = new A2_Advertisement();

        # Meta Keys para coletar post metas
        $this->profileMetas = [
            'id',
            'first_name',
            'last_name',
            '_plan_level',
            '_profile_url',
            '_expiration_date',
            '_profile_whatsapp',
            '_profile_birthdate',
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
        ];

        /** Formulário de login */
        add_shortcode( 'loginForm', [ $this, 'loginForm'] );

        /** Lista de anúncios por localidade */
        add_shortcode( 'advCarousel', [ $this, 'listCarouselAdvertisement'] );

        /** Lista de anúncios genérica  */
        add_shortcode( 'listAdvertisement', [ $this, 'listAdvertisement' ] );

        /** Componente de busca */
        add_shortcode( 'buscaCidade', [ $this, 'searchComponent' ] );

        /** Componente de filtro */
        add_shortcode( 'filtroAnuncios', [ $this, 'filterComponent' ] );

        /** Checkmark de verificação */
        add_action( 'profileCheckmark', [ $this, 'getProfileCheckmark' ] );

        /** Rest Uploader */
        add_shortcode( 'restUploader', [ $this, 'restUploaderForm'] );

        /** Register Page */
        add_shortcode( 'registerPage', [ $this, 'registerPage'] );

        /** Card Active Adv Info */
        add_shortcode( 'activeAdvInfo', [ $this, 'cardAdvActiveInfo'] );

        /** Slider products */
        add_shortcode( 'sliderProducts', [ $this, 'sliderProducts' ] );

        /** Caixa com cards estatísticos */
        add_shortcode( 'profileStatistics', [ $this , 'boxStatistics' ] );

        /** Card de Anúncio */
        add_shortcode( 'profileCard', [ $this, 'advCard' ] );

        /** Conteúdo da página de perfil da acompanhante */
        add_shortcode('profileContent', [$this, 'scortProfileContent']);
    }

    /**
     * Shortcode: Formulário de login
     * 
     * Após o login, o tipo de usuário é identificado e redirecionado para o painel correspondente.
     * Usuários logados devem ser redirecionados para o painel correspondente
     */
    public function loginForm( $atts )
    {
        $a = shortcode_atts( 
            [
                'title' => 'Formulário de Cadastro'
			], 
            $atts
        );
        
        $login = ( isset( $_GET['login'] ) ) ? $_GET['login'] : 0;
        $args = array(
            'redirect'          => home_url('/painel'),
        );

        ob_start();

        if( $login === 'failed' ){
            echo '<p class="login-message"> <strong class="">'. __('ERRO: ') .'</strong>'. __('Nome ou senha inválidos.') .'</p>';
        } elseif( $login === 'empty' ){
            echo '<p class="login-message"> <strong class="">'. __('ERRO: ') .'</strong>'. __('Nome ou senha estão vazios.') .'</p>';
        } elseif( $login === 'false' ){
            echo '<p class="login-message"> <strong class="">'. __('ERRO: ') .'</strong>'. __('Você está desconectado.') .'</p>';
        }

        wp_login_form( $args );

        return ob_get_clean();
    }

    /**
     * Carousel de anúncios
     * 
     * Retorna o carousel de anúncios padrão
     * Pode receber atributos como país, estado ou cidade para filtrar os resultados
     * Ex: [advCarousel pais="" estado="" cidade="" qtd=""]
     */
    public function listCarouselAdvertisement( $atts )
    {
        $a = shortcode_atts( 
            [
                'pais'      => 'br',
                'estado'    => null,
                'cidade'    => null,
                'qtd'       => 8,
			], 
            $atts
        );
        $query = $this->a2Query->advByLocation($a['pais'], $a['estado'], $a['cidade'], $a['qtd']);
        
        $titleCarousel      = '';
        $subtitleCarousel   = '';
        $showWhat           = '';
        $pageLocationLink   = '';
        $totalPosts         = $query->found_posts;
        $paises = [
            'ar' => 'argentina',
            'bo' => 'bolívia',
            'br' => 'brasil',
            'cl' => 'chile',
            'co' => 'colômbia',
            'ec' => 'equador',
            'gy' => 'guiana',
            'fr' => 'guiana francesa',
            'py' => 'paraguai',
            'pe' => 'peru',
            'sr' => 'suriname',
            'uy' => 'uruguai',
            've' => 'venezuela',
        ];
        $estados = [
            'ac'    => 'acre',
            'al'    => 'alagoas',
            'ap'    => 'amapá',
            'am'    => 'amazonas',
            'ba'    => 'bahia',
            'ce'    => 'ceará',
            'df'    => 'distrito federal',
            'es'    => 'espirito santo',
            'go'    => 'goiás',
            'ma'    => 'maranhão',
            'mt'    => 'mato grosso',
            'ms'    => 'mato grosso do sul',
            'mg'    => 'minas gerais',
            'pa'    => 'pará',
            'pb'    => 'paraíba',
            'pr'    => 'paraná',
            'pe'    => 'pernambuco',
            'pi'    => 'piauí',
            'rj'    => 'rio de janeiro',
            'rn'    => 'rio grande do norte',
            'rs'    => 'rio grande do sul',
            'ro'    => 'rondônia',
            'rr'    => 'roraima',
            'sc'    => 'santa catarina',
            'sp'    => 'são paulo',
            'se'    => 'sergipe',
            'to'    => 'tocantins'
        ];

        if( !is_null($a['cidade']) ){
            $titleCarousel      = $a['cidade'];
            $subtitleCarousel   = $a['estado'];
            $showWhat           = 'last-lvl';
        }

        if( is_null($a['cidade']) && !is_null($a['estado']) ){
            $titleCarousel      = $estados[$a['estado']];
            $subtitleCarousel   = $a['pais'];
            $showWhat           = 'third-lvl';
        }

        if( is_null($a['cidade']) && is_null($a['estado']) ){
            $titleCarousel      = $paises[$a['pais']];
            $showWhat           = 'second-lvl';
        }

        $pageLocation   = get_page_by_title( 'Acompanhantes em ' . $titleCarousel, OBJECT, 'post' );
        if( $pageLocation ){
            $pageId = $pageLocation->ID;
            $pageLocationLink = get_permalink( $pageId );
        }

        ob_start();
        require plugin_dir_path( __DIR__ ) . 'public/partials/carousel/tpl-carousel-default.php';
        return ob_get_clean();
    }
    
    /**
     * Lista de anúncios
     * 
     * Retorna uma lista com cards de anúncios
     * O usuário pode passar atributos correspondentes a taxonomias disponíveis ao cpt
     */
    public function listAdvertisement( $atts )
    {
        $a = shortcode_atts( 
            [
                'pais'      => 'br',
                'estado'    => null,
                'cidade'    => null,
                'qtd'       => 12,
			], 
            $atts
        );
        $query = $this->a2Query->advByPlanLevel($a['pais'], $a['estado'], $a['cidade'], $a['qtd']);
        
        ob_start();
        require plugin_dir_path( __DIR__ ) . 'public/partials/tpl-adv-list.php';
        return ob_get_clean();
    }

    /**
     * Componente de busca
     * 
     * Retorna um componente html de busca por cidades
     */
    public function searchComponent( $atts )
    {
        $a = shortcode_atts(
            [
                'tipo'  => 'cidade',
                'qtd'   => 5
            ],
            $atts
        );

        $terms = $this->a2Query->getCities();

        ob_start();
        require plugin_dir_path( __DIR__ ) . 'public/partials/tpl-search-component.php';
        
        return ob_get_clean();
    }

    /**
     * Componente de Filtro
     * Retorna um componente html com opções de filtros para anúncios
     * 
     */
    public function filterComponent( $atts )
    {
        $a = shortcode_atts(
            [
                'tipo'  => 'cidade',
            ],
            $atts
        );
        $cities     = $this->a2Query->getCities();
        $taxonomies = [
            'profile_genre',
            'profile_ethnicity',
            'profile_sign',
            'profile_specialties',
            'profile_services',
            'profile_place_of_service',
            'profile_languages',
            'profile_work_days',
            'profile_payment_methods',
            'profile_preference',
        ];
        $terms = [];
        foreach( $taxonomies as $tax ){
            $terms[$tax] = get_terms([
                'taxonomy'      => $tax,
                'orderby'       => 'name',
                'order'         => 'ASC',
                'hide_empty'    => false,
            ]);
        }

        ob_start();
        require plugin_dir_path( __DIR__ ) . 'public/partials/tpl-filter-component.php';
        
        return ob_get_clean();
    }

    /**
	 * Método retorna a bagde conforme o resultado da verificação de perfil
	 * ACTION
	 * @param int 	    $userId
	 * @return mixed    $badge
	 */
	public function getProfileCheckmark( $userId )
	{
		$key 	= '_verified_profile';
		$value  = get_user_meta( $userId, $key, true );
		$class 	= '';
		$classIcon = '';
		$sealText = '';
		
		switch($value){
			case 'under-analisys':
				$class = 'profile__seal--underAnalysis';
				$classIcon = 'bi-clock-history';
				$sealText = 'Perfil em Análise';
				break;
			case 'verified':
				$class = 'profile__seal--verified';
				$classIcon = 'bi-shield-check';
				$sealText = 'Perfil Verificado';
				break;
			case 'invalid':
				$class = 'profile__seal--invalid';
				$classIcon = 'bi-exclamation-diamond';
				$sealText = 'Perfil Reprovado';
				break;
			default:
				$value = 'reprovado';
		}
		
		$badge = '<div class="profile__stamps mb-2">
					<span class="profile__seal '. $class .' d-inline-flex align-items-center">
						<i class="bi '. $classIcon .' me-1"></i>
						<span class="">'. __($sealText, 'textdomain') .'</span>
					</span>	
				</div>';

		echo $badge;
	}

    public function restUploaderForm()
    {
        wp_enqueue_script( 'rest-uploader' );
        ob_start();
        ?>
        <div class="" style="margin: 150px auto; background: gray;">
            <h2><?php esc_html_e( 'Upload a file', 'rest-uploader' ); ?></h2>
            <form method="post">
                <p>
                    <label for="uploader-title">
                        <?php esc_html_e( 'Title', 'rest-uploader' ); ?>:
                    </label>
                    <input id="uploader-title">
    
                </p>
                <p>
                    <label for="uploader-caption">
                        <?php esc_html_e( 'Caption', 'rest-uploader' ); ?>:
                    </label>
                    <input id="uploader-caption">
    
                </p>
                <p>
                    <label for="uploader-file">
                        <?php esc_html_e( 'File', 'rest-uploader' ); ?>:
                    </label>
                    <input id="uploader-file" type="file">
                </p>
                <button id="uploader-send"><?php esc_html_e( 'Send', 'rest-uploader' ); ?></button>
            </form>
        </div>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * Shortcode página de cadastro
     * 
     */
    public function registerPage( $atts )
    {
        $a = shortcode_atts( 
            [
                'title'     => 'Cadastro',
                'subtitle'  => 'Cadastre-se GRATUITAMENTE de forma rápida e fácil!'
			], 
            $atts
        );

        ob_start();
        require plugin_dir_path( __DIR__ ) . '/templates/pages/register-form.php';
        return ob_get_clean();
    }

    /**
     * Shortcode cartão de anúncio ativo
     * Responsável por mostrar o tipo e a validade do anúncio ativo de uma acompanhante
     * 
     * @param array     $atts   Atributos do shortcode
     */
    public function cardAdvActiveInfo( $atts )
    {
        $a = shortcode_atts( 
            [
                'title'     => __('Anúncio Ativo', 'textdomain')
			], 
            $atts
        );

        $userID         = get_current_user_id();
        $activatedItems = $this->Adv->getActivatedItems($userID);

        ob_start();
        if( !empty($activatedItems) ){
            foreach( $activatedItems as $item ){
                $expDate    = explode( ' ', $this->Adv->getExpirationDate($item)); # String "25-08-2022 17:22:27" 
                $date       = str_replace( '-', '/', $expDate[0] );
                $hour       = $expDate[1];
                $advLvl     = get_post_meta( $item, '_plan_level', true );
                $advType    = '';
                $advIcon    = '';

                $icons  = [
                    'prata'     => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 64 64"><g id="_9554546_medal_silver_badge_achievement_reward_icon" data-name="9554546_medal_silver_badge_achievement_reward_icon" transform="translate(-27.8 -14.2)"><ellipse id="Elipse_62" data-name="Elipse 62" cx="32" cy="32" rx="32" ry="32" transform="translate(27.8 14.2)" fill="#ededed"/><circle id="Elipse_63" data-name="Elipse 63" cx="25.044" cy="25.044" r="25.044" transform="translate(34.756 21.156)" fill="#bcbcbc"/><path id="Caminho_299" data-name="Caminho 299" d="M60.852,31.206l4.119,8.468a1.567,1.567,0,0,0,.981.807l9.122,1.411a1.387,1.387,0,0,1,.785,2.319l-6.571,6.654a1.409,1.409,0,0,0-.392,1.21l1.569,9.376A1.326,1.326,0,0,1,68.5,62.862l-8.141-4.436a1.419,1.419,0,0,0-1.275,0l-8.239,4.436a1.349,1.349,0,0,1-1.962-1.411l1.569-9.376a1.409,1.409,0,0,0-.392-1.21l-6.571-6.654a1.349,1.349,0,0,1,.785-2.319L53.4,40.481a1.31,1.31,0,0,0,.981-.807L58.5,31.206A1.294,1.294,0,0,1,60.852,31.206Z" transform="translate(0.124 -0.336)" fill="#fff"/></g></svg>',
                    'ouro'      => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 64 64"><g id="_9554545_medal_gold_winner_badge_achievement_icon" data-name="9554545_medal_gold_winner_badge_achievement_icon" transform="translate(-27.8 -14.2)"><ellipse id="Elipse_60" data-name="Elipse 60" cx="32" cy="32" rx="32" ry="32" transform="translate(27.8 14.2)" fill="#ffc54d"/><circle id="Elipse_61" data-name="Elipse 61" cx="25.141" cy="25.141" r="25.141" transform="translate(34.659 21.059)" fill="#e8b04b"/><path id="Caminho_297" data-name="Caminho 297" d="M61.083,31.216,65.256,39.8a1.587,1.587,0,0,0,.994.817l9.24,1.43a1.406,1.406,0,0,1,.795,2.35l-6.657,6.743a1.428,1.428,0,0,0-.4,1.226l1.59,9.5a1.344,1.344,0,0,1-1.987,1.43l-8.246-4.5a1.437,1.437,0,0,0-1.292,0l-8.346,4.5a1.367,1.367,0,0,1-1.987-1.43l1.59-9.5a1.428,1.428,0,0,0-.4-1.226L43.5,44.4a1.367,1.367,0,0,1,.795-2.35l9.24-1.43a1.327,1.327,0,0,0,.994-.817L58.7,31.216A1.311,1.311,0,0,1,61.083,31.216Z" transform="translate(-0.091 -0.558)" fill="#fff"/></g></svg>',
                    'diamante'  => '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 64 64"><g id="Grupo_465" data-name="Grupo 465" transform="translate(-1061 -604)"><circle id="Elipse_60" data-name="Elipse 60" cx="32" cy="32" r="32" transform="translate(1061 604)" fill="#9bc9ff"/><circle id="Elipse_64" data-name="Elipse 64" cx="25.044" cy="25.044" r="25.044" transform="translate(1068.057 611.057)" fill="#1e81ce"/><g id="_9035684_diamond_sharp_icon" data-name="9035684_diamond_sharp_icon" transform="translate(1074.658 618.656)"><path id="Caminho_288" data-name="Caminho 288" d="M274.381,32H264l6.606,8.808Z" transform="translate(-245.23 -32)" fill="#fff"/><path id="Caminho_289" data-name="Caminho 289" d="M115.69,32l3.776,8.808L126.071,32Z" transform="translate(-108.471 -32)" fill="#fff"/><path id="Caminho_290" data-name="Caminho 290" d="M197.022,74.67,192,81.365h10.043Z" transform="translate(-178.836 -71.322)" fill="#fff"/><path id="Caminho_291" data-name="Caminho 291" d="M379.923,51.06l-3.663,8.548h8.767Z" transform="translate(-348.734 -49.565)" fill="#fff"/><path id="Caminho_292" data-name="Caminho 292" d="M28.182,51.06,23,59.608h8.846Z" transform="translate(-23 -49.565)" fill="#fff"/><path id="Caminho_293" data-name="Caminho 293" d="M33.626,192H24l17.481,22.6h.042Z" transform="translate(-23.922 -179.631)" fill="#fff"/><path id="Caminho_294" data-name="Caminho 294" d="M272.567,192l-7.9,22.6h.042L282.193,192Z" transform="translate(-245.9 -179.631)" fill="#fff"/><path id="Caminho_295" data-name="Caminho 295" d="M194.127,192H182.61l5.758,16.32Z" transform="translate(-170.183 -179.569)" fill="#fff"/></g></g></svg>'
                ];

                if( $advLvl == 1 ){
                    $advType    = 'prata';
                    $advIcon    = $icons[$advType];
                } else if( $advLvl == 2 ) {
                    $advType    = 'ouro';
                    $advIcon    = $icons[$advType];
                } else {
                    $advType    = 'diamante';
                    $advIcon    = $icons[$advType];
                }

                require plugin_dir_path( __DIR__ ) . 'public/partials/dashboard/tpl-cardAdvActiveInfo.php';
                
            }
        }

        return ob_get_clean();
    }

    /**
     * Shortcode slider de produtos/anúncios
     * Ele vai mostrar em um slider as opções de anúncios para compra
     * 
     * @param array     $atts   Atributos passados pelo shortcode
     */
    public function sliderProducts( $atts )
    {
        $a = shortcode_atts( 
            [
                'title'     => __('Anúncie agora!', 'textdomain')
			], 
            $atts
        );

        $args = array(
            'post_type'     => 'product',
            'post_status'   => 'publish',
            'orderby'       => 'name',
        );
        $products = new WP_Query( $args );
        
        $colors = [
            'diamond_dark'  => '#1E81CE',
            'diamond_light' => '#9BC9FF',
            'gold_dark'     => '#E8B04B',
            'gold_light'    => '#FFC54D',
            'silver_dark'   => '#BCBCBC',
            'silver_light'  => '#C9C9C9'
        ];

        $instructions = '<span class="text-muted text-center d-block mb-4">Ao clicar em comprar, você será redirecionado </br> para página de pagamento.</br> <b>Pagamento 100% seguro. Pague com PicPay.</b></span>';

        ob_start();
        if( $products->have_posts() ){
            echo '<div class="owl-carousel" id="plan-carousel">';
            while( $products->have_posts() ) {
                $products->the_post();
                $pID            = get_the_ID();
                $thumbnailUrl   = get_the_post_thumbnail_url( $pID );
                $title          = get_the_title();
                $content        = get_the_content();
                $product        = wc_get_product( $pID );
                $durations      = explode(',', $product->get_attribute('duracao'));
                $variations     = $product->get_available_variations();
                $prices         = array();
                if( !is_wp_error( $variations ) ){
                    foreach( $variations as $variation ){
                        $prices[$variation['attributes']['attribute_pa_duracao']] = [
                            'id'    => $variation['variation_id'],
                            'price' => $variation['display_price'],
                        ];
                    }
                }
                // Definição das cores
                switch($title){
                    case 'Anúncio Diamante':
                        $primaryColor   = $colors['diamond_dark'];
                        $secondaryColor = $colors['diamond_light'];
                        break;
                    case 'Anúncio Ouro':
                        $primaryColor   = $colors['gold_dark'];
                        $secondaryColor = $colors['gold_light'];
                        break;
                    default:
                        $primaryColor   = $colors['silver_dark'];
                        $secondaryColor = $colors['silver_light'];
                        break;
                }

                require plugin_dir_path( __DIR__ ) . 'public/partials/cards/tpl-card-product.php';
            }
            echo '</div><div id="plan-carousel-dots"></div>' . $instructions ;
        }
        wp_reset_postdata();
        
        return ob_get_clean();
    }

    /**
     * Shortcode que mostra cards com estatísticas do perfil
     * 
     * @param array     $atts   Atributos passados pelo shortcode
     */
    public function boxStatistics( $atts )
    {
        $a = shortcode_atts( 
            [
                'title'     => __('Estatísticas', 'textdomain')
			], 
            $atts
        );

        $userId = get_current_user_id();
        $pageId = $this->profileHelper->getPageIdByAuthor($userId);

        // Visualizações, contatos e cliques em fotos
        $metas = [
            'views'     => '_a2_statistic_views',
            'contacts'  => '_a2_statistic_contacts',
            'clicks'    => '_a2_statistic_clicks',
        ];
        $postFix = '/mês';
        ob_start();
        foreach( $metas as $name => $key ){

            switch($name){
                case 'views':
                    $title      = 'Visualizações';
                    $icon       = '<i class="bi bi-emoji-heart-eyes cardDashboard__icon"></i>';
                    $pageLink   = $this->profileHelper->getProfileLinkById($pageId);
                    break;
                case 'contacts':
                    $title  = 'Contatos';
                    $icon   = '<i class="bi bi-chat-right-heart cardDashboard__icon"></i>';
                    break;
                default: # clicks
                    $title  = 'Cliques em fotos';
                    $icon   = '<i class="bi bi-images cardDashboard__icon"></i>';
                    break;
            }
            $value  = get_post_meta( $pageId, $key, true );

            require plugin_dir_path( __DIR__ ) . 'public/partials/dashboard/tpl-cardStatistic.php';
        }

        return ob_get_clean();
    }

    /**
     * Shortcode responsável por mostrar os cards das modelos
     * Ele deve ser sempre chamado dentro de um loop
     * 
     * @param array     $atts   Atributos
     */
    public function advCard( $atts )
    {
        $a = shortcode_atts( 
            [
                'title' => ''
			], 
            $atts
        );
        global $post;
        $postId     = $post->ID;
        $authorId   = get_the_author_meta('ID');
        $dataProfile    = [];
        foreach( $this->profileMetas as $key ){
            $dataProfile[$key] = get_post_meta( $postId, $key, true );
        }

        $title          = get_the_title( $postId );
        $content        = get_the_content( $postId );
        $thumbUrl       = get_the_post_thumbnail_url( $postId );
        $age            = $this->profileHelper->getAgeById($postId); # Criar classe e método
        $pageProfileId  = $this->profileHelper->getPageIdByAuthor($authorId);
        $gallery        = $this->profileHelper->getGalleryById($pageProfileId);
        $pageProfileUrl = $this->profileHelper->getProfileLinkById($pageProfileId );
        $genre          = $this->profileHelper->getGenreById($pageProfileId);
        $isVerified     = 'yes'; # Coletar verificação do perfil
        $havePlace      = 'yes'; # Adicionar opção na edição do perfil
        
        // Formatando mensagem de contato
        $baseWaApi      = '';
        if( wp_is_mobile() ){
            $baseWaApi      = 'https://api.whatsapp.com/send?phone=';
        } else {
            $baseWaApi      = 'https://web.whatsapp.com/send?phone=';
        }
        $countryCode    = '55';
        $waNumber       = $countryCode . str_replace( ['(', ')', '-', ' '], '', $dataProfile['_profile_whatsapp'] );
        $message        = urlencode('Olá, ' . $title . '! Encontrei seu anúncio no www.acompanhantesa2.com. *Podemos conversar?*');
        $contactLink    = $baseWaApi . $waNumber . '&text=' . $message;

        # 0=silver; 1=gold; 2=diamond;
        switch($dataProfile['_plan_level']){
            case 0:
                require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/cards/adv-card-silver.php';
                break;
            case 1:
                require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/cards/adv-card-gold.php';
                break;
            case 2:
                require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/cards/adv-card-diamond.php';
                break;
            default:
                require plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/cards/adv-card-default.php';
                break;
        }
        
    }

    /**
     * Perfil de modelo
     * @template single-a2_scort
     * 
     */
    public function scortProfileContent($atts)
    {
        $a = shortcode_atts( 
            [
                'title' => ''
			], 
            $atts
        );
        global $post;
        $post_id                = $post->ID;
        $userId					= get_the_author_ID();

		# Coletando Informações - Transformar essa coleta em um método publico na classe A2_Profile
		$profileData 			= array();
		$profileData['name'] 	= get_the_title( $post_id );
		$metaKeys 			= array(
			'_profile_whatsapp',
			'_profile_birthdate',
			'_profile_age',
			'_profile_description',
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
			'_profile_office_hour',
			'_profile_cover',
			'_profile_photo',
			'_profile_gallery',
			'_profile_verified_media',
		);
		
		# Meta posts
		foreach( $metaKeys as $key ){
			if( $key == '_profile_photo' ){
				$profileData[$key] = get_the_post_thumbnail_url( $post_id );
			} else {
				$profileData[$key] = get_post_meta( $post_id, $key, true );
			}
		}

		# Taxonomias
		$taxonomies = array(
			'_profile_ethnicity' 		=> 'profile_ethnicity',
			'_profile_genre'			=> 'profile_genre',
			'_profile_sign'				=> 'profile_sign',
			'_profile_preference'		=> 'profile_preference',
			'_profile_services'			=> 'profile_services',
			'_profile_place_of_service'	=> 'profile_place_of_service',
			'_profile_work_days'		=> 'profile_work_days',
			'_profile_payment_methods'	=> 'profile_payment_methods',
			'_profile_languages'		=> 'profile_languages',
			'_profile_specialties'		=> 'profile_specialties',
			// '_profile_country'			=> 'profile_localization',
			// '_profile_state'				=> 'profile_localization',
			// '_profile_city'				=> 'profile_localization',
			// '_profile_district'			=> 'profile_localization',
		);
		foreach( $taxonomies as $key => $taxonomy ){
			$profileData[$key] = get_the_terms( $post_id, $taxonomy );
		}

		// Formatando mensagem de contato
		$baseWaApi      = '';
		if( wp_is_mobile() ){
			$baseWaApi      = 'https://api.whatsapp.com/send?phone=';
		} else {
			$baseWaApi      = 'https://web.whatsapp.com/send?phone=';
		}
		$countryCode    = '55';
		$waNumber       = $countryCode . str_replace( ['(', ')', '-', ' '], '', $profileData['_profile_whatsapp'] );
		$message        = urlencode('Olá, ' . $profileData['name'] . '! Encontrei seu anúncio no www.acompanhantesa2.com. *Podemos conversar?*');
		$contactLink    = $baseWaApi . $waNumber . '&text=' .$message;
		$telLink 		= 'tel:+' . $waNumber; 

        ob_start();
		?>
		<div class="profileContent">
			<div class="profileCover">
				<div class="profileCover__attachment" style="background-image: url('<?php echo wp_get_attachment_image_url( $profileData['_profile_cover'], 'big' ); ?>')"></div>
			</div>

			<!-- HTML DA PÀGINA AQUI -->
			<div class="profile row align-items-center">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<div class="profile__adornment profile__adornment--n1 rounded-circle shadow">
						<div class="profile__thumb rounded-circle" style="background-image: url('<?php echo $profileData['_profile_photo']; ?>');"></div>
					</div>
				</div>
				<!-- /end profile__thumb -->

				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<?php #do_action( 'profileCheckmark', $userId ); ?>
					<h2 class="text-center fw-bold m-2"><?php echo $profileData['name']; ?></h2>
					<p class="text-center"><?php _e(get_the_content(), 'a2'); ?></p>
					
					<div class="row mt-4">
						<div class="col-12 mb-5">
							<div class="profile__rating mb-2 d-inline-flex align-items-center d-none">
								<div class="d-flex justify-content-start align-items-center">
									<i class="profile__rating--color me-1 fas fa-star"></i>
									<i class="profile__rating--color me-1 fas fa-star"></i>
									<i class="profile__rating--color me-1 fas fa-star"></i>
									<i class="profile__rating--color me-1 fas fa-star-half-alt"></i>
									<i class="profile__rating--color me-1 far fa-star"></i>
								</div>
								<span class="c-white ms-2"><?php echo __('Ver Avaliações', 'textdomain'); ?></span>
							</div>

							<div class="mb-2">
								<ul class="list-group list-group-horizontal text-center">
									<li class="list-group-item list-group-item-action bg-transparent text-secondary fw-medium">
										<i class="bi bi-gender-female"></i>
										<span class=""><?php _e('Mulher', 'a2'); ?></span>
									</li>
									<li class="list-group-item list-group-item-action bg-transparent text-secondary fw-medium">
										<i class="d-inline-flex justify-content-center">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-cake2-fill" viewBox="0 0 16 16">
												<path d="m2.899.804.595-.792.598.79A.747.747 0 0 1 4 1.806v4.886q-.532-.09-1-.201V1.813a.747.747 0 0 1-.1-1.01ZM13 1.806v4.685a15 15 0 0 1-1 .201v-4.88a.747.747 0 0 1-.1-1.007l.595-.792.598.79A.746.746 0 0 1 13 1.806m-3 0a.746.746 0 0 0 .092-1.004l-.598-.79-.595.792A.747.747 0 0 0 9 1.813v5.17q.512-.02 1-.055zm-3 0v5.176q-.512-.018-1-.054V1.813a.747.747 0 0 1-.1-1.01l.595-.79.598.789A.747.747 0 0 1 7 1.806"/>
												<path d="M4.5 6.988V4.226a23 23 0 0 1 1-.114V7.16c0 .131.101.24.232.25l.231.017q.498.037 1.02.055l.258.01a.25.25 0 0 0 .26-.25V4.003a29 29 0 0 1 1 0V7.24a.25.25 0 0 0 .258.25l.259-.009q.52-.018 1.019-.055l.231-.017a.25.25 0 0 0 .232-.25V4.112q.518.047 1 .114v2.762a.25.25 0 0 0 .292.246l.291-.049q.547-.091 1.033-.208l.192-.046a.25.25 0 0 0 .192-.243V4.621c.672.184 1.251.409 1.677.678.415.261.823.655.823 1.2V13.5c0 .546-.408.94-.823 1.201-.44.278-1.043.51-1.745.696-1.41.376-3.33.603-5.432.603s-4.022-.227-5.432-.603c-.702-.187-1.305-.418-1.745-.696C.408 14.44 0 14.046 0 13.5v-7c0-.546.408-.94.823-1.201.426-.269 1.005-.494 1.677-.678v2.067c0 .116.08.216.192.243l.192.046q.486.116 1.033.208l.292.05a.25.25 0 0 0 .291-.247M1 8.82v1.659a1.935 1.935 0 0 0 2.298.43.935.935 0 0 1 1.08.175l.348.349a2 2 0 0 0 2.615.185l.059-.044a1 1 0 0 1 1.2 0l.06.044a2 2 0 0 0 2.613-.185l.348-.348a.94.94 0 0 1 1.082-.175c.781.39 1.718.208 2.297-.426V8.833l-.68.907a.94.94 0 0 1-1.17.276 1.94 1.94 0 0 0-2.236.363l-.348.348a1 1 0 0 1-1.307.092l-.06-.044a2 2 0 0 0-2.399 0l-.06.044a1 1 0 0 1-1.306-.092l-.35-.35a1.935 1.935 0 0 0-2.233-.362.935.935 0 0 1-1.168-.277z"/>
											</svg>
										</i>
										<span class=""><?php _e( '24 Anos'); ?></span>
									</li>
									<li class="list-group-item list-group-item-action bg-transparent text-secondary fw-medium">
										<i class="d-inline-flex justify-content-center">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-house-check-fill" viewBox="0 0 16 16">
												<path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
												<path d="m8 3.293 4.712 4.712A4.5 4.5 0 0 0 8.758 15H3.5A1.5 1.5 0 0 1 2 13.5V9.293z"/>
												<path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.707l.547.547 1.17-1.951a.5.5 0 1 1 .858.514"/>
											</svg>
										</i>
										<span class=""><?php _e( 'Com local'); ?></span>
									</li>
									<li class="list-group-item list-group-item-action bg-transparent text-secondary fw-medium">
										<i class="bi bi-geo-alt-fill"></i>
										<span class=""><?php _e('Jardim Shangri-la A'); ?></span>
									</li>
									<li class="list-group-item list-group-item-action bg-transparent text-secondary fw-medium">
										<i class="bi bi-cash-stack"></i>
										<span class=""><?php _e( 'R$' . $profileData['_profile_cache_hour'] . ' /h'); ?></span>
									</li>
								</ul>
							</div>
						</div>

						<div class="col-12">
							<div class="d-flex align-items-center">
								<button class="btn btn-primary m-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#offCanvasContact" aria-controls="offCanvasContact">
									<i class="bi bi-arrow-through-heart-fill"></i> <?php _e('Conversar com ' . $profileData['name'], 'textdomain'); ?>
								</button>

								<div class="offcanvas offcanvas-bottom bg-transparent" tabindex="-1" id="offCanvasContact" aria-labelledby="offCanvasContactLabel">
									<div class="offcanvas-header">
										<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
									</div>
									<div class="offcanvas-body bg-dark">
										<div class="container text-center mt-3">
											<h5 class=""><?php _e('Converse com ' . $profileData['name'], 'textdomain'); ?></h5>
											<small class=""><?php _e('Ao entrar em contato seja educado(a), amigável e carinhoso(a)!', 'textdomain'); ?></small>
											<div class="row mt-3">
												<div class="col-4">
													<button type="button" class="btn btn-outline-secondary w-100 border-0"><i class="bi bi-clipboard-check me-1"></i><?php echo $profileData['_profile_whatsapp'] ?></button>
												</div>
												<div class="col-4">
													<button class="btn btn-primary w-100 border-0" data-href="<?php echo $telLink; ?>"><i class="bi bi-telephone-forward me-1"></i><?php _e( 'Ligar para ' . $profileData['name'], 'textdomain' ); ?></button>
												</div>
												<div class="col-4">
													<button class="btn btn-primary w-100 border-0" data-href="<?php echo $contactLink; ?>"><i class="bi bi-whatsapp me-1"></i><?php _e( 'Chamar ' . $profileData['name'] , ' no Whatsapp', 'textdomain' ); ?></button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /end .profile -->
			
			<div class="d-flex row profileActionBar d-lg-none d-xl-none d-xxl-none" style="margin: auto">
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					<li class="nav-item col-6" role="presentation">
						<button class="profileActionBar__button nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true"><i class="bi bi-person-lines-fill me-1"></i><?php _e('Perfil', 'textdomain'); ?></button>
					</li>
					<li class="nav-item col-6" role="presentation">
						<button class="profileActionBar__button nav-link" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery" type="button" role="tab" aria-controls="gallery" aria-selected="false"><i class="bi bi-collection-fill me-1"></i><?php _e('Galeria', 'textdomain'); ?></button>
					</li>
				</ul>
				<div class="tab-content p-0" id="myTabContent">
					<div class="tab-pane profile-tab fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
						<div class="profile-description mt-3 mb-5">
							<div class="card profileCard">
								<div class="card-body" style="color: #fff;">
									<h3 class="card-title fw-bold"><?php _e( 'Sobre mim', 'textdomain' ); ?></h3>
									<p class="card-text"><?php echo get_the_content(); ?></p>
								</div>
							</div>
						</div>
						<!-- /end .profile-description -->
						
						<div class="card card-details mb-5">
							<div class="row g-0">
								<div class="col-12">
									<div class="card-body">
										<h3 class="card-title"><i class="bi bi-person-lines-fill me-1"></i><b>Detalhes</b></h3>
										<ul class="list-group list-group-flush">
											<li class="list-group-item"><?php _e( 'Sou <b>' . $profileData['_profile_genre'] . '</b>', 'textdomain'); ?></li>
											<li class="list-group-item"><?php _e( 'Tenho <b>' . $profileData['_profile_age'] . '</b> anos', 'textdomain'); ?></li>
											<li class="list-group-item"><?php _e( '<b>'. $profileData['_profile_height'] .'</b> de Altura', 'textdomain'); ?></li>
											<li class="list-group-item"><?php _e( 'Com <b>'. $profileData['_profile_weight'] .'kg</b>', 'textdomain') ?></li>
											<li class="list-group-item"><?php _e( 'Atendo <b>'. $profileData['_profile_preference'] .'</b>') ?></li>
										</ul>												
									</div>
								</div>
							</div>
						</div>
						<!-- /end .card-details -->
						<div class="accordion accordion-flush profileOptions mb-5" id="accordionFlushExample">
							<div class="accordion-item accordion-priceList profileOptions__body">
								<h2 class="accordion-header" id="flush-headingPriceList">
								<button class="accordion-button profileOptions__button collapsed position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapsePriceList" aria-expanded="false" aria-controls="flush-collapsePriceList">
									<i class="bi bi-bookmark-star-fill me-2"></i>
									<b><?php _e( 'Valores', 'textdomain' );?></b>
									
								</button>
								</h2>
								<div id="flush-collapsePriceList" class="accordion-collapse collapse show" aria-labelledby="flush-headingPriceList" data-bs-parent="#accordionFlushExample">
									<div class="accordion-body">
									<ul class="profilePriceList">
										<li class="profilePriceList__item d-flex">
											<p class="d-none"><?php echo $profileData['_profile_whatsapp']; ?></p>
											<a href="<?php echo $contactLink; ?>" target="_blank" rel="nofollow" class="profilePriceList__button btn btn-primary d-flex align-items-center">
												<i class="fab fa-whatsapp profilePriceList__icon d-flex align-item-center justify-content-center"></i>
												<span class="ms-2 d-flex flex-column">
													<span class="profilePriceList__time fs-6"><?php _e( '30 Minutos', 'textdomain'); ?></span>
													<span class="profilePriceList__price fs-4 fw-bold">R$<?php echo $profileData['_profile_cache_half_an_hour'] ?></span>
												</span>
											</a>
										</li>
										<li class="profilePriceList__item d-flex">
											<a href="<?php echo $contactLink; ?>" target="_blank" rel="nofollow" class="profilePriceList__button btn btn-primary d-flex align-items-center">
												<i class="fab fa-whatsapp profilePriceList__icon d-flex align-item-center justify-content-center"></i>
												<span class="ms-2 d-flex flex-column">
													<span class="profilePriceList__time fs-6"><?php _e( '1 Hora', 'textdomain'); ?></span>
													<span class="profilePriceList__price fs-4 fw-bold">R$<?php echo $profileData['_profile_cache_hour'] ?></span>
												</span>
											</a>
										</li>
										<li class="profilePriceList__item d-flex">
											<a href="<?php echo $contactLink; ?>" target="_blank" rel="nofollow" class="profilePriceList__button btn btn-primary d-flex align-items-center">
												<i class="fab fa-whatsapp profilePriceList__icon d-flex align-item-center justify-content-center"></i>
												<span class="ms-2 d-flex flex-column">
													<span class="profilePriceList__time fs-6"><?php _e( 'Pernoite', 'textdomain'); ?></span>
													<span class="profilePriceList__price fs-4 fw-bold">R$<?php echo $profileData['_profile_cache_overnight_stay'] ?></span>
												</span>
											</a>
										</li>
									</ul>
									</div>
								</div>
							</div>
							<!-- /end .accordion-priceList -->
							<div class="accordion-item accordion-services profileOptions__body">
								<h2 class="accordion-header" id="flush-headingServices">
								<button class="accordion-button profileOptions__button collapsed position-relative" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseServices" aria-expanded="false" aria-controls="flush-collapseServices">
									<i class="bi bi-bookmark-star-fill me-2"></i>
									<b><?php _e( 'Serviços', 'textdomain' );?></b>
									<span class="badge bg-danger text-white fw-normal ms-2"><?php echo ( !empty($profileData['_profile_services']) ? count($profileData['_profile_services']) : '0' ); ?></span>
								</button>
								</h2>
								<div id="flush-collapseServices" class="accordion-collapse collapse" aria-labelledby="flush-headingServices" data-bs-parent="#accordionFlushExample">
									<div class="accordion-body">
										<ul class="profileList">
											<?php
											if( !empty($profileData['_profile_services']) ){
												foreach( $profileData['_profile_services'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											}
											?>
										</ul>
									</div>
								</div>
							</div>
							<!-- /end .accordion-services -->
							<div class="accordion-item accordion-specialties profileOptions__body">
								<h2 class="accordion-header" id="flush-headingSpecialties">
								<button class="accordion-button profileOptions__button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSpecialties" aria-expanded="false" aria-controls="flush-collapseSpecialties">
									<i class="bi bi-bookmark-star-fill me-2"></i>
									<b><?php _e( 'Especialidades', 'textdomain' );?></b>
									<span class="badge bg-danger text-white fw-normal ms-2"><?php echo ( !empty($profileData['_profile_specialties']) ? count($profileData['_profile_specialties']) : '0' ); ?></span>
								</button>
								</h2>
								<div id="flush-collapseSpecialties" class="accordion-collapse collapse" aria-labelledby="flush-headingSpecialties" data-bs-parent="#accordionFlushExample">
									<div class="accordion-body">
										<ul class="profileList">
											<?php 
											if( is_array( $profileData['_profile_specialties']) && !empty( $profileData['_profile_specialties'] ) ){
												foreach( $profileData['_profile_specialties'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											} else {
												echo '<li class="">
														<span class="c-body">'. __('Este perfil não possui especilidades.') .'</span>
													</li>';
											}
											?>
										</ul>
									</div>
								</div>
							</div>
							<!-- /end .accordion-specialties -->
							<div class="accordion-item accordion-locations profileOptions__body">
								<h2 class="accordion-header" id="flush-headingLocations">
								<button class="accordion-button profileOptions__button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseLocations" aria-expanded="false" aria-controls="flush-collapseLocations">
									<i class="bi bi-bookmark-star-fill me-2"></i>
									<b><?php _e( 'Locais', 'textdomain' );?></b>
									<span class="badge bg-danger text-white fw-normal ms-2"><?php echo count($profileData['_profile_place_of_service']); ?></span>
								</button>
								</h2>
								<div id="flush-collapseLocations" class="accordion-collapse collapse" aria-labelledby="flush-headingLocations" data-bs-parent="#accordionFlushExample">
									<div class="accordion-body">
										<ul class="profileList">
											<?php 
											if( !empty($profileData['_profile_place_of_service']) ){
												foreach( $profileData['_profile_place_of_service'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											}
											?>
										</ul>
									</div>
								</div>
							</div>
							<!-- /end .accordion-locations -->
							<div class="accordion-item accordion-worksOfDay profileOptions__body">
								<h2 class="accordion-header" id="flush-headingDaysOfWork">
								<button class="accordion-button profileOptions__button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseDaysOfWork" aria-expanded="false" aria-controls="flush-collapseDaysOfWork">
									<i class="bi bi-bookmark-star-fill me-2"></i>
									<b><?php _e( 'Dias de trabalho', 'textdomain' );?></b>
									<span class="badge bg-danger text-white fw-normal ms-2"><?php echo ( !empty($profileData['_profile_work_days']) ? count($profileData['_profile_work_days']) : '0' ); ?></span>
								</button>
								</h2>
								<div id="flush-collapseDaysOfWork" class="accordion-collapse collapse" aria-labelledby="flush-headingDaysOfWork" data-bs-parent="#accordionFlushExample">
									<div class="accordion-body">
										<ul class="profileList">
											<?php 
											if( !empty($profileData['_profile_work_days']) ){
												foreach( $profileData['_profile_work_days'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											}
											?>
										</ul>
									</div>
								</div>
							</div>
							<!-- /end .accordion-worksOfDay -->
							<div class="accordion-item accordion-languages profileOptions__body">
								<h2 class="accordion-header" id="flush-headingLanguages">
									<button class="accordion-button profileOptions__button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseLanguages" aria-expanded="false" aria-controls="flush-collapseLanguages">
										<i class="bi bi-bookmark-star-fill me-2"></i>
										<b><?php _e( 'Idiomas', 'textdomain' );?></b>
										<span class="badge bg-danger text-white fw-normal ms-2"><?php echo ( !empty($profileData['_profile_languages']) ? count($profileData['_profile_languages']) : '0' ); ?></span>
									</button>
								</h2>
								<div id="flush-collapseLanguages" class="accordion-collapse collapse" aria-labelledby="flush-headingLanguages" data-bs-parent="#accordionFlushExample">
									<div class="accordion-body">
										<ul class="profileList">
											<?php
											if( !empty( $profileData['_profile_languages'] )){
												foreach( $profileData['_profile_languages'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											}
											?>
										</ul>
									</div>
								</div>
							</div>
							<!-- /end .accordion-languages -->
						</div>
						<!-- /end .accordion -->

						<div class="profileWrapper tab-rating mb-5 col-12 d-none">
							<nav>
								<div class="nav nav-tabs" id="nav-tab" role="tablist">
									<button class="nav-link active" id="rating-tab" data-bs-toggle="tab" data-bs-target="#rating" type="button" role="tab" aria-controls="rating" aria-selected="true"><?php _e( 'Avaliações', 'textdomain' ); ?></button>
									<button class="nav-link" id="form-comment-tab" data-bs-toggle="tab" data-bs-target="#form-comment" type="button" role="tab" aria-controls="form-comment" aria-selected="false"><?php _e( 'Faça uma avaliação', 'textdomain' ); ?></button>
								</div>
							</nav>
						
							<div class="tab-content">
								<div class="tab-pane fade show active" id="rating" role="tabpanel" aria-labelledby="rating-tab">
									<div class="row comment">
										<div class="comment-metadata col-12">
											<p class="c-white"><span class="author-name">Kauan Silva</span>/ 02 de Fevereiro, 2022</p>
										</div>
										<div class="comment-content col-12">
											<p class="c-body">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam vel leo a nibh mollis fermentum.</p>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="form-comment" role="tabpanel" aria-labelledby="form-comment-tab">
									<div class="rating d-flex">
										<i class="fas fa-star"></i>
										<i class="fas fa-star"></i>
										<i class="fas fa-star"></i>
										<i class="fas fa-star"></i>
										<i class="fas fa-star"></i>
									</div>
									<form class="col-12">
										<div class="row">
											<div class="input-field col-12">
												<input id="user_name" type="text" class="validate">
												<label for="user_name">Nome/Apelido</label>
											</div>
											<div class="input-field col-12">
												<textarea id="user_feedback" class="materialize-textarea"></textarea>
												<label for="user_feedback">Comentário</label>
											</div>
										</div>
										<button class="waves-effect waves-light btn-large">Enviar</button>
									</form>
								</div>
							</div>
						</div>
						<!-- /end .tab-rating -->
					</div>
					<!-- /end .profile-tab -->

					<div class="tab-pane profile-gallery fade mt-3 mb-5" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
						<div class="col-12 mb-5">
							<!-- ### start of the gallery definition ### -->								
							<div id="mobileGallery" data-nanogallery2 = '{
								"thumbnailHeight":  auto,
								"thumbnailWidth":   auto,
								"itemsBaseURL":     ""}'>
								<?php 
									$gallery = $profileData['_profile_gallery']	;
									$galleryItems = '';
									if( !empty( $gallery ) ){
										foreach( $gallery as $item ){
											$imgOriginal 		= wp_get_attachment_url( $item );
											list($imgW, $imgH) 	= getimagesize($imgOriginal);
											
											$icon 	= false;
											$size 	= '';
											if( $imgW > $imgH ){
												$size = 'o-hr';
											} else {
												$size = 'o-vr';
											}
											$img 	= wp_get_attachment_image_url( $item, $size, $icon );
											$imgUrl	= $img;

											$galleryItems .= '<a href="'. $imgUrl .'"   data-ngThumb="'. $imgUrl .'" ><i class="bi bi-hand-thumbs-up"></i></a>';
										}
									}
									echo $galleryItems;
								?>
							</div>
							<!-- ### end of the gallery definition ### -->
						</div>
					</div>
					<!-- /end .profile-gallery -->
				</div>
			</div>
			<!-- /end Mobile Content -->
			
			<div class="row d-none d-lg-flex d-xl-flex d-xxl-flex" style="margin-top: -50px;">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<div class="row">
						<div class="profileWrapper col-12 d-none">
							<div class="profileWrapper__box bordered">
								<div class="row">
									<div class="aboutMe col-6">
										<h5 class="c-white"><?php _e( 'Sobre mim', 'textdomain' ); ?></h5>
										<p class="c-body"><?php echo get_the_content(); ?></p>
									</div>

									<div class="socialNetwork col-6">
										<h5 class="c-white mb-3"><?php _e('Redes sociais', 'textdomain'); ?></h5>
										<ul class="profileSocialNetwork__list d-flex">
											<?php
											$socialNetworks = ['_profile_instagram', '_profile_tiktok', '_profile_onlyfans'];
											foreach( $socialNetworks as $item ):
												if( strlen($profileData[$item]) > 1 ):
													$icon = '';
													switch( $item ){
														case '_profile_instagram': 
															$icon = 'fab fa-instagram';
															break;
														case '_profile_tiktok':
															$icon = 'fab fa-tiktok';
															break;
														case '_profile_onlyfans':
															$icon = 'fas fa-link';
															break;
														default:
															$icon = '';
															break;
													}
													?>											
													<li class="profileSocialNetwork__item d-flex justify-content-center align-items-center">
														<a href="<?php echo $profileData[$item]; ?>" target="_blank" class="profileSocialNetwork__link c-white"><i class="<?php echo $icon ?>"></i></a>
													</li>
													<?php
												endif;
											endforeach;
											?>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<!-- /End About -->

						<div class="profileWrapper col-12">
							<div class="profileWrapper__box bordered">
								<div class="row">
									<div class="col-8">
										<h5 class="text-uppercase text-light mb-3"><?php _e( 'Sobre mim', 'textdomain' ); ?></h5>
										<hr class="border">
										<p class="">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ultrices felis sit amet massa convallis ultrices. Donec ut ipsum consectetur, vestibulum diam id, elementum nisl. Ut sed nunc nec eros lobortis mattis et at metus. Donec pharetra nunc ultrices, blandit dolor facilisis, cursus sapien. Vivamus lorem lacus, pharetra quis convallis quis, suscipit vel sem. Praesent ultricies arcu quis est porta, in sollicitudin sem pharetra. Ut fringilla convallis ante id euismod.</p>
									</div>
									<div class="col-4">
										<h5 class="text-uppercase text-light mb-3"><?php _e('Detalhes', 'textdomain'); ?></h5>
										<hr class="">
										<ul class="list-group list-group-flush">
											<li class="list-group-item bg-transparent text-light">
												<span class="fw-bold"><?php _e('Genêro:', 'a2'); ?></span>
												<span class="ms-1 text-italic"><?php _e('Mulher', 'a2'); ?></span>
											</li>
											<li class="list-group-item  bg-transparent text-light">
												<span class="fw-bold"><?php _e('Etnia:', 'a2'); ?></span>
												<span class="ms-1 text-italic"><?php _e($profileData['_profile_ethnicity'][0]->name, 'a2'); ?></span>
											</li>
											<li class="list-group-item  bg-transparent text-light">
												<span class="fw-bold"><?php _e('Altura:', 'a2'); ?></span>
												<span class="ms-1 text-italic"><?php _e($profileData['_profile_height'] . ' m', 'a2'); ?></span>
											</li>
											<li class="list-group-item  bg-transparent text-light">
												<span class="fw-bold"><?php _e('Peso:', 'a2'); ?></span>
												<span class="ms-1 text-italic"><?php _e($profileData['_profile_weight'] . ' KG', 'a2'); ?></span>
											</li>
											<li class="list-group-item  bg-transparent text-light">
												<span class="fw-bold"><?php _e('Busto:', 'a2'); ?></span>
												<span class=""><?php _e($profileData['_profile_bust_size'] . ' cm', 'a2'); ?></span>
											</li>
										<ul>
									</div>
								</div>
							</div>
						</div>
						<!-- /End Details-->

						<div class="profileWrapper col-12">
							<div class="profileWrapper__box bordered">
								<div class="col-12 mb-5">
									<h5 class="text-uppercase text-light mb-3"><?php _e('Fotos & Vídeos', 'textdomain'); ?></h5>
									<hr class="">
									<!-- ### start of the gallery definition ### -->								
									<div id="desktopGallery" data-nanogallery2 = '{"thumbnailHeight": auto, "thumbnailWidth": auto, "itemsBaseURL": ""}'>
										<?php echo $galleryItems; ?>
									</div>
								</div>
							</div>
						</div>
						<!-- /End .profileGallery -->

						<div class="profileWrapper col-12">
							<div class="profileWrapper__box bordered">
								<div class="row">
									<div class="col-4">
										<h5 class="text-uppercase text-light mb-3"><?php _e( 'Serviços', 'textdomain' ); ?></h5>
										<hr class="">
										<ul class="profileList">
											<?php 
											if( !empty($profileData['_profile_services']) ){
												foreach( $profileData['_profile_services'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											}
											?>
										</ul>
									</div>
									<div class="col-4">
										<h5 class="text-uppercase text-light mb-3"><?php _e( 'Especilidades', 'textdomain' ); ?></h5>
										<hr class="">
										<ul class="profileList">
											<?php 
											if( is_array( $profileData['_profile_specialties']) && !empty( $profileData['_profile_specialties'] ) ){
												foreach( $profileData['_profile_specialties'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											} else {
												echo '<li class="">
														<span class="c-body">'. __('Este perfil não possui especilidades.') .'</span>
													</li>';
											}
											?>
										</ul>
									</div>
									<div class="col-4">
										<h5 class="text-uppercase text-light mb-3"><?php _e( 'Idiomas', 'textdomain' ); ?></h5>
										<hr class="">
										<ul class="profileList">
											<?php 
											if( is_array( $profileData['_profile_languages']) && !empty( $profileData['_profile_languages'] ) ){
												foreach( $profileData['_profile_languages'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											} else {
												echo '<li class="">
														<span class="c-body">'. __('Este perfil não possui idiomas.') .'</span>
													</li>';
											}
											?>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<!-- /End Services -->

						<div class="profileWrapper col-12">
							<div class="profileWrapper__box bordered">
								<div class="row">
									<div class="col-4">
										<h5 class="text-uppercase text-light mb-3"><?php _e( 'Atendimento', 'textdomain'); ?></h5>
										<hr class="">
										<ul class="profileList">
											<?php 
											if( !empty($profileData['_profile_place_of_service']) ){
												foreach( $profileData['_profile_place_of_service'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											}
											?>
										</ul>
									</div>
									<div class="col-4">
										<h5 class="text-uppercase text-light mb-3"><?php _e( 'Dias de trabalho', 'textdomain'); ?></h5>
										<hr class="">
										<ul class="profileList">
											<?php
											if( !empty($profileData['_profile_work_days']) ){
												foreach( $profileData['_profile_work_days'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											}
											?>
										</ul>
									</div>
									<div class="col-4">
										<h5 class="text-uppercase text-light mb-3"><?php _e( 'Valores', 'textdomain' ); ?></h5>
										<hr>
										<ul class="profilePriceList">
											<li class="profilePriceList__item d-flex">
												<span class="profilePriceList__time c-body"><i class="bi bi-hourglass-split me-1"></i><?php _e( '30 Minutos', 'textdomain'); ?></span>
												<span class="profilePriceList__price c-white">R$<?php echo $profileData['_profile_cache_half_an_hour'] ?></span>
													<a href="<?php echo $contactLink; ?>" target="_blank" rel="nofollow" class="profilePriceList__button btn btn-primary btn-sm">
													<span><?php _e('Whatsapp', 'textdomain'); ?> <i class="fab fa-whatsapp"></i></span>
												</a>
											</li>
											<li class="profilePriceList__item d-flex">
												<span class="profilePriceList__time c-body"><i class="bi bi-hourglass-split me-1"></i><?php _e( '1 Hora', 'textdomain'); ?></span>
												<span class="profilePriceList__price c-white">R$<?php echo $profileData['_profile_cache_hour'] ?></span>
													<a href="<?php echo $contactLink; ?>" target="_blank" rel="nofollow" class="profilePriceList__button btn btn-primary btn-sm">
													<span><?php _e('Whatsapp', 'textdomain'); ?> <i class="fab fa-whatsapp"></i></span>
												</a>
											</li>
											<li class="profilePriceList__item d-flex">
												<span class="profilePriceList__time c-body"><i class="bi bi-calendar2-heart-fill me-1"></i><?php _e( 'Pernoite', 'textdomain'); ?></span>
												<span class="profilePriceList__price c-white">R$<?php echo $profileData['_profile_cache_overnight_stay'] ?></span>
													<a href="<?php echo $contactLink; ?>" target="_blank" rel="nofollow" class="profilePriceList__button btn btn-primary btn-sm">
													<span><?php _e('Whatsapp', 'textdomain'); ?> <i class="fab fa-whatsapp"></i></span>
												</a>
											</li>
										</ul>
										<span class="c-body"><b><?php _e( 'Formas de pagamento: ', 'textdomain' ); ?></b></span>
										<ul class="profileList mt-2">
											<?php
											if( !empty($profileData['_profile_payment_methods']) ){
												foreach( $profileData['_profile_payment_methods'] as $term ){
													echo '<li class="profileList__item">
															<a href="" class="c-body profileList__link" rel="tag">'. $term->name .'</a>
														</li>';
												}
											}
											?>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<!-- /End Places -->

						<div class="profileWrapper col-12 d-none">
							<nav>
								<div class="nav nav-tabs" id="nav-tab" role="tablist">
									<button class="nav-link active" id="rating-tab" data-bs-toggle="tab" data-bs-target="#rating" type="button" role="tab" aria-controls="rating" aria-selected="true"><?php _e( 'Avaliações', 'textdomain' ); ?></button>
									<button class="nav-link" id="form-comment-tab" data-bs-toggle="tab" data-bs-target="#form-comment" type="button" role="tab" aria-controls="form-comment" aria-selected="false"><?php _e( 'Faça uma avaliação', 'textdomain' ); ?></button>
								</div>
							</nav>
						
							<div class="tab-content">
								<div class="tab-pane fade show active" id="rating" role="tabpanel" aria-labelledby="rating-tab">
									<div class="row comment">
										<div class="comment-metadata col-12">
											<p class="c-white"><span class="author-name">Kauan Silva</span>/ 02 de Fevereiro, 2022</p>
										</div>
										<div class="comment-content col-12">
											<p class="c-body">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam vel leo a nibh mollis fermentum.</p>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="form-comment" role="tabpanel" aria-labelledby="form-comment-tab">
									<div class="rating d-flex">
										<i class="fas fa-star"></i>
										<i class="fas fa-star"></i>
										<i class="fas fa-star"></i>
										<i class="fas fa-star"></i>
										<i class="fas fa-star"></i>
									</div>
									<form class="col-12">
										<div class="row">
											<div class="input-field col-12">
												<input id="user_name" type="text" class="validate">
												<label for="user_name">Nome/Apelido</label>
											</div>
											<div class="input-field col-12">
												<textarea id="user_feedback" class="materialize-textarea"></textarea>
												<label for="user_feedback">Comentário</label>
											</div>
										</div>
										<button class="waves-effect waves-light btn-large">Enviar</button>
									</form>
								</div>
							</div>
						</div>
						<!-- /End Avaliações -->
					</div>
				</div>
			</div>
			<!-- /end Desktop Content -->
		</div>
		<?php

        return ob_get_clean();
    }
}