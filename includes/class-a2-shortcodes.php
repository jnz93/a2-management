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
        
        /** Formulário de cadastro */
        add_shortcode( 'registerForm', [ $this, 'registerForm'] );

        /** Formulário de login */
        add_shortcode( 'loginForm', [ $this, 'loginForm'] );

        /** Lista de anúncios por localidade */
        add_shortcode( 'advCarousel', [ $this, 'listCarouselAdvertisement'] );

        /** Lista de anúncios genérica  */
        add_shortcode( 'listAdvertisement', [ $this, 'listAdvertisement' ] );

    }

    /**
     * Shortcode: Formulário de cadastro de usuários
     * 
     * Após a seleção do tipo de perfil que o usuário deseja ser um formulário será mostrado.
     * Este Formulário é chamado pelo método processForm() da classe A2_Register();
     */
    public function registerForm( $atts )
    {
        $a = shortcode_atts( 
            [
                'title' => 'Formulário de Cadastro'
			], 
            $atts
        );

        ob_start();
        $this->register->proccessForm();
        
        return ob_get_clean();
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
}