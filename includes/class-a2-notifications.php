<?php

/**
 * Este arquivo define a classe Notification
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    A2
 * @subpackage A2/includes
 * @since      1.0.0
 * @author     jnz93 <box@unitycode.tech>
 */
class A2_Notifications{

    private $sendTo;
    private $siteUrl;
    private $siteName;
    private $siteLogo;
    private $approvedIcon;
    private $disapprovedIcon;
    private $headers;
    private $attachments;

    public function __construct()
    {
        $adminEmail     = get_option( 'admin_email' );
        $this->siteUrl  = get_option( 'siteurl' );
        $this->siteName = get_option( 'blogname' );
        $this->siteLogo = get_custom_logo();
        $this->approvedIcon     = 'https://cdn2.iconfinder.com/data/icons/free-basic-icon-set-2/300/11-512.png';
        $this->disapprovedIcon  = 'https://cdn2.iconfinder.com/data/icons/free-basic-icon-set-2/300/11-512.png';

        $this->sendTo       = [ $adminEmail, 'joanes.andrades@hotmail.com', 'suporte@acompanhantesa2.com' ];
        $this->headers      = ['Content-Type: text/html; charset=UTF-8'];
        $this->attachments  = [$this->siteLogo, $this->approvedIcon, $this->disapprovedIcon];
    }

    /**
     * Enviar notificação quando uma verificação de perfil é solicitada
     * 
     * @param int   $profileId      Id do usuário que solicitou
     * @param int   $postId         Id do post de validação criado
     */
    public function submitProfileValidation( $profileId, $postId )
    {
        if( is_null($profileId) ) return;

        $subject    = '['. $this->siteName .'] Solicitação de Validação de Perfil';
        $user       = get_user_meta( $profileId, 'first_name', true) . ' ' . get_user_meta( $profileId, 'last_name', true );
        $editLink   = get_edit_post_link( $postId );
        $template   = file_get_contents( plugin_dir_path( __FILE__ ) . 'emails/tpl-profile-validation.php', true );

        $replaceArr = [
            '::logo'        => $this->siteLogo,
            '::user'        => $user,
            '::postLink'    => $editLink,
        ];
        $message = strtr( $template, $replaceArr );        

        wp_mail( $this->sendTo, $subject, $message, $this->headers, $this->attachments );        
    }

    /**
     * Enviar notificação quando uma verificação de perfil é concluída
     * 
     * @param int   $profileId      Id do usuário
     * @param bool  $result         0=false/1=true
     */
    public function sendValidationResult( $profileId, $result )
    {
        if( is_null($profileId) ) return;

        $subject    = '['. $this->siteName .'] Avaliação de Perfil';
        $user       = get_user_meta( $profileId, 'first_name', true) . ' ' . get_user_meta( $profileId, 'last_name', true );
        $userEmail  = get_user_meta( $profileId, 'user_email', true );
        $template   = file_get_contents( plugin_dir_path( __FILE__ ) . 'emails/tpl-profile-result-validation.php', true );

        $replaceArr = [
            '::logo'        => $this->siteLogo,
            '::user'        => $user,
            '::icon'        => $this->approvedIcon,
        ];
        $message = strtr( $template, $replaceArr );        

        wp_mail( $userEmail, $subject, $message, $this->headers, $this->attachments );        
    }

}
