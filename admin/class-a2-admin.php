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

		/**
		 * Custom taxonomies
		 */
		add_action( 'init', [ $this, 'registerCustomTaxonomies' ] );
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

		register_post_type( 'a2_escort', $args );
	}
	
	/**
	 * Custom taxonomies
	 * Criação de taxonomias para o cpt "a2_escort"
	 * 
	 */
	public function registerCustomTaxonomies()
	{
		// Gênero/Sexo
		$labels = array(
			'name'              => _x( 'Gêneros', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Gênero', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Gêneros', 'textdomain' ),
			'all_items'         => __( 'Todos os Gêneros', 'textdomain' ),
			'parent_item'       => __( 'Parent Gênero', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Gênero:', 'textdomain' ),
			'edit_item'         => __( 'Editar Gênero', 'textdomain' ),
			'update_item'       => __( 'Atualizar Gênero', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar novo Gênero', 'textdomain' ),
			'new_item_name'     => __( 'Novo Gênero Name', 'textdomain' ),
			'menu_name'         => __( 'Gêneros', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'genero' ),
		);
	 
		register_taxonomy( 'generos', array( 'a2_escort' ), $args );		
		unset( $args );
		unset( $labels );

		// Etnias
		$labels = array(
			'name'              => _x( 'Etnias', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Etnia', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Etnias', 'textdomain' ),
			'all_items'         => __( 'Todas as Etnias', 'textdomain' ),
			'parent_item'       => __( 'Parent Etnia', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Etnia:', 'textdomain' ),
			'edit_item'         => __( 'Editar Etnia', 'textdomain' ),
			'update_item'       => __( 'Atualizar Etnia', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar nova Etnia', 'textdomain' ),
			'new_item_name'     => __( 'Nova Etnia', 'textdomain' ),
			'menu_name'         => __( 'Etnias', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'etnia' ),
		);
	 
		register_taxonomy( 'etnias', array( 'a2_escort' ), $args );		
		unset( $args );
		unset( $labels );

		// Signo
		$labels = array(
			'name'              => _x( 'Signos', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Signo', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Signos', 'textdomain' ),
			'all_items'         => __( 'Todos os Signos', 'textdomain' ),
			'parent_item'       => __( 'Parent Signo', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Signo:', 'textdomain' ),
			'edit_item'         => __( 'Editar Signo', 'textdomain' ),
			'update_item'       => __( 'Atualizar Signo', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar novo Signo', 'textdomain' ),
			'new_item_name'     => __( 'Novo Signo', 'textdomain' ),
			'menu_name'         => __( 'Signos', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'signo' ),
		);
	 
		register_taxonomy( 'signos', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );
		
		// Locais de atendimento
		$labels = array(
			'name'              => _x( 'Locais de atendimento', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Local de atendimento', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Local de atendimento', 'textdomain' ),
			'all_items'         => __( 'Todos os Locais de atendimento', 'textdomain' ),
			'parent_item'       => __( 'Parent Local de Atendimento', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Local de atendimento:', 'textdomain' ),
			'edit_item'         => __( 'Editar Local de atendimento', 'textdomain' ),
			'update_item'       => __( 'Atualizar Local de atendimento', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar novo Local de atendimento', 'textdomain' ),
			'new_item_name'     => __( 'Novo Local de atendimento', 'textdomain' ),
			'menu_name'         => __( 'Locais de atendimento', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'local-atendimento' ),
		);
	 
		register_taxonomy( 'local-atendimento', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );
		
		// Especialidades
		$labels = array(
			'name'              => _x( 'Especialidades', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Especialidade', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Especialidade', 'textdomain' ),
			'all_items'         => __( 'Todas as Especialidades', 'textdomain' ),
			'parent_item'       => __( 'Parent Especialidade', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Especialidade:', 'textdomain' ),
			'edit_item'         => __( 'Editar Especialidade', 'textdomain' ),
			'update_item'       => __( 'Atualizar Especialidade', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar nova Especialidade', 'textdomain' ),
			'new_item_name'     => __( 'Novo Especialidade', 'textdomain' ),
			'menu_name'         => __( 'Especialidades', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'especialidade' ),
		);
	 
		register_taxonomy( 'especialidades', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );
		
		// Serviços
		$labels = array(
			'name'              => _x( 'Serviços', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Serviço', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Serviço', 'textdomain' ),
			'all_items'         => __( 'Todos os Serviços', 'textdomain' ),
			'parent_item'       => __( 'Parent Serviços', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Serviços:', 'textdomain' ),
			'edit_item'         => __( 'Editar Serviço', 'textdomain' ),
			'update_item'       => __( 'Atualizar Serviço', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar novo Serviço', 'textdomain' ),
			'new_item_name'     => __( 'Novo Serviço', 'textdomain' ),
			'menu_name'         => __( 'Serviços', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'servico' ),
		);
	 
		register_taxonomy( 'servicos', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );

		// Localização
		$labels = array(
			'name'              => _x( 'Localizações', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Localização', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Local', 'textdomain' ),
			'all_items'         => __( 'Todos os Locais', 'textdomain' ),
			'parent_item'       => __( 'Parent Localização', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Localizações:', 'textdomain' ),
			'edit_item'         => __( 'Editar Localização', 'textdomain' ),
			'update_item'       => __( 'Atualizar Localização', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar nova Localização', 'textdomain' ),
			'new_item_name'     => __( 'Novo Localização', 'textdomain' ),
			'menu_name'         => __( 'Localizações', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'localizacao' ),
		);
	 
		register_taxonomy( 'localizacao', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );

		// Idiomas
		$labels = array(
			'name'              => _x( 'Idiomas', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Idioma', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Idioma', 'textdomain' ),
			'all_items'         => __( 'Todos os Idiomas', 'textdomain' ),
			'parent_item'       => __( 'Parent Idiomas', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Idiomas:', 'textdomain' ),
			'edit_item'         => __( 'Editar Idioma', 'textdomain' ),
			'update_item'       => __( 'Atualizar Idioma', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar novo Idioma', 'textdomain' ),
			'new_item_name'     => __( 'Novo Idioma', 'textdomain' ),
			'menu_name'         => __( 'Idiomas', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'idioma' ),
		);
	 
		register_taxonomy( 'idiomas', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );

		// Dias da semana
		$labels = array(
			'name'              => _x( 'Dia de trabalho', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Dias de trabalho', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Dias', 'textdomain' ),
			'all_items'         => __( 'Todos os Dias', 'textdomain' ),
			'parent_item'       => __( 'Parent Dias', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Dias:', 'textdomain' ),
			'edit_item'         => __( 'Editar Dia', 'textdomain' ),
			'update_item'       => __( 'Atualizar Dia', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar novo Dia', 'textdomain' ),
			'new_item_name'     => __( 'Novo Dia', 'textdomain' ),
			'menu_name'         => __( 'Dias de trabalho', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'dias' ),
		);
	 
		register_taxonomy( 'dias-de-trabalho', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );

		// Formas de pagamento
		$labels = array(
			'name'              => _x( 'Formas de pagamento', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Forma de pagamento', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar formas de pagamento', 'textdomain' ),
			'all_items'         => __( 'Todos as formas de pagamento', 'textdomain' ),
			'parent_item'       => __( 'Parent formas de pagamento', 'textdomain' ),
			'parent_item_colon' => __( 'Parent formas de pagamento:', 'textdomain' ),
			'edit_item'         => __( 'Editar Forma de pagamento', 'textdomain' ),
			'update_item'       => __( 'Atualizar Formas de pagamento', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar nova Forma de pagamento', 'textdomain' ),
			'new_item_name'     => __( 'Nova Formas de pagamento', 'textdomain' ),
			'menu_name'         => __( 'Formas de pagamento', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'formas-de-pagamento' ),
		);
	 
		register_taxonomy( 'formas-de-pagamento', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );

		// Atende/Disponível para/Preferência
		$labels = array(
			'name'              => _x( 'Preferências', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Preferência', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Preferências', 'textdomain' ),
			'all_items'         => __( 'Todos as Preferências', 'textdomain' ),
			'parent_item'       => __( 'Parent Preferências', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Preferências:', 'textdomain' ),
			'edit_item'         => __( 'Editar Preferência', 'textdomain' ),
			'update_item'       => __( 'Atualizar Preferência', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar Preferência', 'textdomain' ),
			'new_item_name'     => __( 'Novo Preferência', 'textdomain' ),
			'menu_name'         => __( 'Preferências', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'dias' ),
		);
	 
		register_taxonomy( 'preferencia', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );

		// Perfil de atendimento
		$labels = array(
			'name'              => _x( 'Perfil de Atendimento', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Perfil de Atendimento', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Perfil de Atendimento', 'textdomain' ),
			'all_items'         => __( 'Todos os Perfis de Atendimento', 'textdomain' ),
			'parent_item'       => __( 'Parent Perfil de Atendimento', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Perfil de Atendimento:', 'textdomain' ),
			'edit_item'         => __( 'Editar Perfil de Atendimento', 'textdomain' ),
			'update_item'       => __( 'Atualizar Perfil de Atendimento', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar Perfil de Atendimento', 'textdomain' ),
			'new_item_name'     => __( 'Novo Perfil de Atendimento', 'textdomain' ),
			'menu_name'         => __( 'Perfil de Atendimento', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'perfil' ),
		);
	 
		register_taxonomy( 'perfil-atendimento', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );

		// Biotipo
		$labels = array(
			'name'              => _x( 'Biotipos', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Biotipo', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Biotipo', 'textdomain' ),
			'all_items'         => __( 'Todos os Biotipos', 'textdomain' ),
			'parent_item'       => __( 'Parent Biotipo', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Biotipo:', 'textdomain' ),
			'edit_item'         => __( 'Editar Biotipo', 'textdomain' ),
			'update_item'       => __( 'Atualizar Biotipo', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar Biotipo', 'textdomain' ),
			'new_item_name'     => __( 'Novo Biotipo', 'textdomain' ),
			'menu_name'         => __( 'Biotipo', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'biotipo' ),
		);
	 
		register_taxonomy( 'biotipo', array( 'a2_escort' ), $args );
		unset( $args );
		unset( $labels );

	}
}
