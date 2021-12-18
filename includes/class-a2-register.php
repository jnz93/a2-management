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
     * 
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
                
                echo 'Pré-cadastro completo! Enviamos um e-mail com instruções para configuração da senha. Acesse seu ' . $email . ' e clique no link enviado para finalizar o cadastro.'; 
            } else {
                echo 'Erro ao cadastrar </br>';
                var_dump($userId);
            }
        }
    }
}