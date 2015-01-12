<?php
namespace Nayjest\I18n\Facades;

use Illuminate\Support\Facades\Facade;


class I18n extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'i18n';
    }
}