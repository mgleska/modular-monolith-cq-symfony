#!/bin/bash

set -ex

cd "$(dirname "$0")"

./docker/wait-for database:3306 -t 120

if [ "$1" == "dev" ]
then
    composer install
fi

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate

php bin/console store:import
php bin/console product:import
php bin/console product:quantity
php bin/console offer:import r001
php bin/console offer:import r002
php bin/console offer:import r003

exec php -S 0.0.0.0:8000 -t public/
