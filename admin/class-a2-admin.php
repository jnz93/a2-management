<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       unitycode.tech
 * @since      1.0.0
 *
 * @package    A2
 * @subpackage A2/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    A2
 * @subpackage A2/admin
 * @author     jnz93 <box@unitycode.tech>
 */
class A2_Admin {

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
	 * The register Class
	 */
	private $register;

	/**
	 * The shortcode Class
	 */
	private $shortcodes;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name 	= $plugin_name;
		$this->version 		= $version;
		$this->register 	= new A2_Register();
		$this->shortcodes 	= new A2_Shortcodes();

		/**
		 * Adição do menu na dashboard admin
		 */
		add_action( 'admin_menu', array( $this, 'addMenuPageWpDashboard' ) );

		/**
		 * Custom post types
		 */
		add_action( 'init', [ $this, 'registerCustomPostTypes' ] );
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/a2-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/a2-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Registro do menu na dashboard wordpress
	 * 
	 */
	public function addMenuPageWpDashboard()
	{

		$pageTitle 	= 'A2 - Management';
		$menuTitle 	= 'A2 - Management';
		$capability = '10';
		$menuSlug 	= 'a2-dashboard';
		$iconUrl 	= plugin_dir_url( __DIR__ ) . 'img/icon-menu.png';
		$position	= '20';

		add_menu_page( $pageTitle, $menuTitle, $capability, $menuSlug, array( $this, 'cbPluginPage' ), $iconUrl, $position );
	}

	/**
	 * Callback para página do plugin
	 * 
	 */
	public function cbPluginPage()
	{
		require_once plugin_dir_path( __FILE__ ) . 'partials/a2-admin-display.php';
	}

	/** 
	 * Custom post type
	 * Criação de CPT para acompanhantes
	 * 
	 */
	public function registerCustomPostTypes()
	{
		$labels = array(
			'name'                  => _x( 'Acompanhantes', 'Post type general name', 'textdomain' ),
			'singular_name'         => _x( 'Acompanhante', 'Post type singular name', 'textdomain' ),
			'menu_name'             => _x( 'Acompanhantes', 'Admin Menu text', 'textdomain' ),
			'name_admin_bar'        => _x( 'Acompanhantes', 'Add New on Toolbar', 'textdomain' ),
			'add_new'               => __( 'Adicionar nova(o)', 'textdomain' ),
			'add_new_item'          => __( 'Adicionar nova(o)', 'textdomain' ),
			'new_item'              => __( 'Nova(o) Acompanhante', 'textdomain' ),
			'edit_item'             => __( 'Editar Acompanhante', 'textdomain' ),
			'view_item'             => __( 'Visualizar Acompanhante', 'textdomain' ),
			'all_items'             => __( 'Todas Acompanhantes', 'textdomain' ),
			'search_items'          => __( 'Procurar Acompanhante', 'textdomain' ),
			'parent_item_colon'     => __( 'Parent Acompanhante:', 'textdomain' ),
			'not_found'             => __( 'Nenhum Acompanhante encontrada(o).', 'textdomain' ),
			'not_found_in_trash'    => __( 'Nenhum Acompanhante encontrada(o) na lixeira.', 'textdomain' ),
			'featured_image'        => _x( 'Foto Perfil Acompanhante', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'set_featured_image'    => _x( 'Definir Foto de Perfil', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'remove_featured_image' => _x( 'Remover Foto de Perfil', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'use_featured_image'    => _x( 'Usar como foto de perfil', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'archives'              => _x( 'Arquivos de acompanhantes', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
			'insert_into_item'      => _x( 'Inserir Acompanhante', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
			'uploaded_to_this_item' => _x( 'Carregado para este acompanhante', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
			'filter_items_list'     => _x( 'Filtrar lista de Acompanhantes', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
			'items_list_navigation' => _x( 'Navegação da lista de Acompanhantes', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
			'items_list'            => _x( 'Lista de acompanhantes', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'acompanhante' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
		);

		register_post_type( 'a2_escorts', $args );
	}
}
