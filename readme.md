# note
- this package is helpful laravel 9 using gcp storage

# edit your composer.json
```json
"repositories": {
    "liou2021": {
        "type": "vcs", 
        "url": "https://github.com/LIOU2021/gcs"
    }
}
```

# run this command in your laravel project
```bash
composer require liou2021/gcs
```

# publish config
```bash
php artisan vendor:publish --provider="Liou2021\Gcs\GCSServiceProvider"
```

#edit your config(config/gcs.php)
```php
return[
        'bucket'=>'your bucket name',
        'key'=>'your gcp key'
];

```

# common
```php
return \GCS::allFiles();

return \GCS::Files('test/');

return \GCS::put($request->file, $path);
```

# Ref
- [googleapis/google-cloud-php-storage](https://github.com/googleapis/google-cloud-php-storage)
- [GoogleCloudPlatform/php-docs-samples](https://github.com/GoogleCloudPlatform/php-docs-samples/tree/master/storage/src)
- [開發屬於自己的 Laravel 套件](https://tech.ray247k.com/blog/202107-laravel-package-development/)
