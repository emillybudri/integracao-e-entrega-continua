<?php
/*
Plugin Name: Ativar tema Portal CI/CD
Description: Garante que o tema portal-cicd (montado via volume) esteja ativo.
Version: 4.0
Author: Emilly Budri Bognar
*/

add_action( 'init', function () {
    if ( ! is_blog_installed() ) {
        return;
    }

    if ( get_option( 'stylesheet' ) !== 'portal-cicd' && wp_get_theme( 'portal-cicd' )->exists() ) {
        switch_theme( 'portal-cicd' );
    }
} );
