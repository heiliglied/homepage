<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCheck extends Model
{
    
	protected $table = 'jobcheck';	
		
	protected $fillable = [
        'jobkey', 'status', 'return', 
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
