<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * Description of LeadsLog
 */
class LeadsLog extends Model
{
    protected $table = 'leads_log';
    
    protected $fillable = [
        'method',
        'url',
        'content'
    ];
}
