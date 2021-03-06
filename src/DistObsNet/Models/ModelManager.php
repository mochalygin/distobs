<?php

namespace DistObsNet\Models;

class ModelManager implements ModelManagerInterface
{
    protected $db;
    protected $publisher;

    public function __construct(\Silex\Application $app)
    {
        $this->db = $app['db'];
//        $this->publisher = $app['publisher'];
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

    public function load($pkValue, Model $model)
    {
        $sql = 'SELECT * FROM ' . $this->tableName() . ' WHERE ' . $this->primaryKeyName() . ' = ?';
        $res = $this->db->fetchAssoc($sql, array($pkValue));

        if (! $res)
            return false;

        foreach ($res as $key=>$value) {
            if ( $model->hasAttribute($key) )
                $model->$key = $value;
        }
        $model->isNew = false;

        return $model;
    }

    public function loadAll(Model $metaModel)
    {
        $sql = 'SELECT * FROM ' . $this->tableName();
        $res = $this->db->fetchAll($sql);

        if (! $res)
            return false;

        $models = [];
        foreach($res as $row) {
            $model = clone $metaModel;
            foreach ($row as $key=>$value) {
                if ( $model->hasAttribute($key) )
                    $model->$key = $value;
            }
            $model->isNew = false;
            $models[] = $model;
        }

        return $models;
    }

    public function save(ModelInterface $model)
    {
        if ($model->isNew)
            return $this->insert($model);
        else
            return $this->update($model);
    }

    public function findBy(array $conditions, Model $metaModel, array $options = array())
    {
        if (! $conditions)
            throw new ModelManagerException('No conditions was setted');

        if ( $diff = array_diff(array_keys($conditions), $metaModel->attributesNames()) )
            throw new ModelManagerException('Wrong list of parameters: ' . implode(',', ($diff)));

        $sql = 'SELECT * FROM ' . $this->tableName() . ' WHERE ';
        foreach ($conditions as $param => &$value)
            $value = $param . ' = "?"';
        $sql .= implode(' AND ', $conditions);

        if (! empty($options['limit']))
            $sql .= ' LIMIT ' . $options['limit'];

        $res = $this->db->fetchAssoc($sql, $conditions);

        if (! $res)
            return false;

        foreach ($res as $key=>$value) {
            if ( $model->hasAttribute($key) )
                $model->$key = $value;
        }
        $model->isNew = false;

        return $model;
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

        $attributes = $model->attributes();
        if ( $this->isPublishable() )
            $attributes += array('st' => 1, 'ts' => time());

        if ($this->db->insert($this->tableName(), $attributes)) {
            $model->isNew = false;
            if ($this->db->lastInsertId())
                $model->{$this->primaryKeyName()} = $this->db->lastInsertId();

            $this->publisherNotify();
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

        $attributes = $model->attributes();
        if ( $this->isPublishable() )
            $attributes += array('st' => 1, 'ts' => time());

        $res = $this->db->update(
                $this->tableName(),
                $attributes,
                array($primaryKey => $model->{$primaryKey})
        );

        if ($res)
            $this->publisherNotify();

        return $res;
    }

    /**
     * @param boolean $res
     */
    protected function publisherNotify()
    {
        if ( $this->isPublishable() ) {
//        $this->publisher->notify();
        }
    }

    protected function isPublishable()
    {
        return true;
    }
}

class ModelManagerException extends \Exception {}