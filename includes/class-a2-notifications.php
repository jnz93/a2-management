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
    private $headers;
    private $attachments;

    public function __construct()
    {
        $adminEmail     = get_option( 'admin_email' );
        $this->siteUrl  = get_option( 'siteurl' );
        $this->siteName = get_option( 'blogname' );
        $this->siteLogo = get_custom_logo();

        $this->sendTo       = [ $adminEmail, 'joanes.andrades@hotmail.com' ];
        $this->headers      = ['Content-Type: text/html; charset=UTF-8'];
        $this->attachments  = [$this->siteLogo];
    }

}
