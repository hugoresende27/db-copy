# edb-copy

### to run project : sudo docker-compose up -d --build
### to enter mysql : sudo docker exec -it [containerName] mysql -u root -p
### to enter root project : sudo docker exec -it [containerName] /bin/bash
### to stop and remove images : docker-compose down --rmi all
### get ip of docker container : sudo docker inspect [containerName]  | grep "IPAddress"



------------------------------------------------
#### Controllers :   "php-di/slim-bridge": "*"
- use DI\Bridge\Slim\Bridge as SlimAppFactory;
- $app = SlimAppFactory::create($container);
------------------------------------------------

#### dd function
- composer require symfony/var-dumper
- helpers.php and require helpers.php on bootstrap/app.php
------------------------------------------------

#### rabbitMQ
- https://github.com/php-amqplib/php-amqplib

------------------------------------------------
#### MongoDB
- customize string connection : https://www.mongodb.com/docs/manual/reference/connection-string/
- mongosh to enter mongo client in container

