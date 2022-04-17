#!/bin/sh

echo "Waiting for volume"
while [ ! -f composer.json ]
do
  echo "composer.json not found..."
  sleep 1
done

echo "composer install"
cd /var/www/api && composer install

echo "env file"
cd /var/www/api && if [ ! -z .env ]; then cp .env.example .env; fi

echo "Waiting for mysql"
until mysql -h"db" -P"3306" -uroot -proot &> /dev/null
do
  echo "Mysql not yet ready..."
  sleep 1
done

echo -e "\nMysql ready"

echo "migrate"
php /var/www/api/artisan migrate

echo "permissions"
chmod 777 -R ./storage
