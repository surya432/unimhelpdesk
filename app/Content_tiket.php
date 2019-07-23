<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content_tiket extends Model
{
    //
    protected $fillable = [
        'body', 'tiket_id', 'senders', 
    ];
  
    public function attachmentFile()
    {
        return $this->hasMany('App\Attachment', 'content_tiket_id', 'id');
    }
}
