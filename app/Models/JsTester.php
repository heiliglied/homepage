<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JsTester extends Model
{
    use HasFactory;
	
	protected $table = 'jstester';
	//public $timestamps = false;	
		
	protected $fillable = [
        'user_id', 'session_id', 'page_key', 'view_name', 'code', 
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
