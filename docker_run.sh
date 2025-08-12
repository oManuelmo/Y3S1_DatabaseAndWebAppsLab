set -e

cd /var/www
if ! command -v composer &> /dev/null
then
    echo "Composer not found, installing Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi
env >> /var/www/.env
php artisan clear-compiled
php artisan config:clear
php artisan db:seed
php artisan storage:unlink
php artisan storage:link
php artisan schedule:run
composer require icehouse-ventures/laravel-chartjs
composer require stripe/stripe-php
php artisan vendor:publish --provider="IcehouseVentures\LaravelChartjs\Providers\ChartjsServiceProvider" --tag="config"

echo "* * * * * cd /var/www && php artisan schedule:run >> /dev/null 2>&1" >> cronfile

crontab cronfile

rm cronfile

cron

php-fpm8.3 -D

nginx -g "daemon off;"
