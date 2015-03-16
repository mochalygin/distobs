<?php

namespace DistObsNet\Models;

class DataManager extends ModelManager
{
    public function tableName()
    {
        return 'data';
    }

    public function primaryKeyName()
    {
        return 'id';
    }
}
