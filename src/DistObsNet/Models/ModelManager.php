<?php

namespace DistObsNet\Models;

class ModelManager implements ModelManagerInterface
{
    protected $db = null;

    public function __construct(\Doctrine\DBAL\Connection $db)
    {
        $this->db = $db;
    }

    public function className()
    {
        return __NAMESPACE__ . '\\' . ucfirst($this->tableName());
    }

    public function create()
    {
        $class = $this->className();
        return new $class($this);
    }

    public function load($pkValue)
    {
        $sql = 'SELECT * FROM ' . $this->tableName() . ' WHERE ' . $this->primaryKeyName() . ' = ?';
        $res = $this->db->fetchAssoc($sql, array($pkValue));

        if (! $res)
            return false;

        $model = $this->create();
        foreach ($res as $key=>$value) {
            $model->$key = $value;
        }
        $model->isNew = false;

        return $model;
    }

    public function save(ModelInterface $model)
    {
        if ($model->isNew)
            return $this->insert($model);
        else
            return $this->update($model);
    }

    public function primaryKeyName()
    {
        return 'id';
    }

    protected function insert(ModelInterface $model)
    {
        $class = $this->className();
        if (! ($model instanceof $class))
            throw new ModelManagerException('Wrong model type for this manager');

        $model->isNew = false;

        if ($this->db->insert($this->tableName(), $model->attributes())) {
            if ($this->db->lastInsertId())
                $model->{$this->primaryKeyName()} = $this->db->lastInsertId();

            return true;
        }

        return false;
    }

    protected function update(ModelInterface $model)
    {
        $primaryKey = $this->primaryKeyName();

        $class = $this->className();
        if (! ($model instanceof $class))
            throw new ModelManagerException('Wrong model type for this manager');

        return $this->db->update($this->tableName(), $model->attributes(), array($primaryKey => $model->{$primaryKey}));
    }
}

class ModelManagerException extends \Exception {}