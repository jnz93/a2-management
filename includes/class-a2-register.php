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
}