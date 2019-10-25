<?php

namespace Nayjest\I18n;

use Controller as BaseController;
use Cookie;
use Lang;
use Redirect;
use Response;
use Config;


class Controller extends BaseController
{
    public function switchLanguage($language)
    {
        if (!I18n::getInstance()->isValidLanguage($language)) {
            return Response::json([
                'status'  => 'error',
                'message' => Lang::trans('language_unsupported', e($language))
            ]);
        }
        $cookie = Cookie::forever(I18n::SESSION_KEY, $language, Config::get('session.path', null), Config::get('session.domain', null));
        return Redirect::back()->withCookie($cookie);
    }
} 
