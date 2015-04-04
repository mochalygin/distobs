<?php

namespace DistObsNet\Models;

class Model implements ModelInterface
{
    public $isNew = true;

    private $attributes = array();
    private $manager = null;

    public function __construct(ModelManagerInterface $manager = null)
    {
        $this->manager = $manager;
    }

    public function __get($prop)
    {
        if (in_array($prop, $this->attributesNames)) {
            return isset($this->attributes[$prop])
                    ? $this->attributes[$prop]
                    : null;
        } else
            throw new ModelException('Wrong property name for get: ' . $prop);
    }

    public function __set($prop, $value)
    {
        if (in_array($prop, $this->attributesNames)) {
            $this->attributes[$prop] = $value;
        } else
            throw new ModelException('Wrong property name for set: ' . $prop);
    }

    public function __call($name, $arguments)
    {
        $prop = strtolower(substr($name, 3));
        if (! in_array($prop, $this->attributesNames))
            throw new ModelException('Wrong property name for getProp()/setProp() method: ' . $name);

        if ('get' === substr($name, 0, 3)) {
            return $this->$prop;
        } elseif ('set' == substr($name, 0, 3)) {
            $value = $arguments[0];
            $this->$prop = $value;
        } else
            throw new ModelException('Wrong action, only "getProp()" or "setProp()" accepted. Given: ' . $name);

        return $this;
    }

    public function __toString()
    {
        return json_encode(array_merge($this->attributes, array('isNew' => $this->isNew)), JSON_UNESCAPED_UNICODE);
    }

    public function attributesNames()
    {
        return $this->attributesNames;
    }

    public function attributes()
    {
        return $this->attributes;
    }

    public function save()
    {
        return (bool)($this->manager && $this->manager->save($this));
    }

    public function load($pk)
    {
        return $this->manager->load($pk, $this);
    }

    public function hasAttribute($name)
    {
        return in_array($name, $this->attributesNames());
    }

}

class ModelException extends \Exception {}