# Laravel example application

### According to: https://docs.google.com/document/d/144XXs4SLVv2QkpaE83mVXrrv5L_HNMGUwOFYq_VQuoA/edit?tab=t.0 specification.

### Author: Konstantin Budylov
### Contacts:
    - telegram: @kbudylov
    - mail: k.budylov@gmail.com
    - linkedin: 

### Date: 10/20/2025

## Follow these steps to run and use application

Checkout repository:
```git checkout https://github.com/konstantin-budylov/laravel-example-app.git```

Got to repository local copy folder:
```cd laravel-example-app```

Install dependencies:
```composer install```

```npm install && npm run build```

Configure environment variables:
```cp ./.env.example ./.env```

Run docker cluster:
```./vendor/bin/sail up```

Enter webapp container bash
```./vendor/bin/sail bash```

Apply migrations:
```php artisan migrate```

Setup rabbitmq queue:
```php artisan rabbitmq:init```

Run tests:
```./vendor/bin/phpunit```
