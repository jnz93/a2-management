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
		add_action( 'wp_login_failed', [ $this, 'loginFailed'] );

		/** Tratamento para quando username ou password estão vazios */
		add_action( 'authenticate', [ $this, 'verifyUsernamePassword'] );

		/** Redirecionamento quando faz logout */
		add_action( 'wp_logout', [ $this, 'customLogoutPage'] );
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

		/**
		 * Enqueue Materialize Front-End Framework
		 */
		wp_enqueue_style( 'materialize', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css', [], '', 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/a2-public.js', array( 'jquery' ), $this->version, false );

		/**
		 * Enqueue Materialize Front-End Framework
		 */
		wp_enqueue_script( 'materialize', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js', [], '', true );
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
}
