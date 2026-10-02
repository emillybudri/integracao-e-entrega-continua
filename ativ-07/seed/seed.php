<?php
/**
 * Publicações iniciais de um site recém-instalado.
 * Executado uma única vez pelo provisionamento (wp eval-file), logo após a instalação.
 * Os valores de versão vêm do próprio ambiente em execução.
 */
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

global $wpdb;

$apache = '';
if ( preg_match( '#Apache/([0-9.]+)#', (string) shell_exec( 'apache2 -v 2>/dev/null' ), $m ) ) {
    $apache = $m[1];
}
$debian = '';
if ( preg_match( '/^VERSION_ID="?([0-9.]+)"?/m', (string) @file_get_contents( '/etc/os-release' ), $m ) ) {
    $debian = $m[1];
}
$info  = (string) $wpdb->db_server_info();
$banco = ( false !== stripos( $info, 'mariadb' ) ? 'MariaDB ' : 'MySQL ' ) . $wpdb->db_version();

$vars = array(
    '%WP%'      => get_bloginfo( 'version' ),
    '%PHP%'     => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
    '%APACHE%'  => $apache,
    '%DEBIAN%'  => $debian,
    '%DB%'      => $banco,
    '%TABELAS%' => (string) count( $wpdb->get_col( 'SHOW TABLES' ) ),
    '%POSTS%'   => $wpdb->posts,
);

$posts = array();

$posts[] = array(
    'title'   => 'Arquitetura do ambiente: WordPress, Apache e MariaDB em dois containers',
    'excerpt' => 'Dois serviços em uma rede dedicada. Só a porta 8081 é publicada no host, e o banco fica acessível apenas dentro da rede.',
    'image'   => 'arquitetura.png',
    'alt'     => 'Diagrama: o navegador acessa o WordPress na porta 8081, que consulta o MariaDB na porta 3306 dentro da rede ativ-07_wordpress_net.',
    'content' => <<<'HTML'
<p>O ambiente da Atividade 07 é definido no <code>docker-compose.yml</code> com dois containers: <code>wordpress_web</code> e <code>mariadb_wordpress</code>. Neste ambiente, as versões em execução são WordPress %WP% com PHP %PHP% e Apache %APACHE% sobre Debian %DEBIAN%, e %DB%.</p>

<h2>Rede e portas</h2>
<p>Os dois serviços ficam na rede <code>ativ-07_wordpress_net</code>, criada pelo Compose com o driver bridge. O WordPress publica a porta 80 do container como 8081 no host. O MariaDB escuta na 3306, mas essa porta não é publicada: apenas o WordPress, dentro da rede, chega até o banco, usando o nome do serviço (<code>database:3306</code>).</p>

<h2>Configuração por variáveis</h2>
<p>Usuário, senha e nome do banco vêm de variáveis de ambiente com valores padrão e podem ser trocados por um arquivo <code>.env</code> (o repositório traz um <code>.env.example</code>). O container do WordPress recebe as mesmas credenciais pelas variáveis <code>WORDPRESS_DB_HOST</code>, <code>WORDPRESS_DB_NAME</code>, <code>WORDPRESS_DB_USER</code> e <code>WORDPRESS_DB_PASSWORD</code>.</p>

<h2>O que existe no banco</h2>
<p>Depois da instalação, o banco <code>wordpress_db</code> contém %TABELAS% tabelas. Esta publicação, por exemplo, está gravada na tabela <code>%POSTS%</code>.</p>
HTML,
);

$posts[] = array(
    'title'   => 'Healthcheck do MariaDB e a ordem de inicialização',
    'excerpt' => 'O WordPress só sobe depois que o MariaDB responde a um ping. Veja os parâmetros do healthcheck e como conferir o estado do container.',
    'image'   => 'healthcheck.png',
    'alt'     => 'Linha de pulsos do healthcheck: 15 segundos em starting, verificações a cada 5 segundos e o estado healthy ao final.',
    'content' => <<<'HTML'
<p>Subir o WordPress antes de o banco aceitar conexões gera erro de conexão na primeira inicialização. Para evitar isso, o serviço <code>database</code> tem um healthcheck, e o serviço <code>web</code> depende dele.</p>

<h2>O teste</h2>
<p>O comando executado dentro do container do MariaDB é um ping administrativo:</p>
<pre><code>mariadb-admin ping -h 127.0.0.1 -u root -p$MYSQL_ROOT_PASSWORD</code></pre>

<h2>Os parâmetros</h2>
<ul>
<li><strong>interval: 5s</strong>. O teste roda a cada 5 segundos.</li>
<li><strong>timeout: 5s</strong>. Cada execução tem até 5 segundos para responder.</li>
<li><strong>start_period: 15s</strong>. Nos primeiros 15 segundos, falhas não contam, porque o MariaDB ainda está inicializando.</li>
<li><strong>retries: 10</strong>. Depois do período inicial, 10 falhas seguidas marcam o container como <code>unhealthy</code>.</li>
</ul>

<h2>A dependência</h2>
<p>No serviço <code>web</code>, o <code>depends_on</code> usa a condição <code>service_healthy</code>. O Compose só cria o container do WordPress quando o banco está saudável, sem scripts de espera.</p>

<h2>Como conferir</h2>
<pre><code>docker inspect --format='{{.State.Health.Status}}' mariadb_wordpress</code></pre>
<p>Com o ambiente de pé, o comando retorna <code>healthy</code>. A pipeline usa essa mesma consulta e espera até 60 segundos por esse valor antes de testar o WordPress.</p>
HTML,
);

