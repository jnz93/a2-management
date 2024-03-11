<?php

class SL_RestWorkers{
    public function __construct()
    {
        $this->registerClass = new A2_Register();
        $this->profileClass = new A2_Profile();

        $this->namespace = 'sl/v1';
        $this->routes = [
            '/cadastrar-acompanhante/' => [
                'methods'               => ['POST'],
                'callback'              => [$this, 'registerEscort'],
                'permission_callback'   => '__return_true'
            ],
            '/profile-update/' => [
                'methods'               => ['POST'],
                'callback'              => [$this, 'updateProfile'],
                'permission_callback'   => '__return_true'
            ],
            '/profile-update-image/' => [
                'methods'               => ['POST'],
                'callback'              => [$this, 'updateProfileImage'],
                'permission_callback'   => '__return_true'
            ],
        ];

        add_action('rest_api_init', function(){
            foreach($this->routes as $endpoint => $args){
                register_rest_route($this->namespace, $endpoint, $args);
            }
        });
    }

    public function registerEscort(WP_REST_Request $request)
    {
        $params = $request->get_params();
        // if(!wp_verify_nonce($params['svdt_form_producer_nonce'], 'register_form_producer_event')) return new WP_REST_Response(array('error' => 'Nonce Inválido!'), 400);
     
        $name               = sanitize_text_field($params['sl_escort_full_name']);
        $email              = sanitize_email($params['sl_escort_email']);
        $password           = sanitize_text_field($params['sl_escort_password']);
        $termsAgree         = $params['sl_escort_terms_agree'];
        $ageConfirmation    = $params['sl_escort_age_confirmation'];
        $userType           = $params['sl_user_type'];
        
        $userId = $this->registerClass->addUser($name, $email, $password, $userType);
        if(is_wp_error($userId)) return new WP_REST_Response(['success' => false, 'msg' => $userId->get_error_message()], 200);

        $creds = array(
            'user_login'    => $email,
            'user_password' => $password,
            'remember'      => true,
        );
        $user = wp_signon($creds, false);
        if(is_wp_error($user)) return new WP_REST_Response(array('success' => false, 'msg' => $user->get_error_message()), 200); 
        
        $minhaContaID = wc_get_page_id('myaccount');
        $minhaContaURL = get_permalink($minhaContaID);
        return new WP_REST_Response( ['success' => true, 'url' => $minhaContaURL, 'msg' => 'Cadastro realizado com sucesso!'], 200);
    }

    /**
     * Recebe os dados e faz a solicitação do update do perfil do usuário
     */
    public function updateProfile($request)
    {
        $params = $request->get_params();
        $userId = $params['user_id'];
        $result = $this->profileClass->saveData($params, $userId);

        return new WP_REST_Response( ['success' => true, 'msg' => 'Perfil atualizado com sucesso!', 'log' => $result], 200);
    }
    // $user = ['account_first_name','account_last_name','account_display_name','account_email']
    // metas = ['_profile_whatsapp','_profile_birthday','_profile_description,_profile_height,_profile_weight,_profile_eye_color,_profile_hair_color,_profile_tits_size,_profile_bust_size,_profile_waist_size,'_profile_instagram,_profile_tiktok,_profile_onlyfans,_profile_address,_profile_cep,]
    // [_profile_ethnicity,_profile_genre,_profile_sign,_profile_he_meets,_profile_services,_profile_place,_profile_specialties,_profile_languages,_profile_country,_profile_state,_profile_city,_profile_district,]

    
    /**
     * Faz upload das imagens de perfil e capa de acompanhantes
     * 
     */
    public function updateProfileImage($request)
    {
        $params     = $request->get_params();
        $fileParams = $request->get_file_params();
        $nonce      = $params['nonce'];
        $userId     = $params['user_id'];
        $type       = $params['type'];
        $file       = $fileParams['file'];

        if(!isset($file['error']) || $file['error']) return new WP_REST_Response(['status' => false, 'msg' => 'Ocorreu um erro ao carregar o arquivo.', 'data' => $params], 403);
        
        // if(!wp_verify_nonce($nonce, 'sl_profile_update_photo')) return new WP_REST_Response(['status' => false, 'msg' => 'Autenticação nonce falhou.', 'data' => $params], 403);

        $allowedTypes   = ['jpg', 'jpeg', 'png'];
        $fileType       = wp_check_filetype($file['name'], null);
        if(!in_array($fileType['ext'], $allowedTypes)) return new WP_REST_Response(['status' => false, 'msg' => 'Arquivo inválido. Apenas jpg, jpeg e png são permitidos.'], 403);

        if(!function_exists('media_handle_sideload')){
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/file.php' );
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/image.php' );
            require_once( plugin_dir_path( ABSPATH ) . 'public_html/wp-admin/includes/media.php' ); 
        }

        $fileData = [
            'name'      => $file['name'],
            'type'      => $fileType['type'],
            'tmp_name'  => $file['tmp_name'],
            'error'     => 0,
            'size'      => $file['size']
        ];
        $attachId = media_handle_sideload($fileData, 0);
        if(is_wp_error($attachId)) return new WP_REST_Response(['status' => false, 'msg' => 'upload_error: Ocorreu um erro ao enviar o arquivo.'], 403);

        // update da foto no perfil
        if($type == '_select_profile_photo'){
            $updateItem = $this->profileClass->updateProfilePhoto($attachId, $userId);
        }

        if($type == '_select_profile_cover'){
            $updateItem = $this->profileClass->updateProfileCover($attachId, $userId);
        }
        $attachUrl = wp_get_attachment_url($attachId);

        return new WP_REST_Response(['status' => true, 'msg' => 'Item atualizado com sucesso!', 'url' => $attachUrl], 200);
    }
}