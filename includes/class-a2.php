<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       unitycode.tech
 * @since      1.0.0
 *
 * @package    A2
 * @subpackage A2/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    A2
 * @subpackage A2/includes
 * @author     jnz93 <box@unitycode.tech>
 */
class A2 {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      A2_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'A2_VERSION' ) ) {
			$this->version = A2_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'a2';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - A2_Loader. Orchestrates the hooks of the plugin.
	 * - A2_i18n. Defines internationalization functionality.
	 * - A2_Admin. Defines all hooks for the admin area.
	 * - A2_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-i18n.php';

		/**
		 * Classe reponsável por definir métodos que fazem tratativas de cadsatro de usuários 
		 * e novos tipos de usuários
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-queries.php';

		/**
		 * Classe reponsável por definir métodos que fazem tratativas de cadsatro de usuários 
		 * e novos tipos de usuários
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-register.php';

		/**
		 * Classe reponsável por shortcodes
		 * 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-shortcodes.php';

		/**
		 * Classe que recebe métodos e ações referentes ao perfil de usuários
		 * 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-profile.php';

		/**
		 * Classe que recebe métodos e ações referentes a galeria 
		 * de fotos e vídeos de acompanhante 
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-gallery.php';
		
		/**
		 * Classe responsável pelos anúncios
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-advertisement.php';

		/**
		 * Classe de notificações
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-notifications.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-a2-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-a2-public.php';

		/**
		 * Classe com métodos genéricos e váriados que são utilizados para auxiliar
         * em determinadas tarefas.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-helper.php';

		/**
		 * Classe com métodos genéricos e váriados que são utilizados para auxiliar
         * em determinadas tarefas.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-a2-profileHelper.php';

		$this->loader = new A2_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the A2_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new A2_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new A2_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new A2_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_materialize_css', 1 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    A2_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
