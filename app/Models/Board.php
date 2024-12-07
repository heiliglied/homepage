<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;
	
	protected $table = 'board';
	//public $timestamps = false;	
		
	protected $fillable = [
        'writer', 'user_id', 'subject', 'contents', 'view', 'files', 'censorship', 'deleted_at',  
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
