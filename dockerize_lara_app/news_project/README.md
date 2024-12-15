# Laravel 11 and PHP 8.2

1: Docker setup

    files i provided Docker and docker-composer.yml file
    inside the file i make containers for project,mysql and php myadmin

# Build the Docker images
    docker-compose build

# Start the containers
    docker-compose up -d


2: Run the project with these commands smothly

# 1 . inside the project path
    docker exec -it laravel_docker_news bash

# 2. then run the command
    composer update

# 3. migrate the database
    php artisan migrate

# 4. seed the Category
    php artisan db:seed CategorySeeder

# 5. run the swagger command so you can view them in the provided link 
    php artisan l5-swagger:generate
    http://localhost:8090/news_project/public/api/documentation

# 6. i use 3 news api to fetch the data listed below and api key inside (ENV) file
    1: News Api 
    2: New york time api
    3: The Guardian api

# 7. these apis fetch data on daily basis as i add command for them and set seconds due to test but also provide daily commands
    $schedule->command('app:fetch-news-api')->everySecond();
    $schedule->command('app:fetch-NYT-news-api')->everySecond();
    $schedule->command('app:fetch-guardian-news-api')->everySecond();
