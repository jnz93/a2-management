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
     * Método responsável pela inserção de um novo usuário na base.
     * 
     * @param string $firstName     Primeiro nome
     * @param string $lastName      Sobrenome
     * @param string $email         Email do usuário
     * @param string $userType      Nível do usuário
     *
     */
    private function user( $firstName, $lastName, $email, $userType )
    {
        global $regErrors;

        if( 1 > count( $regErrors->get_error_messages() ) ){
            $userData = array(
                'user_login'    => $firstName,
                'first_name'    => $firstName,
                'last_name'     => $lastName,
                'user_email'    => $email,
                'role'          => $userType,
            );
            $userId = wp_insert_user( $userData );
            if( !is_wp_error( $userId ) ){
                # Marcação das users metas pós-cadastro | 0=false/1=true
                update_user_meta( $userId, '_acepted_terms', 1 );
                update_user_meta( $userId, '_confirmation_age', 1 );
                update_user_meta( $userId, '_is_completed_perfil', 0 );

                # Disparo do e-mail
                retrieve_password($firstName);
                
                echo '<p>Pré-cadastro completo! Enviamos um e-mail com instruções para configuração da senha. Acesse seu ' . $email . ' e clique no link enviado para finalizar o cadastro.</p>'; 
            } else {
                echo 'Erro ao cadastrar </br>';
                var_dump($userId);
            }
        }
    }

    /**
     * Método responsável por retonar o HTML com formulário de cadastro
     * O método recebe um parâmetro para saber qual tipo de usuário será cadastrado
     * 
     * @param string $firstName     Primeiro nome
     * @param string $lastName      Sobrenome
     * @param string $email         E-mail 
     * @param string $userType      Tipo do usuário que será cadastrado Seguidor/Acompanhante
     */
    private function form( $firstName, $lastName, $email, $userType )
    {
        $form = '
        <div id="selectPerfil" class="">
            <h4 class="mb-1">'. __('Selecione o perfil', 'textdomain') .'</h4>
            <p class="text-black-50">'. __('Escolha qual tipo de perfil deseja criar na plataforma.') .'</p>
            <div class="row mt-2">
                <div class="col-6">
                    <a class="btn btn-outline-secondary" style="width: 100%" onclick="showFormScort()">'. __( 'Quero ser </b>Acompanhante</b>', 'textdomain' ) .'</a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-secondary" style="width: 100%" onclick="showFormFollower()"> '. __( 'Quero ser </b>Seguidor(a)</b>', 'textdomain' ).'</a>
                </div>
            </div>
        </div>

        <div id="registrationForm" class="row mt-5 mb-5 d-none">
            <form class="col-12" action="'. $_SERVER['REQUEST_URI'] .'" method="post">
                <div class="row">
                    <div class="col-6 mb-3">
                        <label for="first_name" class="form-label">Nome</label>
                        <input id="first_name" name="first_name" class="form-control" type="text" value="'. ( isset( $_POST['first_name'] ) ? $firstName : null  ) .'">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="last_name" class="form-label">Sobrenome</label>
                        <input id="last_name" name="last_name" class="form-control" type="text"  value="'. ( isset( $_POST['last_name'] ) ? $lastName : null  ) .'">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input id="email" name="email" class="form-control" type="email" value="'. ( isset( $_POST['email'] ) ? $email : null  ) .'">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="form-check form-switch ms-2 col-12">
                        <input class="form-check-input" type="checkbox" role="switch" name="terms_agree" id="terms_agree">
                        <label class="form-check-label" for="terms_agree">'. __('Concordo com os <b><a href="#">termos de uso e políticas de privacidade</a></b> da plataforma A2 Acompanhantes.', 'textdomain') .'</label>
                    </div>

                    <div class="form-check form-switch ms-2 col-12">
                        <input class="form-check-input" type="checkbox" role="switch" name="age_confirm" id="age_confirm">
                        <label class="form-check-label" for="age_confirm">'. __('Declaro que <b>sou maior de 18 anos</b>.', 'textdomain') .'</label>
                    </div>
                </div>

                <input id="user_type" name="user_type" type="hidden" value="'. ( isset( $_POST['user_type'] ) ? $userType : null ) .'">
                <button class="btn btn-primary btn-lg" type="submit" name="submit" value="registerUser">'. __('Cadastrar', 'textdomain') .'<i class="bi bi-send-fill ms-2"></i></button>
            </form>
        </div>

        <script>
            function showFormScort(){
                var formWrapper     = jQuery("#registrationForm"),
                    buttonsWrapper  = jQuery("#selectPerfil"),
                    typeUser        = jQuery("#user_type");

                formWrapper.removeClass("d-none");
                buttonsWrapper.hide();
                typeUser.val("a2_scort");
            }

            function showFormFollower(){
                var formWrapper = jQuery("#registrationForm"),
                    buttonsWrapper = jQuery("#selectPerfil"),
                    typeUser        = jQuery("#user_type");

                formWrapper.removeClass("d-none");
                buttonsWrapper.hide();
                typeUser.val("a2_follower");
            }
        </script>
        ';

        echo $form;
    }

    /**
     * Método responsável pela validação dos inputs do usuário
     * 
     * @param string $firstName     Primeiro Nome
     * @param string $lastName      Sobrenome
     * @param string $email         E-mail do usuário
     * @param boolean $termsConfirm Confirmação da aceitação dos termos
     * @param boolean $ageConfirm   Confirmação de maioridade  
     * 
     */
    private function validate( $firstName, $lastName, $email, $termsConfirm, $ageConfirm )
    {
        global $regErrors;
        $regErrors = new WP_Error();

        if( empty( $firstName ) || empty( $lastName ) || empty( $email ) ){
            $regErrors->add( 'field', __('Todos os campos são obrigatórios.')  );
        }

        if( 4 > strlen( $firstName ) ){
            $regErrors->add( 'short_firstname', __('Primeiro nome está muito curto. Preencha com pelo menos 4 caracteres.') );
        }
        if( 4 > strlen( $lastName ) )
        {
            $regErrors->add( 'short_lastname', __('Sobrenome está muito curto. Preencha com pelo menos 4 caracteres.') );
        }

        if( !is_email( $email ) ){
            $regErrors->add('email_invalid', __('E-mail inválido.') );
        }
        if( email_exists( $email ) ){
            $regErrors->add('email', __('E-mail em uso.') );
        }

        if( $termsConfirm == false ){
            $regErrors->add('accept_terms', __('Você precisa aceitar os termos de uso e políticas de privacidade para completar o cadastro.') );
        }

        if( $ageConfirm == false ){
            $regErrors->add('age_confirm', __('Você precisa confirmar ser maior de 18 anos para completar o cadastro.') );
        }

        // Checando os erros e mostrando se houver algum.
        if ( is_wp_error( $regErrors ) ) {

            foreach ( $regErrors->get_error_messages() as $error ) {
                echo '<div>';
                echo '<strong>ERROR</strong>:';
                echo $error . '<br/>';
                echo '</div>';
            }
        
        }
    }

    /**
     * Método responsável por mostrar o formulário ou processar um novo cadastro
     * Este método é passado para o shortcode.
     * 
     */
    public function proccessForm()
    {
        if( isset( $_POST['submit'] ) ){
            $this->validate(
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['email'],
                $_POST['terms_agree'],
                $_POST['age_confirm'],
            );

            # Limpeza dos dados do usuário
            $firstName  = sanitize_text_field( $_POST['first_name'] );
            $lastName   = sanitize_text_field( $_POST['last_name'] );
            $userType   = sanitize_text_field( $_POST['user_type'] );
            $email      = sanitize_email( $_POST['email'] );

            # Chamada da função de adição de usuário
            $this->user($firstName, $lastName, $email, $userType);
        }

        $this->form( $firstName, $lastName, $email, $userType );
    }
}