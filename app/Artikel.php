<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    //
    protected $fillable = [
        'name', 'body', 'created_by', 'departement_id', 'created_at', 'updated_at', 'rate',
    ];
}
