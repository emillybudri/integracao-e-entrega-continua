<?php
get_header();

$home = is_home() || is_front_page();
?>

<?php if ( $home ) :
    $env = portal_cicd_ambiente(); ?>

<section class="hero wide">
    <div class="hero-text">
        <h1>WordPress e MariaDB em containers Docker.</h1>
        <p>Ambiente orquestrado com Docker Compose. A cada push, o GitHub Actions valida a configuração, sobe os containers e testa a resposta da aplicação.</p>
        <dl class="facts">
            <div><dt>Estado</dt><dd class="online">Online</dd></div>
            <div><dt>Endereço</dt><dd>localhost:8081</dd></div>
            <div><dt>WordPress</dt><dd><?php echo esc_html( $env['wordpress'] ); ?></dd></div>
            <div><dt>Banco de dados</dt><dd><?php echo esc_html( $env['banco'] ); ?></dd></div>
            <div><dt>PHP</dt><dd><?php echo esc_html( $env['php'] ); ?></dd></div>
        </dl>
    </div>
    <div class="hero-art">
        <?php get_template_part( 'parts/arquitetura' ); ?>
        <p class="note">O WordPress só inicia depois que o banco passa no healthcheck. Os dados ficam em volumes e a porta 3306 não é exposta fora da rede.</p>
    </div>
</section>

<?php endif; ?>

<div class="col">

<?php if ( is_singular() ) : ?>

    <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class( 'entry' ); ?>>
            <?php if ( is_single() ) : ?>
                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
            <?php endif; ?>
            <h1><?php the_title(); ?></h1>
            <?php if ( is_single() ) { portal_cicd_acoes( get_the_ID() ); } ?>
            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="entry-thumb"><?php the_post_thumbnail( 'large' ); ?></figure>
            <?php endif; ?>
            <div class="entry-content"><?php the_content(); ?></div>
        </article>
    <?php endwhile; ?>
    <a class="back" href="<?php echo esc_url( home_url( '/#publicacoes' ) ); ?>">← Publicações</a>

<?php else : ?>

    <section id="publicacoes" aria-labelledby="h-posts">
        <h2 class="label" id="h-posts"><?php echo $home ? 'Publicações' : esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h2>

        <?php if ( have_posts() ) : ?>
            <ul>
                <?php while ( have_posts() ) : the_post(); ?>
                    <li <?php post_class( 'post' ); ?>>
                        <a href="<?php the_permalink(); ?>" class="<?php echo has_post_thumbnail() ? 'has-thumb' : ''; ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <span class="post-thumb"><?php the_post_thumbnail( 'portal-card', array( 'alt' => '', 'loading' => 'lazy' ) ); ?></span>
                            <?php endif; ?>
                            <span class="post-body">
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                                <?php if ( 'draft' === get_post_status() ) : ?><span class="badge">Rascunho</span><?php endif; ?>
                                <h3><?php the_title(); ?></h3>
                                <p><?php echo esc_html( get_the_excerpt() ); ?></p>
                            </span>
                        </a>
                        <?php portal_cicd_acoes( get_the_ID() ); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
            <?php the_posts_pagination( array( 'prev_text' => '← Anteriores', 'next_text' => 'Próximas →' ) ); ?>
        <?php else : ?>
            <p class="empty">Nenhuma publicação ainda. <a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>">Criar a primeira</a>.</p>
        <?php endif; ?>
    </section>

<?php endif; ?>

</div>
<?php get_footer(); ?>
