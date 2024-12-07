<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardReply extends Model
{
    use HasFactory;
	
	protected $table = 'board_reply';
	//public $timestamps = false;	
		
	protected $fillable = [
        'board_id', 'writer', 'user_id', 'reply', 'parent_id', 'depth', 'censorship', 
    ];
	
	protected function serializeDate(\DateTimeInterface $date): String
	{
        if($date == '' || $date == '0000-00-00 00:00:00' || is_null($date)) {
            return '';
        } else {
            return $date->timezone('Asia/Seoul')->format('Y-m-d H:i:s');
        }
	}
}
