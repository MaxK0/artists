## Запуск через docker-compose

- ```bash 
    docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
    ```
- Настройка .env
- ./vendor/bin/sail up -d
- Можно добавить alias: alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
- sail artisan key:generate
- sail artisan migrate
- sail artisan db:seed - для тестовых данных
- sail test - выполнение тестов

<br>

Просмотр swagger: /swagger

<br>

Пути к api начинаются с /api/v1/
