<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    //
    protected $fillable = [
        'name', 'file', 'content_tiket_id',
    ];
}
