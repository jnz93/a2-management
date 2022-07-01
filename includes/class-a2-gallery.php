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

    /**
     * Retorna um array de url's das imagens da galeria. Retornar `false` case seja um array vazio;
     * 
     * @return array/bool
     */
    public function getUrls($postId)
    {
        if( is_null($postId) ) return;

        $gallery    = [];
        $ids        = $this->get($postId);
        if( empty($ids) ){
            $gallery = false;
        } else{
            foreach( $ids as $id ){
                $gallery[] = wp_get_attachment_url( $id );
            }
        }

        return $gallery;
    }

    /**
     * Adicionar tamanhos customs de imagens enviadas
     * Para isso vamos levar me consideração as melhores práticas
     * @link https://www.strikingly.com/content/blog/website-image-size-guidelines/
     */
    public function registerCustomImageSizes()
    {
        add_image_size( 'o-hr', 1200, 900, true );
        add_image_size( 'o-vr', 900, 1200, true );
    }

    /**
     * Definição do diretório de upload
     * Chamada do método que vai manipular a imagem e inserir a marca d'água
     * 
     * @param array    $meta    Array que representa os metadados do arquivo
     * @return array   $meta    Array que representa os metadados do arquivo modificados
     */
    public function generateWatermarkedImage( $meta )
    {
        $time       = substr( $meta['file'], 0, 7); # Extract the date in form "2015/04"
        $uploadDir  = wp_upload_dir( $time );
        $guidelines = ['o-hr', 'o-vr']; # Images Guidelines

        foreach( $guidelines as $guideline ){
            $filename                           = $meta['sizes'][$guideline]['file'];
            $meta['sizes'][$guideline]['file']  = $this->applyWatermark( $filename, $uploadDir );
        }

        return $meta;
    }

    /**
     * Manipulando imagens para inserir a marca d'água
     * Utilizando a biblioteca ImageMagick
     * 
     * @param string    $filename   nome do arquivo manipulado
     * @param string    $uploadDir  caminho para o local de upload
     */
    public function applyWatermark( $filename, $uploadDir )
    {
        $originalImagePath  = trailingslashit( $uploadDir['path'] ) . $filename;
        $imageResource      = new Imagick( $originalImagePath );
        list( $imgW, $imgH ) = getimagesize( $originalImagePath );

        # Setup watermark
        $tempWatermarkUrl   = 'https://acompanhantesa2.com/wp-content/uploads/2022/06/logotipo-transparente.png';
        $watermarkResource  = new Imagick( $tempWatermarkUrl );
        list( $wmW, $wmH ) = getimagesize( $tempWatermarkUrl );
        
        # Setup $coords to insert watermark
        $coords =  [
            'top-left'      =>'25, 25', 
            'top-right'     => ($imgW-$wmW) - 25 . ', 25', 
            'middle'        => ($imgW - $wmW) / 2 . ', ' . ($imgH - $wmH) / 2, 
            'bottom-left'   => '25, ' . ($imgH - $wmH) - 25,
            'bottom-right'  => ($imgW - $wmW) - 25 . ', ' . ($imgH - $wmH) - 25
        ];
        
        foreach( $coords as $key => $value ){
            $coord = explode(',', $value);
            $imageResource->compositeImage( $watermarkResource, Imagick::COMPOSITE_DEFAULT, $coord[0], $coord[1] );
        }
       
        return $this->saveWatermarkedImage( $imageResource, $originalImagePath );
    }

    /**
     * Salvando o arquivo com a marca d'água e excluindo o antigo
     * 
     * 
     */
    public function saveWatermarkedImage( $imageResource, $originalImagePath )
    {
        $imageData              = pathinfo( $originalImagePath );
        $newFilename            = $imageData['filename'] . '-wm.' . $imageData['extension'];
        $watermarkedImagePath   = str_replace($imageData['basename'], $newFilename, $originalImagePath);

        if ( !$imageResource->writeImage( $watermarkedImagePath ) ) return $imageData['basename'];

        unlink( $originalImagePath );

        return $newFilename;
    }
}