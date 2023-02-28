<?php

namespace Ddbaidya\BkashLaravel;

use Illuminate\Support\Facades\Facade;

class BkashFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bkash';
    }
}
