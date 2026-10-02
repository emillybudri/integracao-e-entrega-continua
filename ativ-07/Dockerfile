# Imagem do WordPress que se instala sozinha no primeiro boot:
# WP-CLI + tema + mu-plugins + conteúdo inicial, sem passar pelo assistente de instalação.
#
# Com WORDPRESS_DB_HOST definido (docker compose), usa o MariaDB informado.
# Sem ele (ex.: deploy no Render sem banco), entra em modo demonstração com SQLite no próprio container.
FROM wordpress:7.1.2-php8.3-apache

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp

# Plugin oficial que permite ao WordPress usar SQLite (usado só no modo demonstração).
RUN curl -fsSL -o /tmp/sqlite.zip https://downloads.wordpress.org/plugin/sqlite-database-integration.latest-stable.zip \
    && php -r '$z = new ZipArchive; if ($z->open("/tmp/sqlite.zip") !== true) { exit(1); } $z->extractTo("/usr/src/wordpress/wp-content/plugins");' \
    && rm /tmp/sqlite.zip \
    && test -f /usr/src/wordpress/wp-content/plugins/sqlite-database-integration/db.copy

# Tema e mu-plugins entram na imagem (o docker-compose ainda os monta por cima para edição ao vivo).
COPY theme /usr/src/wordpress/wp-content/themes/portal-cicd
COPY mu-plugins /usr/src/wordpress/wp-content/mu-plugins
COPY seed /usr/local/share/portal/seed

COPY docker/entrypoint.sh /usr/local/bin/portal-entrypoint.sh
COPY docker/provision.sh /usr/local/bin/portal-provision.sh
RUN sed -i 's/\r$//' /usr/local/bin/portal-*.sh \
    && chmod +x /usr/local/bin/portal-*.sh

ENTRYPOINT ["portal-entrypoint.sh"]
CMD ["apache2-foreground"]
