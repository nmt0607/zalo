release: php artisan cache:clear && php artisan config:cache
web: vendor/bin/heroku-php-apache2 public/
supervisor: supervisord -c /app/supervisor.ini -n
