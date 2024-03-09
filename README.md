# Laravel & Vue

## Setup

```sh
docker-compose build
```

```sh
docker-compose up -d
```

```sh
docker-compose exec backend chmod -R 777 storage bootstrap/cache
```

```sh
docker-compose run --rm composer install
```

```sh
docker-compose exec backend php artisan migrate:fresh --seed
```

## Info

### Site Url: 

http://localhost

### Sample User:

username: test@example.com
password: password

### Mail:

All emails can be viewed at http://localhost:8100/

