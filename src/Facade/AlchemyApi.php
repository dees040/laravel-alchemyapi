<?php

namespace dees040\AlchemyApi\Facade;

use Illuminate\Support\Facades\Facade;

class AlchemyApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'dees040\AlchemyApi\AlchemyApi';
    }
}