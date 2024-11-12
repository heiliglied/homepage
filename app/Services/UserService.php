<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\PasswordResets;

class UserService 
{
	private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new UserService();
        }

        return self::$instance;
    }
	
	public function findUser(String $user_id)
	{
		return User::where('user_id', $user_id)->first();
	}
	
	public function registerUser(array $data)
	{
		return User::create($data);
	}
	
	public function updateUser(int $id, array $data)
	{
		return User::where('id', $id)->update($data);
	}
	
	public function createToken(array $data)
	{
		return PasswordResets::updateOrInsert(
			[
				'email' => $data['email'],
			],
			[
				'email' => $data['email'],
				'token' => $data['token'],
				'created_at' => DB::raw("now()"),
			]
		);
	}
	
	public function checkToken(array $data)
	{
		return PasswordResets::where('email', $data['email'])
							->where('token', $data['token'])
							->where('created_at', '>=', $data['created_at'])
							->count();
	}
}