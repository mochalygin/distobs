<?php

namespace DistObsNet\Models;

class ObserverManager extends ModelManager
{
    public function tableName()
    {
        return 'observer';
    }

    public function primaryKeyName()
    {
        return 'node_id';
    }

    protected function isPublishable()
    {
        return false;
    }
}