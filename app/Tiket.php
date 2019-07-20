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
        return $this->hasMany('App\Content_tiket', 'content_tikets.id', 'tikets.tiket_id');
    }
    public function Departement()
    {
        return $this->hasMany('App\Departement', 'departements.id', 'tikets.departement_id');
    }
    public function Status()
    {
        return $this->hasMany('App\Status', 'statuses.id', 'tikets.status_id');
    }
    public function Prioritas()
    {
        return $this->hasMany('App\Prioritas', 'prioritas.id', 'tikets.prioritas_id');
    }
    public function Users()
    {
        return $this->hasMany('App\User', 'users.id', 'tikets.user_id');
    }
}
