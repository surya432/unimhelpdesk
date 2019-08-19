<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingData extends Model
{
    //
  protected $fillable = [
        'words', 'hasilPrediksi', 'keysword', 'tiket_id',
    ];
}
