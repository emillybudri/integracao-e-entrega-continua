#!/bin/sh
set -e

# Modo demonstração: sem banco externo informado, usa SQLite dentro do próprio container.
# Assim o deploy funciona sem preencher nada (os dados duram enquanto o container existir).
if [ -z "${WORDPRESS_DB_HOST:-}" ]; then
    export PORTAL_DB_MODE=sqlite
    export WORDPRESS_DB_HOST=sqlite WORDPRESS_DB_NAME=wordpress WORDPRESS_DB_USER=sqlite WORDPRESS_DB_PASSWORD=sqlite
    export PORTAL_ADMIN_PASSWORD="${PORTAL_ADMIN_PASSWORD:-admin123}"
    export PORTAL_LOGIN_HINT="${PORTAL_LOGIN_HINT:-1}"

    # Drop-in db.php do plugin oficial: troca a camada de banco do WordPress por SQLite.
    PLUGIN_DIR=sqlite-database-integration
    sed -e "s#{SQLITE_IMPLEMENTATION_FOLDER_PATH}#/var/www/html/wp-content/plugins/$PLUGIN_DIR#g" \
        -e "s#{SQLITE_PLUGIN}#$PLUGIN_DIR/load.php#g" \
        "/usr/src/wordpress/wp-content/plugins/$PLUGIN_DIR/db.copy" > /usr/src/wordpress/wp-content/db.php

    # Se o WordPress já foi copiado para /var/www/html (reinício do container), garante o drop-in.
    if [ -d /var/www/html/wp-content ] && [ ! -e /var/www/html/wp-content/db.php ]; then
        cp /usr/src/wordpress/wp-content/db.php /var/www/html/wp-content/db.php
    fi
fi

# O provisionamento roda em segundo plano enquanto o entrypoint oficial prepara o WordPress
# e inicia o Apache. Ele espera o banco ficar disponível antes de instalar.
if [ "$1" = "apache2-foreground" ]; then
    portal-provision.sh &
fi

exec docker-entrypoint.sh "$@"
