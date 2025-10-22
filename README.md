# Laravel example application
### XLS file uploading with data display and broadcasting


### Tech spec: https://docs.google.com/document/d/144XXs4SLVv2QkpaE83mVXrrv5L_HNMGUwOFYq_VQuoA/edit?tab=t.0

### Author: Konstantin Budylov
### Contacts:
    - telegram: @kbudylov
    - mail: k.budylov@gmail.com

### Date: 10/22/2025

## How to run:

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

## How to use
- Go to http://localhost
- Upload file
- Explore broadcasting messages in docker logs (reverb container)
- Go to /data to see imported data
- Run tests:
  ```./vendor/bin/phpunit```


## Application file structure overview
```
app/
├─ Http/
│  └─ Controllers/
│     ├─ ImportController.php
│     └─ DataController.php
├─ resources/
│  └─ views/
│     ├─ welcome.blade.php
│     └─ data/
│        └─ index.blade.php
└─ Import/
├─ Concerns/
│  └─ FormatProcessor.php
├─ Domain/
│  ├─ Formats/
│  │  └─ ImportedDataFormatProcessor.php
│  ├─ Models/
│  │  └─ ImportedData.php
│  ├─ ImportedDataRepository.php
│  └─ ImportedDataService.php
├─ Events/
│  ├─ ImportFailed.php
│  ├─ ImportRowFailed.php
│  ├─ ImportRowSuccess.php
│  ├─ ImportStarted.php
│  └─ ImportSuccess.php
├─ Jobs/
│  ├─ FileImportJob.php
│  └─ ProcessImportChunkJob.php
├─ Xls/
│  └─ ImportedData.php
├─ FileImportService.php
└─ FileImportServiceProvider.php
```


