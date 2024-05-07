<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'csv_id',
        'name',
        'org_name',
        'domain',
        'country',
        'type',
        'category',
        'popularity_index',
    ];
}
