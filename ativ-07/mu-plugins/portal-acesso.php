<?php
/*
Plugin Name: Portal CI/CD, acesso simplificado
Description: Endereço do site acompanha a URL de acesso, login direto para o site e dica de credenciais em ambientes de demonstração.
Version: 1.0
Author: Emilly Budri Bognar
*/

// 1. O site funciona em qualquer endereço de acesso (localhost:8081, 127.0.0.1, domínio do Render),
//    sem precisar reconfigurar a URL no banco.
if ( ! empty( $_SERVER['HTTP_HOST'] ) && preg_match( '/^[a-z0-9.\-]+(:[0-9]+)?$/i', $_SERVER['HTTP_HOST'] ) ) {
    $portal_https = ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'];
    $portal_url   = ( $portal_https ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'];

    add_filter( 'pre_option_home', function () use ( $portal_url ) { return $portal_url; } );
    add_filter( 'pre_option_siteurl', function () use ( $portal_url ) { return $portal_url; } );
}

// 2. Quem entra pela tela de login, sem destino definido, vai para o site (onde fica o botão +),
//    e não para o painel.
add_filter( 'login_redirect', function ( $redirect_to, $requested, $user ) {
    if ( '' === $requested && $user instanceof WP_User && user_can( $user, 'edit_posts' ) ) {
        return home_url( '/' );
    }
    return $redirect_to;
}, 10, 3 );

// 3. Em ambientes de demonstração (PORTAL_LOGIN_HINT=1), mostra o usuário e a senha na tela de login.
add_filter( 'login_message', function ( $message ) {
    $senha = getenv( 'PORTAL_ADMIN_PASSWORD' );
    if ( '1' !== getenv( 'PORTAL_LOGIN_HINT' ) || ! $senha ) {
        return $message;
    }
    $usuario = getenv( 'PORTAL_ADMIN_USER' ) ?: 'admin';

    return $message . sprintf(
        '<p class="message">Ambiente de demonstração.<br>Usuário: <strong>%s</strong><br>Senha: <strong>%s</strong></p>',
        esc_html( $usuario ),
        esc_html( $senha )
    );
} );

// 4. Tela de login com a mesma identidade visual do site.
add_filter( 'login_headerurl', function () { return home_url( '/' ); } );
add_filter( 'login_headertext', function () { return get_bloginfo( 'name' ); } );

add_action( 'login_enqueue_scripts', function () {
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        html body.login {
            --wp-admin-theme-color: #DB2777 !important;
            --wp-admin-theme-color--rgb: 219, 39, 119 !important;
            --wp-admin-theme-color-darker-10: #BE185D !important;
            --wp-admin-theme-color-darker-10--rgb: 190, 24, 93 !important;
            --wp-admin-theme-color-darker-20: #9D174D !important;
            --wp-admin-theme-color-darker-20--rgb: 157, 23, 77 !important;
        }
        body.login { background: #fff; font-family: 'Poppins', system-ui, sans-serif; }
        .login h1 a,
        .login h1.wp-login-logo a {
            background: none !important;
            width: auto !important;
            height: auto !important;
            text-indent: 0 !important;
            font-size: 1.25rem !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
            color: #000 !important;
        }
        .login form { border: 1px solid #D5D5D5; border-radius: 12px; box-shadow: none; }
        .login .message { border-left-color: #DB2777; border-inline-start-color: #DB2777 !important; box-shadow: none; background: #FDF2F8; }
        .login label { font-weight: 500; }
        html body.login .button-primary { background: #DB2777 !important; border-color: #DB2777 !important; border-radius: 6px; }
        html body.login .button-primary:hover, html body.login .button-primary:focus { background: #BE185D !important; border-color: #BE185D !important; }
        .login #nav a, .login #backtoblog a { color: #6B6B6B; }
        html body.login .language-switcher .button { color: #DB2777 !important; border-color: #DB2777 !important; }
        html body.login input:focus, html body.login select:focus { border-color: #DB2777 !important; box-shadow: 0 0 0 1px #DB2777 !important; }
    </style>
    <?php
} );
