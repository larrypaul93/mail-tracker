<?php

namespace jdavidbakr\MailTracker\Model;

use Illuminate\Database\Eloquent\Model;

class SentEmail extends Model
{
    protected $fillable = [
    	'hash',
    	'headers',
    	'sender',
    	'recipient',
    	'subject',
    	'content',
    	'opens',
    	'clicks',
        'bounced'
    ];
}
