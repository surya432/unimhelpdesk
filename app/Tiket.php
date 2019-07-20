<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    //
    protected $fillable = [
        'subject', 'user_id', 'prioritas_id', 'status_id', 'departement_id', 'rate',
    ];
    public function RepplyTiket()
    {
        return $this->hasMany('App\Content_tiket', 'tiket_id', 'content_tikets.id');
    }
    public function Departement()
    {
        return $this->hasMany('App\Departement', 'departement_id', 'departements.id');
    }
    public function Status()
    {
        return $this->hasMany('App\Status', 'statuses.id', 'tikets.status_id');
    }
}
