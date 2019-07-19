<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content_tiket extends Model
{
    //
    protected $fillable = [
        'body', 'tiket_id', 'senders', 
    ];
    function Tiket()
    {
        return $this->hasMany('App\Tiket');
    }
     public function attachments()
    {
        return $this->hasMany('App\Attachment', 'content_tiket_id', 'id');
    }
}
