<?php

namespace Nayjest\I18n;

class I18n
{
    protected static $instance;

    const SESSION_KEY = 'i18n_language';

    /**
     * @return I18n
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * @return string[]
     */
    public function getSupportedLanguages()
    {
        return \Config::get('i18n::languages');
    }

    /**
     * @param string $language
     * @return bool
     */
    public function isValidLanguage($language)
    {
        return array_key_exists($language, $this->getSupportedLanguages());
    }
} 