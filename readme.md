# NaSTA Submissions Site
Built with <a href="https://github.com/laravel/framework">Laravel</a>

## Developer setup
__NOTE:__ This process currently only supports linux. It uses docker via vagrant so should be possible to run on other platforms with some modifications.
Requirements:
* Vagrant ^1.9.1
* Docker
* NodeJS >=6.0
* PHP 5.6 (can be avoided if all relevent commands are run in docker instead)
* Composer

### Steps:
1. Make a copy of storage/env/env.example to storage/env/env and adjust as needed. The following values were changed in my setup: 
    ```
    APP_URL=http://localhost:8001
    DB_HOST=172.17.0.1:33306
    ENCODE_DB_HOST=172.17.0.1:33306
    QUEUE_DRIVER=sync
    DROPBOX_CLIENT_ID=<REDACTED>
    DROPBOX_CLIENT_SECRET=<REDACTED>
    MAIL_ADMIN=nasta@julusian.co.uk
    ```
    Note: The DB_HOST fields do not need to be if you do not wish to run the tests and artisan commands from the host machine
1. ```docker run -v "$(pwd):/src" -it julusian/composer:5.6 sh -c "cd /src; composer install"```
1. ```docker run -v "$(pwd):/src" -it node:6 sh -c "cd /src; npm install"```
1. ```docker run -v "$(pwd):/src" -it node:6 sh -c "cd /src; npm run dev"``` This should be left running as it will recompile any js/css assets as they are changed
1. copy vagrant-settings.yml.dist to vagrant-settings.yml
1. (Optional) Modify vagrant-settings.yml as desired
1. ```vagrant up --no-parallel```
1. ```docker exec -it nasta-submissions-http php artisan migrate```
1. ```docker exec -it nasta-submissions-http php artisan db:seed``` This will populate the database with the stations and the categories for 2017, as well as a test admin and judge user account. Additional users must be created directly in the database, except for judges who have an artisan command. If these defaults want to be modified, they are located under database/seeds.
1. (Optional) ```docker exec -it nasta-submissions-http php artisan db:seed --class=FakeEntriesSeeder``` This will create some fake entries from some stations. Useful for working on pages which need lots of submission data. This data may be incomplete, so it is advised to follow the upload process to get some real data for some pages
1. ```docker exec -it nasta-submissions-http php artisan key:generate```

### The following services are now available to use:
* Submissions system: http://localhost:8001
* PHPMyAdmin: http://localhost:8002
* Mailhog (catchall email): http://localhost:8013

### Default credentials:
* test_admin: test123
* test_judge: test123
* Station credentials can be set by following the forgot password process, and viewing the email via mailhog

### Tests:
Unit tests can be run with ```docker exec -it nasta-submissions-http vendor/bin/phpunit```

These include tests of scraping the dropbox api for file informations so require dropbox secrets to be defined in the env file. For this reason, it is recommended that the env file uses a throwaway account.

## Deployment
For 2017, this repo was setup to use ci powered by gitlab. This would build the docker images, run the unit tests and then deploy the images to a kubernetes cluster running on google cloud. (The file download was run on a secondary cluster, local to the target storage)

This will either need converting to ci scripts for another ci service, or they can be used as a reference for manual deployment.

## How to guides
### Create file requests
__WARNING:__ This will cause dropbox to send an email per folder created. When run in 2017, it took 20 minutes for gmail to receive all ~1200 emails. Be careful of what email provider you use, it could trigger spam detection rules.

The dropbox api does not currently support creating file requests. To work around this, there is a job to generate them in bulk which should be run before submissions open.   
To do so, the following process is needed:
 * Ensure each station has a dropbox_account_id set, that is part of one of the accounts to be used, and that all categories exist.
 * Run ```php artisan create-file-requests```
 * When prompted, supply the authentication tokens. These can be retrieved from one of the many requests shown in the chrome dev tools when using the dropbox web client.
 * If an error is encountered, you may be prompted for tokens again, and you may need to rerun the command to ensure that no folder was skipped.

### Send welcome emails
To allow users to login for the first time, it is possible to send welcome emails to users.   
This can be done with the following command ```php artisan email:welcome station```. station can be replaced with judge or admin.   
Upon running the command, you can choose whether to send the email to all users of the type, or only to those who do not yet have a password.
