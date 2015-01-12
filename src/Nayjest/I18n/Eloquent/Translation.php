<?php
namespace Nayjest\I18n\Eloquent;


trait Translation
{
    /**
     * @return string
     * @throws \Exception
     */
    public static function getEntityClass()
    {
        if (property_exists(get_called_class(), 'entityClass')) {
            return static::$entityClass;
        } else {
            $class = str_replace('Translation','', get_called_class());
            if ($class ===get_called_class()) {
                throw new \Exception(
                    'Entity class must be specified for ' . get_called_class()
                );
            }
            return $class;
        }
    }

    public static function getRelationName()
    {
        if (property_exists(get_called_class(), 'relationName')) {
            return static::$relationName;
        } else {

            return str_replace('\\', '', snake_case(class_basename(static::getEntityClass())));
        }
    }


    public function entity()
    {
        $relationName = static::getRelationName();
        return $this->belongsTo(static::getEntityClass(), "{$relationName}_id");
    }

} 