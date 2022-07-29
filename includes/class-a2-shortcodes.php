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
    private $profile;

    /**
	 * Inicialização da classe e configurações de hooks, filtros e propriedades.
	 *
	 * @since    1.0.0
	 */
    public function __construct()
    {
        $this->register = new A2_Register();
        $this->a2Query  = new A2_Query();
        $this->gallery  = new A2_Gallery();
        $this->profile  = new A2_Profile();
        $this->Adv      = new A2_Advertisement();

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
        $this->register->page();
        
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
                $colorOne   = '';
                $colorTwo   = '';

                $icons  = [
                    'prata'     => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><g id="_9554546_medal_silver_badge_achievement_reward_icon" data-name="9554546_medal_silver_badge_achievement_reward_icon" transform="translate(-27.8 -14.2)"><ellipse id="Elipse_62" data-name="Elipse 62" cx="32" cy="32" rx="32" ry="32" transform="translate(27.8 14.2)" fill="#ededed"/><circle id="Elipse_63" data-name="Elipse 63" cx="25.044" cy="25.044" r="25.044" transform="translate(34.756 21.156)" fill="#bcbcbc"/><path id="Caminho_299" data-name="Caminho 299" d="M60.852,31.206l4.119,8.468a1.567,1.567,0,0,0,.981.807l9.122,1.411a1.387,1.387,0,0,1,.785,2.319l-6.571,6.654a1.409,1.409,0,0,0-.392,1.21l1.569,9.376A1.326,1.326,0,0,1,68.5,62.862l-8.141-4.436a1.419,1.419,0,0,0-1.275,0l-8.239,4.436a1.349,1.349,0,0,1-1.962-1.411l1.569-9.376a1.409,1.409,0,0,0-.392-1.21l-6.571-6.654a1.349,1.349,0,0,1,.785-2.319L53.4,40.481a1.31,1.31,0,0,0,.981-.807L58.5,31.206A1.294,1.294,0,0,1,60.852,31.206Z" transform="translate(0.124 -0.336)" fill="#fff"/></g></svg>',
                    'ouro'      => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><g id="_9554545_medal_gold_winner_badge_achievement_icon" data-name="9554545_medal_gold_winner_badge_achievement_icon" transform="translate(-27.8 -14.2)"><ellipse id="Elipse_60" data-name="Elipse 60" cx="32" cy="32" rx="32" ry="32" transform="translate(27.8 14.2)" fill="#ffc54d"/><circle id="Elipse_61" data-name="Elipse 61" cx="25.141" cy="25.141" r="25.141" transform="translate(34.659 21.059)" fill="#e8b04b"/><path id="Caminho_297" data-name="Caminho 297" d="M61.083,31.216,65.256,39.8a1.587,1.587,0,0,0,.994.817l9.24,1.43a1.406,1.406,0,0,1,.795,2.35l-6.657,6.743a1.428,1.428,0,0,0-.4,1.226l1.59,9.5a1.344,1.344,0,0,1-1.987,1.43l-8.246-4.5a1.437,1.437,0,0,0-1.292,0l-8.346,4.5a1.367,1.367,0,0,1-1.987-1.43l1.59-9.5a1.428,1.428,0,0,0-.4-1.226L43.5,44.4a1.367,1.367,0,0,1,.795-2.35l9.24-1.43a1.327,1.327,0,0,0,.994-.817L58.7,31.216A1.311,1.311,0,0,1,61.083,31.216Z" transform="translate(-0.091 -0.558)" fill="#fff"/></g></svg>',
                    'diamante'  => '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><g id="Grupo_465" data-name="Grupo 465" transform="translate(-1061 -604)"><circle id="Elipse_60" data-name="Elipse 60" cx="32" cy="32" r="32" transform="translate(1061 604)" fill="#9bc9ff"/><circle id="Elipse_64" data-name="Elipse 64" cx="25.044" cy="25.044" r="25.044" transform="translate(1068.057 611.057)" fill="#1e81ce"/><g id="_9035684_diamond_sharp_icon" data-name="9035684_diamond_sharp_icon" transform="translate(1074.658 618.656)"><path id="Caminho_288" data-name="Caminho 288" d="M274.381,32H264l6.606,8.808Z" transform="translate(-245.23 -32)" fill="#fff"/><path id="Caminho_289" data-name="Caminho 289" d="M115.69,32l3.776,8.808L126.071,32Z" transform="translate(-108.471 -32)" fill="#fff"/><path id="Caminho_290" data-name="Caminho 290" d="M197.022,74.67,192,81.365h10.043Z" transform="translate(-178.836 -71.322)" fill="#fff"/><path id="Caminho_291" data-name="Caminho 291" d="M379.923,51.06l-3.663,8.548h8.767Z" transform="translate(-348.734 -49.565)" fill="#fff"/><path id="Caminho_292" data-name="Caminho 292" d="M28.182,51.06,23,59.608h8.846Z" transform="translate(-23 -49.565)" fill="#fff"/><path id="Caminho_293" data-name="Caminho 293" d="M33.626,192H24l17.481,22.6h.042Z" transform="translate(-23.922 -179.631)" fill="#fff"/><path id="Caminho_294" data-name="Caminho 294" d="M272.567,192l-7.9,22.6h.042L282.193,192Z" transform="translate(-245.9 -179.631)" fill="#fff"/><path id="Caminho_295" data-name="Caminho 295" d="M194.127,192H182.61l5.758,16.32Z" transform="translate(-170.183 -179.569)" fill="#fff"/></g></g></svg>'
                ];
                
                $colors = [
                    'diamond_dark'  => '#1E81CE',
                    'diamond_light' => '#9BC9FF',
                    'gold_dark'     => '#E8B04B',
                    'gold_light'    => '#FFC54D',
                    'silver_dark'   => '#BCBCBC',
                    'silver_light'  => '#C9C9C9'
                ];

                if( $advLvl == 1 ){
                    $advType    = 'prata';
                    $advIcon    = $icons[$advType];
                    $colorOne   = $colors['silver_dark'];
                    $colorTwo   = $colors['silver_light'];
                } else if( $advLvl == 2 ) {
                    $advType    = 'ouro';
                    $advIcon    = $icons[$advType];
                    $colorOne   = $colors['gold_dark'];
                    $colorTwo   = $colors['gold_light'];
                } else {
                    $advType    = 'diamante';
                    $advIcon    = $icons[$advType];
                    $colorOne   = $colors['diamond_dark'];
                    $colorTwo   = $colors['diamond_light'];
                }

                require plugin_dir_path( __DIR__ ) . 'public/partials/dashboard/tpl-advCardInfo.php';
                
            }
        }

        return ob_get_clean();
    }
}