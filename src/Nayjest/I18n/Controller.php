<?php
/**
 * Created by PhpStorm.
 * User: Nayjest
 * Date: 24.02.14
 * Time: 0:31
 */

namespace Nayjest\I18n;
use \App;

class Controller extends \BaseController
{
    protected function getLanguageKeys()
    {
        return \Config::get('i18n::languages');
    }

    public function switchLanguage($language)
    {
        if (!I18n::getInstance()->isValidLanguage($language))
        {
            return \Response::json([
                'status' => 'error',
                'message' => \Lang::trans('language_unsupported', e($language))
            ]);
        }
        $cookie = \Cookie::forever(I18n::SESSION_KEY, $language, '/');
        return \Redirect::back()->withCookie($cookie);
    }
} 