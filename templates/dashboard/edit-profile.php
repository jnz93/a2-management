<?php
/**
 * Template: my-gallery
 *
 * @package a2 plugin
 */
do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<div class="row mb-4">
		<div class="file-field col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 d-flex" style="flex-flow: column;align-items: start;">
			<div class="thumbnail-view mb-1" style="width: 140px; height: 140px; background-color: gray; background-position: center;background-repeat: no-repeat;background-size: cover; display: flex; align-items: center; justify-content: center; <?php echo strlen($profilePhotoUrl) != 0 ? 'background-image: url(' . $profilePhotoUrl . ')' : ''; ?>">
				<div class="spinner-border spinner-border-sm d-none" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
			</div>
			<div class="mb-3">
				<label for="_select_profile_photo" class="form-label">Selecione a foto de Perfil</label>
				<input class="form-control" type="file" accept="image/png, image/jpeg" id="_select_profile_photo">
				<input type="hidden" name="_profile_photo" id="_profile_photo" value="<?php echo strlen($userData['_profile_photo']) != 0 ? $userData['_profile_photo'] : '' ?>">
			</div>
		</div>

		<div class="file-field col-xs-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 col-xxl-8 d-flex" style="flex-flow: column;align-items: start;">
			<div class="thumbnail-view mb-1" style="width: 100%; height: 140px; background-color: gray; background-position: center;background-repeat: no-repeat;background-size: cover; display: flex; align-items: center; justify-content: center; <?php echo strlen($profileCoverUrl) != 0 ? 'background-image: url(' . $profileCoverUrl . ')' : ''; ?>">
				<div class="spinner-border spinner-border-sm d-none" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
			</div>
			<div class="mb-3">
				<label for="_select_profile_cover" class="form-label">Selecione a foto de Capa</label>
				<input class="form-control" type="file" accept="image/png, image/jpeg" id="_select_profile_cover">
				<input type="hidden" name="_profile_cover" id="_profile_cover" value="<?php echo strlen($userData['_profile_cover']) != 0 ? $userData['_profile_cover'] : '' ?>">
			</div>
		</div>
	</div>

	<!-- Informações pessoais -->
	<div class="row mb-4">
		<h5 class=""><?php echo __( 'Informações Pessoais', 'textdomain' ); ?></h5>
		<div class="clear"></div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
			<label for="account_first_name" class="form-label"><?php echo __( 'Primeiro nome', 'textdomain' ) ?></label>
			<input name="account_first_name" id="account_first_name" type="text" class="form-control" value="<?php echo esc_attr( $user->first_name ); ?>">
		</div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
			<label for="account_last_name" class="form-label"><?php echo __( 'Sobrenome', 'textdomain' ) ?></label>
			<input name="account_last_name" id="account_last_name" type="text" class="form-control" value="<?php echo esc_attr( $user->last_name ); ?>">
		</div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
			<label for="account_display_name" class="form-label"><?php echo __( 'Nome de exibição / Apelido', 'textdomain' ) ?></label>
			<input name="account_display_name" id="account_display_name" type="text" class="form-control" value="<?php echo esc_attr( $user->display_name ); ?>">
		</div>
		
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
			<label for="account_email" class="form-label"><?php echo __( 'E-mail', 'textdomain' ) ?></label>
			<input name="account_email" id="account_email" type="email" class="form-control" value="<?php echo esc_attr( $user->user_email ); ?>">
		</div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
			<label for="_profile_whatsapp" class="form-label"><?php echo __( 'Telefone/Whatsapp', 'textdomain' ) ?></label>
			<input name="_profile_whatsapp" id="_profile_whatsapp" type="tel" class="form-control" value="<?php echo esc_attr( $userData['_profile_whatsapp'] ); ?>">
		</div>

		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
			<label for="_profile_birthday" class="form-label"><?php echo __( 'Data de Nascimento', 'textdomain' ) ?></label>
			<input name="_profile_birthday" id="_profile_birthday" type="date" class="form-control" value="<?php echo esc_attr( $userData['_profile_birthday'] ); ?>">
		</div>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 mb-3">
			<label for="_profile_description" class="form-label"><?php echo __( 'Descrição', 'textdomain' ) ?></label>
			<textarea name="_profile_description" id="_profile_description" class="materialize-textarea"><?php echo esc_attr( $userData['_profile_description'] ); ?></textarea>
		</div>
	</div>

	<!-- Características -->
	<?php if( current_user_can( 'a2_scort') ): ?>
		<div class="row mb-4">
			<h5 class=""><?php echo __( 'Características', 'textdomain' ); ?></h5>
			<div class="clear"></div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_height" class="form-label"><?php echo __( 'Altura', 'textdomain' ) ?></label>
				<input name="_profile_height" id="_profile_height" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_height'] ); ?>">
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_weight" class="form-label"><?php echo __( 'Peso', 'textdomain' ) ?></label>
				<input name="_profile_weight" id="_profile_weight" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_weight'] ); ?>">
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_eye_color" class="form-label"><?php echo __( 'Cor dos Olhos', 'textdomain' ) ?></label>
				<input name="_profile_eye_color" id="_profile_eye_color" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_eye_color'] ); ?>">
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_hair_color" class="form-label"><?php echo __( 'Cor do Cabelo', 'textdomain' ) ?></label>
				<input name="_profile_hair_color" id="_profile_hair_color" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_hair_color'] ); ?>">
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_tits_size" class="form-label"><?php echo __( 'Tamanho dos seios', 'textdomain' ) ?></label>
				<input name="_profile_tits_size" id="_profile_tits_size" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_tits_size'] ); ?>">
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_bust_size" class="form-label"><?php echo __( 'Busto', 'textdomain' ) ?></label>
				<input name="_profile_bust_size" id="_profile_bust_size" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_bust_size'] ); ?>">
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_waist_size" class="form-label"><?php echo __( 'Cintura', 'textdomain' ) ?></label>
				<input name="_profile_waist_size" id="_profile_waist_size" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_waist_size'] ); ?>">
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_ethnicity" class="form-label"><?php echo __( 'Etnia', 'textdomain' ) ?></label>
				<select name="_profile_ethnicity" id="_profile_ethnicity" class="form-select validate">
					<option value="" disabled selected><?php echo __( 'Etnia') ?></option>
					<?php 
						if( !empty( $etnias ) ){
							foreach( $etnias as $item ){
								echo '<option value="'. $item->name .'"';
								if( $item->name == $userData['_profile_ethnicity'] ){
									echo 'selected';
								}
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_genre" class="form-label"><?php echo __( 'Gênero', 'textdomain' ) ?></label>
				<select name="_profile_genre" id="_profile_genre" class="form-select validate">
					<option value="" disabled selected><?php echo __( 'Gênero', 'textdomain' ) ?></option>
					<?php 
						if( !empty( $generos ) ){
							foreach( $generos as $item ){
								echo '<option value="'. $item->name .'"';
								if( $item->name == $userData['_profile_genre'] ){
									echo 'selected';
								}
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_sign" class="form-label"><?php echo __( 'Signo', 'textdomain' ) ?></label>
				<select name="_profile_sign" id="_profile_sign" class="form-select validate">
					<option value="" disabled selected><?php echo __( 'Signo', 'textdomain' ) ?></option>
					<?php 
						if( !empty( $signos ) ){
							foreach( $signos as $item ){
								echo '<option value="'. $item->name .'"';
								if( $item->name == $userData['_profile_sign'] ){
									echo 'selected';
								}
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_he_meets" class="form-label d-block"><?php echo __( 'Atende', 'textdomain' ) ?></label>
				<select name="_profile_he_meets[]" id="_profile_he_meets" class="selectpicker" multiple data-live-search="true">
					<option value="" disabled selected><?php echo __( 'Atende', 'textdomain' ) ?></option>
					<?php
						if( !empty( $preferencias ) ){
							foreach( $preferencias as $item ){
								echo '<option value="'. $item->name .'"';
								if( is_array($userData['_profile_he_meets']) && in_array($item->name, $userData['_profile_he_meets']) ){
									echo 'selected';
								}
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_services" class="form-label d-block"><?php echo __( 'Serviços', 'textdomain' ) ?></label>
				<select name="_profile_services[]" id="_profile_services" class="selectpicker" multiple data-live-search="true">
					<option value="" disabled selected><?php echo __( 'Serviços', 'textdomain' ) ?></option>
					<?php 
						if( !empty( $servicos ) ){
							foreach( $servicos as $item ){
								echo '<option value="'. $item->name .'"';
								if( is_array($userData['_profile_services']) && in_array($item->name, $userData['_profile_services']) ){
									echo 'selected';
								}
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_place" class="form-label d-block"><?php echo __( 'Local de atendimento', 'textdomain' ) ?></label>	
				<select name="_profile_place[]" id="_profile_place" class="selectpicker" multiple data-live-search="true">
					<option value="" disabled selected><?php echo __( 'Local de atendimento', 'textdomain' ) ?></option>
					<?php 
						if( !empty( $locaisAtendimento ) ){
							foreach( $locaisAtendimento as $item ){
								echo '<option value="'. $item->name .'"';
								if( is_array($userData['_profile_place']) && in_array($item->name, $userData['_profile_place']) ){
									echo 'selected';
								}
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_specialties" class="form-label d-block"><?php echo __( 'Especialidades', 'textdomain' ) ?></label>
				<select name="_profile_specialties[]" id="_profile_specialties" class="selectpicker" multiple data-live-search="true">
					<option value="" disabled selected><?php echo __( 'Especialidades', 'textdomain' ) ?></option>
					<?php 
						if( !empty( $especialidades ) ){
							foreach( $especialidades as $item ){
								echo '<option value="'. $item->name .'"';
								if( is_array($userData['_profile_specialties']) && in_array($item->name, $userData['_profile_specialties']) ){
									echo 'selected';
								}
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_languages" class="form-label d-block"><?php echo __( 'Idiomas', 'textdomain' ) ?></label>
				<select name="_profile_languages[]" id="_profile_languages" class="selectpicker" multiple data-live-search="true">
					<option value="" disabled selected><?php echo __( 'Idiomas', 'textdomain' ) ?></option>
					<?php 
						if( !empty( $idiomas ) ){
							foreach( $idiomas as $item ){
								echo '<option value="'. $item->name .'"';
								if( is_array($userData['_profile_languages']) && in_array($item->name, $userData['_profile_languages']) ){
									echo 'selected';
								}
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>
		</div>
		
		<!-- Redes sociais -->
		<div class="row mb-4">
			<h5 class=""><?php _e( 'Redes Sociais', 'textdomain' ) ?></h5>
			<div class="clear"></div>
				
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6">					
				<label for="_profile_instagram" class="form-label"><?php echo __( 'Instagram', 'textdomain' ) ?></label>
				<div class="input-group mb-3">
					<span class="input-group-text" id="_profile_instagram_addon">https://instagram.com/</span>
					<input name="_profile_instagram" id="_profile_instagram" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_instagram'] ); ?>" aria-describedby="_profile_instagram_addon">
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
				<label for="_profile_tiktok" class="form-label"><?php echo __( 'TikTok', 'textdomain' ) ?></label>
				<div class="input-group mb-3">
					<span class="input-group-text" id="_profile_tiktok_addon">https://tiktok.com/</span>
					<input name="_profile_tiktok" id="_profile_tiktok" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_tiktok'] ); ?>" aria-describedby="_profile_tiktok_addon">
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6">
				<label for="_profile_onlyfans" class="form-label"><?php echo __( 'OnlyFans', 'textdomain' ) ?></label>
				<div class="input-group mb-3">
					<span class="input-group-text" id="_profile_onlyfans_addon">https://onlyfans.com/</span>
					<input name="_profile_onlyfans" id="_profile_onlyfans" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_onlyfans'] ); ?>" aria-describedby="_profile_tiktok_addon">
				</div>
			</div>
		</div>

		<!-- Endereço -->
		<div class="row mb-4">
			<h5 class=""><?php echo __( 'Endereço', 'textdomain' ); ?></h5>
			<div class="clear"></div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_country" class="form-label"><?php echo __( 'Selecione o País', 'textdomain' ) ?></label>
				<select name="_profile_country" id="_profile_country" class="form-select" onchange="getLocalizationChildrenTerms( jQuery(this) )">
					<option value="" disabled selected><?php echo __( 'País', 'textdomain' ) ?></option>
					<?php 
						if( !empty( $localizacao ) ){
							foreach( $localizacao as $item ){
								echo '<option value="'. $item->term_id .'" term-id="'. $item->term_id .'" children-type="states"';
								// if( $item->term_id == $userData['_profile_country'] ){
								// 	echo 'selected';
								// }
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_state" class="form-label"><?php echo __( 'Selecione o Estado', 'textdomain' ) ?></label>
				<select name="_profile_state" id="_profile_state" class="form-select" onchange="getLocalizationChildrenTerms( jQuery(this) )" disabled>
					<option value="" disabled selected><?php echo __( 'Estado', 'textdomain' ) ?></option>
				</select>
			</div>
			
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_city" class="form-label"><?php echo __( 'Selecione a Cidade', 'textdomain' ) ?></label>
				<select name="_profile_city" id="_profile_city" class="form-select" onchange="getLocalizationChildrenTerms( jQuery(this) )" disabled>
					<option value="" disabled selected><?php echo __( 'Cidade', 'textdomain' ) ?></option>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_district" class="form-label"><?php echo __( 'Selecione o Bairro', 'textdomain' ) ?></label>
				<select name="_profile_district" id="_profile_district" class="form-select" onchange="getLocalizationChildrenTerms( jQuery(this) )" disabled>
					<option value="" disabled selected><?php echo __( 'Bairro', 'textdomain' ) ?></option>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 col-xl-8 col-xxl-8 mb-3">
				<label for="_profile_address" class="form-label"><?php echo __( 'Endereço', 'textdomain' ) ?></label>
				<input name="_profile_address" id="_profile_address" type="text" class="form-input" value="<?php echo esc_attr( $userData['_profile_address'] ); ?>">
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_cep" class="form-label"><?php echo __( 'CEP', 'textdomain' ) ?></label>
				<input name="_profile_cep" id="_profile_cep" type="text" class="form-input" value="<?php echo esc_attr( $userData['_profile_cep'] ); ?>">
			</div>
		</div>
		
		<!-- Valores -->
		<div class="row mb-4">
			<h5 class=""><?php echo __( 'Valores', 'textdomain' ); ?></h5>
			<div class="clear"></div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_cache_quickie" class="form-label"><?php echo __( 'Cachê Rapinha', 'textdomain' ) ?></label>
				<div class="input-group mb-3">
					<span class="input-group-text" id="_profile_cache_quickie_addon">R$</span>
					<input name="_profile_cache_quickie" id="_profile_cache_quickie" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_cache_quickie'] ); ?>" aria-describedby="_profile_cache_quickie_addon">
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_cache_half_an_hour" class="form-label"><?php echo __( 'Cachê 30 Minutos', 'textdomain' ) ?></label>
				<div class="input-group mb-3">
					<span class="input-group-text" id="_profile_cache_half_an_hour_addon">R$</span>
					<input name="_profile_cache_half_an_hour" id="_profile_cache_half_an_hour" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_cache_half_an_hour'] ); ?>" aria-describedby="_profile_cache_half_an_hour_addon">
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_cache_hour" class="form-label"><?php echo __( 'Cachê 1 Hora', 'textdomain' ) ?></label>
				<div class="input-group mb-3">
					<span class="input-group-text" id="_profile_cache_hour_addon">R$</span>
					<input name="_profile_cache_hour" id="_profile_cache_hour" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_cache_hour'] ); ?>" aria-describedby="_profile_cache_hour_addon">
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 col-xl-4 col-xxl-4 mb-3">
				<label for="_profile_cache_overnight_stay" class="form-label"><?php echo __( 'Cachê Pernoite', 'textdomain' ) ?></label>
				<div class="input-group mb-3">
					<span class="input-group-text" id="_profile_cache_overnight_stay_addon">R$</span>
					<input name="_profile_cache_overnight_stay" id="_profile_cache_overnight_stay" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_cache_overnight_stay'] ); ?>" aria-describedby="_profile_cache_overnight_stay_addon">
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_cache_promotion" class="form-label"><?php echo __( 'Cachê Promoção', 'textdomain' ) ?></label>
				<div class="input-group mb-1">
					<span class="input-group-text" id="_profile_cache_promotion_addon">R$</span>
					<input name="_profile_cache_promotion" id="_profile_cache_promotion" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_cache_promotion'] ); ?>" aria-describedby="_profile_cache_promotion_addon">
				</div>

				<div class="form-check form-switch d-flex">
					<?php echo '<input name="_profile_cache_promotion_activated" id="_profile_cache_promotion_activated" class="form-check-input" type="checkbox" role="switch"';
							if( $userData['_profile_cache_promotion_activated'] == 'on' ){
								echo 'checked';
							}
						echo '>';
					?>
					<label class="form-check-label" for="_profile_cache_promotion_activated"><?php _e( 'Ativar preço promocional', 'textdomain'); ?></label>
				</div>
			</div>
		</div>

		<!-- Atendimento -->
		<div class="row mb-4">
			<h5 class=""><?php echo __( 'Atendimento', 'textdomain' ); ?></h5>
			<div class="clear"></div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 mb-3">
				<label for="_profile_work_days" class="form-label d-block"><?php echo __( 'Selecione os dias de trabalho', 'textdomain' ) ?></label>
				<select name="_profile_work_days[]" id="_profile_work_days"  class="selectpicker" multiple data-live-search="true">
					<option value="" disabled selected><?php echo __( 'Dias de Expediente', 'textdomain' ) ?></option>
					<?php 
						if( !empty( $diasTrabalho ) ){
							foreach( $diasTrabalho as $item ){
								echo '<option value="'. $item->name .'"';
								if( is_array($userData['_profile_work_days']) && in_array($item->name, $userData['_profile_work_days']) ){
									echo 'selected';
								}
								echo '>'. __( $item->name, 'textdomain' ) .'</option>';
							}
						}
					?>
				</select>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-6 col-xxl-6 mb-3">
				<label for="_profile_office_hour" class="form-label"><?php echo __( 'Horario de expediente', 'textdomain' ) ?></label>
				<input name="_profile_office_hour" id="_profile_office_hour" type="text" class="form-control" value="<?php echo esc_attr( $userData['_profile_office_hour'] ); ?>">
			</div>
		</div>

		<!-- Formas de pagamento -->
		<div class="row mb-4">
			<h5 class=""><?php echo __( 'Formas de pagamento', 'textdomain') ?></h5>
			<div class="clear"></div>

			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
				<label for="" class="form-label"><?php echo __( 'Selecione as formas de pagamento', 'textdomain' ) ?></label>
				<div class="row form-check form-switch d-flex">
					<?php 
						if( !empty( $paymentMethods ) ){
							foreach( $paymentMethods as $method ){
								echo '<div class="col-3">
										<input name="_profile_payment_method_'. $method->term_id .'" id="_profile_payment_method_'. $method->term_id .'" class="form-check-input" type="checkbox" role="switch"';
										if( $userData['_profile_payment_method_' . $method->term_id] == 'on' ){
											echo 'checked';
										}
										echo '> <label class="form-check-label" for="_profile_payment_method_'. $method->term_id .'">'. __( $method->name, 'textdomain') .'</label>
									</div>';
							}
						}
					?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>"><?php esc_html_e( 'Save changes', 'woocommerce' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>


<style>
	input:focus, textarea:focus{
		background: none !important;
	}
</style>
<script>
	// Inicialização select input 
	jQuery(document).ready(function(){
		profileActivateMasks();
	});
</script>