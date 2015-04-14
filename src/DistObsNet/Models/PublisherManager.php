<?php

namespace DistObsNet\Models;

class PublisherManager extends ModelManager
{
    public function tableName()
    {
        return 'publisher';
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