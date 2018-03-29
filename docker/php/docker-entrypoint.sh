#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
    composer install --prefer-dist --no-progress --no-suggest --no-interaction
    php bin/console assets:install
    php bin/console doctrine:database:create --if-not-exists
    php bin/console doctrine:schema:drop --full-database --force --no-interaction
    php bin/console doctrine:schema:create --no-interaction
    php bin/console doctrine:query:sql "CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\""
    php bin/console hautelook:fixtures:load --no-interaction

	# Permissions hack because setfacl does not work on Mac and Windows
	chown -R www-data var
fi

exec docker-php-entrypoint "$@"