## Запуск через docker-compose

- docker-compose up -d
- docker-compose exec laravel.test composer install
- docker-compose down
- Настройка .env
- ./vendor/bin/sail up
- Можно добавить alias: alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
- sail artisan key:generate
- sail artisan migrate
- sail artisan db:seed - для тестовых данных
- sail test - выполнение тестов

<br>

Просмотр swagger: /swagger