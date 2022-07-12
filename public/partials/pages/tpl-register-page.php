<?php #get_header(); ?>
<div class="row m-0 p-0" style="height: 100vh;">
    <div id="registrationForm" class="col-11 col-md-6 col-lg-6 d-flex align-content-center register__formContainer">
        <div class="row register__form">
            <div class="col-11 col-md-8 col-lg-8 m-auto">
                <div class="register__header">
                    <h1 class="text-center fw-bolder"><?php _e( 'Cadastro', 'textdomain' ); ?></h1>
                    <p class="text-center"><?php _e( 'Cadastre-se GRATUITAMENTE de forma rápida e fácil!', 'textdomain' ); ?></p>
                </div>
                <form class="mt-5 register__form" action="<?php $_SERVER['REQUEST_URI'] ?>" method="post" autocomplete="off">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="full_name" class="form-label"><?php _e( 'Nome Completo', 'textdomain' ); ?></label>
                            <input id="full_name" name="full_name" class="form-control" type="text" value="<?php ( isset( $_POST['full_name'] ) ? $firstName : null  ) ?>">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="user_email" class="form-label"><?php _e( 'E-mail', 'textdomain' ); ?></label>
                            <input id="user_email" name="user_email" class="form-control" type="email" value="<?php ( isset( $_POST['email'] ) ? $email : null  ) ?>" autocomplete="false">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="user_password" class="form-label"><?php _e( 'Senha', 'textdomain' ); ?></label>
                            <input id="user_password" name="user_password" class="form-control" type="password">
                        </div>
                    </div>
            
                    <div class="row mb-3">
                        <div class="form-check form-switch ms-2 col-12">
                            <input class="form-check-input" type="checkbox" role="switch" name="terms_agree" id="terms_agree">
                            <label class="form-check-label" for="terms_agree"><?php _e('Concordo com os <b><a href="#">termos de uso e políticas de privacidade</a></b> da plataforma A2 Acompanhantes.', 'textdomain') ?></label>
                        </div>
            
                        <div class="form-check form-switch ms-2 col-12">
                            <input class="form-check-input" type="checkbox" role="switch" name="age_confirm" id="age_confirm">
                            <label class="form-check-label" for="age_confirm"><?php _e('Declaro que <b>sou maior de 18 anos</b>.', 'textdomain') ?></label>
                        </div>
                    </div>
            
                    <input id="user_type" name="user_type" type="hidden" value="a2_scort">
                    <button class="btn btn-primary register__btn" type="submit" name="submit" value="registerUser"><?php _e('Cadastrar', 'textdomain') ?><i class="bi bi-send-fill ms-2"></i></button>
                </form>
            </div>
        </div>

        <div class="row register__completed d-none">
            <div class="col-11 col-md-8 col-lg-8 m-auto text-center">
                <h1 class="fw-bolder"><?php _e( 'Parabéns!', 'textdomain' ); ?></h1>
                <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" style="max-width: 240px;">
                    <g id="Flat"><g id="Color"><circle cx="32" cy="58" fill="#f9a215" r="1"/><circle cx="32" cy="29" fill="#fccd1d" r="15.5"/><circle cx="32" cy="29" fill="#f9a215" r="11.39"/><path d="M33.1,22.93,34.31,25a1.26,1.26,0,0,0,.85.6l2.43.48a1.22,1.22,0,0,1,.67,2l-1.67,1.78a1.22,1.22,0,0,0-.33,1l.29,2.4a1.26,1.26,0,0,1-1.78,1.27l-2.24-1a1.3,1.3,0,0,0-1.06,0l-2.24,1a1.26,1.26,0,0,1-1.78-1.27l.29-2.4a1.22,1.22,0,0,0-.33-1l-1.67-1.78a1.22,1.22,0,0,1,.67-2l2.43-.48a1.26,1.26,0,0,0,.85-.6l1.21-2.11A1.28,1.28,0,0,1,33.1,22.93Z" fill="#fccd1d"/><path d="M11.5,54c-2.58,0-4.81-1.87-5.86-2.92a1,1,0,0,1,0-1.41c1.05-1.05,3.28-2.92,5.86-2.92s4.81,1.87,5.86,2.92a1,1,0,0,1,0,1.41C16.31,52.13,14.08,54,11.5,54Z" fill="#fccd1d"/><path d="M6.48,41.81a6.52,6.52,0,0,1-3.29-3.44,1,1,0,0,1,.48-1.32,6.61,6.61,0,0,1,4.73-.52A6.63,6.63,0,0,1,11.69,40a1,1,0,0,1-.48,1.32A6.62,6.62,0,0,1,6.48,41.81Z" fill="#fccd1d"/><path d="M4.88,29.93A4.61,4.61,0,0,1,3,27.41a1,1,0,0,1,.74-1.3,4.62,4.62,0,0,1,3.12.33A4.55,4.55,0,0,1,8.74,29,1,1,0,0,1,8,30.26,4.57,4.57,0,0,1,4.88,29.93Z" fill="#fccd1d"/><path d="M4.26,20.15a3.9,3.9,0,0,1-1.58-2.23,1,1,0,0,1,.73-1.26,4,4,0,0,1,2.72.25A3.94,3.94,0,0,1,7.7,19.14,1,1,0,0,1,7,20.4,3.87,3.87,0,0,1,4.26,20.15Z" fill="#fccd1d"/><path d="M52.5,54c2.58,0,4.81-1.87,5.86-2.92a1,1,0,0,0,0-1.41c-1-1.05-3.28-2.92-5.86-2.92s-4.81,1.87-5.86,2.92a1,1,0,0,0,0,1.41C47.69,52.13,49.92,54,52.5,54Z" fill="#fccd1d"/><path d="M57.52,41.81a6.52,6.52,0,0,0,3.29-3.44,1,1,0,0,0-.48-1.32,6.61,6.61,0,0,0-4.73-.52A6.63,6.63,0,0,0,52.31,40a1,1,0,0,0,.48,1.32A6.62,6.62,0,0,0,57.52,41.81Z" fill="#fccd1d"/><path d="M59.12,29.93A4.61,4.61,0,0,0,61,27.41a1,1,0,0,0-.74-1.3,4.62,4.62,0,0,0-3.12.33A4.55,4.55,0,0,0,55.26,29,1,1,0,0,0,56,30.26,4.57,4.57,0,0,0,59.12,29.93Z" fill="#fccd1d"/><path d="M59.74,20.15a3.9,3.9,0,0,0,1.58-2.23,1,1,0,0,0-.73-1.26,4,4,0,0,0-2.72.25,3.94,3.94,0,0,0-1.57,2.23A1,1,0,0,0,57,20.4,3.87,3.87,0,0,0,59.74,20.15Z" fill="#fccd1d"/><path d="M28.5,57.14C15,49.22,8.75,35.2,9,13V9.76a1,1,0,0,0-2,0V13C6.74,36,13.26,50.55,27.5,58.86A.9.9,0,0,0,28,59a1,1,0,0,0,.5-1.86Z" fill="#f9a215"/><path d="M56,8.76a1,1,0,0,0-1,1V13c.24,22.2-6,36.22-19.51,44.14A1,1,0,0,0,36,59a.9.9,0,0,0,.5-.14C50.74,50.55,57.26,36,57,13V9.76A1,1,0,0,0,56,8.76Z" fill="#f9a215"/><path d="M6.46,9.76a4.29,4.29,0,0,1,1-2.5.7.7,0,0,1,1.09,0,4.22,4.22,0,0,1,1,2.5,4.22,4.22,0,0,1-1,2.5.7.7,0,0,1-1.09,0A4.29,4.29,0,0,1,6.46,9.76Z" fill="#fccd1d"/><path d="M57.54,9.76a4.29,4.29,0,0,0-1-2.5.7.7,0,0,0-1.09,0,4.22,4.22,0,0,0-1,2.5,4.22,4.22,0,0,0,1,2.5.7.7,0,0,0,1.09,0A4.29,4.29,0,0,0,57.54,9.76Z" fill="#fccd1d"/></g></g>
                </svg>
                <p class=""><?php _e( 'Cadastro efetuado com sucesso! Basta clicar no botão abaixo e fazer login na plataforma para começar seu perfil de acompanhante!', 'textdomain') ?></p>
                <a href="<?php echo site_url('/login'); ?>" class="btn btn-primary d-flex justify-content-center align-items-center register__btn"><?php _e( 'Entrar na Plataforma', 'textdomain' ); ?></a>
            </div>
        </div>      
    </div>

    <div class="col-md-6 col-lg-6 register__bgContainer">
        <div class="register__backgroundImage"></div>
    </div>
</div>

