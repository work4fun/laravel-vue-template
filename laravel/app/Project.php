<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [ 'id' ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format("Y-m-d G:i:s");
    }

    public function statistics()
    {
        return $this->hasMany(ProjectStatistics::class, "project_id", "id");
    }
}
