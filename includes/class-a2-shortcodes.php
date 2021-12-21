<?php
/**
 * The file that defines the shortcode classes
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package     A2
 * @subpackage  A2/includes
 * @since       1.0.0
 * @author      jnz93 <box@unitycode.tech>
 * @link        https://codex.wordpress.org/Shortcode_API
 */
class A2_Shortcodes{

    /**
     * Instância da classe A2_Register
     */
    private $register;

    /**
	 * Inicialização da classe e configurações de hooks, filtros e propriedades.
	 *
	 * @since    1.0.0
	 */
    public function __construct()
    {
        $this->register = new A2_Register();

        /** Formulário de cadastro */
        add_shortcode( 'registerForm', [ $this, 'registerForm'] );

        /** Formulário de login */
        add_shortcode( 'loginForm', [ $this, 'loginForm'] );
    }

    /**
     * Shortcode: Formulário de cadastro de usuários
     * 
     * Após a seleção do tipo de perfil que o usuário deseja ser um formulário será mostrado.
     * Este Formulário é chamado pelo método processForm() da classe A2_Register();
     */
    public function registerForm( $atts )
    {
        $a = shortcode_atts( 
            [
                'title' => 'Formulário de Cadastro'
			], 
            $atts
        );

        ob_start();
        $this->register->proccessForm();
        
        return ob_get_clean();
    }

    /**
     * Shortcode: Formulário de login
     * 
     * Após o login, o tipo de usuário é identificado e redirecionado para o painel correspondente.
     * Usuários logados devem ser redirecionados para o painel correspondente
     */
    public function loginForm( $atts )
    {
        $a = shortcode_atts( 
            [
                'title' => 'Formulário de Cadastro'
			], 
            $atts
        );
        
        $login = ( isset( $_GET['login'] ) ) ? $_GET['login'] : 0;
        $args = array(
            'redirect'          => home_url('/painel'),
        );

        ob_start();

        if( $login === 'failed' ){
            echo '<p class="login-message"> <strong class="">'. __('ERRO: ') .'</strong>'. __('Nome ou senha inválidos.') .'</p>';
        } elseif( $login === 'empty' ){
            echo '<p class="login-message"> <strong class="">'. __('ERRO: ') .'</strong>'. __('Nome ou senha estão vazios.') .'</p>';
        } elseif( $login === 'false' ){
            echo '<p class="login-message"> <strong class="">'. __('ERRO: ') .'</strong>'. __('Você está desconectado.') .'</p>';
        }

        wp_login_form( $args );

        return ob_get_clean();
    }
}