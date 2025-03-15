steps to run project


- clone repo
- env file in the root directory of the repo

- setup your database by adding db name, password and user in the env.

- The Migration and DB seeder has been configured to seed a Default admin user and some spatie configured roles automatically provided the APP_ENV is local this can be found in the AppServiceProvider. if APP_ENV is not local then you will have to run 'php artisan db:migrate --seed' manually

- run composer install to generate vendor folder with packages and dependencies.

- run composer dump-autoload to globally initialize helper methods in App\Helpers folder (Composer Install might have handled this or not)

- Swagger UI has been configured with documentation so when you do php artisan serve
  you can go to http://127.0.0.1:8000/api/documentation to see the API docs if you do not see the docs run this command 

  php artisan l5-swagger:generate and reload the link



- authorization to other endpoints besides auth endpoints needs token to be passed in the header, on swagger you will see the Authorize on the top right of the screen. Just pass in the laravel sanctum generated token on login without Bearer and access will be granted.



Note: If you do not want to use swagger - there is an api-docs.json in /storage/api-docs folder, you can use this to generate a postman collection. My personal preference is swagger. 
