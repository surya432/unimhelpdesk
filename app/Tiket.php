<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    //
    protected $fillable = [
        'subject', 'user_id', 'prioritas_id', 'services_id', 'status_id', 'departement_id', 'rate',
    ];
    public function RepplyTiket()
    {
        return $this->hasOne('App\Content_tiket', 'id', 'tikets.tiket_id');
    }
    public function Departement()
    {
        return $this->hasOne('App\Departement', 'id', 'tikets.departement_id');
    }
    public function Status()
    {
        return $this->hasOne('App\Status', 'id', 'tikets.status_id');
    }
    public function Prioritas()
    {
        return $this->hasOne('App\Prioritas', 'id', 'tikets.prioritas_id');
    }
    public function Services()
    {
        return $this->hasOne('App\Services', 'id', 'tikets.services_id');
    }
    public function Users()
    {
        return $this->hasOne('App\User', 'id', 'tikets.user_id');
    }
}
