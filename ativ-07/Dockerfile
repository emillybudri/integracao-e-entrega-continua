# Imagem do WordPress que se instala sozinha no primeiro boot:
# WP-CLI + tema + mu-plugins + conteúdo inicial, sem passar pelo assistente de instalação.
FROM wordpress:7.1.2-php8.3-apache

RUN curl -fsSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x /usr/local/bin/wp

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
