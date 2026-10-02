#!/bin/sh
# Instalação automática do WordPress (idempotente: não faz nada se o site já está instalado).

WP_PATH=/var/www/html
log() { echo "[portal] $*"; }
wpcli() { runuser -u www-data -- wp --path="$WP_PATH" "$@"; }

# 1. Espera o wp-config.php (criado pelo entrypoint oficial) e a conexão com o banco.
tentativas=0
until [ -f "$WP_PATH/wp-config.php" ] && php -r '
    $host = getenv("WORDPRESS_DB_HOST"); $port = 3306;
    if (false !== strpos($host, ":")) { list($host, $port) = explode(":", $host, 2); }
    $ssl = false !== strpos((string) getenv("WORDPRESS_CONFIG_EXTRA"), "MYSQLI_CLIENT_SSL");
    $c = mysqli_init();
    $ok = @mysqli_real_connect($c, $host, getenv("WORDPRESS_DB_USER"), getenv("WORDPRESS_DB_PASSWORD"), getenv("WORDPRESS_DB_NAME"), (int) $port, null, $ssl ? MYSQLI_CLIENT_SSL : 0);
    exit($ok ? 0 : 1);
' >/dev/null 2>&1; do
    tentativas=$((tentativas + 1))
    if [ "$tentativas" -gt 90 ]; then
        log "Banco de dados indisponível após 3 minutos. Instalação automática cancelada."
        exit 1
    fi
    sleep 2
done

# 2. Se já está instalado (volume persistente), não mexe em nada.
if wpcli core is-installed >/dev/null 2>&1; then
    log "WordPress já instalado. Nada a fazer."
    exit 0
fi

SITE_URL="${PORTAL_SITE_URL:-${RENDER_EXTERNAL_URL:-http://localhost:8081}}"
SITE_TITLE="${PORTAL_SITE_TITLE:-Portal CI/CD · Atividade 07}"
ADMIN_USER="${PORTAL_ADMIN_USER:-admin}"
ADMIN_EMAIL="${PORTAL_ADMIN_EMAIL:-admin@example.com}"
ADMIN_PASS="${PORTAL_ADMIN_PASSWORD:-}"

# Sem senha definida, gera uma aleatória e mostra uma única vez nos logs.
if [ -z "$ADMIN_PASS" ]; then
    ADMIN_PASS=$(head -c 48 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c 16)
    log "Senha gerada para o usuário '$ADMIN_USER': $ADMIN_PASS"
fi

log "Instalando WordPress em $SITE_URL ..."
wpcli core install \
    --url="$SITE_URL" \
    --title="$SITE_TITLE" \
    --admin_user="$ADMIN_USER" \
    --admin_password="$ADMIN_PASS" \
    --admin_email="$ADMIN_EMAIL" \
    --skip-email || { log "Falha na instalação."; exit 1; }

wpcli language core install pt_BR --activate >/dev/null 2>&1 || log "Aviso: não foi possível ativar o idioma pt_BR."
wpcli option update timezone_string America/Sao_Paulo >/dev/null
wpcli theme activate portal-cicd || log "Aviso: tema portal-cicd não encontrado."

# Remove o conteúdo de exemplo do WordPress (post "Olá, mundo!" e página de exemplo).
wpcli post delete 1 2 --force >/dev/null 2>&1 || true

if [ "${PORTAL_SEED_POSTS:-1}" = "1" ]; then
    wpcli eval-file /usr/local/share/portal/seed/seed.php --user="$ADMIN_USER" || log "Aviso: falha ao criar as publicações iniciais."
fi

log "Pronto. Acesse $SITE_URL e entre com o usuário '$ADMIN_USER'."
