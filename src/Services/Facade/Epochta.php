<?php


namespace Fomvasss\Epochta\Services\Facade;

use Illuminate\Support\Facades\Facade;

class Epochta extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Epochta';
    }
}