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
}