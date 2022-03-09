<?php

/**
 * Este arquivo define a classe A2_Gallery
 *
 * Métodos publicos e privados referente ações na galeria de fotos e vídeos do usuário
 *
 * @package    A2
 * @subpackage A2/includes
 * @since      1.0.0
 * @author     jnz93 <box@unitycode.tech>
 */
class A2_Gallery{

   /**
	 * O meta key onde a galeria está sendo salva no post
	 *
	 * @since    1.0.0
	 * @access   private
	 */
    private $metaKey;

    public function __construct()
    {
        $this->metaKey = '_profile_gallery';
        // Actions devem ser movidas para um arquivo único pois não podem ser registradas no __contruct de uma classe que é invocada por outras.
    }

    /**
     * Este método é responsável por atualizar a galeria de fotos na galeria da acompanhante. 
     * Ele recebe o id da página de acompanhante($postId), uma lista de ID's($list) 
     * e o parâmetro, opcional, que é utilizado para quando não queremos dar merge da galeria atual com o $list.
     * 
     * @param int   $postId     Id da página do acompanhante
     * @param array $list       Array de ids
     * @param bool  $justUpdate default=true; situacionalmente pode ser passado como "false".
     * @return bool
     */
    public function update( $postId, $list, $justUpdate = true )
    {
        $gallery        = array();
        $currentGallery = $this->get( $postId );
        if( $currentGallery && $justUpdate ){
            $gallery = array_merge( $list, $currentGallery );
        } else {
            $gallery = array_merge( $list, $gallery );
        }

        # Salvando a galeria
        return update_post_meta( $postId, $this->metaKey, $gallery );
    }

    /**
     * Este método faz a remoção dos attachemnts passados em $list da galeria do wordpress utilizando `wp_delete_attachment($id)`. 
     * Depois remove os itens em $list da galeria do perfil em `_profile_gallery`;
     * 
     * @param int   $postId     Id da página do acompanhante
     * @param array $list       array de ids
     */
    public function remove( $postId, $list )
    {
        $log = array();
        // Excluir o arquivo
		if( is_array($list) ){
			foreach( $list as $id ){
				$log[$id] = wp_delete_attachment( $id );
			}
		} else {
			$log[$list] = wp_delete_attachment( $list );
		}

        // Remover do meta key 
        $currentGallery = $this->get( $postId );
        $currentGallery = $currentGallery;
        $extractDiff    = array_diff( $currentGallery, $list );
        $log['removed'] = $this->update( $postId, $extractDiff, false );
        return $log;
    }

    /**
     * Retorna o array de ids da galeria do perfil. Retorna `false` caso seja um array vazio.
     * 
     * @param int $postId
     * @return array/bool
     */
    public function get( $postId )
    {
        $currentGallery = get_post_meta( $postId, $this->metaKey );
        if( empty( $currentGallery ) ){
            $currentGallery = false;
        } else {
            $currentGallery = $currentGallery[0];
        }

        return $currentGallery;
    }
}