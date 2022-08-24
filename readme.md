# prepare

- required 
```
php 8
laravel 9
```

- install gcp package
```bash
composer require google/cloud
```
- setting
```php
config('filesystems.gcs.bucket');
config('filesystems.gcs.key');
```
