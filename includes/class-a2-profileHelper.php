<?php
/**
 * Este arquivo define a classe ProfileHelper;
 *
 * A classe contem métodos que ajudam a pegar informações sobre o perfil da acompanhante.
 *
 * @package    A2
 * @subpackage A2/includes
 * @since      1.0.0
 * @author     jnz93 <box@unitycode.tech>
 */
class A2_ProfileHelper{

    public function __construct()
    {
        $this->currentUserId    = get_current_user_id();
        $this->metaKeyPageId    = '_profile_page_id';
        $this->metaKeyGallery   = '_profile_gallery';
        $this->metaKeyBirthday  = '_profile_birthdate';
        $this->metaKeyVerified  = '_verified_profile';
        $this->taxGenre         = 'profile_genre';

        $this->currDay 	        = date('d');
        $this->currMonth 	    = date('m');
        $this->currYear 	    = date('Y');

    }

    /**
     * Pegar o ID da página da acompanhante
     * 
     * @return mixed    $page   Se não existir retorna false, caso contrário retorna o ID da página.
     */
    public function getPageId()
    {
        $pageId = get_user_meta( $this->currentUserId, $this->metaKeyPageId, true );
        $page   = false;
        if( $pageId ){
            $foundStatus 	= get_post_status( $pageId );

            if( $foundStatus == 'publish' ){
                $page  = (int) $pageId;
            }
        }
        
        return $page;
    }

    /**
     * Pegar o ID da página da acompanhante, passando ID do usuário como parâmetro
     * 
     * @param int   $id     Id do usuário
     * 
     * @return int  $page   Id da página de acompanhante
     */
    public function getPageIdByAuthor( $id )
    {
        $pageId = get_user_meta( $id, $this->metaKeyPageId, true );
        $page   = false;
        
        if( !is_wp_error( $pageId ) ){
            $foundStatus 	= get_post_status( $pageId );

            if( $foundStatus == 'publish' ){
                $page  = (int) $pageId;
            }
        }

        return $page;
    }

    /**
     * Retorna o endereço da página de perfil de uma acompanhante
     * 
     * @return mixed    $url    Retorna o endereço da página de perfil
     */
    public function getProfileLink()
    {
        $page   = $this->getPageId();
        $url    = false;

        if( $page ){
            $url  = get_permalink( $page );
        }

        return $url;
    }

    /**
     * Retorna o endereço da página de perfil baseado no id passado como parâmetro
     * 
     * @param int       $id     Id da página de perfil
     * 
     * @return mixed    $url    Link da página de perfil    
     */
    public function getProfileLinkById( $id )
    {
        $url    = false;
        if( is_int($id) ){
            $url  = get_permalink( $id );
        }

        return $url;
    }

    /**
     * Retorna a idade da acompanhante
     * 
     * @return int      $age    Retorna a idade de uma acompanhante
     */
    public function getAge()
    {
        $birthday 	= get_user_meta( $this->currentUserId, $this->metaKeyBirthday, true );
		$age 		= null;
		if( $birthday ){
			$arr 	= explode('-', $birthday);
			$bYear 	= (int) $arr[0];
			$bMonth = (int) $arr[1];
			$bDay 	= (int) $arr[2];

			$age 	= $this->currYear - $bYear;
			if( $this->currMonth < $bMonth ){
				$age--;
			}elseif( $this->currMonth == $bMonth && $this->currDay <= $bDay ){
				$age--;
			}
		}

		return $age;
    }

    /**
     * Retorna a idade da acompanhante com base no ID da página passado como parâmetro
     * 
     * @param int   $id     Id da página de perfil
     */
    public function getAgeById( $id )
    {
        $birthday 	= get_post_meta( $id, $this->metaKeyBirthday, true );
		$age 		= null;
		if( !is_wp_error($birthday) ){
			$arr 	= explode('-', $birthday);
			$bYear 	= (int) $arr[0];
			$bMonth = (int) $arr[1];
			$bDay 	= (int) $arr[2];

			$age 	= $this->currYear - $bYear;
			if( $this->currMonth < $bMonth ){
				$age--;
			}elseif( $this->currMonth == $bMonth && $this->currDay <= $bDay ){
				$age--;
			}
		}

		return $age;
    }

    /**
     * Retorna uma coleção de imagens da galeria de fotos da acompanhante
     * 
     * @return mixed    $imgs   Retorna um array de imagens
     */
    public function getGallery()
    {
        $pageId = $this->getPageId();
        $imgs   = array(); 
        if( $pageId ){
            $imgs = get_post_meta( $pageId, $this->metaKeyGallery )[0];
        }
        
        return $imgs;
    }

    /**
     * Retorna uma coleção de imagens da galeria de fotos da acompanhantes 
     * com basae no ID da página passado como parâmetro
     * 
     * @param int       $id     Id da página de perfil
     * 
     * @return array    $imgs   Array com lista de urls da galeria
     */
    public function getGalleryById( $id )
    {
        $imgs   = array(); 
        if( is_int($id) ){
            $imgs = get_post_meta( $id, $this->metaKeyGallery )[0];
        }
        
        return $imgs;
    }

    /**
     * Retorna o genêro do perfil
     * 
     * 
     * @return string   $genre  Genêro do perfil
     */
    public function getGenre()
    {
        $id         = $this->getPageId();
        $taxObj     = get_the_terms( $id, $this->taxGenre );
        $genre      = '';
        if( !is_wp_error($taxObj) ){
            $genre  = join('', wp_list_pluck($taxObj, 'name') );
        }

        return $genre;
    }

    /**
     * Retorna o genêro do perfil baseado no ID da página passada como parâmetro
     * 
     * @param int       $id     Id da página de perfil
     * 
     * @return string   $genre  Genêro do perfil
     */
    public function getGenreById( $id )
    {
        $taxObj     = get_the_terms( $id, $this->taxGenre );
        $genre      = '';
        if( !is_wp_error($taxObj) ){
            $genre  = join('', wp_list_pluck($taxObj, 'name') );
        }

        return $genre;
    }

    /**
	 * Retorna o status atual da verificação de perfil
	 * 
	 * @param int 	    $id     Id do usuário
     * 
     * @return string   $value  Status da verificação
	 */
	public function getVerifyStatus()
	{
		$data 	= get_user_meta( $this->currentUserId, $this->metaKeyVerified, true );
        $value  = '';

        if( !is_wp_error($data) ){
            $value = $data;
        }

		return $value;
	}
}