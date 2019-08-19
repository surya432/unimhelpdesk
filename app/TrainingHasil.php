<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingHasil extends Model
{
    //
    protected $fillable = [
        'keys', 'values', 'training_data_id',
    ];
}
