#!/bin/sh
set -e

# O provisionamento roda em segundo plano enquanto o entrypoint oficial prepara o WordPress
# e inicia o Apache. Ele espera o banco ficar disponível antes de instalar.
if [ "$1" = "apache2-foreground" ]; then
    portal-provision.sh &
fi

exec docker-entrypoint.sh "$@"
