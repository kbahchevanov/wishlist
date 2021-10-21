#!/bin/bash

composer install -n
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:migrations:migrate -n

npm install --quiet

exec "$@"