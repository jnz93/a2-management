<?php

/**
 * The file that defines the registration class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    A2
 * @subpackage A2/includes
 * @since      1.0.0
 * @author     jnz93 <box@unitycode.tech>
 */
class A2_Register{

    /**
     * Update Roles Version
     */
    private $updateRolesVersion;

    /**
     *  Remove roles version
     */
    private $removeRolesVersion;

    /**
	 * Inicialização da classe e configurações de hooks, filtros e propriedades.
	 *
	 * @since    1.0.0.
	 */
    public function __construct()
    {
        $this->updateRolesVersion = get_option( '_update_roles_version' );
        $this->removeRolesVersion = get_option( '_remove_roles_version' );

        add_action( 'init', array( $this, 'updateUsersRoles') );
        add_action( 'init', array( $this, 'removeUsersRoles' ) );
    }

    /**
     * Este método adiciona novos tipos de usuários para o wordpress.
     * https://developer.wordpress.org/reference/functions/add_role/
     */
    public function updateUsersRoles()
    {
        if( $this->updateRolesVersion < 1 ){
            $followerSetup = array(
                'seguidor'  => [
                    'read'
                ]
            );
            $scortSetup     = array(
                'Acompanhante'  => [
                    'delete_posts',
                    'delete_published_posts',
                    'edit_posts',
                    'publish_posts',
                    'read',
                    'upload_files',
                ]
            );
            $roles = array(
                'a2_follower'   => $followerSetup,
                'a2_scort'      => $scortSetup,
            );
    
            foreach( $roles as $role => $setupRole ){
                foreach( $setupRole as $name => $capabilities ){
                    add_role( $role, __( $name ), $capabilities );
                }
            }
            update_option( '_update_roles_version', 1 );
        }
    }

    /**
     * Este método remove tipos de usuários obsoletos para a plataforma
     * 
     */
    public function removeUsersRoles()
    {
        if( $this->removeRolesVersion < 1 ){
            $removeRoles = array(
                'subscriber',
                'contributor',
                'author',
                'editor',
            );
            
            foreach( $removeRoles as $role ){
                remove_role( $role );
            }
            update_option( '_remove_roles_version', 1 );
        }
    }

    /**
     * Método responsável por mostrar o template de cadastro
     * Este método é chamado no shortcode [registerPage]
     * 
     */
    public function page()
    {
        if( isset( $_POST['submit'] ) ){
            $this->validation(
                $_POST['full_name'],
                $_POST['user_email'],
                $_POST['user_password'],
                $_POST['terms_agree'],
                $_POST['age_confirm'],
            );

            # Limpeza dos dados do usuário
            $fullName   = sanitize_text_field( $_POST['full_name'] );
            $userEmail  = sanitize_email( $_POST['user_email'] );
            $userPass   = $_POST['user_password'];
            $userType   = $_POST['user_type'];

            # Chamada da função de adição de usuário
            $this->addUser($fullName, $userEmail, $userPass, $userType);
        }

        require plugin_dir_path( __DIR__ ) . 'public/partials/pages/tpl-register-page.php';
    }

    /**
     * Retornar usuários obsoletos
     * Cadastros obsoletos são aqueles que não tem atividade de login há mais de 2 meses
     * 
     * @return array    $obsoleteList
     */
    public function getObsoleteUsers()
    {
        $limit          = strtotime('-2 months');
        $obsoleteList   = [];
        $args   = [
            'capability' => ['a2_scort'],
        ];
        $users = get_users( $args );

        if(!is_wp_error($users)){
            foreach( $users as $user ){
                $lastLoginTIme = get_user_meta($user->ID, '_last_login', true);
                if( empty($lastLoginTIme) || $lastLoginTIme < $limit ){
                    $obsoleteList[] = $user->ID;
                }
            }
        }
        return $obsoleteList;
    }

    /**
     * Este método recebe parâmetros como nome, email senha e tipo para 
     * adicionar um novo usuário do tipo acompanhante
     * 
     * @param string $name          Primeiro nome
     * @param string $email         Email
     * @param string $password      Senha
     * @param string $type          Nível do usuário
     *
     */
    private function addUser( $name, $email, $password, $type )
    {
        global $regErrors;

        if( 1 > count( $regErrors->get_error_messages() ) ){
            
            $nameArr    = explode( ' ', $name );
            $firstName  = $nameArr[0];
            $lastName   = $nameArr[1];

            $userData = array(
                'user_login'    => $name,
                'first_name'    => $firstName,
                'last_name'     => $lastName,
                'display_name'  => $name,
                'user_email'    => $email,
                'user_pass'     => $password,
                'role'          => $type,
            );
            $userId = wp_insert_user( $userData );
            if( !is_wp_error( $userId ) ){
                # Marcação das users metas pós-cadastro | 0=false/1=true
                update_user_meta( $userId, '_acepted_terms', 1 );
                update_user_meta( $userId, '_confirmation_age', 1 );
                update_user_meta( $userId, '_completed_account', 0 );
                
                echo '<script type="text/javascript">setTimeout( () => {showMessageForRegisterSuccess()}, 100 );</script>';
            }
        }
    }

    /**
     * Este método faz a validação dos dados de cadastro
     * 
     * @param string $name              Nome completo
     * @param string $email             E-mail do usuário
     * @param string $password          Senha da conta
     * @param boolean $termsConfirm     Confirmação da aceitação dos termos
     * @param boolean $ageConfirm       Confirmação de maioridade  
     * 
     */
    private function validation( $name, $email, $password, $termsConfirm, $ageConfirm )
    {
        global $regErrors;
        $regErrors = new WP_Error();

        if( empty( $name ) || empty( $email ) || empty( $password ) ){
            $regErrors->add( 'field', __('<strong>Todos</strong> os campos são obrigatórios.')  );
        }

        if( 4 > strlen( $name ) ){
            $regErrors->add( 'short_name', __('O campo <strong>nome</strong> está muito curto. Preencha com pelo menos 4 caracteres.') );
        }
        
        if( !is_email( $email ) ){
            $regErrors->add('email_invalid', __('<strong>E-mail</strong> inválido.') );
        }
        if( email_exists( $email ) ){
            $regErrors->add('email', __('Este <strong>E-mail</strong> já está em uso.') );
        }
        if( empty( $password ) ){
            $regErrors->add('password', __('Preencha o campo <strong>senha</strong> para completar o cadastro.', 'textdomain') );
        }

        if( $termsConfirm == false ){
            $regErrors->add('accept_terms', __('Aceite os <strong>termos de uso e políticas de privacidade</strong> para completar o cadastro.') );
        }

        if( $ageConfirm == false ){
            $regErrors->add('age_confirm', __('Você precisa ser <strong>maior de 18 anos</strong> para completar o cadastro.') );
        }

        // Checando os erros e mostrando se houver algum.
        if ( is_wp_error( $regErrors ) ) {
            
            ob_start();
            echo '<div class="position-fixed bottom-0 p-3 wrapper__errors">';
            foreach ( $regErrors->get_error_messages() as $error ) {
                require plugin_dir_path( __DIR__ ) . 'public/partials/alerts/tpl-alert-danger.php';
            }
            echo '</div>';
            echo ob_get_clean();
        
        }
    }

}