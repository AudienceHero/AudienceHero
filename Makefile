deps:
	yarn install
	composer install

assets: assets-clean
	./node_modules/.bin/encore production

assets-test: assets-clean js-routes-test
	./node_modules/.bin/encore dev

assets-watch: assets-clean js-routes-dev css
	./node_modules/.bin/encore dev --watch

fixtures:
	php bin/console doctrine:database:drop --if-exists --force
	php bin/console doctrine:database:create
	php bin/console doctrine:query:sql --ansi -- "create extension \"uuid-ossp\";"
	php bin/console doctrine:schema:create
	php bin/console hautelook:fixtures:load --no-interaction
	php bin/console audiencehero:activity:aggregate
