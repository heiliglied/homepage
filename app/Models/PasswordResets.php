<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResets extends Model
{
    protected $table = 'password_reset_tokens';
	//public $timestamps = false;
	const UPDATED_AT = null;
	public $primaryKey = 'email';
	
	protected $fillable = [
        'email', 'token',
    ];
}