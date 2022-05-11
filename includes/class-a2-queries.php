<?php

/**
 * Este arquivo define a classe A2_Query
 * Está classe recebe métodos e executa tarefas relacionadas aos anúncios da plataforma.
 * 
 * @package    A2
 * @subpackage A2/includes
 * @since      1.0.0
 * @author     jnz93 <box@unitycode.tech>
*/
class A2_Query{
    
    /**
     * Time now timestamp
     */
    private $now;

    /**
     * Meta key expiration
     */
    private $metaKeyExpiration;

    /**
     * Tax key localization
     */
    private $taxKeyLocalization;

    /**
     * Tax key plan level
     */
    private $taxKeyPlanLevel;

    public function __construct()
    {
		date_default_timezone_set('America/Sao_Paulo'); # Setando GMT padrão

        $this->now                  = time();
        $this->metaKeyExpiration    = '_expiration_date';
        $this->taxKeyLocalization   = 'profile_localization';
        $this->taxKeyPlanLevel      = 'advertisement_level';
    }

    /**
     * Retorna query com anúncios baseado na localização
     * Esse método é chamado especialmente no sistema de carousel
     * 
     * @param string    $country    sigla do pais
     * @param string    $state      sigla do estado
     * @param string    $city       nome da cidade
     * @param integer   $qty        total de anúncios que a query deve retornar
     * 
     * @return object   $query      objeto wp contendo anúncios do banco
     */
    public function advByLocation($country, $state, $city, $qty)
    {
        // WP_Query arguments
        $type   = 'a2_advertisement';
        $status = 'publish';
        $order  = 'DESC';
        $orderBy = 'data';

        $args   = array(
            'post_type'         => $type,       // use any for any kind of post type, custom post type slug for custom post type
            'post_status'       => $status,     // Also support: pending, draft, auto-draft, future, private, inherit, trash, any
            'posts_per_page'    => $qty,        // use -1 for all post
            'order'             => $order,      // Also support: ASC
            'orderby'           => $orderBy,    // Also support: none, rand, id, title, slug, modified, parent, menu_order, comment_count
            'meta_query'        => [
                [
                    'key' 		=> $this->metaKeyExpiration,
                    'value' 	=> $this->now,
                    'type' 	    => 'NUMERIC',
                    'compare' 	=> '>'
                ]
            ]
        );

        if( strlen($country) > 1 ){
            $args['tax_query'] = [
                'relation' => 'AND',
                [
                    'taxonomy' => $this->taxKeyLocalization,
                    'field'    => 'slug',
                    'terms'    => $country,
                ],
            ];

            if( !is_null($state)  ){
                $args['tax_query'][] = [
                    'taxonomy' => $this->taxKeyLocalization,
                    'field'    => 'slug',
                    'terms'    => $state,
                ];
            }
    
            if( !is_null($city) ){
                $args['tax_query'][] = [
                    'taxonomy' => $this->taxKeyLocalization,
                    'field'    => 'name',
                    'terms'    => $city,
                ];
            }
        }
        $query = new WP_Query($args);

        return $query;
    }

    /**
     * Retorna query com anúncios baseados na localização
     * A ordem de retorno é filtrada conforme o nível do anúncios adquirido(prata, ouro ou diamante)
     * 
     * @param string    $country    sigla do pais
     * @param string    $state      sigla do estado
     * @param string    $city       nome da cidade
     * @param integer   $qty        total de anúncios que a query deve retornar
     * 
     * @return object   $query      objeto wp contendo anúncios do banco
     */
    public function advByPlanLevel( $country, $state, $city, $qty )
    {
        // WP_Query arguments
        $type       = 'a2_advertisement';
        $status     = 'publish';
        $order      = 'DESC';
        $orderBy    = 'meta_value_num';
        $metaKey    = '_plan_level';

        $args   = array(
            'post_type'         => $type,       // use any for any kind of post type, custom post type slug for custom post type
            'post_status'       => $status,     // Also support: pending, draft, auto-draft, future, private, inherit, trash, any
            'posts_per_page'    => $qty,        // use -1 for all post
            'order'             => $order,      // Also support: ASC
            'orderby'           => $orderBy,    // Also support: none, rand, id, title, slug, modified, parent, menu_order, comment_count
            'meta_key'          => $metaKey,
            'meta_query'        => [
                [
                    'key' 		=> $this->metaKeyExpiration,
                    'value' 	=> $this->now,
                    'type' 	    => 'NUMERIC',
                    'compare' 	=> '>'
                ]
            ],
            'meta_query'        => [
                [
                    'key'           => $metaKey,
                    'value'         => [1, 2, 3], # 1=prata; 2=ouro; 3=diamante
                    'compare'       => 'IN',
                ]
            ],
        );

        if( strlen($country) > 1 ){
            $args['tax_query'] = [
                'relation' => 'AND',
                [
                    'taxonomy' => $this->taxKeyLocalization,
                    'field'    => 'slug',
                    'terms'    => $country,
                ],
            ];

            if( !is_null($state)  ){
                $args['tax_query'][] = [
                    'taxonomy' => $this->taxKeyLocalization,
                    'field'    => 'slug',
                    'terms'    => $state,
                ];
            }
    
            if( !is_null($city) ){
                $args['tax_query'][] = [
                    'taxonomy' => $this->taxKeyLocalization,
                    'field'    => 'name',
                    'terms'    => $city,
                ];
            }
        }
        $query = new WP_Query($args);

        return $query;
    }

    /**
     * Retorna a lista de cidades registradas no site
     * 
     * @param
     * @return  array       lista de cidades
     */
    public function getCities()
    {
        $taxonomy   = 'profile_localization';
        $statesBRObj   = get_terms(
            [
                'taxonomy'      => $taxonomy,
                'hide_empty'    => false,
                'orderby'       => 'name',
                'order'         => 'ASC',
                'parent'        => 195, # ID Brasil(depois vamos precisar organizar por países assim como por estado);
            ]
        );
        
        // Pegar as cidades filhas dos $statesBR
        $citiesBRObj = array();
        if( !empty($statesBR) ){
            foreach( $statesBR as $state ){
                $slug   = $state['slug'];
                $parent = $state['id'];

                $citiesBRObj[$slug] = get_terms(
                    [
                        'taxonomy'      => $taxonomy,
                        'hide_empty'    => false,
                        'orderby'       => 'name',
                        'order'         => 'ASC',
                        'parent'        => $parent
                    ]
                );
            }
        }

        // Tratamento das cidades
        $citiesBR = array();
        if( !empty($citiesBRObj) ){
            foreach( $citiesBRObj as $state => $cities ){
                if( !empty($cities) ){
                    foreach( $cities as $cityObj ){
                        $citiesBR[] = $cityObj->name . ' - ' . strtoupper($state);
                    }
                }
            }
        }

        return $citiesBR;
    }
}