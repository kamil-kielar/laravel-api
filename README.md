
## Initial setup
```bash
git clone 
cd library-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Auth
```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "password": "password"}'
```

## Test
```bash
php artisan test
```

## Commands
```bash
php artisan author:create 
