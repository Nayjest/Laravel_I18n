<?php
namespace Nayjest\I18n\Eloquent;

use \App;
use \Exception;
use \Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Nayjest\I18n\I18n as Languages;

/**
 * Trait for models that supports internationalization
 *
 * @todo implement setting translated attributes following way: $this->fill($data[lang][attributeName])
 *
 * @property Collection $translations
 *
 * @package Nayjest\I18n
 */
trait Translated
{

    protected $currentLanguage;

    public function getCurrentLanguage()
    {
        if (null === $this->currentLanguage) {
            $this->resetCurrentLanguage();
        }
        return $this->currentLanguage;
    }

    public function setCurrentLanguage($lang)
    {
        $this->currentLanguage = $lang;
    }

    public function resetCurrentLanguage()
    {
        $this->currentLanguage = App::getLocale();
    }

    /**
     * Set to null for disabling fallback in derived classes
     * @var string
     */
    public static $fallbackLanguage = 'en';

    /**
     * @todo implement fallback support(separately): where lang = $target or lang = $fallback LIMIT 1 ORDER BY lang = $target DESC
     * @param string $language
     * @return callable
     */
    private static function getWhereLangCallback($language)
    {
        return function ($query) use ($language) {
            $query->where('language', '=', $language);
        };
    }

    /**
     * @return string
     */
    public static function getTranslationClass()
    {
        if (property_exists(get_called_class(), 'translationClass')) {
            return static::$translationClass;
        } else {
            return get_called_class() . 'Translation';
        }
    }

    protected $autoCreatedTranslations = [];

    /**
     * Creates translation associated with this model
     * If translation was already created, returns it instead of creating new
     * (But does not saves)
     *
     * @param string $lang
     * @return Eloquent|Translated
     */
    public function createTranslation($lang)
    {
        if (empty($this->autoCreatedTranslations[$lang])) {
            $class = static::getTranslationClass();
            /** @var Eloquent|Translation|null $translation */
            $translation = new $class([
                'language' => $lang,
            ]);
            $translation->entity()->associate($this);
            $this->autoCreatedTranslations[$lang] = $translation;
        }
        return $this->autoCreatedTranslations[$lang];
    }

    /**
     * Defines Eloquent relation
     *
     * @todo implement languages as translation collection keys
     * @return mixed
     */
    public function translations()
    {
        $translationClass = static::getTranslationClass();
        $relationName = $translationClass::getRelationName();
        return $this->hasMany(static::getTranslationClass(), $relationName . '_id');
    }

    /**
     * @param string $language
     * @return Eloquent|Translated|null
     */
    public function getTranslation($language, $autoCreate = false)
    {
        $translations =&$this->translations;
        if ($translations) {
            $model = $translations->filter(
                function ($translation) use ($language) {
                    if ($translation->language === $language) {
                        return $translation;
                    }
                }
            )->pop();
        } else {
            $model = null;
        }

        return ($model or !$autoCreate) ? $model : $this->createTranslation($language);
    }

    /**
     * @param bool $autoCreate
     * @param bool $useFallback
     * @return Eloquent|Translation|null
     */
    public function getCurrentTranslation($autoCreate = true, $useFallback = true)
    {
        $language = $this->getCurrentLanguage();
        /** @var Eloquent|Translated|null $translation */
        $translation = $this->getTranslation($language);
        if (is_null($translation) and $useFallback and $fallback = static::$fallbackLanguage) {
            $translation = $this->getTranslation($fallback);
        }
        return ($translation or !$autoCreate) ? $translation : $this->createTranslation($language);
    }

    /**
     * Provides access to translation in current language as attribute
     *
     * @return \Eloquent
     */
    public function getTranslationAttribute()
    {
        return $this->getCurrentTranslation();
    }

    /**
     * Returns only records translated to current language
     *
     * @param $query
     * @return mixed
     */
    public function scopeOnCurrentLanguage($query)
    {
        return $query->whereHas(
            'translations',
            self::getWhereLangCallback($this->getCurrentLanguage())
        );
    }

    /**
     * Returns only records translated to target language
     *
     * @param $query
     * @param string $language
     * @return mixed
     */
    public function scopeTranslatedTo($query, $language)
    {
        return $query->whereHas(
            'translations',
            self::getWhereLangCallback($language)
        );
    }

    public function scopeWithTranslation($query, $language)
    {
        return $query->with([
            'translations' => self::getWhereLangCallback($language)
        ]);
    }

    public function scopeWithCurrentTranslation($query)
    {
        return $query->with([
            'translations' => self::getWhereLangCallback($this->getCurrentLanguage())
        ]);
    }

    /**
     * Overrides Eloquent model functionality to consider translation
     * Get an attribute from the model.
     *
     * @param  string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $inAttributes = array_key_exists($key, $this->attributes);
        if ($inAttributes || $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }
        $camelKey = camel_case($key);
        if (method_exists($this, $camelKey)) {
            return $this->getRelationshipFromMethod($key, $camelKey);
        }
        if($this->exists)
        {
            // same for translation
            $translation = $this->getCurrentTranslation(false);
            if ($translation) {
                return $translation->getAttribute($key);
            }
        }
    }

    /**
     * @param array $attributes
     * @return Eloquent|Translated
     */
    public static function createWithTranslations($attributes)
    {
        $languages = Languages::getInstance()->getSupportedLanguages();
        $attributesNotTranslated = array_diff_key($attributes, $languages);
        /** @var Translated|Eloquent $model */
        $model = static::create($attributesNotTranslated);
        $translated = array_diff_key($attributes, $attributesNotTranslated);
        foreach ($translated as $lang => $translationAttributes) {
            $translation = $model->createTranslation($lang);
            $translation->fill($translationAttributes);
            $translation->save();
        }
        return $model;
    }

    /**
     * @param array $attributes
     * @param array|null $translatedAttributes
     * @return Eloquent|Translated
     */
    public static function createWithSameTranslation($attributes, $translatedAttributes = null)
    {
        $languages = Languages::getInstance()->getSupportedLanguages();
        if (null === $translatedAttributes) {
            $translatedAttributes = $attributes['translated'];
            unset($attributes['translated']);
        }
        $model = static::create($attributes);
        foreach ($languages as $lang => $langName) {
            $translation = $model->createTranslation($lang);
            $translation->fill($translatedAttributes);
            $translation->save();
        }
        return $model;
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        // same for translation
        $translation = $this->getCurrentTranslation(false);
        if ($translation) {
            $translated = $translation->toArray();
            unset($translated['language']);
            unset($translated['created_at']);
            unset($translated['updated_at']);
            unset($translated['id']);
            $class = get_class($translation);
            $relName = $class::getRelationName();
            unset($translated[$relName . '_id']);
            # @todo does not work :(
            //unset($attributes['translations']);
            $attributes = array_merge($attributes, $translated);
        }
        return $attributes;
    }

    public function delete()
    {
        try {
            $this->translations()->delete();
        } catch(\Exception $e) {
            return false;
        }
        return parent::delete();
    }

} 