<?php 
/**
 * Template: navigation
 * @package a2 plugin
 */
?>
<!-- Corpo da página -->
<div class="row">
	<!-- Container #feedWrapper -->
	<div id="feedWrapper" class="col-xs-12 col-sm-12 col-md-12 col-lg-9 col-xl-9 col-xxl-9" style="min-height: 500px;">
		
		<?php if( current_user_can('a2_scort') ): ?>
			<div id="notifications" class="row content mb-4">

                <!-- Estatísticas -->
                <div class="col-12 mb-4">
                    <div class="h4 pb-2 mb-3 text-muted border-bottom"><?php _e( 'Estatísticas do perfil', 'textdomain' ); ?></div>
                    <div class="row mb-4">
                        <?php echo do_shortcode('[profileStatistics]'); ?>
                    </div>
                </div>

                <!-- Anúncios ativos -->
                <div class="col-12 mb-4">
                    <div class="h4 pb-2 mb-3 text-muted border-bottom"><?php _e( 'Anúncios ativos', 'textdomain' ); ?></div>
                    <div class="row mb-4">
                        <?php echo do_shortcode( '[activeAdvInfo]' ); ?>
                    </div>
                    <span class="text-muted"><?php _e( 'Você pode ter até 3 anúncios ativos.', 'textodmain' ); ?></span>
                </div>

				<div class="col-12 mb-3 <?php echo !$showNotifications ? 'd-none' : ''; ?>">
					<h5 class=""><?php _e( 'Notificações e Avisos', 'textdomain' ); ?></h5>
					<p class=""><?php _e( '', 'textodmain' ); ?></p>
				</div>
				<div class="col-12 <?php echo (strlen($verificationStatus) > 0 ? 'd-none' : '') ?>">
					<div class="alert alert-warning d-flex align-items-center" role="alert">
						<div class="col-9 d-flex">
							<i class="bi bi-exclamation-diamond-fill"></i>
							<div class="ms-2">
								<?php _e( 'Perfil não verificado. Solicite a análise de perfil e aguarde nossa avaliação dentro de 7 dias.', 'textdomain' ); ?>
							</div>
						</div>
						<div class="col-3 d-flex justify-content-end">
							<button type="button" class="btn btn-outline-primary p-1 btn-sm" data-bs-toggle="modal" data-bs-target="#validationProfileModal"><?php _e( 'Solicitar Análise', 'textdomain' ); ?></button>
						</div>
					</div>
				</div>
				<div class="col-12 <?php echo ( $profilePage ? 'd-none' : '' ) ?>">
					<div class="alert alert-warning d-flex align-items-center" role="alert">
						<div class="col-9 d-flex">
							<i class="bi bi-exclamation-diamond-fill"></i>
							<div class="ms-2">
								<?php _e( 'Sua página de acompanhante ainda não foi publicada. Configure seu perfil para publicar a página', 'textdomain' ); ?>
							</div>
						</div>
						<div class="col-3 d-flex justify-content-end">
							<a href="https://acompanhantesa2.com/painel/edit-account/" class="btn btn-outline-primary p-1 btn-sm"><?php _e( 'Editar perfil', 'textdomain' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div id="guides" class="row content">
			<div class="col-12 mb-2">
                <div class="h4 pb-2 mb-2 text-muted border-bottom"><?php _e( 'Guias', 'textdomain' ); ?></div>
				<p class="d-none"><?php echo __('Está com dúvidas sobre como funciona a plataforma A2 Acompanhantes? A gente te explica!', 'textdomain'); ?></p>
			</div>
			<?php
				if( $guides->have_posts() ){
					while( $guides->have_posts() ){
						$guides->the_post(); ?>
						<div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-1">
							<div class="card mb-2" style="cursor: pointer;">
								<a href="<?php echo get_the_permalink(); ?>" class="" target="_blank">
									<div class="row g-0">
										<div class="col-md-4">
											<img src="<?php echo get_the_post_thumbnail_url(); ?>" class="img-fluid rounded-start" alt="<?php _e( the_title(), 'textdomain' ); ?>">
										</div>
										<div class="col-md-8">
											<div class="card-body">
												<h5 class="card-title"><?php _e( the_title(), 'textdomain' ); ?></h5>
												<p class="card-text"><?php _e( wp_trim_words( get_the_excerpt(), 13, '...' ), 'textdomain' ); ?></p>
											</div>
										</div>
									</div>
								</a>
							</div>
						</div>
						<?php
					}
				} else {
					?>
					<h2 class=""><?php _e( 'Nenhum guia disponível', 'textdomain' ); ?></h2>
					<?php
				}
				wp_reset_postdata();
			?>
		</div>
	</div>

	<!-- Cotainer #asideWrapper -->
	<div id="asideWrapper" class="col-xs-12 col-sm-12 col-md-12 col-lg-3 col-xl-3 col-xxl-3">
        <div class="">
            <div class="h5 pb-2 mt-1 mb-3 text-muted border-bottom"><?php _e( 'Comprar Anúncios', 'textdomain' ); ?></div>
            <div class="">
                <?php echo do_shortcode( '[sliderProducts]' ); ?>
            </div>
        </div>
	</div>
</div>

<!-- Modal #validationProfileModal -->
<div class="modal fade" id="validationProfileModal" tabindex="-1" aria-labelledby="validationProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
		  <div class="container position-relative">
			  <h3 class="modal-title" id="validationProfileModalLabel"><?php _e( 'Validação de Perfil', 'textdomain' ); ?></h3>
			  <p class="fw-normal"><?php _e( 'Para sua segurança e também de quem acessa a plataforma, antes de continuar com outras etapas é necessário o envio da documentação solicitada abaixo.', 'textomain' ); ?></p>
			  <button type="button" class="btn-close position-absolute top-50 end-0" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
      </div>
      <div class="modal-body">
		  <div class="container">
			  <form class="row g-3" novalidate>
				<div class="col-12">
					<p class="fw-bolder"><?php _e( 'Envie fotos da frente, verso e segurando a sua identidade.', 'textdomain' );?></p>
					<div class="row">
						<div class="col-12 col-md-4 col-lg-4">
							<div class="thumbnail-view mb-1" style="width: 240px; height: 370px; background-color: gray; background-position: center;background-repeat: no-repeat;background-size: contain; display: flex; align-items: center; justify-content: center; background-image: url('https://acompanhantesa2.com/wp-content/uploads/2022/05/rg_frente.png')">
								<div class="spinner-border spinner-border-sm d-none" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</div>
							<div class="mb-3">
								<label for="_front_of_document" class="form-label"><?php _e( 'Foto de frente', 'textdomain' ); ?></label>
								<input class="form-control" type="file" id="_front_of_document" required>
								<input type="hidden" name="_front_of_document_id" id="_front_of_document_id" value="">
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="thumbnail-view mb-1" style="width: 240px; height: 370px; background-color: gray; background-position: center;background-repeat: no-repeat;background-size: contain; display: flex; align-items: center; justify-content: center; background-image: url('https://acompanhantesa2.com/wp-content/uploads/2022/05/rg_verso.png')">
								<div class="spinner-border spinner-border-sm d-none" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</div>
							<div class="mb-3">
								<label for="" class="form-label"><?php _e( 'Foto de Verso', 'textdomain' ); ?></label>
								<input class="form-control" type="file" id="_back_of_document">
								<input type="hidden" name="_back_of_document_id" id="_back_of_document_id" value="">
							</div>
						</div>
						<div class="col-12 col-md-4 col-lg-4">
							<div class="thumbnail-view mb-1" style="width: 240px; height: 370px; background-color: gray; background-position: center;background-repeat: no-repeat;background-size: contain; display: flex; align-items: center; justify-content: center; background-image: url('https://acompanhantesa2.com/wp-content/uploads/2022/05/Grupo-2.png')">
								<div class="spinner-border spinner-border-sm d-none" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</div>
							<div class="mb-3">
								<label for="" class="form-label"><?php _e( 'Segurando o documento', 'textdomain' ); ?></label>
								<input class="form-control" type="file" id="_holding_doc">
								<input type="hidden" name="_holding_doc_id" id="_holding_doc_id" value="">
							</div>
						</div>
					</div>
				</div>
				<div class="col-12">
					<p class="fw-bolder"><?php _e( 'Envie o vídeo de verificação', 'textdomain' );?></p>
					<div class="row">
						<div class="col-12">
							<div class="thumbnail-view mb-1" style="width: 100%; height: 230px; background-color: gray; background-position: center;background-repeat: no-repeat;background-size: cover; display: flex; align-items: center; justify-content: center; <?php echo strlen($profileCoverUrl) != 0 ? 'background-image: url(' . $profileCoverUrl . ')' : ''; ?>">
								<div class="spinner-border spinner-border-sm d-none" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
							</div>
							<div class="mb-3">
								<label for="" class="form-label"><?php _e( 'Vídeo de veríficação', 'textdomain' ); ?></label>
								<input class="form-control" type="file" id="_verification_media">
								<input class="form-control" type="hidden" name="_verification_media_id" id="_verification_media_id">
							</div>
						</div>
					</div>
				</div>
			</form>
		  </div>
      </div>
      <div class="modal-footer">
		  <div class="container d-flex justify-content-center" style="margin: auto;">
			  <button type="button" class="btn btn-primary" id="submitVerificationProfile"><?php _e( 'Solicitar Verificação', 'textdomain' ); ?></button>
		  </div>
      </div>
    </div>
  </div>
</div>

<!-- Notificações -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
	<!-- Then put toasts within -->
	<div class="toast" id="toastSuccess" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
		<div class="toast-header bg-success">
			<div class="rounded me-2 d-flex">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg text-white" viewBox="0 0 16 16">
					<path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
				</svg>
			</div>
			<strong class="me-auto text-white"><?php _e( 'Análise em andamento!', 'textdomain' ); ?></strong>
			<small class="text-white-50"><?php _e( 'agora mesmo', 'textdomain'); ?></small>
			<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
		</div>
		<div class="toast-body">
			<?php _e( 'Muito Obrigado(a). Já estamos analisando suas informações.', 'textdomain' ); ?>
		</div>
	</div>
</div>