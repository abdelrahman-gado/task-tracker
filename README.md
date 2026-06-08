# task-tracker
PHP CLI task tracker project for https://roadmap.sh/projects/task-tracker

## Perquisites
1. Docker
2. Docker compose

## Installation
1. Clone project `git clone git@github.com:abdelrahman-gado/task-tracker.git`
2. `cd` into the project => `cd task-tracker`
3. Run `git submodule add git@github.com:abdelrahman-gado/laradock.git` in terminal
4. Copy `laradock/.env.example` to make `laradock/.env`
5. In `laradock` directory, run this in terminal `docker compose up workspace`
6. After the container start, open it container shell and run this `composer install` in `/var/www`
7. In `/var/www/src`, run `php task-cli.php help`


## Usage

from `/var/www/src`, you can

```sh
# Adding a new task
php task-cli.php add "Buy groceries"
# Output: Task added successfully (ID: 1)

# Updating and deleting tasks
php task-cli.php update 1 "Buy groceries and cook dinner"
php task-cli.php delete 1

# Marking a task as in progress or done
php task-cli.php mark-in-progress 1
php task-cli.php mark-done 1

# Listing all tasks
php task-cli.php list

# Listing tasks by status
php task-cli.php list done
php task-cli.php list todo
php task-cli.php list in-progress

# help
php task-cli.php help
```

## Notes
In this project, i used:
1. PHP v8.4
2. Composer v2.10
3. Xdebug v3.5.0
4. roave/security-advisories
5. friendsofphp/php-cs-fixer v3.95
6. phpstan v2.2
7. rector v2.4
8. captainHook v5.29
9. phpunit v13.1
10. mockery v1.6
11. paratest for parallel testing v7.22
