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
	 * The A2_Profile instantiate class
	 * 
	 */
	private $profile;

	/**
	 * The A2_Gallery instantiate class
	 */
	private $gallery;

	/**
	 * The A2_Advertisement instatiate class
	 */
	private $Advertisement;

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
		$this->profile = new A2_Profile();
		$this->gallery = new A2_Gallery();
		$this->Advertisement = new A2_Advertisement();

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

		/** Inserir o modal de termos de condições no rodapé */
		add_action( 'wp_footer', [ $this, 'modalTermsAndConditionsOfUse' ] );
		
		/** Action ajax p/ upload de arquivo único(foto perfi e capa) */
		add_action( 'wp_ajax_upload_attachment', [ $this, 'uploadAttachment' ] );

		/** Action ajax p/ upload de fotos para a galeria de perfil */
		add_action( 'wp_ajax_upload_gallery', [ $this, 'uploadGallery' ] );
		
		/** Action ajax p/ retorno dos children terms */
		add_action( 'wp_ajax_remove_gallery_items', [ $this, 'removeItemsFromGallery' ] );
		
		/** Action ajax p/ upload de vídeo */
		add_action( 'wp_ajax_upload_video', [ $this, 'uploadMedia' ] );
		
		/** Action ajax p/ verificação de perfil */
		add_action( 'wp_ajax_request_profile_evaluation', [ $this, 'requestProfileEvaluation' ] );

		/** Action ajax p/ verificação de perfil */
		add_action( 'wp_ajax_save_profile_evaluation_result', [ $this, 'saveProfileEvaluationResult' ] );

		/** Action para atualizar a lista de anúncios ativos */
		add_action( 'publish_to_draft', [ $this, 'removeActivatedAdvertisement' ], 10, 1 );

		/** Alterando a main query para remover anúncios expirados */
		add_action( 'pre_get_posts', [ $this, 'filterExpiredAdvertisement' ], 10, 1 );
	}

	// Remover anúncio da lista de ativados
	public function removeActivatedAdvertisement( $post )
	{
		if( $post->post_type !== 'a2_advertisement' ) return;

		$customerId	= $post->post_author;
		$postId 	= $post->ID;
		$this->Advertisement->removeActivatedItem( $postId, $customerId );
	}


	// Manipulando a consulta para remover anúncios expirados
	public function filterExpiredAdvertisement( $query )
	{
		if( is_admin() ) return;

		if( $query->is_main_query() && is_post_type_archive( 'a2_advertisement' ) ){
			date_default_timezone_set('America/Sao_Paulo'); # Setando GMT padrão

			$now = time();
			$metaquery = array(
				array(
					 'key' 		=> '_expiration_date',
					 'value' 	=> $now,
					 'type' 	=> 'NUMERIC',
					 'compare' 	=> '<'
				)
			);
			$query->set( 'meta_query', $metaquery );
		}
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
		wp_enqueue_style( 'bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css', array(), '1.8.1', 'all' );
		wp_enqueue_style( 'owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', array(), null, 'all' );
		wp_enqueue_style( 'owl-carousel-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css', array(), null, 'all' );
		
	}

	/**
	 * Register the Materialize CSS for the public-facing side of the site
	 * 
	 * @since 	1.0.0
	 */
	public function enqueue_materialize_css(){
		/**
		 * Enqueue Bootstrap e bootstrap select
		 */
		wp_enqueue_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', [], '1.0.0', 'all' );
		wp_enqueue_style( 'bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/css/bootstrap-select.min.css', [], '1.0.0', 'all' );

		if( get_post_type() == 'a2_escort' ){
			wp_enqueue_style( 'nano-gallery-2', 'https://cdnjs.cloudflare.com/ajax/libs/nanogallery2/3.0.5/css/nanogallery2.min.css', [], '3.0.5', 'all' );
		}
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
		wp_enqueue_script( 'a2-terms-and-conditions', plugin_dir_url( __FILE__ ) . 'js/terms-and-conditions.js', array( 'jquery' ), $this->version, false );
		
		/**
		 * Enqueue Bootstrap, fontaweson e jquery mask
		 */		
		wp_enqueue_script( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', [], '', true );
		wp_enqueue_script( 'fa-kit', 'https://kit.fontawesome.com/f18f521cf8.js', [], '', true );
		wp_enqueue_script( 'bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta2/dist/js/bootstrap-select.min.js', [], '', true );
		wp_enqueue_script( 'jquery-mask', plugin_dir_url( __FILE__ ) . 'js/jquery.mask.min.js', [], '', true );
		wp_enqueue_script( 'owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', [], null, true );

		if( get_post_type() == 'a2_escort' ){
			wp_enqueue_script( 'nano-gallery-2', 'https://cdnjs.cloudflare.com/ajax/libs/nanogallery2/3.0.5/jquery.nanogallery2.min.js', [], '3.0.5', true );
		}
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
				'edit-account'		=> 'Editar Perfil',
				'gallery'			=> 'Galeria',
				'advertisements'	=> 'Anúncios',
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
		echo '<ul id="breadcrumbs" class="">';
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
			'gallery'			=> EP_ROOT | EP_PAGES,
			'advertisements'	=> EP_ROOT | EP_PAGES,
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
		$vars[] = 'advertisements';

		return $vars;
	}

	/**
	 * Retorna descendentes diretos do termo recebido via ajax
	 * Este método é utilizado na edição do perfil para seleção das opções de localização
	 * e deve ser movido para outra classe que faça mais sentido.
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

	/**
	 * Esté método insere o modal de condições, termos de uso e confirmação de idade
	 * no rodapé do site quando identifica que o cookie de confirmação de uso não existe
	 * ou expirou.
	 * 
	 */
	public function modalTermsAndConditionsOfUse()
	{
		require_once plugin_dir_path( __FILE__ ) . 'partials/tpl-modal-terms-and-conditions.php';
	}

	/**
	 * Método responsável pelo upload de fotos via ajax
	 * 
	 * @return JSON $attachData
	 */
	public function uploadAttachment()
    {
        # Checking if media_handle_sideload exists
        if( !function_exists( 'media_handle_sideload' ) ){
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/file.php' );
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/image.php' );
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/media.php' ); 
        }
		
		$postId 			= 0;
        $file               = $_FILES['file'];
        $desc               = '';
		$attachData			= [];
        $allowedMimeTypes   = array(
            'jpg|jpeg|jpe'  => 'image/jpeg',
            'png'           => 'image/png',
            'webp'          => 'image/webp',
        );
        $overrides          = array(
            'test_form'     => false,
            'mimes'         => $allowedMimeTypes,
        );
        $attachId 			= media_handle_sideload( $file, $postId, $desc, $overrides );

		if( !is_wp_error( $attachId ) ){
			$attachData['attachId'] 	= $attachId;
			$attachData['attachUrl'] 	= wp_get_attachment_url( $attachId );
		}
		$attachData = json_encode( $attachData );

		echo $attachData;
		die();
    }

	/**
	 * Método responsável pelo upload de fotos e vídeos via ajax
	 * 
	 * @return JSON $attachData
	 */
	public function uploadGallery()
    {
        # Checking if media_handle_sideload exists
        if( !function_exists( 'media_handle_sideload' ) ){
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/file.php' );
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/image.php' );
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/media.php' ); 
        }
        $files	= $_FILES['files'];
		if( empty( $files ) || is_null( $files ) ) die();

		$fNames = $files['name'];
		$tNames = $files['tmp_name'];
		$fToUpload = array();
		foreach( $fNames as $i => $name ){
			$fToUpload[] = [
				'name'		=> $name,
				'tmp_name'	=> $tNames[$i]
			];
		}

		$userId 			= get_current_user_id();
		$postId 			= $this->profile->getProfilePageId( $userId );
        $desc               = '';
		$attachData			= [];
		$attachmentList		= [];
		$filesList 			= [];
		$allowedMimeTypes   = array(
			'jpg|jpeg|jpe'  => 'image/jpeg',
			'png'           => 'image/png',
		);
		$overrides          = array(
			'test_form'     => false,
			'mimes'         => $allowedMimeTypes,
		);

		if( true ){
			foreach( $fToUpload as $file ){
				# $file = ['name' => '....', 'tmp_name' => '...' ];
				$attachId 			= media_handle_sideload( $file, $postId, $desc, $overrides );
		
				if( !is_wp_error( $attachId ) ){
					$attachData[]	= [
						'attachId'	=> $attachId,
						'attachUrl'	=> wp_get_attachment_url( $attachId )
					];
					$attachmentList[] = $attachId;
				}
			}

			# Update na galeria
			$this->gallery->update( $postId, $attachmentList );

			$json = json_encode( $attachData );
			echo $json;
		}
		die();
    }

	/**
	 * Método chamado para exclusão de um ou mais arquivos da galeria
	 * de acompanhante
	 * 
	 * @return bool
	 */
	public function removeItemsFromGallery()
	{
		$excludeList 	= $_POST['excludeList'];
		if( is_null($excludeList) ) die();

		$result 		= false;
		$userId			= get_current_user_id();
		$postId			= $this->profile->getProfilePageId( $userId );
		$update 		= $this->gallery->remove( $postId, $excludeList );

		if( is_array($update)) {
			$result = true;
		}
		echo $result;
		
		die();
	}

	/**
	 * Método responsável pelo upload de vídeo via ajax
	 * 
	 * @return JSON $attachData
	 */
	public function uploadMedia()
    {
        # Checking if media_handle_sideload exists
        if( !function_exists( 'media_handle_sideload' ) ){
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/file.php' );
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/image.php' );
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/media.php' ); 
        }
		
		$postId 			= 0;
        $file               = $_FILES['file'];
        $desc               = '';
		$attachData			= [];
        $allowedMimeTypes   = array(
            'mpeg'  		=> 'video/mpeg',
            'ogv'  			=> 'video/ogg',
            'webm'			=> 'video/webm',
        );
        $overrides          = array(
            'test_form'     => false,
            'mimes'         => $allowedMimeTypes,
        );
        $attachId 			= media_handle_sideload( $file, $postId, $desc, $overrides );

		if( !is_wp_error( $attachId ) ){
			$attachData['attachId'] 	= $attachId;
			$attachData['attachUrl'] 	= wp_get_attachment_url( $attachId );
		}
		$attachData = json_encode( $attachData );

		echo $attachData;
		die();
    }

	/**
	 * Este método recebe os documentos de validação de perfil via ajax
	 * e cria uma nova solicitação de avaliação
	 * 
	 */
	public function requestProfileEvaluation()
	{
		// wp_verify_nonce( $nonce, $action ); # Verificar validade do código nonce
		if( empty($_POST) ) die();

		$attachFrontDoc = $_POST['frontDoc'];
		$attachBackDoc 	= $_POST['backDoc'];
		$attachMedia	= $_POST['media'];

		$result 		= null;
		if( strlen($attachFrontDoc) > 0 && strlen($attachBackDoc) > 0 && strlen($attachMedia) > 0 ){
			$attachments = [
				'_front_doc' 			=> $attachFrontDoc,
				'_back_doc'				=> $attachBackDoc,
				'_verification_media' 	=> $attachMedia
			];

			$postType	= 'a2_analysis';
			$status		= 'publish';
			$author 	= get_current_user_id();
			$profileName= get_user_meta( $author, 'first_name', true ) . ' ' . get_user_meta( $author, 'last_name', true );
			$title 		= 'Análise perfil - ' . $profileName;
			$content	= 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed varius velit vulputate risus pellentesque, id consectetur urna egestas';
			$postarr	= [
				'post_type'		=> $postType,
				'post_status'	=> $status,
				'post_title'	=> $title,
				'post_author'	=> $author,
				'post_content'	=> $content,
			];
			$postId = wp_insert_post( $postarr );

			if( !is_wp_error($postId) ){
				foreach( $attachments as $key => $attach ){
					# Setando o 'post_parent' do arquivo
					wp_update_post([
						'ID'			=> $attach,
						'post_parent'	=> $postId,
					]);

					# Salvando a URL dos arquivos no $postId
					update_post_meta( $postId, $key, wp_get_attachment_url($attach) );
				}

				$this->profile->underAnalysis($author);
				$result = $postId;
			}
		} else {
			$result = 'Documentos inválidos. Tente novamente';
		}

		echo $result;
		die();
	}

	/**
	 * Este método é responsável por salvar o resultado da avaliação
	 * em uma requisição ajax
	 * 
	 */
	public function saveProfileEvaluationResult()
	{
		// wp_verify_nonce( $nonce, $action ); # Verificar validade do código nonce
		if( empty($_POST) ) die();
		$nonce 		= $_POST['nonce'];
		$adminId 	= $_POST['adminId'];
		$result 	= $_POST['result'];
		$profileId 	= $_POST['profile'];

		# 0=reprovado / 1=aprovado
		if($result == '1'){
			$this->profile->markAsValid($profileId, $adminId);
		} else {
			$this->profile->markAsInvalid($profileId, $adminId);
		}
		die();
	}
}
