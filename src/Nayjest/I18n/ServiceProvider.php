<?php

namespace Nayjest\I18n;

use App;
use Cookie;
use Config;
use Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->package('nayjest/i18n');
        App::instance('i18n', I18n::getInstance());


        Route::filter('i18n.applyLanguage', function () {

            # @todo update user profile with language
            if (!$language = Cookie::get(I18n::SESSION_KEY)) {
                $browser_language = strtolower(substr(@$_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
                if (
                    strlen($browser_language)
                    && array_key_exists(
                        $browser_language,
                        I18n::getInstance()->getSupportedLanguages()
                    )
                ) {
                    $language = $browser_language;
                } else {
                    $language = App::getLocale();
                }
                Cookie::queue(I18n::SESSION_KEY, $language, 60 * 24 * 60, '/');
            }

            Config::set('app.locale', $language);
            App::setLocale($language);
            $locale = Config::get("i18n::locale.$language");
            $locale_en = Config::get("i18n::locale.en");
            if ($locale) {
                setlocale(LC_COLLATE, $locale);
                setlocale(LC_TIME, $locale);
                setlocale(LC_CTYPE, $locale);
                # may not work on windows (available if PHP was compiled with libintl)
                if (defined('LC_MESSAGES')) {
                    setlocale(LC_MESSAGES, $locale);
                }
                setlocale(LC_MONETARY, $locale);
                setlocale(LC_NUMERIC, $locale_en);
            }

        });
        Route::when('*', 'i18n.applyLanguage');
        Route::get('i18n/switch-language/{lang}', [
            'uses' => 'Nayjest\I18n\Controller@switchLanguage',
            'as' => 'i18n.switchLanguage'
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}