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
        return $this->hasMany('App\Content_tiket', 'id', 'tikets.tiket_id');
    }
    public function Departement()
    {
        return $this->hasMany('App\Departement', 'id', 'tikets.departement_id');
    }
    public function Status()
    {
        return $this->hasMany('App\Status', 'id', 'tikets.status_id');
    }
    public function Prioritas()
    {
        return $this->hasMany('App\Prioritas', 'id', 'tikets.prioritas_id');
    }
    public function Services()
    {
        return $this->hasMany('App\Services', 'id', 'tikets.services_id');
    }
    public function Users()
    {
        return $this->hasMany('App\User', 'users.id', 'tikets.user_id');
    }
}
