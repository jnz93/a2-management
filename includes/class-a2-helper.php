<?php

/**
 * Classe com métodos genéricos e váriados que são utilizados para auxiliar
 * em determinadas tarefas.
 *
 * @link       unitycode.tech
 * @since      1.0.0
 *
 * @package    A2
 * @subpackage A2/admin
 */
class A2_Helper{

    public function __construct()
    {
        $this->apiIBGE = 'https://servicodados.ibge.gov.br/api/v1/';
    }

    
    /**
     * Este método se conecta a API do IBGE para retornar os estados brasileiros, incluindo DF.
     * 
     * @return array    $body   Coleção de estados  
     */
    public function getStatesFromIBGE()
    {
        try {
            $url        = $this->apiIBGE . 'localidades/estados?view=nivelado';
            $response   = wp_remote_get( $url );
            $body       = null;

            if( !is_wp_error( $response ) && ( 200 === wp_remote_retrieve_response_code( $response ) ) ){
                $body = wp_remote_retrieve_body( $response );
                if( json_last_error() === JSON_ERROR_NONE ){
                    $body = json_decode($body);
                }
            }

            return $body;

        } catch ( Exception $ex ) {
            throw $ex;
        }
    }

    /**
     * Método responsável por adicionar estados a taxonomia profile_localization
     * 
     * @return void
     */
    public function addStateTerm()
    {
        $states     = $this->getStatesFromIBGE();
        $taxonomy   = 'profile_localization';
        $args       = [
            'parent'    => 195, # Brasil
        ];

        if( !is_null( $states ) ){
            foreach( $states as $state ){
                $state          = (array) $state;
                $stateSlug      = $state['UF-sigla'];
                $stateName      = $state['UF-nome'];
                $stateID        = $state['UF-id'];
                $regionID       = $state['regiao-id'];
                $regionSlug     = $state['regiao-sigla'];
                $regionName     = $state['regiao-nome'];

                $termID         = null;
                $termExists     = term_exists( $stateName, $taxonomy );
                if( $termExists ){
                    $termID     = $termExists['term_id'];
                } else {
                    $args['slug']   = $stateSlug;
                    $term           = wp_insert_term( $stateName, $taxonomy, $args );

                    if( !is_wp_error( $term ) ){
                        $termID = $term['term_id'];
                    }
                }

                # Updating Term Data
                $metas = [
                    'uf_slug'       => $stateSlug,
                    'uf_id'         => $stateID,
                    'region_name'   => $regionName,
                    'region_slug'   => $regionSlug,
                    'region_id'     => $regionID,
                ];

                if( $termID ){
                    foreach( $metas as $key => $value )
                    {
                        update_term_meta( $termID, $key, $value );
                    }
                }
            }
        }
        
    }

    /**
     * Este método se conecta a API do IBGE para retornar cidades conforme o estado solicidato
     * no parâmetro.
     * 
     * @param string    $uf     Sigla do estado brasileiro
     * @return array    $body   Coleção de cidades  
     */
    public function getCitiesByUfFromIBGE( $uf )
    {
        try {
            $url        = $this->apiIBGE . 'localidades/estados/'. $uf .'/municipios?view=nivelado';
            $response   = wp_remote_get( $url );
            $body       = null;

            if( !is_wp_error( $response ) && ( 200 === wp_remote_retrieve_response_code( $response ) ) ){
                $body = wp_remote_retrieve_body( $response );
                if( json_last_error() === JSON_ERROR_NONE ){
                    $body = json_decode($body);
                }
            }

            return $body;

        } catch ( Exception $ex ) {
            throw $ex;
        }
    }

    /**
     * Método responsável por adicionar cidades à taxonomia profile_localization
     * 
     * @param array     $city       Array com os dados do município direto do IBGE
     * @param integer   $parentID   Id do termo pai da taxonomia
     */
    public function addCityTerm( $city, $parentID )
    {
        $taxonomy               = 'profile_localization';
        $args                   = [
            'parent'    => $parentID,
        ];

        # City Data
        $cityID                 = $city['municipio-id'];
        $cityName               = $city['municipio-nome'];
        $regionName             = $city['regiao-nome'];
        $microRegionName        = $city['microrregiao-nome'];
        $mesoRegionName         = $city['mesorregiao-nome'];
        $immediateRegionName    = $city['regiao-imediata-nome'];
        $intermediateRegionName = $city['regiao-intermediaria-nome'];
        
        $termID         = null;
        $termExists     = term_exists( $cityName, $taxonomy );
        if( $termExists ){
            $termID     = $termExists['term_id'];
        } else {
            $term       = wp_insert_term( $cityName, $taxonomy, $args );

            if( !is_wp_error( $term ) ){
                $termID = $term['term_id'];
            }
        }

        # Updating Term Data
        $metas = [
            'city_id'                   => $cityID,
            'region_name'               => $regionName,
            'microregion_name'          => $microRegionName,
            'mesoregion_name'           => $mesoRegionName,
            'immediateregion_name'      => $immediateRegionName,
            'intermediateregion_name'   => $intermediateRegionName,
        ];

        if( $termID ){
            echo '<b>'. $cityName .'</b> importada com sucesso! </br>';
            foreach( $metas as $key => $value )
            {
                update_term_meta( $termID, $key, $value );
                echo '<b>'. $key .'</b> meta dado salvo! </br>';
            }
        }
    }

    /**
     * Método que importa cidades da API do IBGE e salva na taxonomia "profile_localization"
     * 
     */
    public function importCitiesFromIBGE()
    {
        $states = $this->getChildrenTerms( 195, 'profile_localization' );
        // var_dump($states);

        if( !is_wp_error( $states) ){
            foreach( $states as $state ){
                $uf         = $state->slug;
                $stateID    = $state->term_id;
                $cities     = $this->getCitiesByUfFromIBGE($uf);

                # Temp
                $imported   = ['ac', 'al', 'ap', 'am', 'ba', 'ce', 'df', 'es', 'go', 'ma', 'mt', 'ms', 'mg', 'pa', 'pb', 'pr', 'pi', 'rj'];

                if( !empty($cities) && !in_array(strtolower($uf), $imported) ){
                    foreach( $cities as $city ){
                        $city = (array) $city;
                        $this->addCityTerm($city, $stateID);
                    }
                }

                echo '--------------------------------------------------------------- FIM '. $uf .' </br>';
            }
        }
    }
    
    /**
     * Método retonar children terms do parent term passado como parâmetro
     * 
     * @param integer   $id         Id do termo pai
     * @param string    $taxonomy   Taxonomia de termos
     * @return 
     */
    public function getChildrenTerms( $id, $taxonomy )
    {
		$args   = array(
			'taxonomy'		=> $taxonomy,
			'parent'		=> $id,
			'hide_empty'	=> false,
			'orderby'		=> 'name',
			'order'			=> 'ASC'
		);
		$terms  = get_terms( $args );

        return $terms;
    }
}