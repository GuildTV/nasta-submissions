# NaSTA Submissions Site
Built with <a href="https://github.com/laravel/framework">Laravel</a>

## Create file requests
The dropbox api does not currently support creating file requests. To work around this, there is a job to generate them in bulk which should be run before submissions open.   
To do so, the following process is needed:
 * Ensure each station has a dropbox_account_id set, that is part of one of the accounts to be used.
 * Run ```php artisan create-file-requests```
 * When prompted, supply the authentication tokens. These can be retrieved from one of the many requests shown in the chrome dev tools when using the dropbox web client.