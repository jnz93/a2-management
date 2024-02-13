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
	 * The profile Class
	 */
	private $profile;

	/**
	 * The advertisement Class
	 */
	private $advertisement;

	/**
	 * The Gallery Class
	 */
	private $gallery;

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
		$this->profile 		= new A2_Profile();
		$this->advertisement = new A2_Advertisement();
		$this->gallery 		= new A2_Gallery();
		$this->woocTemplates = new SL_WoocTemplates();
		$this->restWorkers 	= new SL_RestWorkers();

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

		/**
		 * Registro de novos tamanhos e imagens
		 */
		add_action( 'init', [ $this->gallery, 'registerCustomImageSizes' ] );

		/**
		 * Filtro para manipular o upload de imagens
		 */
		add_filter( 'wp_generate_attachment_metadata', [ $this->gallery, 'generateWatermarkedImage' ] );

		/** 
		 * Action para criação do anúncio 
		 * Atualmente estamos utilizando o status "completed", mas o ideal é que seja o "processing"
		 */
		add_action( 'woocommerce_order_status_changed', [ $this, 'paymentComplete' ], 10, 4 ); # Pedidos com cupon ficam com o status "Processing", por isso reativamos esse hack.
		add_action( 'woocommerce_order_status_completed', [ $this, 'createAdvertisement' ], 10, 1 );

        /** Sistema LTO(last time online) */
        add_action( 'wp_login', [$this, 'setLastTimeLogin'] );
        add_action( 'wp_head', [$this, 'setUserLastActivity'] );

        # Registro de ação para exclusão de usuários obsoletos
		add_action( 'a2_removeUsers', [ $this, 'removerObsoleteUsers' ], 10, 1 );

        # Aplicando rotina para exclusão de usuários obsoletos
		if( !wp_next_scheduled( 'a2_removeUsers' ) ){
			wp_schedule_event( time(), 'weekly', 'a2_removeUsers' );
		}

		# Filtro para substituir templates woocommerce
		add_filter('wc_get_template', [$this, 'customWoocMyAccountTemplates'], 10, 3);
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
		
		unset($labels);
		unset($args);

		$labels = array(
			'name'                  => _x( 'Anúncios', 'Post type general name', 'textdomain' ),
			'singular_name'         => _x( 'Anúncio', 'Post type singular name', 'textdomain' ),
			'menu_name'             => _x( 'Anúncios', 'Admin Menu text', 'textdomain' ),
			'name_admin_bar'        => _x( 'Anúncios', 'Add New on Toolbar', 'textdomain' ),
			'add_new'               => __( 'Adicionar nova(o)', 'textdomain' ),
			'add_new_item'          => __( 'Adicionar nova(o)', 'textdomain' ),
			'new_item'              => __( 'Nova(o) Anúncio', 'textdomain' ),
			'edit_item'             => __( 'Editar Anúncio', 'textdomain' ),
			'view_item'             => __( 'Visualizar Anúncio', 'textdomain' ),
			'all_items'             => __( 'Todas Anúncios', 'textdomain' ),
			'search_items'          => __( 'Procurar Anúncio', 'textdomain' ),
			'parent_item_colon'     => __( 'Parent Anúncio:', 'textdomain' ),
			'not_found'             => __( 'Nenhum Anúncio encontrada(o).', 'textdomain' ),
			'not_found_in_trash'    => __( 'Nenhum Anúncio encontrada(o) na lixeira.', 'textdomain' ),
			'featured_image'        => _x( 'Foto Perfil Anúncio', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'set_featured_image'    => _x( 'Definir Capa do anúncio', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'remove_featured_image' => _x( 'Remover Capa do anúncio', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'use_featured_image'    => _x( 'Usar como Capa do anúncio', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'archives'              => _x( 'Arquivos de Anúncios', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
			'insert_into_item'      => _x( 'Inserir Anúncio', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
			'uploaded_to_this_item' => _x( 'Carregado para este Anúncio', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
			'filter_items_list'     => _x( 'Filtrar lista de Anúncios', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
			'items_list_navigation' => _x( 'Navegação da lista de Anúncios', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
			'items_list'            => _x( 'Lista de Anúncios', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
		);
		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'anuncio' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
		);
		register_post_type( 'a2_advertisement', $args );

		unset($labels);
		unset($args);

		$labels = array(
			'name'                  => _x( 'Solicitações de Análise de Perfil', 'Post type general name', 'textdomain' ),
			'singular_name'         => _x( 'Solicitação de Análise de Perfil', 'Post type singular name', 'textdomain' ),
			'menu_name'             => _x( 'Solicitações de Análise de Peril', 'Admin Menu text', 'textdomain' ),
			'name_admin_bar'        => _x( 'Solicitações de Análise de Peril', 'Add New on Toolbar', 'textdomain' ),
			'add_new'               => __( 'Adicionar nova(o)', 'textdomain' ),
			'add_new_item'          => __( 'Adicionar nova(o)', 'textdomain' ),
			'new_item'              => __( 'Nova(o) Análise', 'textdomain' ),
			'edit_item'             => __( 'Editar Análise', 'textdomain' ),
			'view_item'             => __( 'Visualizar Solicitação', 'textdomain' ),
			'all_items'             => __( 'Todas as Solicitações', 'textdomain' ),
			'search_items'          => __( 'Procurar Solicitações', 'textdomain' ),
			'parent_item_colon'     => __( 'Parent Solicitações:', 'textdomain' ),
			'not_found'             => __( 'Nenhuma Solicitação encontrada(o).', 'textdomain' ),
			'not_found_in_trash'    => __( 'Nenhuma Solicitação encontrada(o) na lixeira.', 'textdomain' ),
			'featured_image'        => _x( 'Thumb da Análise', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'set_featured_image'    => _x( 'Definir Capa do Análise', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'remove_featured_image' => _x( 'Remover Capa do Análise', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'use_featured_image'    => _x( 'Usar como Capa do Análise', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain' ),
			'archives'              => _x( 'Arquivos de Análises', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain' ),
			'insert_into_item'      => _x( 'Inserir Análise', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain' ),
			'uploaded_to_this_item' => _x( 'Carregado para este Análise', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain' ),
			'filter_items_list'     => _x( 'Filtrar lista de Análises', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'textdomain' ),
			'items_list_navigation' => _x( 'Navegação da lista de Análises', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'textdomain' ),
			'items_list'            => _x( 'Lista de Análises', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'textdomain' ),
		);
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'analise' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author' ),
		);
		register_post_type( 'a2_analysis', $args );
		
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
			'menu_name'         => __( 'Gênero', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'generos' ),
		);	 
		register_taxonomy( 'profile_genre', array( 'a2_escort', 'a2_advertisement' ), $args );		
		
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
			'menu_name'         => __( 'Etnia', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'etnias' ),
		);
		register_taxonomy( 'profile_ethnicity', array( 'a2_escort', 'a2_advertisement' ), $args );

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
			'menu_name'         => __( 'Signo', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'signos' ),
		);	 
		register_taxonomy( 'profile_sign', array( 'a2_escort', 'a2_advertisement' ), $args );
		
		unset( $args );
		unset( $labels );
		
		// Locais
		$labels = array(
			'name'              => _x( 'Locais', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Local', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Local', 'textdomain' ),
			'all_items'         => __( 'Todos os Locais', 'textdomain' ),
			'parent_item'       => __( 'Parent Local', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Local:', 'textdomain' ),
			'edit_item'         => __( 'Editar Local', 'textdomain' ),
			'update_item'       => __( 'Atualizar Local', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar novo Local', 'textdomain' ),
			'new_item_name'     => __( 'Novo Local', 'textdomain' ),
			'menu_name'         => __( 'Local', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'locais' ),
		);	 
		register_taxonomy( 'profile_place_of_service', array( 'a2_escort', 'a2_advertisement' ), $args );

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
			'menu_name'         => __( 'Especialidade', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'especialidades' ),
		);
	 	register_taxonomy( 'profile_specialties', array( 'a2_escort', 'a2_advertisement' ), $args );

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
			'menu_name'         => __( 'Serviço', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'servicos' ),
		);
		register_taxonomy( 'profile_services', array( 'a2_escort', 'a2_advertisement' ), $args );

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
			'new_item_name'     => __( 'Nova Localização', 'textdomain' ),
			'menu_name'         => __( 'Localização', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'acompanhantes-em' ),
		);
		register_taxonomy( 'profile_localization', array( 'a2_escort', 'a2_advertisement' ), $args );
		
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
			'menu_name'         => __( 'Idioma', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'idiomas' ),
		);
		register_taxonomy( 'profile_languages', array( 'a2_escort', 'a2_advertisement' ), $args );
		
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
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'dias' ),
		);
	 
		register_taxonomy( 'profile_work_days', array( 'a2_escort', 'a2_advertisement' ), $args );
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
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'pagar-com' ),
		);
		register_taxonomy( 'profile_payment_methods', array( 'a2_escort', 'a2_advertisement' ), $args );
		
		unset( $args );
		unset( $labels );

		// Atende/Disponível para/Preferência
		$labels = array(
			'name'              => _x( 'Preferências', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Preferência', 'taxonomy singular name', 'textdomain' ),
			'search_items'      => __( 'Procurar Preferência', 'textdomain' ),
			'all_items'         => __( 'Todos os Preferências', 'textdomain' ),
			'parent_item'       => __( 'Parent Preferência', 'textdomain' ),
			'parent_item_colon' => __( 'Parent Preferência:', 'textdomain' ),
			'edit_item'         => __( 'Editar Preferência', 'textdomain' ),
			'update_item'       => __( 'Atualizar Preferência', 'textdomain' ),
			'add_new_item'      => __( 'Adicionar Preferência', 'textdomain' ),
			'new_item_name'     => __( 'Novo Preferência', 'textdomain' ),
			'menu_name'         => __( 'Preferência', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'preferencias' ),
		);
		register_taxonomy( 'profile_preference', array( 'a2_escort', 'a2_advertisement' ), $args );

		// Nível de anúncios / Exclusivo: a2_advertisement
		$labels = array(
			'name'              => _x( 'Niveis', 'taxonomy general name', 'textdomain' ),
			'singular_name'     => _x( 'Nível', 'taxonomy singular name', 'textdomain' ),
			'menu_name'         => __( 'Nível', 'textdomain' ),
		);
	 
		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'nivel' ),
		);
		register_taxonomy( 'advertisement_level', array( 'a2_advertisement' ), $args );
	}

	/**
	 * Se o status de pedido for alterado para "processing" automaticamente é setado para "completed"
	 * Esse é um hack útil para executar a publicação de anúncios. Uma vez que o hook "woocommerce_order_status_processing" não funcionava para isso
	 * 
	 * @param int 		$order_id
	 * @param string	$old_status
	 * @param string 	$new_status
	 * @param object 	$order
	 */
	public function paymentComplete( $order_id, $old_status, $new_status, $order )
	{
		if( $new_status == 'processing' ){
			$order->update_status( 'completed' );
		}
	}
	
	/**
	 * Chamada do método de criação de anúncio
	 * 
	 * @param int 	$order_id
	 */
	public function createAdvertisement( $order_id )
	{
		$this->advertisement->create( $order_id );
	}

    /**
     * Salvar data do último login de um usuário
     * Solução baseada no artigo: https://www.kvcodes.com/2015/12/how-to-set-user-last-login-date-and-time-in-wordpress/
     * 
     * @hook wp_login
     * 
     * @param string    $login 
     */
    public function setLastTimeLogin( $login )
    {
        $user = get_userdatabylogin($login);
        $currentLoginTime = get_user_meta( $user->ID, '_current_login', true );
        
        if( !empty($currentLoginTime) ){
            update_user_meta( $user->ID, '_last_login', $currentLoginTime );
            update_user_meta( $user->ID, '_current_login', time() );
        } else {
            update_user_meta( $user->ID, '_current_login', time() );
            update_user_meta( $user->ID, '_last_login', time() );
        }
    }

    /**
     * Salvar data da última atividade de um usuário logado
     * Atividade online é considerada requisição ao servidor, atualização de página, envio de arquivos, etc...
     * 
     * @hook wp_head
     */
    public function setUserLastActivity()
    {
        if( !is_user_logged_in() ) return;

        $userId = get_current_user_id();

        if( !is_wp_error($userId) ){
            update_user_meta( $userId, '_last_activity', time() );
        }
    }

    /**
     * Remoção de cadastros obsoletos
     * 
     */
    public function removerObsoleteUsers()
    {
        $list = $register->getObsoleteUsers();

        if( !empty($list) ){
            foreach( $list as $id ){
                wp_delete_user( $id );
            }
        }
    }


	/**
	 * Substituição dos templates padrões do painel "minha-conta" do woocommerce
	 * 
	 */
	public function customWoocMyAccountTemplates($template, $template_name, $args) {
		if('myaccount/form-edit-account.php' === $template_name){
			$content = do_shortcode('[sl_tplEditCccount]');

			return print('<div class="custom-edit-account-content">' . $content . '</div>');
		}
		
		return $template;
	}
}
