<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class ProjectStatistics extends Model
{
    const TABLE_NAME = 'project_statistics';

    protected $guarded = [ 'id' ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format("Y-m-d G:i:s");
    }
}
