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
    public function addUser($name, $email, $password, $type)
    {
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
        $userId = wp_insert_user($userData);

        if(!is_wp_error($userId)){
            # Marcação das users metas pós-cadastro | 0=false/1=true
            update_user_meta($userId, '_spl_acepted_terms', 1);
            update_user_meta($userId, '_spl_confirmation_age', 1);
            update_user_meta($userId, '_spl_verified_email', 0);
            update_user_meta($userId, '_spl_verified_documents', 0);
            update_user_meta($userId, '_spl_completed_account', 0);
            update_user_meta($userId, '_spl_completed_profile', 0);
            update_user_meta($userId, '_spl_account_level', '0'); # 0=free / 1=premium / 2=super...
        }

        return $userId;
    }
}