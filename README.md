steps to run project


- clone repo
- create an env file in the root directory

and add the following into the file

APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_GENERATE_YAML_COPY=true
L5_SWAGGER_UI_DARK_MODE=true



- setup your database by adding db name, password and user in the env.

- The Migration and DB seeder has been configured to seed a Default admin user and some spatie configured roles automatically provided the APP_ENV is local this can be found in the AppServiceProvider. if APP_ENV is not local then you will have to run 'php artisan db:migrate --seed' manually

- run composer install to generate vendor folder with packages and dependencies.

- run composer dump-autoload to globally initialize helper methods in App\Helpers folder (Composer Install might have handled this or not)

- Swagger UI has been configured with documentation so when you do php artisan serve
  you can go to http://127.0.0.1:8000/api/documentation to see the API docs if you do not see the docs run this command 

  php artisan l5-swagger:generate and reload the link



- authorization to other endpoints besides auth endpoints needs token to be passed in the header, on swagger you will see the Authorize on the top right of the screen. Just pass in the laravel sanctum generated token on login without Bearer and access will be granted.



Note: If you do not want to use swagger - there is an api-docs.json in /storage/api-docs folder, you can use this to generate a postman collection. My personal preference is swagger. 
