<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       unitycode.tech
 * @since      1.0.0
 *
 * @package    A2
 * @subpackage A2/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    A2
 * @subpackage A2/public
 * @author     jnz93 <box@unitycode.tech>
 */
class A2_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		/** Redirect para página de login Custom */
		// add_action( 'init', [ $this, 'redirectLoginPage' ] ); # Funcionando mas desativado em desenvolvimento

		/** Tratamento para quando há falhas no login */
		// add_action( 'wp_login_failed', [ $this, 'loginFailed'] );

		/** Tratamento para quando username ou password estão vazios */
		// add_action( 'authenticate', [ $this, 'verifyUsernamePassword'], 10, 3 );

		/** Redirecionamento quando faz logout */
		// add_action( 'wp_logout', [ $this, 'customLogoutPage'] );

		/** Filtro para customização do menu do painel */
		add_filter( 'woocommerce_account_menu_items', [ $this, 'customizeUsersDashboardMenu' ] );

		/** Action breadcrumbs # Mover para classe apropriada */
		add_action( 'theBreadcrumbs', [ $this, 'customBreadcrumb'] );

		/** Registro de novos endpoints */
		add_action( 'init', [ $this, 'addCustomEndpoints' ] );

		/** Registro de novas $query_vars */
		add_filter( 'query_vars', [ $this, 'addCustomQueryVars' ], 0 );

		// add_action( 'init', [ $this, 'createCustomTaxonomy' ] );

		/** Action ajax p/ retorno dos children terms */
		add_action( 'wp_ajax_listChildrenTerms', [ $this, 'getDescendantTerms' ] );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in A2_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The A2_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/a2-public.css', array(), $this->version, 'all' );
		
	}

	/**
	 * Register the Materialize CSS for the public-facing side of the site
	 * 
	 * @since 	1.0.0
	 */
	public function enqueue_materialize_css(){
		/**
		 * Enqueue Materialize Front-End Framework
		 */
		wp_enqueue_style( 'materialize', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css', [], '1.0.0', 'all' );

		/**
		 * Enqueue Materialize Icons Font
		 */
		wp_enqueue_style( 'materialize-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', [], '', 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in A2_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The A2_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'a2-public', plugin_dir_url( __FILE__ ) . 'js/a2-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( 'a2-public', 'publicAjax', array(
			'nonce'     => wp_create_nonce( 'public-nonce' ),
			'url'		=> admin_url( 'admin-ajax.php' )
		) );
		/**
		 * Enqueue Materialize Front-End Framework
		 */
		wp_enqueue_script( 'materialize', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js', [], '', true );
		wp_enqueue_script( 'jquery-mask', plugin_dir_url( __FILE__ ) . 'js/jquery.mask.min.js', [], '', true );
	}
	
	/**
	 * Redirecionar acessos a página "/wp-login.php" para "/login"
	 * 
	 * @since 1.0.0
	 */
	public function redirectLoginPage()
	{
		$loginPage 	= home_url('/login/');
		$pageViewed = basename( $_SERVER['REQUEST_URI'] );

		if( $pageViewed == 'wp-login.php' && $_SERVER['REQUEST_METHOD'] == 'GET' ){
			
			wp_redirect( $loginPage );
			exit;
		}
	}

	/**
	 * Redirecionamento para página login custom quando uma falha de login acontecer
	 * 
	 * @since 1.0.0
	 */
	public function loginFailed()
	{
		$loginPage = home_url('/login/');

		wp_redirect( $loginPage . '?login=failed' );
		exit;
	}

	/**
	 * Verificação de entradas no login
	 * 
	 * @since 1.0.0
	 */
	public function verifyUsernamePassword( $user, $username, $password ){
		$loginPage = home_url('/login/');

		if( $username == '' || $password == '' ){
			wp_redirect( $loginPage . '?login=empty' );
			exit;
		}
	}

	/**
	 * Redirecionamento após logout
	 * 
	 * @since 1.0.0
	 */
	public function customLogoutPage()
	{
		$loginPage = home_url('/login/');

		wp_redirect( $loginPage . '?login=false' );
		exit;
	}

	/**
	 * Customização dos menus dos painéis de Seguidores e Acompanhantes
	 * 
	 * @param array $menu_links
	 * @return array $menu_links
	 * 
	 * @since 1.0.0
	 */
	public function customizeUsersDashboardMenu( $menu_links )
	{
		$newLinks 	= array();
        if( current_user_can( 'a2_follower' ) ){
			# Remoção de itens
			unset( $menu_links['orders'] );
			unset( $menu_links['downloads'] );
			unset( $menu_links['edit-address'] );
			unset( $menu_links['edit-account'] );
			
			# Adição de novos
			$newLinks = [
				'followed-profiles' => 'Favoritos',
				'edit-account'		=> 'Editar Perfil',
				'change-password' 	=> 'Alterar Senha',
			];
		} elseif( current_user_can( 'a2_scort') ){

			// Remoção de itens
			unset( $menu_links['downloads'] );
			unset( $menu_links['orders'] );
			unset( $menu_links['edit-address'] );
			unset( $menu_links['edit-account'] );

			# Adição de novos
			$newLinks = [
				'gallery'			=> 'Galeria',
				'edit-account'		=> 'Editar Perfil',
				'orders'			=> 'Faturas',
				'change-password' 	=> 'Alterar Senha',
			];
		}

		$menu_links = array_slice( $menu_links, 0, 1, true ) + $newLinks + array_slice( $menu_links, 1, NULL, true );

		return $menu_links;
	}

	/**
	 * Função responsável por criar o breadcrumbs de páginas e posts
	 * 
	 * @since 1.0.0
	 */
	public function customBreadcrumb()
	{
		global $post;
		echo '<ul id="breadcrumbs" class="d-flex">';
		if (!is_home()) {
			echo '<li><a href="';
			echo get_option('home');
			echo '">';
			echo 'Home';
			echo '</a></li><li class="separator"> / </li>';
			if (is_category() || is_single()) {
				echo '<li>';
				the_category(' </li><li class="separator"> / </li><li> ');
				if (is_single()) {
					echo '</li><li class="separator"> / </li><li>';
					the_title();
					echo '</li>';
				}
			} elseif (is_page()) {
				if($post->post_parent){
					$anc = get_post_ancestors( $post->ID );
					$title = get_the_title();
					foreach ( $anc as $ancestor ) {
						$output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> <li class="separator">/</li>';
					}
					echo $output;
					echo '<strong title="'.$title.'"> '.$title.'</strong>';
				} else {
					echo '<li><strong> '.get_the_title().'</strong></li>';
				}
			}
		}
		elseif (is_tag()) {single_tag_title();}
		elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
		elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
		elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
		elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
		elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
		elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
		echo '</ul>';
	}

	/**
	 * Registro de novo endpoint
	 * Nota: Resalvar Permalinks se não resultara em erro 404
	 * 
	 * @since 1.0.0
	 */
	public function addCustomEndpoints()
	{

		# Array com endpoints a serem adicionados - Modelo: 'endpoint' => places
		$newEndpoints = array(
			'gallery'	=> EP_ROOT | EP_PAGES,
		);

		if( !empty($newEndpoints) ){
			foreach( $newEndpoints as $endpoint => $places ){
				add_rewrite_endpoint( $endpoint, $places );
			}
		}
	}

	/**
	 * Adição de novas $query_vars
	 * 
	 * @since v1.0.0
	 */
	public function addCustomQueryVars( $vars )
	{
		$vars[] = 'gallery';

		return $vars;
	}

	/**
	 * Retorna descendentes diretos do termo recebido via ajax
	 * Este método deve ser movido para outra classe mais tarde
	 * 
	 */
	public function getDescendantTerms()
	{
		if( empty( $_POST ) ) die();

		$termId 	= $_POST['termId'];		
		$args 		= array(
			'taxonomy'		=> 'profile_localization',
			'parent'		=> $termId,
			'hide_empty'	=> false,
			'orderby'		=> 'name',
			'order'			=> 'ASC'
		);
		$terms = get_terms( $args );

		$data = array();
		if( !empty( $terms ) ){
			foreach( $terms as $term ){
				if( $term->parent != 0 ){
					$data[] = [ 'id' => $term->term_id, 'name' => $term->name ];
				}
			}
		}
		$data = json_encode( $data, JSON_PRETTY_PRINT );

		die($data);
	}
}
