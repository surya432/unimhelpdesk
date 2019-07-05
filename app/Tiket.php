<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    //
    protected $fillable = [
        'subject', 'user_id', 'prioritas_id', 'status_id', 'departement_id', 'rate',
    ];
    function content()
    {
        return $this->belongsTo( 'App\Content_tiket');
    }
}
