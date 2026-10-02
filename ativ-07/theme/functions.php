<?php
define( 'PORTAL_CICD_VERSION', '5.0.0' );

// Verifica permissão do usuário para gerenciar posts
function portal_cicd_pode_editar() {
    return is_user_logged_in() && current_user_can( 'edit_posts' );
}

function portal_cicd_scripts() {
    wp_enqueue_style(
        'portal-cicd-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap',
        array(),
        null
    );
    wp_enqueue_style( 'portal-cicd-style', get_stylesheet_uri(), array( 'portal-cicd-fonts' ), PORTAL_CICD_VERSION );

    if ( portal_cicd_pode_editar() ) {
        wp_enqueue_script( 'portal-cicd-app', get_template_directory_uri() . '/app.js', array(), PORTAL_CICD_VERSION, true );
        wp_localize_script( 'portal-cicd-app', 'PortalCRUD', array(
            'root'  => esc_url_raw( rest_url( 'wp/v2/' ) ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'home'  => esc_url_raw( home_url( '/' ) ),
        ) );
    }
}
add_action( 'wp_enqueue_scripts', 'portal_cicd_scripts' );

function portal_cicd_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'style', 'script' ) );
    add_image_size( 'portal-card', 560, 294, true );
}
add_action( 'after_setup_theme', 'portal_cicd_setup' );

// Ícone padrão caso a instalação não tenha favicon
add_action( 'wp_head', function () {
    if ( ! has_site_icon() ) {
        echo '<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 32 32%27%3E%3Crect width=%2732%27 height=%2732%27 rx=%278%27 fill=%27%23DB2777%27/%3E%3Ccircle cx=%2716%27 cy=%2716%27 r=%276%27 fill=%27%23fff%27/%3E%3C/svg%3E">' . "\n";
    }
}, 1 );

// Oculta a barra de administração nativa
add_filter( 'show_admin_bar', '__return_false' );

add_filter( 'excerpt_length', function () { return 28; } );
add_filter( 'excerpt_more', function () { return '…'; } );

// Inclui rascunhos na listagem para usuários com permissão de edição
add_action( 'pre_get_posts', function ( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_home() && portal_cicd_pode_editar() ) {
        $query->set( 'post_status', array( 'publish', 'draft' ) );
    }
} );

// Renderiza botões de ação (editar e excluir)
function portal_cicd_acoes( $post_id ) {
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    ?>
    <div class="post-actions">
        <button type="button" class="link-btn" data-edit="<?php echo (int) $post_id; ?>">Editar</button>
        <?php if ( current_user_can( 'delete_post', $post_id ) ) : ?>
            <button type="button" class="link-btn" data-delete="<?php echo (int) $post_id; ?>" data-title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">Excluir</button>
        <?php endif; ?>
    </div>
    <?php
}

// Retorna as versões do WordPress, PHP e Banco de Dados
function portal_cicd_ambiente() {
    global $wpdb;

    $info  = (string) $wpdb->db_server_info();
    $banco = ( false !== stripos( $info, 'mariadb' ) ) ? 'MariaDB' : 'MySQL';

    return array(
        'wordpress' => get_bloginfo( 'version' ),
        'php'       => PHP_VERSION,
        'banco'     => $banco . ' ' . $wpdb->db_version(),
    );
}

