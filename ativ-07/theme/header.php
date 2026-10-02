<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#conteudo">Ir para o conteúdo</a>

<header class="site-header">
    <div class="wide">
        <a class="site-name" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
        <nav aria-label="Principal">
            <a href="<?php echo esc_url( home_url( '/#publicacoes' ) ); ?>">Publicações</a>
            <?php if ( is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( admin_url() ); ?>">Painel</a>
                <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">Sair</a>
            <?php else : ?>
                <a href="<?php echo esc_url( wp_login_url( home_url( '/' ) ) ); ?>">Entrar</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main id="conteudo" class="main">
