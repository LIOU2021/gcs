<?php

namespace Liou2021\Gcs;

use Illuminate\Support\Facades\Facade;

class GCSFacade  extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'GCS';
    }
}