$posts[] = array(
    'title'   => 'Volumes nomeados e bind mounts: onde ficam os dados',
    'excerpt' => 'Dois volumes guardam o banco e os arquivos do WordPress. O tema e o mu-plugin entram por bind mount, direto da pasta do repositório.',
    'image'   => 'volumes.png',
    'alt'     => 'Os containers WordPress e MariaDB gravam nos volumes wordpress_data e db_data; tema e mu-plugins entram por bind mount.',
    'content' => <<<'HTML'
<p>Containers são descartáveis, os dados não. Por isso o Compose declara dois volumes nomeados, e o Docker prefixa o nome com o do projeto (a pasta <code>ativ-07</code>).</p>

<h2>Volumes nomeados</h2>
<ul>
<li><code>ativ-07_db_data</code> é montado em <code>/var/lib/mysql</code> no MariaDB e guarda todas as tabelas, incluindo as publicações do site.</li>
<li><code>ativ-07_wordpress_data</code> é montado em <code>/var/www/html</code> no WordPress e guarda o núcleo, os envios de mídia e o <code>wp-config.php</code> gerado.</li>
</ul>

<h2>Bind mounts</h2>
<p>Duas pastas do repositório são montadas por cima do volume do WordPress:</p>
<ul>
<li><code>./theme</code> em <code>wp-content/themes/portal-cicd</code>, o tema deste site.</li>
<li><code>./mu-plugins</code> em <code>wp-content/mu-plugins</code>, que ativa o tema e simplifica o acesso.</li>
</ul>
<p>Como são bind mounts, editar um arquivo na máquina reflete no site na hora, sem reconstruir imagem.</p>

<h2>Parar sem perder dados</h2>
<p><code>docker compose down</code> remove os containers e a rede, mas mantém os volumes. Já <code>docker compose down -v</code> apaga também os volumes, e o ambiente volta ao estado inicial, com nova instalação automática. É esse comando que a pipeline usa na limpeza final.</p>
HTML,
);

$posts[] = array(
    'title'   => 'Pipeline de CI no GitHub Actions: validar, subir e testar',
    'excerpt' => 'A cada push na pasta ativ-07, dois jobs validam o Compose e testam o ambiente completo, com limpeza garantida ao final.',
    'image'   => 'pipeline.png',
    'alt'     => 'Dois jobs em sequência: validar-configuracao executa docker compose config; testar-ambiente-wordpress sobe, espera o banco, testa o HTTP e limpa.',
    'content' => <<<'HTML'
<p>O workflow fica em <code>.github/workflows/ativ-07-pipeline-docker-wordpress.yml</code> e roda em <code>ubuntu-latest</code>.</p>

<h2>Quando executa</h2>
<p>Em <code>push</code> nas branches <code>main</code>, <code>dev</code> e <code>feat/*</code>, e em <code>pull_request</code> para <code>main</code> e <code>dev</code>. Em ambos os casos, só quando algo muda em <code>ativ-07/</code> ou no próprio arquivo do workflow.</p>

<h2>Job 1: validar-configuracao</h2>
<p>Faz o checkout do código e roda <code>docker compose config</code>, que interpreta o <code>docker-compose.yml</code> e falha se houver erro de sintaxe.</p>

<h2>Job 2: testar-ambiente-wordpress</h2>
<p>Depende do primeiro (<code>needs</code>) e executa, em ordem:</p>
<ol>
<li><code>docker compose up -d --build</code> para construir a imagem e subir WordPress e MariaDB.</li>
<li>Espera de até 60 segundos até o MariaDB ficar <code>healthy</code>.</li>
<li>Espera pela instalação automática do WordPress: a página inicial precisa responder 200 com o tema ativo. Se não responder em até 3 minutos, o job imprime os logs e falha.</li>
<li><code>docker compose ps</code> para registrar o estado final dos containers.</li>
<li><code>docker compose down -v</code> em um passo com <code>if: always()</code>, que roda mesmo se algo falhar e deixa o runner limpo.</li>
</ol>
HTML,
);

// Insere do mais antigo para o mais novo, para a lista exibir a Arquitetura no topo.
$posts = array_reverse( $posts );
$dir   = '/usr/local/share/portal/seed/images/';

foreach ( $posts as $i => $p ) {
    if ( $i > 0 ) {
        sleep( 1 );
    }

    $post_id = wp_insert_post( array(
        'post_title'   => $p['title'],
        'post_content' => strtr( $p['content'], $vars ),
        'post_excerpt' => $p['excerpt'],
        'post_status'  => 'publish',
        'post_type'    => 'post',
        'post_author'  => get_current_user_id() ?: 1,
        'post_date'    => current_time( 'mysql' ),
    ), true );

    if ( is_wp_error( $post_id ) ) {
        WP_CLI::warning( $post_id->get_error_message() );
        continue;
    }

    $tmp = wp_tempnam( $p['image'] );
    copy( $dir . $p['image'], $tmp );
    $att = media_handle_sideload( array( 'name' => sanitize_title( $p['title'] ) . '.png', 'tmp_name' => $tmp ), $post_id, $p['title'] );

    if ( is_wp_error( $att ) ) {
        WP_CLI::warning( $att->get_error_message() );
        continue;
    }

    update_post_meta( $att, '_wp_attachment_image_alt', $p['alt'] );
    set_post_thumbnail( $post_id, $att );
}

WP_CLI::success( count( $posts ) . ' publicações iniciais criadas.' );
