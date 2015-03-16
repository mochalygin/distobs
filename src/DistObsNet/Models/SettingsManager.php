<?php

namespace DistObsNet\Models;

class SettingsManager extends ModelManager
{
    public function tableName()
    {
        return 'settings';
    }

    public function primaryKeyName()
    {
        return 'code';
    }
}