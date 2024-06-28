install:
	@cd ../docker/ && docker-compose build
	@cd ../docker/ && docker-compose up -d
	@docker exec php-cli-cron composer install
	@docker exec php-cli-cron php /var/www/html/bin/console doctrine:migrations:migrate
	@cd ../docker/ && docker-compose down

start:
	@cd ../docker/ && docker-compose up -d

stop:
	@cd ../docker/ && docker-compose down

restart:
	@make stop
	@make start

cs-fixer:
	@docker exec php-cli-cron composer phpcbf
	@docker exec php-cli-cron composer phpcs

terminal:
	@docker exec -it php-cli-cron bash

